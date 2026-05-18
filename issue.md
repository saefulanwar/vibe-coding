# Perencanaan: Sistem Angkatan (Batching) E-Learning Hybrid

Dokumen spesifikasi teknis ini dirancang sebagai panduan implementasi komprehensif bagi developer/agen untuk membangun fitur **Sistem Angkatan (Batching)** pada platform hybrid e-learning (Laravel + Moodle). Sistem ini mengedepankan efisiensi melalui pola desain *Master-Detail* dan efisiensi manajemen LMS melalui *Moodle Groups*.

---

## 1. Arsitektur Database (Konsep Master-Detail)

Sistem akan memisahkan antara entitas Materi Induk (`courses`) dan Pelaksanaan Kelas (`course_batches`).

### a. Pembuatan Tabel Baru: `course_batches`
Tabel ini merepresentasikan pelaksanaan nyata (angkatan) dari sebuah kursus master.
*   **Kolom Wajib:**
    *   `id` (Primary Key)
    *   `course_id` (Foreign Key $\rightarrow$ `courses.id`, hapus secara cascade)
    *   `name` (String, misal: "Angkatan 1 - Q3 2026")
    *   `moodle_group_id` (Integer/Nullable, menyimpan ID Group dari Moodle API)
    *   `quota` (Integer, jumlah maksimal peserta)
    *   `start_date` (Datetime, tanggal kelas dimulai)
    *   `end_date` (Datetime, tanggal kelas berakhir)
    *   `registration_end_date` (Datetime, batas akhir pendaftaran/pembayaran)
    *   `timestamps`

### b. Pergeseran Relasi Transaksi & Hak Akses
Struktur *checkout* tidak lagi terikat langsung ke kursus, melainkan ke angkatan.
*   **Tabel `orders`**: Ubah kolom `course_id` menjadi `course_batch_id` (Foreign Key $\rightarrow$ `course_batches.id`).
*   **Tabel `enrollments`**: Ubah kolom `course_id` menjadi `course_batch_id` (Foreign Key $\rightarrow$ `course_batches.id`).
*   *Catatan Eksekusi:* Buat file *migration* baru (misal: `create_course_batches_and_modify_orders_table`) untuk:
    1. Membuat tabel `course_batches`.
    2. Menghapus constraint/kolom `course_id` pada tabel `orders` dan `enrollments`.
    3. Menambahkan kolom `course_batch_id` ke tabel `orders` dan `enrollments`.

---

## 2. Strategi Efisiensi Moodle (Integrasi Fitur *Groups*)

Tujuan arsitektur ini adalah menghindari duplikasi kursus di Moodle (menghindari tumpukan kursus sampah). Satu Master Kursus di Moodle akan berisi banyak angkatan, dipisahkan secara rapi menggunakan fungsionalitas Moodle *Groups*.

### a. MoodleService: Penambahan Fungsi API Baru
Tambahkan *method* baru pada `App\Services\MoodleService`:
1.  **`createMoodleGroup($moodleCourseId, $groupName)`**
    *   Memanggil *Web Service API*: `core_group_create_groups`
    *   Parameter: `courseid`, `name` (nama batch), `description`.
    *   Mengembalikan: `group_id` (integer) yang selanjutnya disimpan di kolom `course_batches.moodle_group_id`.
2.  **`addUserToGroup($moodleGroupId, $moodleUserId)`**
    *   Memanggil *Web Service API*: `core_group_add_group_members`
    *   Parameter: `groupids` (array yang berisi object `{ groupid, userid }`).

### b. Alur Otomatisasi (Webhooks & Filament)
*   **Panel Admin (Pembuatan Batch)**: Saat admin menyimpan data `CourseBatch` (melalui Filament Resource/Relation Manager) untuk kursus bertipe `moodle`, jalankan *observer/action* untuk langsung menembak API `createMoodleGroup()`. Simpan respons ID ke `moodle_group_id`.
*   **EnrollmentService (Webhook Lunas)**: Saat status `orders` menjadi `paid` / `settlement`, alur aktivasi dimodifikasi menjadi:
    1. Buat User Moodle (jika belum ada).
    2. Daftarkan User ke *Master Course Moodle* (via `enrol_manual_enrol_users`).
    3. **[BARU]** Masukkan User ke dalam kelompok spesifik angkatannya dengan memanggil `addUserToGroup($batch->moodle_group_id, $moodleUserId)`. Nilai, kuis, dan diskusi otomatis terisolasi secara transparan di dalam platform Moodle.

