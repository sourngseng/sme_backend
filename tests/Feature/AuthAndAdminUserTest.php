<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\OtpRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthAndAdminUserTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('otp_request:test_otp@example.com');
        RateLimiter::clear('otp_request:+85512999888');

        Role::firstOrCreate(['name' => 'super_admin'], ['guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'parent'], ['guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'driver'], ['guard_name' => 'web']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@schoolmeal.test'],
            [
                'full_name' => 'Super Administrator',
                'phone' => '+85512000001',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $admin->syncRoles(['super_admin']);
    }

    public function test_user_can_register_with_email(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'full_name' => 'John Parent',
            'email' => 'john.parent@example.com',
            'password' => 'Secret1234!',
            'role' => 'parent',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'User registered successfully.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'full_name', 'email', 'status', 'roles'],
                    'token',
                    'token_type',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.parent@example.com',
            'full_name' => 'John Parent',
        ]);

        // Verify Auditable trait recorded the creation
        $user = User::where('email', 'john.parent@example.com')->first();
        $this->assertNotNull($user);
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'action' => 'created',
        ]);
    }

    public function test_user_can_login_with_email_or_phone(): void
    {
        $user = User::create([
            'full_name' => 'Login Tester',
            'email' => 'login.tester@example.com',
            'phone' => '+85512777111',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $user->assignRole('parent');

        // Login via email
        $emailLogin = $this->postJson('/api/v1/auth/login', [
            'login' => 'login.tester@example.com',
            'password' => 'Password123!',
        ]);

        $emailLogin->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.user.email', 'login.tester@example.com');

        // Login via phone
        $phoneLogin = $this->postJson('/api/v1/auth/login', [
            'login' => '+85512777111',
            'password' => 'Password123!',
        ]);

        $phoneLogin->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.user.phone', '+85512777111');
    }

    public function test_login_rejects_inactive_or_wrong_credentials(): void
    {
        $user = User::create([
            'full_name' => 'Suspended User',
            'email' => 'suspended@example.com',
            'password' => 'Password123!',
            'status' => 'suspended',
        ]);

        // Wrong password
        $this->postJson('/api/v1/auth/login', [
            'login' => 'suspended@example.com',
            'password' => 'WrongPassword',
        ])->assertStatus(401)
            ->assertJson(['error_code' => 'INVALID_CREDENTIALS']);

        // Inactive account
        $this->postJson('/api/v1/auth/login', [
            'login' => 'suspended@example.com',
            'password' => 'Password123!',
        ])->assertStatus(403)
            ->assertJson(['error_code' => 'ACCOUNT_INACTIVE']);
    }

    public function test_otp_request_and_strict_redis_rate_limiting(): void
    {
        $target = 'test_otp@example.com';
        RateLimiter::clear('otp_request:' . $target);

        // 3 allowed requests
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/v1/auth/otp/request', [
                'phone_or_email' => $target,
            ]);
            $response->assertStatus(200)
                ->assertJson(['success' => true]);
        }

        // 4th request must be rejected with 429 Too Many Requests
        $rateLimited = $this->postJson('/api/v1/auth/otp/request', [
            'phone_or_email' => $target,
        ]);

        $rateLimited->assertStatus(429)
            ->assertJson([
                'success' => false,
                'error_code' => 'RATE_LIMIT_EXCEEDED',
            ])
            ->assertJsonStructure(['retry_after_seconds']);
    }

    public function test_otp_verify_and_session_token_issuance(): void
    {
        $target = '+85512999888';
        $plainOtp = '123456';

        OtpRequest::create([
            'phone_or_email' => $target,
            'otp_hash' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        // Invalid OTP test
        $this->postJson('/api/v1/auth/otp/verify', [
            'phone_or_email' => $target,
            'otp' => '000000',
        ])->assertStatus(422)
            ->assertJson(['error_code' => 'INVALID_OTP']);

        // Valid OTP test
        $response = $this->postJson('/api/v1/auth/otp/verify', [
            'phone_or_email' => $target,
            'otp' => '123456',
            'full_name' => 'OTP Verified User',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.user.phone', $target);

        $this->assertDatabaseHas('users', [
            'phone' => $target,
            'status' => 'active',
        ]);
    }

    public function test_authenticated_user_profile_and_logout(): void
    {
        $user = User::create([
            'full_name' => 'Active Member',
            'email' => 'active.member@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $user->assignRole('parent');

        $token = $user->createToken('test-token')->plainTextToken;

        // GET /api/v1/auth/me
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'email' => 'active.member@example.com',
                ],
            ]);

        // POST /api/v1/auth/logout
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_permission_middleware_blocks_unauthorized_users_with_standardized_403(): void
    {
        // Parent user does not have 'users.view' permission
        $parentUser = User::create([
            'full_name' => 'Regular Parent',
            'email' => 'regular.parent@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $parentUser->assignRole('parent');
        $token = $parentUser->createToken('parent-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/admin/users');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error_code' => 'FORBIDDEN',
                'message' => 'Forbidden. You do not have permission to perform this action.',
                'required_permissions' => ['users.view'],
            ]);
    }

    public function test_admin_user_crud_and_automatic_auditable_logging(): void
    {
        // Super admin user has full permissions
        $admin = User::where('email', 'admin@schoolmeal.test')->first();
        $adminToken = $admin->createToken('admin-token')->plainTextToken;

        // 1. Search / List Users
        $listResponse = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->getJson('/api/v1/admin/users?role=parent');

        $listResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data', 'meta' => ['current_page', 'total']]);

        // 2. Create User
        $createResponse = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->postJson('/api/v1/admin/users', [
                'full_name' => 'New Driver Candidate',
                'email' => 'new.driver@example.com',
                'phone' => '+85512999111',
                'password' => 'DriverPass123!',
                'status' => 'active',
                'roles' => ['driver'],
            ]);

        $createResponse->assertStatus(201)
            ->assertJson(['success' => true]);

        $newUserId = $createResponse->json('data.id');

        // Check audit log for creation
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => User::class,
            'auditable_id' => $newUserId,
            'action' => 'created',
            'actor_id' => $admin->id,
        ]);

        // 3. Update User Status
        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->patchJson("/api/v1/admin/users/{$newUserId}", [
                'status' => 'suspended',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'suspended');

        // Check audit log for status update
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => User::class,
            'auditable_id' => $newUserId,
            'action' => 'status_updated',
            'actor_id' => $admin->id,
        ]);

        // 4. Delete User
        $deleteResponse = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->deleteJson("/api/v1/admin/users/{$newUserId}");

        $deleteResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('users', ['id' => $newUserId]);
    }
}
