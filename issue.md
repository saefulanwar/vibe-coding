# Issue: Implementasi Dashboard Multi-Role dan Sistem Otorisasi (RBAC)

## Deskripsi Tugas
Buatlah implementasi halaman dashboard yang menarik secara visual dan responsif. Sistem ini harus mendukung tiga role pengguna (Member, Superadmin, dan Admin Fakultas) dengan ketentuan routing, penyajian data pendapatan, serta pembatasan akses (Role-Based Access Control) yang ketat.

Dokumen ini dirancang sebagai panduan teknis yang siap dieksekusi oleh programmer atau model LLM pengembang.

---

## Kriteria Penerimaan (Acceptance Criteria)

### 1. Dashboard Member (Halaman Default)
* **URL Target:** `/dashboard`
* **Hak Akses:** Dapat diakses oleh user dengan role `Member`.
* **Ketentuan Tampilan:**
    * Menjadi halaman default setelah login bagi user biasa.
    * Desain modern, bersih, dan menarik (menyediakan metrik umum atau ringkasan aktivitas member).

### 2. Dashboard Superadmin
* **URL Target:** `/superadmin/dashboard` (atau kondisional di `/dashboard` dengan pengecekan role)
* **Hak Akses:** Hanya dapat diakses oleh `Superadmin`.
* **Ketentuan Data & Tampilan:**
    * Menampilkan metrik **Total Pendapatan Global**.
    * Menampilkan grafik atau tabel ringkasan **jumlah pendapatan dari masing-masing unit** (seluruh unit yang terdaftar di sistem).
    * Menyediakan filter waktu (opsional, misal: bulanan/tahunan).

### 3. Dashboard Admin Fakultas / Unit
* **URL Target:** `/faculty/dashboard` (atau kondisional di `/dashboard` dengan pengecekan role)
* **Hak Akses:** Hanya dapat diakses oleh `Admin Fakultas`.
* **Ketentuan Data & Tampilan:**
    * Menampilkan **jumlah pendapatan spesifik dari fakultas atau unit** tempat admin tersebut ditugaskan.
    * Admin Fakultas **tidak boleh** melihat data pendapatan dari fakultas atau unit lain.

### 4. Keamanan & Batasan Akses (RBAC)
* Terapkan **Middleware Otorisasi** di tingkat route/endpoint.
* Jika user mencoba mengakses halaman dashboard yang bukan haknya (misal: Member mengakses halaman Superadmin):
    * Sistem harus menolak akses secara mutlak.
    * Kembalikan respons `HTTP 403 Forbidden` atau lakukan *auto-redirect* ke halaman `/dashboard` disertai dengan pesan *error flash notification*.
* Pastikan proteksi ini berlaku baik di sisi Frontend (routing guard) maupun Backend (API security).

## Standar Kualitas UI/UX
* Gunakan komponen kartu (cards) untuk ringkasan angka transaksi/pendapatan.
* Gunakan representasi visual seperti grafik lingkaran (Pie Chart) atau batang (Bar Chart) untuk visualisasi pendapatan per unit pada dashboard Superadmin.
* Pastikan layout fully-responsive (bagus diakses via Mobile maupun Desktop).
