# Perencanaan: Penambahan Entitas Unit Kerja & Scoping Data

Dokumen spesifikasi teknis ini dirancang sebagai panduan implementasi komprehensif bagi developer/agen untuk membangun fitur **Unit Kerja (Work Unit)** pada platform hybrid e-learning. Fitur ini memungkinkan desentralisasi pengelolaan kursus berdasarkan departemen atau unit kerja tertentu.

---

## 1. Arsitektur Database & Migrasi

Sistem akan menambahkan entitas baru `units` dan memodifikasi tabel `users` serta `courses` agar memiliki relasi terhadap unit.

### a. Pembuatan Tabel Baru: `units`
Tabel ini merepresentasikan unit kerja yang akan bertindak sebagai entitas pengelola kursus.
*   **Kolom Wajib:**
    *   `id` (Primary Key)
    *   `code` (String, unique, misal: "IT", "HRD", "MKG")
    *   `name` (String, misal: "Information Technology", "Human Resources", "Marketing")
    *   `timestamps`

### b. Modifikasi Tabel `users`
Tambahkan kolom relasi ke tabel `users` untuk menandakan unit mana user (instruktur/admin unit) bernaung.
*   **Kolom Baru:** `unit_id` (Foreign Key $\rightarrow$ `units.id`, nullable, onDelete set null).

### c. Modifikasi Tabel `courses`
Tambahkan kolom relasi ke tabel `courses` agar sistem tahu kursus tersebut merupakan milik unit mana.
*   **Kolom Baru:** `unit_id` (Foreign Key $\rightarrow$ `units.id`, nullable, onDelete cascade/set null).

---

## 2. Eloquent Model & Relasi

Pastikan relasi Eloquent diatur dengan benar pada model terkait:

1.  **Model `Unit`**:
    *   Buat file model `App\Models\Unit`.
    *   Relasi: `hasMany(User::class)` dan `hasMany(Course::class)`.
    *   Kolom fillable: `code`, `name`.

2.  **Model `User`**:
    *   Tambahkan relasi: `belongsTo(Unit::class, 'unit_id')`.
    *   Tambahkan `unit_id` ke dalam properti `$fillable`.

3.  **Model `Course`**:
    *   Tambahkan relasi: `belongsTo(Unit::class, 'unit_id')`.
    *   Tambahkan `unit_id` ke dalam properti `$fillable`.

---

## 3. Isolasi Data & Akses (Data Scoping)

Tujuan utama dari fitur ini adalah membatasi visibilitas data (Tenant-like Scoping) agar "user sebagai unit" hanya melihat dan mengelola data miliknya sendiri.

### a. Logika Hak Akses (Permissions)
*   Role unit ini akan terhubung dengan user. Izin (permissions) spesifik antar-role akan dikelola kemudian melalui antarmuka sistem (Filament Shield di menu Roles).
*   Pengguna dengan peran "Unit Admin" akan memiliki `unit_id` yang terisi. Pengguna Super Admin akan memiliki `unit_id` bernilai `null` yang mengartikan akses tanpa batasan (*global view*).

### b. Filament Resource Scoping (Isolasi Dasbor Kursus)
Untuk memastikan data menyesuaikan dengan unit dari user yang login, sistem perlu menerapkan filter data pada *query* utama.

*   **Penerapan Scoping di `CourseResource.php`**:
    Timpa (override) fungsi `getEloquentQuery` untuk memfilter tampilan data tabel agar khusus menampilkan milik unitnya saja:
    ```php
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Jika user memiliki unit_id, batasi kursus hanya untuk unit tersebut
        if ($user && $user->unit_id) {
            $query->where('unit_id', $user->unit_id);
        }

        return $query;
    }
    ```
*   **Penerapan Pengisian Form Otomatis**:
    Saat pembuatan kursus (`CreateCourse.php`), sistem secara implisit (*hidden*) menyematkan `unit_id` dari pengguna yang sedang login. Jika pengguna login adalah Super Admin (`unit_id` null), barulah sistem menampilkan komponen `Select::make('unit_id')` untuk menugaskan kursus tersebut ke unit tertentu.

---

## 4. Manajemen Entitas di Filament Panel

Agar struktur unit ini bisa dikelola, tambahkan antarmuka (UI) berikut:

1.  **`UnitResource`**:
    *   Buat resource Filament baru khusus untuk tabel `units` (terbatas untuk Super Admin) yang memungkinkan penambahan, pengubahan, dan penghapusan unit kerja.
2.  **Modifikasi `UserResource`**:
    *   Tambahkan komponen form `Select::make('unit_id')->relationship('unit', 'name')` pada halaman pembuatan/edit user.

---

## 5. Ringkasan Tugas Implementasi (Task List)
Panduan bertahap (step-by-step) eksekusi untuk agen/developer:
1.  [ ] Buat migration untuk pembuatan tabel `units`.
2.  [ ] Buat migration untuk menambahkan kolom `unit_id` ke tabel `users` dan `courses`.
3.  [ ] Buat model `Unit` dan perbarui properti `$fillable` serta relasi pada model `User` dan `Course`.
4.  [ ] Buat `UnitResource` di Filament untuk manajemen master data unit.
5.  [ ] Modifikasi `UserResource` dan `CourseResource` agar mendukung input `unit_id` (dengan kondisional `hidden` berbasis role).
6.  [ ] Terapkan fungsi `getEloquentQuery()` pada `CourseResource` untuk memastikan isolasi/scoping data antar unit kerja.
7.  [ ] Lakukan *test database migration* dan verifikasi isolasi data.
