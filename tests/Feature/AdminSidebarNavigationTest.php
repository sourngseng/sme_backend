<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['display_name' => 'Super Administrator', 'description' => 'Full administrative clearance']
        );

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin.tester@schoolmeal.test',
            'status' => 'active',
        ]);
        $this->superAdmin->roles()->attach($role->id);
    }

    public function test_super_admin_left_sidebar_renders_admin_routes_only(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/dashboard');

        $response->assertStatus(200);

        // Verify the left sidebar links point to localhost:8000/admin/... endpoints
        $response->assertSee('/admin/users');
        $response->assertSee('/admin/roles');
        $response->assertSee('/admin/administrators');
        $response->assertSee('/admin/schools');
        $response->assertSee('/admin/students');
        $response->assertSee('/admin/restaurants');
        $response->assertSee('/admin/menu');
        $response->assertSee('/admin/meal-plans');
        $response->assertSee('/admin/nutrition');
        $response->assertSee('/admin/orders');
        $response->assertSee('/admin/kitchen-queue');
        $response->assertSee('/admin/order-analytics');
        $response->assertSee('/admin/payments');
        $response->assertSee('/admin/drivers');
        $response->assertSee('/admin/deliveries');
        $response->assertSee('/admin/tracking');
        $response->assertSee('/admin/commissions');
        $response->assertSee('/admin/settlements');
        $response->assertSee('/admin/financial-reports');
        $response->assertSee('/admin/marketing');
        $response->assertSee('/admin/vouchers');
        $response->assertSee('/admin/promotions');
        $response->assertSee('/admin/notifications');
        $response->assertSee('/admin/support');
        $response->assertSee('/admin/analytics');
        $response->assertSee('/admin/reports');
        $response->assertSee('/admin/profile');
        $response->assertSee('/admin/languages');
        $response->assertSee('/admin/system-settings');
        $response->assertSee('/admin/api-tester');
        $response->assertSee('/admin/audit-logs');
        $response->assertSee('/admin/system-health');
    }

    public function test_super_admin_can_access_all_left_sidebar_sections_with_200_ok(): void
    {
        $sections = [
            'users',
            'roles',
            'administrators',
            'schools',
            'students',
            'restaurants',
            'menu',
            'meal-plans',
            'nutrition',
            'orders',
            'kitchen-queue',
            'order-analytics',
            'payments',
            'drivers',
            'deliveries',
            'tracking',
            'commissions',
            'settlements',
            'financial-reports',
            'marketing',
            'vouchers',
            'promotions',
            'notifications',
            'support',
            'analytics',
            'reports',
            'audit-logs',
            'system-health',
        ];

        foreach ($sections as $section) {
            $response = $this->actingAs($this->superAdmin)->get("/admin/{$section}");

            $response->assertStatus(200);
            $response->assertSee('SchoolMeal');
            $response->assertSee('toastContainer'); // Strict Toast Container
            $response->assertDontSee('alert alert-danger'); // No inline error banners
            $response->assertDontSee('alert alert-success'); // No inline success banners
        }
    }
}
