<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SystemSettingTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super_admin'], ['description' => 'System Root Super Admin']);
        Role::firstOrCreate(['name' => 'parent'], ['description' => 'Parent User']);

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'sysadmin.tester@schoolmeal.test'],
            [
                'full_name' => 'Chief Platform Architect',
                'phone' => '+85512000001',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $this->superAdmin->syncRoles(['super_admin']);

        $this->regularUser = User::firstOrCreate(
            ['email' => 'regular.tester@schoolmeal.test'],
            [
                'full_name' => 'Standard Platform User',
                'phone' => '+85512000002',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $this->regularUser->syncRoles(['parent']);

        SystemSetting::seedDefaults();
    }

    /**
     * Test that guests cannot access system settings and are redirected to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.system_settings.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that non-admin authenticated users are forbidden/redirected.
     */
    public function test_non_admin_is_redirected_with_error(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.system_settings.index'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * Test super admin can access system settings and sees all 6 tabs.
     */
    public function test_super_admin_can_access_system_settings_and_see_tabs(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.system_settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Platform System Settings');
        $response->assertSee('General &amp; Platform', false);
        $response->assertSee('Meal Ordering', false);
        $response->assertSee('Merchant &amp; Kitchen', false);
        $response->assertSee('Security &amp; Auth', false);
        $response->assertSee('Notifications', false);
        $response->assertSee('Cache &amp; Maintenance', false);
    }

    /**
     * Test querying specific tab renders properly with active state.
     */
    public function test_super_admin_can_switch_tabs_via_query_param(): void
    {
        $tabs = ['general', 'ordering', 'merchant', 'security', 'notifications', 'maintenance'];

        foreach ($tabs as $tab) {
            $response = $this->actingAs($this->superAdmin)->get(route('admin.system_settings.index', ['tab' => $tab]));
            $response->assertStatus(200);
            $response->assertViewHas('activeTab', $tab);
        }
    }

    /**
     * Test updating system settings persists to database and records an audit log.
     */
    public function test_super_admin_can_update_general_settings(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.system_settings.update'), [
            'active_tab' => 'general',
            'settings' => [
                'platform_name' => 'SchoolMeal National Portal',
                'support_email' => 'support@national.schoolmeal.test',
                'default_currency' => 'KHR',
                'exchange_rate_usd_khr' => '4150',
                'maintenance_mode' => '1',
            ],
        ]);

        $response->assertRedirect(route('admin.system_settings.index', ['tab' => 'general']));
        $response->assertSessionHas('success');

        // Verify values saved
        $this->assertEquals('SchoolMeal National Portal', SystemSetting::get('platform_name'));
        $this->assertEquals('support@national.schoolmeal.test', SystemSetting::get('support_email'));
        $this->assertEquals(4150, SystemSetting::get('exchange_rate_usd_khr'));
        $this->assertTrue(SystemSetting::get('maintenance_mode'));

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->superAdmin->id,
            'action' => 'system.settings.update.general',
        ]);
    }

    /**
     * Test boolean checkboxes that are unchecked (omitted from request) are normalized to '0'.
     */
    public function test_boolean_toggle_deselection_normalized_to_zero(): void
    {
        // First set maintenance mode to '1'
        SystemSetting::set('maintenance_mode', '1');
        $this->assertTrue(SystemSetting::get('maintenance_mode'));

        // Now post without 'maintenance_mode' in the payload for general tab
        $response = $this->actingAs($this->superAdmin)->post(route('admin.system_settings.update'), [
            'active_tab' => 'general',
            'settings' => [
                'platform_name' => 'SchoolMeal Portal Clean',
                // maintenance_mode omitted
            ],
        ]);

        $response->assertRedirect(route('admin.system_settings.index', ['tab' => 'general']));
        $this->assertFalse(SystemSetting::get('maintenance_mode'));
    }

    /**
     * Test updating ordering settings with business rules.
     */
    public function test_super_admin_can_update_ordering_settings(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.system_settings.update'), [
            'active_tab' => 'ordering',
            'settings' => [
                'order_cutoff_time' => '07:30',
                'daily_spending_cap_default' => '25000',
                'strict_allergy_check' => '1',
                'allow_weekend_ordering' => '0',
            ],
        ]);

        $response->assertRedirect(route('admin.system_settings.index', ['tab' => 'ordering']));
        $response->assertSessionHas('success');

        $this->assertEquals('07:30', SystemSetting::get('order_cutoff_time'));
        $this->assertEquals(25000, SystemSetting::get('daily_spending_cap_default'));
        $this->assertTrue(SystemSetting::get('strict_allergy_check'));
        $this->assertFalse(SystemSetting::get('allow_weekend_ordering'));

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->superAdmin->id,
            'action' => 'system.settings.update.ordering',
        ]);
    }

    /**
     * Test updating security policies.
     */
    public function test_super_admin_can_update_security_settings(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.system_settings.update'), [
            'active_tab' => 'security',
            'settings' => [
                'session_lifetime_minutes' => '180',
                'password_min_length' => '12',
                'enforce_2fa_for_staff' => '1',
                'max_login_attempts' => '3',
                'lockout_duration_minutes' => '30',
            ],
        ]);

        $response->assertRedirect(route('admin.system_settings.index', ['tab' => 'security']));
        $response->assertSessionHas('success');

        $this->assertEquals(180, SystemSetting::get('session_lifetime_minutes'));
        $this->assertEquals(12, SystemSetting::get('password_min_length'));
        $this->assertTrue(SystemSetting::get('enforce_2fa_for_staff'));
        $this->assertEquals(3, SystemSetting::get('max_login_attempts'));
        $this->assertEquals(30, SystemSetting::get('lockout_duration_minutes'));

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->superAdmin->id,
            'action' => 'system.settings.update.security',
            'auditable_type' => SystemSetting::class,
            'auditable_id' => SystemSetting::SYSTEM_UUID,
        ]);
    }

    /**
     * Test cache clear action executes successfully and creates audit log.
     */
    public function test_super_admin_can_clear_cache(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.system_settings.cache_clear'), [
            'cache_type' => 'all',
        ]);

        $response->assertRedirect(route('admin.system_settings.index', ['tab' => 'maintenance']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->superAdmin->id,
            'action' => 'system.cache.cleared',
            'auditable_type' => SystemSetting::class,
            'auditable_id' => SystemSetting::SYSTEM_UUID,
        ]);
    }

    /**
     * Test restoring baseline system parameters.
     */
    public function test_super_admin_can_reset_defaults(): void
    {
        // Mutate a setting
        SystemSetting::set('platform_name', 'Temporary Altered Name');
        $this->assertEquals('Temporary Altered Name', SystemSetting::get('platform_name'));

        // Reset
        $response = $this->actingAs($this->superAdmin)->post(route('admin.system_settings.reset_defaults'));

        $response->assertRedirect(route('admin.system_settings.index'));
        $response->assertSessionHas('success');

        // Check reset
        $this->assertEquals('SchoolMeal National E-Commerce', SystemSetting::get('platform_name'));

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->superAdmin->id,
            'action' => 'system.settings.reset_defaults',
            'auditable_type' => SystemSetting::class,
            'auditable_id' => SystemSetting::SYSTEM_UUID,
        ]);
    }

    /**
     * Test AGENTS.md rule: Toast notifications only, no inline alert banners.
     */
    public function test_no_inline_alert_banners_rendered_conforms_to_agents_rule(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.system_settings.index'));

        $content = $response->getContent();

        // Must include toast container
        $this->assertStringContainsString('toast-container', $content);

        // Must NOT contain static inline alert banner classes
        $this->assertStringNotContainsString('class="alert alert-', $content);
        $this->assertStringNotContainsString('class="alert alert-danger', $content);
        $this->assertStringNotContainsString('class="alert alert-success', $content);
    }

    /**
     * Test Khmer language support on system settings page.
     */
    public function test_khmer_localization_renders_properly(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->withSession(['locale' => 'km'])
            ->get(route('admin.system_settings.index'));

        $response->assertStatus(200);
        // Expect Khmer translated headings
        $response->assertSee('ការកំណត់ប្រព័ន្ធ');
    }
}
