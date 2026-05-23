# Feature: Sertifikat Online Glacier, Integrasi Kontrol Kelayakan & Sistem Antrean (Queue)

## Rangkuman Langkah-Langkah Praktis

**1. Arsitektur Pembuatan & Tampilan (Generation)**
* **Pemisahan Desain & Data:** Pisahkan antara Desain Template (HTML + CSS) dan Data Dinamis (Nama, Judul Kelas, UUID) agar template sertifikat dinamis, mudah dikelola, dan siap dikirim ke API SiAgen UNY menggunakan `tag_koordinat`.
* **Gunakan Renderer Modern:** Manfaatkan library berbasis Chromium seperti Spatie Browsershot di Laravel agar template sertifikat dapat didesain menggunakan Tailwind CSS penuh dengan hasil cetak PDF yang tajam.
* **Antisipasi Nama Panjang:** Terapkan CSS Flexbox atau dynamic font-sizing via Alpine.js agar ukuran huruf otomatis mengecil secara proporsional jika nama lengkap siswa terlalu panjang beserta gelarnya.
* **Penyimpanan On-Demand (Hemat Storage):** Jangan langsung merender PDF saat siswa lulus. Simpan metadatanya saja di database, lalu render file fisik PDF secara on-demand hanya ketika proses verifikasi disetujui atau tombol unduh ditekan.

**2. Kontrol Manajemen, Kredensial, & Antrean**
* **Filter Hak Akses Wilayah:** Manfaatkan Filament Shield agar Admin Unit (Fakultas/Instansi) hanya dapat melihat dan menyaring daftar antrean siswa yang berada di bawah naungan unit mereka sendiri.
* **Form Input Kredensial TTE (Modal Form):** Gunakan form modal di Filament v5 untuk meminta NIK dan Passphrase langsung dari Admin Unit sesaat sebelum proses signing dilakukan.
* **Enkripsi Payload Sensitif:** Amankan data Passphrase yang dikirim ke Laravel Queue menggunakan fasad `Crypt` Laravel agar kredensial TTE tidak tersimpan dalam bentuk teks polos (plaintext) di database antrean kerja.
* **Pemrosesan Latar Belakang (Asynchronous Job):** Masukkan daftar siswa yang lolos seleksi ke antrean kerja (`ProcessCertificateTteJob`) agar proses render PDF dan penembakan API TTE dikerjakan satu per satu di latar belakang secara stabil tanpa memicu browser timeout.

**3. Keamanan & Validasi Keaslian Konten**
* **Generasikan Serial Unik (UUID):** Hindari ID berurutan. Gunakan kode serial berbasis UUID (contoh: `CERT-2026-XXXX-XXXX`) untuk meminimalkan risiko manipulasi URL.
* **Sematkan QR Code Dinamis:** Pasang QR Code publik di pojok sertifikat yang langsung mengarah ke sistem penjamin keaslian internal platform Glacier.
* **Sediakan Halaman Verifikasi Publik:** Sediakan halaman khusus (unauthenticated) agar pihak eksternal (seperti HRD) dapat memvalidasi nama, judul kompetensi, tanggal lulus, dan keabsahan sertifikat.
* **Kunci Data Profil (Data Freezing):** Begitu Admin Unit menekan tombol setuju/generate, kunci nama lengkap siswa untuk sertifikat tersebut. Perubahan nama akun di kemudian hari tidak akan memengaruhi sertifikat yang sudah sah terbit.

**4. Integrasi Hukum (TTE SiAgen UNY)**
* **Otomatisasi Pemicu Kelulusan:** Gunakan Context Listener/Webhook dari Moodle saat progress belajar mencapai 100% untuk mendaftarkan nama siswa ke daftar tunggu dasbor Filament Admin Unit.
* **Gunakan Penanda Teks (tag_koordinat):** Letakkan karakter khusus tersembunyi (seperti `#` dengan warna transparan/`text-transparent`) pada area tanda tangan di template HTML agar server TTE SiAgen UNY dapat mendeteksi koordinat pikselnya secara otomatis tanpa takut meleset.
* **Amankan Koneksi API:** Implementasikan pengiriman file PDF mentah menggunakan `CURLFile` atau HTTP Client Laravel, kirim melalui endpoint khusus dengan autentikasi API Key.

---

## Alur Sistem (Flowchart)

```text
[Siswa Lulus di Moodle/Glacier] 
               │
               ▼
[Data Masuk ke Antrean Dasbor Filament Admin Unit]
               │
               ▼
[Admin Unit Pilih Siswa via Checkbox -> Klik "Generate & TTE"] 
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│  [BARU] Muncul Modal Pop-up: Admin Input NIK & Passphrase   │
└─────────────────────────────────────────────────────────────┘
               │
               ▼
[Sistem Mengenkripsi Passphrase & Melempar Job ke Laravel Queue] 
               │
               ▼
[Job Background: Mendekripsi Passphrase & Merender HTML ke PDF Mentah] 
               │
               ▼
[Job Background: Kirim PDF + NIK + Passphrase ke API SiAgen UNY via HTTP POST] 
               │
               ▼
[Server SiAgen Menimpa 'tag_koordinat' dengan Visual QR Code TTE Resmi] 
               │
               ▼
[Sertifikat Sah Disimpan ke Storage & Kredensial TTE Terhapus dari Memori]
```

