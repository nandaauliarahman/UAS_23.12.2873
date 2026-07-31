<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('organizer.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'tenant_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'pic_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $tenant = DB::transaction(function () use ($data) {
            $baseSlug = Str::slug($data['tenant_name']);
            $slug = $baseSlug;
            $i = 1;
            while (Tenant::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }

            $tenant = Tenant::create([
                'name' => $data['tenant_name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'is_approved' => false,
            ]);

            $user = User::create([
                'name' => $data['pic_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'organizer',
                'tenant_id' => $tenant->id,
            ]);

            $tenant->update(['owner_id' => $user->id]);

            return $tenant;
        });

        Auth::login($tenant->owner);

        return redirect()->route('organizer.dashboard')
            ->with('success', 'Pendaftaran berhasil! Akun Anda menunggu persetujuan dari Superadmin sebelum bisa publish event.');
    }

    public function showLogin()
    {
        return view('organizer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials) && Auth::user()->role === 'organizer') {
            $request->session()->regenerate();
            return redirect()->route('organizer.dashboard');
        }

        Auth::logout();

        return back()->withErrors(['email' => 'Email/password salah, atau akun ini bukan akun Penyelenggara.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('organizer.login');
    }
}