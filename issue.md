# 📋 [Task Planning] Panduan Implementasi Teknis Login SSO Google pada Filament

## 🎯 Ringkasan Tugas
Dokumen ini merupakan panduan teknis *step-by-step* yang dirancang khusus untuk programmer junior. Fitur yang akan dibangun adalah integrasi Single Sign-On (SSO) menggunakan akun Google melalui package `laravel/socialite` pada otentikasi standar panel admin Filament. 

Terdapat dua regulasi khusus dalam implementasi ini:
1. **Pembatasan Domain Email**: Hanya email dengan domain tertentu (misalnya `@perusahaan.com`) yang diizinkan masuk atau mendaftar.
2. **Auto-Register & Role Assignment**: Jika pengguna lolos validasi domain dan belum terdaftar, akun akan otomatis dibuat (auto-register) dan otomatis diberikan hak akses (role) menggunakan Spatie Permission.

---

## ⚠️ User Review Required

> [!WARNING]
> **Kredensial Google OAuth & Konfigurasi:**
> Anda wajib membuat project di Google Cloud Console, mengatur OAuth Consent Screen, dan mendaftarkan URL callback. Pastikan Anda memiliki `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET`.

> [!IMPORTANT]
> **Konfigurasi Domain & Role:**
> Di dalam skrip di bawah ini, saya menggunakan `@perusahaan.com` sebagai batasan domain dan role `panel_user` sebagai role *default*. **Silakan sesuaikan konfigurasi ini di `.env` nantinya atau perbarui *hardcode* dalam controller jika dibutuhkan.** (Mohon setujui pendekatan ini).

---

## 🛠️ Langkah-Langkah Implementasi

Berikut adalah panduan detail yang wajib Anda ikuti secara berurutan.

### 1. Prasyarat & Instalasi 📦

Kita membutuhkan package resmi `laravel/socialite` untuk menangani protokol OAuth Google.

#### [NEW] Perintah Terminal
Buka terminal/command prompt pada direktori *root* project Anda dan jalankan perintah berikut:
```bash
composer require laravel/socialite
```

### 2. Konfigurasi Lingkungan 🌍

Konfigurasi untuk menyimpan *keys* dan *secrets* secara aman.

#### [MODIFY] File `.env`
Tambahkan baris berikut di bagian paling bawah pada file `.env` (dan `.env.example` untuk dokumentasi tim):
```env
GOOGLE_CLIENT_ID="isi_client_id_dari_google_console"
GOOGLE_CLIENT_SECRET="isi_client_secret_dari_google_console"
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
# Konfigurasi Tambahan untuk Fitur
SSO_ALLOWED_DOMAIN="@perusahaan.com"
SSO_DEFAULT_ROLE="panel_user"
```

#### [MODIFY] File `config/services.php`
Tambahkan driver `google` ke dalam array konfigurasi:
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

### 3. Arsitektur Database 🗄️

Kita harus memodifikasi tabel `users` untuk menyimpan ID *provider* dari Google agar login selanjutnya tidak harus menebak password.

#### [NEW] Pembuatan Migration
Jalankan perintah ini di terminal:
```bash
php artisan make:migration add_sso_columns_to_users_table
```

#### [MODIFY] File Migration yang Baru Dibuat
Buka file migration di `database/migrations/xxxx_xx_xx_xxxxxx_add_sso_columns_to_users_table.php` dan sesuaikan menjadi:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider_name')->nullable()->after('remember_token');
            $table->string('provider_id')->nullable()->after('provider_name');
            $table->string('password')->nullable()->change(); // Password diizinkan kosong karena via SSO
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider_name', 'provider_id']);
            $table->string('password')->nullable(false)->change(); // Kembalikan ke asal jika rollback
        });
    }
};
```
Jalankan migrasi setelah kode di atas disimpan:
```bash
php artisan migrate
```

#### [MODIFY] Model `app/Models/User.php`
Tambahkan `$fillable` agar Eloquent mengizinkan insert data SSO, dan pastikan Spatie `HasRoles` terpasang.
```php
// Tambahkan baris ini ke dalam array $fillable
protected $fillable = [
    'name',
    'email',
    'password',
    'provider_name', // <-- Tambahan
    'provider_id',   // <-- Tambahan
];
```

### 4. Logika Kode (Backend) ⚙️

Pembuatan routing dan Controller untuk memproses request otentikasi, validasi domain, pembuatan user, dan *role assignment*.

#### [MODIFY] `routes/web.php`
Tambahkan route untuk SSO:
```php
use App\Http\Controllers\Auth\SocialiteController;

