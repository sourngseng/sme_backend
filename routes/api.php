<?php

use App\Http\Controllers\Api\V1\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Api\V1\Admin\RestaurantManagementController;
use App\Http\Controllers\Api\V1\Admin\SettlementController as AdminSettlementController;
use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\GuardianStudentController;
use App\Http\Controllers\Api\V1\MealFeedbackController;
use App\Http\Controllers\Api\V1\NutritionController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PaymentWebhookController;
use App\Http\Controllers\Api\V1\Delivery\DeliveryDispatchController;
use App\Http\Controllers\Api\V1\Delivery\DriverDeliveryController;
use App\Http\Controllers\Api\V1\Restaurant\KitchenOperationsController;
use App\Http\Controllers\Api\V1\Restaurant\MenuItemController;
use App\Http\Controllers\Api\V1\Restaurant\RestaurantSettlementController;
use App\Http\Controllers\Api\V1\Restaurant\RestaurantStaffController;
use App\Http\Controllers\Api\V1\SchoolController;
use App\Http\Controllers\Api\V1\SchoolLocationController;
use App\Http\Controllers\Api\V1\SchoolMealPolicyController;
use App\Http\Controllers\Api\V1\SchoolShiftController;
use App\Http\Controllers\Api\V1\StudentController;
use Illuminate\Support\Facades\Route;

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'app' => config('app.name'),
        'timezone' => config('app.timezone'),
        'timestamp' => now()->toIso8601String(),
    ]);
});

