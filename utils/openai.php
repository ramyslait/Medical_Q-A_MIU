<?php
// Simple OpenAI helper. Reads OPENAI_API_KEY from project .env (if present)
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
    $apiKey = load_env_value('OPENAI_API_KEY');
    if (!$apiKey) {
        error_log('OpenAI API key missing. Set OPENAI_API_KEY in environment or .env');
        return false;
    }

    $url = 'https://api.openai.com/v1/chat/completions';

    $payload = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a helpful, conservative medical assistant. Provide clear, evidence-based information, include a short safety disclaimer, and avoid giving definitive diagnoses. Keep the tone professional.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.2,
        'max_tokens' => 800,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $result = curl_exec($ch);
    if ($result === false) {
        error_log('OpenAI request failed: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 300) {
        error_log('OpenAI returned HTTP ' . $httpCode . ': ' . $result);
        return false;
    }

    $data = json_decode($result, true);
    if (!isset($data['choices'][0]['message']['content'])) {
        error_log('OpenAI response missing expected fields: ' . $result);
        return false;
    }

    // Trim and return the content
    $content = trim($data['choices'][0]['message']['content']);
    return $content;
}

?>
