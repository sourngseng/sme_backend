<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;

// Public Locale Switcher (Available to guests on login and authenticated users)
Route::match(['get', 'post'], '/locale/switch', [\App\Http\Controllers\Web\LanguageController::class, 'switchLocale'])->name('locale.switch');

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated Web Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Role-specific and generic dashboards
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/school/dashboard', [DashboardController::class, 'school'])->name('school.dashboard');
    Route::get('/restaurant/dashboard', [DashboardController::class, 'restaurant'])->name('restaurant.dashboard');
    Route::get('/restaurant/queue-paginate', [DashboardController::class, 'kitchenQueuePaginate'])->name('restaurant.queue.paginate');
    Route::get('/kitchen/dashboard', [DashboardController::class, 'restaurantStaff'])->name('kitchen.dashboard');
    Route::get('/kitchen/queue-paginate', [DashboardController::class, 'kitchenQueuePaginate'])->name('kitchen.queue.paginate');
    Route::get('/driver/dashboard', [DashboardController::class, 'driver'])->name('driver.dashboard');
    Route::get('/finance/dashboard', [DashboardController::class, 'finance'])->name('finance.dashboard');
    Route::get('/parent/dashboard', [DashboardController::class, 'parent'])->name('parent.dashboard');
    Route::get('/student/dashboard', [DashboardController::class, 'student'])->name('student.dashboard');
    Route::get('/marketing/dashboard', [DashboardController::class, 'marketing'])->name('marketing.dashboard');
    Route::get('/support/dashboard', [DashboardController::class, 'support'])->name('support.dashboard');

    // Language & Translation Management (Super Admin Portal)
    Route::prefix('admin/languages')->name('admin.languages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\LanguageController::class, 'index'])->name('index');
        Route::post('/switch', [\App\Http\Controllers\Web\LanguageController::class, 'switchLocale'])->name('switch');
        Route::post('/update', [\App\Http\Controllers\Web\LanguageController::class, 'update'])->name('update');
        Route::post('/bulk', [\App\Http\Controllers\Web\LanguageController::class, 'bulkSave'])->name('bulk');
    });

    // System Settings Management (Super Admin Portal)
    Route::prefix('admin/system-settings')->name('admin.system_settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\SystemSettingController::class, 'index'])->name('index');
        Route::post('/update', [\App\Http\Controllers\Web\SystemSettingController::class, 'update'])->name('update');
        Route::post('/cache-clear', [\App\Http\Controllers\Web\SystemSettingController::class, 'clearCache'])->name('cache_clear');
        Route::post('/reset-defaults', [\App\Http\Controllers\Web\SystemSettingController::class, 'resetDefaults'])->name('reset_defaults');
    });

    // Aliases for system settings
    Route::get('/admin/system-setting', [\App\Http\Controllers\Web\SystemSettingController::class, 'index']);
    Route::get('/admin/system_settings', [\App\Http\Controllers\Web\SystemSettingController::class, 'index']);
    Route::get('/admin//system-settings', [\App\Http\Controllers\Web\SystemSettingController::class, 'index']);

    // Profile Routing Dispatcher
    Route::get('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'index'])->name('profile');

    // Super Admin Profile, Settings & Security
    Route::prefix('admin/profile')->name('admin.profile')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\ProfileController::class, 'adminProfile']);
        Route::get('/settings', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\Web\ProfileController::class)->adminProfile($request, 'settings');
        })->name('.settings');
        Route::get('/setting', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\Web\ProfileController::class)->adminProfile($request, 'settings');
        })->name('.setting');
        Route::get('/security', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\Web\ProfileController::class)->adminProfile($request, 'security');
        })->name('.security');
        Route::post('/', [\App\Http\Controllers\Web\ProfileController::class, 'updateProfile'])->name('.update');
        Route::post('/password', [\App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])->name('.password');
        Route::post('/sessions/logout-other', [\App\Http\Controllers\Web\ProfileController::class, 'logoutOtherSessions'])->name('.sessions.logout_other');
        Route::delete('/sessions/{id}', [\App\Http\Controllers\Web\ProfileController::class, 'destroySession'])->name('.sessions.destroy');
    });

    // Fallback alias for accidental double slashes (e.g., http://localhost:8000/admin//profile/security)
    Route::get('/admin//profile/security', function (\Illuminate\Http\Request $request) {
        return app(\App\Http\Controllers\Web\ProfileController::class)->adminProfile($request, 'security');
    });
    Route::get('/admin//profile/setting', function (\Illuminate\Http\Request $request) {
        return app(\App\Http\Controllers\Web\ProfileController::class)->adminProfile($request, 'settings');
    });
    Route::get('/admin//profile', function (\Illuminate\Http\Request $request) {
        return app(\App\Http\Controllers\Web\ProfileController::class)->adminProfile($request, 'overview');
    });

    // Kitchen Staff Profile, Settings & Security
    Route::prefix('kitchen/profile')->name('kitchen.profile')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\ProfileController::class, 'kitchenProfile']);
        Route::get('/settings', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\Web\ProfileController::class)->kitchenProfile($request, 'settings');
        })->name('.settings');
        Route::get('/security', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\Web\ProfileController::class)->kitchenProfile($request, 'security');
        })->name('.security');
        Route::post('/', [\App\Http\Controllers\Web\ProfileController::class, 'updateProfile'])->name('.update');
        Route::post('/password', [\App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])->name('.password');
        Route::post('/sessions/logout-other', [\App\Http\Controllers\Web\ProfileController::class, 'logoutOtherSessions'])->name('.sessions.logout_other');
        Route::delete('/sessions/{id}', [\App\Http\Controllers\Web\ProfileController::class, 'destroySession'])->name('.sessions.destroy');
    });

    // Restaurant Merchant Profile, Settings & Security
    Route::prefix('restaurant/profile')->name('restaurant.profile')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\ProfileController::class, 'restaurantProfile']);
        Route::get('/settings', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\Web\ProfileController::class)->restaurantProfile($request, 'settings');
        })->name('.settings');
        Route::get('/security', function (\Illuminate\Http\Request $request) {
            return app(\App\Http\Controllers\Web\ProfileController::class)->restaurantProfile($request, 'security');
        })->name('.security');
        Route::post('/', [\App\Http\Controllers\Web\ProfileController::class, 'updateProfile'])->name('.update');
        Route::post('/password', [\App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])->name('.password');
        Route::post('/sessions/logout-other', [\App\Http\Controllers\Web\ProfileController::class, 'logoutOtherSessions'])->name('.sessions.logout_other');
        Route::delete('/sessions/{id}', [\App\Http\Controllers\Web\ProfileController::class, 'destroySession'])->name('.sessions.destroy');
    });
});


