<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\MoodleService;
use Illuminate\Support\Facades\Log;

echo "--- Simulasi Integrasi Moodle Glacier ---\n\n";

$service = app(MoodleService::class);
$email = 'andhika123456@student.uny.ac.id';
$courseId = 2; // dari contoh issue

echo "1. Cek User '$email' di Moodle...\n";
$moodleUserId = null;
try {
    $moodleUserId = $service->getMoodleUserByEmail($email);
    if ($moodleUserId) {
        echo "   -> [OK] User ditemukan dengan ID: $moodleUserId\n";
    } else {
        echo "   -> [INFO] User tidak ditemukan.\n";
    }
} catch (\Exception $e) {
    echo "   -> [ERROR] Gagal cek user: " . $e->getMessage() . "\n";
}

if (!$moodleUserId) {
    echo "\n2. Buat User Baru di Moodle...\n";
    try {
        $userData = [
            'email' => $email,
            'name' => 'Andhika Test',
            'firstname' => 'Andhika',
            'lastname' => '12502241004',
            'password' => 'Pass@word123!',
        ];
        $moodleUserId = $service->createMoodleUser($userData);
        echo "   -> [OK] User berhasil dibuat! ID: $moodleUserId\n";
    } catch (\Exception $e) {
        echo "   -> [ERROR] Gagal buat user: " . $e->getMessage() . "\n";
    }
}

if ($moodleUserId) {
    echo "\n3. Daftarkan (Enroll) User ID $moodleUserId ke Moodle Course ID $courseId...\n";
    try {
        $service->enrollUserInCourse($moodleUserId, $courseId);
        echo "   -> [OK] Berhasil didaftarkan ke course!\n";
    } catch (\Exception $e) {
        echo "   -> [ERROR] Gagal mendaftar: " . $e->getMessage() . "\n";
    }
}

echo "\n--- Simulasi Selesai ---\n";
