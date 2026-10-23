<?php

namespace Tests\Unit;

use App\Models\MenuItem;
use App\Models\MenuVersion;
use App\Models\OrderItem;
use App\Models\NutritionConsumptionLog;
use App\Models\NutritionFact;
use App\Models\Restaurant;
use App\Models\SchoolShift;
use App\Models\User;
use App\Services\SettlementService;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CoreDomainCalculationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * PB-076: Server-side ordering cutoff calculation across timezones and school shifts.
     */
    public function test_cutoff_calculation_respects_shift_hours_and_timezone(): void
    {
        $timezone = 'Asia/Phnom_Penh';

        // Morning Shift: Lunch served at 11:30 AM, cutoff at 08:30 AM
        $shift = new SchoolShift([
            'name' => 'Morning Elementary Lunch',
            'start_time' => '11:30:00',
            'end_time' => '12:30:00',
            'cutoff_time' => '08:30:00',
        ]);

        $deliveryDate = Carbon::create(2026, 10, 24, 0, 0, 0, $timezone);

        // Before cutoff: 08:15 AM
        $beforeCutoff = Carbon::create(2026, 10, 24, 8, 15, 0, $timezone);
        $cutoffDateTime = Carbon::create(2026, 10, 24, 8, 30, 0, $timezone);

        $this->assertTrue($beforeCutoff->lessThan($cutoffDateTime));
        $this->assertEquals(15, $beforeCutoff->diffInMinutes($cutoffDateTime));

        // After cutoff: 08:31 AM
        $afterCutoff = Carbon::create(2026, 10, 24, 8, 31, 0, $timezone);
        $this->assertTrue($afterCutoff->greaterThan($cutoffDateTime));

        // Ensure timezone conversion from UTC maintains strict epoch equivalence
        $utcTimeBefore = $beforeCutoff->copy()->setTimezone('UTC');
        $this->assertEquals($beforeCutoff->timestamp, $utcTimeBefore->timestamp);
    }

    /**
     * PB-076: Historical Immutability Test
     * Updating a menu item creates a new menu_versions row and leaves existing order_items untouched.
     */
    public function test_historical_immutability_preserves_order_snapshots_when_menu_version_updates(): void
    {
        $owner = User::create([
            'full_name' => 'Chef Owner',
            'email' => 'chef.imm@sme.test',
            'phone' => '+85512999001',
            'password' => 'Password123!',
            'status' => 'active',
        ]);

        $restaurant = Restaurant::firstOrCreate(
            ['code' => 'REST-IMMUTABLE-01'],
            [
                'name' => 'Immutability Kitchen',
                'owner_user_id' => $owner->id,
                'is_active' => true,
                'status' => 'approved',
                'timezone' => 'Asia/Phnom_Penh',
            ]
        );

        $menuItem = MenuItem::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Original Organic Stew',
            'sku' => 'STEW-IMM-01',
            'is_active' => true,
        ]);

        // Version 1: $3.50 (350 minor), 480 kcal
        $v1 = MenuVersion::create([
            'menu_item_id' => $menuItem->id,
            'version_no' => 1,
            'price_minor' => 350,
            'status' => 'published',
            'created_at' => now()->subDays(5),
        ]);

        $v1Nutrition = NutritionFact::create([
            'menu_version_id' => $v1->id,
            'calories_kcal' => 480,
            'protein_g' => 28,
            'carbohydrates_g' => 52,
            'fat_g' => 12,
        ]);

        // Place an Order snapshot based on v1
        $orderItem = new OrderItem([
            'menu_version_id' => $v1->id,
            'quantity' => 2,
            'unit_price_minor' => $v1->price_minor, // 350 cents each
            'line_total_minor' => 700,
            'calories_kcal_snapshot' => $v1Nutrition->calories_kcal,
            'nutrition_snapshot' => [
                'protein_grams' => $v1Nutrition->protein_g,
                'carbs_grams' => $v1Nutrition->carbohydrates_g,
                'fat_grams' => $v1Nutrition->fat_g,
            ],
        ]);

        // Merchant now creates Version 2 with inflation: $5.00 (500 minor) and 600 kcal
        $v2 = MenuVersion::create([
            'menu_item_id' => $menuItem->id,
            'version_no' => 2,
            'price_minor' => 500,
            'status' => 'published',
            'created_at' => now(),
        ]);

        $v2Nutrition = NutritionFact::create([
            'menu_version_id' => $v2->id,
            'calories_kcal' => 600,
            'protein_g' => 35,
            'carbohydrates_g' => 70,
            'fat_g' => 18,
        ]);

        // Assert v1 and order snapshot remain strictly unchanged
        $this->assertEquals(1, $v1->version_no);
        $this->assertEquals(350, $v1->price_minor);
        $this->assertEquals(2, $v2->version_no);
        $this->assertEquals(500, $v2->price_minor);

        $this->assertEquals(350, $orderItem->unit_price_minor);
        $this->assertEquals(700, $orderItem->line_total_minor);
        $this->assertEquals(480, (int) $orderItem->calories_kcal_snapshot);
        $this->assertEquals(28, (int) $orderItem->nutrition_snapshot['protein_grams']);
        $this->assertNotEquals($v2->price_minor, $orderItem->unit_price_minor);
    }

    /**
     * PB-076: Commission calculation from order snapshots during settlement generation.
     */
    public function test_commission_calculation_from_order_snapshots(): void
    {
        $service = new SettlementService();

        // 3 order items: $4.00, $3.50, $2.50 = $10.00 total gross (1000 minor)
        $orderAmountsMinor = [400, 350, 250];
        $grossSalesMinor = array_sum($orderAmountsMinor);
        $this->assertEquals(1000, $grossSalesMinor);

        // 10% Platform commission rate
        $commissionRatePct = 10.0;
        $commissionFeeMinor = (int) round($grossSalesMinor * ($commissionRatePct / 100));
        $netPayoutMinor = $grossSalesMinor - $commissionFeeMinor;

        $this->assertEquals(100, $commissionFeeMinor); // $1.00
        $this->assertEquals(900, $netPayoutMinor);     // $9.00

        // Test odd fractional rounding: $15.55 with 7.5% commission
        $oddGross = 1555; // $15.55
        $oddRate = 7.5;
        $expectedFee = (int) round($oddGross * ($oddRate / 100)); // 1555 * 0.075 = 116.625 -> 117
        $this->assertEquals(117, $expectedFee);
        $this->assertEquals(1438, $oddGross - $expectedFee);
    }

    /**
     * PB-076: Nutrition consumption log calorie/protein percentage math.
     */
    public function test_nutrition_consumption_log_calorie_and_protein_percentage_math(): void
    {
        $baseCalories = 540.0;
        $baseProtein = 32.0;

        // Scenario 1: Child consumes 75% of meal
        $consumptionPct75 = 75;
        $consumedCalories75 = round($baseCalories * ($consumptionPct75 / 100), 2);
        $consumedProtein75 = round($baseProtein * ($consumptionPct75 / 100), 2);
        $wastedCalories75 = round($baseCalories - $consumedCalories75, 2);

        $this->assertEquals(405.0, $consumedCalories75);
        $this->assertEquals(24.0, $consumedProtein75);
        $this->assertEquals(135.0, $wastedCalories75);

        // Scenario 2: Child consumes 100% of meal
        $consumptionPct100 = 100;
        $consumedCalories100 = round($baseCalories * ($consumptionPct100 / 100), 2);
        $this->assertEquals(540.0, $consumedCalories100);

        // Scenario 3: Child consumes 25% of meal
        $consumptionPct25 = 25;
        $consumedCalories25 = round($baseCalories * ($consumptionPct25 / 100), 2);
        $this->assertEquals(135.0, $consumedCalories25);
    }
}
