<?php
session_start();

if (empty($_SESSION['login'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

if (!$input || empty($input['prompt'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No prompt provided']);
    exit();
}

$apiKey = getenv('OPENAI_API_KEY') ?: 'YOUR_OPENAI_API_KEY';

$url = 'https://api.openai.com/v1/chat/completions';

$payload = json_encode([
    'model' => 'gpt-4.1-nano',
    'messages' => [
        [
            'role' => 'system',
            'content' => 'أنت مساعد طبي مبسط للمرضى. أجب دائمًا باللغة العربية فقط. أرجع النتيجة بصيغة JSON صحيحة فقط بدون أي نص إضافي.'
        ],
        [
            'role' => 'user',
            'content' => $input['prompt']
        ]
    ],
    'temperature' => 0.2,
    'max_tokens' => 800,
    'response_format' => [
        'type' => 'json_object'
    ]
], JSON_UNESCAPED_UNICODE);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ],
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL error: ' . $curlError]);
    exit();
}

$openaiData = json_decode($response, true);

if ($httpCode !== 200 || empty($openaiData['choices'][0]['message']['content'])) {
    http_response_code($httpCode ?: 500);
    echo json_encode([
        'error' => $openaiData['error']['message'] ?? 'Unknown error from OpenAI',
        'httpCode' => $httpCode
    ]);
    exit();
}

$text = $openaiData['choices'][0]['message']['content'];

echo json_encode([
    'content' => [
        ['type' => 'text', 'text' => $text]
    ]
], JSON_UNESCAPED_UNICODE);
