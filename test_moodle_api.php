<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\MoodleService::class);
try {
    $email = 'test' . rand(1,9999) . '@student.uny.ac.id';
    echo "Creating user with email: " . $email . "\n";
    $id = $service->createMoodleUser([
        'email' => $email,
        'name' => 'Tester Bro'
    ]);
    echo "Created user ID: " . $id . "\n";
} catch (\Exception $e) {
    echo $e->getMessage();
}
