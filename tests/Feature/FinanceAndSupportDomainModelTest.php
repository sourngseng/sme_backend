<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Campaign;
use App\Models\CommissionRule;
use App\Models\Expense;
use App\Models\Guardian;
use App\Models\MealFeedback;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuVersion;
use App\Models\NutritionConsumptionLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSchedule;
use App\Models\Promotion;
use App\Models\ReportSnapshot;
use App\Models\Restaurant;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLocation;
use App\Models\SchoolShift;
use App\Models\Settlement;
use App\Models\SettlementItem;
use App\Models\Student;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceAndSupportDomainModelTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $parentUser;
    protected Restaurant $restaurant;
    protected School $school;
    protected Student $student;
    protected Guardian $guardian;
    protected Order $order;
    protected OrderSchedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'super_admin'], ['guard_name' => 'web', 'display_name' => 'Super Admin']);

        $this->adminUser = User::create([
            'full_name' => 'Finance Director',
            'email' => 'finance@sme.test',
            'phone' => '+85512999001',
            'password' => 'Secret1234!',
            'status' => 'active',
        ]);
        $this->adminUser->roles()->attach($adminRole->id);

        $this->parentUser = User::create([
            'full_name' => 'Parent Tester',
            'email' => 'parent@sme.test',
            'phone' => '+85512999002',
            'password' => 'Secret1234!',
            'status' => 'active',
        ]);

        $this->guardian = Guardian::create([
            'user_id' => $this->parentUser->id,
            'relationship' => 'mother',
        ]);

        $this->school = School::create([
            'code' => 'SCH-FIN-01',
            'name' => 'Finance Academy',
            'level' => 'primary',
            'address' => 'Norodom Blvd, Phnom Penh',
            'status' => 'active',
        ]);

        $shift = SchoolShift::create([
            'school_id' => $this->school->id,
            'name' => 'Morning Shift',
            'start_time' => '07:30',
            'end_time' => '11:30',
            'order_cutoff_time' => '06:30',
        ]);

        $location = SchoolLocation::create([
            'school_id' => $this->school->id,
            'name' => 'Main Cafeteria',
            'address' => 'Building A, Ground Floor',
        ]);

        $this->student = Student::create([
            'student_code' => 'STU-FIN-01',
            'school_id' => $this->school->id,
            'school_shift_id' => $shift->id,
            'full_name' => 'Finley Student',
            'grade_level' => 'Grade 5',
            'status' => 'active',
        ]);
        $this->student->guardians()->attach($this->guardian->id, ['is_primary' => true]);

        $this->restaurant = Restaurant::create([
            'code' => 'REST-FIN-01',
            'name' => 'Healthy Bites',
            'owner_user_id' => $this->adminUser->id,
            'status' => 'active',
            'commission_default_pct' => 15.00,
        ]);

        $category = MenuCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Healthy Lunches',
        ]);

        $menuItem = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $category->id,
            'name' => 'Brown Rice & Chicken',
            'sku' => 'BRC-CHK-01',
            'meal_type' => 'lunch',
            'status' => 'active',
        ]);

        $menuVersion = MenuVersion::create([
            'menu_item_id' => $menuItem->id,
            'version_no' => 1,
            'price_minor' => 350,
            'currency' => 'USD',
            'status' => 'active',
            'cutoff_minutes' => 60,
            'max_daily_quantity' => 100,
        ]);

        $this->order = Order::create([
            'order_number' => 'ORD-FIN-1001',
            'guardian_id' => $this->guardian->id,
            'student_id' => $this->student->id,
            'school_id' => $this->school->id,
            'restaurant_id' => $this->restaurant->id,
            'status' => 'confirmed',
            'subtotal_minor' => 350,
            'total_minor' => 350,
            'placed_at' => now(),
        ]);

        $item = OrderItem::create([
            'order_id' => $this->order->id,
            'menu_version_id' => $menuVersion->id,
            'item_name_snapshot' => $menuItem->name,
            'unit_price_minor' => 350,
            'quantity' => 1,
            'line_total_minor' => 350,
        ]);

        $this->schedule = OrderSchedule::create([
            'order_id' => $this->order->id,
            'order_item_id' => $item->id,
            'delivery_date' => now()->toDateString(),
            'shift' => 'morning',
            'school_location_id' => $location->id,
            'status' => 'delivered',
            'cutoff_at' => now()->subHours(2),
            'delivered_at' => now()->subHour(),
        ]);
    }

    public function test_commission_rule_creation_and_relations(): void
    {
        $item = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Steamed Fish',
            'sku' => 'ST-FISH-01',
            'status' => 'active',
        ]);

        $version = MenuVersion::create([
            'menu_item_id' => $item->id,
            'version_no' => 1,
            'price_minor' => 400,
            'currency' => 'USD',
            'status' => 'active',
        ]);

        $rule = CommissionRule::create([
            'restaurant_id' => $this->restaurant->id,
            'menu_version_id' => $version->id,
            'commission_pct' => 12.50,
            'effective_from' => now()->toDateString(),
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('commission_rules', [
            'id' => $rule->id,
            'restaurant_id' => $this->restaurant->id,
            'commission_pct' => 12.50,
        ]);

        $this->assertEquals($this->restaurant->id, $rule->restaurant->id);
        $this->assertEquals($version->id, $rule->menuVersion->id);
        $this->assertTrue($this->restaurant->commissionRules->contains($rule));
    }

    public function test_settlement_and_settlement_items_with_auditable_transitions(): void
    {
        $this->actingAs($this->adminUser);

        $settlement = Settlement::create([
            'restaurant_id' => $this->restaurant->id,
            'period_start' => now()->startOfWeek()->toDateString(),
            'period_end' => now()->endOfWeek()->toDateString(),
            'gross_sales_minor' => 10000,
            'commission_minor' => 1500,
            'refund_minor' => 0,
            'adjustment_minor' => 0,
            'net_payable_minor' => 8500,
            'status' => 'draft',
        ]);

        $item = SettlementItem::create([
            'settlement_id' => $settlement->id,
            'order_id' => $this->order->id,
            'gross_minor' => 350,
            'commission_minor' => 52,
            'net_minor' => 298,
        ]);

        $this->assertDatabaseHas('settlements', [
            'id' => $settlement->id,
            'status' => 'draft',
            'net_payable_minor' => 8500,
        ]);
        $this->assertDatabaseHas('settlement_items', [
            'id' => $item->id,
            'settlement_id' => $settlement->id,
            'net_minor' => 298,
        ]);

        $this->assertEquals($settlement->id, $item->settlement->id);
        $this->assertEquals($this->order->id, $item->order->id);
        $this->assertTrue($settlement->items->contains($item));

        // Update status to approved then paid and verify audit logs
        $settlement->update(['status' => 'approved']);
        $settlement->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->assertEquals('paid', $settlement->fresh()->status);
        $this->assertNotNull($settlement->fresh()->paid_at);

        // Verify audit log captured the transition
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Settlement::class,
            'auditable_id' => $settlement->id,
            'action' => 'status_updated',
        ]);
    }

    public function test_expenses_tracking_and_user_ownership(): void
    {
        $this->actingAs($this->adminUser);

        $expense = Expense::create([
            'category' => 'Logistics & Fuel',
            'description' => 'Delivery van fuel reimbursement for driver fleet',
            'amount_minor' => 4500,
            'currency' => 'USD',
            'expense_date' => now()->toDateString(),
            'status' => 'recorded',
            'created_by' => $this->adminUser->id,
        ]);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount_minor' => 4500,
            'created_by' => $this->adminUser->id,
        ]);

        $this->assertEquals($this->adminUser->id, $expense->creator->id);
        $this->assertTrue($this->adminUser->expenses->contains($expense));

        $expense->update(['status' => 'approved']);
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Expense::class,
            'auditable_id' => $expense->id,
            'action' => 'status_updated',
        ]);
    }

    public function test_campaign_and_promotions_hierarchy_and_limits(): void
    {
        $this->actingAs($this->adminUser);

        $campaign = Campaign::create([
            'name' => 'Back To School 2026',
            'description' => 'Discounts for school term kickoff',
            'start_at' => now(),
            'end_at' => now()->addMonth(),
            'status' => 'active',
            'created_by' => $this->adminUser->id,
        ]);

        $promo = Promotion::create([
            'campaign_id' => $campaign->id,
            'code' => 'SCHOOLDAY20',
            'type' => 'percentage',
            'value' => 20.00,
            'max_discount_minor' => 200,
            'usage_limit' => 500,
            'used_count' => 12,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'name' => 'Back To School 2026']);
        $this->assertDatabaseHas('promotions', ['id' => $promo->id, 'code' => 'SCHOOLDAY20']);

        $this->assertEquals($campaign->id, $promo->campaign->id);
        $this->assertTrue($campaign->promotions->contains($promo));
        $this->assertTrue($this->adminUser->campaigns->contains($campaign));
    }

    public function test_meal_feedback_ratings_and_student_relationship(): void
    {
        $feedback = MealFeedback::create([
            'order_schedule_id' => $this->schedule->id,
            'student_id' => $this->student->id,
            'guardian_id' => $this->guardian->id,
            'rating' => 5,
            'taste_rating' => 5,
            'portion_rating' => 4,
            'quality_rating' => 5,
            'comment' => 'The chicken was tender and delicious! Finley loved it.',
        ]);

        $this->assertDatabaseHas('meal_feedback', [
            'id' => $feedback->id,
            'student_id' => $this->student->id,
            'rating' => 5,
        ]);

        $this->assertEquals($this->schedule->id, $feedback->schedule->id);
        $this->assertEquals($this->student->id, $feedback->student->id);
        $this->assertEquals($this->guardian->id, $feedback->guardian->id);
        $this->assertTrue($this->student->feedbacks->contains($feedback));
        $this->assertTrue($this->guardian->feedbacks->contains($feedback));
    }

    public function test_nutrition_consumption_log_metrics(): void
    {
        $log = NutritionConsumptionLog::create([
            'order_schedule_id' => $this->schedule->id,
            'student_id' => $this->student->id,
            'consumption_status' => 'consumed',
            'estimated_consumed_pct' => 100.00,
            'calories_consumed_kcal' => 520.50,
            'protein_consumed_g' => 28.50,
            'notes' => 'Plate cleaned completely during lunch break.',
            'recorded_at' => now(),
        ]);

        $this->assertDatabaseHas('nutrition_consumption_logs', [
            'id' => $log->id,
            'consumption_status' => 'consumed',
            'student_id' => $this->student->id,
        ]);

        $this->assertEquals($this->student->id, $log->student->id);
        $this->assertEquals($this->schedule->id, $log->schedule->id);
        $this->assertTrue($this->student->consumptionLogs->contains($log));
    }

    public function test_support_ticket_auto_numbering_and_messages(): void
    {
        $this->actingAs($this->parentUser);

        $ticket = SupportTicket::create([
            'opened_by' => $this->parentUser->id,
            'category' => 'Delivery Issue',
            'priority' => 'high',
            'status' => 'open',
            'subject' => 'Late delivery for morning shift',
            'description' => 'The meal arrived 15 minutes after shift started.',
        ]);

        // ticket_number must be auto-generated
        $this->assertNotNull($ticket->ticket_number);
        $this->assertStringStartsWith('TCK-', $ticket->ticket_number);

        $message = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $this->parentUser->id,
            'message' => 'Please confirm if driver route has been reviewed.',
        ]);

        $this->assertDatabaseHas('support_messages', [
            'id' => $message->id,
            'ticket_id' => $ticket->id,
        ]);

        $this->assertEquals($ticket->id, $message->ticket->id);
        $this->assertEquals($this->parentUser->id, $message->sender->id);
        $this->assertTrue($ticket->messages->contains($message));
        $this->assertTrue($this->parentUser->openedTickets->contains($ticket));

        // Assign to admin and resolve ticket with audit log check
        $this->actingAs($this->adminUser);
        $ticket->update([
            'assigned_to' => $this->adminUser->id,
            'status' => 'resolved',
        ]);

        $this->assertTrue($this->adminUser->assignedTickets->contains($ticket));
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => SupportTicket::class,
            'auditable_id' => $ticket->id,
        ]);
    }

    public function test_report_snapshot_json_data(): void
    {
        $snapshot = ReportSnapshot::create([
            'report_type' => 'monthly_settlement_recap',
            'period_start' => '2026-10-01',
            'period_end' => '2026-10-31',
            'filters' => ['restaurant_id' => $this->restaurant->id, 'status' => 'paid'],
            'data' => [
                'total_orders' => 450,
                'gross_sales_minor' => 157500,
                'commission_minor' => 23625,
                'net_payable_minor' => 133875,
            ],
            'generated_by' => $this->adminUser->id,
            'generated_at' => now(),
        ]);

        $this->assertDatabaseHas('report_snapshots', [
            'id' => $snapshot->id,
            'report_type' => 'monthly_settlement_recap',
        ]);

        $fresh = $snapshot->fresh();
        $this->assertIsArray($fresh->filters);
        $this->assertIsArray($fresh->data);
        $this->assertEquals(450, $fresh->data['total_orders']);
        $this->assertEquals($this->adminUser->id, $fresh->generator->id);
        $this->assertTrue($this->adminUser->reportSnapshots->contains($fresh));
    }
}
