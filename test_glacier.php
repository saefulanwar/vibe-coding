<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$token = config('services.moodle.token');
$baseUrl = rtrim(config('services.moodle.url'), '/');

$url = $baseUrl . '/local/web-service/create-user-glacier.php';
$params = [
    'token' => $token,
    'email' => 'glacier_test_' . rand(1,999) . '@student.uny.ac.id',
    'nama' => 'Test Glacier',
    'lastname' => 'User',
    'password' => 'P@ssw0rd123!',
    'auth' => 'oauth2',
    'country' => 'ID'
];

echo "URL: $url\n";
$response = Illuminate\Support\Facades\Http::get($url, $params);
echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
