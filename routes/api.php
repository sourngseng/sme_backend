<?php

use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GuardianStudentController;
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

    // School Admin & Platform Admin APIs (PB-007 to PB-016)
    Route::middleware('auth:sanctum')->group(function () {

        // Schools CRUD
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

        // Student Search & Admin CRUD
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
    });

    // Language / Localization Endpoints (For Next.js Frontend & Mobile App)
    Route::get('/translations/{locale?}', [\App\Http\Controllers\Web\LanguageController::class, 'apiTranslations']);
});
