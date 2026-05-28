# Implementasi Fitur Review Kursus (Course Review)

## Deskripsi Singkat
Untuk melengkapi arsitektur sistem, "Member Memberikan Review dari Course" ditambahkan sebagai komponen penting dalam feedback loop pembelajaran. Review ini akan dikelola langsung oleh Glacier (Laravel) sebagai pusat data komersial dan interaksi pengguna, bukan di Moodle. Hal ini bertujuan agar rating dapat langsung terintegrasi dan ditampilkan di halaman katalog checkout kursus.

---

## 1. Pemetaan Alur Data (Workflow Review)
Proses ulasan ini dirancang untuk terjadi pada fase akhir, yaitu setelah siswa mendapatkan sertifikat atau mencapai progres kelulusan tertentu.

*   **Pemicu (Trigger):** Siswa menyelesaikan kursus atau menekan tombol "Beri Ulasan" yang muncul di dashboard Glacier.
*   **Proses Input:** Siswa mengisi form ulasan berupa rating (bintang 1–5) dan ulasan tekstual melalui antarmuka Glacier Player / Web Dashboard.
*   **Penyimpanan:** Data ulasan dikirim dan disimpan ke dalam tabel `course_reviews` di PostgreSQL (Central Database Glacier).
*   **Dampak Sistem (Katalog):** Glacier akan melakukan perhitungan ulang/pembaruan agregasi rating kursus. Pengunjung atau calon siswa baru di halaman Web Katalog dapat melihat ulasan dan rata-rata rating tersebut saat memilih kursus.

---

## 2. Skenario Pengujian (UAT / Postman Planning)
Pastikan implementasi melewati skenario pengujian berikut dengan hasil yang sesuai kriteria sukses:

### Skenario 1: Validasi Hak Akses Review (Security Check)
*   **Target Uji:** Pengguna yang belum membeli kursus atau progres penyelesaiannya belum memenuhi syarat (misal belum lulus) mencoba mengirim ulasan via API atau mengakses Form.
*   **Kriteria Sukses:**
    *   [ ] Sistem (API/Backend) menolak ulasan dan mengembalikan HTTP status `403 Forbidden` dengan pesan error (contoh: "Anda harus menyelesaikan kursus ini terlebih dahulu").
    *   [ ] Di sisi UI, tombol "Beri Ulasan" tidak di-render/tersembunyi di dashboard jika status progres di Moodle belum menyentuh batas minimal (misal 100% atau Lulus).

### Skenario 2: Input Data & Karakter (Data Integrity)
*   **Target Uji:** Pengguna mengirimkan ulasan dengan format salah, misalnya rating di luar rentang 1-5 (angka 0, 6, atau string) atau ulasan teks yang diisi script berbahaya.
*   **Kriteria Sukses:**
    *   [ ] Validasi di layer Controller/Form (Laravel Form Request / Filament) berhasil memblokir request dan memastikan input rating berupa `integer|min:1|max:5`.
    *   [ ] Input ulasan teks melewati proses sanitasi (XSS Sanitization / `strip_tags` atau ekuivalennya) sebelum disimpan ke PostgreSQL untuk mencegah celah keamanan.

### Skenario 3: Pembaruan Rating Publik (Real-time Agregation)
*   **Target Uji:** Setelah ulasan berhasil disimpan ke database.
*   **Kriteria Sukses:**
    *   [ ] Total rata-rata rating pada tabel/model kursus terkait langsung diperbarui (misal menjadi 4.8/5).
    *   [ ] Halaman katalog kursus publik menampilkan angka rata-rata rating terbaru secara otomatis tanpa error.

---

## 3. Komponen Manajemen Admin (Filament PHP)
Tim Reviewer/Admin internal membutuhkan akses untuk mengelola ulasan yang masuk melalui Filament Admin Panel.

