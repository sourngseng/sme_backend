<?php

namespace Tests\Feature;

use App\Models\Allergen;
use App\Models\DeliveryBatch;
use App\Models\Guardian;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuVersion;
use App\Models\NutritionConsumptionLog;
use App\Models\NutritionFact;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSchedule;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLocation;
use App\Models\SchoolShift;
use App\Models\Settlement;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceAndReportingOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $merchantOwner;
    protected User $parentUser;
    protected User $otherParent;
    protected Guardian $guardian;
    protected School $school;
    protected SchoolLocation $location;
    protected SchoolShift $shift;
    protected Student $student;
    protected Restaurant $restaurant;
    protected MenuItem $menuItem;
    protected MenuVersion $menuVersion;
    protected Order $order;
    protected OrderSchedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'super_admin'], ['guard_name' => 'web']);
        $ownerRole = Role::firstOrCreate(['name' => 'restaurant_owner'], ['guard_name' => 'web']);
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['guard_name' => 'web']);

        $this->superAdmin = User::create([
            'full_name' => 'Admin Controller',
            'email' => 'admin.fin@sme.test',
            'phone' => '+85512100001',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->superAdmin->roles()->attach($adminRole->id);

        $this->merchantOwner = User::create([
            'full_name' => 'Chef Merchant',
            'email' => 'merchant.fin@sme.test',
            'phone' => '+85512100002',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->merchantOwner->roles()->attach($ownerRole->id);

        $this->parentUser = User::create([
            'full_name' => 'Parent Fin User',
            'email' => 'parent.fin@sme.test',
            'phone' => '+85512100003',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->parentUser->roles()->attach($parentRole->id);

        $this->otherParent = User::create([
            'full_name' => 'Other Parent User',
            'email' => 'other.parent@sme.test',
            'phone' => '+85512100004',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->otherParent->roles()->attach($parentRole->id);

        $this->guardian = Guardian::create([
            'user_id' => $this->parentUser->id,
            'relationship' => 'father',
        ]);

        $this->school = School::create([
            'code' => 'SCH-REP-01',
            'name' => 'Reporting High School',
            'level' => 'secondary',
            'address' => 'Russian Blvd, Phnom Penh',
            'status' => 'active',
        ]);

        $this->shift = SchoolShift::create([
            'school_id' => $this->school->id,
            'name' => 'Lunch Shift',
            'start_time' => '11:30:00',
            'end_time' => '13:00:00',
        ]);

        $this->location = SchoolLocation::create([
            'school_id' => $this->school->id,
            'name' => 'Hall C Drop-off',
            'address' => 'Building C, 1st Floor',
        ]);

        $this->student = Student::create([
            'student_code' => 'STU-REP-01',
            'school_id' => $this->school->id,
            'school_shift_id' => $this->shift->id,
            'full_name' => 'Alexandre Student',
            'grade_level' => 'Grade 8',
            'status' => 'active',
        ]);
        $this->student->guardians()->attach($this->guardian->id, ['is_primary' => true]);

        $this->restaurant = Restaurant::create([
            'code' => 'REST-REP-01',
            'name' => 'Fresh Farm Meals',
            'owner_user_id' => $this->merchantOwner->id,
            'status' => 'active',
            'commission_default_pct' => 10.00,
        ]);

        $cat = MenuCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Nutritious Bowls',
        ]);

        $this->menuItem = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $cat->id,
            'name' => 'Grilled Salmon Rice Bowl',
            'sku' => 'SALMON-BOWL-01',
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
            'max_daily_quantity' => 50,
        ]);

        NutritionFact::create([
            'menu_version_id' => $this->menuVersion->id,
            'calories_kcal' => 600.00,
            'protein_g' => 35.00,
            'carbohydrates_g' => 65.00,
            'fat_g' => 14.00,
            'nutrition_source' => 'lab_tested',
            'verified_at' => now(),
        ]);

        $this->order = Order::create([
            'order_number' => 'ORD-REP-9001',
            'guardian_id' => $this->guardian->id,
            'student_id' => $this->student->id,
            'school_id' => $this->school->id,
            'restaurant_id' => $this->restaurant->id,
            'status' => 'confirmed',
            'subtotal_minor' => 450,
            'total_minor' => 450,
            'commission_minor' => 45, // snapshotted 10%
            'placed_at' => now(),
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $this->order->id,
            'menu_version_id' => $this->menuVersion->id,
            'item_name_snapshot' => $this->menuItem->name,
            'unit_price_minor' => 450,
            'quantity' => 1,
            'line_total_minor' => 450,
            'calories_kcal_snapshot' => 600.00,
            'nutrition_snapshot' => ['protein_g' => 35.00, 'carbs_g' => 65.00],
        ]);

        $this->schedule = OrderSchedule::create([
            'order_id' => $this->order->id,
            'order_item_id' => $orderItem->id,
            'delivery_date' => now()->toDateString(),
            'shift' => 'morning',
            'school_location_id' => $this->location->id,
            'status' => 'delivered',
            'cutoff_at' => now()->subHours(2),
            'delivered_at' => now()->subHour(),
        ]);
    }

    public function test_super_admin_can_generate_merchant_settlement_using_snapshotted_values(): void
    {
        $response = $this->actingAs($this->superAdmin, 'sanctum')->postJson('/api/v1/admin/settlements/generate', [
            'restaurant_id' => $this->restaurant->id,
            'period_start' => now()->subDays(2)->toDateString(),
            'period_end' => now()->addDays(2)->toDateString(),
            'adjustment_minor' => 50,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Settlement generated successfully.')
            ->assertJsonPath('settlement.restaurant_id', $this->restaurant->id)
            ->assertJsonPath('settlement.gross_sales_minor', 450)
            ->assertJsonPath('settlement.commission_minor', 45)
            ->assertJsonPath('settlement.net_payable_minor', 455); // (450 - 45 + 50) = 455

        $settlementId = $response->json('settlement.id');

        $this->assertDatabaseHas('settlements', [
            'id' => $settlementId,
            'status' => 'draft',
            'net_payable_minor' => 455,
        ]);

        $this->assertDatabaseHas('settlement_items', [
            'settlement_id' => $settlementId,
            'order_id' => $this->order->id,
            'gross_minor' => 450,
            'commission_minor' => 45,
            'net_minor' => 405,
        ]);

        // Transition status to approved, then paid
        $approveRes = $this->actingAs($this->superAdmin, 'sanctum')->patchJson("/api/v1/admin/settlements/{$settlementId}/status", [
            'status' => 'approved',
        ]);
        $approveRes->assertStatus(200)->assertJsonPath('settlement.status', 'approved');

        $payRes = $this->actingAs($this->superAdmin, 'sanctum')->patchJson("/api/v1/admin/settlements/{$settlementId}/status", [
            'status' => 'paid',
        ]);
        $payRes->assertStatus(200)->assertJsonPath('settlement.status', 'paid');
        $this->assertNotNull($payRes->json('settlement.paid_at'));
    }

    public function test_restaurant_owner_can_view_own_settlements(): void
    {
        // First generate a settlement
        $this->actingAs($this->superAdmin, 'sanctum')->postJson('/api/v1/admin/settlements/generate', [
            'restaurant_id' => $this->restaurant->id,
            'period_start' => now()->subDays(5)->toDateString(),
            'period_end' => now()->addDays(5)->toDateString(),
        ])->assertStatus(201);

        // Merchant owner views settlements
        $response = $this->actingAs($this->merchantOwner, 'sanctum')->getJson('/api/v1/restaurant/settlements');
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'current_page']);

        $settlements = $response->json('data');
        $this->assertCount(1, $settlements);
        $this->assertEquals($this->restaurant->id, $settlements[0]['restaurant_id']);

        $settlementId = $settlements[0]['id'];
        $showRes = $this->actingAs($this->merchantOwner, 'sanctum')->getJson("/api/v1/restaurant/settlements/{$settlementId}");
        $showRes->assertStatus(200)->assertJsonPath('settlement.id', $settlementId);
    }

    public function test_parent_can_submit_meal_feedback(): void
    {
        $response = $this->actingAs($this->parentUser, 'sanctum')->postJson("/api/v1/orders/schedules/{$this->schedule->id}/feedback", [
            'rating' => 5,
            'taste_rating' => 5,
            'portion_rating' => 4,
            'quality_rating' => 5,
            'comment' => 'Alexandre really enjoyed the grilled salmon today!',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Meal feedback submitted successfully.')
            ->assertJsonPath('feedback.rating', 5)
            ->assertJsonPath('feedback.student_id', $this->student->id);

        $this->assertDatabaseHas('meal_feedback', [
            'order_schedule_id' => $this->schedule->id,
            'student_id' => $this->student->id,
            'guardian_id' => $this->guardian->id,
            'rating' => 5,
        ]);
    }

    public function test_unauthorized_parent_cannot_submit_feedback_for_other_student(): void
    {
        $response = $this->actingAs($this->otherParent, 'sanctum')->postJson("/api/v1/orders/schedules/{$this->schedule->id}/feedback", [
            'rating' => 4,
            'comment' => 'Malicious or unauthorized feedback',
        ]);

        $response->assertStatus(403);
    }

    public function test_nutrition_consumption_logging_calculates_macros_accurately(): void
    {
        // 1. Fully consumed (100%)
        $resFull = $this->actingAs($this->parentUser, 'sanctum')->postJson('/api/v1/nutrition/consumption-logs', [
            'order_schedule_id' => $this->schedule->id,
            'consumption_status' => 'consumed',
            'notes' => 'Finished the whole bowl.',
        ]);

        $resFull->assertStatus(201)
            ->assertJsonPath('log.consumption_status', 'consumed')
            ->assertJsonPath('log.estimated_consumed_pct', '100.00')
            ->assertJsonPath('log.calories_consumed_kcal', '600.00')
            ->assertJsonPath('log.protein_consumed_g', '35.00');

        // 2. Partially consumed (75%)
        $resPartial = $this->actingAs($this->parentUser, 'sanctum')->postJson('/api/v1/nutrition/consumption-logs', [
            'order_schedule_id' => $this->schedule->id,
            'consumption_status' => 'partial',
            'estimated_consumed_pct' => 75.0,
            'notes' => 'Ate 3/4 of the bowl.',
        ]);

        $resPartial->assertStatus(201)
            ->assertJsonPath('log.consumption_status', 'partial')
            ->assertJsonPath('log.estimated_consumed_pct', '75.00')
            ->assertJsonPath('log.calories_consumed_kcal', '450.00') // 600 * 0.75
            ->assertJsonPath('log.protein_consumed_g', '26.25'); // 35 * 0.75

        // 3. Not consumed (0%)
        $resNone = $this->actingAs($this->parentUser, 'sanctum')->postJson('/api/v1/nutrition/consumption-logs', [
            'order_schedule_id' => $this->schedule->id,
            'consumption_status' => 'not_consumed',
            'notes' => 'Student was sick and could not eat.',
        ]);

        $resNone->assertStatus(201)
            ->assertJsonPath('log.consumption_status', 'not_consumed')
            ->assertJsonPath('log.estimated_consumed_pct', '0.00')
            ->assertJsonPath('log.calories_consumed_kcal', '0.00')
            ->assertJsonPath('log.protein_consumed_g', '0.00');
    }

    public function test_parent_nutrition_summary_endpoint(): void
    {
        // Log consumption first
        $this->actingAs($this->parentUser, 'sanctum')->postJson('/api/v1/nutrition/consumption-logs', [
            'order_schedule_id' => $this->schedule->id,
            'consumption_status' => 'consumed',
        ])->assertStatus(201);

        $response = $this->actingAs($this->parentUser, 'sanctum')->getJson("/api/v1/guardian/students/{$this->student->id}/nutrition-summary");

        $response->assertStatus(200)
            ->assertJsonPath('student_id', $this->student->id)
            ->assertJsonPath('totals.logged_meals', 1)
            ->assertJsonPath('totals.fully_consumed', 1)
            ->assertJsonPath('totals.total_calories_kcal', 600)
            ->assertJsonPath('totals.total_protein_g', 35);
    }

    public function test_operations_dashboard_kpi_report(): void
    {
        $response = $this->actingAs($this->superAdmin, 'sanctum')->getJson('/api/v1/admin/reports/operations-kpi');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'date',
                'orders' => ['total_count', 'total_volume_minor', 'by_status'],
                'kitchen_queue' => ['total_meals', 'pending', 'preparing', 'ready', 'dispatched', 'delivered'],
                'deliveries' => ['total_batches', 'completed_batches', 'completion_rate_pct'],
                'payments' => ['total_attempts', 'successful_count', 'success_rate_pct'],
            ]);
    }

    public function test_order_report_with_csv_export(): void
    {
        $response = $this->actingAs($this->superAdmin, 'sanctum')->getJson('/api/v1/admin/reports/orders?export=csv');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->getContent();
        $this->assertStringContainsString('ORD-REP-9001', $content);
        $this->assertStringContainsString('Alexandre Student', $content);
        $this->assertStringContainsString('Grilled Salmon Rice Bowl', $content);
    }

    public function test_school_delivery_reconciliation_report(): void
    {
        $response = $this->actingAs($this->superAdmin, 'sanctum')->getJson('/api/v1/admin/reports/school-delivery-reconciliation?' . http_build_query([
            'school_id' => $this->school->id,
            'date' => now()->toDateString(),
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('school_id', $this->school->id)
            ->assertJsonPath('summary.total_expected', 1)
            ->assertJsonPath('summary.delivered', 1)
            ->assertJsonPath('summary.completion_rate_pct', 100);
    }

    public function test_restaurant_production_summary_report(): void
    {
        $response = $this->actingAs($this->superAdmin, 'sanctum')->getJson('/api/v1/admin/reports/restaurant-production?' . http_build_query([
            'restaurant_id' => $this->restaurant->id,
            'date' => now()->toDateString(),
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('restaurant_id', $this->restaurant->id)
            ->assertJsonPath('total_production_count', 1);
    }

    public function test_nutrition_coverage_report_flags_unverified_menu_versions(): void
    {
        // Create an unverified version without nutrition facts
        $unverifiedItem = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Mystery Snack',
            'sku' => 'MYS-SNK-01',
            'status' => 'active',
        ]);

        MenuVersion::create([
            'menu_item_id' => $unverifiedItem->id,
            'version_no' => 1,
            'price_minor' => 150,
            'currency' => 'USD',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin, 'sanctum')->getJson('/api/v1/admin/reports/nutrition-coverage');

        $response->assertStatus(200)
            ->assertJsonPath('summary.total_active_menu_versions', 2)
            ->assertJsonPath('summary.verified_count', 1)
            ->assertJsonPath('summary.unverified_or_missing_count', 1);
    }
}
