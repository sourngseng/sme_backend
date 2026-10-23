<?php

namespace Tests\Feature;

use App\Models\Allergen;
use App\Models\DeliveryBatch;
use App\Models\DeliveryStop;
use App\Models\Driver;
use App\Models\Guardian;
use App\Models\MenuItem;
use App\Models\MenuVersion;
use App\Models\NutritionConsumptionLog;
use App\Models\NutritionFact;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSchedule;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Restaurant;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLocation;
use App\Models\SchoolMealPolicy;
use App\Models\SchoolShift;
use App\Models\Student;
use App\Models\StudentNutritionProfile;
use App\Models\User;
use App\Services\SettlementService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EndToEndMealLifecycleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $parentUser;
    protected Guardian $guardian;
    protected User $kitchenUser;
    protected User $driverUser;
    protected Driver $driver;
    protected User $financeAdmin;
    protected School $school;
    protected SchoolShift $shift;
    protected SchoolLocation $location;
    protected Restaurant $restaurant;
    protected Student $student;
    protected Allergen $peanutAllergen;
    protected MenuItem $menuItem;
    protected MenuVersion $menuVersion;

    protected function setUp(): void
    {
        parent::setUp();

        $tz = 'Asia/Phnom_Penh';

        // 1. Roles & Permissions Setup
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['guard_name' => 'web']);
        $kitchenRole = Role::firstOrCreate(['name' => 'restaurant_staff'], ['guard_name' => 'web']);
        $driverRole = Role::firstOrCreate(['name' => 'driver'], ['guard_name' => 'web']);
        $financeRole = Role::firstOrCreate(['name' => 'finance_billing'], ['guard_name' => 'web']);

        $perms = [
            'orders.view', 'orders.create', 'orders.update',
            'students.view', 'students.update',
            'delivery.view', 'delivery.update',
            'finance.view', 'finance.manage',
        ];

        foreach ($perms as $pName) {
            $p = Permission::firstOrCreate(['name' => $pName]);
            $parentRole->permissions()->syncWithoutDetaching([$p->id]);
            $kitchenRole->permissions()->syncWithoutDetaching([$p->id]);
            $driverRole->permissions()->syncWithoutDetaching([$p->id]);
            $financeRole->permissions()->syncWithoutDetaching([$p->id]);
        }

        // 2. School, Shift & Location
        $this->school = School::create([
            'code' => 'SCH-E2E-' . uniqid(),
            'name' => 'Northbridge E2E Academy',
            'level' => 'primary',
            'address' => 'Norodom Blvd, Phnom Penh',
            'status' => 'active',
            'timezone' => $tz,
        ]);

        SchoolMealPolicy::create([
            'school_id' => $this->school->id,
            'allow_cod' => false,
            'daily_order_cutoff_time' => '08:30:00',
        ]);

        $this->shift = SchoolShift::create([
            'school_id' => $this->school->id,
            'name' => 'Lunch Shift',
            'start_time' => '11:30:00',
            'end_time' => '12:30:00',
            'cutoff_time' => '08:30:00',
        ]);

        $this->location = SchoolLocation::create([
            'school_id' => $this->school->id,
            'name' => 'Primary Cafeteria Counter A',
            'dropoff_point_instructions' => 'Gate 2 entrance, cafeteria dispatch counter',
            'is_active' => true,
        ]);

        // 3. Restaurant & Merchant Owner
        $owner = User::create([
            'full_name' => 'Master Chef Sovann',
            'email' => 'chef.e2e.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);

        $this->restaurant = Restaurant::create([
            'code' => 'REST-E2E-' . uniqid(),
            'name' => 'Royal Khmer Kitchen',
            'owner_user_id' => $owner->id,
            'status' => 'active',
            'is_active' => true,
            'commission_default_pct' => 10.00,
            'timezone' => $tz,
        ]);

        // 4. Menu Item with Peanut Allergen and Nutrition Facts
        $this->peanutAllergen = Allergen::firstOrCreate(
            ['code' => 'PEANUT'],
            ['name' => 'Peanuts', 'description' => 'Peanut oils and groundnuts']
        );

        $this->menuItem = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Khmer Peanut Chicken Curry',
            'sku' => 'CURRY-PEANUT-' . uniqid(),
            'meal_type' => 'lunch',
            'status' => 'active',
        ]);

        $this->menuVersion = MenuVersion::create([
            'menu_item_id' => $this->menuItem->id,
            'version_no' => 1,
            'price_minor' => 450, // $4.50
            'currency' => 'USD',
            'status' => 'active',
            'cutoff_minutes' => 60,
            'published_at' => now(),
        ]);

        $this->menuVersion->allergens()->attach($this->peanutAllergen->id, ['severity' => 'severe']);

        NutritionFact::create([
            'menu_version_id' => $this->menuVersion->id,
            'calories_kcal' => 580.0,
            'protein_g' => 36.0,
            'carbohydrates_g' => 62.0,
            'fat_g' => 22.0,
        ]);

        // 5. Parent & Student Setup
        $this->parentUser = User::create([
            'full_name' => 'Sophea Parent E2E',
            'email' => 'parent.e2e.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->parentUser->roles()->attach($parentRole->id);

        $this->guardian = Guardian::create([
            'user_id' => $this->parentUser->id,
            'emergency_contact' => '+85512999888',
        ]);

        $this->student = Student::create([
            'student_code' => 'STU-E2E-' . uniqid(),
            'school_id' => $this->school->id,
            'school_shift_id' => $this->shift->id,
            'full_name' => 'Dara Student',
            'grade_level' => 'Grade 3',
            'class_name' => '3-A',
            'status' => 'active',
        ]);

        // 6. Kitchen Staff & Driver Users
        $this->kitchenUser = User::create([
            'full_name' => 'Kitchen Staff Vuth',
            'email' => 'kitchen.e2e.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->kitchenUser->roles()->attach($kitchenRole->id);

        $this->driverUser = User::create([
            'full_name' => 'Driver Rath',
            'email' => 'driver.e2e.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->driverUser->roles()->attach($driverRole->id);

        $this->driver = Driver::create([
            'user_id' => $this->driverUser->id,
            'driver_code' => 'DRV-E2E-' . uniqid(),
            'phone' => '+85598765432',
            'vehicle_type' => 'van',
            'vehicle_plate' => 'PHNOM-PENH-2BC-9988',
            'status' => 'active',
            'self_delivery' => false,
        ]);

        $this->financeAdmin = User::create([
            'full_name' => 'Finance Director',
            'email' => 'finance.e2e.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->financeAdmin->roles()->attach($financeRole->id);
    }

    /**
     * PB-075 & PB-077: Complete End-to-End Meal Journey Test.
     */
    public function test_complete_parent_to_settlement_end_to_end_journey(): void
    {
        $tz = 'Asia/Phnom_Penh';

        // -------------------------------------------------------------
        // STEP 1: Parent registers & links child with Peanut allergy
        // -------------------------------------------------------------
        $regRes = $this->actingAs($this->parentUser, 'sanctum')->postJson('/api/v1/guardian/students', [
            'school_id' => $this->school->id,
            'school_shift_id' => $this->shift->id,
            'full_name' => 'Dara Student E2E',
            'date_of_birth' => '2016-05-15',
            'gender' => 'male',
            'grade_level' => 'Grade 3',
            'class_name' => '3-A',
            'relationship' => 'Mother',
            'is_primary' => true,
            'can_order' => true,
            'can_view_nutrition' => true,
            'dietary_preferences' => ['Halal friendly', 'Nut-Free'],
            'allergen_ids' => [$this->peanutAllergen->id],
            'nutrition_notes' => 'Severe peanut allergy',
        ]);

        $regRes->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.full_name', 'Dara Student E2E')
            ->assertJsonPath('data.guardian_permissions.can_order', true);

        $studentId = $regRes->json('data.id');

        // Verify nutrition profile was created with peanut allergy
        $profile = StudentNutritionProfile::where('student_id', $studentId)->first();
        $this->assertNotNull($profile);
        $this->assertContains($this->peanutAllergen->id, $profile->allergen_ids);

        // -------------------------------------------------------------
        // STEP 2: Parent selects 5-day meal schedule with allergen warning check
        // -------------------------------------------------------------
        $mon = Carbon::now($tz)->addDays(4);
        $scheduleDates = [
            $mon->copy()->toDateString(),
            $mon->copy()->addDay()->toDateString(),
            $mon->copy()->addDays(2)->toDateString(),
            $mon->copy()->addDays(3)->toDateString(),
            $mon->copy()->addDays(4)->toDateString(),
        ];

        $itemsPayload = array_map(fn($d) => [
            'menu_version_id' => $this->menuVersion->id,
            'quantity' => 1,
            'delivery_date' => $d,
            'shift' => 'Lunch Shift',
        ], $scheduleDates);

        // 2a. Attempting without allergen acknowledgment must fail with 422
        $unackResponse = $this->actingAs($this->parentUser, 'sanctum')
            ->postJson('/api/v1/orders', [
                'student_id' => $studentId,
                'restaurant_id' => $this->restaurant->id,
                'school_location_id' => $this->location->id,
                'acknowledge_allergen_warning' => false,
                'items' => $itemsPayload,
            ], [
                'Idempotency-Key' => 'idem-e2e-fail-' . uniqid(),
            ]);

        $unackResponse->assertStatus(422)
            ->assertJsonValidationErrors(['acknowledge_allergen_warning']);

        // 2b. With explicit allergen acknowledgment -> order created
        $orderResponse = $this->actingAs($this->parentUser, 'sanctum')
            ->postJson('/api/v1/orders', [
                'student_id' => $studentId,
                'restaurant_id' => $this->restaurant->id,
                'school_location_id' => $this->location->id,
                'acknowledge_allergen_warning' => true,
                'items' => $itemsPayload,
            ], [
                'Idempotency-Key' => 'idem-e2e-succ-' . uniqid(),
            ]);

        $orderResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $orderId = $orderResponse->json('data.id');
        $order = Order::findOrFail($orderId);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(2250, $order->total_minor); // 5 meals * $4.50 = 2250 minor

        // -------------------------------------------------------------
        // STEP 3: Initiates KHQR Payment
        // -------------------------------------------------------------
        $payInitiateResponse = $this->actingAs($this->parentUser, 'sanctum')
            ->postJson('/api/v1/payments/initiate', [
                'order_id' => $order->id,
                'provider' => 'KHQR',
            ], [
                'Idempotency-Key' => 'idem-e2e-pay-' . uniqid(),
            ]);

        $payInitiateResponse->assertStatus(201)
            ->assertJsonPath('data.status', 'pending');

        $providerRef = $payInitiateResponse->json('data.provider_reference');
        $payment = Payment::where('provider_reference', $providerRef)->firstOrFail();

        // -------------------------------------------------------------
        // STEP 4: Signed Payment Webhook Callback confirms order
        // -------------------------------------------------------------
        $secret = config('services.khqr.secret', 'khqr_webhook_secret_key');
        $webhookData = [
            'event_id' => 'EVT-E2E-' . uniqid(),
            'event_type' => 'payment.completed',
            'provider_reference' => $providerRef,
            'payment_id' => $payment->id,
            'status' => 'SUCCESS',
            'amount_minor' => 2250,
            'currency' => 'USD',
        ];
        $rawWebhook = json_encode($webhookData);
        $sig = hash_hmac('sha256', $rawWebhook, $secret);

        $webhookResponse = $this->call(
            'POST',
            '/api/v1/webhooks/payments/khqr',
            [], [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_SIGNATURE' => $sig],
            $rawWebhook
        );

        $webhookResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('paid', $payment->fresh()->status);
        $this->assertEquals('confirmed', $order->fresh()->status);

        // -------------------------------------------------------------
        // STEP 5: Kitchen marks batch prepared & ready for driver pickup
        // -------------------------------------------------------------
        $schedule = OrderSchedule::where('order_id', $order->id)->firstOrFail();

        $batch = DeliveryBatch::create([
            'restaurant_id' => $this->restaurant->id,
            'driver_id' => $this->driver->id,
            'delivery_date' => $schedule->delivery_date,
            'shift' => 'Lunch',
            'status' => 'assigned',
        ]);

        $stop = DeliveryStop::create([
            'delivery_batch_id' => $batch->id,
            'order_schedule_id' => $schedule->id,
            'school_id' => $this->school->id,
            'school_location_id' => $this->location->id,
            'sequence_no' => 1,
            'status' => 'pending',
        ]);

        // Driver pickup
        $pickupResponse = $this->actingAs($this->driverUser)
            ->postJson("/api/v1/driver/batches/{$batch->id}/pickup");

        $pickupResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'picked_up');

        // -------------------------------------------------------------
        // STEP 6: Driver submits photo proof of delivery & completes stop
        // -------------------------------------------------------------
        $completeResponse = $this->actingAs($this->driverUser)
            ->postJson("/api/v1/driver/stops/{$stop->id}/complete", [
                'proof_type' => 'signature',
                'photo_url' => 'https://s3.schoolmeals.com/e2e/tray_drop.jpg',
                'signature_url' => 'https://s3.schoolmeals.com/e2e/sig.png',
                'recipient_name' => 'Teacher Chanthy',
                'notes' => 'Handed over at Room 3A counter',
            ]);

        $completeResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'delivered');

        $this->assertEquals('delivered', $schedule->fresh()->status);

        // -------------------------------------------------------------
        // STEP 7: Parent logs 80% consumption & 5-star rating (PB-054)
        // -------------------------------------------------------------
        $logResponse = $this->actingAs($this->parentUser, 'sanctum')
            ->postJson('/api/v1/nutrition/consumption-logs', [
                'order_schedule_id' => $schedule->id,
                'consumption_status' => 'partial',
                'estimated_consumed_pct' => 80.0,
                'notes' => 'Child ate most of it and loved the mild curry!',
            ]);

        $logResponse->assertStatus(201)
            ->assertJsonPath('log.consumption_status', 'partial')
            ->assertJsonPath('log.estimated_consumed_pct', '80.00');

        $this->assertDatabaseHas('nutrition_consumption_logs', [
            'order_schedule_id' => $schedule->id,
            'consumption_status' => 'partial',
        ]);

        // Submit 5-star feedback
        $feedbackResponse = $this->actingAs($this->parentUser, 'sanctum')
            ->postJson("/api/v1/orders/schedules/{$schedule->id}/feedback", [
                'rating' => 5,
                'taste_rating' => 5,
                'quality_rating' => 5,
                'comment' => 'Outstanding quality and very fresh!',
            ]);

        $feedbackResponse->assertStatus(201)
            ->assertJsonPath('feedback.rating', 5);

        // -------------------------------------------------------------
        // STEP 8: Finance generates settlement statement for restaurant
        // -------------------------------------------------------------
        $settlementService = new SettlementService();
        $start = Carbon::parse($order->placed_at)->subDay()->toDateString();
        $end = Carbon::parse($order->placed_at)->addDays(14)->toDateString();

        $settlement = $settlementService->generateSettlement(
            $this->restaurant->id,
            $start,
            $end
        );

        $this->assertNotNull($settlement);
        $this->assertEquals('draft', $settlement->status);
        $this->assertGreaterThanOrEqual(2250, $settlement->gross_sales_minor);

        // 10% platform commission on 2250 = 225 minor commission, net payout = 2025 minor ($20.25)
        $expectedCommission = (int) round(2250 * 0.10);
        $expectedNet = 2250 - $expectedCommission;

        $this->assertEquals(225, $expectedCommission);
        $this->assertEquals(2025, $expectedNet);
    }
}
