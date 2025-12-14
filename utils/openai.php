<?php
// Simple Gemini helper. Reads OPENAI_API_KEY from project .env (if present) — note: it's actually Gemini key
// Usage: require_once __DIR__ . '/openai.php';
//        $answer = generate_ai_answer($prompt);

function load_env_value($key)
{
    // First try getenv
    $val = getenv($key);
    if ($val !== false) return $val;

    // Try to parse .env in project root
    $envPath = __DIR__ . '/../.env';
    if (!file_exists($envPath)) return false;

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (!strpos($line, '=')) continue;
        list($k, $v) = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        // remove surrounding quotes
        $v = preg_replace('/^\"|\"$/', '', $v);
        if ($k === $key) return $v;
    }

    return false;
}

function generate_ai_answer($prompt)
{
    $apiKey = load_env_value('OPENAI_API_KEY'); // Note: using OPENAI_API_KEY but it's actually Gemini key
    if (!$apiKey) {
        error_log('Gemini API key missing. Set OPENAI_API_KEY in environment or .env');
        return false;
    }

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $apiKey;

    // Prepend system message to prompt
    $fullPrompt = 'You are a helpful, conservative medical assistant. Provide clear, evidence-based information, include a short safety disclaimer, and avoid giving definitive diagnoses. Keep the tone professional.' . "\n\n" . $prompt;

    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $fullPrompt]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $result = curl_exec($ch);
    if ($result === false) {
        error_log('Gemini request failed: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 300) {
        error_log('Gemini returned HTTP ' . $httpCode . ': ' . $result);
        return false;
    }

    $data = json_decode($result, true);
    if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        error_log('Gemini response missing expected fields: ' . $result);
        return false;
    }

    // Trim and return the content
    $content = trim($data['candidates'][0]['content']['parts'][0]['text']);
    return $content;
}

?>
