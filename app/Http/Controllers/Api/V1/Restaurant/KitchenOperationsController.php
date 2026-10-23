<?php

namespace App\Http\Controllers\Api\V1\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kitchen\UpdateKitchenScheduleStatusRequest;
use App\Models\Order;
use App\Models\OrderSchedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class KitchenOperationsController extends Controller
{
    /**
     * Get kitchen production queue with aggregated meal quantities and masked student schedule cards.
     */
    public function queue(Request $request): JsonResponse
    {
        $user = $request->user();
        $restaurantId = $user->restaurant_id
            ?? $user->restaurantStaff()->first()?->restaurant_id
            ?? $user->ownedRestaurants()->first()?->id
            ?? $request->query('restaurant_id');

        if (! $restaurantId && ! $user->hasRole('super_admin')) {
            return response()->json([
                'message' => 'User is not associated with an active restaurant kitchen.',
            ], Response::HTTP_FORBIDDEN);
        }

        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $deliveryDate = $request->query('delivery_date', Carbon::now($tz)->toDateString());
        $shift = $request->query('shift');

        $query = OrderSchedule::with([
            'order.student.nutritionProfile',
            'orderItem.menuVersion.allergens',
            'orderItem.menuVersion.menuItem',
            'location',
        ])
            ->whereHas('order', function ($q) use ($restaurantId) {
                if ($restaurantId) {
                    $q->where('restaurant_id', $restaurantId);
                }
            })
            ->whereDate('delivery_date', $deliveryDate)
            ->whereIn('status', ['scheduled', 'preparing', 'prepared']);

        if ($shift) {
            $query->where('shift', $shift);
        }

        $schedules = $query->orderBy('shift')->get();

        // 1. Aggregated meal quantities by menu_version_id
        $aggregated = $schedules->groupBy(fn ($s) => $s->orderItem?->menu_version_id)->map(function ($items, $versionId) {
            $first = $items->first();
            $item = $first->orderItem;
            $version = $item?->menuVersion;

            return [
                'menu_version_id' => $versionId,
                'item_name' => $item?->item_name_snapshot,
                'meal_type' => $version?->menuItem?->meal_type ?? 'lunch',
                'total_quantity' => $items->count(),
                'prepared_quantity' => $items->where('status', 'prepared')->count(),
                'pending_quantity' => $items->whereIn('status', ['scheduled', 'preparing'])->count(),
            ];
        })->values();

        // 2. Individual schedules with masked guardian privacy data (PB-046)
        $individual = $schedules->map(function (OrderSchedule $s) {
            $student = $s->order?->student;
            $allergens = $s->orderItem?->menuVersion?->allergens?->pluck('name')->all() ?? [];
            $dietary = $student?->nutritionProfile?->dietary_preferences ?? [];

            return [
                'schedule_id' => $s->id,
                'order_id' => $s->order_id,
                'order_number' => $s->order?->order_number,
                'status' => $s->status,
                'delivery_date' => $s->delivery_date instanceof Carbon ? $s->delivery_date->toDateString() : (string) $s->delivery_date,
                'shift' => $s->shift,
                'student' => [
                    'id' => $student?->id,
                    'full_name' => $student?->full_name,
                    'student_code' => $student?->student_code,
                    'grade_level' => $student?->grade_level,
                    'class_name' => $student?->class_name,
                ],
                'meal_name' => $s->orderItem?->item_name_snapshot,
                'location' => $s->location ? [
                    'id' => $s->location->id,
                    'name' => $s->location->name,
                    'address' => $s->location->address,
                    'instructions' => $s->location->instructions,
                ] : null,
                'allergens' => $allergens,
                'dietary_preferences' => $dietary,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'delivery_date' => $deliveryDate,
                'shift' => $shift,
                'total_portions' => $schedules->count(),
                'total_meals' => $aggregated->count(),
                'aggregated_meals' => $aggregated,
                'schedules' => $individual,
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Transition kitchen schedules status to preparing or prepared and update primary order status.
     */
    public function updateScheduleStatus(UpdateKitchenScheduleStatusRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $targetStatus = $validated['status'];
        $scheduleStatus = in_array($targetStatus, ['prepared', 'ready'], true) ? 'prepared' : 'preparing';

        $updatedCount = DB::transaction(function () use ($validated, $scheduleStatus, $targetStatus) {
            $scheduleIds = $validated['schedule_ids'];

            // Update schedules
            OrderSchedule::whereIn('id', $scheduleIds)->update([
                'status' => $scheduleStatus,
            ]);

            // Synchronize parent Orders status
            $orderIds = OrderSchedule::whereIn('id', $scheduleIds)->pluck('order_id')->unique();

            foreach ($orderIds as $orderId) {
                $order = Order::with('schedules')->find($orderId);
                if (! $order) {
                    continue;
                }

                $allPrepared = $order->schedules->every(fn ($s) => in_array($s->status, ['prepared', 'delivered', 'skipped', 'cancelled'], true));

                if ($allPrepared && $targetStatus === 'ready') {
                    $order->update(['status' => 'ready']);
                } elseif (! in_array($order->status, ['ready', 'delivered'], true)) {
                    $order->update(['status' => 'preparing']);
                }
            }

            return count($scheduleIds);
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updatedCount} meal schedules to {$targetStatus}.",
            'data' => [
                'updated_count' => $updatedCount,
                'status' => $targetStatus,
            ],
        ], Response::HTTP_OK);
    }
}
