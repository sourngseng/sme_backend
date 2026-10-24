<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ApiTesterTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'super_admin'], ['guard_name' => 'web']);
    }

    public function test_guest_is_redirected_to_login_when_accessing_api_tester(): void
    {
        $response = $this->get('/admin/api-tester');
        $response->assertRedirect('/login');
    }

    public function test_super_admin_can_access_api_tester_console(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@schoolmeal.test'],
            [
                'full_name' => 'Super Administrator',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $admin->syncRoles(['super_admin']);

        $response = $this->actingAs($admin)->get('/admin/api-tester');

        $response->assertStatus(200);
        $response->assertSee('REST API Studio');
        $response->assertSee('Postman-style');
        $response->assertSee('Send');
    }

    public function test_super_admin_can_generate_persona_tokens_on_the_fly(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@schoolmeal.test'],
            [
                'full_name' => 'Super Administrator',
                'password' => 'Password@123',
                'status' => 'active',
            ]
        );
        $admin->syncRoles(['super_admin']);

        $response = $this->actingAs($admin)->postJson('/admin/api-tester/token', [
            'role' => 'parent',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'full_name', 'email', 'role'],
            ]);
    }
}
