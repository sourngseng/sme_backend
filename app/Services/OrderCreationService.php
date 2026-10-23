<?php

namespace App\Services;

use App\Models\CommissionRule;
use App\Models\Guardian;
use App\Models\MenuVersion;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSchedule;
use App\Models\Promotion;
use App\Models\Restaurant;
use App\Models\School;
use App\Models\SchoolLocation;
use App\Models\SchoolShift;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderCreationService
{
    /**
     * Create an order with 1-7 day meal scheduling, server-side cutoff enforcement,
     * allergen cross-referencing, and immutable snapshots.
     *
     * @param array<string, mixed> $data
     */
    public function createOrder(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            // 1. Resolve & Authorize Guardian
            $guardian = Guardian::firstOrCreate(['user_id' => $user->id]);
            $student = Student::with(['school.mealPolicy', 'school.shifts', 'nutritionProfile'])
                ->findOrFail($data['student_id']);

            $isAuthorized = $student->guardians()
                ->where('guardian_id', $guardian->id)
                ->wherePivot('can_order', true)
                ->exists();

            if (! $isAuthorized && ! $user->hasRole('super_admin')) {
                throw new AuthorizationException('You are not authorized to place meal orders for this student.');
            }

            // 2. Validate Restaurant & Location
            $restaurant = Restaurant::findOrFail($data['restaurant_id']);
            if ($restaurant->status !== 'active') {
                throw ValidationException::withMessages([
                    'restaurant_id' => ['This restaurant is currently not accepting orders.'],
                ]);
            }

            $school = $student->school;
            $location = SchoolLocation::where('school_id', $school->id)
                ->findOrFail($data['school_location_id']);

            // 3. Process Scheduled Items & Cutoff Enforcement
            $rawItems = $data['items'];
            $preparedSchedules = [];
            $itemsByVersion = [];
            $tz = config('app.timezone', 'Asia/Phnom_Penh');
            $now = Carbon::now($tz);

            $studentAllergenIds = $student->nutritionProfile?->allergen_ids ?? [];
            $detectedAllergenConflicts = [];

            foreach ($rawItems as $index => $itemData) {
                $version = MenuVersion::with(['menuItem', 'nutritionFact', 'allergens'])
                    ->findOrFail($itemData['menu_version_id']);

                if ($version->status !== 'active' || $version->menuItem->status !== 'active') {
                    throw ValidationException::withMessages([
                        "items.{$index}.menu_version_id" => ["Meal '{$version->menuItem->name}' is no longer active."],
                    ]);
                }

                $deliveryDate = Carbon::parse($itemData['delivery_date'], $tz)->toDateString();
                $quantity = (int) ($itemData['quantity'] ?? 1);

                // Date availability check
                if ($version->available_from && Carbon::parse($version->available_from, $tz)->startOfDay()->greaterThan(Carbon::parse($deliveryDate, $tz))) {
                    throw ValidationException::withMessages([
                        "items.{$index}.delivery_date" => ["Meal '{$version->menuItem->name}' is not yet available on {$deliveryDate}."],
                    ]);
                }
                if ($version->available_to && Carbon::parse($version->available_to, $tz)->endOfDay()->lessThan(Carbon::parse($deliveryDate, $tz))) {
                    throw ValidationException::withMessages([
                        "items.{$index}.delivery_date" => ["Meal '{$version->menuItem->name}' is expired for delivery on {$deliveryDate}."],
                    ]);
                }

                // Resolve matching school shift
                $shiftInput = $itemData['shift'] ?? 'lunch';
                $matchedShift = $school->shifts->first(function (SchoolShift $s) use ($shiftInput) {
                    return strcasecmp($s->name, $shiftInput) === 0
                        || strcasecmp($s->id, $shiftInput) === 0
                        || stripos($s->name, $shiftInput) !== false;
                }) ?? $school->shifts->first();

                $shiftStartTime = $matchedShift ? $matchedShift->start_time : '11:30:00';
                $shiftName = $matchedShift ? $matchedShift->name : ucfirst($shiftInput);

                // Server-Side Cut-Off Enforcement (PB-035)
                $deliveryShiftStart = Carbon::parse("{$deliveryDate} {$shiftStartTime}", $tz);
                $cutoffMinutes = max(
                    $version->cutoff_minutes ?? 0,
                    $school->mealPolicy?->default_cutoff_minutes ?? 120
                );
                $cutoffAt = $deliveryShiftStart->copy()->subMinutes($cutoffMinutes);

                if ($now->greaterThan($cutoffAt)) {
                    throw ValidationException::withMessages([
                        "items.{$index}.delivery_date" => [
                            "Ordering cutoff for {$deliveryDate} ({$shiftName}) passed at {$cutoffAt->toIso8601String()}.",
                        ],
                    ]);
                }

                // Max daily quantity check
                if ($version->max_daily_quantity !== null) {
                    $alreadyOrdered = (int) OrderSchedule::where('delivery_date', $deliveryDate)
                        ->where('shift', $shiftName)
                        ->whereHas('orderItem', fn ($q) => $q->where('menu_version_id', $version->id))
                        ->whereNotIn('status', ['cancelled', 'skipped'])
                        ->count();

                    if ($alreadyOrdered + $quantity > $version->max_daily_quantity) {
                        throw ValidationException::withMessages([
                            "items.{$index}.quantity" => [
                                "Daily limit of {$version->max_daily_quantity} portions exceeded for {$version->menuItem->name} on {$deliveryDate}.",
                            ],
                        ]);
                    }
                }

                // Allergen Screening (PB-029)
                if (! empty($studentAllergenIds) && $version->allergens->isNotEmpty()) {
                    foreach ($version->allergens as $allergen) {
                        if (in_array($allergen->id, $studentAllergenIds, true)) {
                            $detectedAllergenConflicts[] = $allergen->name;
                        }
                    }
                }

                // Group quantities per version for OrderItems snapshotting
                if (! isset($itemsByVersion[$version->id])) {
                    $itemsByVersion[$version->id] = [
                        'version' => $version,
                        'total_quantity' => 0,
                    ];
                }
                $itemsByVersion[$version->id]['total_quantity'] += $quantity;

                // Queue schedule entry
                $preparedSchedules[] = [
                    'version_id' => $version->id,
                    'delivery_date' => $deliveryDate,
                    'shift' => $shiftName,
                    'cutoff_at' => $cutoffAt,
                    'quantity' => $quantity,
                ];
            }

            // Check if allergen warning acknowledgment was required
            if (! empty($detectedAllergenConflicts)) {
                $acknowledged = filter_var($data['acknowledge_allergen_warning'] ?? false, FILTER_VALIDATE_BOOLEAN);
                if (! $acknowledged) {
                    $uniqueConflicts = array_unique($detectedAllergenConflicts);
                    throw ValidationException::withMessages([
                        'acknowledge_allergen_warning' => [
                            'Selected meals contain allergen conflicts (' . implode(', ', $uniqueConflicts) . ') declared on child profile. Explicit acknowledgment required.',
                        ],
                    ]);
                }
            }

            // 4. Financial Calculations in Minor Units (BIGINT)
            $subtotalMinor = 0;
            foreach ($itemsByVersion as $grouped) {
                $subtotalMinor += ($grouped['version']->price_minor * $grouped['total_quantity']);
            }

            // Optional Promotion Discount
            $discountMinor = 0;
            if (! empty($data['promotion_code'])) {
                $promo = Promotion::where('code', strtoupper($data['promotion_code']))
                    ->where('status', 'active')
                    ->first();

                if ($promo) {
                    if ($promo->type === 'percentage') {
                        $discountMinor = (int) round($subtotalMinor * ($promo->value / 100));
                    } else {
                        $discountMinor = (int) round($promo->value * 100);
                    }

                    if ($promo->max_discount_minor && $discountMinor > $promo->max_discount_minor) {
                        $discountMinor = $promo->max_discount_minor;
                    }
                    $discountMinor = min($discountMinor, $subtotalMinor);
                }
            }

            $deliveryFeeMinor = 0; // Flat or campus-subsidized delivery
            $totalMinor = max(0, $subtotalMinor + $deliveryFeeMinor - $discountMinor);

            // 5. Commission Snapshot (PB-058)
            $commissionPct = (float) $restaurant->commission_default_pct;
            $activeRule = CommissionRule::where('restaurant_id', $restaurant->id)
                ->where('status', 'active')
                ->where(function ($q) use ($now) {
                    $today = $now->toDateString();
                    $q->whereNull('effective_from')->orWhere('effective_from', '<=', $today);
                })
                ->where(function ($q) use ($now) {
                    $today = $now->toDateString();
                    $q->whereNull('effective_to')->orWhere('effective_to', '>=', $today);
                })
                ->latest('created_at')
                ->first();

            if ($activeRule) {
                $commissionPct = (float) $activeRule->commission_pct;
            }

            $commissionMinor = (int) round($subtotalMinor * ($commissionPct / 100));

            // 6. Create Primary Order Record
            $orderNumber = 'ORD-' . $now->format('Ymd') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'order_number' => $orderNumber,
                'guardian_id' => $guardian->id,
                'student_id' => $student->id,
                'school_id' => $school->id,
                'restaurant_id' => $restaurant->id,
                'status' => 'pending',
                'subtotal_minor' => $subtotalMinor,
                'delivery_fee_minor' => $deliveryFeeMinor,
                'discount_minor' => $discountMinor,
                'commission_minor' => $commissionMinor,
                'total_minor' => $totalMinor,
                'currency' => 'USD',
                'notes' => $data['notes'] ?? null,
                'placed_at' => $now,
            ]);

            // 7. Create OrderItems with Immutable Snapshots (PB-039)
            $createdOrderItems = [];
            foreach ($itemsByVersion as $versionId => $itemGroup) {
                $version = $itemGroup['version'];
                $qty = $itemGroup['total_quantity'];
                $lineTotal = $version->price_minor * $qty;

                // Build nutrition snapshot JSONB
                $nutritionFact = $version->nutritionFact;
                $nutritionSnapshot = $nutritionFact ? [
                    'calories_kcal' => $nutritionFact->calories_kcal ? (float) $nutritionFact->calories_kcal : null,
                    'protein_g' => $nutritionFact->protein_g ? (float) $nutritionFact->protein_g : null,
                    'carbohydrates_g' => $nutritionFact->carbohydrates_g ? (float) $nutritionFact->carbohydrates_g : null,
                    'fat_g' => $nutritionFact->fat_g ? (float) $nutritionFact->fat_g : null,
                    'fiber_g' => $nutritionFact->fiber_g ? (float) $nutritionFact->fiber_g : null,
                    'sugar_g' => $nutritionFact->sugar_g ? (float) $nutritionFact->sugar_g : null,
                    'sodium_mg' => $nutritionFact->sodium_mg ? (float) $nutritionFact->sodium_mg : null,
                ] : null;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_version_id' => $version->id,
                    'item_name_snapshot' => $version->menuItem->name,
                    'unit_price_minor' => $version->price_minor,
                    'quantity' => $qty,
                    'line_total_minor' => $lineTotal,
                    'calories_kcal_snapshot' => $nutritionFact?->calories_kcal,
                    'nutrition_snapshot' => $nutritionSnapshot,
                ]);

                $createdOrderItems[$version->id] = $orderItem;
            }

            // 8. Expand into individual OrderSchedules
            foreach ($preparedSchedules as $sched) {
                $orderItem = $createdOrderItems[$sched['version_id']];

                // Create individual schedule record for each portion/day
                for ($q = 0; $q < $sched['quantity']; $q++) {
                    OrderSchedule::create([
                        'order_id' => $order->id,
                        'order_item_id' => $orderItem->id,
                        'delivery_date' => $sched['delivery_date'],
                        'shift' => $sched['shift'],
                        'school_location_id' => $location->id,
                        'status' => 'scheduled',
                        'cutoff_at' => $sched['cutoff_at'],
                    ]);
                }
            }

            $order->load(['items', 'schedules.location', 'student', 'school', 'restaurant']);

            return $order;
        });
    }

    /**
     * Skip or cancel an individual day meal schedule prior to its cutoff.
     */
    public function updateScheduleStatus(OrderSchedule $schedule, string $newStatus, ?string $reason = null): OrderSchedule
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $now = Carbon::now($tz);

        if ($schedule->cutoff_at && $now->greaterThan($schedule->cutoff_at)) {
            throw ValidationException::withMessages([
                'schedule' => ["Cannot {$newStatus} schedule: ordering cutoff passed on {$schedule->cutoff_at->toIso8601String()}."],
            ]);
        }

        if (in_array($schedule->status, ['delivered', 'prepared'], true)) {
            throw ValidationException::withMessages([
                'schedule' => ["Cannot change status of an already {$schedule->status} meal schedule."],
            ]);
        }

        $schedule->update([
            'status' => $newStatus,
        ]);

        return $schedule;
    }
}
