<?php

namespace Tests\Feature;

use App\Models\Allergen;
use App\Models\Guardian;
use App\Models\MenuItem;
use App\Models\MenuVersion;
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
use App\Models\Settlement;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IdorAndCrossTenantSecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Ensure core roles exist
        Role::firstOrCreate(['name' => 'parent'], ['guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'restaurant_owner'], ['guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'restaurant_staff'], ['guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'school_admin'], ['guard_name' => 'web']);

        $perms = [
            'orders.view', 'orders.create', 'orders.update',
            'students.view', 'students.create', 'students.update',
            'schools.view',
            'menu_items.create', 'menu_items.update', 'menu_items.delete',
        ];

        foreach ($perms as $pName) {
            $p = Permission::firstOrCreate(['name' => $pName]);
            Role::where('name', 'parent')->first()?->permissions()->syncWithoutDetaching([$p->id]);
            Role::where('name', 'restaurant_owner')->first()?->permissions()->syncWithoutDetaching([$p->id]);
            Role::where('name', 'school_admin')->first()?->permissions()->syncWithoutDetaching([$p->id]);
        }
    }

    /**
     * PB-005, PB-078: Verify Parent A receives 403 Forbidden when viewing/ordering/skipping meals for Parent B's child.
     */
    public function test_parent_a_cannot_view_order_or_skip_meals_for_parent_b_child(): void
    {
        $tz = 'Asia/Phnom_Penh';
        Carbon::setTestNow(Carbon::parse('2026-10-26 07:00:00', $tz));

        // School & Restaurant setup
        $school = School::create([
            'code' => 'SCH-IDOR-' . uniqid(),
            'name' => 'IDOR Test Academy',
            'level' => 'primary',
            'address' => 'Phnom Penh, Cambodia',
            'status' => 'active',
            'timezone' => $tz,
        ]);

        SchoolMealPolicy::create([
            'school_id' => $school->id,
            'allow_cod' => false,
            'daily_order_cutoff_time' => '08:30:00',
        ]);

        $shift = SchoolShift::create([
            'school_id' => $school->id,
            'name' => 'Lunch',
            'start_time' => '11:30:00',
            'end_time' => '12:30:00',
            'cutoff_time' => '08:30:00',
        ]);

        $location = SchoolLocation::create([
            'school_id' => $school->id,
            'name' => 'East Cafeteria',
            'is_active' => true,
        ]);

        $restaurantOwner = User::create([
            'full_name' => 'Chef Owner IDOR',
            'email' => 'chef.idor.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);

        $restaurant = Restaurant::create([
            'code' => 'REST-IDOR-' . uniqid(),
            'name' => 'Healthy Bowl Bistro',
            'owner_user_id' => $restaurantOwner->id,
            'status' => 'active',
            'is_active' => true,
            'commission_default_pct' => 12.00,
            'timezone' => $tz,
        ]);

        $menuItem = MenuItem::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Steamed Rice with Veggies',
            'meal_type' => 'lunch',
            'status' => 'active',
        ]);

        $menuVersion = MenuVersion::create([
            'menu_item_id' => $menuItem->id,
            'version_no' => 1,
            'price_minor' => 350,
            'currency' => 'USD',
            'status' => 'active',
            'published_at' => now(),
        ]);

        // Parent A & Child A
        $userA = User::create([
            'full_name' => 'Parent Alice',
            'email' => 'alice.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $userA->assignRole('parent');
        $guardianA = Guardian::create(['user_id' => $userA->id, 'relationship' => 'Mother']);

        $studentA = Student::create([
            'school_id' => $school->id,
            'school_shift_id' => $shift->id,
            'student_code' => 'STU-A-' . uniqid(),
            'full_name' => 'Child Alice Junior',
            'grade_level' => 'Grade 3',
            'status' => 'active',
        ]);
        $guardianA->students()->attach($studentA->id, [
            'is_primary' => true,
            'can_order' => true,
            'can_view_nutrition' => true,
        ]);

        // Parent B & Child B
        $userB = User::create([
            'full_name' => 'Parent Bob',
            'email' => 'bob.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $userB->assignRole('parent');
        $guardianB = Guardian::create(['user_id' => $userB->id, 'relationship' => 'Father']);

        $studentB = Student::create([
            'school_id' => $school->id,
            'school_shift_id' => $shift->id,
            'student_code' => 'STU-B-' . uniqid(),
            'full_name' => 'Child Bob Junior',
            'grade_level' => 'Grade 4',
            'status' => 'active',
        ]);
        $guardianB->students()->attach($studentB->id, [
            'is_primary' => true,
            'can_order' => true,
            'can_view_nutrition' => true,
        ]);

        // 1. Parent A attempts to view Child B's details via Guardian endpoint -> 403 Forbidden
        $viewChildB = $this->actingAs($userA, 'sanctum')->getJson("/api/v1/guardian/students/{$studentB->id}");
        $viewChildB->assertStatus(403);

        // 2. Parent A attempts to update Child B's nutrition profile -> 403 Forbidden
        $updateNutritionB = $this->actingAs($userA, 'sanctum')->putJson("/api/v1/guardian/students/{$studentB->id}/nutrition-profile", [
            'notes' => 'Unauthorized peanut allergy injection',
        ]);
        $updateNutritionB->assertStatus(403);

        // 3. Parent A attempts to order a meal for Child B -> 403 Forbidden
        $orderForChildB = $this->actingAs($userA, 'sanctum')->postJson('/api/v1/orders', [
            'student_id' => $studentB->id,
            'restaurant_id' => $restaurant->id,
            'school_location_id' => $location->id,
            'items' => [
                [
                    'menu_version_id' => $menuVersion->id,
                    'quantity' => 1,
                    'delivery_date' => '2026-10-27',
                    'shift' => 'Lunch',
                ],
            ],
        ], [
            'X-Idempotency-Key' => 'IDEM-ORDER-FORGE-' . uniqid(),
        ]);
        $orderForChildB->assertStatus(403);

        // 4. Create an authentic Order and Schedule for Parent B
        $orderB = Order::create([
            'order_number' => 'ORD-B-' . uniqid(),
            'guardian_id' => $guardianB->id,
            'student_id' => $studentB->id,
            'school_id' => $school->id,
            'restaurant_id' => $restaurant->id,
            'status' => 'confirmed',
            'currency' => 'USD',
            'subtotal_minor' => 350,
            'total_minor' => 350,
            'placed_at' => now(),
        ]);

        $itemB = OrderItem::create([
            'order_id' => $orderB->id,
            'menu_version_id' => $menuVersion->id,
            'item_name_snapshot' => $menuItem->name,
            'unit_price_minor' => 350,
            'quantity' => 1,
            'subtotal_minor' => 350,
        ]);

        $scheduleB = OrderSchedule::create([
            'order_id' => $orderB->id,
            'order_item_id' => $itemB->id,
            'school_location_id' => $location->id,
            'delivery_date' => '2026-10-27',
            'shift' => 'Lunch',
            'cutoff_at' => Carbon::parse('2026-10-27 08:30:00', $tz),
            'status' => 'confirmed',
        ]);

        // 5. Parent A attempts to view Parent B's order -> 403 Forbidden
        $viewOrderB = $this->actingAs($userA, 'sanctum')->getJson("/api/v1/orders/{$orderB->id}");
        $viewOrderB->assertStatus(403);

        // 6. Parent A attempts to skip Parent B's meal schedule -> 403 Forbidden
        $skipScheduleB = $this->actingAs($userA, 'sanctum')->postJson("/api/v1/orders/schedules/{$scheduleB->id}/skip", [
            'reason' => 'Malicious parent cancellation',
        ]);
        $skipScheduleB->assertStatus(403);

        // 7. Parent A attempts to cancel Parent B's meal schedule -> 403 Forbidden
        $cancelScheduleB = $this->actingAs($userA, 'sanctum')->postJson("/api/v1/orders/schedules/{$scheduleB->id}/cancel", [
            'reason' => 'Malicious parent cancellation',
        ]);
        $cancelScheduleB->assertStatus(403);

        Carbon::setTestNow(null);
    }

    /**
     * PB-005, PB-078: Verify Restaurant A cannot view or mutate Restaurant B's orders, menus, or settlements.
     */
    public function test_restaurant_a_cannot_view_or_mutate_restaurant_b_orders_menus_or_settlements(): void
    {
        $tz = 'Asia/Phnom_Penh';

        // Merchant A
        $userOwnerA = User::create([
            'full_name' => 'Owner Restaurant A',
            'email' => 'owner.a.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $userOwnerA->assignRole('restaurant_owner');

        $restaurantA = Restaurant::create([
            'code' => 'REST-A-' . uniqid(),
            'name' => 'Restaurant Alpha',
            'owner_user_id' => $userOwnerA->id,
            'status' => 'active',
            'is_active' => true,
            'timezone' => $tz,
        ]);
        $userOwnerA->update(['restaurant_id' => $restaurantA->id]);

        // Merchant B
        $userOwnerB = User::create([
            'full_name' => 'Owner Restaurant B',
            'email' => 'owner.b.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $userOwnerB->assignRole('restaurant_owner');

        $restaurantB = Restaurant::create([
            'code' => 'REST-B-' . uniqid(),
            'name' => 'Restaurant Beta',
            'owner_user_id' => $userOwnerB->id,
            'status' => 'active',
            'is_active' => true,
            'timezone' => $tz,
        ]);
        $userOwnerB->update(['restaurant_id' => $restaurantB->id]);

        // Menu Item B belonging to Restaurant B
        $menuItemB = MenuItem::create([
            'restaurant_id' => $restaurantB->id,
            'name' => 'Beta Special Salmon Roll',
            'sku' => 'BETA-SALMON-' . uniqid(),
            'meal_type' => 'lunch',
            'status' => 'active',
        ]);

        MenuVersion::create([
            'menu_item_id' => $menuItemB->id,
            'version_no' => 1,
            'price_minor' => 600,
            'currency' => 'USD',
            'status' => 'active',
            'published_at' => now(),
        ]);

        // 1. Restaurant A attempts to update Restaurant B's menu item -> 403 Forbidden
        $mutateMenuB = $this->actingAs($userOwnerA, 'sanctum')->patchJson("/api/v1/restaurant/menu-items/{$menuItemB->id}", [
            'name' => 'Compromised Name by Rival Merchant',
            'price_minor' => 100,
        ]);
        $mutateMenuB->assertStatus(403);

        // 2. Restaurant A attempts to delete Restaurant B's menu item -> 403 Forbidden
        $deleteMenuB = $this->actingAs($userOwnerA, 'sanctum')->deleteJson("/api/v1/restaurant/menu-items/{$menuItemB->id}");
        $deleteMenuB->assertStatus(403);

        // 3. Restaurant B Order
        $school = School::create([
            'code' => 'SCH-REST-' . uniqid(),
            'name' => 'Merchant Test Academy',
            'level' => 'secondary',
            'address' => 'Phnom Penh',
            'status' => 'active',
        ]);

        $parent = User::create([
            'full_name' => 'Customer Parent',
            'email' => 'customer.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $guardian = Guardian::create(['user_id' => $parent->id]);

        $student = Student::create([
            'school_id' => $school->id,
            'student_code' => 'STU-REST-' . uniqid(),
            'full_name' => 'Student Beta',
            'status' => 'active',
        ]);

        $orderB = Order::create([
            'order_number' => 'ORD-REST-B-' . uniqid(),
            'guardian_id' => $guardian->id,
            'student_id' => $student->id,
            'school_id' => $school->id,
            'restaurant_id' => $restaurantB->id,
            'status' => 'confirmed',
            'currency' => 'USD',
            'subtotal_minor' => 600,
            'total_minor' => 600,
            'placed_at' => now(),
        ]);

        // Restaurant A attempts to view Restaurant B's order -> 403 Forbidden
        $viewOrderB = $this->actingAs($userOwnerA, 'sanctum')->getJson("/api/v1/orders/{$orderB->id}");
        $viewOrderB->assertStatus(403);

        // 4. Restaurant B Settlement
        $settlementB = Settlement::create([
            'settlement_number' => 'SET-B-' . uniqid(),
            'restaurant_id' => $restaurantB->id,
            'period_start' => now()->startOfWeek(),
            'period_end' => now()->endOfWeek(),
            'total_orders' => 10,
            'gross_amount_minor' => 6000,
            'commission_rate' => 10.00,
            'commission_amount_minor' => 600,
            'tax_amount_minor' => 0,
            'net_payout_minor' => 5400,
            'currency' => 'USD',
            'status' => 'pending',
        ]);

        // Restaurant A attempts to view Restaurant B's settlement -> 403 Forbidden
        $viewSettlementB = $this->actingAs($userOwnerA, 'sanctum')->getJson("/api/v1/restaurant/settlements/{$settlementB->id}");
        $viewSettlementB->assertStatus(403);
    }

    /**
     * PB-005, PB-078: Verify School Admin A cannot access School B's student roster or individual students.
     */
    public function test_school_admin_a_cannot_access_school_b_student_roster_or_details(): void
    {
        $tz = 'Asia/Phnom_Penh';

        // School A & Admin A
        $schoolA = School::create([
            'code' => 'SCH-A-' . uniqid(),
            'name' => 'Academy Alpha',
            'level' => 'primary',
            'address' => 'Tuol Kork, Phnom Penh',
            'status' => 'active',
            'timezone' => $tz,
        ]);

        $adminA = User::create([
            'full_name' => 'Admin Alpha',
            'email' => 'admin.a.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
            'school_id' => $schoolA->id,
        ]);
        $adminA->assignRole('school_admin');

        $studentA = Student::create([
            'school_id' => $schoolA->id,
            'student_code' => 'STU-ALPHA-' . uniqid(),
            'full_name' => 'Alpha Student One',
            'grade_level' => 'Grade 1',
            'status' => 'active',
        ]);

        // School B & Student B
        $schoolB = School::create([
            'code' => 'SCH-B-' . uniqid(),
            'name' => 'Academy Beta',
            'level' => 'secondary',
            'address' => 'BKK1, Phnom Penh',
            'status' => 'active',
            'timezone' => $tz,
        ]);

        $studentB = Student::create([
            'school_id' => $schoolB->id,
            'student_code' => 'STU-BETA-' . uniqid(),
            'full_name' => 'Beta Student One',
            'grade_level' => 'Grade 8',
            'status' => 'active',
        ]);

        // 1. School Admin A attempts to access School B meal roster -> 403 Forbidden
        $rosterResponse = $this->actingAs($adminA, 'sanctum')->getJson("/api/v1/schools/{$schoolB->id}/meal-roster");
        $rosterResponse->assertStatus(403);

        // 2. School Admin A attempts to view School B student detail -> 403 Forbidden
        $studentDetailResponse = $this->actingAs($adminA, 'sanctum')->getJson("/api/v1/students/{$studentB->id}");
        $studentDetailResponse->assertStatus(403);

        // 3. School Admin A queries student index -> Scoped strictly to School A, Student B is NEVER leaked
        $listResponse = $this->actingAs($adminA, 'sanctum')->getJson('/api/v1/students');
        $listResponse->assertStatus(200);

        $returnedStudentIds = collect($listResponse->json('data'))->pluck('id')->all();
        $this->assertContains($studentA->id, $returnedStudentIds);
        $this->assertNotContains($studentB->id, $returnedStudentIds);
    }

    /**
     * PB-042, PB-078: Verify forged webhook signatures and duplicate event_id callbacks are rejected.
     */
    public function test_forged_webhook_signatures_and_duplicate_event_id_callbacks_are_rejected(): void
    {
        $school = School::create([
            'code' => 'SCH-WEBHOOK-' . uniqid(),
            'name' => 'Security Academy',
            'level' => 'primary',
            'address' => 'Phnom Penh',
            'status' => 'active',
        ]);

        $parent = User::create([
            'full_name' => 'Security Parent',
            'email' => 'sec.parent.' . uniqid() . '@schoolmeal.test',
            'phone' => '+85512' . rand(100000, 999999),
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $guardian = Guardian::create(['user_id' => $parent->id]);

        $student = Student::create([
            'school_id' => $school->id,
            'student_code' => 'STU-SEC-' . uniqid(),
            'full_name' => 'Security Child',
            'status' => 'active',
        ]);

        $restaurant = Restaurant::create([
            'code' => 'REST-SEC-' . uniqid(),
            'name' => 'Secure Kitchen',
            'owner_user_id' => $parent->id,
            'status' => 'active',
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-SEC-' . uniqid(),
            'guardian_id' => $guardian->id,
            'student_id' => $student->id,
            'school_id' => $school->id,
            'restaurant_id' => $restaurant->id,
            'status' => 'placed',
            'currency' => 'USD',
            'subtotal_minor' => 850,
            'total_minor' => 850,
            'placed_at' => now(),
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'KHQR',
            'method' => 'bakong_qr',
            'status' => 'pending',
            'amount_minor' => 850,
            'currency' => 'USD',
            'provider_reference' => 'REF-SEC-' . uniqid(),
        ]);

        $secret = config('services.khqr.secret', 'khqr_webhook_secret_key');
        $eventId = 'EVT-IDEM-' . uniqid();

        $payload = [
            'event_id' => $eventId,
            'event_type' => 'payment.completed',
            'provider_reference' => $payment->provider_reference,
            'payment_id' => $payment->id,
            'status' => 'SUCCESS',
            'amount_minor' => 850,
            'currency' => 'USD',
        ];

        $rawJson = json_encode($payload);

        // 1. Forged Signature Attempt -> 401 Unauthorized
        $forgedResponse = $this->call(
            'POST',
            '/api/v1/webhooks/payments/khqr',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_SIGNATURE' => 'forged_fake_signature_hash',
            ],
            $rawJson
        );
        $forgedResponse->assertStatus(401);

        // Verify payment is still pending and not tampered
        $this->assertEquals('pending', $payment->fresh()->status);
        $this->assertEquals('placed', $order->fresh()->status);

        // 2. Legitimate Cryptographically Signed Callback -> 200 OK & Status Transitions
        $legitSignature = hash_hmac('sha256', $rawJson, $secret);

        $legitResponse = $this->call(
            'POST',
            '/api/v1/webhooks/payments/khqr',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_SIGNATURE' => $legitSignature,
            ],
            $rawJson
        );
        $legitResponse->assertStatus(200);

        $this->assertEquals('paid', $payment->fresh()->status);
        $this->assertEquals('confirmed', $order->fresh()->status);

        // 3. Duplicate event_id Replay Attack -> Must return idempotent already_processed
        $replayResponse = $this->call(
            'POST',
            '/api/v1/webhooks/payments/khqr',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_SIGNATURE' => $legitSignature,
            ],
            $rawJson
        );
        $replayResponse->assertStatus(200);
        $replayResponse->assertJson([
            'status' => 'already_processed',
            'event_id' => $eventId,
        ]);
    }
}
