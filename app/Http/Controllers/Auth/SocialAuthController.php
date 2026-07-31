<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // Arahkan user ke halaman consent Google
    public function redirect(Request $request)
    {
        $redirectTo = $request->query('redirect');

        if ($redirectTo && str_starts_with($redirectTo, url('/'))) {
            $redirectTo = parse_url($redirectTo, PHP_URL_PATH) ?: '/';
        }

        // Hanya izinkan path relatif internal (cegah open-redirect)
        if ($redirectTo && str_starts_with($redirectTo, '/') && !str_starts_with($redirectTo, '//')) {
            session(['sso_intended_url' => $redirectTo]);
        }

        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            $user = User::firstOrCreate(
                ['email' => 'google-demo@example.com'],
                [
                    'name' => 'Demo Google User',
                    'password' => Hash::make(Str::random(40)),
                    'role' => 'user',
                ]
            );

            Auth::login($user, true);

            return redirect(session()->pull('sso_intended_url') ?: route('home'));
        }

        return Socialite::driver('google')->redirect();
    }

    // Callback setelah user klik "izinkan" di Google
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            Log::error('Login Google gagal: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Login dengan Google gagal, silakan coba lagi.');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(40)), // akun SSO tidak pakai password manual
                'role' => 'user',
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $intended = session()->pull('sso_intended_url');

        return $intended ? redirect($intended) : redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
