<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\LanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageTranslationTest extends TestCase
{
    /**
     * Test global helper transLabel() returns correct localized text.
     */
    public function test_trans_label_helper_resolves_translations_correctly(): void
    {
        app()->setLocale('en');
        $this->assertEquals('Dashboard', transLabel('menu.dashboard'));
        $this->assertEquals('Email Address', transLabel('form.email_address'));

        app()->setLocale('km');
        $this->assertEquals('ផ្ទាំងគ្រប់គ្រង', transLabel('menu.dashboard'));
        $this->assertEquals('អាសយដ្ឋានអ៊ីមែល', transLabel('form.email_address'));

        // Fallback testing
        $this->assertEquals('NonExistentKey', transLabel('some.nonexistent.key', 'NonExistentKey'));

        // Reset back to English
        app()->setLocale('en');
    }

    /**
     * Test that an authenticated Super Admin can access the Manage Languages portal view.
     */
    public function test_super_admin_can_view_language_management_page(): void
    {
        $admin = User::first() ?? User::factory()->create([
            'role' => 'super_admin',
            'full_name' => 'SENG Sourng',
            'email' => 'admin@schoolmeal.test',
        ]);

        $response = $this->actingAs($admin)->get('/admin/languages');

        $response->assertStatus(200);
        $response->assertSee('Manage Languages');
        $response->assertSee('lang/en.json');
        $response->assertSee('lang/km.json');
        $response->assertSee('menu.dashboard');
    }

    /**
     * Test switching active locale via POST /locale/switch updates the session and app locale.
     */
    public function test_user_can_switch_locale(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'super_admin']);

        // Switch to Khmer
        $response = $this->actingAs($admin)->post('/locale/switch', [
            'locale' => 'km',
        ]);

        $response->assertSessionHas('locale', 'km');
        $response->assertSessionHas('status');

        // Switch back to English
        $response = $this->actingAs($admin)->post('/locale/switch', [
            'locale' => 'en',
        ]);

        $response->assertSessionHas('locale', 'en');
    }

    /**
     * Test locale switch rejects unsupported locales.
     */
    public function test_switch_locale_validates_input(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)->post('/locale/switch', [
            'locale' => 'fr', // French is not currently in supported list
        ]);

        $response->assertSessionHasErrors(['locale']);
    }

    /**
     * Test saving or updating an individual translation key updates the JSON dictionary files.
     */
    public function test_admin_can_update_translation_key(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'super_admin']);

        $testKey = 'form.test_unit_label';
        $testEn = 'Test Unit Label EN';
        $testKm = 'ផ្លាកសាកល្បង KM';

        $response = $this->actingAs($admin)->post('/admin/languages/update', [
            'key' => $testKey,
            'en' => $testEn,
            'km' => $testKm,
        ]);

        $response->assertSessionHas('status');

        $langService = app(LanguageService::class);
        $this->assertEquals($testEn, $langService->transLabel($testKey, null, 'en'));
        $this->assertEquals($testKm, $langService->transLabel($testKey, null, 'km'));

        // Clean up test key
        $langService->deleteTranslationKey('en', $testKey);
        $langService->deleteTranslationKey('km', $testKey);
    }

    /**
     * Test bulk saving translations from the management dashboard table.
     */
    public function test_admin_can_bulk_save_translations(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)->post('/admin/languages/bulk', [
            'translations' => [
                'common.test_bulk_one' => [
                    'en' => 'Bulk One EN',
                    'km' => 'កញ្ចប់ទីមួយ KM',
                ],
                'common.test_bulk_two' => [
                    'en' => 'Bulk Two EN',
                    'km' => 'កញ្ចប់ទីពីរ KM',
                ],
            ],
        ]);

        $response->assertSessionHas('status');

        $langService = app(LanguageService::class);
        $this->assertEquals('Bulk One EN', $langService->transLabel('common.test_bulk_one', null, 'en'));
        $this->assertEquals('កញ្ចប់ទីពីរ KM', $langService->transLabel('common.test_bulk_two', null, 'km'));

        // Clean up
        $langService->deleteTranslationKey('en', 'common.test_bulk_one');
        $langService->deleteTranslationKey('km', 'common.test_bulk_one');
        $langService->deleteTranslationKey('en', 'common.test_bulk_two');
        $langService->deleteTranslationKey('km', 'common.test_bulk_two');
    }

    /**
     * Test the public REST API endpoint for Next.js frontend and mobile apps.
     */
    public function test_public_api_endpoint_returns_json_translations_for_clients(): void
    {
        // 1. All languages export
        $response = $this->getJson('/api/v1/translations');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'en',
                'km',
            ],
        ]);

        // 2. Specific English dictionary
        $responseEn = $this->getJson('/api/v1/translations/en');
        $responseEn->assertStatus(200);
        $responseEn->assertJsonFragment([
            'menu.dashboard' => 'Dashboard',
        ]);

        // 3. Specific Khmer dictionary
        $responseKm = $this->getJson('/api/v1/translations/km');
        $responseKm->assertStatus(200);
        $responseKm->assertJsonFragment([
            'menu.dashboard' => 'ផ្ទាំងគ្រប់គ្រង',
        ]);

        // 4. Unsupported locale 404
        $responseInvalid = $this->getJson('/api/v1/translations/invalid_locale');
        $responseInvalid->assertStatus(404);
        $responseInvalid->assertJson(['success' => false]);
    }

    /**
     * Test master layout incorporates Hanuman font and enhanced Khmer typography rules.
     */
    public function test_master_layout_imports_hanuman_font_and_khmer_typography_rules(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'super_admin']);

        // Switch locale to Khmer
        session(['locale' => 'km']);
        app()->setLocale('km');

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        // Assert Hanuman Google Font import & CSS rules
        $response->assertSee('family=Hanuman', false);
        $response->assertSee('--font-khmer', false);
        $response->assertSee('Hanuman', false);
        $response->assertSee('html[lang="km"]', false);
        $response->assertSee('html[lang="km"] .sidebar-item', false);
        $response->assertSee('0.94rem', false); // Increased font size for Khmer sidebar
        $response->assertSee('line-height: 1.65', false);
    }

    /**
     * Test that each role portal has a separate, standalone master layout with dedicated branding and non-clipping notification dropdown.
     */
    public function test_all_role_master_layouts_are_standalone_with_dedicated_portals(): void
    {
        $roleLayoutFiles = [
            'master_super_admin.blade.php',
            'master_school_admin.blade.php',
            'master_restaurant_owner.blade.php',
            'master_restaurant_staff.blade.php',
            'master_driver.blade.php',
            'master_parent.blade.php',
            'master_student.blade.php',
            'master_finance.blade.php',
            'master_marketing.blade.php',
            'master_support.blade.php',
        ];

        $layoutsDir = resource_path('views/layouts');

        foreach ($roleLayoutFiles as $file) {
            $path = $layoutsDir . DIRECTORY_SEPARATOR . $file;
            $this->assertFileExists($path, "Role layout file {$file} must exist.");
            $content = file_get_contents($path);

            // Separate standalone layout: must NOT simply be an extension of master.blade.php
            $this->assertStringNotContainsString("@extends('layouts.master')", $content, "{$file} must be an independent separate master layout.");

            // Must include dedicated portal architecture, topbar, mobile bottom navigation, and toast container
            $this->assertStringContainsString('<!DOCTYPE html>', $content, "{$file} must be a standalone HTML document.");
            $this->assertStringContainsString('admin-sidebar', $content, "{$file} must include its own dedicated sidebar.");
            $this->assertStringContainsString("layouts.partials.topbar", $content, "{$file} must include the improved topbar with notification center.");
            $this->assertStringContainsString('mobile-bottom-bar', $content, "{$file} must include mobile bottom navigation.");
            $this->assertStringContainsString('<x-toast-container />', $content, "{$file} must include floating toast container.");
            // Role badge must be removed from top navbar
            $this->assertStringNotContainsString('portalBadge', $content, "{$file} must not pass portalBadge to topbar.");
        }

        // Topbar partial must not render portalBadge
        $topbarContent = file_get_contents($layoutsDir . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'topbar.blade.php');
        $this->assertStringNotContainsString('portalBadge', $topbarContent, "Topbar partial must not contain role portal badge.");
    }

    /**
     * Test admin dashboard Operational Health rail renders full Khmer translations.
     */
    public function test_admin_dashboard_renders_khmer_translations_for_operational_health_rail(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)->withSession(['locale' => 'km'])->get('/admin/dashboard');

        $response->assertStatus(200);

        // Operational Health badge in Khmer
        $response->assertSee('សុខភាពប្រតិបត្តិការ & ការផ្សាយផ្ទាល់');

        // Views pill count in Khmer without double prefix
        $response->assertSee('4 ទិដ្ឋភាព');
        $response->assertDontSee('4 4 ទិដ្ឋភាព');
        $response->assertSee('រំកិលដើម្បីមើលបន្ថែម');

        // Card Titles in Khmer
        $response->assertSee('ស្ថានភាពការទូទាត់ប្រាក់');
        $response->assertSee('ប្រសិទ្ធភាពការដឹកជញ្ជូន');
        $response->assertSee('អត្រាចូលរួមបរិភោគអាហាររបស់សិស្ស');
        $response->assertSee('សកម្មភាពថ្មីៗ');

        // Legends & center text in Khmer
        $response->assertSee('ចំណូលសរុប');
        $response->assertSee('បានទូទាត់រួច');
        $response->assertSee('រង់ចាំការបញ្ជាក់');
        $response->assertSee('បរាជ័យ');
        $response->assertSee('បានសងប្រាក់វិញ');
        $response->assertSee('ទាន់ពេលវេលា');
        $response->assertSee('យឺតយ៉ាវ');
        $response->assertSee('បានបញ្ជាទិញ');
        $response->assertSee('មិនបានបញ្ជាទិញ');

        // Recent activity tabs in Khmer
        $response->assertSee('ទាំងអស់');
        $response->assertSee('ការទូទាត់');
        $response->assertSee('ប្រតិបត្តិការ');
        $response->assertSee('អ្នកប្រើប្រាស់');
    }
}
