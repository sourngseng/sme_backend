<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Restaurant;
use App\Models\RestaurantStaff;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kitchenUser;
    protected User $restaurantUser;
    protected User $superAdminUser;
    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles
        $kitchenRole = Role::firstOrCreate(['name' => 'restaurant_staff'], ['description' => 'Kitchen Operations']);
        $ownerRole = Role::firstOrCreate(['name' => 'restaurant_owner'], ['description' => 'Restaurant Merchant Owner']);
        $adminRole = Role::firstOrCreate(['name' => 'super_admin'], ['description' => 'System Root Super Admin']);

        // Super Admin User
        $this->superAdminUser = User::firstOrCreate(
            ['email' => 'admin.tester@schoolmeal.test'],
            [
                'full_name' => 'SENG Sourng (Platform Architect)',
                'phone' => '+85512888001',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $this->superAdminUser->syncRoles(['super_admin']);

        // Restaurant Owner User
        $this->restaurantUser = User::firstOrCreate(
            ['email' => 'merchant.tester@schoolmeal.test'],
            [
                'full_name' => 'Khmer Kitchen Admin',
                'phone' => '+85512777002',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $this->restaurantUser->syncRoles(['restaurant_owner']);

        // Restaurant
        $this->restaurant = Restaurant::firstOrCreate(
            ['code' => 'REST-TEST-01'],
            [
                'name' => 'Green Garden Organic Kitchen',
                'owner_user_id' => $this->restaurantUser->id,
                'phone' => '+85512999001',
                'email' => 'kitchen@greengarden.test',
                'status' => 'active',
                'commission_default_pct' => 12.50,
                'address' => 'No. 45 Street 310, Chamkarmon, Phnom Penh',
                'latitude' => 11.5451,
                'longitude' => 104.9185,
            ]
        );

        // Kitchen User
        $this->kitchenUser = User::firstOrCreate(
            ['email' => 'kitchen.tester@schoolmeal.test'],
            [
                'full_name' => 'Sokha Ngin (Lead Chef)',
                'phone' => '+85512777001',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $this->kitchenUser->syncRoles(['restaurant_staff']);

        RestaurantStaff::firstOrCreate(
            ['restaurant_id' => $this->restaurant->id, 'user_id' => $this->kitchenUser->id],
            ['job_title' => 'Head Prep & Line Chef', 'status' => 'active']
        );
    }

    /**
     * Unauthenticated users are redirected to login.
     */
    public function test_unauthenticated_user_cannot_access_profile(): void
    {
        $response = $this->get('/kitchen/profile');
        $response->assertRedirect('/login');

        $response2 = $this->get('/restaurant/profile');
        $response2->assertRedirect('/login');
    }

    /**
     * Kitchen Staff can view own profile overview information.
     */
    public function test_kitchen_staff_can_view_own_profile(): void
    {
        $response = $this->actingAs($this->kitchenUser)->get('/kitchen/profile');

        $response->assertStatus(200);
        $response->assertSee('Sokha Ngin (Lead Chef)');
        $response->assertSee('Kitchen Staff');
        $response->assertSee('Green Garden Organic Kitchen');
        $response->assertSee('My Profile');
        $response->assertSee('Account Settings');
        $response->assertSee('Security & Sessions');
        $response->assertSee('Active Duty');
        $response->assertSee('Staff Identification');
        $response->assertSee('Assigned Kitchen & Facility');
    }

    /**
     * Kitchen Staff can directly access settings and security tabs.
     */
    public function test_kitchen_staff_can_access_settings_and_security_tabs(): void
    {
        $settingsResponse = $this->actingAs($this->kitchenUser)->get('/kitchen/profile/settings');
        $settingsResponse->assertStatus(200);
        $settingsResponse->assertSee('Personal & Contact Profile');
        $settingsResponse->assertSee('Profile Picture');
        $settingsResponse->assertSee('Work Email Address');

        $securityResponse = $this->actingAs($this->kitchenUser)->get('/kitchen/profile/security');
        $securityResponse->assertStatus(200);
        $securityResponse->assertSee('Change Password');
        $securityResponse->assertSee('Active Devices & Sessions');
        $securityResponse->assertSee('Two-Factor Authentication');
    }

    /**
     * Kitchen staff can update their personal profile information.
     */
    public function test_kitchen_staff_can_update_profile_information(): void
    {
        $response = $this->actingAs($this->kitchenUser)->post('/kitchen/profile', [
            'full_name' => 'Sokha Ngin Updated',
            'email' => 'sokha.updated@schoolmeal.test',
            'phone' => '+85512888999',
            'bio' => 'Certified Master Line Chef with HACCP excellence certificate.',
            'notify_sound' => '1',
            'notify_alerts' => '1',
        ]);

        $response->assertRedirect(route('kitchen.profile.settings'));
        $response->assertSessionHas('success');

        $this->kitchenUser->refresh();
        $this->assertEquals('Sokha Ngin Updated', $this->kitchenUser->full_name);
        $this->assertEquals('sokha.updated@schoolmeal.test', $this->kitchenUser->email);
        $this->assertEquals('+85512888999', $this->kitchenUser->phone);
        $this->assertEquals('Certified Master Line Chef with HACCP excellence certificate.', $this->kitchenUser->bio);
        $this->assertTrue($this->kitchenUser->notification_preferences['sound']);

        // Verify audit log creation
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->kitchenUser->id,
            'action' => 'user.profile.update',
        ]);
    }

    /**
     * Kitchen staff can change password securely.
     */
    public function test_kitchen_staff_can_change_password(): void
    {
        $response = $this->actingAs($this->kitchenUser)->post('/kitchen/profile/password', [
            'current_password' => 'Password@123',
            'password' => 'NewSecur3Pass!',
            'password_confirmation' => 'NewSecur3Pass!',
        ]);

        $response->assertRedirect(route('kitchen.profile.security'));
        $response->assertSessionHas('success');

        $this->kitchenUser->refresh();
        $this->assertTrue(Hash::check('NewSecur3Pass!', $this->kitchenUser->password_hash));

        // Verify audit log creation
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->kitchenUser->id,
            'action' => 'user.security.password_change',
        ]);
    }

    /**
     * Password update fails with wrong current password.
     */
    public function test_password_change_fails_with_invalid_current_password(): void
    {
        $response = $this->actingAs($this->kitchenUser)->post('/kitchen/profile/password', [
            'current_password' => 'WrongPassword',
            'password' => 'NewSecur3Pass!',
            'password_confirmation' => 'NewSecur3Pass!',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    /**
     * Restaurant owner can view and update business profile.
     */
    public function test_restaurant_owner_can_view_and_update_restaurant_profile(): void
    {
        $viewResponse = $this->actingAs($this->restaurantUser)->get('/restaurant/profile');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Khmer Kitchen Admin');
        $viewResponse->assertSee('Restaurant Owner');
        $viewResponse->assertSee('Green Garden Organic Kitchen');
        $viewResponse->assertSee('Verified Merchant');

        $updateResponse = $this->actingAs($this->restaurantUser)->post('/restaurant/profile', [
            'full_name' => 'Khmer Kitchen Managing Director',
            'email' => 'merchant.updated@schoolmeal.test',
            'phone' => '+85512999888',
            'restaurant_name' => 'Khmer Royal Kitchen & Bakery',
            'restaurant_phone' => '+85512999777',
            'restaurant_address' => 'No. 88 Blvd Mao Tse Toung, Phnom Penh',
        ]);

        $updateResponse->assertRedirect(route('restaurant.profile.settings'));
        $updateResponse->assertSessionHas('success');

        $this->restaurantUser->refresh();
        $this->assertEquals('Khmer Kitchen Managing Director', $this->restaurantUser->full_name);
        $this->assertEquals('merchant.updated@schoolmeal.test', $this->restaurantUser->email);

        $this->restaurant->refresh();
        $this->assertEquals('Khmer Royal Kitchen & Bakery', $this->restaurant->name);
        $this->assertEquals('No. 88 Blvd Mao Tse Toung, Phnom Penh', $this->restaurant->address);
    }

    /**
     * User can manage and revoke other active sessions.
     */
    public function test_user_can_revoke_other_active_sessions(): void
    {
        // Insert a dummy session for the user
        $dummySessionId = 'test_session_' . uniqid();
        DB::table('sessions')->insert([
            'id' => $dummySessionId,
            'user_id' => $this->kitchenUser->id,
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
            'payload' => base64_encode('dummy'),
            'last_activity' => time() - 3600,
        ]);

        $securityResponse = $this->actingAs($this->kitchenUser)->get('/kitchen/profile/security');
        $securityResponse->assertStatus(200);
        $securityResponse->assertSee('192.168.1.100');
        $securityResponse->assertSee('Apple Safari');

        // Revoke the dummy session
        $deleteResponse = $this->actingAs($this->kitchenUser)->delete("/kitchen/profile/sessions/{$dummySessionId}");
        $deleteResponse->assertRedirect(route('kitchen.profile.security'));
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('sessions', ['id' => $dummySessionId]);
    }

    /**
     * Bulk logout of all other sessions.
     */
    public function test_user_can_logout_all_other_sessions(): void
    {
        $dummySession1 = 'test_sess_1_' . uniqid();
        $dummySession2 = 'test_sess_2_' . uniqid();

        DB::table('sessions')->insert([
            [
                'id' => $dummySession1,
                'user_id' => $this->kitchenUser->id,
                'ip_address' => '10.0.0.1',
                'user_agent' => 'Mozilla/5.0 Chrome/120.0 Windows',
                'payload' => base64_encode('dummy1'),
                'last_activity' => time() - 1800,
            ],
            [
                'id' => $dummySession2,
                'user_id' => $this->kitchenUser->id,
                'ip_address' => '10.0.0.2',
                'user_agent' => 'Mozilla/5.0 Firefox/121.0 Linux',
                'payload' => base64_encode('dummy2'),
                'last_activity' => time() - 7200,
            ],
        ]);

        $response = $this->actingAs($this->kitchenUser)->post('/kitchen/profile/sessions/logout-other');
        $response->assertRedirect(route('kitchen.profile.security'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('sessions', ['id' => $dummySession1]);
        $this->assertDatabaseMissing('sessions', ['id' => $dummySession2]);
    }

    /**
     * Topbar includes working links to My Profile, Account Settings, and Security & Sessions.
     */
    public function test_topbar_renders_working_profile_and_security_links(): void
    {
        $response = $this->actingAs($this->kitchenUser)->get('/kitchen/dashboard');
        $response->assertStatus(200);
        $response->assertSee(route('kitchen.profile'), false);
        $response->assertSee(route('kitchen.profile.settings'), false);
        $response->assertSee(route('kitchen.profile.security'), false);

        $ownerResponse = $this->actingAs($this->restaurantUser)->get('/restaurant/dashboard');
        $ownerResponse->assertStatus(200);
        $ownerResponse->assertSee(route('restaurant.profile'), false);
        $ownerResponse->assertSee(route('restaurant.profile.settings'), false);
        $ownerResponse->assertSee(route('restaurant.profile.security'), false);

        $adminResponse = $this->actingAs($this->superAdminUser)->get('/admin/dashboard');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee(route('admin.profile'), false);
        $adminResponse->assertSee(route('admin.profile.settings'), false);
        $adminResponse->assertSee(route('admin.profile.security'), false);
    }

    /**
     * General /profile route redirects to role-specific profile.
     */
    public function test_profile_dispatcher_redirects_by_role(): void
    {
        $response = $this->actingAs($this->kitchenUser)->get('/profile');
        $response->assertRedirect(route('kitchen.profile'));

        $response2 = $this->actingAs($this->restaurantUser)->get('/profile');
        $response2->assertRedirect(route('restaurant.profile'));

        $response3 = $this->actingAs($this->superAdminUser)->get('/profile');
        $response3->assertRedirect(route('admin.profile'));
    }

    /**
     * AGENTS.md Compliance: No inline alert banners on profile pages; x-toast-container present.
     */
    public function test_profile_strictly_complies_with_toast_standards_and_no_inline_alerts(): void
    {
        $response = $this->actingAs($this->kitchenUser)->get('/kitchen/profile');
        $response->assertStatus(200);
        $response->assertSee('toast-container', false);
        $response->assertDontSee('alert-danger');
        $response->assertDontSee('alert-success');
        $response->assertDontSee('alert-warning');
    }

    /**
     * Kitchen Profile Settings translates all layout elements when switching language.
     */
    public function test_kitchen_profile_translates_all_layout_elements_when_switching_language(): void
    {
        // 1. In English
        $enResponse = $this->actingAs($this->kitchenUser)->withSession(['locale' => 'en'])->get('/kitchen/profile/settings');
        $enResponse->assertStatus(200);
        $enResponse->assertSee('Account Settings');
        $enResponse->assertSee('My Profile');
        $enResponse->assertSee('Security & Sessions');
        $enResponse->assertSee('Personal & Contact Profile');
        $enResponse->assertSee('Full Legal Name');
        $enResponse->assertSee('Work Email Address');
        $enResponse->assertSee('Contact Mobile Phone');
        $enResponse->assertSee('Preferred Operations Language');
        $enResponse->assertSee('Kitchen Operational Notifications');
        $enResponse->assertSee('Audio Chime on New Orders');
        $enResponse->assertSee('Preparation Delay Warnings');
        $enResponse->assertSee('Special Dietary & Allergen Flashes');
        $enResponse->assertSee('Save Changes');
        $enResponse->assertSee('Reset');

        // 2. In Khmer
        $kmResponse = $this->actingAs($this->kitchenUser)->withSession(['locale' => 'km'])->get('/kitchen/profile/settings');
        $kmResponse->assertStatus(200);
        $kmResponse->assertSee('ការកំណត់គណនី'); // Account Settings
        $kmResponse->assertSee('គណនីរបស់ខ្ញុំ'); // My Profile
        $kmResponse->assertSee('សុវត្ថិភាព និងសម័យប្រជុំ'); // Security & Sessions
        $kmResponse->assertSee('ព័ត៌មានផ្ទាល់ខ្លួន និងទំនាក់ទំនង'); // Personal & Contact Profile
        $kmResponse->assertSee('ឈ្មោះពេញផ្លូវការ'); // Full Legal Name
        $kmResponse->assertSee('អាសយដ្ឋានអ៊ីមែលការងារ'); // Work Email Address
        $kmResponse->assertSee('លេខទូរស័ព្ទទាក់ទង'); // Contact Mobile Phone
        $kmResponse->assertSee('ភាសាប្រតិបត្តិការដែលពេញចិត្ត'); // Preferred Operations Language
        $kmResponse->assertSee('ការជូនដំណឹងប្រតិបត្តិការផ្ទះបាយ'); // Kitchen Operational Notifications
        $kmResponse->assertSee('សំឡេងកណ្ដឹងពេលមានការបញ្ជាទិញថ្មី'); // Audio Chime on New Orders
        $kmResponse->assertSee('ការព្រមានការពន្យារពេលរៀបចំ'); // Preparation Delay Warnings
        $kmResponse->assertSee('ការជូនដំណឹងអំពីអាហាររូបត្ថម្ភ និងអាឡែរហ្ស៊ីពិសេស'); // Special Dietary & Allergen Flashes
        $kmResponse->assertSee('រក្សាទុកការផ្លាស់ប្តូរ'); // Save Changes
        $kmResponse->assertSee('កំណត់ឡើងវិញ'); // Reset
        $kmResponse->assertSee('បុគ្គលិកផ្ទះបាយ'); // Kitchen Staff role in Topbar/Header

        // 3. Security tab in Khmer
        $kmSecResponse = $this->actingAs($this->kitchenUser)->withSession(['locale' => 'km'])->get('/kitchen/profile/security');
        $kmSecResponse->assertStatus(200);
        $kmSecResponse->assertSee('ផ្លាស់ប្តូរពាក្យសម្ងាត់'); // Change Password
        $kmSecResponse->assertSee('ពាក្យសម្ងាត់បច្ចុប្បន្ន'); // Current Password
        $kmSecResponse->assertSee('ពាក្យសម្ងាត់ថ្មី'); // New Password
        $kmSecResponse->assertSee('បញ្ជាក់ពាក្យសម្ងាត់ថ្មី'); // Confirm New Password
        $kmSecResponse->assertSee('ការផ្ទៀងផ្ទាត់ពីរកត្តា (2FA)'); // Two-Factor Authentication
        $kmSecResponse->assertSee('ឧបករណ៍ និងសម័យប្រជុំសកម្ម'); // Active Devices & Sessions
        $kmSecResponse->assertSee('ឧបករណ៍នេះ'); // This Device
    }

    /**
     * Restaurant Profile Settings translates all layout elements when switching language.
     */
    public function test_restaurant_profile_translates_all_layout_elements_when_switching_language(): void
    {
        // 1. In English
        $enResponse = $this->actingAs($this->restaurantUser)->withSession(['locale' => 'en'])->get('/restaurant/profile/settings');
        $enResponse->assertStatus(200);
        $enResponse->assertSee('Account Settings');
        $enResponse->assertSee('Personal & Contact Profile');
        $enResponse->assertSee('Merchant Principal Full Name');
        $enResponse->assertSee('Restaurant Business Entity');
        $enResponse->assertSee('Official Restaurant Brand Name');
        $enResponse->assertSee('Kitchen Hotline Number');
        $enResponse->assertSee('Business / Invoicing Email');
        $enResponse->assertSee('Physical Kitchen Operating Address');
        $enResponse->assertSee('Save Changes');

        // 2. In Khmer
        $kmResponse = $this->actingAs($this->restaurantUser)->withSession(['locale' => 'km'])->get('/restaurant/profile/settings');
        $kmResponse->assertStatus(200);
        $kmResponse->assertSee('ការកំណត់គណនី');
        $kmResponse->assertSee('ឈ្មោះពេញម្ចាស់អាជីវកម្មចម្បង');
        $kmResponse->assertSee('នីតិបុគ្គលអាជីវកម្មភោជនីយដ្ឋាន');
        $kmResponse->assertSee('ឈ្មោះម៉ាកយីហោភោជនីយដ្ឋានផ្លូវការ');
        $kmResponse->assertSee('លេខទូរស័ព្ទទាន់ហេតុការណ៍ផ្ទះបាយ');
        $kmResponse->assertSee('អ៊ីមែលអាជីវកម្ម / វិក្កយបត្រ');
        $kmResponse->assertSee('អាសយដ្ឋានប្រតិបត្តិការផ្ទះបាយជាក់ស្តែង');
        $kmResponse->assertSee('រក្សាទុកការផ្លាស់ប្តូរ');
        $kmResponse->assertSee('ម្ចាស់ភោជនីយដ្ឋាន'); // Restaurant Owner in topbar
    }

    /**
     * Super Admin can view own profile overview with root governance matrix.
     */
    public function test_super_admin_can_view_own_profile(): void
    {
        $response = $this->actingAs($this->superAdminUser)->get('/admin/profile');

        $response->assertStatus(200);
        $response->assertSee('SENG Sourng (Platform Architect)');
        $response->assertSee('Super Admin');
        $response->assertSee('Level 0 - Master Root Clearance');
        $response->assertSee('Platform Core Infrastructure');
        $response->assertSee('Registered Schools');
        $response->assertSee('Partner Restaurants');
        $response->assertSee('Total Food Orders');
        $response->assertSee('Master Administrative Privileges');
    }

    /**
     * Super Admin can access /admin/profile, /admin/profile/setting, /admin/profile/settings, and /admin/profile/security.
     */
    public function test_super_admin_can_access_setting_and_settings_and_security_urls(): void
    {
        // 1. Settings tab via /admin/profile/setting
        $settingResponse = $this->actingAs($this->superAdminUser)->get('/admin/profile/setting');
        $settingResponse->assertStatus(200);
        $settingResponse->assertViewIs('profile.admin');
        $settingResponse->assertViewHas('activeTab', 'settings');
        $settingResponse->assertSee('Personal & Contact Profile');

        // 2. Settings tab via /admin/profile/settings
        $settingsResponse = $this->actingAs($this->superAdminUser)->get('/admin/profile/settings');
        $settingsResponse->assertStatus(200);
        $settingsResponse->assertViewIs('profile.admin');
        $settingsResponse->assertViewHas('activeTab', 'settings');

        // 3. Security tab via /admin/profile/security
        $secResponse = $this->actingAs($this->superAdminUser)->get('/admin/profile/security');
        $secResponse->assertStatus(200);
        $secResponse->assertViewIs('profile.admin');
        $secResponse->assertViewHas('activeTab', 'security');
        $secResponse->assertSee('Active Devices & Sessions');

        // 4. Double slash alias tolerance /admin//profile/security
        $doubleSlashResponse = $this->actingAs($this->superAdminUser)->get('/admin//profile/security');
        $doubleSlashResponse->assertStatus(200);
        $doubleSlashResponse->assertViewIs('profile.admin');
        $doubleSlashResponse->assertViewHas('activeTab', 'security');
    }

    /**
     * Super Admin can update profile information and is redirected to /admin/profile/settings.
     */
    public function test_super_admin_can_update_profile_and_redirects_to_settings(): void
    {
        $response = $this->actingAs($this->superAdminUser)->post('/admin/profile', [
            'full_name' => 'SENG Sourng Senior Architect',
            'email' => 'admin.updated@schoolmeal.test',
            'phone' => '+85512888999',
            'bio' => 'Lead Enterprise Infrastructure & School Nutrition Administrator',
            'notify_orders' => '1',
            'notify_alerts' => '1',
        ]);

        $response->assertRedirect(route('admin.profile.settings'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->superAdminUser->id,
            'full_name' => 'SENG Sourng Senior Architect',
            'email' => 'admin.updated@schoolmeal.test',
            'phone' => '+85512888999',
        ]);
    }

    /**
     * Super Admin can update password and is redirected to /admin/profile/security.
     */
    public function test_super_admin_can_update_password_and_redirects_to_security(): void
    {
        $response = $this->actingAs($this->superAdminUser)->post('/admin/profile/password', [
            'current_password' => 'Password@123',
            'password' => 'NewSecuredPass#2026',
            'password_confirmation' => 'NewSecuredPass#2026',
        ]);

        $response->assertRedirect(route('admin.profile.security'));
        $response->assertSessionHas('success');

        $this->superAdminUser->refresh();
        $this->assertTrue(Hash::check('NewSecuredPass#2026', $this->superAdminUser->password_hash));
    }

    /**
     * Super Admin Profile translates all layout elements when switching language.
     */
    public function test_super_admin_profile_translates_all_layout_elements_when_switching_language(): void
    {
        // 1. In English
        $enResponse = $this->actingAs($this->superAdminUser)->withSession(['locale' => 'en'])->get('/admin/profile');
        $enResponse->assertStatus(200);
        $enResponse->assertSee('Super Admin');
        $enResponse->assertSee('Root Active');
        $enResponse->assertSee('Level 0 - Master Root Clearance');
        $enResponse->assertSee('Platform Core Infrastructure');
        $enResponse->assertSee('Registered Schools');
        $enResponse->assertSee('Partner Restaurants');
        $enResponse->assertSee('Total Food Orders');

        // 2. In Khmer
        $kmResponse = $this->actingAs($this->superAdminUser)->withSession(['locale' => 'km'])->get('/admin/profile');
        $kmResponse->assertStatus(200);
        $kmResponse->assertSee('Root សកម្មភាពពេញលេញ');
        $kmResponse->assertSee('កម្រិត ០ - សិទ្ធិ Master Root កំពូល');
        $kmResponse->assertSee('ហេដ្ឋារចនាសម្ព័ន្ធស្នូលនៃវេទិកា');
        $kmResponse->assertSee('សាលារៀនបានចុះឈ្មោះ');
        $kmResponse->assertSee('ភោជនីយដ្ឋានដៃគូ');
        $kmResponse->assertSee('ការបញ្ជាទិញអាហារសរុប');
    }

    /**
     * Super Admin Profile complies with toast notification standards and no inline alerts.
     */
    public function test_super_admin_profile_strictly_complies_with_toast_standards_and_no_inline_alerts(): void
    {
        $response = $this->actingAs($this->superAdminUser)->get('/admin/profile');
        $response->assertStatus(200);
        $response->assertSee('toast-container', false);
        $response->assertDontSee('alert-danger');
        $response->assertDontSee('alert-success');
        $response->assertDontSee('alert-warning');
    }
}

