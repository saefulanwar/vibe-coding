# 📋 [Task Planning] Implementasi Kontrol Akses (Role, Permission, & Impersonate) dengan Filament v5

## 🎯 Ringkasan Tugas
Mengimplementasikan sistem manajemen hak akses pengguna (*Role-Based Access Control*) menggunakan paket **Filament Shield**, membuat data uji coba (*seeder*), dan mengaktifkan fitur penyamaran identitas (*user impersonation*) pada panel admin Filament 5.

---

## 🛠️ Spesifikasi & Langkah Implementasi

### 1. Konfigurasi Role & Permission (Filament Shield)
* **Tugas:** Integrasikan `FilamentShieldPlugin` ke dalam proyek Filament v5.
* **Instruksi Detail:**
  * Install paket `bezhansalleh/filament-shield` yang kompatibel dengan Filament v5 via Composer.
  * Daftarkan `FilamentShieldPlugin::make()` pada file konfigurasi panel admin (`AdminPanelProvider.php`).
  * Jalankan perintah instalasi wizard: `php artisan shield:install` untuk mempublikasikan konfigurasi dan migrasi database.
  * Pastikan kebijakan keamanan (*Policies*) otomatis digenerasikan untuk semua *Resources* yang ada saat ini.

### 2. Pembuatan Data Sampel Pengguna (Database Seeder)
* **Tugas:** Buat atau perbarui file `DatabaseSeeder.php` untuk menyediakan akun uji coba siap pakai.
* **Instruksi Detail:**
  * Buat minimal 3 akun dengan kombinasi *Role* yang berbeda (Contoh: **Super Admin**, **Admin Fakultas**, dan **Member**).
  * Pastikan setiap akun otomatis dikaitkan (*attached*) dengan *Role* yang sesuai setelah proses pembuatan *user*.
  * Akun harus memiliki alamat email dan kata sandi statis yang aman agar memudahkan proses pengujian internal.

### 3. Implementasi Fitur Penyamaran Pengguna (Impersonate User)
* **Tugas:** Izinkan pengguna dengan level akses tinggi (misal: Super Admin) untuk masuk ke panel sebagai pengguna lain tanpa mengetahui kata sandi mereka.
* **Instruksi Detail:**
  * Gunakan plugin pihak ketiga (seperti `stephenjude/filament-impersonate`) atau manfaatkan fitur aksi kustom bawaan Filament.
  * Tempatkan tombol "Impersonate" pada tabel manajemen *User* (`UserResource/Pages/ListUsers.php` atau `EditUser.php`).
  * **Aturan Keamanan SANGAT KETAT:** Terapkan kondisi *can* atau *policy* agar tombol Impersonate **hanya muncul dan hanya bisa dieksekusi** oleh pengguna yang memiliki *role* `Super Admin`. Pastikan *Member* biasa tidak bisa menyamar menjadi user lain.

---

## 🧪 Kriteria Penerimaan (Acceptance Criteria)
* [ ] Perintah `php artisan db:seed` berjalan sukses tanpa error dan menghasilkan user sampel yang tepat.
* [ ] Menu manajemen *Roles* dan *Permissions* dari Shield muncul dan berfungsi penuh di sidebar admin.
* [ ] Tombol "Impersonate" terbukti muncul hanya pada akun berwenang, dan ketika diklik, panel admin berhasil berubah peran menjadi target user dengan sukses.
* [ ] Terdapat tombol/notifikasi yang jelas untuk kembali ke akun asli (*Leave Impersonation*).
