<?php

use App\Http\Controllers\Google2FAController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use PragmaRX\Google2FALaravel\Google2FA;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/2fa/setup', [Google2FAController::class, 'enable2FA'])->name('2fa.setup');
    Route::post('/2fa/verify', [Google2FAController::class, 'verify2FA'])->name('2fa.verify');
});

// 2FA Verification Routes
Route::get('/2fa/verify', function () {
    return Inertia::render('auth/TwoFactorVerify');
})->name('2fa.verify.form');

Route::post('/2fa/validate', function (Request $request) {
    $request->validate(['code' => 'required|digits:6']);

    $user = User::find(session('2fa:user:id'));

    if (!$user) {
        return redirect()->route('login')->withErrors(['email' => 'Session expired, please log in again.']);
    }

    // Pass the $request object into the Google2FA constructor
    $google2fa = new Google2FA($request);

    if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
        Auth::login($user);
        session()->forget('2fa:user:id'); // Remove session key
        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors(['code' => 'Invalid authentication code.']);
})->name('2fa.validate');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
