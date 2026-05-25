<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Unit;
use App\Models\CertificateTemplate;
use App\Models\Certificate;
use App\Jobs\ProcessCertificateJob;

try {
    // Ensure User
    $user = User::firstOrCreate(
        ['email' => 'test@example.com'],
        ['name' => 'John Doe', 'password' => bcrypt('password')]
    );

    // Ensure Course
    $course = Course::firstOrCreate(
        ['title' => 'Mastering Laravel 13 - Dasar hingga Mahir - Glacier'],
        [
            'description' => 'Dummy course', 
            'price' => 100000, 
            'thumbnail' => 'dummy.jpg', 
            'status' => 'published',
            'slug' => 'mastering-laravel-13'
        ]
    );

    // Ensure Template
    $template = CertificateTemplate::firstOrCreate(
        ['title' => 'Template Dummy Default'],
        [
            'background_image' => 'dummy.jpg',
            'font_color' => '#000000',
            'tag_koordinat' => '#'
        ]
    );

    // Delete old certificate if exists
    Certificate::where('user_id', $user->id)->where('course_id', $course->id)->delete();

    // Create Certificate record
    $certificate = Certificate::create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'template_id' => $template->id,
        'status' => 'pending',
        'student_name_snapshot' => $user->name,
        'course_title_snapshot' => $course->title,
        'completed_at' => now(),
    ]);

    // Run job synchronously
    $job = new ProcessCertificateJob($certificate);
    $job->handle();

    $certificate->refresh();

    echo "\n\n----------------------------------------\n";
    echo "Sertifikat berhasil dibuat (Status: " . $certificate->status . ")!\n";
    echo "File Path: " . $certificate->file_path . "\n";
    echo "Link Verifikasi: http://127.0.0.1:8000/verify/" . $certificate->id . "\n";
    echo "----------------------------------------\n\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
