<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class WebAuthTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $schoolUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Prepare roles if needed
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['guard_name' => 'web', 'display_name' => 'Super Administrator']
        );

        $schoolAdminRole = Role::firstOrCreate(
            ['name' => 'school_admin'],
            ['guard_name' => 'web', 'display_name' => 'School Administrator']
        );

        // Prepare test users
        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin@schoolmeal.test'],
            [
                'full_name' => 'SENG Sourng',
                'phone' => '+85512000001',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $this->adminUser->update([
            'full_name' => 'SENG Sourng',
            'password' => 'Password@123',
            'phone' => '+85512000001',
            'status' => 'active',
        ]);
        $this->adminUser->syncRoles(['super_admin']);

        $this->schoolUser = User::firstOrCreate(
            ['email' => 'school@schoolmeal.test'],
            [
                'full_name' => 'School Administrator',
                'phone' => '+85512000002',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $this->schoolUser->syncRoles(['school_admin']);
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SchoolMeal');
        $response->assertSee('Healthy Meals');
        $response->assertSee('Brighter Students');
        $response->assertSee('Sign in to your account');
        $response->assertSee('Platform Access For');
    }

    public function test_super_admin_can_login_and_is_redirected_to_admin_dashboard(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin@schoolmeal.test',
            'password' => 'Password@123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->adminUser);

        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Dashboard');
        $dashboardResponse->assertSee('Total Orders');
        $dashboardResponse->assertSee('Orders Overview');
    }

    public function test_login_with_remember_me_on_succeeds_and_sets_cookie(): void
    {
        // Test with 'on' (default HTML form checkbox submission)
        $response = $this->post('/login', [
            'login' => 'admin@schoolmeal.test',
            'password' => 'Password@123',
            'remember' => 'on',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertCookie(Auth::guard()->getRecallerName());
    }

    public function test_login_with_remember_me_one_succeeds_and_sets_cookie(): void
    {
        // Test with '1' (explicit checkbox value="1")
        $response = $this->post('/login', [
            'login' => 'admin@schoolmeal.test',
            'password' => 'Password@123',
            'remember' => '1',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertCookie(Auth::guard()->getRecallerName());
    }

    public function test_login_without_remember_me_does_not_set_remember_cookie(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin@schoolmeal.test',
            'password' => 'Password@123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertCookieMissing(Auth::guard()->getRecallerName());
    }

    public function test_login_with_phone_number_succeeds(): void
    {
        $response = $this->post('/login', [
            'login' => '+85512000001',
            'password' => 'Password@123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->adminUser);
    }

    public function test_login_with_phone_number_and_remember_me_succeeds_and_sets_cookie(): void
    {
        $response = $this->post('/login', [
            'login' => '+85512000001',
            'password' => 'Password@123',
            'remember' => '1',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertCookie(Auth::guard()->getRecallerName());
    }

    public function test_school_admin_is_redirected_to_school_dashboard(): void
    {
        $response = $this->post('/login', [
            'login' => 'school@schoolmeal.test',
            'password' => 'Password@123',
        ]);

        $response->assertRedirect('/school/dashboard');
        $this->assertAuthenticatedAs($this->schoolUser);

        $dashboardResponse = $this->get('/school/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('School Meal Coordination Portal');
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin@schoolmeal.test',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guest_can_switch_language_and_view_fully_translated_login_form(): void
    {
        // 1. Initial login page in English
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign in to your account');
        $response->assertSee('Healthy Meals,');
        $response->assertSee('Brighter Students');
        $response->assertSee('Platform Access For');
        $response->assertSee('switchLangEn');
        $response->assertSee('switchLangKm');

        // 2. Switch to Khmer via guest-accessible locale switch endpoint
        $switchResponse = $this->from('/login')->post('/locale/switch', [
            'locale' => 'km',
        ]);
        $switchResponse->assertRedirect('/login');
        $switchResponse->assertSessionHas('locale', 'km');

        // 3. Login page now renders in Khmer
        $kmResponse = $this->withSession(['locale' => 'km'])->get('/login');
        $kmResponse->assertStatus(200);
        $kmResponse->assertSee('ចូលទៅក្នុងគណនីរបស់អ្នក'); // Sign in to your account
        $kmResponse->assertSee('អាហារូបត្ថម្ភល្អ,'); // Healthy meals
        $kmResponse->assertSee('សិស្សានុសិស្សភ្លឺស្វាង'); // Brighter students
        $kmResponse->assertSee('ការចូលប្រើប្រាស់សម្រាប់'); // Platform Access For
        $kmResponse->assertSee('អ្នកគ្រប់គ្រង'); // Admin
        $kmResponse->assertSee('អ្នកគ្រប់គ្រងភោជនីយដ្ឋាន'); // Restaurant Admin
        $kmResponse->assertSee('ផ្ទះបាយ'); // Kitchen
        $kmResponse->assertSee('អ្នកដឹកជញ្ជូន'); // Driver
        $kmResponse->assertSee('ហិរញ្ញវត្ថុ'); // Finance

        // 4. Switch back to English
        $switchEnResponse = $this->from('/login')->post('/locale/switch', [
            'locale' => 'en',
        ]);
        $switchEnResponse->assertRedirect('/login');
        $switchEnResponse->assertSessionHas('locale', 'en');

        // 5. Login page returns to English
        $enResponse = $this->withSession(['locale' => 'en'])->get('/login');
        $enResponse->assertStatus(200);
        $enResponse->assertSee('Sign in to your account');
        $enResponse->assertSee('Healthy Meals,');
    }

    public function test_toast_notifications_are_used_for_messages_and_no_inline_alerts_rendered(): void
    {
        // 1. Check login page has toast container and does NOT render inline alerts
        $response = $this->withSession([
            'status' => 'Test password reset sent',
        ])->get('/login');

        $response->assertStatus(200);
        $response->assertSee('id="toastContainer"', false);
        $response->assertSee('window.Toast.success', false);
        $response->assertSee('Test password reset sent');
        // Ensure no inline alert boxes are placed in the form layout
        $response->assertDontSee('background: #f0fdf4; border: 1px solid #bbf7d0', false);

        // 2. Check validation error displays via Toast handler
        $errorResponse = $this->get('/login');
        $errorResponse = $this->from('/login')->post('/login', [
            'login' => 'nonexistent@test.com',
            'password' => 'wrong',
        ]);
        $followUp = $this->followRedirects($errorResponse);
        $followUp->assertStatus(200);
        $followUp->assertSee('window.Toast.error', false);
        $followUp->assertDontSee('background: #fef2f2; border: 1px solid #fecaca', false);

        // 3. Check admin dashboard/languages view uses Toast container
        $admin = User::first();
        $langResponse = $this->actingAs($admin)->withSession([
            'status' => 'Saved translation successfully',
        ])->get('/admin/languages');

        $langResponse->assertStatus(200);
        $langResponse->assertSee('id="toastContainer"', false);
        $langResponse->assertSee('window.Toast.success', false);
        $langResponse->assertSee('Saved translation successfully');
    }

    public function test_admin_dashboard_renders_meta_style_scrollable_insights_carousel(): void
    {
        $admin = User::first();
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('id="insightsTrack"', false);
        $response->assertSee('id="insightsPrevBtn"', false);
        $response->assertSee('id="insightsNextBtn"', false);
        $response->assertSee('id="insightsFadeLeft"', false);
        $response->assertSee('id="insightsFadeRight"', false);
        $response->assertSee('Key Insights');
        $response->assertSee('Scroll to explore');
        $response->assertSee('Total Orders');
        $response->assertSee('Revenue (USD)');
        $response->assertSee('Meals Served');
        $response->assertSee('Satisfaction Rate');
    }

    public function test_admin_dashboard_renders_responsive_scrollable_graphs_and_lists_rails(): void
    {
        $admin = User::first();
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);

        // Graphs Rail Assertions
        $response->assertSee('id="graphsTrack"', false);
        $response->assertSee('id="graphsPrevBtn"', false);
        $response->assertSee('id="graphsNextBtn"', false);
        $response->assertSee('id="graphsFadeLeft"', false);
        $response->assertSee('id="graphsFadeRight"', false);
        $response->assertSee('Visual Analytics & Monitoring');
        $response->assertSee('chartOrdersOverview');
        $response->assertSee('chartOrdersByStatus');
        $response->assertSee('siemReapMap');
        $response->assertSee('window.scrollGraphs');

        // Lists Rail Assertions
        $response->assertSee('id="listsTrack"', false);
        $response->assertSee('id="listsPrevBtn"', false);
        $response->assertSee('id="listsNextBtn"', false);
        $response->assertSee('id="listsFadeLeft"', false);
        $response->assertSee('id="listsFadeRight"', false);
        $response->assertSee('Operations & Leaderboard Lists');
        $response->assertSee('Top Restaurants by Orders');
        $response->assertSee('Top Schools by Orders');
        $response->assertSee('Recent Orders');
        $response->assertSee('window.scrollLists');
    }

    public function test_admin_dashboard_renders_operational_health_and_gauges_scrollable_rail(): void
    {
        $admin = User::first();
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);

        // Gauges & Stream Rail Assertions
        $response->assertSee('id="gaugesTrack"', false);
        $response->assertSee('id="gaugesPrevBtn"', false);
        $response->assertSee('id="gaugesNextBtn"', false);
        $response->assertSee('id="gaugesFadeLeft"', false);
        $response->assertSee('id="gaugesFadeRight"', false);
        $response->assertSee('Operational Health & Live Stream');
        $response->assertSee('Payment Status');
        $response->assertSee('Delivery Performance');
        $response->assertSee('Student Meal Participation');
        $response->assertSee('Recent Activities');
        $response->assertSee('filterActivities');
        $response->assertSee('window.scrollGauges');
    }

    public function test_master_layout_renders_mobile_bottom_bar(): void
    {
        $admin = User::first();
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);

        // Mobile Bottom Bar assertions
        $response->assertSee('id="mobileBottomBar"', false);
        $response->assertSee('id="mobileTabDashboard"', false);
        $response->assertSee('id="mobileTabAnalytics"', false);
        $response->assertSee('id="mobileTabOrders"', false);
        $response->assertSee('id="mobileTabNotifs"', false);
        $response->assertSee('id="mobileTabMenu"', false);
        $response->assertSee('toggleSidebarCollapse()', false);
    }

    public function test_admin_dashboard_renders_interactive_date_filter_and_switches_ranges(): void
    {
        $admin = $this->adminUser;
        $response = $this->actingAs($admin)->get('/admin/dashboard?range=last_7_days');

        $response->assertStatus(200);
        $response->assertSee('id="adminDateFilter_btn"', false);
        $response->assertSee('id="adminDateFilter_dropdown"', false);
        $response->assertSee('id="adminDateFilter_backdrop"', false);
        $response->assertSee('mobile-drag-handle', false);
        $response->assertSee('date-filter-close-btn', false);
        $response->assertSee('Oct 16, 2026 – Oct 22, 2026');
        $response->assertSee('window.applyDashboardDateFilter', false);
    }

    public function test_admin_dashboard_returns_filtered_json_data_for_ajax(): void
    {
        $admin = $this->adminUser;
        $response = $this->actingAs($admin)->get('/admin/dashboard?range=today&format=json');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'stats' => [
                'total_orders',
                'revenue_usd',
                'active_schools',
                'total_students',
                'partner_restaurants',
                'ontime_delivery_pct',
                'meals_served',
                'satisfaction_rate',
            ],
            'chartData' => [
                'ordersOverview',
                'ordersByStatus',
                'paymentStatus',
                'deliveryPerformance',
                'studentParticipation',
            ],
            'topRestaurants',
            'topSchools',
            'recentOrders',
            'activeRange',
            'activeLabel',
        ]);
        $this->assertEquals('today', $response->json('activeRange'));
    }

    public function test_all_dashboards_support_date_filtering_and_json_responses(): void
    {
        $admin = $this->adminUser;

        // Kitchen/Restaurant
        $restaurantRes = $this->actingAs($admin)->get('/restaurant/dashboard?range=last_7_days&format=json');
        $restaurantRes->assertStatus(200);
        $restaurantRes->assertJsonPath('success', true);
        $this->assertArrayHasKey('metrics', $restaurantRes->json());

        // School
        $schoolRes = $this->actingAs($admin)->get('/school/dashboard?range=this_month&format=json');
        $schoolRes->assertStatus(200);
        $schoolRes->assertJsonPath('success', true);
        $this->assertArrayHasKey('metrics', $schoolRes->json());

        // Finance
        $financeRes = $this->actingAs($admin)->get('/finance/dashboard?range=today&format=json');
        $financeRes->assertStatus(200);
        $financeRes->assertJsonPath('success', true);
        $this->assertArrayHasKey('metrics', $financeRes->json());

        // Marketing
        $marketingRes = $this->actingAs($admin)->get('/marketing/dashboard?range=last_7_days&format=json');
        $marketingRes->assertStatus(200);
        $marketingRes->assertJsonPath('success', true);
        $this->assertArrayHasKey('metrics', $marketingRes->json());

        // Driver
        $driverRes = $this->actingAs($admin)->get('/driver/dashboard?range=last_7_days&format=json');
        $driverRes->assertStatus(200);
        $driverRes->assertJsonPath('success', true);
        $this->assertArrayHasKey('metrics', $driverRes->json());

        // Parent
        $parentRes = $this->actingAs($admin)->get('/parent/dashboard?range=last_7_days&format=json');
        $parentRes->assertStatus(200);
        $parentRes->assertJsonPath('success', true);
        $this->assertArrayHasKey('metrics', $parentRes->json());

        // Student
        $studentRes = $this->actingAs($admin)->get('/student/dashboard?range=today&format=json');
        $studentRes->assertStatus(200);
        $studentRes->assertJsonPath('success', true);
        $this->assertArrayHasKey('metrics', $studentRes->json());

        // Support
        $supportRes = $this->actingAs($admin)->get('/support/dashboard?range=last_7_days&format=json');
        $supportRes->assertStatus(200);
        $supportRes->assertJsonPath('success', true);
        $this->assertArrayHasKey('metrics', $supportRes->json());
    }

    public function test_admin_dashboard_renders_dynamic_graph_trends_and_card_filters(): void
    {
        $admin = $this->adminUser;
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        // Trend indicators & badges
        $response->assertSee('id="overviewTrendBadge"', false);
        $response->assertSee('id="overviewTrendText"', false);
        $response->assertSee('toggleDataset', false);

        // Per-card filter controls
        $response->assertSee('id="filterOrdersOverview"', false);
        $response->assertSee('data-chart-target="ordersOverview"', false);
        $response->assertSee('id="filterOrdersByStatus"', false);
        $response->assertSee('id="filterPaymentStatus"', false);
        $response->assertSee('id="filterDeliveryPerformance"', false);
        $response->assertSee('id="filterStudentParticipation"', false);

        // Reactive sparklines with area gradients and pulse tips
        $response->assertSee('kpi-sparkline-gradient', false);
        $response->assertSee('kpi-sparkline-area', false);
        $response->assertSee('kpi-sparkline-line', false);
        $response->assertSee('kpi-sparkline-pulse', false);
        $response->assertSee('kpi-sparkline-dot', false);

        // Test custom date range AJAX filtering
        $ajaxResponse = $this->actingAs($admin)->get('/admin/dashboard?range=custom&start_date=2026-10-01&end_date=2026-10-20&format=json');
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonPath('success', true);
        $ajaxResponse->assertJsonPath('activeRange', 'custom');
        $this->assertNotEmpty($ajaxResponse->json('chartData.ordersOverview.revenue'));
    }

    public function test_super_admin_left_sidebar_renders_complete_sections_and_scrollable_container(): void
    {
        $admin = $this->adminUser;
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);

        // Sidebar container and scrollable menu class
        $response->assertSee('id="adminSidebar"', false);
        $response->assertSee('class="sidebar-menu"', false);

        // Brand & Platform title
        $response->assertSee('SchoolMeal');
        $response->assertSee('E-Commerce & Nutrition Platform');

        // All 5 Category Section Titles
        $response->assertSee('MANAGEMENT');
        $response->assertSee('FINANCE');
        $response->assertSee('OPERATIONS');
        $response->assertSee('REPORTS');
        $response->assertSee('SYSTEM');

        // Management links & dropdowns
        $response->assertSee('id="groupUsers"', false);
        $response->assertSee('Users & Roles');
        $response->assertSee('Schools');
        $response->assertSee('Students');
        $response->assertSee('Restaurants');
        $response->assertSee('id="groupMenu"', false);
        $response->assertSee('Menu & Nutrition');
        $response->assertSee('id="groupOrders"', false);
        $response->assertSee('Orders');
        $response->assertSee('Payments');
        $response->assertSee('id="groupDelivery"', false);
        $response->assertSee('Delivery & Drivers');

        // Finance links
        $response->assertSee('Commissions');
        $response->assertSee('Settlements');
        $response->assertSee('Financial Reports');

        // Operations links
        $response->assertSee('id="groupMarketing"', false);
        $response->assertSee('Marketing');
        $response->assertSee('Notifications');
        $response->assertSee('Support Tickets');

        // Reports links
        $response->assertSee('Analytics');
        $response->assertSee('Reports');

        // System links
        $response->assertSee('Manage Languages');
        $response->assertSee('Settings');
        $response->assertSee('Audit Logs');
        $response->assertSee('System Health');

        // Pinned bottom user profile card
        $response->assertSee('id="sidebarUserBox"', false);
        $response->assertSee('SENG Sourng');
        $response->assertSee('Super Admin');
    }

    public function test_login_page_renders_all_six_platform_access_personas_including_restaurant_admin(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Platform Access For');
        $response->assertSee('id="pillAdmin"', false);
        $response->assertSee('id="pillSchool"', false);
        $response->assertSee('id="pillRestaurantAdmin"', false);
        $response->assertSee('id="pillKitchen"', false);
        $response->assertSee('id="pillDriver"', false);
        $response->assertSee('id="pillFinance"', false);
        $response->assertSee('Restaurant Admin');
        $response->assertSee('merchant@schoolmeal.test');
        $response->assertSee('kitchen@schoolmeal.test');
    }

    public function test_restaurant_admin_can_login_and_is_redirected_to_restaurant_dashboard(): void
    {
        Role::firstOrCreate(['name' => 'restaurant_owner'], ['guard_name' => 'web', 'display_name' => 'Restaurant Owner']);
        $merchant = User::firstOrCreate(
            ['email' => 'merchant@schoolmeal.test'],
            [
                'full_name' => 'Green Healthy Kitchen Owner',
                'phone' => '+85512000004',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $merchant->syncRoles(['restaurant_owner']);

        $response = $this->post('/login', [
            'login' => 'merchant@schoolmeal.test',
            'password' => 'Password@123',
        ]);

        $response->assertRedirect('/restaurant/dashboard');
        $this->assertAuthenticatedAs($merchant);

        $dashboardResponse = $this->get('/restaurant/dashboard');
        $dashboardResponse->assertStatus(200);
    }

    public function test_kitchen_staff_can_login_and_is_redirected_to_kitchen_dashboard(): void
    {
        Role::firstOrCreate(['name' => 'restaurant_staff'], ['guard_name' => 'web', 'display_name' => 'Restaurant Staff']);
        $kitchen = User::firstOrCreate(
            ['email' => 'kitchen@schoolmeal.test'],
            [
                'full_name' => 'Sokha Ngin (Kitchen Staff)',
                'phone' => '+85512000009',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $kitchen->syncRoles(['restaurant_staff']);

        $response = $this->post('/login', [
            'login' => 'kitchen@schoolmeal.test',
            'password' => 'Password@123',
        ]);

        $response->assertRedirect('/kitchen/dashboard');
        $this->assertAuthenticatedAs($kitchen);

        $dashboardResponse = $this->get('/kitchen/dashboard');
        $dashboardResponse->assertStatus(200);
    }
}

