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
        $redirectTo = $this->normalizeInternalRedirect($request->query('redirect'));

        if ($redirectTo) {
            session(['sso_intended_url' => $redirectTo]);
        }

        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()
                ->route('home')
                ->with('error', 'Login Google belum dikonfigurasi. Isi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET terlebih dahulu.');
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    // Callback setelah user klik "izinkan" di Google
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Login Google gagal: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Login dengan Google gagal, silakan coba lagi.');
        }

        if (! $googleUser->getEmail()) {
            return redirect()
                ->route('home')
                ->with('error', 'Akun Google tidak mengirimkan alamat email. Coba pilih akun Google lain.');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        $name = $googleUser->getName() ?: $googleUser->getNickname() ?: $googleUser->getEmail();

        if ($user) {
            $user->update([
                'name' => $user->name ?: $name,
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name' => $name,
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

    private function normalizeInternalRedirect(?string $redirectTo): ?string
    {
        if (! $redirectTo) {
            return null;
        }

        if (str_starts_with($redirectTo, url('/'))) {
            $parts = parse_url($redirectTo);
            $redirectTo = ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');
        }

        // Hanya izinkan path relatif internal (cegah open-redirect)
        if (str_starts_with($redirectTo, '/') && ! str_starts_with($redirectTo, '//')) {
            return $redirectTo;
        }

        return null;
    }
}
