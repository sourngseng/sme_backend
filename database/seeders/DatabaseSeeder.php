<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedRolesAndPermissions();
            $this->seedDefaultUsers();
        });

        $this->call(PlatformDemoSeeder::class);
    }

    /**
     * Seed all default roles and granular permissions across 14 functional domains.
     */
    protected function seedRolesAndPermissions(): void
    {
        // 1. Define Default Roles
        $rolesData = [
            'super_admin' => 'System Super Administrator with unrestricted access across all domains and settings.',
            'school_admin' => 'School Administrator managing campus shifts, locations, meal policies, and student delivery rosters.',
            'parent' => 'Parent or Student Guardian managing children, dietary profiles, orders, and payment checkouts.',
            'restaurant_owner' => 'Restaurant Merchant Owner managing menus, recipes, nutritional values, pricing versions, and kitchen operations.',
            'restaurant_staff' => 'Restaurant Kitchen or Operational Staff managing daily preparation queues and production batches.',
            'driver' => 'Delivery Driver managing order pickup from merchants, route stops, and proof-of-delivery handoff.',
            'finance' => 'Finance & Accounting Officer managing commission tiers, settlement payouts, and financial reconciliations.',
            'support' => 'Customer Care & Support Agent managing inquiries, dispute resolution, and audit reviews.',
            'marketing' => 'Marketing Specialist managing discount vouchers, seasonal promotions, and campaigns.',
        ];

        $roles = [];
        foreach ($rolesData as $roleName => $roleDesc) {
            $roles[$roleName] = Role::firstOrCreate(
                ['name' => $roleName],
                ['description' => $roleDesc]
            );
        }

        // 2. Define Granular Permissions across 14 Functional Domains
        $domains = [
            'users' => [
                'users.view' => 'View user accounts, contact details, and platform activity.',
                'users.create' => 'Create and onboard new user accounts.',
                'users.update' => 'Edit user profile information.',
                'users.delete' => 'Soft-delete or deactivate user accounts.',
                'users.status.update' => 'Change user account status (active, suspended, blocked).',
            ],
            'roles_permissions' => [
                'roles.view' => 'View system roles and permission matrices.',
                'roles.manage' => 'Create, modify, and delete roles.',
                'permissions.view' => 'View all registered system permissions.',
                'permissions.assign' => 'Assign and revoke permissions to roles.',
            ],
            'schools' => [
                'schools.view' => 'View school profiles and campus information.',
                'schools.create' => 'Onboard new partner schools.',
                'schools.update' => 'Edit school details, contact info, and campus boundaries.',
                'schools.delete' => 'Soft-delete schools.',
                'schools.shifts.manage' => 'Configure school operating shifts and meal delivery schedules.',
                'schools.locations.manage' => 'Configure school meal delivery counters and drop-off points.',
                'schools.policies.manage' => 'Configure school cutoff rules, meal types, and COD permissions.',
                'schools.roster.view' => 'View school daily delivery rosters and student meal distribution lists.',
            ],
            'students' => [
                'students.view' => 'View student roster and grade/class assignments.',
                'students.create' => 'Register new students in a school.',
                'students.update' => 'Update student details and class assignments.',
                'students.delete' => 'Soft-delete student records.',
                'guardians.link' => 'Link guardians to students with ordering and nutrition permissions.',
                'nutrition_profiles.view' => 'View student dietary restrictions, preferences, and allergens.',
                'nutrition_profiles.update' => 'Update student dietary preferences and declared allergens.',
            ],
            'restaurants' => [
                'restaurants.view' => 'View merchant restaurant profiles and ratings.',
                'restaurants.create' => 'Register new restaurant partner accounts.',
                'restaurants.update' => 'Update restaurant profiles, operating addresses, and geocoordinates.',
                'restaurants.delete' => 'Soft-delete restaurant partners.',
                'restaurants.approve' => 'Review and approve newly submitted restaurant accounts.',
                'restaurants.suspend' => 'Suspend or reactivate restaurant merchant accounts.',
                'restaurants.staff.manage' => 'Invite, assign, and manage restaurant kitchen and dispatch staff.',
            ],
            'catalog' => [
                'catalog.view' => 'Browse restaurant food menus and available meal options.',
                'menu_categories.manage' => 'Create, reorder, and edit menu categories.',
                'menu_items.create' => 'Add new food items to restaurant catalog.',
                'menu_items.update' => 'Edit food items and dietary attributes.',
                'menu_items.delete' => 'Soft-delete food items.',
                'menu_versions.create' => 'Publish immutable menu versions with price and portion revisions.',
                'menu_versions.publish' => 'Activate or retire specific menu item versions.',
            ],
            'nutrition' => [
                'nutrition.view' => 'Inspect meal nutritional facts, calories, macros, and sodium levels.',
                'nutrition.manage' => 'Create, edit, and certify laboratory or verified nutritional facts.',
                'allergens.view' => 'View system allergen master list.',
                'allergens.manage' => 'Create, edit, and classify allergen types and severity.',
            ],
            'orders' => [
                'orders.view' => 'View customer orders and scheduled delivery lines.',
                'orders.create' => 'Place new meal orders with 1-7 day future scheduling.',
                'orders.update' => 'Update delivery notes or order preferences prior to cutoff.',
                'orders.cancel' => 'Cancel an order within allowed server-side cutoff window.',
                'orders.schedules.view' => 'View daily meal delivery schedule lines.',
                'orders.schedules.skip' => 'Skip an individual scheduled day within cutoff window.',
                'orders.schedules.cancel' => 'Cancel an individual scheduled day within cutoff window.',
            ],
            'payments' => [
                'payments.view' => 'View payments, payment status, and transaction breakdown.',
                'payments.initiate' => 'Initiate payments via KHQR/Bakong, ABA PayWay, ACLEDA, or COD.',
                'payments.refund' => 'Process financial refunds for cancelled or undelivered meals.',
                'payments.webhooks.manage' => 'View and reprocess signed payment gateway webhooks.',
            ],
            'kitchen' => [
                'kitchen.queue.view' => 'View real-time meal preparation queues grouped by shift and menu item.',
                'kitchen.batches.manage' => 'Group scheduled meals into cooking batches.',
                'kitchen.status.update' => 'Update cooking status from preparing to ready.',
            ],
            'delivery' => [
                'delivery.batches.view' => 'View driver delivery dispatch batches.',
                'delivery.batches.manage' => 'Assign and dispatch delivery batches to drivers.',
                'delivery.stops.view' => 'View assigned sequential delivery stops for a shift.',
                'delivery.pickup' => 'Verify scan and confirm package pickup at restaurant kitchen.',
                'delivery.complete' => 'Complete delivery at school drop-off point with digital proof of delivery.',
                'delivery.exception' => 'Record delivery exceptions (e.g. refused, damaged, school closed).',
            ],
            'finance' => [
                'finance.commissions.view' => 'View platform commission percentages and custom merchant rules.',
                'finance.commissions.manage' => 'Configure platform and item-level commission rules.',
                'finance.settlements.view' => 'View merchant settlement payout statements.',
                'finance.settlements.generate' => 'Generate periodic merchant payout settlements.',
                'finance.settlements.approve' => 'Approve calculated settlement statements.',
                'finance.settlements.pay' => 'Record disbursement and mark settlements as paid.',
                'finance.expenses.manage' => 'Record and manage platform operational expenses.',
            ],
            'marketing' => [
                'marketing.campaigns.view' => 'View marketing and school promotional campaigns.',
                'marketing.campaigns.manage' => 'Create, schedule, and evaluate seasonal promotional campaigns.',
                'marketing.promotions.view' => 'View discount coupons, promo codes, and usage metrics.',
                'marketing.promotions.manage' => 'Create and configure promo codes and subsidy rules.',
            ],
            'support_feedback' => [
                'support.tickets.view' => 'View customer service and school support tickets.',
                'support.tickets.manage' => 'Assign, reply to, and resolve support tickets.',
                'feedback.view' => 'View parent meal ratings, taste scores, and feedback comments.',
                'consumption_logs.view' => 'View teacher/staff logs of actual student meal consumption.',
                'consumption_logs.record' => 'Record actual student consumption percentage at school.',
                'audit_logs.view' => 'Inspect immutable security, auth, and data mutation audit trails.',
                'reports.view' => 'View operational, financial, and nutritional analytics dashboards.',
                'reports.generate' => 'Generate and export scheduled platform reports.',
            ],
        ];

        $permissionsByDomain = [];
        $allPermissionIds = [];

        foreach ($domains as $domainKey => $perms) {
            foreach ($perms as $permName => $permDesc) {
                $permission = Permission::firstOrCreate(
                    ['name' => $permName],
                    ['description' => $permDesc]
                );
                $permissionsByDomain[$permName] = $permission->id;
                $allPermissionIds[] = $permission->id;
            }
        }

        // 3. Assign Granular Permissions to Roles

        // Super Admin gets ALL permissions
        $roles['super_admin']->permissions()->sync($allPermissionIds);

        // School Admin
        $schoolAdminPerms = [
            'schools.view', 'schools.update', 'schools.shifts.manage', 'schools.locations.manage',
            'schools.policies.manage', 'schools.roster.view',
            'students.view', 'nutrition_profiles.view',
            'orders.view', 'orders.schedules.view',
            'consumption_logs.view', 'consumption_logs.record',
            'reports.view',
        ];
        $this->syncRolePermissions($roles['school_admin'], $schoolAdminPerms, $permissionsByDomain);

        // Parent / Guardian
        $parentPerms = [
            'catalog.view', 'nutrition.view', 'allergens.view',
            'students.view', 'guardians.link', 'nutrition_profiles.view', 'nutrition_profiles.update',
            'orders.view', 'orders.create', 'orders.cancel',
            'orders.schedules.view', 'orders.schedules.skip', 'orders.schedules.cancel',
            'payments.view', 'payments.initiate',
            'feedback.view',
            'consumption_logs.view',
            'support.tickets.view', 'support.tickets.manage',
        ];
        $this->syncRolePermissions($roles['parent'], $parentPerms, $permissionsByDomain);

        // Restaurant Owner
        $restaurantOwnerPerms = [
            'restaurants.view', 'restaurants.update', 'restaurants.staff.manage',
            'catalog.view', 'menu_categories.manage', 'menu_items.create', 'menu_items.update',
            'menu_items.delete', 'menu_versions.create', 'menu_versions.publish',
            'nutrition.view', 'nutrition.manage', 'allergens.view',
            'kitchen.queue.view', 'kitchen.batches.manage', 'kitchen.status.update',
            'orders.view', 'orders.schedules.view',
            'finance.settlements.view',
            'feedback.view',
        ];
        $this->syncRolePermissions($roles['restaurant_owner'], $restaurantOwnerPerms, $permissionsByDomain);

        // Restaurant Kitchen Staff
        $restaurantStaffPerms = [
            'catalog.view', 'nutrition.view',
            'kitchen.queue.view', 'kitchen.status.update',
            'orders.schedules.view',
        ];
        $this->syncRolePermissions($roles['restaurant_staff'], $restaurantStaffPerms, $permissionsByDomain);

        // Driver
        $driverPerms = [
            'delivery.batches.view', 'delivery.stops.view',
            'delivery.pickup', 'delivery.complete', 'delivery.exception',
        ];
        $this->syncRolePermissions($roles['driver'], $driverPerms, $permissionsByDomain);

        // Finance Officer
        $financePerms = [
            'finance.commissions.view', 'finance.commissions.manage',
            'finance.settlements.view', 'finance.settlements.generate',
            'finance.settlements.approve', 'finance.settlements.pay',
            'finance.expenses.manage',
            'payments.view', 'payments.refund',
            'orders.view',
            'reports.view', 'reports.generate',
        ];
        $this->syncRolePermissions($roles['finance'], $financePerms, $permissionsByDomain);

        // Customer Support Agent
        $supportPerms = [
            'support.tickets.view', 'support.tickets.manage',
            'users.view', 'students.view',
            'orders.view', 'orders.schedules.view',
            'feedback.view',
            'audit_logs.view',
        ];
        $this->syncRolePermissions($roles['support'], $supportPerms, $permissionsByDomain);

        // Marketing Specialist
        $marketingPerms = [
            'marketing.campaigns.view', 'marketing.campaigns.manage',
            'marketing.promotions.view', 'marketing.promotions.manage',
            'catalog.view',
            'reports.view',
        ];
        $this->syncRolePermissions($roles['marketing'], $marketingPerms, $permissionsByDomain);
    }

    /**
     * Helper to map and sync permissions for a role.
     *
     * @param array<int, string> $permissionNames
     * @param array<string, string> $permissionMap
     */
    protected function syncRolePermissions(Role $role, array $permissionNames, array $permissionMap): void
    {
        $ids = collect($permissionNames)
            ->filter(fn ($name) => isset($permissionMap[$name]))
            ->map(fn ($name) => $permissionMap[$name])
            ->values()
            ->all();

        $role->permissions()->sync($ids);
    }

    /**
     * Seed baseline demonstration accounts for system operations and automated testing.
     */
    protected function seedDefaultUsers(): void
    {
        $defaultPassword = Hash::make('Password@123');

        $users = [
            [
                'full_name' => 'Super Administrator',
                'email' => 'admin@schoolmeal.test',
                'phone' => '+85512000001',
                'password_hash' => $defaultPassword,
                'status' => 'active',
                'role' => 'super_admin',
            ],
            [
                'full_name' => 'Phnom Penh Academy Admin',
                'email' => 'school@schoolmeal.test',
                'phone' => '+85512000002',
                'password_hash' => $defaultPassword,
                'status' => 'active',
                'role' => 'school_admin',
            ],
            [
                'full_name' => 'Sophea Sok (Parent)',
                'email' => 'parent@schoolmeal.test',
                'phone' => '+85512000003',
                'password_hash' => $defaultPassword,
                'status' => 'active',
                'role' => 'parent',
            ],
            [
                'full_name' => 'Green Healthy Kitchen Owner',
                'email' => 'merchant@schoolmeal.test',
                'phone' => '+85512000004',
                'password_hash' => $defaultPassword,
                'status' => 'active',
                'role' => 'restaurant_owner',
            ],
            [
                'full_name' => 'Borey Chan (Dispatch Driver)',
                'email' => 'driver@schoolmeal.test',
                'phone' => '+85512000005',
                'password_hash' => $defaultPassword,
                'status' => 'active',
                'role' => 'driver',
            ],
            [
                'full_name' => 'Chenda Pich (Finance Lead)',
                'email' => 'finance@schoolmeal.test',
                'phone' => '+85512000006',
                'password_hash' => $defaultPassword,
                'status' => 'active',
                'role' => 'finance',
            ],
            [
                'full_name' => 'Kosal Ly (Customer Support)',
                'email' => 'support@schoolmeal.test',
                'phone' => '+85512000007',
                'password_hash' => $defaultPassword,
                'status' => 'active',
                'role' => 'support',
            ],
            [
                'full_name' => 'Dina Meas (Marketing Manager)',
                'email' => 'marketing@schoolmeal.test',
                'phone' => '+85512000008',
                'password_hash' => $defaultPassword,
                'status' => 'active',
                'role' => 'marketing',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->assignRole($roleName);
        }
    }
}
