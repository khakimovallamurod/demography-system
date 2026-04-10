<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'reply' => 'Faqat POST so‘rov qabul qilinadi'], 405);
}

if (!is_logged_in()) {
    json_response(['ok' => false, 'reply' => 'Avval tizimga kiring'], 401);
}

$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput ?: '{}', true);
$message = trim((string) ($payload['message'] ?? ''));

if ($message === '') {
    json_response(['ok' => false, 'reply' => 'Savol matnini kiriting'], 422);
}

if (!gemini_is_enabled()) {
    json_response(['ok' => true, 'reply' => 'AI tizim hali ishga tushirilmagan']);
}

$roleLabel = is_admin() ? 'administrator' : 'talaba foydalanuvchi';
$prompt = "Foydalanuvchi roli: {$roleLabel}\n"
    . "Tizim nomi: " . SITE_NAME . "\n"
    . "Javob tili: o'zbek (lotin)\n"
    . "Javob uslubi: qisqa, aniq, foydali, web tizim yordamchisi kabi.\n"
    . "Savol: {$message}";

$result = gemini_generate_text($prompt, [
    'system_instruction' => "Sen demografiya o‘quv tizimi uchun AI yordamchisan. "
        . "Foydalanuvchilarga tizimdagi sahifalar, testlar, materiallar va umumiy o‘quv jarayoni bo‘yicha tushunarli yordam ber. "
        . "Noaniq yoki maxfiy narsalarni o‘ylab topma.",
    'temperature' => 0.6,
    'max_output_tokens' => 700,
]);

if (!$result['ok']) {
    $fallback = $result['message'] ?? 'AI tizim hali ishga tushirilmagan';
    if (!gemini_is_enabled()) {
        $fallback = 'AI tizim hali ishga tushirilmagan';
    }

    json_response([
        'ok' => false,
        'reply' => $fallback,
    ], 200);
}

json_response([
    'ok' => true,
    'reply' => $result['text'],
]);
