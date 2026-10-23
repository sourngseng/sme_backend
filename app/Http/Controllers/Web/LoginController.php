<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the SchoolMeal Admin & Management login page.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request for web sessions.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ]);

        $login = $credentials['login'];
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Determine if login is email or phone
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Check user existence and status first
        $user = User::where($fieldType, $login)->first();

        if (! $user || ! Auth::attempt([$fieldType => $login, 'password' => $password], $remember)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials do not match our records.'],
            ]);
        }

        if ($user->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'login' => ["Your account status is currently {$user->status}. Please contact administrator."],
            ]);
        }

        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        // Redirect based on user role
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out successfully.');
    }

    /**
     * Redirect to the dedicated dashboard according to the user's primary role.
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        if ($user->hasRole('super_admin')) {
            return redirect()->intended('/admin/dashboard');
        }

        if ($user->hasRole('school_admin')) {
            return redirect()->intended('/school/dashboard');
        }

        if ($user->hasRole(['restaurant_staff', 'kitchen_staff'])) {
            return redirect()->intended('/kitchen/dashboard');
        }

        if ($user->hasRole('restaurant_owner')) {
            return redirect()->intended('/restaurant/dashboard');
        }

        if ($user->hasRole('driver')) {
            return redirect()->intended('/driver/dashboard');
        }

        if ($user->hasRole('finance')) {
            return redirect()->intended('/finance/dashboard');
        }

        return redirect()->intended('/dashboard');
    }
}
