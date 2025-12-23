<?php

$ch = curl_init('https://api.resend.com/emails');

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer re_4zTKXHVy_P5Yk61VE1HjBRK2o5t6zGh2Y',
        'Content-Type: application/json',
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'from' => 'onboarding@resend.dev',
        'to' => ['pribadiagus321@gmail.com'],
        'subject' => 'TEST RESEND',
        'html' => '<b>MASUK = RESEND OK</b>',
    ]),
]);

$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "HTTP: $code\n";
echo $response;