*   **Fitur Moderasi (Soft Delete/Unpublish):** Admin dapat menyembunyikan atau menghapus sementara (soft delete) ulasan yang melanggar aturan (misal: mengandung SARA, kata kasar, atau spam) agar tidak tampil di katalog publik.
*   **Fitur Balasan (Reply):** Pengajar atau Admin dapat memberikan tanggapan/balasan resmi terhadap review (terutama review bintang 1-3) langsung dari dashboard admin untuk menjaga reputasi layanan e-learning.

---

## 4. Tahapan Implementasi (Panduan untuk Programmer / AI)
Kerjakan fitur ini secara bertahap mengikuti urutan berikut:

### Tahap 1: Persiapan Database & Model
1.  Buat migration untuk tabel `course_reviews`. Field yang dibutuhkan (contoh):
    *   `id` (Primary Key)
    *   `user_id` (Foreign key ke tabel `users`)
    *   `course_id` (Foreign key ke tabel `courses`)
    *   `rating` (Integer, 1-5)
    *   `review_text` (Text, nullable)
    *   `status` (String/Enum: 'published', 'hidden' - default: 'published')
    *   `reply_text` (Text, nullable - untuk balasan admin)
    *   `timestamps()` & `softDeletes()`
2.  Buat model `CourseReview` dan tentukan relasinya:
    *   `CourseReview` `belongsTo` `User`
    *   `CourseReview` `belongsTo` `Course`
3.  Update model `Course` untuk memiliki relasi `hasMany` `CourseReview`. Tambahkan juga method/logic (atau event observer) untuk menghitung ulang dan menyimpan nilai *average rating* di tabel `courses` setiap kali ada review baru ditambahkan atau diubah.

### Tahap 2: Backend Logic & Validasi
1.  Buat logic/method untuk mengecek kelayakan user memberi review berdasarkan status *enrollment* dan progres *completion* mereka.
2.  Terapkan validasi input ketat saat form disubmit (rating 1-5 wajib, sanitasi tag HTML pada teks review).
3.  Pastikan fungsi trigger perhitungan ulang rata-rata rating berjalan secara atomik (sebaiknya bungkus dengan Database Transaction) saat proses simpan ulasan berlangsung.

### Tahap 3: Frontend Web / Member Dashboard
1.  **Dashboard Siswa:** Di halaman detail kursus yang diikuti, tampilkan tombol/form "Beri Ulasan" **hanya jika** sistem menyatakan user telah memenuhi syarat lulus.
2.  **Form Input:** Sediakan interaksi UI berupa komponen Star Rating (bintang 1-5) yang bisa di-klik dan sebuah Textarea untuk isi review.
3.  **Katalog Publik:** Di halaman detail kursus publik, tambahkan seksi untuk menampilkan agregat rating (contoh: "⭐ 4.8 dari 120 Ulasan") dan daftar review yang diberikan oleh user lain (hanya yang berstatus 'published').

### Tahap 4: Admin Panel (Filament PHP)
1.  Buat Resource baru di Filament: `CourseReviewResource`.
2.  **Konfigurasi Table:** Tampilkan kolom Nama Siswa, Nama Kursus, Rating (visualisasikan dengan komponen icon/bintang), Cuplikan Review, dan Status. Berikan fitur *Filter* berdasarkan rating (1-5) dan kursus.
3.  **Konfigurasi Action (Moderasi):** Sediakan *action* praktis di tabel untuk mengganti status ulasan menjadi 'hidden' (menyembunyikan) atau memulihkannya kembali menjadi 'published'.
4.  **Halaman Edit (Reply):** Buat form edit di mana rating dan ulasan user bersifat *read-only*. Sediakan textarea untuk Admin/Pengajar mengisi `reply_text` sebagai tanggapan publik terhadap ulasan tersebut.

### Tahap 5: Pengujian Akhir (QA)
1.  Eksekusi 3 skenario UAT yang tertulis di bagian (2) untuk memverifikasi keamanan dan fungsionalitas.
2.  Lakukan audit *database query* untuk memastikan pemuatan daftar ulasan di halaman publik tidak menyebabkan masalah performa (*N+1 query problem*).
