<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class ApiTesterController extends Controller
{
    /**
     * Display the Postman-style REST API Manager & Interactive Tester console.
     */
    public function index(Request $request): View
    {
        $categories = $this->getApiCollections();
        $personas = $this->getDemoPersonas();

        return view('admin.api_tester.index', [
            'categories' => $categories,
            'personas' => $personas,
            'totalEndpoints' => collect($categories)->sum(fn ($c) => count($c['endpoints'])),
        ]);
    }

    /**
     * Quick token generator for testing role-based access control.
     */
    public function generateToken(Request $request): JsonResponse
    {
        $role = $request->input('role', 'super_admin');
        
        $roleEmailMap = [
            'super_admin' => 'admin@schoolmeal.test',
            'school_admin' => 'school@schoolmeal.test',
            'parent' => 'parent@schoolmeal.test',
            'restaurant_owner' => 'merchant@schoolmeal.test',
            'restaurant_staff' => 'kitchen@schoolmeal.test',
            'driver' => 'driver@schoolmeal.test',
            'finance' => 'finance@schoolmeal.test',
        ];

        $email = $roleEmailMap[$role] ?? 'admin@schoolmeal.test';
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'full_name' => ucfirst(str_replace('_', ' ', $role)) . ' (Tester)',
                    'phone' => '+85512' . rand(100000, 999999),
                    'password' => 'Password@123',
                    'status' => 'active',
                ]
            );
            $user->assignRole($role);
        }

        // Revoke prior tester tokens and create a fresh one
        $user->tokens()->where('name', 'api-tester-token')->delete();
        $token = $user->createToken('api-tester-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => "Active Bearer token generated for role [{$role}]",
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $role,
            ],
        ]);
    }

    /**
     * Get the full curated catalog of all platform REST APIs.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getApiCollections(): array
    {
        return [
            [
                'id' => 'auth',
                'name' => '1. Identity, Auth & Security',
                'description' => 'User registration, dual-channel credentials, OTP verification, and session tokens (PB-001 to PB-006).',
                'icon' => 'shield-check',
                'endpoints' => [
                    [
                        'id' => 'auth_register',
                        'name' => 'Register New User',
                        'method' => 'POST',
                        'path' => '/api/v1/auth/register',
                        'description' => 'PB-001: Register parent/user with email, phone, and optional role.',
                        'requires_auth' => false,
                        'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
                        'params' => [],
                        'body' => json_encode([
                            'full_name' => 'Sophea Tester',
                            'email' => 'sophea.tester@example.com',
                            'phone' => '+85512345678',
                            'password' => 'SecretPass@2026',
                            'role' => 'parent',
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ],
                    [
                        'id' => 'auth_login',
                        'name' => 'Login (Email / Phone)',
                        'method' => 'POST',
                        'path' => '/api/v1/auth/login',
                        'description' => 'PB-002: Authenticate using either email or phone with password.',
                        'requires_auth' => false,
                        'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
                        'params' => [],
                        'body' => json_encode([
                            'login' => 'admin@schoolmeal.test',
                            'password' => 'Password@123',
                            'device_name' => 'Postman Studio',
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ],
                    [
                        'id' => 'auth_otp_request',
                        'name' => 'Request Mobile OTP',
                        'method' => 'POST',
                        'path' => '/api/v1/auth/otp/request',
                        'description' => 'PB-003: Request 6-digit OTP code with strict Redis rate limiting (max 3/10 min).',
                        'requires_auth' => false,
                        'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
                        'params' => [],
                        'body' => json_encode([
                            'phone_or_email' => '+85512999888',
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ],
                    [
                        'id' => 'auth_otp_verify',
                        'name' => 'Verify OTP & Issue Token',
                        'method' => 'POST',
                        'path' => '/api/v1/auth/otp/verify',
                        'description' => 'PB-003: Verify OTP code and automatically issue Sanctum session token.',
                        'requires_auth' => false,
                        'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
                        'params' => [],
                        'body' => json_encode([
                            'phone_or_email' => '+85512999888',
                            'otp' => '123456',
                            'full_name' => 'OTP Verified User',
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ],
                    [
                        'id' => 'auth_me',
                        'name' => 'Get Current Authenticated Profile',
                        'method' => 'GET',
                        'path' => '/api/v1/auth/me',
                        'description' => 'PB-004: Retrieve current user profile, roles, and granular permissions.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => '',
                    ],
                    [
                        'id' => 'auth_logout',
                        'name' => 'Logout & Revoke Token',
                        'method' => 'POST',
                        'path' => '/api/v1/auth/logout',
                        'description' => 'PB-004: Invalidate current personal access token.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => '',
                    ],
                ],
            ],
            [
                'id' => 'admin_users',
                'name' => '2. User & RBAC Management',
                'description' => 'Platform administration, role assignments, and audit logging (PB-006, PB-079).',
                'icon' => 'users',
                'endpoints' => [
                    [
                        'id' => 'users_list',
                        'name' => 'List Platform Users',
                        'method' => 'GET',
                        'path' => '/api/v1/admin/users',
                        'description' => 'PB-006: Paginated list of users with search, role, and status filters.',
                        'requires_auth' => true,
                        'required_permission' => 'users.view',
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => ['search' => '', 'role' => '', 'status' => 'active', 'per_page' => '15'],
                        'body' => '',
                    ],
                    [
                        'id' => 'users_create',
                        'name' => 'Create Administrator / Staff',
                        'method' => 'POST',
                        'path' => '/api/v1/admin/users',
                        'description' => 'PB-006: Create user with role assignment and audit trail recording.',
                        'requires_auth' => true,
                        'required_permission' => 'users.create',
                        'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => json_encode([
                            'full_name' => 'Campus Coordinator',
                            'email' => 'coordinator.' . rand(100, 999) . '@schoolmeal.test',
                            'phone' => '+85512' . rand(100000, 999999),
                            'password' => 'StaffPass@2026',
                            'role' => 'school_admin',
                            'status' => 'active',
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ],
                ],
            ],
            [
                'id' => 'schools',
                'name' => '3. Schools, Shifts & Locations',
                'description' => 'Campus profiles, delivery gates, shifts, and school meal policies (PB-007 to PB-012).',
                'icon' => 'academic-cap',
                'endpoints' => [
                    [
                        'id' => 'schools_list',
                        'name' => 'List Partner Schools',
                        'method' => 'GET',
                        'path' => '/api/v1/schools',
                        'description' => 'PB-007: Browse active schools with campus locations.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => ['status' => 'active'],
                        'body' => '',
                    ],
                    [
                        'id' => 'schools_roster',
                        'name' => 'Daily School Meal Delivery Roster',
                        'method' => 'GET',
                        'path' => '/api/v1/schools/{school_id}/meal-roster',
                        'description' => 'PB-011: Daily classroom meal roster grouped by shift and gate location.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => ['delivery_date' => date('Y-m-d'), 'shift' => 'lunch'],
                        'body' => '',
                    ],
                    [
                        'id' => 'schools_shifts',
                        'name' => 'List School Operating Shifts',
                        'method' => 'GET',
                        'path' => '/api/v1/school-shifts',
                        'description' => 'PB-008: Get shift times (Morning, Lunch, Afternoon).',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => '',
                    ],
                    [
                        'id' => 'schools_locations',
                        'name' => 'List Campus Delivery Locations',
                        'method' => 'GET',
                        'path' => '/api/v1/school-locations',
                        'description' => 'PB-009: Get gate desks and canteen drop-off points.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => '',
                    ],
                ],
            ],
            [
                'id' => 'students',
                'name' => '4. Students, Guardians & Allergens',
                'description' => 'Student enrollment, child linking, dietary profiles, and allergen screening (PB-013 to PB-016).',
                'icon' => 'user-group',
                'endpoints' => [
                    [
                        'id' => 'students_list',
                        'name' => 'Search Students Directory',
                        'method' => 'GET',
                        'path' => '/api/v1/students',
                        'description' => 'PB-013: Search students by grade level, class name, or full name.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => ['grade_level' => '', 'class_name' => ''],
                        'body' => '',
                    ],
                    [
                        'id' => 'guardian_students_list',
                        'name' => 'Parent Linked Children',
                        'method' => 'GET',
                        'path' => '/api/v1/guardian/students',
                        'description' => 'PB-014: Get all children linked to current parent with allergen flags.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => '',
                    ],
                ],
            ],
            [
                'id' => 'catalog',
                'name' => '5. Menu Catalog & Versioning',
                'description' => 'Meal catalog browsing, immutable menu versions, and nutrition facts (PB-021 to PB-029).',
                'icon' => 'book-open',
                'endpoints' => [
                    [
                        'id' => 'catalog_meals',
                        'name' => 'Browse Meals & Allergen Screening',
                        'method' => 'GET',
                        'path' => '/api/v1/catalog/meals',
                        'description' => 'PB-015, PB-029: Browse active meals with automatic allergen conflict check if student_id is passed.',
                        'requires_auth' => false,
                        'headers' => ['Accept' => 'application/json'],
                        'params' => [
                            'delivery_date' => date('Y-m-d', strtotime('+1 day')),
                            'shift' => 'lunch',
                        ],
                        'body' => '',
                    ],
                    [
                        'id' => 'catalog_allergens',
                        'name' => 'List Master Allergens',
                        'method' => 'GET',
                        'path' => '/api/v1/allergens',
                        'description' => 'PB-028: Master list of food allergens (peanuts, seafood, gluten, etc.).',
                        'requires_auth' => false,
                        'headers' => ['Accept' => 'application/json'],
                        'params' => [],
                        'body' => '',
                    ],
                    [
                        'id' => 'merchant_menu_items',
                        'name' => 'Merchant Menu Items Roster',
                        'method' => 'GET',
                        'path' => '/api/v1/restaurant/menu-items',
                        'description' => 'PB-021: Restaurant owner views their menu items with active version numbers.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => '',
                    ],
                ],
            ],
            [
                'id' => 'commerce',
                'name' => '6. Commerce & 1–7 Day Scheduling',
                'description' => 'Order creation with server-side cutoff engine and snapshot immutability (PB-031 to PB-039).',
                'icon' => 'shopping-cart',
                'endpoints' => [
                    [
                        'id' => 'orders_list',
                        'name' => 'List Parent Orders',
                        'method' => 'GET',
                        'path' => '/api/v1/orders',
                        'description' => 'PB-037: Parent views scheduled meal orders with multi-day breakdown.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => ['status' => ''],
                        'body' => '',
                    ],
                    [
                        'id' => 'orders_create',
                        'name' => 'Place 1–7 Day Scheduled Order',
                        'method' => 'POST',
                        'path' => '/api/v1/orders',
                        'description' => 'PB-031, PB-035: Place scheduled order with server-side cutoff time validation and Idempotency-Key.',
                        'requires_auth' => true,
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer {token}',
                            'Idempotency-Key' => 'req_' . bin2hex(random_bytes(8)),
                        ],
                        'params' => [],
                        'body' => json_encode([
                            'student_id' => '00000000-0000-0000-0000-000000000001',
                            'restaurant_id' => '00000000-0000-0000-0000-000000000002',
                            'school_location_id' => '00000000-0000-0000-0000-000000000003',
                            'notes' => 'Please provide eco-friendly wooden spoon.',
                            'items' => [
                                [
                                    'menu_version_id' => '00000000-0000-0000-0000-000000000004',
                                    'quantity' => 1,
                                    'delivery_date' => date('Y-m-d', strtotime('+2 days')),
                                    'shift' => 'lunch',
                                ],
                            ],
                            'acknowledge_allergen_warning' => true,
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ],
                ],
            ],
            [
                'id' => 'payments',
                'name' => '7. Payments & Webhooks',
                'description' => 'Bakong KHQR, ABA PayWay, COD and signed webhook receivers (PB-040 to PB-045).',
                'icon' => 'credit-card',
                'endpoints' => [
                    [
                        'id' => 'payments_initiate',
                        'name' => 'Initiate Payment (KHQR / ABA)',
                        'method' => 'POST',
                        'path' => '/api/v1/payments/initiate',
                        'description' => 'PB-040: Generate EMVCo dynamic KHQR or ABA PayWay checkout URL.',
                        'requires_auth' => true,
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer {token}',
                            'Idempotency-Key' => 'pay_' . bin2hex(random_bytes(8)),
                        ],
                        'params' => [],
                        'body' => json_encode([
                            'order_id' => '00000000-0000-0000-0000-000000000001',
                            'provider' => 'KHQR',
                            'method' => 'qr',
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ],
                    [
                        'id' => 'payment_status',
                        'name' => 'Check Payment Status',
                        'method' => 'GET',
                        'path' => '/api/v1/payments/{payment_id}',
                        'description' => 'PB-041: Polling endpoint for client to check payment confirmation.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => '',
                    ],
                ],
            ],
            [
                'id' => 'kitchen',
                'name' => '8. Kitchen Operations & KDS',
                'description' => 'Live Kitchen Display System, production batches, and meal prep flags (PB-046 to PB-049).',
                'icon' => 'fire',
                'endpoints' => [
                    [
                        'id' => 'kitchen_queue',
                        'name' => 'Live Kitchen Production Queue',
                        'method' => 'GET',
                        'path' => '/api/v1/restaurant/kitchen/queue',
                        'description' => 'PB-046: Aggregated meal counts to prepare with privacy-masked child tags.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => ['delivery_date' => date('Y-m-d'), 'shift' => 'lunch'],
                        'body' => '',
                    ],
                    [
                        'id' => 'kitchen_status_update',
                        'name' => 'Update Prep Status',
                        'method' => 'PATCH',
                        'path' => '/api/v1/restaurant/kitchen/schedules/status',
                        'description' => 'PB-048: Mark meal schedules as prepared / ready.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => json_encode([
                            'schedule_ids' => ['00000000-0000-0000-0000-000000000001'],
                            'status' => 'prepared',
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ],
                ],
            ],
            [
                'id' => 'logistics',
                'name' => '9. Driver Logistics & Multi-Modal POD',
                'description' => 'Route dispatch, package scan verification, and proof of delivery (PB-050 to PB-056).',
                'icon' => 'truck',
                'endpoints' => [
                    [
                        'id' => 'driver_batches_today',
                        'name' => "Driver Assigned Today's Batches",
                        'method' => 'GET',
                        'path' => '/api/v1/driver/batches/today',
                        'description' => 'PB-051: Route stops, school gate navigation, and meal counts.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => '',
                    ],
                    [
                        'id' => 'driver_stop_complete',
                        'name' => 'Complete Stop with Proof of Delivery (POD)',
                        'method' => 'POST',
                        'path' => '/api/v1/driver/stops/{stop_id}/complete',
                        'description' => 'PB-054: Submit photo proof, signature, or OTP confirmation. Remember: Delivered != Consumed.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => json_encode([
                            'proof_type' => 'photo',
                            'photo_url' => 'https://storage.schoolmeal.test/proofs/stop-proof-01.jpg',
                            'recipient_name' => 'Teacher Sopheap (Gate Coordinator)',
                            'notes' => 'Handed over 25 hot lunch boxes to school gate cooler.',
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ],
                ],
            ],
            [
                'id' => 'finance',
                'name' => '10. Finance, Settlements & Reports',
                'description' => 'Merchant commission calculation, automated settlement statements, and KPI analytics (PB-057 to PB-074).',
                'icon' => 'chart-bar',
                'endpoints' => [
                    [
                        'id' => 'finance_settlements_list',
                        'name' => 'List Merchant Settlements',
                        'method' => 'GET',
                        'path' => '/api/v1/admin/settlements',
                        'description' => 'PB-057: Admin views merchant billing and payable statements.',
                        'requires_auth' => true,
                        'required_permission' => 'finance.settlements.view',
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => ['status' => 'draft'],
                        'body' => '',
                    ],
                    [
                        'id' => 'finance_reports_kpi',
                        'name' => 'Live Operations KPI Counters',
                        'method' => 'GET',
                        'path' => '/api/v1/admin/reports/operations-kpi',
                        'description' => 'PB-069: Executive metrics: today orders, kitchen prep %, delivery %, total revenue.',
                        'requires_auth' => true,
                        'required_permission' => 'reports.view',
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => '',
                    ],
                ],
            ],
            [
                'id' => 'nutrition',
                'name' => '11. Nutrition Tracking & Feedback',
                'description' => 'Consumption logging (Delivered != Consumed), macronutrient intake math, and meal ratings (PB-030, PB-062 to PB-065).',
                'icon' => 'heart',
                'endpoints' => [
                    [
                        'id' => 'nutrition_log_consumption',
                        'name' => 'Log Nutrition Consumption (Delivered != Consumed)',
                        'method' => 'POST',
                        'path' => '/api/v1/nutrition/consumption-logs',
                        'description' => 'PB-062: Parent or school records actual consumed percentage (100%, 75%, 50%, 0%).',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => [],
                        'body' => json_encode([
                            'order_schedule_id' => '00000000-0000-0000-0000-000000000001',
                            'consumption_status' => 'partial',
                            'estimated_consumed_pct' => 75.0,
                            'notes' => 'Ate most of the chicken and all vegetables, left rice.',
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ],
                    [
                        'id' => 'nutrition_student_summary',
                        'name' => 'Student Nutrition Intake Summary',
                        'method' => 'GET',
                        'path' => '/api/v1/guardian/students/{student_id}/nutrition-summary',
                        'description' => 'PB-064: Aggregated calories and protein intake vs daily Recommended Dietary Allowance.',
                        'requires_auth' => true,
                        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer {token}'],
                        'params' => ['from' => date('Y-m-d', strtotime('-7 days')), 'to' => date('Y-m-d')],
                        'body' => '',
                    ],
                ],
            ],
        ];
    }

    /**
     * Preconfigured demo persona accounts for quick role-swapping during API testing.
     *
     * @return array<string, array<string, string>>
     */
    protected function getDemoPersonas(): array
    {
        return [
            'super_admin' => [
                'role' => 'super_admin',
                'label' => 'Super Admin',
                'email' => 'admin@schoolmeal.test',
                'badge' => 'Full Access (All Permissions)',
                'color' => '#1E3A8A',
            ],
            'school_admin' => [
                'role' => 'school_admin',
                'label' => 'School Admin',
                'email' => 'school@schoolmeal.test',
                'badge' => 'Campus & Daily Roster Access',
                'color' => '#0284C7',
            ],
            'parent' => [
                'role' => 'parent',
                'label' => 'Parent / Guardian',
                'email' => 'parent@schoolmeal.test',
                'badge' => 'Children & Ordering Access',
                'color' => '#10B981',
            ],
            'restaurant_owner' => [
                'role' => 'restaurant_owner',
                'label' => 'Restaurant Owner',
                'email' => 'merchant@schoolmeal.test',
                'badge' => 'Catalog, KDS & Settlement Access',
                'color' => '#F59E0B',
            ],
            'restaurant_staff' => [
                'role' => 'restaurant_staff',
                'label' => 'Kitchen Staff',
                'email' => 'kitchen@schoolmeal.test',
                'badge' => 'Live Prep Queue Access',
                'color' => '#EA580C',
            ],
            'driver' => [
                'role' => 'driver',
                'label' => 'Delivery Driver',
                'email' => 'driver@schoolmeal.test',
                'badge' => 'Route Stops & POD Access',
                'color' => '#8B5CF6',
            ],
            'finance' => [
                'role' => 'finance',
                'label' => 'Finance Officer',
                'email' => 'finance@schoolmeal.test',
                'badge' => 'Commissions & Payout Access',
                'color' => '#059669',
            ],
        ];
    }
}
