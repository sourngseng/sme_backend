<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\DeliveryBatch;
use App\Models\DeliveryStop;
use App\Models\Driver;
use App\Models\Guardian;
use App\Models\MenuItem;
use App\Models\MenuVersion;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSchedule;
use App\Models\ProductionBatch;
use App\Models\ProductionItem;
use App\Models\ProofOfDelivery;
use App\Models\Restaurant;
use App\Models\School;
use App\Models\SchoolLocation;
use App\Models\SchoolShift;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KitchenAndDeliveryDomainModelTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Restaurant $restaurant;
    protected School $school;
    protected SchoolLocation $location;
    protected OrderSchedule $schedule;
    protected MenuVersion $menuVersion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'full_name' => 'Kitchen Operations User',
            'email' => 'kitchen.ops.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);

        $this->restaurant = Restaurant::create([
            'code' => 'REST-OPS-' . uniqid(),
            'name' => 'Operations Kitchen',
            'owner_user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $this->school = School::create([
            'code' => 'SCH-OPS-' . uniqid(),
            'name' => 'Operations Academy',
            'level' => 'primary',
            'status' => 'active',
        ]);

        $shift = SchoolShift::create([
            'school_id' => $this->school->id,
            'name' => 'Lunch',
            'start_time' => '11:30:00',
            'end_time' => '13:00:00',
        ]);

        $this->location = SchoolLocation::create([
            'school_id' => $this->school->id,
            'name' => 'Building A Cafeteria',
        ]);

        $guardian = Guardian::create(['user_id' => $this->user->id]);

        $student = Student::create([
            'student_code' => 'STU-OPS-' . uniqid(),
            'school_id' => $this->school->id,
            'school_shift_id' => $shift->id,
            'full_name' => 'Ops Student',
            'status' => 'active',
        ]);

        $menuItem = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Curry Rice',
            'sku' => 'CURRY-' . uniqid(),
            'meal_type' => 'lunch',
            'status' => 'active',
        ]);

        $this->menuVersion = MenuVersion::create([
            'menu_item_id' => $menuItem->id,
            'version_no' => 1,
            'price_minor' => 450,
            'status' => 'active',
            'published_at' => now(),
        ]);

        $order = Order::create([
            'order_number' => 'ORD-OPS-' . uniqid(),
            'guardian_id' => $guardian->id,
            'student_id' => $student->id,
            'school_id' => $this->school->id,
            'restaurant_id' => $this->restaurant->id,
            'status' => 'confirmed',
            'subtotal_minor' => 450,
            'total_minor' => 450,
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'menu_version_id' => $this->menuVersion->id,
            'item_name_snapshot' => 'Curry Rice',
            'unit_price_minor' => 450,
            'quantity' => 1,
            'line_total_minor' => 450,
        ]);

        $this->schedule = OrderSchedule::create([
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'delivery_date' => Carbon::now()->addDays(2)->toDateString(),
            'shift' => 'Lunch',
            'school_location_id' => $this->location->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_production_batch_and_items_lifecycle_with_auditing(): void
    {
        $this->actingAs($this->user);

        $batch = ProductionBatch::create([
            'restaurant_id' => $this->restaurant->id,
            'production_date' => Carbon::now()->toDateString(),
            'shift' => 'Lunch',
            'status' => 'planned',
        ]);

        $this->assertNotNull($batch->id);
        $this->assertEquals($this->restaurant->id, $batch->restaurant->id);

        $item = ProductionItem::create([
            'production_batch_id' => $batch->id,
            'menu_version_id' => $this->menuVersion->id,
            'total_quantity' => 50,
            'prepared_quantity' => 0,
            'status' => 'pending',
        ]);

        $this->assertNotNull($item->id);
        $this->assertEquals($batch->id, $item->batch->id);
        $this->assertEquals(50, $item->total_quantity);
        $this->assertCount(1, $batch->items);

        // Update batch status to preparing
        $batch->update(['status' => 'preparing']);
        $this->assertEquals('preparing', $batch->fresh()->status);

        // Verify audit log
        $audit = AuditLog::where('auditable_type', ProductionBatch::class)
            ->where('auditable_id', $batch->id)
            ->whereIn('action', ['updated', 'status_updated'])
            ->first();

        $this->assertNotNull($audit);
    }

    public function test_driver_creation_and_unique_driver_code(): void
    {
        $driverUser = User::create([
            'full_name' => 'Driver Bunthan',
            'email' => 'driver.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);

        $driver = Driver::create([
            'user_id' => $driverUser->id,
            'driver_code' => 'DRV-1001',
            'phone' => '+85512345678',
            'vehicle_type' => 'motorcycle',
            'vehicle_plate' => '1AB-1234',
            'license_no' => 'LIC-778899',
            'status' => 'active',
            'self_delivery' => true,
        ]);

        $this->assertNotNull($driver->id);
        $this->assertTrue($driver->self_delivery);
        $this->assertEquals($driverUser->id, $driver->user->id);

        // Duplicate driver code should throw QueryException
        $this->expectException(QueryException::class);
        Driver::create([
            'user_id' => $this->user->id,
            'driver_code' => 'DRV-1001',
        ]);
    }

    public function test_delivery_batch_and_stops_hierarchy_with_proof(): void
    {
        $driverUser = User::create([
            'full_name' => 'Driver Sopheap',
            'email' => 'driver.sopheap.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);

        $driver = Driver::create([
            'user_id' => $driverUser->id,
            'driver_code' => 'DRV-' . uniqid(),
            'status' => 'active',
        ]);

        $deliveryBatch = DeliveryBatch::create([
            'restaurant_id' => $this->restaurant->id,
            'driver_id' => $driver->id,
            'delivery_date' => Carbon::now()->toDateString(),
            'shift' => 'lunch',
            'status' => 'assigned',
            'notes' => 'Deliver before 11:30 AM',
        ]);

        $this->assertNotNull($deliveryBatch->id);
        $this->assertEquals($driver->id, $deliveryBatch->driver->id);

        $stop = DeliveryStop::create([
            'delivery_batch_id' => $deliveryBatch->id,
            'order_schedule_id' => $this->schedule->id,
            'school_id' => $this->school->id,
            'school_location_id' => $this->location->id,
            'sequence_no' => 1,
            'status' => 'delivered',
            'arrived_at' => Carbon::now()->subMinutes(10),
            'completed_at' => Carbon::now(),
        ]);

        $this->assertNotNull($stop->id);
        $this->assertEquals($deliveryBatch->id, $stop->batch->id);
        $this->assertEquals($this->schedule->id, $stop->schedule->id);
        $this->assertEquals($this->location->id, $stop->location->id);
        $this->assertCount(1, $deliveryBatch->stops);

        // Attach Proof of Delivery
        $pod = ProofOfDelivery::create([
            'delivery_stop_id' => $stop->id,
            'proof_type' => 'QR',
            'photo_url' => 'https://storage.schoolmeals.com/pod/meal123.jpg',
            'recipient_name' => 'Teacher Meas',
            'otp_verified' => true,
            'notes' => 'Handed over directly to classroom representative',
        ]);

        $this->assertNotNull($pod->id);
        $this->assertTrue($pod->otp_verified);
        $this->assertEquals($stop->id, $pod->stop->id);
        $this->assertEquals($pod->id, $stop->fresh()->proof->id);

        // ProofOfDelivery is unique per delivery_stop_id
        $this->expectException(QueryException::class);
        ProofOfDelivery::create([
            'delivery_stop_id' => $stop->id,
            'proof_type' => 'signature',
        ]);
    }
}
