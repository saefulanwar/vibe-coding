<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

try {
    $url = 'https://glacier.uny.ac.id/lms/webservice/rest/server.php';
    echo "Pinging $url via Laravel Http client (decode_content => false)...\n";
    $response = Http::withoutVerifying()
        ->asForm()
        ->withOptions([
            'decode_content' => false,
        ])
        ->post($url, [
            'wstoken' => '5d9f312e34175994aa57b6f7c6b31fdb',
            'wsfunction' => 'core_user_get_users',
            'moodlewsrestformat' => 'json',
            'criteria' => [
                [
                    'key' => 'email',
                    'value' => 'saefulanwar25@gmail.com'
                ]
            ]
        ]);
    echo "Status: " . $response->status() . "\n";
    echo "Body: " . $response->body() . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