// API Version 1
Route::prefix('v1')->group(function () {

    // Authentication & Authorization Domain (PB-001 to PB-006)
    Route::prefix('auth')->group(function () {
        // Public Auth
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/otp/request', [AuthController::class, 'requestOtp']);
        Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);

        // Authenticated Auth
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    // Public / Hybrid Catalog & Nutrition Screening (PB-015, PB-029)
    Route::prefix('catalog')->group(function () {
        Route::get('/meals', [CatalogController::class, 'meals']);
    });
    Route::get('/allergens', [CatalogController::class, 'allergens']);

    // Signed Payment Webhook Receiver (PB-042)
    Route::post('/webhooks/payments/{provider}', [PaymentWebhookController::class, 'handle']);

    // Admin User CRUD (PB-001 to PB-006)
    Route::prefix('admin/users')
        ->middleware(['auth:sanctum'])
        ->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->middleware('permission:users.view');
            Route::post('/', [AdminUserController::class, 'store'])->middleware('permission:users.create');
            Route::get('/{user}', [AdminUserController::class, 'show'])->middleware('permission:users.view');
            Route::patch('/{user}', [AdminUserController::class, 'update'])->middleware('permission:users.update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->middleware('permission:users.delete');
        });

    // Authenticated Business APIs
    Route::middleware('auth:sanctum')->group(function () {

        // Admin Merchant Management (PB-017, PB-018)
        Route::prefix('admin/restaurants')->group(function () {
            Route::get('/', [RestaurantManagementController::class, 'index'])->middleware('permission:restaurants.view');
            Route::patch('/{restaurant}/status', [RestaurantManagementController::class, 'updateStatus'])->middleware('permission:restaurants.approve,restaurants.suspend');
        });

        // Restaurant Staff & Menu Management (PB-019 to PB-028)
        Route::prefix('restaurant')->group(function () {
            // Staff management
            Route::get('/staff', [RestaurantStaffController::class, 'index']);
            Route::post('/staff', [RestaurantStaffController::class, 'store']);
            Route::delete('/staff/{staff}', [RestaurantStaffController::class, 'destroy']);

            // Menu Items with Immutable Versioning
            Route::get('/menu-items', [MenuItemController::class, 'index']);
            Route::post('/menu-items', [MenuItemController::class, 'store']);
            Route::get('/menu-items/{menu_item}', [MenuItemController::class, 'show']);
            Route::match(['put', 'patch'], '/menu-items/{menu_item}', [MenuItemController::class, 'update']);
            Route::delete('/menu-items/{menu_item}', [MenuItemController::class, 'destroy']);

            // Kitchen Production Queue & Status (PB-046 to PB-049)
            Route::get('/kitchen/queue', [KitchenOperationsController::class, 'queue']);
            Route::patch('/kitchen/schedules/status', [KitchenOperationsController::class, 'updateScheduleStatus']);
        });

        // Schools CRUD (PB-007 to PB-012)
        Route::prefix('schools')->group(function () {
            Route::get('/', [SchoolController::class, 'index']);
            Route::post('/', [SchoolController::class, 'store'])->middleware('permission:schools.create');
            Route::get('/{school}', [SchoolController::class, 'show']);
            Route::match(['put', 'patch'], '/{school}', [SchoolController::class, 'update']);
            Route::delete('/{school}', [SchoolController::class, 'destroy'])->middleware('permission:schools.delete');

            // Nested School Shifts
            Route::get('/{school}/shifts', [SchoolShiftController::class, 'index']);
            Route::post('/{school}/shifts', [SchoolShiftController::class, 'store']);

            // Nested School Locations
            Route::get('/{school}/locations', [SchoolLocationController::class, 'index']);
            Route::post('/{school}/locations', [SchoolLocationController::class, 'store']);

            // School Meal Policies
            Route::get('/{school}/meal-policy', [SchoolMealPolicyController::class, 'show']);
            Route::match(['put', 'patch'], '/{school}/meal-policy', [SchoolMealPolicyController::class, 'update']);

            // School Daily Meal Roster (PB-011)
            Route::get('/{school}/meal-roster', [SchoolController::class, 'mealRoster']);
        });

        // Direct School Shifts endpoints
        Route::prefix('school-shifts')->group(function () {
            Route::get('/', [SchoolShiftController::class, 'index']);
            Route::post('/', [SchoolShiftController::class, 'store']);
            Route::get('/{shift}', [SchoolShiftController::class, 'show']);
            Route::match(['put', 'patch'], '/{shift}', [SchoolShiftController::class, 'update']);
            Route::delete('/{shift}', [SchoolShiftController::class, 'destroy']);
        });

        // Direct School Locations endpoints
        Route::prefix('school-locations')->group(function () {
            Route::get('/', [SchoolLocationController::class, 'index']);
            Route::post('/', [SchoolLocationController::class, 'store']);
            Route::get('/{location}', [SchoolLocationController::class, 'show']);
            Route::match(['put', 'patch'], '/{location}', [SchoolLocationController::class, 'update']);
            Route::delete('/{location}', [SchoolLocationController::class, 'destroy']);
        });

        // Student Search & Admin CRUD (PB-013 to PB-016)
        Route::prefix('students')->group(function () {
            Route::get('/', [StudentController::class, 'index']);
            Route::post('/', [StudentController::class, 'store'])->middleware('permission:students.create');
            Route::get('/{student}', [StudentController::class, 'show']);
            Route::match(['put', 'patch'], '/{student}', [StudentController::class, 'update']);
            Route::delete('/{student}', [StudentController::class, 'destroy'])->middleware('permission:students.delete');
        });

        // Parent/Guardian Child Management APIs
        Route::prefix('guardian/students')->group(function () {
            Route::get('/', [GuardianStudentController::class, 'index']);
            Route::post('/', [GuardianStudentController::class, 'store']);
            Route::get('/{student}', [GuardianStudentController::class, 'show']);
            Route::put('/{student}/nutrition-profile', [GuardianStudentController::class, 'updateNutritionProfile']);
        });

        // Orders & 1-7 Day Meal Scheduling (PB-031 to PB-039)
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store'])->middleware('idempotency');
            Route::get('/{order}', [OrderController::class, 'show']);
            Route::post('/schedules/{schedule}/skip', [OrderController::class, 'skipSchedule']);
            Route::post('/schedules/{schedule}/cancel', [OrderController::class, 'cancelSchedule']);
        });

        // Payment Gateway Initiation & Tracking (PB-040 to PB-045)
        Route::prefix('payments')->group(function () {
            Route::post('/initiate', [PaymentController::class, 'initiate'])->middleware('idempotency');
            Route::get('/{payment}', [PaymentController::class, 'show']);
        });

        // Delivery Dispatching & Routing (PB-050 to PB-053)
        Route::prefix('delivery/batches')->group(function () {
            Route::get('/', [DeliveryDispatchController::class, 'index']);
            Route::post('/', [DeliveryDispatchController::class, 'store']);
        });

        // Driver Delivery Execution, Scans & POD (PB-050 to PB-056)
        Route::prefix('driver')->group(function () {
            Route::get('/batches/today', [DriverDeliveryController::class, 'todayBatches']);
            Route::post('/batches/{batch}/pickup', [DriverDeliveryController::class, 'pickupBatch']);
            Route::post('/stops/{stop}/verify-scan', [DriverDeliveryController::class, 'verifyScan']);
            Route::post('/stops/{stop}/complete', [DriverDeliveryController::class, 'completeStop']);
            Route::post('/stops/{stop}/exception', [DriverDeliveryController::class, 'recordException']);
        });

        // Merchant Settlements (PB-057 to PB-060)
        Route::prefix('admin/settlements')->middleware('permission:finance.settlements.view')->group(function () {
            Route::get('/', [AdminSettlementController::class, 'index']);
            Route::post('/generate', [AdminSettlementController::class, 'generate'])->middleware('permission:finance.settlements.generate');
            Route::get('/{settlement}', [AdminSettlementController::class, 'show']);
            Route::patch('/{settlement}/status', [AdminSettlementController::class, 'updateStatus'])->middleware('permission:finance.settlements.approve,finance.settlements.pay');
        });

        // Restaurant Portal Settlements View
        Route::prefix('restaurant/settlements')->group(function () {
            Route::get('/', [RestaurantSettlementController::class, 'index']);
            Route::get('/{settlement}', [RestaurantSettlementController::class, 'show']);
        });

        // Meal Feedback & Nutrition Consumption Logging (PB-030, PB-062 to PB-065)
        Route::prefix('nutrition')->group(function () {
            Route::post('/consumption-logs', [NutritionController::class, 'storeLog']);
        });
        Route::get('/guardian/students/{student}/nutrition-summary', [NutritionController::class, 'studentSummary']);

        Route::prefix('feedback/meals')->group(function () {
            Route::get('/', [MealFeedbackController::class, 'index']);
            Route::post('/{schedule}', [MealFeedbackController::class, 'store']);
        });
        Route::post('/orders/schedules/{schedule}/feedback', [MealFeedbackController::class, 'store']);

        // Operational & Management Reports (PB-069 to PB-074)
        Route::prefix('admin/reports')->middleware('permission:reports.view')->group(function () {
            Route::get('/operations-kpi', [AdminReportController::class, 'operationsKpi']);
            Route::get('/orders', [AdminReportController::class, 'orders']);
            Route::get('/school-delivery-reconciliation', [AdminReportController::class, 'schoolReconciliation']);
            Route::get('/restaurant-production', [AdminReportController::class, 'restaurantProduction']);
            Route::get('/nutrition-coverage', [AdminReportController::class, 'nutritionCoverage']);
        });
    });

    // Language / Localization Endpoints (For Next.js Frontend & Mobile App)
    Route::get('/translations/{locale?}', [\App\Http\Controllers\Web\LanguageController::class, 'apiTranslations']);
});