> [!NOTE]
> **Pengecualian untuk Kursus Lokal:**
> Seluruh alur otomatisasi API Moodle (Pembuatan Group dan Enroll Member) pada bagian 2 ini **dilewati (di-bypass)** jika kursus tersebut memiliki tipe sumber daya `local` (`course.source == 'local'`). Bagi kursus lokal, kolom `moodle_group_id` dapat dibiarkan kosong (null).

---

## 3. Gerbang Logika (Logic Gates) & Aturan Bisnis di Laravel

Laravel akan bertindak sebagai "satpam pintar" sebelum melempar pendaftaran ke *Payment Gateway*. **Catatan Penting:** Aturan bisnis pada bagian ini (kuota, jadwal pendaftaran, jadwal belajar) **berlaku secara universal**, baik untuk kelas `moodle` maupun kelas `local`.

### a. Validasi Ketersediaan di `CheckoutController` (Pra-Transaksi)
Sebelum memanggil Payment Gateway (Midtrans), sistem wajib memvalidasi parameter batch:
1.  **Cek Batas Waktu Pendaftaran (Time Limit)**:
    *   Logika: `now() <= $batch->registration_end_date`
    *   Jika lewat: Transaksi ditolak secara hard-stop (Tampilkan *alert* "Waktu pendaftaran angkatan ini telah berakhir").
2.  **Cek Kuota Peserta (Capacity Limit)**:
    *   Hitung jumlah baris di tabel `enrollments` berdasarkan `course_batch_id`.
    *   Logika: `enrollments_count < $batch->quota`
    *   Jika penuh: Transaksi ditolak secara hard-stop (Tampilkan *alert* "Kuota angkatan ini sudah penuh").

### b. Kontrol UI Portal Siswa (`CourseController` / Dasbor)
Pengalaman pengguna (UX) wajib disesuaikan dengan ketersediaan dan jadwal angkatan:
*   **Penguncian Pendaftaran**: Tombol "Beli Sekarang" pada halaman katalog/kursus harus diganti dengan *badge* merah (misal: "Pendaftaran Ditutup" / "Kuota Penuh") jika validasi Pra-Transaksi (kuota atau batas waktu) gagal secara *real-time*.
*   **Penguncian Akses Belajar (Start Learning Lock)**: Tombol "Mulai Belajar" (yang mengarah ke halaman *SSO* Moodle atau *Lesson Viewer* lokal) pada dasbor portal siswa (`/dashboard`) **hanya aktif jika jadwal kelas telah dimulai** (`now() >= $batch->start_date`). Tampilkan teks informasi "Kelas Dimulai Pada: [Tanggal]" jika waktu belajar belum tiba.

---

## 4. Persyaratan Eksekusi Filament Admin Panel
1.  **Model Relational**: Pastikan Eloquent model telah di-update:
    *   `Course` `hasMany` `CourseBatch`
    *   `CourseBatch` `belongsTo` `Course`
    *   `CourseBatch` `hasMany` `Order` dan `Enrollment`
2.  **Integrasi Antarmuka Filament**:
    *   Buat `RelationManager` (misalnya `BatchesRelationManager`) untuk dimasukkan ke dalam `CourseResource`. Dengan cara ini, Admin dapat langsung menambah, mengubah, dan menghapus *Batch* pada saat mengedit halaman Master Course, menciptakan pengalaman UI/UX yang premium dan logis.
    *   Pastikan tabel pada `CourseBatch` Filament menampilkan persentase keterisian kursi/kuota.

Silakan jadikan *blueprint* di atas sebagai *Source of Truth* (sumber acuan utama) untuk pengembangan tahap selanjutnya.