Route::get('/auth/google', [SocialiteController::class, 'redirect'])->name('sso.google.login');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])->name('sso.google.callback');
```

#### [NEW] Buat Controller Baru `app/Http/Controllers/Auth/SocialiteController.php`
Buat file ini (atau gunakan `php artisan make:controller Auth/SocialiteController`) dan gunakan logika berikut:

```php
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
```

### 5. Modifikasi Antarmuka (UI/UX) 🎨

Tambahkan tombol untuk memicu otentikasi Google SSO tanpa harus menimpa (override) seluruh halaman Filament bawaan.

#### [NEW] Buat File `resources/views/filament/auth/sso-login-button.blade.php`
Buat file Blade baru dan isikan kode berikut. Desain menyesuaikan standar *tailwind* bawaan Filament.
```html
<div class="mt-4">
    <a href="{{ route('sso.google.login') }}"
       class="w-full flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition duration-200">
        <!-- SVG Logo Google -->
        <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Login dengan Google
    </a>
</div>
```

#### [MODIFY] Modifikasi `app/Providers/Filament/AdminPanelProvider.php`
Daftarkan (render hook) komponen view tersebut agar muncul di halaman Auth Login Filament (biasanya di bawah form login default).
```php
// Tambahkan namespace ini di bagian atas:
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

// Lalu di dalam method boot():
public function boot(): void
{
    // Render tombol SSO di bawah form Auth
    FilamentView::registerRenderHook(
        PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
        fn (): string => view('filament.auth.sso-login-button')->render(),
    );
}
```

---

## 🧪 Skenario Pengujian (Testing)

Setiap implementasi ini wajib diverifikasi menggunakan langkah-langkah di bawah ini:

### 1. Uji Validasi Domain Email Gagal
- **Langkah:** Klik *Login dengan Google* dan pilih akun dengan email di luar domain `@perusahaan.com` (misal `@gmail.com` biasa).
- **Ekspektasi (Pass):** Sistem menolak login, pengguna dikembalikan ke halaman login admin dengan pesan *error* "Akses ditolak. Harap gunakan email dengan domain...". Data di tabel `users` tidak bertambah.

### 2. Uji Auto-Register (Valid Domain)
- **Langkah:** Pilih akun Google dengan email domain `@perusahaan.com` yang **belum pernah** terdaftar di database.
- **Ekspektasi (Pass):** Anda otomatis ter-redirect ke dashboard utama.
- **Validasi Teknis:** Cek tabel `users`, pastikan *record* baru tercipta dengan password NULL, `provider_name` google, dan cek tabel relasi role Spatie bahwa user baru ini otomatis memiliki role `panel_user` (atau sesuai `SSO_DEFAULT_ROLE`).

### 3. Uji Login Berulang (Existing User)
- **Langkah:** Log out, lalu login kembali menggunakan akun `@perusahaan.com` yang sama.
- **Ekspektasi (Pass):** Login berhasil dengan lancar dan diarahkan ke dashboard. Tidak ada duplikasi data (row) di tabel `users`.

### 4. Uji Integrasi Akun yang Sudah Terdaftar Manual
- **Langkah:** Buat user manual dengan email `@perusahaan.com` tanpa provider SSO, namun memiliki password. Kemudian coba klik "Login dengan Google" menggunakan akun tersebut.
- **Ekspektasi (Pass):** Sistem otomatis mendeteksi email tersebut, meng-update kolom `provider_name` dan `provider_id`, lalu memperbolehkan login ke dashboard tanpa menimpa data lama.
