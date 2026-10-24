<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuditLogsTest extends TestCase
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
            'email' => 'superadmin.audit@schoolmeal.test',
            'full_name' => 'Super Admin Tester',
            'status' => 'active',
        ]);
        $this->superAdmin->roles()->attach($role->id);
    }

    public function test_super_admin_can_view_audit_logs_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/audit-logs');

        $response->assertStatus(200);
        $response->assertSee('Security & Operational Audit Trail', false);
        $response->assertSee('IMMUTABLE AUDIT TRAIL & COMPLIANCE', false);
        $response->assertSee('Backup & Retention', false);
        $response->assertSee('Export Data');
        $response->assertSee('Today (Day)');
        $response->assertSee('Last 7 Days (Weeks)');
        $response->assertSee('Last 30 Days (Month)');
        $response->assertSee('This Year (Year)');
        $response->assertSee('Custom Range');
        $response->assertSee('toastContainer');
        $response->assertDontSee('alert alert-danger');
        $response->assertDontSee('alert alert-success');
    }

    public function test_audit_logs_ajax_serverside_pagination(): void
    {
        // Seed 15 audit logs
        for ($i = 0; $i < 15; $i++) {
            AuditLog::create([
                'actor_id' => $this->superAdmin->id,
                'action' => 'test.event.' . $i,
                'auditable_type' => User::class,
                'auditable_id' => $this->superAdmin->id,
                'old_values' => ['step' => $i],
                'new_values' => ['step' => $i + 1],
                'ip_address' => '127.0.0.1',
            ]);
        }

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/admin/audit-logs?per_page=10&page=1');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'html',
            'pagination_html',
            'total',
            'from',
            'to',
            'current_page',
            'last_page',
            'per_page',
            'stats' => [
                'filtered_count',
                'total_count',
                'today_count',
                'writes_count',
            ],
        ]);

        $this->assertEquals(10, $response->json('per_page'));
        $this->assertGreaterThanOrEqual(15, $response->json('total'));
        $this->assertStringContainsString('TEST.EVENT', $response->json('html'));
    }

    public function test_audit_logs_filtering_by_action(): void
    {
        AuditLog::create([
            'actor_id' => $this->superAdmin->id,
            'action' => 'user.login',
            'auditable_type' => User::class,
            'auditable_id' => $this->superAdmin->id,
            'ip_address' => '192.168.1.50',
        ]);

        AuditLog::create([
            'actor_id' => $this->superAdmin->id,
            'action' => 'system.settings.update.general',
            'auditable_type' => User::class,
            'auditable_id' => $this->superAdmin->id,
            'ip_address' => '192.168.1.51',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/admin/audit-logs?action_filter=login');

        $response->assertStatus(200);
        $this->assertStringContainsString('USER.LOGIN', $response->json('html'));
        $this->assertStringNotContainsString('system.settings.update.general', $response->json('html'));
    }

    public function test_audit_logs_advance_date_filtering(): void
    {
        // Log created today
        AuditLog::create([
            'actor_id' => $this->superAdmin->id,
            'action' => 'action.today',
            'auditable_type' => User::class,
            'auditable_id' => $this->superAdmin->id,
        ]);

        // Log created 15 days ago (explicit update via DB query to bypass auto-timestamp)
        $oldLog = AuditLog::create([
            'actor_id' => $this->superAdmin->id,
            'action' => 'action.old',
            'auditable_type' => User::class,
            'auditable_id' => $this->superAdmin->id,
        ]);
        \Illuminate\Support\Facades\DB::table('audit_logs')
            ->where('id', $oldLog->id)
            ->update(['created_at' => now()->subDays(15)]);

        // Filter by today
        $todayResponse = $this->actingAs($this->superAdmin)
            ->getJson('/admin/audit-logs?date_preset=today');

        $todayResponse->assertStatus(200);
        $this->assertStringContainsString('ACTION.TODAY', $todayResponse->json('html'));
        $this->assertStringNotContainsString('action.old', $todayResponse->json('html'));

        // Filter by last 7 days (weeks)
        $weekResponse = $this->actingAs($this->superAdmin)
            ->getJson('/admin/audit-logs?date_preset=week');

        $weekResponse->assertStatus(200);
        $this->assertStringContainsString('ACTION.TODAY', $weekResponse->json('html'));
        $this->assertStringNotContainsString('action.old', $weekResponse->json('html'));

        // Custom range covering old log
        $customResponse = $this->actingAs($this->superAdmin)
            ->getJson('/admin/audit-logs?date_preset=custom&start_date=' . now()->subDays(20)->toDateString() . '&end_date=' . now()->subDays(10)->toDateString());

        $customResponse->assertStatus(200);
        $this->assertStringContainsString('ACTION.OLD', $customResponse->json('html'));
        $this->assertStringNotContainsString('action.today', $customResponse->json('html'));
    }

    public function test_audit_logs_keyword_search(): void
    {
        AuditLog::create([
            'actor_id' => $this->superAdmin->id,
            'action' => 'unique.targeted.action',
            'auditable_type' => User::class,
            'auditable_id' => $this->superAdmin->id,
            'ip_address' => '10.200.30.40',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/admin/audit-logs?search=10.200.30.40');

        $response->assertStatus(200);
        $this->assertStringContainsString('UNIQUE.TARGETED.ACTION', $response->json('html'));
        $this->assertStringContainsString('10.200.30.40', $response->json('html'));
    }

    public function test_audit_logs_export_csv(): void
    {
        AuditLog::create([
            'actor_id' => $this->superAdmin->id,
            'action' => 'export.sample.action',
            'auditable_type' => User::class,
            'auditable_id' => $this->superAdmin->id,
            'old_values' => ['role' => 'editor'],
            'new_values' => ['role' => 'admin'],
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get('/admin/audit-logs/export?format=csv');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('audit_logs_export_', $response->headers->get('content-disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Log ID', $content);
        $this->assertStringContainsString('Timestamp (ICT)', $content);
        $this->assertStringContainsString('export.sample.action', $content);
    }

    public function test_audit_logs_export_json_archive(): void
    {
        AuditLog::create([
            'actor_id' => $this->superAdmin->id,
            'action' => 'json.archive.action',
            'auditable_type' => User::class,
            'auditable_id' => $this->superAdmin->id,
            'old_values' => ['status' => 'inactive'],
            'new_values' => ['status' => 'active'],
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get('/admin/audit-logs/export?format=json');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');

        $content = $response->streamedContent();
        $this->assertStringContainsString('SchoolMeal E-Commerce Audit Archive', $content);
        $this->assertStringContainsString('json.archive.action', $content);
    }

    public function test_audit_log_detail_inspector_endpoint(): void
    {
        $log = AuditLog::create([
            'actor_id' => $this->superAdmin->id,
            'action' => 'inspect.detail.action',
            'auditable_type' => User::class,
            'auditable_id' => $this->superAdmin->id,
            'old_values' => ['email' => 'old@schoolmeal.test'],
            'new_values' => ['email' => 'new@schoolmeal.test'],
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->getJson("/admin/audit-logs/{$log->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'log' => [
                'id' => $log->id,
                'action' => 'inspect.detail.action',
                'auditable_type_short' => 'User',
                'actor' => [
                    'name' => 'Super Admin Tester',
                    'email' => 'superadmin.audit@schoolmeal.test',
                ],
                'old_values' => ['email' => 'old@schoolmeal.test'],
                'new_values' => ['email' => 'new@schoolmeal.test'],
            ],
        ]);

        $diff = $response->json('log.diff');
        $this->assertIsArray($diff);
        $this->assertEquals('email', $diff[0]['key']);
        $this->assertEquals('old@schoolmeal.test', $diff[0]['old']);
        $this->assertEquals('new@schoolmeal.test', $diff[0]['new']);
        $this->assertTrue($diff[0]['changed']);
    }

    public function test_audit_logs_backup_retention_endpoint(): void
    {
        AuditLog::create([
            'actor_id' => $this->superAdmin->id,
            'action' => 'backup.event.action',
            'auditable_type' => User::class,
            'auditable_id' => $this->superAdmin->id,
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post('/admin/audit-logs/backup', [
                'cutoff' => 'all',
                'format' => 'json',
                'purge_after_backup' => '0',
            ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');

        $content = $response->streamedContent();
        $this->assertStringContainsString('IMMUTABLE_AUDIT_LOG_SNAPSHOT', $content);
        $this->assertStringContainsString('backup.event.action', $content);
    }
}
