# Issue: Implementasi Multi-Bahasa (i18n) pada Aplikasi

## Deskripsi Tugas
Tugas ini bertujuan untuk mengimplementasikan fitur multi-bahasa (Indonesia dan Inggris) pada aplikasi. Dokumen ini dirancang sebagai panduan teknis komprehensif yang siap dieksekusi oleh tim pengembang (Junior Programmer / Model LLM).

### 1. UI/UX Pemilih Bahasa (Language Switcher)
- **Gunakan Nama Bahasa, Bukan Bendera**: Jangan gunakan bendera negara (seperti 🇲🇨 atau 🇬🇧) untuk ikon pemilih bahasa. Bendera merepresentasikan negara, bukan bahasa (Bahasa Inggris digunakan di UK, USA, Australia, dll). Gunakan teks ringkas yang jelas seperti `ID` dan `EN` atau `Indonesia` dan `English`.
- **Lokasi yang Konsisten**: Tempatkan tombol *language switcher* di pojok kanan atas navbar pada tampilan desktop, dan di bagian paling atas atau bawah menu *hamburger* pada tampilan *mobile*.
- **Persistensi Pilihan Pengguna**: Simpan pilihan bahasa terakhir pengguna menggunakan Cookie (direkomendasikan) atau Session dengan masa kedaluwarsa yang cukup lama (misal: 1 tahun). Ini memastikan pengguna tidak perlu memilih ulang bahasa dari awal saat mereka kembali esok hari.

### 2. Struktur Rute & Optimalisasi SEO
Untuk mendukung SEO yang optimal, konten multi-bahasa harus dipisahkan lewat URL yang jelas bagi *search engine* (seperti Google).

**Pendekatan yang Dipilih: URL Prefix (Sangat Direkomendasikan)**
- Format URL *landing page* akan menggunakan *prefix* bahasa, contoh: `domain.com/id` untuk bahasa Indonesia dan `domain.com/en` untuk bahasa Inggris.
- **Kelebihan**: Sangat bagus untuk SEO karena Google *bot* tahu persis halaman mana yang menggunakan bahasa apa. *Hindari* penggunaan parameter session murni tanpa perubahan URL karena *bot* kesulitan merayapi (*crawling*) konten tersebut.

### 3. Implementasi Teknis Dasar (Laravel Localization)
- **Hindari Hardcoding**: Jangan pernah menulis teks mentah di file Blade.
- **Gunakan Helper Localization**: Kumpulkan semua teks *landing page* di dalam folder `lang/` dan gunakan helper bawaan Laravel (`__('')`).
- **Middleware Bahasa**: Buat sebuah *middleware* yang mendeteksi bahasa dari URL *prefix* atau *session* sebelum halaman dirender, lalu atur lokal aplikasi menggunakan `App::setLocale()`.

### 4. Penanganan Data Dinamis dari Database (Judul & Deskripsi Kursus)
Teks statis dapat ditangani file `lang/`, tetapi data spesifik dari database (seperti dari Moodle/Filament) memerlukan penanganan khusus:

- **Opsi A: Gunakan Package Spatie Translatable (Direkomendasikan)**: Jika sistem memegang kendali penuh atas database kursus, gunakan *package* `spatie/laravel-translatable`. Data di database akan disimpan dalam format JSON: `{"id": "Belajar Laravel", "en": "Learn Laravel"}`. Model akan otomatis mengambilkan string yang sesuai bahasa aktif.
- **Opsi B: Sistem Fallback Bersih**: Jika data ditarik mentah-mentah dari sistem pihak ketiga (misal: Moodle) dan hanya tersedia dalam satu bahasa, tampilkan data aslinya dan berikan catatan kecil dwibahasa di bawah konten. Contoh: `* Content only available in Indonesian.`

### 5. Checklist Tambahan
- **Format Mata Uang & Angka**: Lokalisasikan format angka. Jika bahasa diatur ke `EN`, format investasi kursus harus menyesuaikan (misal dari `Rp 1.500.000` menjadi `IDR 1,500,000` agar audiens internasional tidak bingung dengan tanda pemisah ribuan).
- **Aksesibilitas & Kecepatan Gambar**: Pastikan teks alternatif (`alt=""`) pada gambar *thumbnail* juga di-set multi-bahasa untuk mendukung aksesibilitas (*screen reader*) dan *SEO image search* yang baik.

---

## 🚀 Saran & Implementasi Terbaik Tambahan (Best Practices)

Untuk implementasi multi-bahasa yang lebih canggih, terstandarisasi, dan mudah di- *maintain*, terapkan panduan tambahan berikut:

1. **Gunakan Package `mcamara/laravel-localization`**
   Daripada membuat konfigurasi *routing* dan *middleware* manual, sangat disarankan menggunakan *package* ini.
   - **Keuntungan**: Otomatis menangani *routing prefix* URL (`/en/courses`, `/id/courses`).
   - Menyediakan fitur *Language Hiding* untuk bahasa *default* (misal: Bahasa Indonesia sebagai *default* tidak perlu *prefix* `/id`, cukup `/courses`, sedangkan versi Inggris menjadi `/en/courses`). Ini meminimalisir masalah *duplicate content* di mata Google.

2. **Deteksi Bahasa Otomatis (Auto-Negotiation)**
   Aplikasi sebaiknya otomatis mengecek header `Accept-Language` dari *browser* pengunjung pertama kali. Jika OS atau browser pengunjung menggunakan bahasa Inggris, aplikasi dapat secara otomatis me- *redirect* mereka ke versi `EN`, memberikan *First Impression* yang lebih intuitif.

3. **Terapkan Tag Hreflang Otomatis**
   SEO bukan hanya soal URL. Anda wajib menambahkan tag HTML `<link rel="alternate" hreflang="x" href="..."/>` di bagian `<head>` untuk saling menautkan antar versi bahasa. (Package `mcamara` sudah menyediakan fungsi bawaan untuk ini).

4. **Gunakan File JSON untuk Translasi, Bukan Array PHP**
   Alih-alih menggunakan banyak file *array* PHP (`lang/en/messages.php`), gunakan satu file JSON besar (`lang/en.json` dan `lang/id.json`).
   - **Keuntungan**: Mempermudah proses pencarian dan penulisan teks. Anda bisa langsung memanggil kalimat bahasa aslinya sebagai key: `__('Course Investment')`, alih-alih `__('messages.course_investment')`.

5. **Tag HTML Lang Global**
   Pastikan tag utama HTML selalu dinamis. Buka file layout utama (seperti `app.blade.php`) dan pastikan formatnya: 
   `<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">`

6. **Lokalisasi Tanggal dengan Carbon**
   Pastikan format seperti "3 Hari yang lalu" atau "Januari 2024" menyesuaikan bahasa aktif dengan menerapkan `Carbon::setLocale(app()->getLocale());` di dalam *Service Provider*.
