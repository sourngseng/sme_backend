<?php

namespace Database\Seeders;

use App\Models\Allergen;
use App\Models\CommissionRule;
use App\Models\Driver;
use App\Models\Guardian;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuVersion;
use App\Models\NutritionFact;
use App\Models\Promotion;
use App\Models\Restaurant;
use App\Models\RestaurantLocation;
use App\Models\RestaurantStaff;
use App\Models\School;
use App\Models\SchoolLocation;
use App\Models\SchoolMealPolicy;
use App\Models\SchoolShift;
use App\Models\Student;
use App\Models\StudentNutritionProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformDemoSeeder extends Seeder
{
    /**
     * Run the database seeds for platform master and demonstration records.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedAllergens();
            $this->seedSchoolAndStudents();
            $this->seedRestaurantAndMenu();
            $this->seedDriver();
            $this->seedPromotions();
        });
    }

    protected function seedAllergens(): void
    {
        $allergens = [
            ['code' => 'PEANUTS', 'name' => 'Peanuts', 'description' => 'Peanuts and products thereof.'],
            ['code' => 'TREE_NUTS', 'name' => 'Tree Nuts', 'description' => 'Almonds, hazelnuts, walnuts, cashews, pecans, Brazil nuts, pistachios, macadamia nuts.'],
            ['code' => 'MILK', 'name' => 'Milk / Dairy', 'description' => 'Milk and dairy products including lactose.'],
            ['code' => 'EGGS', 'name' => 'Eggs', 'description' => 'Eggs and egg-derived ingredients.'],
            ['code' => 'WHEAT_GLUTEN', 'name' => 'Wheat & Gluten', 'description' => 'Wheat, rye, barley, oats, spelt, kamut.'],
            ['code' => 'SOYBEANS', 'name' => 'Soybeans', 'description' => 'Soybeans and soy products (tofu, soy sauce).'],
            ['code' => 'FISH', 'name' => 'Fish', 'description' => 'All freshwater and saltwater fish species.'],
            ['code' => 'SHELLFISH', 'name' => 'Crustacean Shellfish', 'description' => 'Crabs, lobsters, prawns, shrimp.'],
            ['code' => 'SESAME', 'name' => 'Sesame Seeds', 'description' => 'Sesame seeds and sesame oil.'],
            ['code' => 'MOLLUSCS', 'name' => 'Molluscs', 'description' => 'Mussels, oysters, squid, octopus, snails.'],
            ['code' => 'MUSTARD', 'name' => 'Mustard', 'description' => 'Mustard seeds and mustard condiment.'],
            ['code' => 'CELERY', 'name' => 'Celery', 'description' => 'Celery stalks, leaves, and celeriac.'],
        ];

        foreach ($allergens as $item) {
            Allergen::firstOrCreate(['code' => $item['code']], $item);
        }
    }

    protected function seedSchoolAndStudents(): void
    {
        // 1. Primary School
        $school = School::firstOrCreate(
            ['code' => 'PPA-001'],
            [
                'name' => 'Phnom Penh International Academy',
                'level' => 'primary',
                'status' => 'active',
                'phone' => '+85523888001',
                'email' => 'info@pp-academy.edu.kh',
                'address' => 'No. 128 Norodom Blvd, Boeung Keng Kang, Phnom Penh',
                'latitude' => 11.5564000,
                'longitude' => 104.9282000,
            ]
        );

        $schoolAdmin = User::where('email', 'school@schoolmeal.test')->first();
        if ($schoolAdmin) {
            $schoolAdmin->update(['school_id' => $school->id]);
        }

        // Shifts
        $morningShift = SchoolShift::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Morning Shift'],
            ['start_time' => '07:30:00', 'end_time' => '11:30:00', 'status' => 'active']
        );

        $lunchShift = SchoolShift::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Lunch & Afternoon Shift'],
            ['start_time' => '11:30:00', 'end_time' => '16:30:00', 'status' => 'active']
        );

        // Delivery Locations
        $gateLocation = SchoolLocation::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Main Gate Delivery Station'],
            [
                'address' => 'Guardhouse Station 1, Gate A',
                'latitude' => 11.5564500,
                'longitude' => 104.9282500,
                'instructions' => 'Drop at insulated delivery racks in front of Guardhouse A.',
                'status' => 'active',
            ]
        );

        $canteenLocation = SchoolLocation::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Primary Canteen Counter 3'],
            [
                'address' => 'Building B, Ground Floor Canteen Counter 3',
                'latitude' => 11.5563800,
                'longitude' => 104.9281800,
                'instructions' => 'Pass security badge and hand over directly to canteen supervisor.',
                'status' => 'active',
            ]
        );

        // Meal Policy
        SchoolMealPolicy::firstOrCreate(
            ['school_id' => $school->id],
            [
                'default_cutoff_minutes' => 120, // 2 hours before shift start
                'allowed_meal_types' => ['breakfast', 'lunch', 'snack'],
                'allow_cod' => false,
            ]
        );

        // 2. Guardian (Linked to parent user)
        $parentUser = User::where('email', 'parent@schoolmeal.test')->first();
        if ($parentUser) {
            $guardian = Guardian::firstOrCreate(
                ['user_id' => $parentUser->id],
                ['relationship' => 'Mother']
            );

            // 3. Students
            $student = Student::firstOrCreate(
                ['student_code' => 'STU-2026-001'],
                [
                    'school_id' => $school->id,
                    'school_shift_id' => $lunchShift->id,
                    'full_name' => 'Dara Sok',
                    'date_of_birth' => '2016-05-15',
                    'gender' => 'male',
                    'grade_level' => 'Grade 4',
                    'class_name' => '4-A',
                    'status' => 'active',
                ]
            );

            // Link Guardian to Student
            $guardian->students()->syncWithoutDetaching([
                $student->id => [
                    'is_primary' => true,
                    'can_order' => true,
                    'can_view_nutrition' => true,
                    'created_at' => now(),
                ],
            ]);

            // Nutrition Profile (peanut allergy)
            $peanutAllergen = Allergen::where('code', 'PEANUTS')->first();
            StudentNutritionProfile::firstOrCreate(
                ['student_id' => $student->id],
                [
                    'dietary_preferences' => ['Halal friendly', 'No spicy food'],
                    'allergen_ids' => $peanutAllergen ? [$peanutAllergen->id] : [],
                    'notes' => 'Severe allergic reaction to peanuts. Please ensure kitchen separation.',
                ]
            );
        }
    }

    protected function seedRestaurantAndMenu(): void
    {
        $merchantUser = User::where('email', 'merchant@schoolmeal.test')->first();
        if (! $merchantUser) {
            return;
        }

        // Restaurant
        $restaurant = Restaurant::firstOrCreate(
            ['code' => 'REST-001'],
            [
                'name' => 'Green Garden Organic Kitchen',
                'owner_user_id' => $merchantUser->id,
                'phone' => '+85512999001',
                'email' => 'kitchen@greengarden.test',
                'status' => 'active',
                'commission_default_pct' => 12.50, // 12.5% platform commission
                'address' => 'No. 45 Street 310, Chamkarmon, Phnom Penh',
                'latitude' => 11.5451000,
                'longitude' => 104.9185000,
            ]
        );

        // Staff
        RestaurantStaff::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'user_id' => $merchantUser->id],
            ['job_title' => 'Executive Chef & Owner', 'status' => 'active']
        );

        $kitchenUser = User::where('email', 'kitchen@schoolmeal.test')->first();
        if ($kitchenUser) {
            RestaurantStaff::firstOrCreate(
                ['restaurant_id' => $restaurant->id, 'user_id' => $kitchenUser->id],
                ['job_title' => 'Head Prep & Line Chef', 'status' => 'active']
            );
        }

        // Location
        RestaurantLocation::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'name' => 'Central Kitchen Chamkarmon'],
            [
                'address' => 'No. 45 Street 310, Chamkarmon, Phnom Penh',
                'latitude' => 11.5451000,
                'longitude' => 104.9185000,
                'instructions' => 'Dispatch ramp located behind building in back alley.',
                'status' => 'active',
            ]
        );

        // Categories
        $healthyLunches = MenuCategory::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'name' => 'Nutritious Lunches'],
            ['description' => 'Balanced macro meals crafted for student brain focus and stamina.', 'sort_order' => 1, 'status' => 'active']
        );

        $fruitSnacks = MenuCategory::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'name' => 'Fresh Fruit & Snacks'],
            ['description' => 'Cold-pressed juices, seasonal tropical fruits and light bites.', 'sort_order' => 2, 'status' => 'active']
        );

        // Menu Item 1: Grilled Chicken Rice Bowl
        $chickenBowl = MenuItem::firstOrCreate(
            ['sku' => 'GG-LUNCH-01'],
            [
                'restaurant_id' => $restaurant->id,
                'category_id' => $healthyLunches->id,
                'name' => 'Herb-Grilled Chicken Breast with Brown Rice & Steamed Veggies',
                'description' => 'Tender local farm-raised chicken breast served with organic brown rice, sweet carrots, broccoli and light sesame soy glaze.',
                'meal_type' => 'lunch',
                'status' => 'active',
            ]
        );

        // Version 1 of Chicken Bowl
        $chickenVersion = MenuVersion::firstOrCreate(
            ['menu_item_id' => $chickenBowl->id, 'version_no' => 1],
            [
                'price_minor' => 350, // $3.50
                'currency' => 'USD',
                'serving_size_g' => 380.00,
                'available_from' => Carbon::now()->subMonths(1),
                'available_to' => Carbon::now()->addMonths(12),
                'cutoff_minutes' => 90,
                'max_daily_quantity' => 150,
                'status' => 'active',
            ]
        );

        // Nutrition Fact for Chicken Bowl
        NutritionFact::firstOrCreate(
            ['menu_version_id' => $chickenVersion->id],
            [
                'calories_kcal' => 520.00,
                'protein_g' => 38.50,
                'carbohydrates_g' => 62.00,
                'fat_g' => 11.20,
                'fiber_g' => 7.50,
                'sugar_g' => 4.20,
                'sodium_mg' => 480.00,
                'nutrition_source' => 'Pasteur Institute Cambodia Lab Verified',
                'verified_at' => Carbon::now()->subMonths(1),
            ]
        );

        // Allergen link: Sesame & Soy
        $sesame = Allergen::where('code', 'SESAME')->first();
        $soy = Allergen::where('code', 'SOYBEANS')->first();
        if ($sesame) {
            $chickenVersion->allergens()->syncWithoutDetaching([$sesame->id => ['severity' => 'mild', 'notes' => 'Garnish contains toasted sesame seeds.']]);
        }
        if ($soy) {
            $chickenVersion->allergens()->syncWithoutDetaching([$soy->id => ['severity' => 'moderate', 'notes' => 'Light soy marinade.']]);
        }

        // Commission Rule
        CommissionRule::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'menu_version_id' => null],
            [
                'commission_pct' => 12.50,
                'effective_from' => '2026-01-01',
                'status' => 'active',
            ]
        );
    }

    protected function seedDriver(): void
    {
        $driverUser = User::where('email', 'driver@schoolmeal.test')->first();
        if ($driverUser) {
            Driver::firstOrCreate(
                ['user_id' => $driverUser->id],
                [
                    'driver_code' => 'DRV-PP-001',
                    'phone' => '+85512000005',
                    'vehicle_type' => 'Motorcycle Insulated Box',
                    'vehicle_plate' => 'Phnom Penh 1AK-8899',
                    'license_no' => 'DL-KH-992819',
                    'status' => 'active',
                    'self_delivery' => false,
                ]
            );
        }
    }

    protected function seedPromotions(): void
    {
        $adminUser = User::where('email', 'admin@schoolmeal.test')->first();
        if (! $adminUser) {
            return;
        }

        Promotion::firstOrCreate(
            ['code' => 'BACK2SCHOOL'],
            [
                'type' => 'percentage',
                'value' => 15.00, // 15% discount
                'max_discount_minor' => 500, // $5.00 max discount
                'usage_limit' => 500,
                'used_count' => 0,
                'status' => 'active',
            ]
        );

        Promotion::firstOrCreate(
            ['code' => 'FIRSTMEAL'],
            [
                'type' => 'fixed',
                'value' => 2.00, // $2 off
                'max_discount_minor' => 200,
                'usage_limit' => 1000,
                'used_count' => 0,
                'status' => 'active',
            ]
        );
    }
}
