<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QRCode;
use App\Models\User;

class Google2FAController extends Controller
{
    public function enable2FA()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();
    
        if (!$user->google2fa_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->google2fa_secret = $secret;
            $user->save();
        } else {
            $secret = $user->google2fa_secret;
        }

        // Store user ID for session-based verification
      //  session(['2fa:user:id' => $user->id]);

        // Generate QR Code
        $qrCodeUrl = 'data:image/svg+xml;base64,' . base64_encode(QRCode::size(200)->generate(
            $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret)
        ));
        
        return view('auth.2fa_setup', compact('qrCodeUrl', 'secret'));
    }

    public function verify2FA(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);
        $google2fa = new Google2FA();
    
        $user = Auth::user(); // Directly get authenticated user
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User session expired. Please login again.']);
        }
    
        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            // Enable 2FA for the user
            $user->google2fa_enabled = true;
            $user->save();
    
            session()->forget('2fa:user:id');
            Auth::login($user);
    
            return redirect()->intended(route('dashboard', absolute: false))->with('success', '2FA setup completed successfully.');
        }
    
        return back()->withErrors(['code' => 'Invalid authentication code']);
    }
    
}
