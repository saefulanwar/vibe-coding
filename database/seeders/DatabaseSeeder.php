<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminFakultasRole = Role::firstOrCreate(['name' => 'admin_fakultas']);
        $memberRole = Role::firstOrCreate(['name' => 'member']);

        // Create Units
        $unitIT = \App\Models\Unit::updateOrCreate(
            ['code' => 'IT'],
            ['name' => 'Teknologi Informasi']
        );

        $unitHRD = \App\Models\Unit::updateOrCreate(
            ['code' => 'HRD'],
            ['name' => 'Human Resources Development']
        );

        // Super Admin (global, no unit)
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'unit_id' => null,
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Admin Fakultas (assigned to IT unit)
        $adminFakultas = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Fakultas',
                'password' => Hash::make('password'),
                'unit_id' => $unitIT->id,
            ]
        );
        $adminFakultas->assignRole($adminFakultasRole);

        // Member
        $member = User::updateOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Member User',
                'password' => Hash::make('password'),
            ]
        );
        $member->assignRole($memberRole);

        // Seed Category
        $category = \App\Models\Category::updateOrCreate(
            ['slug' => 'teknologi-informasi'],
            ['name' => 'Teknologi Informasi']
        );

        // Seed Local Course (assigned to IT unit)
        $localCourse = \App\Models\Course::updateOrCreate(
            ['slug' => 'mastering-laravel-13-dasar-hingga-mahir'],
            [
                'category_id' => $category->id,
                'unit_id' => $unitIT->id,
                'title' => 'Mastering Laravel 13 - Dasar hingga Mahir',
                'description' => 'Pelajari framework PHP paling populer dari nol. Kursus mandiri ini mencakup MVC, Eloquent ORM, Filament Admin Panel, dan deployment.',
                'price' => 150000.00,
                'is_published' => true,
                'source' => 'local',
            ]
        );

        // Modules and Lessons for Local Course
        $module1 = \App\Models\Module::updateOrCreate(
            [
                'course_id' => $localCourse->id,
                'title' => 'Module 1: Instalasi & Konfigurasi',
            ],
            ['sort_order' => 1]
        );

        \App\Models\Lesson::updateOrCreate(
            [
                'module_id' => $module1->id,
                'title' => 'Persiapan Lingkungan Kerja (PHP & Composer)',
            ],
            [
                'content_text' => '<p>Di pelajaran ini, kita akan mempelajari cara menginstal <strong>PHP 8.3+</strong> dan <strong>Composer</strong> sebagai dasar pengembangan aplikasi Laravel 13.</p><p>Pastikan Anda menginstal tools berikut:</p><ul><li>PHP 8.3 ke atas</li><li>Composer versi terbaru</li><li>Visual Studio Code</li></ul>',
                'video_url' => 'https://www.youtube.com/watch?v=IM-D67uRpeo', // Simulating a learning video
                'sort_order' => 1,
            ]
        );

        \App\Models\Lesson::updateOrCreate(
            [
                'module_id' => $module1->id,
                'title' => 'Inisialisasi Project Laravel',
            ],
            [
                'content_text' => '<p>Kita akan memulai project baru dengan menggunakan Composer. Jalankan perintah berikut di terminal Anda:</p><pre><code>composer create-project laravel/laravel vibe-learning</code></pre>',
                'video_url' => 'https://www.youtube.com/watch?v=2n5mGqC6t-Y',
                'sort_order' => 2,
            ]
        );

        $module2 = \App\Models\Module::updateOrCreate(
            [
                'course_id' => $localCourse->id,
                'title' => 'Module 2: Eloquent ORM & Migrations',
            ],
            ['sort_order' => 2]
        );

        \App\Models\Lesson::updateOrCreate(
            [
                'module_id' => $module2->id,
                'title' => 'Mendesain Database Schema',
            ],
            [
                'content_text' => '<p>Di bab ini kita mempelajari cara mendesain schema database secara terstruktur menggunakan Laravel Migrations dan relasi database.</p>',
                'video_url' => null,
                'sort_order' => 1,
            ]
        );

        // Seed Moodle Course
        \App\Models\Course::updateOrCreate(
            ['slug' => 'sertifikasi-profesional-cloud-computing'],
            [
                'category_id' => $category->id,
                'title' => 'Sertifikasi Profesional Cloud Computing',
                'description' => 'Kursus tingkat lanjut tentang arsitektur Cloud Computing (AWS/GCP). Seluruh kuis, tugas akhir, dan sertifikasi didelegasikan secara seamless langsung ke LMS Moodle.',
                'price' => 350000.00,
                'is_published' => true,
                'source' => 'moodle',
                'moodle_course_id' => 101, // Mock Moodle Course ID
            ]
        );
    }
}
