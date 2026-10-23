<?php

namespace Tests\Feature;

use App\Models\Allergen;
use App\Models\CommissionRule;
use App\Models\Guardian;
use App\Models\MenuItem;
use App\Models\MenuVersion;
use App\Models\NutritionFact;
use App\Models\Order;
use App\Models\OrderSchedule;
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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderSchedulingAndCutoffTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $schoolAdmin;
    protected User $otherSchoolAdmin;
    protected User $parentUser;
    protected User $unauthorizedParent;
    protected Guardian $guardian;
    protected School $school;
    protected SchoolShift $lunchShift;
    protected SchoolLocation $location;
    protected Restaurant $restaurant;
    protected Student $student;
    protected Allergen $peanutAllergen;
    protected MenuItem $menuItemChicken;
    protected MenuVersion $menuVersionChicken;
    protected MenuItem $menuItemPeanut;
    protected MenuVersion $menuVersionPeanut;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles & Permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['guard_name' => 'web']);
        $schoolAdminRole = Role::firstOrCreate(['name' => 'school_admin'], ['guard_name' => 'web']);
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['guard_name' => 'web']);

        $perms = [
            'orders.view', 'orders.create', 'orders.update', 'orders.delete',
            'schools.view',
        ];

        foreach ($perms as $pName) {
            $p = Permission::firstOrCreate(['name' => $pName]);
            $parentRole->permissions()->syncWithoutDetaching([$p->id]);
            $schoolAdminRole->permissions()->syncWithoutDetaching([$p->id]);
            $superAdminRole->permissions()->syncWithoutDetaching([$p->id]);
        }

        // 2. School, Shift & Location
        $this->school = School::create([
            'code' => 'SCH-ORD-' . uniqid(),
            'name' => 'Phnom Penh International Academy',
            'level' => 'primary',
            'status' => 'active',
            'address' => 'Norodom Blvd, Phnom Penh',
        ]);

        SchoolMealPolicy::create([
            'school_id' => $this->school->id,
            'default_cutoff_minutes' => 120, // 2 hours
            'allowed_meal_types' => ['lunch', 'snack'],
            'allow_cod' => false,
        ]);

        $this->lunchShift = SchoolShift::create([
            'school_id' => $this->school->id,
            'name' => 'Lunch Shift',
            'start_time' => '11:30:00',
            'end_time' => '13:00:00',
        ]);

        $this->location = SchoolLocation::create([
            'school_id' => $this->school->id,
            'name' => 'Building B Cafeteria',
            'building' => 'Building B',
            'floor' => 'Ground Floor',
            'room' => 'Hall 1',
        ]);

        // 3. Restaurant Owner & Restaurant
        $merchantOwner = User::create([
            'full_name' => 'Kitchen Owner',
            'email' => 'owner.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);

        $this->restaurant = Restaurant::create([
            'code' => 'REST-' . uniqid(),
            'name' => 'NutriMeals Kitchen',
            'owner_user_id' => $merchantOwner->id,
            'status' => 'active',
            'commission_default_pct' => 12.50,
        ]);

        CommissionRule::create([
            'restaurant_id' => $this->restaurant->id,
            'commission_pct' => 15.00,
            'effective_from' => Carbon::now()->subDays(5)->toDateString(),
            'effective_to' => Carbon::now()->addDays(30)->toDateString(),
            'status' => 'active',
            'notes' => 'Active promotional commission tier',
        ]);

        // 4. Allergens & Menu Items
        $this->peanutAllergen = Allergen::firstOrCreate(
            ['code' => 'PEANUTS'],
            ['name' => 'Peanuts', 'description' => 'Peanut derivatives']
        );

        // Meal 1: Chicken Rice (Safe)
        $this->menuItemChicken = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Steamed Chicken Rice',
            'sku' => 'CHK-RC-' . uniqid(),
            'meal_type' => 'lunch',
            'status' => 'active',
        ]);

        $this->menuVersionChicken = MenuVersion::create([
            'menu_item_id' => $this->menuItemChicken->id,
            'version_no' => 1,
            'price_minor' => 350, // $3.50
            'currency' => 'USD',
            'status' => 'active',
            'cutoff_minutes' => 60,
            'max_daily_quantity' => 100,
            'published_at' => now(),
        ]);

        NutritionFact::create([
            'menu_version_id' => $this->menuVersionChicken->id,
            'calories_kcal' => 520.50,
            'protein_g' => 32.00,
            'carbohydrates_g' => 58.00,
            'fat_g' => 12.50,
        ]);

        // Meal 2: Satay Noodle Bowl (Contains Peanuts)
        $this->menuItemPeanut = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Peanut Satay Noodles',
            'sku' => 'SATAY-' . uniqid(),
            'meal_type' => 'lunch',
            'status' => 'active',
        ]);

        $this->menuVersionPeanut = MenuVersion::create([
            'menu_item_id' => $this->menuItemPeanut->id,
            'version_no' => 1,
            'price_minor' => 400, // $4.00
            'currency' => 'USD',
            'status' => 'active',
            'cutoff_minutes' => 60,
            'published_at' => now(),
        ]);

        $this->menuVersionPeanut->allergens()->attach($this->peanutAllergen->id, ['severity' => 'severe']);

        NutritionFact::create([
            'menu_version_id' => $this->menuVersionPeanut->id,
            'calories_kcal' => 610.00,
            'protein_g' => 22.00,
            'carbohydrates_g' => 70.00,
            'fat_g' => 20.00,
        ]);

        // 5. Users & Student
        $this->superAdmin = User::create([
            'full_name' => 'Super Admin',
            'email' => 'admin.orders.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->superAdmin->assignRole('super_admin');

        $this->schoolAdmin = User::create([
            'full_name' => 'School Admin Sokha',
            'email' => 'sokha.admin.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'school_id' => $this->school->id,
            'status' => 'active',
        ]);
        $this->schoolAdmin->assignRole('school_admin');

        $otherSchool = School::create([
            'code' => 'SCH-OTH-' . uniqid(),
            'name' => 'Other School Academy',
            'level' => 'secondary',
            'status' => 'active',
        ]);
        $this->otherSchoolAdmin = User::create([
            'full_name' => 'Other Admin Bun',
            'email' => 'bun.admin.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'school_id' => $otherSchool->id,
            'status' => 'active',
        ]);
        $this->otherSchoolAdmin->assignRole('school_admin');

        $this->parentUser = User::create([
            'full_name' => 'Parent Chanthy',
            'email' => 'chanthy.parent.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->parentUser->assignRole('parent');
        $this->guardian = Guardian::firstOrCreate(['user_id' => $this->parentUser->id]);

        $this->unauthorizedParent = User::create([
            'full_name' => 'Unrelated Parent',
            'email' => 'unrelated.parent.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->unauthorizedParent->assignRole('parent');

        // Student with peanut allergy
        $this->student = Student::create([
            'student_code' => 'STU-' . uniqid(),
            'school_id' => $this->school->id,
            'school_shift_id' => $this->lunchShift->id,
            'full_name' => 'Visal Chanthy',
            'grade_level' => 'Grade 4',
            'class_name' => '4-A',
            'status' => 'active',
        ]);

        $this->student->guardians()->attach($this->guardian->id, [
            'is_primary' => true,
            'can_order' => true,
            'can_view_nutrition' => true,
        ]);

        StudentNutritionProfile::create([
            'student_id' => $this->student->id,
            'allergen_ids' => [$this->peanutAllergen->id],
            'dietary_preferences' => ['halal'],
        ]);
    }

    public function test_guardian_can_place_multi_day_order_with_schedules_and_snapshots(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $day1 = Carbon::now($tz)->addDays(2)->toDateString();
        $day2 = Carbon::now($tz)->addDays(3)->toDateString();

        $payload = [
            'student_id' => $this->student->id,
            'restaurant_id' => $this->restaurant->id,
            'school_location_id' => $this->location->id,
            'notes' => 'Please serve with extra spoon',
            'items' => [
                [
                    'menu_version_id' => $this->menuVersionChicken->id,
                    'quantity' => 1,
                    'delivery_date' => $day1,
                    'shift' => 'Lunch Shift',
                ],
                [
                    'menu_version_id' => $this->menuVersionChicken->id,
                    'quantity' => 2,
                    'delivery_date' => $day2,
                    'shift' => 'Lunch Shift',
                ],
            ],
        ];

        $response = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/orders', $payload, ['Idempotency-Key' => 'idem-' . uniqid()]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.student.id', $this->student->id)
            ->assertJsonPath('data.school.id', $this->school->id)
            ->assertJsonPath('data.restaurant.id', $this->restaurant->id);

        $orderId = $response->json('data.id');
        $order = Order::with(['items', 'schedules'])->find($orderId);

        $this->assertNotNull($order);
        // Total items quantity = 1 + 2 = 3 portions @ $3.50 (350 minor) = 1050 minor
        $this->assertEquals(1050, $order->subtotal_minor);
        $this->assertEquals(1050, $order->total_minor);

        // Commission Rule = 15% of 1050 = 158 minor
        $this->assertEquals(158, $order->commission_minor);

        // Check immutable item snapshot
        $this->assertCount(1, $order->items);
        $item = $order->items->first();
        $this->assertEquals('Steamed Chicken Rice', $item->item_name_snapshot);
        $this->assertEquals(350, $item->unit_price_minor);
        $this->assertEquals(3, $item->quantity);
        $this->assertEquals(1050, $item->line_total_minor);
        $this->assertEquals(520.50, $item->calories_kcal_snapshot);
        $this->assertNotNull($item->nutrition_snapshot);
        $this->assertEquals(32.00, $item->nutrition_snapshot['protein_g']);

        // Check 3 schedules generated across the 2 delivery dates
        $day1Schedules = $order->schedules->filter(fn ($s) => Carbon::parse($s->delivery_date)->toDateString() === $day1);
        $day2Schedules = $order->schedules->filter(fn ($s) => Carbon::parse($s->delivery_date)->toDateString() === $day2);
        $this->assertCount(1, $day1Schedules);
        $this->assertCount(2, $day2Schedules);

        foreach ($order->schedules as $sched) {
            $this->assertEquals('scheduled', $sched->status);
            $this->assertEquals('Lunch Shift', $sched->shift);
            $this->assertNotNull($sched->cutoff_at);
        }
    }

    public function test_server_side_cutoff_enforcement_rejects_past_or_too_late_orders(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        // A date in the past
        $pastDate = Carbon::now($tz)->subDays(1)->toDateString();

        $payload = [
            'student_id' => $this->student->id,
            'restaurant_id' => $this->restaurant->id,
            'school_location_id' => $this->location->id,
            'items' => [
                [
                    'menu_version_id' => $this->menuVersionChicken->id,
                    'quantity' => 1,
                    'delivery_date' => $pastDate,
                    'shift' => 'Lunch Shift',
                ],
            ],
        ];

        $response = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/orders', $payload, ['Idempotency-Key' => 'idem-' . uniqid()]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.delivery_date']);
    }

    public function test_allergen_conflict_requires_explicit_warning_acknowledgment(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $futureDate = Carbon::now($tz)->addDays(4)->toDateString();

        // 1. Attempt order without allergen acknowledgment -> must fail with 422
        $payloadWithoutAck = [
            'student_id' => $this->student->id,
            'restaurant_id' => $this->restaurant->id,
            'school_location_id' => $this->location->id,
            'acknowledge_allergen_warning' => false,
            'items' => [
                [
                    'menu_version_id' => $this->menuVersionPeanut->id,
                    'quantity' => 1,
                    'delivery_date' => $futureDate,
                    'shift' => 'Lunch Shift',
                ],
            ],
        ];

        $failedResponse = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/orders', $payloadWithoutAck, ['Idempotency-Key' => 'idem-' . uniqid()]);

        $failedResponse->assertStatus(422)
            ->assertJsonValidationErrors(['acknowledge_allergen_warning']);

        // 2. Attempt order WITH explicit allergen warning acknowledgment -> must succeed
        $payloadWithAck = array_merge($payloadWithoutAck, [
            'acknowledge_allergen_warning' => true,
        ]);

        $successResponse = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/orders', $payloadWithAck, ['Idempotency-Key' => 'idem-' . uniqid()]);

        $successResponse->assertStatus(201)
            ->assertJsonPath('success', true);
    }

    public function test_unauthorized_guardian_is_blocked_from_ordering(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $futureDate = Carbon::now($tz)->addDays(2)->toDateString();

        $payload = [
            'student_id' => $this->student->id,
            'restaurant_id' => $this->restaurant->id,
            'school_location_id' => $this->location->id,
            'items' => [
                [
                    'menu_version_id' => $this->menuVersionChicken->id,
                    'quantity' => 1,
                    'delivery_date' => $futureDate,
                ],
            ],
        ];

        $response = $this->actingAs($this->unauthorizedParent)
            ->postJson('/api/v1/orders', $payload, ['Idempotency-Key' => 'idem-' . uniqid()]);

        $response->assertStatus(403);
    }

    public function test_guardian_can_skip_or_cancel_schedule_before_cutoff(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $futureDate = Carbon::now($tz)->addDays(5)->toDateString();

        // Place order first
        $order = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/orders', [
                'student_id' => $this->student->id,
                'restaurant_id' => $this->restaurant->id,
                'school_location_id' => $this->location->id,
                'items' => [
                    [
                        'menu_version_id' => $this->menuVersionChicken->id,
                        'quantity' => 2,
                        'delivery_date' => $futureDate,
                        'shift' => 'Lunch Shift',
                    ],
                ],
            ], ['Idempotency-Key' => 'idem-' . uniqid()])
            ->json('data');

        $scheduleId1 = $order['schedules'][0]['id'];
        $scheduleId2 = $order['schedules'][1]['id'];

        // Skip schedule 1
        $skipResponse = $this->actingAs($this->parentUser)
            ->postJson("/api/v1/orders/schedules/{$scheduleId1}/skip", [
                'reason' => 'Student has doctor appointment',
            ]);

        $skipResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'skipped');

        $this->assertDatabaseHas('order_schedules', [
            'id' => $scheduleId1,
            'status' => 'skipped',
        ]);

        // Cancel schedule 2
        $cancelResponse = $this->actingAs($this->parentUser)
            ->postJson("/api/v1/orders/schedules/{$scheduleId2}/cancel", [
                'reason' => 'Family traveling',
            ]);

        $cancelResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('order_schedules', [
            'id' => $scheduleId2,
            'status' => 'cancelled',
        ]);
    }

    public function test_guardian_cannot_skip_schedule_after_cutoff(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $futureDate = Carbon::now($tz)->addDays(2)->toDateString();

        // Place order
        $order = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/orders', [
                'student_id' => $this->student->id,
                'restaurant_id' => $this->restaurant->id,
                'school_location_id' => $this->location->id,
                'items' => [
                    [
                        'menu_version_id' => $this->menuVersionChicken->id,
                        'quantity' => 1,
                        'delivery_date' => $futureDate,
                        'shift' => 'Lunch Shift',
                    ],
                ],
            ], ['Idempotency-Key' => 'idem-' . uniqid()])
            ->json('data');

        $schedule = OrderSchedule::findOrFail($order['schedules'][0]['id']);

        // Force cutoff_at into past to simulate cutoff expiry
        $schedule->update([
            'cutoff_at' => Carbon::now($tz)->subMinutes(10),
        ]);

        $response = $this->actingAs($this->parentUser)
            ->postJson("/api/v1/orders/schedules/{$schedule->id}/skip");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['schedule']);
    }

    public function test_school_admin_can_view_daily_meal_roster(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $deliveryDate = Carbon::now($tz)->addDays(3)->toDateString();

        // Place order for student
        $this->actingAs($this->parentUser)
            ->postJson('/api/v1/orders', [
                'student_id' => $this->student->id,
                'restaurant_id' => $this->restaurant->id,
                'school_location_id' => $this->location->id,
                'items' => [
                    [
                        'menu_version_id' => $this->menuVersionChicken->id,
                        'quantity' => 1,
                        'delivery_date' => $deliveryDate,
                        'shift' => 'Lunch Shift',
                    ],
                ],
            ], ['Idempotency-Key' => 'idem-' . uniqid()])
            ->assertStatus(201);

        // School Admin accesses roster
        $response = $this->actingAs($this->schoolAdmin)
            ->getJson("/api/v1/schools/{$this->school->id}/meal-roster?delivery_date={$deliveryDate}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.school.id', $this->school->id)
            ->assertJsonPath('data.delivery_date', $deliveryDate)
            ->assertJsonPath('data.total_meals', 1);

        $rosterGroup = $response->json('data.roster.0');
        $this->assertEquals('Grade 4', $rosterGroup['grade_level']);
        $this->assertEquals('4-A', $rosterGroup['class_name']);
        $this->assertEquals('Lunch Shift', $rosterGroup['shift']);
        $this->assertEquals($this->location->id, $rosterGroup['school_location_id']);
        $this->assertEquals('Steamed Chicken Rice', $rosterGroup['roster_items'][0]['meal_name']);
        $this->assertEquals($this->student->student_code, $rosterGroup['roster_items'][0]['student_code']);

        // Other school admin should be rejected (403)
        $unauthResponse = $this->actingAs($this->otherSchoolAdmin)
            ->getJson("/api/v1/schools/{$this->school->id}/meal-roster?delivery_date={$deliveryDate}");

        $unauthResponse->assertStatus(403);
    }

    public function test_roster_excludes_cancelled_meals_by_default(): void
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $deliveryDate = Carbon::now($tz)->addDays(4)->toDateString();

        $order = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/orders', [
                'student_id' => $this->student->id,
                'restaurant_id' => $this->restaurant->id,
                'school_location_id' => $this->location->id,
                'items' => [
                    [
                        'menu_version_id' => $this->menuVersionChicken->id,
                        'quantity' => 1,
                        'delivery_date' => $deliveryDate,
                        'shift' => 'Lunch Shift',
                    ],
                ],
            ], ['Idempotency-Key' => 'idem-' . uniqid()])
            ->json('data');

        $scheduleId = $order['schedules'][0]['id'];

        // Cancel the schedule
        $this->actingAs($this->parentUser)
            ->postJson("/api/v1/orders/schedules/{$scheduleId}/cancel")
            ->assertStatus(200);

        // Check meal roster
        $response = $this->actingAs($this->schoolAdmin)
            ->getJson("/api/v1/schools/{$this->school->id}/meal-roster?delivery_date={$deliveryDate}");

        $response->assertStatus(200)
            ->assertJsonPath('data.total_meals', 0);
    }
}
