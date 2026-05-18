<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BatchSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'mastering-laravel-13-dasar-hingga-mahir')->first();
        if (!$course) {
            $this->command->error('Mastering Laravel course not found. Please run DatabaseSeeder first.');
            return;
        }

        // 1. Batch Terbuka & Sudah Dimulai (Bisa dibeli & Bisa langsung belajar)
        CourseBatch::updateOrCreate(
            ['name' => 'Angkatan 1 - Akses Terbuka'],
            [
                'course_id' => $course->id,
                'quota' => 15,
                'start_date' => Carbon::now()->subDays(2), // Started 2 days ago
                'end_date' => Carbon::now()->addDays(30),
                'registration_end_date' => Carbon::now()->addDays(5),
            ]
        );

        // 2. Batch Belum Mulai (Bisa dibeli, tetapi akses belajar masih terkunci)
        CourseBatch::updateOrCreate(
            ['name' => 'Angkatan 2 - Belum Dimulai'],
            [
                'course_id' => $course->id,
                'quota' => 20,
                'start_date' => Carbon::now()->addDays(4), // Starts in 4 days
                'end_date' => Carbon::now()->addDays(34),
                'registration_end_date' => Carbon::now()->addDays(2),
            ]
        );

        // 3. Batch Kuota Penuh (Beli Sekarang diganti dengan badge Kuota Penuh)
        $fullBatch = CourseBatch::updateOrCreate(
            ['name' => 'Angkatan 3 - Kelas Terbatas (Kuota Penuh)'],
            [
                'course_id' => $course->id,
                'quota' => 2, // Quota only 2
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(40),
                'registration_end_date' => Carbon::now()->addDays(8),
            ]
        );

        // Seed 2 mock users to fill quota
        $userA = User::updateOrCreate(['email' => 'student.a@example.com'], ['name' => 'Student A', 'password' => bcrypt('password')]);
        $userB = User::updateOrCreate(['email' => 'student.b@example.com'], ['name' => 'Student B', 'password' => bcrypt('password')]);
        
        Enrollment::updateOrCreate(['user_id' => $userA->id, 'course_batch_id' => $fullBatch->id], ['enrolled_at' => now()]);
        Enrollment::updateOrCreate(['user_id' => $userB->id, 'course_batch_id' => $fullBatch->id], ['enrolled_at' => now()]);

        // 4. Batch Pendaftaran Ditutup (Beli Sekarang diganti dengan badge Pendaftaran Ditutup)
        CourseBatch::updateOrCreate(
            ['name' => 'Angkatan 4 - Pendaftaran Ditutup'],
            [
                'course_id' => $course->id,
                'quota' => 50,
                'start_date' => Carbon::now()->addDays(1),
                'end_date' => Carbon::now()->addDays(31),
                'registration_end_date' => Carbon::now()->subDays(1), // Registration closed yesterday
            ]
        );

        // 5. Let's also enroll our default member user in the locked batch to simulate the lock on "Kursus Anda"
        $member = User::where('email', 'member@example.com')->first();
        if ($member) {
            $lockedBatch = CourseBatch::where('name', 'Angkatan 2 - Belum Dimulai')->first();
            Enrollment::updateOrCreate(
                ['user_id' => $member->id, 'course_batch_id' => $lockedBatch->id],
                ['enrolled_at' => now()]
            );
        }
    }
}
