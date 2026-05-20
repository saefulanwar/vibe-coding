# Issue: Pembuatan Landing Page Interaktif dengan Tailwind CSS dan Livewire

## Deskripsi Tugas
Dengan menggunakan Tailwind CSS (untuk UI modern dan responsif) dan Laravel Livewire (untuk interaktivitas real-time tanpa reload halaman), kita akan membuat landing page yang sangat interaktif dan ringan untuk aplikasi platform kursus multi-unit.

Dokumen ini dirancang sebagai panduan teknis yang siap dieksekusi oleh programmer atau model LLM pengembang.

---

## 🛡️ Tema & Desain Sistem (Tailwind CSS)
**Vibe:** Profesional, Akademis, Tepercaya, namun Modern dan Bersih.

**Palet Warna:**
*   **Primary:** Biru Navy (`bg-slate-900` atau `bg-indigo-900`) – melambangkan kredibilitas akademis.
*   **Secondary:** Biru Cerah/Sian (`text-sky-500`) – untuk aksen, link, dan elemen modern.
*   **Accent:** Amber/Emas (`bg-amber-500`) – untuk tombol Call to Action (CTA) atau penanda harga/rating.

**Font:** Inter atau Plus Jakarta Sans (`font-sans`) untuk keterbacaan yang tinggi.

---

## 🧩 Arsitektur Komponen Livewire
Untuk performa optimal, kita akan membagi landing page ini menjadi beberapa komponen Livewire terisolasi:

1.  **Navbar:** Navigasi responsif + status login user.
2.  **HeroSearch:** Bagian utama dengan search bar interaktif.
3.  **FacultyGrid:** Menampilkan daftar fakultas/unit secara dinamis.
4.  **CourseCatalog:** Komponen utama pencarian, filter, dan list kursus (real-time).

---

## 📋 Cetak Biru Seksi Landing Page (Section-by-Section)

### 1. Header & Navigation Bar (`Livewire/Navbar.php`)
*   **UI (Tailwind):** `sticky top-0 backdrop-blur-md bg-white/80` (efek transparan premium saat di-scroll).
*   **Fitur:** Logo platform, menu navigasi (Kursus, Fakultas, Tentang Kami), dan tombol dinamis: 
    *   Jika belum login tampilkan "Masuk / Daftar"
    *   Jika sudah login tampilkan "Ke Dashboard [Nama User]".

### 2. Hero Section dengan Pencarian Instan (`Livewire/HeroSearch.php`)
Meniru pola platform besar yang langsung menyajikan kolom pencarian besar di atas *fold*.
*   **UI (Tailwind):** Sisi kiri berisi headline tebal: "Tingkatkan Keahlian Anda Bersama Fakultas & Unit Terbaik", sisi kanan berupa ilustrasi atau foto mahasiswa yang bersih.
*   **Fitur Livewire:** Input pencarian menggunakan `wire:model.live="search"`. Ketika user mengetik, langsung muncul dropdown rekomendasi kursus secara real-time sebelum mereka menekan tombol cari.

### 3. Statis/Materi Promosi: Keunggulan Platform
*   **UI (Tailwind):** Grid 3 kolom (`grid grid-cols-1 md:grid-cols-3 gap-8`).
*   **Konten:** 3 kartu yang menjelaskan mengapa harus belajar di sini (misal: Sertifikat Resmi, Instruktur Ahli dari Unit/Fakultas, Akses Selamanya).

### 4. Eksibisi Fakultas / Unit (`Livewire/FacultyGrid.php`)
Menampilkan logo dan nama-nama fakultas/unit yang mengelola kursus.
*   **UI (Tailwind):** Efek hover card (`hover:shadow-lg hover:-translate-y-1 transition duration-300`).
*   **Fitur Livewire:** Klik pada kartu fakultas akan otomatis mengarahkan atau men-filter katalog kursus di bawahnya untuk hanya menampilkan kursus dari unit tersebut.

### 5. Katalog Kursus Interaktif (`Livewire/CourseCatalog.php`) – Core Feature
Ini adalah bagian paling fungsional, memanfaatkan keunggulan Livewire untuk menyaring data tanpa refresh halaman.

*   **Sisi Kiri (Sidebar Filter):**
    *   Pilihan kategori/fakultas (Checkbox).
    *   Filter harga (Gratis / Berbayar).
    *   Urutan (Terpopuler, Terbaru, Harga Terendah).
*   **Sisi Kanan (Grid Kursus):**
    *   Menampilkan kartu kursus dengan informasi: Gambar mini (thumbnail), Badge Nama Unit/Fakultas (misal: "Fakultas Teknik"), Judul Kursus, Rating bintang, dan Harga.
*   **Fitur Livewire:**
    *   Menggunakan Computed Properties untuk query data kursus berdasarkan filter yang dipilih.
    *   Implementasi `wire:click="loadMore"` untuk infinite scrolling atau paginasi halus.

### 6. Call to Action (CTA) & Footer
*   **UI (Tailwind):** Banner dengan latar belakang gradient gelap (`bg-gradient-to-r from-blue-900 to-indigo-900`) yang mengajak institusi/unit lain untuk bergabung atau mengajak mahasiswa mendaftar.
*   **Footer:** Navigasi standar, hak cipta, dan media sosial dengan warna teks yang diredam (`text-slate-400`).

---

## 🚀 Tips Implementasi Teknis (Untuk Programmer/LLM)

1.  **Lazy Loading:** Gunakan fitur `#[Lazy]` milik Livewire v3 pada komponen `CourseCatalog` dan `FacultyGrid` agar landing page utama terbuka dalam hitungan milidetik, sementara data kursus dimuat di latar belakang.
2.  **Alpine.js untuk UI Ringan:** Untuk interaksi mikro seperti membuka menu dropdown profil atau mobile menu hamburger, jangan gunakan Livewire. Gunakan Alpine.js (yang sudah *built-in* di Livewire) dengan `x-data="{ open: false }"` agar hemat *resource server*.
3.  **Image Optimization:** Pastikan gambar thumbnail kursus menggunakan aspek rasio yang konsisten memakai Tailwind (`aspect-video object-cover`).
