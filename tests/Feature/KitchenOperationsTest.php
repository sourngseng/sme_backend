<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KitchenOperationsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kitchenUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(
            ['name' => 'restaurant_staff'],
            ['description' => 'Kitchen Staff Operations']
        );

        $this->kitchenUser = User::firstOrCreate(
            ['email' => 'kitchen@schoolmeal.test'],
            [
                'full_name' => 'Sokha Ngin',
                'phone' => '+85512000099',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $this->kitchenUser->syncRoles(['restaurant_staff']);
    }

    /**
     * Test kitchen dashboard renders with complete operations layout and components.
     */
    public function test_kitchen_dashboard_renders_with_operations_layout(): void
    {
        $response = $this->actingAs($this->kitchenUser)->get('/kitchen/dashboard');

        $response->assertStatus(200);

        // Sidebar assertions matching the design reference image
        $response->assertSee('Khmer Kitchen');
        $response->assertSee('Restaurant Staff');
        $response->assertSee('Online');
        $response->assertSee('KITCHEN OPERATIONS');
        $response->assertSee('Kitchen Queue');
        $response->assertSee('Preparing Meals');
        $response->assertSee('Completed Meals');
        $response->assertSee('Meal Schedule');
        $response->assertSee('Menu Items');
        $response->assertSee('Incoming Orders');
        $response->assertSee('Order History');
        $response->assertSee('Cancellations');
        $response->assertSee('Delivery Handover');
        $response->assertSee('Drivers');
        $response->assertSee('Daily Summary');
        $response->assertSee('Profile');
        $response->assertSee('Notifications');
        $response->assertSee('Logout');

        // Sidebar badge counts
        $response->assertSee('class="kitchen-badge-count">12<', false);
        $response->assertSee('class="kitchen-badge-count">8<', false);
        $response->assertSee('class="kitchen-badge-count">3<', false);

        // Greeting and shift
        $response->assertSee('Good morning, Sokha!');
        $response->assertSee("Let's prepare healthy meals for our students.");
        $response->assertSee('Lunch Shift');
        $response->assertSee('10:00 - 14:00');

        // 5 Metric Stat Cards
        $response->assertSee("Today's Orders");
        $response->assertSee('124');
        $response->assertSee('Meals to Prepare');
        $response->assertSee('58');
        $response->assertSee('Prepared Meals');
        $response->assertSee('42');
        $response->assertSee('Ready for Handover');
        $response->assertSee('32');
        $response->assertSee('Delayed Meals');
        $response->assertSee('4');

        // All 8 rows in Kitchen Queue Table
        $response->assertSee('ORD-00121');
        $response->assertSee('Sok Pisey');
        $response->assertSee('Chicken Rice Bowl');
        $response->assertSee('ORD-00120');
        $response->assertSee('Chan Dany');
        $response->assertSee('Fried Noodle');
        $response->assertSee('ORD-00119');
        $response->assertSee('Lim Ratanak');
        $response->assertSee('Beef Pasta');
        $response->assertSee('ORD-00118');
        $response->assertSee('Meas Sophea');
        $response->assertSee('Grilled Fish');
        $response->assertSee('ORD-00117');
        $response->assertSee('Pich Vireak');
        $response->assertSee('Vegetable Rice');
        $response->assertSee('ORD-00116');
        $response->assertSee('Srey Leak');
        $response->assertSee('Chicken Curry');
        $response->assertSee('ORD-00115');
        $response->assertSee('Vuthy Rin');
        $response->assertSee('ORD-00114');
        $response->assertSee('Kunthea');

        // Dietary tags
        $response->assertSee('diet-badge egg', false);
        $response->assertSee('diet-badge dairy', false);
        $response->assertSee('diet-badge vegetarian', false);

        // Mobile Order Cards and Scrollable Filter Rail
        $response->assertSee('queue-filter-rail', false);
        $response->assertSee('data-filter="all"', false);
        $response->assertSee('data-filter="Preparing"', false);
        $response->assertSee('data-filter="Ready"', false);
        $response->assertSee('data-filter="Delayed"', false);
        $response->assertSee('queue-cards-wrapper', false);
        $response->assertSee('queue-card', false);
        $response->assertSee('order-card-header', false);
        $response->assertSee('order-card-body', false);
        $response->assertSee('order-card-footer', false);
        $response->assertSee('queueCardsEmptyState', false);

        // Ellipsis truncation and context menu for action buttons
        $response->assertSee('cell-truncate', false);
        $response->assertSee('btn-context-trigger', false);
        $response->assertSee('btn-card-context', false);
        $response->assertSee('kitchenOrderContextMenu', false);

        // Preparing Now Card
        $response->assertSee('Preparing Now');
        $response->assertSee('/ 10 prepared');
        $response->assertSee('12 min left');
        $response->assertSee('Mark as Ready');
        $response->assertSee('View Details');
        $response->assertSee('stepperVal', false);

        // Next in queue
        $response->assertSee('Next in Queue');
        $response->assertSee('View All');

        // Bottom 4 Cards
        $response->assertSee("Today's Meal Progress");
        $response->assertSee('68%');
        $response->assertSee('42 / 58');
        $response->assertSee('Meals Prepared');
        $response->assertSee('Meal Type Breakdown');
        $response->assertSee('Lunch');
        $response->assertSee('Morning Snack');
        $response->assertSee('Afternoon Snack');
        $response->assertSee('Special Diet');
        $response->assertSee('Allergen Alerts Today');
        $response->assertSee('Egg');
        $response->assertSee('Dairy');
        $response->assertSee('Peanut');
        $response->assertSee('Gluten');

        // Quick Actions 4 buttons
        $response->assertSee('Quick Actions');
        $response->assertSee('Start Preparing');
        $response->assertSee('Mark as Ready');
        $response->assertSee('View Orders');
        $response->assertSee('Delivery Handover');

        // UI & Notification Rule: strictly Toast container, no inline banners
        $response->assertSee('toast-container', false);

        // Mobile Bottom Bar & Safe Bottom Spacer for mobile layout view
        $response->assertSee('mobile-bottom-bar', false);
        $response->assertSee('mobile-only-spacer', false);
    }

    /**
     * Test restaurant owner portal also renders complete Kitchen Operations sidebar and layout.
     */
    public function test_restaurant_dashboard_renders_with_operations_layout(): void
    {
        Role::firstOrCreate(['name' => 'restaurant_owner'], ['description' => 'Restaurant Owner']);
        $owner = User::firstOrCreate(
            ['email' => 'owner@schoolmeal.test'],
            [
                'full_name' => 'Restaurant Owner',
                'phone' => '+85512000098',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $owner->syncRoles(['restaurant_owner']);

        $response = $this->actingAs($owner)->get('/restaurant/dashboard');
        $response->assertStatus(200);

        // Sidebar assertions matching the design reference image
        $response->assertSee('Khmer Kitchen');
        $response->assertSee('Kitchen Admin');
        $response->assertSee('Online');
        $response->assertSee('KITCHEN OPERATIONS');
        $response->assertSee('Kitchen Queue');
        $response->assertSee('Preparing Meals');
        $response->assertSee('Completed Meals');
        $response->assertSee('Meal Schedule');
        $response->assertSee('Menu Items');
        $response->assertSee('Incoming Orders');
        $response->assertSee('Order History');
        $response->assertSee('Cancellations');
        $response->assertSee('Delivery Handover');
        $response->assertSee('Drivers');
        $response->assertSee('Daily Summary');
        $response->assertSee('Profile');
        $response->assertSee('Notifications');
        $response->assertSee('Logout');

        // Sidebar badge counts
        $response->assertSee('class="kitchen-badge-count">12<', false);
        $response->assertSee('class="kitchen-badge-count">8<', false);
        $response->assertSee('class="kitchen-badge-count">3<', false);

        // Mobile Order Cards and Scrollable Filter Rail
        $response->assertSee('queue-filter-rail', false);
        $response->assertSee('data-filter="all"', false);
        $response->assertSee('queue-cards-wrapper', false);
        $response->assertSee('queue-card', false);
        $response->assertSee('cell-truncate', false);
        $response->assertSee('btn-context-trigger', false);
        $response->assertSee('kitchenOrderContextMenu', false);
    }

    /**
     * Test Language Switcher uses crisp SVG flags instead of text country codes.
     */
    public function test_language_switcher_uses_svg_flags_instead_of_text_codes(): void
    {
        $response = $this->actingAs($this->kitchenUser)->get('/kitchen/dashboard');
        $response->assertStatus(200);

        // Assert SVG flag graphics exist in topbar language switcher
        $response->assertSee('flags/gb.svg', false);
        $response->assertSee('flags/km.svg', false);

        // Assert country code strings 'GB EN' or 'KM' are NOT used inside the button
        $response->assertDontSee('lang-text-mobile">🇬🇧 EN', false);
        $response->assertDontSee('lang-text-mobile">🇰🇭 KM', false);

        // Switch to Khmer and verify Cambodia flag is active
        $kmResponse = $this->actingAs($this->kitchenUser)->withSession(['locale' => 'km'])->get('/kitchen/dashboard');
        $kmResponse->assertStatus(200);
        $kmResponse->assertSee('flags/km.svg', false);
    }

    /**
     * Test all dashboards in project render Meta (Facebook) Insights scrollable rails.
     */
    public function test_kitchen_and_all_portals_render_meta_insights_scrollable_rail(): void
    {
        // 1. Kitchen Staff Dashboard
        $staffRes = $this->actingAs($this->kitchenUser)->get('/kitchen/dashboard');
        $staffRes->assertStatus(200);
        $staffRes->assertSee('kitchen-insights-wrapper', false);
        $staffRes->assertSee('kitchenMetricsTrack', false);
        $staffRes->assertSee('kitchenMetricsFadeLeft', false);
        $staffRes->assertSee('kitchenMetricsFadeRight', false);
        $staffRes->assertSee('kitchenMetricsPrevBtn', false);
        $staffRes->assertSee('kitchenMetricsNextBtn', false);

        // 2. Restaurant Owner Dashboard
        Role::firstOrCreate(['name' => 'restaurant_owner'], ['description' => 'Restaurant Owner']);
        $owner = User::firstOrCreate(
            ['email' => 'owner_insights@schoolmeal.test'],
            ['full_name' => 'Owner Insights', 'phone' => '+85512000077', 'password' => 'Password@123', 'status' => 'active']
        );
        $owner->syncRoles(['restaurant_owner']);
        $ownerRes = $this->actingAs($owner)->get('/restaurant/dashboard');
        $ownerRes->assertStatus(200);
        $ownerRes->assertSee('kitchen-insights-wrapper', false);
        $ownerRes->assertSee('kitchenMetricsTrack', false);
        $ownerRes->assertSee('kitchenMetricsFadeLeft', false);
        $ownerRes->assertSee('kitchenMetricsFadeRight', false);

        // 3. School Admin Portal
        Role::firstOrCreate(['name' => 'school_admin'], ['description' => 'School Admin']);
        $schoolUser = User::firstOrCreate(
            ['email' => 'school_insights@schoolmeal.test'],
            ['full_name' => 'School Admin', 'phone' => '+85512000071', 'password' => 'Password@123', 'status' => 'active']
        );
        $schoolUser->syncRoles(['school_admin']);
        $schoolRes = $this->actingAs($schoolUser)->get('/school/dashboard');
        $schoolRes->assertStatus(200);
        $schoolRes->assertSee('insights-track-container', false);
        $schoolRes->assertSee('portal-kpi-grid', false);
        $schoolRes->assertSee('insights-fade-edge fade-left', false);
        $schoolRes->assertSee('insights-fade-edge fade-right', false);

        // 4. Finance Portal
        Role::firstOrCreate(['name' => 'finance'], ['description' => 'Finance']);
        $financeUser = User::firstOrCreate(
            ['email' => 'finance_insights@schoolmeal.test'],
            ['full_name' => 'Finance Admin', 'phone' => '+85512000072', 'password' => 'Password@123', 'status' => 'active']
        );
        $financeUser->syncRoles(['finance']);
        $financeRes = $this->actingAs($financeUser)->get('/finance/dashboard');
        $financeRes->assertStatus(200);
        $financeRes->assertSee('insights-track-container', false);
        $financeRes->assertSee('portal-kpi-grid', false);

        // 5. Driver Portal
        Role::firstOrCreate(['name' => 'driver'], ['description' => 'Driver']);
        $driverUser = User::firstOrCreate(
            ['email' => 'driver_insights@schoolmeal.test'],
            ['full_name' => 'Driver User', 'phone' => '+85512000073', 'password' => 'Password@123', 'status' => 'active']
        );
        $driverUser->syncRoles(['driver']);
        $driverRes = $this->actingAs($driverUser)->get('/driver/dashboard');
        $driverRes->assertStatus(200);
        $driverRes->assertSee('insights-track-container', false);
        $driverRes->assertSee('portal-kpi-grid', false);

        // 6. Marketing Portal
        Role::firstOrCreate(['name' => 'marketing'], ['description' => 'Marketing']);
        $marketingUser = User::firstOrCreate(
            ['email' => 'marketing_insights@schoolmeal.test'],
            ['full_name' => 'Marketing User', 'phone' => '+85512000074', 'password' => 'Password@123', 'status' => 'active']
        );
        $marketingUser->syncRoles(['marketing']);
        $marketingRes = $this->actingAs($marketingUser)->get('/marketing/dashboard');
        $marketingRes->assertStatus(200);
        $marketingRes->assertSee('insights-track-container', false);
        $marketingRes->assertSee('portal-kpi-grid', false);

        // 7. Parent Portal
        Role::firstOrCreate(['name' => 'parent'], ['description' => 'Parent']);
        $parentUser = User::firstOrCreate(
            ['email' => 'parent_insights@schoolmeal.test'],
            ['full_name' => 'Parent User', 'phone' => '+85512000075', 'password' => 'Password@123', 'status' => 'active']
        );
        $parentUser->syncRoles(['parent']);
        $parentRes = $this->actingAs($parentUser)->get('/parent/dashboard');
        $parentRes->assertStatus(200);
        $parentRes->assertSee('insights-track-container', false);
        $parentRes->assertSee('portal-kpi-grid', false);

        // 8. Student Portal
        Role::firstOrCreate(['name' => 'student'], ['description' => 'Student']);
        $studentUser = User::firstOrCreate(
            ['email' => 'student_insights@schoolmeal.test'],
            ['full_name' => 'Student User', 'phone' => '+85512000076', 'password' => 'Password@123', 'status' => 'active']
        );
        $studentUser->syncRoles(['student']);
        $studentRes = $this->actingAs($studentUser)->get('/student/dashboard');
        $studentRes->assertStatus(200);
        $studentRes->assertSee('insights-track-container', false);
        $studentRes->assertSee('portal-kpi-grid', false);

        // 9. Support Portal
        Role::firstOrCreate(['name' => 'support'], ['description' => 'Support']);
        $supportUser = User::firstOrCreate(
            ['email' => 'support_insights@schoolmeal.test'],
            ['full_name' => 'Support User', 'phone' => '+85512000078', 'password' => 'Password@123', 'status' => 'active']
        );
        $supportUser->syncRoles(['support']);
        $supportRes = $this->actingAs($supportUser)->get('/support/dashboard');
        $supportRes->assertStatus(200);
        $supportRes->assertSee('insights-track-container', false);
        $supportRes->assertSee('portal-kpi-grid', false);
    }

    /**
     * Test kitchen queue table renders consolidated 5-column layout with inline action options.
     */
    public function test_kitchen_queue_table_renders_consolidated_columns_and_inline_action_options(): void
    {
        $response = $this->actingAs($this->kitchenUser)->get('/kitchen/dashboard');
        $response->assertStatus(200);

        // Header assertions: consolidated columns with fixed Order ID & Student and merged Status & Time Left
        $response->assertSee('Order ID & Student', false);
        $response->assertSee('Meal, Qty & Dietary', false);
        $response->assertSee('Status & Time Left', false);
        $response->assertSee('col-sticky-seq', false);
        $response->assertSee('col-sticky-order', false);
        $response->assertSee('status-time-composite', false);

        // Column 1 composite structure: Order ID + Student + Action Option
        $response->assertSee('order-student-composite', false);
        $response->assertSee('order-id-line', false);
        $response->assertSee('cell-order-id', false);
        $response->assertSee('student-action-line', false);
        $response->assertSee('cell-student', false);
        $response->assertSee('action-options-inline', false);
        $response->assertSee('btn-micro-action', false);

        // Column 2 composite structure: Meal + Qty + Dietary Info
        $response->assertSee('meal-composite', false);
        $response->assertSee('meal-name-row', false);
        $response->assertSee('cell-meal', false);
        $response->assertSee('meal-meta-row', false);
        $response->assertSee('meal-qty-badge', false);

        // DataTable Server-Side Pagination Controls & Mobile Layout
        $response->assertSee('dt-server-pagination', false);
        $response->assertSee('dt-server-meta', false);
        $response->assertSee('dt-select-wrap', false);
        $response->assertSee('queuePaginationInfo', false);
        $response->assertSee('queuePerPageSelect', false);
        $response->assertSee('queuePrevPageBtn', false);
        $response->assertSee('queueNextPageBtn', false);
        $response->assertSee('queuePageNumbers', false);
        $response->assertSee('kitchenQueuePaginationBottom', false);
        $response->assertSee('mobile-only-pagination', false);
    }

    /**
     * Test DataTable Server-Side Pagination AJAX endpoint with filtering and searching.
     */
    public function test_kitchen_datatable_server_side_pagination_endpoint(): void
    {
        // 1. Page 1 with per_page = 5
        $resPage1 = $this->actingAs($this->kitchenUser)->getJson('/kitchen/queue-paginate?page=1&per_page=5&draw=1');
        $resPage1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'draw' => 1,
                'recordsTotal' => 12,
                'recordsFiltered' => 12,
                'current_page' => 1,
                'per_page' => 5,
                'last_page' => 3,
                'from' => 1,
                'to' => 5,
            ])
            ->assertJsonCount(5, 'data');

        // 2. Page 2 with per_page = 5
        $resPage2 = $this->actingAs($this->kitchenUser)->getJson('/kitchen/queue-paginate?page=2&per_page=5&draw=2');
        $resPage2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'draw' => 2,
                'current_page' => 2,
                'from' => 6,
                'to' => 10,
            ])
            ->assertJsonCount(5, 'data');

        // 3. Status filter: Preparing
        $resPreparing = $this->actingAs($this->kitchenUser)->getJson('/kitchen/queue-paginate?status=Preparing&per_page=10');
        $resPreparing->assertStatus(200)
            ->assertJson([
                'success' => true,
                'recordsFiltered' => 6,
                'total' => 6,
            ])
            ->assertJsonCount(6, 'data');

        // 4. Status filter: Delayed
        $resDelayed = $this->actingAs($this->kitchenUser)->getJson('/kitchen/queue-paginate?status=Delayed');
        $resDelayed->assertStatus(200)
            ->assertJson([
                'success' => true,
                'recordsFiltered' => 2,
                'total' => 2,
            ]);

        // 5. Keyword Search: 'Sok Pisey'
        $resSearch = $this->actingAs($this->kitchenUser)->getJson('/kitchen/queue-paginate?search=Sok%20Pisey');
        $resSearch->assertStatus(200)
            ->assertJson([
                'success' => true,
                'recordsFiltered' => 1,
            ])
            ->assertJsonPath('data.0.student', 'Sok Pisey')
            ->assertJsonPath('data.0.id', 'ORD-00121');

        // 6. Restaurant Owner Endpoint parity
        Role::firstOrCreate(['name' => 'restaurant_owner'], ['description' => 'Restaurant Owner']);
        $owner = User::firstOrCreate(
            ['email' => 'owner_dt@schoolmeal.test'],
            ['full_name' => 'Owner DT', 'phone' => '+85512000079', 'password' => 'Password@123', 'status' => 'active']
        );
        $owner->syncRoles(['restaurant_owner']);
        $ownerRes = $this->actingAs($owner)->getJson('/restaurant/queue-paginate?page=1&per_page=8');
        $ownerRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'recordsTotal' => 12,
                'current_page' => 1,
                'per_page' => 8,
            ]);
    }
}
