<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OtpRequestCodeRequest;
use App\Http\Requests\Auth\OtpVerifyRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Guardian;
use App\Models\OtpRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Register a new user account.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'password' => $validated['password'],
                'status' => 'active',
                'last_login_at' => now(),
            ]);

            $roleName = $validated['role'] ?? 'parent';
            $user->assignRole($roleName);

            // If registering as a parent, auto-initialize guardian profile
            if ($roleName === 'parent') {
                Guardian::firstOrCreate(['user_id' => $user->id]);
            }

            return $user;
        });

        $user->load(['roles.permissions']);
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * Authenticate user with email or phone and password.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $login = $validated['login'];

        $user = User::where('email', $login)
            ->orWhere('phone', $login)
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email/phone or password.',
                'error_code' => 'INVALID_CREDENTIALS',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => "Your account is {$user->status}. Please contact support.",
                'error_code' => 'ACCOUNT_INACTIVE',
            ], Response::HTTP_FORBIDDEN);
        }

        $user->update(['last_login_at' => now()]);
        $user->load(['roles.permissions']);

        $tokenName = $validated['device_name'] ?? 'auth-token';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Request an OTP code with strict Redis rate-limiting (max 3 per 10 mins).
     */
    public function requestOtp(OtpRequestCodeRequest $request): JsonResponse
    {
        $target = $request->validated('phone_or_email');
        $rateLimitKey = 'otp_request:' . strtolower($target);

        // Max 3 requests per 10 minutes (600 seconds)
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $secondsRemaining = RateLimiter::availableIn($rateLimitKey);

            return response()->json([
                'success' => false,
                'message' => "Too many OTP requests. Please wait {$secondsRemaining} seconds before trying again.",
                'error_code' => 'RATE_LIMIT_EXCEEDED',
                'retry_after_seconds' => $secondsRemaining,
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        RateLimiter::hit($rateLimitKey, 600);

        // Generate 6-digit numeric code
        $plainOtp = sprintf('%06d', random_int(100000, 999999));

        OtpRequest::create([
            'phone_or_email' => $target,
            'otp_hash' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP generated and dispatched successfully.',
            'expires_in_seconds' => 600,
            // Include OTP in debug/local mode for automated testing and client validation
            'debug_otp' => config('app.debug') ? $plainOtp : null,
        ], Response::HTTP_OK);
    }

    /**
     * Verify an OTP code and issue access token.
     */
    public function verifyOtp(OtpVerifyRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $target = strtolower($validated['phone_or_email']);
        $otp = $validated['otp'];

        $otpRecord = OtpRequest::where('phone_or_email', $target)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $otpRecord || $otpRecord->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired or was not requested.',
                'error_code' => 'OTP_EXPIRED',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! $otpRecord->verify($otp)) {
            $remaining = max(0, 5 - $otpRecord->attempts);

            return response()->json([
                'success' => false,
                'message' => "Invalid OTP code. {$remaining} attempts remaining.",
                'error_code' => 'INVALID_OTP',
                'attempts_remaining' => $remaining,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Find or create the user associated with this identifier
        $user = User::where('email', $target)
            ->orWhere('phone', $target)
            ->first();

        if (! $user) {
            $isEmail = filter_var($target, FILTER_VALIDATE_EMAIL);

            $user = DB::transaction(function () use ($target, $isEmail, $validated) {
                $user = User::create([
                    'full_name' => $validated['full_name'] ?? ($isEmail ? explode('@', $target)[0] : 'User ' . substr($target, -4)),
                    'email' => $isEmail ? $target : null,
                    'phone' => ! $isEmail ? $target : null,
                    'password_hash' => null,
                    'status' => 'active',
                    'last_login_at' => now(),
                ]);

                $user->assignRole('parent');
                Guardian::firstOrCreate(['user_id' => $user->id]);

                return $user;
            });
        } else {
            if ($user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => "Your account is {$user->status}. Please contact support.",
                    'error_code' => 'ACCOUNT_INACTIVE',
                ], Response::HTTP_FORBIDDEN);
            }

            $user->update(['last_login_at' => now()]);
        }

        $user->load(['roles.permissions']);
        $tokenName = $validated['device_name'] ?? 'otp-token';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Get the authenticated user's profile and permissions.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['roles.permissions', 'guardian', 'driver']);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ], Response::HTTP_OK);
    }

    /**
     * Invalidate the current session access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ], Response::HTTP_OK);
    }
}
