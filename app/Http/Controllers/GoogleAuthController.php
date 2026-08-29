<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    protected function hasValidGoogleConfiguration(): bool
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        return !blank($clientId)
            && !blank($clientSecret)
            && !str_contains(strtolower((string) $clientId), 'your_google_client_id_here')
            && !str_contains(strtolower((string) $clientSecret), 'your_google_client_secret_here');
    }

    public function redirect()
    {
        if (!$this->hasValidGoogleConfiguration()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google sign-in is not configured. Add a valid Google client ID and secret, and use the redirect URI http://127.0.0.1:8000/auth/google/callback.',
            ]);
        }

        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        if (!$this->hasValidGoogleConfiguration()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google sign-in is not configured yet. Please add the valid OAuth credentials in .env.',
            ]);
        }

        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            return redirect()->route('login')->withErrors([
                'email' => 'No account found for this Google email. Please request access first.',
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
