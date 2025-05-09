<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();
        if ($user->isBanned()) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account has been banned.',
            ]);
        }
        if ($user->hasRole('client') && !$user->profile->approved_by) {
                Auth::logout();
                return redirect()->route('waiting-approval');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('clients-management.index');
        }
        if ($user->hasRole('manager')) {
            return redirect()->route('manager.manage-receptionists');
        }
        if ($user->hasRole('receptionist')) {
            return redirect()->route('clients-management.index');
        }
            return redirect()->route('home');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->intended(route('home', absolute: false));
    }
}
