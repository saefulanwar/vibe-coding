# Perencanaan: Integrasi Kustom LMS Moodle (Glacier UNY)

Dokumen ini berisi spesifikasi teknis untuk mengintegrasikan sistem platform kursus ini dengan LMS Moodle Glacier UNY secara langsung, menggunakan custom API endpoint yang telah disediakan oleh pihak infrastruktur. Dokumen ini dirancang sebagai pedoman implementasi langsung bagi developer atau agen AI.

---

## 1. Variabel Lingkungan (.env)

Sistem harus menggunakan variabel environment berikut untuk otentikasi dan merutekan permintaan API ke server Moodle Glacier:
```env
MOODLE_BASE_URL=https://glacier.uny.ac.id/lms
MOODLE_API_TOKEN=ddfcf0f5f6a13a8d3dc26ddbf98a44ce
MOODLE_REST_FORMAT=json
```

---

## 2. Refactoring `MoodleService.php`

Saat ini `App\Services\MoodleService` masih menggunakan arsitektur standard web service Moodle (`server.php`). Berdasarkan kebutuhan spesifik integrasi Glacier, fungsi pembuatan *user* dan pendaftaran *course* (enrollment) harus diubah menggunakan custom endpoint.

### a. Custom API Create User
*   **Endpoint:** `/local/web-service/create-user-glacier.php`
*   **Method:** GET / POST (melalui `http_build_query`)
*   **Parameter yang dibutuhkan:**
    *   `token` (wajib, dari `.env` `MOODLE_API_TOKEN`)
    *   `email` (wajib, email pendaftar)
    *   `lastname` (wajib, string/identitas)
    *   `nama` (wajib, firstname)
    *   `password` (kosongkan `''` atau generate secure)
    *   `auth` (wajib: `'oauth2'` atau `'manual'`)
    *   `idnumber` (opsional)
    *   `kota` (opsional)
    *   `department` (opsional)
    *   `institution` (opsional)
    *   `country` (wajib: `'ID'`)

**Tugas Implementasi:**
Ubah fungsi `createMoodleUser(array $userData)` di `MoodleService.php` agar memanggil HTTP Request (`CURL` atau `Http::get`/`post`) ke endpoint `create-user-glacier.php` dengan parameter form di atas. Ekstrak data JSON/String kembalian untuk mendapatkan Moodle `userid` baru.

### b. Custom API Enroll Course
*   **Endpoint:** `/local/web-service/enrol-course-glacier.php`
*   **Method:** GET / POST (melalui `http_build_query`)
*   **Parameter yang dibutuhkan:**
    *   `token` (wajib, dari `.env`)
    *   `roleid` (wajib: `5` untuk student)
    *   `userid` (wajib: id pengguna di Moodle yang terdaftar)
    *   `courseid` (wajib: id kursus di Moodle yang disalin ke Filament `moodle_course_id`)
    *   `timestart` (opsional: `0`)
    *   `timeend` (opsional: `0`)
    *   `suspend` (opsional: `0` untuk enroll aktif, `1` untuk suspend)

**Tugas Implementasi:**
Ubah fungsi `enrollUserInCourse(int $moodleUserId, int $moodleCourseId, int $roleId = 5)` di `MoodleService.php` agar memanggil endpoint `enrol-course-glacier.php` menggunakan parameter yang disebutkan.

### c. Mengambil/Validasi Data User (Get Users)
*   **Endpoint:** Standard REST Server (`/webservice/rest/server.php`)
*   **Function:** `core_user_get_users`
*   **Parameter POST Moodle standard:**
    *   `wstoken`
    *   `wsfunction=core_user_get_users`
    *   `moodlewsrestformat=json`
    *   `criteria[0][key]=email` (atau 'username')
    *   `criteria[0][value]=...` (nilai pencarian)

**Tugas Implementasi:**
Buat fungsi baru `getMoodleUserByEmail(string $email)` di `MoodleService.php`. Fungsi ini berguna untuk mengecek secara mandiri apakah email pengguna sudah ada di server Moodle.

---

## 3. Logika Alur Integrasi Workflow (EnrollmentService)

Saat sebuah transaksi berubah status menjadi `paid` pada kelas bersumber Moodle:
1. Panggil `MoodleService::getMoodleUserByEmail()` untuk memvalidasi akun.
2. **Jika user ditemukan**, simpan ID balikan Moodle ke kolom `moodle_user_id` di database lokal (update).
3. **Jika user belum ditemukan**, panggil `MoodleService::createMoodleUser()` via endpoint custom Glacier, tangkap respons pembuatan, dan simpan `moodle_user_id` baru ke dalam tabel lokal.
4. Terakhir, panggil `MoodleService::enrollUserInCourse()` (via endpoint custom) untuk mem-binding user ke dalam kursus Moodle secara mulus.

---

## 4. Daftar Periksa (Task List) untuk Agen / Developer
- [ ] Konfigurasi environment variables rahasia (Token & URL Glacier).
- [ ] Refactor fungsi `MoodleService::createMoodleUser` agar mengonsumsi custom endpoint `/local/web-service/create-user-glacier.php`.
- [ ] Refactor fungsi `MoodleService::enrollUserInCourse` agar mengonsumsi custom endpoint `/local/web-service/enrol-course-glacier.php`.
- [ ] Tambahkan fungsi utilitas `MoodleService::getMoodleUserByEmail` yang memanggil `core_user_get_users`.
- [ ] Perbarui `EnrollmentService` (atau logika aktivasi pemesanan) untuk mengandalkan fungsi "cek lalu daftar" guna mencegah pembuatan user duplikat di sistem Glacier.
