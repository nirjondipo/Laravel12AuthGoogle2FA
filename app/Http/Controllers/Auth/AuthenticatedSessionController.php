<?php

namespace App\Http\Controllers\Auth;

use \Log;
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
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
    
        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ]);
        }
    
        $user = Auth::user(); // Get the authenticated user
        \Log::info('User logged in:', ['id' => Auth::id()]);
        
        if (!$user->google2fa_enabled) {
            return redirect()->route('2fa.setup'); // Redirect to setup page
        }
        if ($user->google2fa_enabled) {
            Auth::logout(); // Temporarily log them out
            session(['2fa:user:id' => $user->id]); // Store user ID in session
            return redirect()->route('2fa.verify.form'); // Redirect to 2FA page
        }
    
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
    }
    
    
    

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
