<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/admin/login')->withErrors(['email' => 'Gagal terhubung dengan Google.']);
        }

        $allowedDomain = env('SSO_ALLOWED_DOMAIN', '@perusahaan.com');

        // Validasi Domain Email
        if (!Str::endsWith($googleUser->email, $allowedDomain)) {
            // Tolak akses jika domain tidak sesuai
            return redirect('/admin/login')->withErrors([
                'email' => "Akses ditolak. Harap gunakan email dengan domain {$allowedDomain}."
            ]);
        }

        // Cari atau buat user (Auto-Register)
        $user = User::where('email', $googleUser->email)
                    ->orWhere('provider_id', $googleUser->id)
                    ->first();

        if (!$user) {
            // User tidak ada, lakukan Auto-Register
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => null, // Password kosong
                'provider_name' => 'google',
                'provider_id' => $googleUser->id,
            ]);

            // Assign role otomatis menggunakan Spatie Permission
            $defaultRole = env('SSO_DEFAULT_ROLE', 'panel_user');
            if ($defaultRole) {
                $user->assignRole($defaultRole);
            }
        } else {
            // Jika user sudah ada tapi provider belum diset, update datanya
            if (!$user->provider_id) {
                $user->update([
                    'provider_name' => 'google',
                    'provider_id' => $googleUser->id,
                ]);
            }
        }

        // Autentikasi user dan redirect ke panel Filament
        Auth::login($user);
        
        return redirect()->intended(route('filament.admin.pages.dashboard'));
    }
}
