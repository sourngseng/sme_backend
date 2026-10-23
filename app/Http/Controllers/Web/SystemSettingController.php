<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    /**
     * Display the System Settings Administration Console.
     */
    public function index(Request $request): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->hasRole(['super_admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access to Platform System Settings.');
        }

        $activeTab = $request->query('tab', 'general');
        $validTabs = ['general', 'ordering', 'merchant', 'security', 'notifications', 'maintenance'];
        if (! in_array($activeTab, $validTabs)) {
            $activeTab = 'general';
        }

        $groupedSettings = SystemSetting::getAllGrouped();

        $stats = [
            'total_settings' => SystemSetting::count(),
            'last_updated' => SystemSetting::latest('updated_at')->value('updated_at'),
            'maintenance_mode' => SystemSetting::get('maintenance_mode', false),
            'currency' => SystemSetting::get('default_currency', 'KHR'),
            'exchange_rate' => SystemSetting::get('exchange_rate_usd_khr', 4100),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'db_driver' => config('database.default', 'pgsql'),
            'cache_driver' => config('cache.default', 'file'),
        ];

        // Gather recent audit events relating to system config
        $recentAuditLogs = AuditLog::with('actor')
            ->where('action', 'like', 'system.%')
            ->latest()
            ->take(6)
            ->get();

        return view('admin.system_settings.index', [
            'user' => $user,
            'activeTab' => $activeTab,
            'groupedSettings' => $groupedSettings,
            'stats' => $stats,
            'recentAuditLogs' => $recentAuditLogs,
        ]);
    }

    /**
     * Update settings for a specific group or multiple groups.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->hasRole(['super_admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        if (SystemSetting::count() === 0) {
            SystemSetting::seedDefaults();
        }

        $submittedSettings = $request->input('settings', []);
        $activeTab = $request->input('active_tab', 'general');

        if (! empty($submittedSettings) && is_array($submittedSettings)) {
            $oldValues = [];
            $newValues = [];

            // Identify all boolean settings for the active tab to handle unchecked checkboxes
            $targetGroup = in_array($activeTab, ['general', 'ordering', 'merchant', 'security', 'notifications']) ? $activeTab : null;
            if ($targetGroup) {
                $booleanKeys = SystemSetting::where('group', $targetGroup)
                    ->whereIn('type', ['boolean', 'bool'])
                    ->pluck('key')
                    ->toArray();

                foreach ($booleanKeys as $bKey) {
                    if (! isset($submittedSettings[$bKey])) {
                        $submittedSettings[$bKey] = '0';
                    }
                }
            }

            foreach ($submittedSettings as $key => $val) {
                $setting = SystemSetting::firstOrNew(['key' => $key]);
                $oldValues[$key] = $setting->value;

                if (in_array($setting->type, ['boolean', 'bool'])) {
                    $val = ($val === '1' || $val === 'on' || $val === true) ? '1' : '0';
                }

                $setting->value = (string) $val;
                if (! $setting->exists) {
                    $setting->group = $activeTab;
                    $setting->type = 'string';
                    $setting->label = ucwords(str_replace('_', ' ', $key));
                }
                $setting->save();
                $newValues[$key] = $val;
            }

            // Record audit trail
            AuditLog::create([
                'actor_id' => $user->id,
                'action' => 'system.settings.update.' . $activeTab,
                'auditable_type' => SystemSetting::class,
                'auditable_id' => SystemSetting::SYSTEM_UUID,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('admin.system_settings.index', ['tab' => $activeTab])
            ->with('success', transLabel('settings.toast_saved', 'System parameters updated successfully and applied platform-wide.'));
    }

    /**
     * Clear application cache, views, and optimize configs.
     */
    public function clearCache(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->hasRole(['super_admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $type = $request->input('cache_type', 'all');

        try {
            if ($type === 'cache' || $type === 'all') {
                Artisan::call('cache:clear');
            }
            if ($type === 'views' || $type === 'all') {
                Artisan::call('view:clear');
            }
            if ($type === 'config' || $type === 'all') {
                Artisan::call('config:clear');
            }

            AuditLog::create([
                'actor_id' => $user->id,
                'action' => 'system.cache.cleared',
                'auditable_type' => SystemSetting::class,
                'auditable_id' => SystemSetting::SYSTEM_UUID,
                'new_values' => ['type' => $type, 'timestamp' => now()->toIso8601String()],
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('admin.system_settings.index', ['tab' => 'maintenance'])
                ->with('success', transLabel('settings.toast_cache_cleared', 'Application cache, view compiled templates, and framework cache cleared successfully.'));
        } catch (\Throwable $e) {
            return redirect()->route('admin.system_settings.index', ['tab' => 'maintenance'])
                ->with('error', 'Cache clearance error: ' . $e->getMessage());
        }
    }

    /**
     * Reset default system settings to national baseline values.
     */
    public function resetDefaults(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->hasRole(['super_admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        SystemSetting::seedDefaults();

        AuditLog::create([
            'actor_id' => $user->id,
            'action' => 'system.settings.reset_defaults',
            'auditable_type' => SystemSetting::class,
            'auditable_id' => SystemSetting::SYSTEM_UUID,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.system_settings.index')
            ->with('success', transLabel('settings.toast_reset_success', 'System parameters reset to national baseline defaults.'));
    }
}