---

## 🛠️ Panduan Implementasi Teknis (Untuk Programmer)

Bagian ini merincikan tahapan teknis dari awal hingga akhir untuk mengimplementasikan fitur di atas. Kerjakan secara berurutan.

### Fase 1: Persiapan Database & Manajemen Template
Anda perlu membuat tabel untuk menyimpan template sertifikat agar unit atau admin bisa mengganti gambar latar belakang atau mengatur tata letak teks tanpa menyentuh kode program.

1. **Buat Migration `certificate_templates` (Desain Database di Filament v5):**
   * `id` (Primary Key)
   * `title` (Nama Template, misal: "Template Kelas Kompetensi Resmi")
   * `background_image` (Path file gambar mentah sertifikat kosongan/border)
   * `font_color` (Warna teks utama, default: #000000)
   * `tag_koordinat` (Karakter penanda TTE, default: `#`)
   * `content_html` (Struktur teks opsional jika letak teks ingin dinamis)

2. **Buat Migration `certificates`:**
   * `id` (UUID primer, format string 36 char atau native UUID).
   * `user_id`, `course_id`, `unit_id` (untuk filter Admin Unit).
   * `template_id` (Foreign key ke tabel `certificate_templates`).
   * `student_name_snapshot`, `course_title_snapshot`, `completed_at` (*Data freezing*).
   * `status` (enum: `pending`, `processing`, `completed`, `failed`).
   * `file_path` (nullable, diisi saat PDF dari SiAgen berhasil didapat).

3. **Setup Model:** Pastikan `Certificate` menggunakan trait `HasUuids` dan memiliki relasi `belongsTo` ke `CertificateTemplate`.

### Fase 2: Integrasi Webhook Moodle (Trigger Awal)
1. **Buat Endpoint Webhook:** Buat route API (`/api/webhooks/moodle/course-completed`) yang menerima payload dari Moodle.
2. **Validasi & Simpan:** Saat payload diterima (siswa lulus 100%), buat record baru di tabel `certificates` dengan status `pending` dan isi kolom `unit_id` sesuai dengan unit asal siswa tersebut.

### Fase 3: Pembuatan Dashboard Filament (Admin Unit)

**Pembaruan Alur Generate Sertifikat:**
1. **Pengaturan Template di Level Kursus (Course):**
   * Tambahkan konfigurasi `Sertifikat` di form **Courses**. Admin dapat memilih **Template Sertifikat** dan mencentang opsi **Gunakan Tanda Tangan Elektronik (TTE SiAgen)** atau tanpa TTE (menggunakan template dengan tanda tangan yang sudah tercetak).
2. **Generate Sertifikat via Enrollments (Peserta):**
   * Di halaman detail *Course*, akses tab **Peserta (Enrollments)**.
   * Pilih siswa yang telah lulus menggunakan *checkbox*.
   * Jalankan **Bulk Action "Generate Sertifikat"**.
   * Jika kursus mewajibkan TTE, sistem akan memunculkan modal meminta NIK dan Passphrase, kemudian memprosesnya dengan `ProcessCertificateTteJob`.
   * Jika tanpa TTE, sistem akan langsung menjalankan `ProcessCertificateJob` yang me-render PDF secara instan menggunakan template pilihan dan mengubah status ke `completed` tanpa menembak ke API SiAgen.
3. **Menerima Sertifikat:**
   * Siswa dapat mengunduh sertifikat mereka (yang telah tergenerate) melalui portal pembelajaran masing-masing atau halaman verifikasi publik.
4. **Enkripsi Kredensial di Action Controller:**
   * Saat modal disubmit, tangkap `$data['nik']` dan `$data['passphrase']`.
   * Enkripsi passphrase: `$encryptedPassphrase = Crypt::encryptString($data['passphrase']);`.

### Fase 4: Laravel Queue & Background Job
1. **Buat Job Class (`ProcessCertificateTteJob`):**
   * Constructor menerima: Model `Certificate`, `$nik`, dan `$encryptedPassphrase`.
   * Dispatch job ini di Action Filament (Fase 3).
2. **Logika di dalam Job (`handle()` method):**
   * Ubah status sertifikat menjadi `processing`.
   * Dekripsi passphrase: `$passphrase = Crypt::decryptString($this->encryptedPassphrase);`.
   * Lakukan *Data Freezing*: Ambil nama user saat ini, simpan ke `student_name_snapshot`. Update record.

### Fase 5: Implementasi Desain Template (HTML & Tailwind CSS)
Gunakan pendekatan HTML biasa yang dibungkus dengan ukuran kertas standar sertifikat (biasanya A4 Landscape: 297 x 210 mm).
Berikut adalah contoh file Blade template (`resources/views/pdf/certificate.blade.php`) yang sudah ramah terhadap converter PDF dan menyembunyikan `tag_koordinat` secara rapi:

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat {{ $user_name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-white">

    <div class="relative w-[297mm] h-[210mm] overflow-hidden bg-cover bg-center flex flex-col justify-between p-16" 
         style="background-image: url('{{ asset('storage/' . $template->background_image) }}');">
        
        <div class="text-center mt-12">
            <p class="text-sm tracking-widest text-slate-500 uppercase font-semibold">Sertifikat Kelulusan</p>
            <p class="text-xs text-slate-400 mt-1">Nomor: CERT-{{ $uuid }}</p>
        </div>

        <div class="text-center flex-grow flex flex-col justify-center px-20">
            <p class="text-base text-slate-600">Diberikan Kepada:</p>
            
            <h1 class="text-3xl font-extrabold text-slate-900 my-4 tracking-tight leading-tight">
                {{ $user_name }}
            </h1>
            
            <p class="text-sm text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Atas kelulusannya pada kelas kompetensi <span class="font-bold text-slate-900">"{{ $course_title }}"</span> 
                yang diselenggarakan oleh <span class="font-semibold text-slate-800">{{ $unit_name }}</span> 
                pada platform Glacier LMS.
            </p>
        </div>

        <div class="flex justify-between items-end px-12 pb-6">
            <div class="text-left">
                <div class="p-2 bg-white border border-slate-100 rounded-lg inline-block shadow-xs">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=65x65&data={{ url('/verify/'.$uuid) }}" alt="Verify">
                </div>
                <p class="text-[9px] text-slate-400 mt-1">Pindai untuk verifikasi keaslian</p>
            </div>

            <div class="text-center relative min-w-[200px]">
                <p class="text-xs text-slate-500 mb-14">Yogyakarta, {{ now()->translatedFormat('d F Y') }}</p>
                
                <div class="absolute left-1/2 top-4 -translate-x-1/2 text-transparent select-none pointer-events-none">
                    {{ $template->tag_koordinat }}
                </div>

                <div class="border-t border-slate-300 pt-1.5">
                    <p class="text-xs font-bold text-slate-900">{{ $signer_name }}</p>
                    <p class="text-[10px] text-slate-400">{{ $signer_position }}</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
```
**Alur Eksekusi Render:** Saat sistem (melalui Job) akan memproses sertifikat, sistem akan menggabungkan data dinamis siswa ke dalam template di atas dan mengubahnya menjadi PDF mentah (`$tempPdfPath`) menggunakan library seperti Spatie Browsershot, lalu disiapkan untuk dikirim ke API SiAgen.

### Fase 6: Penembakan API TTE SiAgen UNY
1. **Siapkan cURL Request (Tetap di dalam Job):**
   * Gunakan HTTP Client Laravel (`Http::withHeaders(...)`) atau cURL murni.
   * Arahkan request dengan method `POST` ke endpoint: `https://siagen.uny.ac.id/tte-rest/pdf`.
   * Tambahkan autentikasi API Key di bagian Header: `key: Lw_oJ3KQomQnh_eT29Ep9Li3ybDpiPrY`.
   * Gunakan format body `multipart/form-data` (`asMultipart()` jika menggunakan Http Client Laravel).
   * Susun payload form-data sesuai spesifikasi:
     * `nik`: berisikan data NIK dari input Admin (contoh di Postman: `1111`).
     * `file`: attach file PDF sementara (menggunakan path `$tempPdfPath`).
     * `passphrase`: string passphrase yang telah didekripsi (contoh di Postman: `*`).
   * Set `timeout` yang aman (misal 60 detik) karena proses signing di server eksternal bisa memakan waktu.
2. **Tangani Response:**
   * Jika sukses, API SiAgen akan mengembalikan stream file PDF yang sudah tertandatangani secara digital.
   * Simpan stream tersebut ke local storage atau S3 (`Storage::disk('s3')->put("certificates/{$uuid}.pdf", $responseBody)`).
   * Update tabel `certificates`: isi `file_path` dan ubah `status` menjadi `completed`.
   * Hapus file PDF mentah `$tempPdfPath` dari direktori temporary.
3. **Error Handling:** Jika API SiAgen gagal/timeout, ubah status menjadi `failed` dan log errornya.

### Fase 7: Halaman Verifikasi Publik (Public Facing)
1. **Buat Route Frontend Publik:** Misal `/verify/{uuid}`.
2. **Tampilan Verifikasi:** 
   * Buat halaman sederhana (tanpa middleware auth).
   * Tampilkan pesan "Sertifikat Valid" jika UUID ditemukan dan statusnya `completed`.
   * Tampilkan detail `student_name_snapshot`, judul course, dan tanggal kelulusan.
   * Tambahkan tombol untuk mengunduh PDF dari path `file_path`.
