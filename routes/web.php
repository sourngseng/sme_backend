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

    // REST API Studio & Interactive Tester (Postman for Super Admin)
    Route::prefix('admin/api-tester')->name('admin.api_tester.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\ApiTesterController::class, 'index'])->name('index');
        Route::post('/token', [\App\Http\Controllers\Web\ApiTesterController::class, 'generateToken'])->name('token');
    });
    Route::get('/admin/api-manager', [\App\Http\Controllers\Web\ApiTesterController::class, 'index']);
    Route::get('/admin/rest-api', [\App\Http\Controllers\Web\ApiTesterController::class, 'index']);

    // Super Admin Left Sidebar Modular Sections (All accessible via localhost:8000/admin/...)
    Route::prefix('admin')->name('admin.')->group(function () {
        // Management
        Route::get('/users', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'users')->name('users');
        Route::get('/roles', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'roles')->name('roles');
        Route::get('/administrators', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'administrators')->name('administrators');
        Route::get('/schools', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'schools')->name('schools');
        Route::get('/students', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'students')->name('students');
        Route::get('/restaurants', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'restaurants')->name('restaurants');
        Route::get('/menu', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'menu')->name('menu');
        Route::get('/meal-plans', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'meal-plans')->name('meal_plans');
        Route::get('/nutrition', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'nutrition')->name('nutrition');
        Route::get('/orders', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'orders')->name('orders');
        Route::get('/kitchen-queue', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'kitchen-queue')->name('kitchen_queue');
        Route::get('/order-analytics', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'order-analytics')->name('order_analytics');
        Route::get('/payments', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'payments')->name('payments');
        Route::get('/drivers', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'drivers')->name('drivers');
        Route::get('/deliveries', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'deliveries')->name('deliveries');
        Route::get('/tracking', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'tracking')->name('tracking');

        // Finance
        Route::get('/commissions', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'commissions')->name('commissions');
        Route::get('/settlements', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'settlements')->name('settlements');
        Route::get('/financial-reports', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'financial-reports')->name('financial_reports');

        // Operations
        Route::get('/marketing', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'marketing')->name('marketing');
        Route::get('/vouchers', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'vouchers')->name('vouchers');
        Route::get('/promotions', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'promotions')->name('promotions');
        Route::get('/notifications', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'notifications')->name('notifications');
        Route::get('/support', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'support')->name('support');

        // Reports & Analytics
        Route::get('/analytics', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'analytics')->name('analytics');
        Route::get('/reports', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'reports')->name('reports');

        // System & Audit Logs
        Route::get('/audit-logs/export', [\App\Http\Controllers\Web\AdminSectionController::class, 'exportAuditLogs'])->name('audit_logs.export');
        Route::post('/audit-logs/backup', [\App\Http\Controllers\Web\AdminSectionController::class, 'backupAuditLogs'])->name('audit_logs.backup');
        Route::get('/audit-logs/{id}', [\App\Http\Controllers\Web\AdminSectionController::class, 'getAuditLogDetail'])->name('audit_logs.detail');
        Route::get('/audit-logs', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'audit-logs')->name('audit_logs');
        Route::get('/system-health', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->defaults('section', 'system-health')->name('system_health');

        // Generic fallback for any other admin sub-section
        Route::get('/section/{section}', [\App\Http\Controllers\Web\AdminSectionController::class, 'show'])->name('section');
    });

    // School Admin Left Sidebar Modular Sections (All accessible via localhost:8000/school/...)
    Route::prefix('school')->name('school.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'school'])->name('dashboard');

        // School Management
        Route::get('/profile', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'profile')->name('profile');
        Route::get('/shifts', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'shifts')->name('shifts');
        Route::post('/shifts', [\App\Http\Controllers\Web\SchoolSectionController::class, 'storeShift'])->name('shifts.store');
        Route::get('/locations', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'locations')->name('locations');
        Route::post('/locations', [\App\Http\Controllers\Web\SchoolSectionController::class, 'storeLocation'])->name('locations.store');
        Route::get('/students', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'students')->name('students');
        Route::post('/students', [\App\Http\Controllers\Web\SchoolSectionController::class, 'storeStudent'])->name('students.store');
        Route::put('/students/{id}', [\App\Http\Controllers\Web\SchoolSectionController::class, 'updateStudent'])->name('students.update');
        Route::delete('/students/{id}', [\App\Http\Controllers\Web\SchoolSectionController::class, 'deleteStudent'])->name('students.delete');
        Route::get('/classes-grades', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'classes-grades')->name('classes_grades');
        Route::get('/classes', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'classes-grades');
        Route::get('/grades', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'classes-grades');
        Route::post('/classes', [\App\Http\Controllers\Web\SchoolSectionController::class, 'storeClass'])->name('classes.store');
        Route::put('/classes/{id}', [\App\Http\Controllers\Web\SchoolSectionController::class, 'updateClass'])->name('classes.update');
        Route::delete('/classes/{id}', [\App\Http\Controllers\Web\SchoolSectionController::class, 'deleteClass'])->name('classes.delete');
        Route::post('/grades', [\App\Http\Controllers\Web\SchoolSectionController::class, 'storeGrade'])->name('grades.store');
        Route::put('/grades/{id}', [\App\Http\Controllers\Web\SchoolSectionController::class, 'updateGrade'])->name('grades.update');
        Route::delete('/grades/{id}', [\App\Http\Controllers\Web\SchoolSectionController::class, 'deleteGrade'])->name('grades.delete');

        // Meal Operations
        Route::get('/roster', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'roster')->name('roster');
        Route::get('/orders', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'orders')->name('orders');
        Route::get('/delivery', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'delivery')->name('delivery');
        Route::get('/nutrition', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'nutrition')->name('nutrition');

        // Reports
        Route::get('/participation', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'participation')->name('participation');
        Route::get('/consumption', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'consumption')->name('consumption');
        Route::get('/reports', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'reports')->name('reports');

        // Communication
        Route::get('/announcements', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'announcements')->name('announcements');
        Route::post('/announcements', [\App\Http\Controllers\Web\SchoolSectionController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::get('/support', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'support')->name('support');
        Route::post('/support', [\App\Http\Controllers\Web\SchoolSectionController::class, 'storeTicket'])->name('support.store');

        // Switch School
        Route::get('/switch', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->defaults('section', 'profile')->name('switch');
        Route::post('/switch', [\App\Http\Controllers\Web\SchoolSectionController::class, 'setSchool'])->name('set_school');

        // Generic fallback for any other school sub-section
        Route::get('/section/{section}', [\App\Http\Controllers\Web\SchoolSectionController::class, 'show'])->name('section');
    });

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


