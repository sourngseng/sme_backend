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
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSchedule;
use App\Models\Permission;
use App\Models\ProofOfDelivery;
use App\Models\Restaurant;
use App\Models\RestaurantStaff;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLocation;
use App\Models\SchoolMealPolicy;
use App\Models\SchoolShift;
use App\Models\Student;
use App\Models\StudentNutritionProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KitchenAndDriverDeliveryOperationsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kitchenStaffUser;
    protected User $driverUser;
    protected User $parentUser;
    protected Driver $driver;
    protected Restaurant $restaurant;
    protected School $school;
    protected SchoolLocation $location;
    protected SchoolShift $shift;
    protected Student $student;
    protected Order $order;
    protected OrderSchedule $schedule1;
    protected OrderSchedule $schedule2;
    protected MenuItem $menuItem;
    protected MenuVersion $menuVersion;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles & Permissions
        $kitchenRole = Role::firstOrCreate(['name' => 'kitchen_staff'], ['guard_name' => 'web']);
        $driverRole = Role::firstOrCreate(['name' => 'driver'], ['guard_name' => 'web']);
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['guard_name' => 'web']);

        // 2. Restaurant & Kitchen Staff
        $owner = User::create([
            'full_name' => 'Restaurant Owner',
            'email' => 'owner.kitchen.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);

        $this->restaurant = Restaurant::create([
            'code' => 'REST-KITCHEN-' . uniqid(),
            'name' => 'Central Kitchen',
            'owner_user_id' => $owner->id,
            'status' => 'active',
            'commission_default_pct' => 12.00,
        ]);

        $this->kitchenStaffUser = User::create([
            'full_name' => 'Chef Mengly',
            'email' => 'mengly.chef.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->kitchenStaffUser->assignRole('kitchen_staff');

        RestaurantStaff::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->kitchenStaffUser->id,
            'job_title' => 'Head Chef',
            'status' => 'active',
        ]);

        // 3. Driver & Driver User
        $this->driverUser = User::create([
            'full_name' => 'Driver Rathana',
            'email' => 'rathana.driver.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->driverUser->assignRole('driver');

        $this->driver = Driver::create([
            'user_id' => $this->driverUser->id,
            'driver_code' => 'DRV-' . uniqid(),
            'phone' => '+85598765432',
            'vehicle_type' => 'motorcycle',
            'vehicle_plate' => '1BC-5678',
            'status' => 'active',
            'self_delivery' => false,
        ]);

        // 4. School, Location & Student
        $this->school = School::create([
            'code' => 'SCH-KITCHEN-' . uniqid(),
            'name' => 'Kitchen Operations Academy',
            'level' => 'primary',
            'status' => 'active',
            'address' => 'Tuol Kork, Phnom Penh',
        ]);

        SchoolMealPolicy::create([
            'school_id' => $this->school->id,
            'default_cutoff_minutes' => 60,
            'allowed_meal_types' => ['lunch'],
            'allow_cod' => true,
        ]);

        $this->shift = SchoolShift::create([
            'school_id' => $this->school->id,
            'name' => 'Lunch',
            'start_time' => '11:30:00',
            'end_time' => '13:00:00',
        ]);

        $this->location = SchoolLocation::create([
            'school_id' => $this->school->id,
            'name' => 'Grade 3 Wing Building C',
            'instructions' => 'Building C Floor 2 Room 204',
        ]);

        $this->parentUser = User::create([
            'full_name' => 'Secret Parent',
            'email' => 'secret.parent.' . uniqid() . '@example.com',
            'phone' => '+85599887766',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->parentUser->assignRole('parent');
        $guardian = Guardian::create(['user_id' => $this->parentUser->id]);

        $this->student = Student::create([
            'student_code' => 'STU-KITCHEN-' . uniqid(),
            'school_id' => $this->school->id,
            'school_shift_id' => $this->shift->id,
            'full_name' => 'Chanthy Student',
            'grade_level' => 'Grade 3',
            'class_name' => '3-B',
            'status' => 'active',
        ]);

        $this->student->guardians()->attach($guardian->id, [
            'is_primary' => true,
            'can_order' => true,
        ]);

        $peanut = Allergen::firstOrCreate(['code' => 'PEANUTS'], ['name' => 'Peanuts']);
        StudentNutritionProfile::create([
            'student_id' => $this->student->id,
            'allergen_ids' => [$peanut->id],
            'dietary_preferences' => ['nut-free', 'halal'],
        ]);

        // 5. Menu & Orders
        $this->menuItem = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Khmer Fish Amok Bowl',
            'sku' => 'AMOK-' . uniqid(),
            'meal_type' => 'lunch',
            'status' => 'active',
        ]);

        $this->menuVersion = MenuVersion::create([
            'menu_item_id' => $this->menuItem->id,
            'version_no' => 1,
            'price_minor' => 450,
            'status' => 'active',
            'published_at' => now(),
        ]);

        $this->order = Order::create([
            'order_number' => 'ORD-KIT-' . uniqid(),
            'guardian_id' => $guardian->id,
            'student_id' => $this->student->id,
            'school_id' => $this->school->id,
            'restaurant_id' => $this->restaurant->id,
            'status' => 'confirmed',
            'subtotal_minor' => 900,
            'total_minor' => 900,
            'currency' => 'USD',
            'placed_at' => now(),
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $this->order->id,
            'menu_version_id' => $this->menuVersion->id,
            'item_name_snapshot' => 'Khmer Fish Amok Bowl',
            'unit_price_minor' => 450,
            'quantity' => 2,
            'line_total_minor' => 900,
        ]);

        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $today = Carbon::now($tz)->toDateString();

        $this->schedule1 = OrderSchedule::create([
            'order_id' => $this->order->id,
            'order_item_id' => $orderItem->id,
            'delivery_date' => $today,
            'shift' => 'Lunch',
            'school_location_id' => $this->location->id,
            'status' => 'scheduled',
        ]);

        $this->schedule2 = OrderSchedule::create([
            'order_id' => $this->order->id,
            'order_item_id' => $orderItem->id,
            'delivery_date' => $today,
            'shift' => 'Lunch',
            'school_location_id' => $this->location->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_kitchen_queue_aggregates_meals_and_masks_guardian_private_data(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $today = Carbon::now($tz)->toDateString();

        $response = $this->actingAs($this->kitchenStaffUser)
            ->getJson("/api/v1/restaurant/kitchen/queue?delivery_date={$today}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_portions', 2);

        // Check aggregated quantities
        $aggregated = $response->json('data.aggregated_meals.0');
        $this->assertEquals($this->menuVersion->id, $aggregated['menu_version_id']);
        $this->assertEquals('Khmer Fish Amok Bowl', $aggregated['item_name']);
        $this->assertEquals(2, $aggregated['total_quantity']);
        $this->assertEquals(2, $aggregated['pending_quantity']);

        // Check individual schedule card
        $scheduleCard = $response->json('data.schedules.0');
        $this->assertEquals($this->student->full_name, $scheduleCard['student']['full_name']);
        $this->assertEquals('Grade 3', $scheduleCard['student']['grade_level']);
        $this->assertEquals('3-B', $scheduleCard['student']['class_name']);
        $this->assertContains('nut-free', $scheduleCard['dietary_preferences']);
        $this->assertEquals('Grade 3 Wing Building C', $scheduleCard['location']['name']);
        $this->assertEquals('Building C Floor 2 Room 204', $scheduleCard['location']['instructions']);

        // PRIVACY ENFORCEMENT: Guardian phone and email must NEVER be leaked in kitchen queue
        $rawResponse = $response->getContent();
        $this->assertStringNotContainsString('Secret Parent', $rawResponse);
        $this->assertStringNotContainsString('+85599887766', $rawResponse);
        $this->assertStringNotContainsString('secret.parent', $rawResponse);
    }

    public function test_kitchen_schedule_status_transition_updates_schedules_and_parent_order(): void
    {
        // 1. Transition schedule 1 to 'preparing' -> order should become 'preparing'
        $preparingResponse = $this->actingAs($this->kitchenStaffUser)
            ->patchJson('/api/v1/restaurant/kitchen/schedules/status', [
                'schedule_ids' => [$this->schedule1->id],
                'status' => 'preparing',
            ]);

        $preparingResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('preparing', $this->schedule1->fresh()->status);
        $this->assertEquals('preparing', $this->order->fresh()->status);

        // 2. Transition both schedules to 'ready' -> order should become 'ready'
        $readyResponse = $this->actingAs($this->kitchenStaffUser)
            ->patchJson('/api/v1/restaurant/kitchen/schedules/status', [
                'schedule_ids' => [$this->schedule1->id, $this->schedule2->id],
                'status' => 'ready',
            ]);

        $readyResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('prepared', $this->schedule1->fresh()->status);
        $this->assertEquals('prepared', $this->schedule2->fresh()->status);
        $this->assertEquals('ready', $this->order->fresh()->status);
    }

    public function test_admin_or_dispatcher_can_create_delivery_batch_with_sequenced_stops(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $today = Carbon::now($tz)->toDateString();

        $response = $this->actingAs($this->kitchenStaffUser)
            ->postJson('/api/v1/delivery/batches', [
                'restaurant_id' => $this->restaurant->id,
                'driver_id' => $this->driver->id,
                'delivery_date' => $today,
                'shift' => 'Lunch',
                'schedule_ids' => [$this->schedule1->id, $this->schedule2->id],
                'notes' => 'Campus main entrance delivery',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.restaurant.id', $this->restaurant->id)
            ->assertJsonPath('data.driver.id', $this->driver->id)
            ->assertJsonPath('data.status', 'assigned')
            ->assertJsonPath('data.total_stops', 2);

        $batchId = $response->json('data.id');
        $stops = DeliveryStop::where('delivery_batch_id', $batchId)->orderBy('sequence_no')->get();

        $this->assertCount(2, $stops);
        $this->assertEquals(1, $stops[0]->sequence_no);
        $this->assertEquals(2, $stops[1]->sequence_no);
        $this->assertEquals('pending', $stops[0]->status);
    }

    public function test_driver_can_fetch_today_assigned_batches(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $today = Carbon::now($tz)->toDateString();

        // Create batch for driver
        $batch = DeliveryBatch::create([
            'restaurant_id' => $this->restaurant->id,
            'driver_id' => $this->driver->id,
            'delivery_date' => $today,
            'shift' => 'Lunch',
            'status' => 'assigned',
        ]);

        DeliveryStop::create([
            'delivery_batch_id' => $batch->id,
            'order_schedule_id' => $this->schedule1->id,
            'school_id' => $this->school->id,
            'school_location_id' => $this->location->id,
            'sequence_no' => 1,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->driverUser)
            ->getJson('/api/v1/driver/batches/today');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_batches', 1)
            ->assertJsonPath('data.batches.0.id', $batch->id)
            ->assertJsonPath('data.batches.0.stops.0.package.student_name', 'Chanthy Student');
    }

    public function test_driver_pickup_batch_updates_batch_and_stops_status(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $today = Carbon::now($tz)->toDateString();

        $batch = DeliveryBatch::create([
            'restaurant_id' => $this->restaurant->id,
            'driver_id' => $this->driver->id,
            'delivery_date' => $today,
            'shift' => 'Lunch',
            'status' => 'assigned',
        ]);

        $stop = DeliveryStop::create([
            'delivery_batch_id' => $batch->id,
            'order_schedule_id' => $this->schedule1->id,
            'school_id' => $this->school->id,
            'school_location_id' => $this->location->id,
            'sequence_no' => 1,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->driverUser)
            ->postJson("/api/v1/driver/batches/{$batch->id}/pickup");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'picked_up');

        $this->assertEquals('picked_up', $batch->fresh()->status);
        $this->assertNotNull($batch->fresh()->pickup_at);
        $this->assertEquals('picked_up', $stop->fresh()->status);
    }

    public function test_driver_package_verify_scan_enforces_barcode_matching(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $batch = DeliveryBatch::create([
            'restaurant_id' => $this->restaurant->id,
            'driver_id' => $this->driver->id,
            'delivery_date' => Carbon::now($tz)->toDateString(),
            'status' => 'picked_up',
        ]);

        $stop = DeliveryStop::create([
            'delivery_batch_id' => $batch->id,
            'order_schedule_id' => $this->schedule1->id,
            'school_id' => $this->school->id,
            'school_location_id' => $this->location->id,
            'sequence_no' => 1,
            'status' => 'picked_up',
        ]);

        // 1. Mismatch code -> must fail with 422
        $mismatchResponse = $this->actingAs($this->driverUser)
            ->postJson("/api/v1/driver/stops/{$stop->id}/verify-scan", [
                'package_code' => 'WRONG-BARCODE-999',
            ]);

        $mismatchResponse->assertStatus(422)
            ->assertJsonValidationErrors(['package_code']);

        // 2. Matching schedule ID -> must succeed
        $matchResponse = $this->actingAs($this->driverUser)
            ->postJson("/api/v1/driver/stops/{$stop->id}/verify-scan", [
                'package_code' => $this->schedule1->id,
            ]);

        $matchResponse->assertStatus(200)
            ->assertJsonPath('verified', true)
            ->assertJsonPath('data.stop_id', $stop->id);
    }

    public function test_driver_complete_stop_with_proof_of_delivery_and_delivered_not_consumed_rule(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $batch = DeliveryBatch::create([
            'restaurant_id' => $this->restaurant->id,
            'driver_id' => $this->driver->id,
            'delivery_date' => Carbon::now($tz)->toDateString(),
            'status' => 'picked_up',
        ]);

        $stop = DeliveryStop::create([
            'delivery_batch_id' => $batch->id,
            'order_schedule_id' => $this->schedule1->id,
            'school_id' => $this->school->id,
            'school_location_id' => $this->location->id,
            'sequence_no' => 1,
            'status' => 'picked_up',
        ]);

        // Complete stop with signature and photo proof
        $response = $this->actingAs($this->driverUser)
            ->postJson("/api/v1/driver/stops/{$stop->id}/complete", [
                'proof_type' => 'signature',
                'photo_url' => 'https://s3.schoolmeals.com/pod/tray_handed_over.jpg',
                'signature_url' => 'https://s3.schoolmeals.com/pod/teacher_sig.png',
                'recipient_name' => 'Homeroom Teacher Chenda',
                'notes' => 'Received at classroom door',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'delivered')
            ->assertJsonPath('data.proof.proof_type', 'signature')
            ->assertJsonPath('data.proof.recipient_name', 'Homeroom Teacher Chenda');

        // Verify stop and schedule marked delivered
        $this->assertEquals('delivered', $stop->fresh()->status);
        $this->assertNotNull($stop->fresh()->completed_at);
        $this->assertEquals('delivered', $this->schedule1->fresh()->status);
        $this->assertNotNull($this->schedule1->fresh()->delivered_at);

        // DELIVERED != CONSUMED RULE (PB-054):
        // Confirm that NO nutrition consumption log was automatically created or marked as consumed
        $consumptionCount = NutritionConsumptionLog::where('order_schedule_id', $this->schedule1->id)->count();
        $this->assertEquals(0, $consumptionCount);
    }

    public function test_driver_record_delivery_exception(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $batch = DeliveryBatch::create([
            'restaurant_id' => $this->restaurant->id,
            'driver_id' => $this->driver->id,
            'delivery_date' => Carbon::now($tz)->toDateString(),
            'status' => 'picked_up',
        ]);

        $stop = DeliveryStop::create([
            'delivery_batch_id' => $batch->id,
            'order_schedule_id' => $this->schedule1->id,
            'school_id' => $this->school->id,
            'school_location_id' => $this->location->id,
            'sequence_no' => 1,
            'status' => 'picked_up',
        ]);

        $response = $this->actingAs($this->driverUser)
            ->postJson("/api/v1/driver/stops/{$stop->id}/exception", [
                'exception_code' => 'student_absent',
                'exception_note' => 'Student was not at school today due to illness',
                'action' => 'failed',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'failed')
            ->assertJsonPath('data.exception_code', 'student_absent');

        $this->assertEquals('failed', $stop->fresh()->status);
        $this->assertEquals('failed', $this->schedule1->fresh()->status);
    }
}
