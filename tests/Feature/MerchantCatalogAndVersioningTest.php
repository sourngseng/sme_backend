<?php

namespace Tests\Feature;

use App\Models\Allergen;
use App\Models\Guardian;
use App\Models\MenuItem;
use App\Models\MenuVersion;
use App\Models\Permission;
use App\Models\Restaurant;
use App\Models\RestaurantStaff;
use App\Models\Role;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentNutritionProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MerchantCatalogAndVersioningTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $merchantOwner;
    protected User $merchantStaff;
    protected User $parentUser;
    protected Restaurant $restaurant;
    protected Allergen $peanutAllergen;
    protected Allergen $dairyAllergen;
    protected Student $studentWithPeanutAllergy;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles & Permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['guard_name' => 'web']);
        $ownerRole = Role::firstOrCreate(['name' => 'restaurant_owner'], ['guard_name' => 'web']);
        $staffRole = Role::firstOrCreate(['name' => 'restaurant_staff'], ['guard_name' => 'web']);
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['guard_name' => 'web']);

        $restaurantPerms = [
            'restaurants.view', 'restaurants.create', 'restaurants.update', 'restaurants.delete',
            'restaurants.approve', 'restaurants.suspend', 'restaurants.staff.manage',
            'menu_items.create', 'menu_items.update', 'menu_items.delete',
        ];

        foreach ($restaurantPerms as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName]);
            $ownerRole->permissions()->syncWithoutDetaching([$perm->id]);
            $superAdminRole->permissions()->syncWithoutDetaching([$perm->id]);
        }

        // 2. Users
        $this->superAdmin = User::create([
            'full_name' => 'Platform Super Admin',
            'email' => 'admin.catalog@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->superAdmin->assignRole('super_admin');

        $this->merchantOwner = User::create([
            'full_name' => 'Chef Vannak',
            'email' => 'vannak.chef@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->merchantOwner->assignRole('restaurant_owner');

        $this->merchantStaff = User::create([
            'full_name' => 'Staff Dara',
            'email' => 'dara.staff@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->merchantStaff->assignRole('restaurant_staff');

        $this->parentUser = User::create([
            'full_name' => 'Parent Rathana',
            'email' => 'rathana.parent@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->parentUser->assignRole('parent');
        $guardian = Guardian::firstOrCreate(['user_id' => $this->parentUser->id]);

        // 3. Allergens
        $this->peanutAllergen = Allergen::firstOrCreate(
            ['code' => 'PEANUTS'],
            ['name' => 'Peanuts', 'description' => 'Peanut derivatives']
        );

        $this->dairyAllergen = Allergen::firstOrCreate(
            ['code' => 'MILK'],
            ['name' => 'Milk / Dairy', 'description' => 'Lactose and dairy proteins']
        );

        // 4. Restaurant
        $this->restaurant = Restaurant::create([
            'code' => 'REST-KHMER-01',
            'name' => 'Healthy Khmer Meals',
            'owner_user_id' => $this->merchantOwner->id,
            'status' => 'active',
            'commission_default_pct' => 15.00,
        ]);

        // 5. School & Student with declared peanut allergy
        $school = School::create([
            'code' => 'SCH-TEST-CAT',
            'name' => 'Catalog Test Academy',
            'level' => 'primary',
            'status' => 'active',
        ]);

        $this->studentWithPeanutAllergy = Student::create([
            'student_code' => 'STU-CAT-001',
            'school_id' => $school->id,
            'full_name' => 'Kanha Sok',
            'status' => 'active',
        ]);

        $guardian->students()->syncWithoutDetaching([
            $this->studentWithPeanutAllergy->id => [
                'is_primary' => true,
                'can_order' => true,
                'can_view_nutrition' => true,
            ],
        ]);

        StudentNutritionProfile::create([
            'student_id' => $this->studentWithPeanutAllergy->id,
            'allergen_ids' => [$this->peanutAllergen->id],
            'dietary_preferences' => ['Halal friendly'],
            'notes' => 'Severe reaction to peanuts',
        ]);
    }

    public function test_admin_can_approve_and_suspend_restaurant(): void
    {
        // 1. Suspend restaurant
        $suspendRes = $this->actingAs($this->superAdmin, 'sanctum')
            ->patchJson("/api/v1/admin/restaurants/{$this->restaurant->id}/status", [
                'status' => 'suspended',
                'reason' => 'Quality compliance review',
            ]);

        $suspendRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', 'suspended');

        $this->assertDatabaseHas('restaurants', [
            'id' => $this->restaurant->id,
            'status' => 'suspended',
        ]);

        // 2. Reactivate restaurant
        $activateRes = $this->actingAs($this->superAdmin, 'sanctum')
            ->patchJson("/api/v1/admin/restaurants/{$this->restaurant->id}/status", [
                'status' => 'active',
            ]);

        $activateRes->assertStatus(200)
            ->assertJsonPath('data.status', 'active');
    }

    public function test_restaurant_owner_can_manage_staff(): void
    {
        // Owner adds kitchen staff
        $addStaffRes = $this->actingAs($this->merchantOwner, 'sanctum')
            ->postJson('/api/v1/restaurant/staff', [
                'restaurant_id' => $this->restaurant->id,
                'user_id' => $this->merchantStaff->id,
                'job_title' => 'kitchen_staff',
            ]);

        $addStaffRes->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.job_title', 'kitchen_staff');

        $staffId = $addStaffRes->json('data.id');

        // Owner lists staff
        $listRes = $this->actingAs($this->merchantOwner, 'sanctum')
            ->getJson("/api/v1/restaurant/staff?restaurant_id={$this->restaurant->id}");

        $listRes->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Owner deletes staff
        $deleteRes = $this->actingAs($this->merchantOwner, 'sanctum')
            ->deleteJson("/api/v1/restaurant/staff/{$staffId}");

        $deleteRes->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('restaurant_staff', ['id' => $staffId]);
    }

    public function test_immutable_menu_versioning_creates_new_version_on_change(): void
    {
        // 1. Create Menu Item with Version 1
        $createRes = $this->actingAs($this->merchantOwner, 'sanctum')
            ->postJson('/api/v1/restaurant/menu-items', [
                'restaurant_id' => $this->restaurant->id,
                'name' => 'Chicken Amok Rice Bowl',
                'sku' => 'AMOK-001',
                'meal_type' => 'lunch',
                'price_minor' => 350, // $3.50
                'currency' => 'USD',
                'serving_size_g' => 350.00,
                'cutoff_minutes' => 60,
                'nutrition' => [
                    'calories_kcal' => 520,
                    'protein_g' => 32.5,
                    'carbohydrates_g' => 65.0,
                    'fat_g' => 14.0,
                ],
                'allergens' => [$this->dairyAllergen->id],
            ]);

        $createRes->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.current_version.version_no', 1)
            ->assertJsonPath('data.current_version.price_minor', 350);

        $itemId = $createRes->json('data.id');
        $v1Id = $createRes->json('data.current_version.id');

        // 2. Update price and add peanut allergen -> MUST trigger MenuVersioningService to produce v2
        $updateRes = $this->actingAs($this->merchantOwner, 'sanctum')
            ->putJson("/api/v1/restaurant/menu-items/{$itemId}", [
                'price_minor' => 400, // $4.00
                'allergens' => [$this->dairyAllergen->id, $this->peanutAllergen->id],
            ]);

        $updateRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.current_version.version_no', 2)
            ->assertJsonPath('data.current_version.price_minor', 400);

        $v2Id = $updateRes->json('data.current_version.id');
        $this->assertNotEquals($v1Id, $v2Id);

        // 3. Verify that Version 1 is marked as retired and its price/nutrition remain intact
        $v1Record = MenuVersion::find($v1Id);
        $this->assertEquals('retired', $v1Record->status);
        $this->assertEquals(350, $v1Record->price_minor);

        // 4. Verify Version 2 is active
        $v2Record = MenuVersion::find($v2Id);
        $this->assertEquals('active', $v2Record->status);
        $this->assertEquals(400, $v2Record->price_minor);
        $this->assertEquals(2, $v2Record->allergens()->count());
    }

    public function test_parent_catalog_browsing_with_live_allergen_conflict_detection(): void
    {
        // 1. Create a meal with peanuts
        $peanutItem = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Peanut Chicken Salad',
            'meal_type' => 'lunch',
            'status' => 'active',
        ]);
        $peanutVersion = MenuVersion::create([
            'menu_item_id' => $peanutItem->id,
            'version_no' => 1,
            'price_minor' => 300,
            'currency' => 'USD',
            'status' => 'active',
        ]);
        $peanutVersion->allergens()->sync([$this->peanutAllergen->id => ['severity' => 'severe']]);

        // 2. Create a safe meal without peanuts
        $safeItem = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Steamed Fish & Green Vegetables',
            'meal_type' => 'lunch',
            'status' => 'active',
        ]);
        MenuVersion::create([
            'menu_item_id' => $safeItem->id,
            'version_no' => 1,
            'price_minor' => 450,
            'currency' => 'USD',
            'status' => 'active',
        ]);

        // 3. Query catalog with student_id (who has peanut allergy)
        $catalogRes = $this->getJson("/api/v1/catalog/meals?student_id={$this->studentWithPeanutAllergy->id}");

        $catalogRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('meta.student_allergen_screening_applied', true);

        $meals = collect($catalogRes->json('data'));

        // The peanut meal must be flagged with allergen_conflict: true
        $peanutMeal = $meals->firstWhere('name', 'Peanut Chicken Salad');
        $this->assertNotNull($peanutMeal);
        $this->assertTrue($peanutMeal['allergen_conflict']);
        $this->assertCount(1, $peanutMeal['conflicting_allergens']);
        $this->assertEquals('PEANUTS', $peanutMeal['conflicting_allergens'][0]['code']);

        // The safe meal must have allergen_conflict: false
        $safeMeal = $meals->firstWhere('name', 'Steamed Fish & Green Vegetables');
        $this->assertNotNull($safeMeal);
        $this->assertFalse($safeMeal['allergen_conflict']);
        $this->assertEmpty($safeMeal['conflicting_allergens']);
    }

    public function test_suspended_restaurant_meals_are_hidden_from_parent_browsing(): void
    {
        MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Mystery Noodle Soup',
            'meal_type' => 'lunch',
            'status' => 'active',
        ])->versions()->create([
            'version_no' => 1,
            'price_minor' => 200,
            'currency' => 'USD',
            'status' => 'active',
        ]);

        // Verify visible when active
        $beforeSuspend = $this->getJson('/api/v1/catalog/meals');
        $this->assertTrue(collect($beforeSuspend->json('data'))->pluck('name')->contains('Mystery Noodle Soup'));

        // Suspend restaurant
        $this->restaurant->update(['status' => 'suspended']);

        // Verify immediately hidden from catalog
        $afterSuspend = $this->getJson('/api/v1/catalog/meals');
        $this->assertFalse(collect($afterSuspend->json('data'))->pluck('name')->contains('Mystery Noodle Soup'));
    }
}
