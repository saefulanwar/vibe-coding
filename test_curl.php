<?php
$url = 'https://glacier.uny.ac.id/lms/webservice/rest/server.php';
$postFields = http_build_query([
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

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Expect:', // Remove Expect: 100-continue
    'Accept-Encoding: identity', // Ask for uncompressed
]);

echo "Running pure cURL...\n";
$result = curl_exec($ch);
if ($result === false) {
    echo "cURL Error (" . curl_errno($ch) . "): " . curl_error($ch) . "\n";
} else {
    echo "cURL Success! Response:\n";
    echo $result . "\n";
}
curl_close($ch);
