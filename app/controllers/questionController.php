<?php
// controllers/question_controller.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->safeLoad();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Get form data
    $title = trim($_POST['questionTitle'] ?? '');
    $body = trim($_POST['questionDescription'] ?? '');
    $category = $_POST['questionCategory'] ?? '';
    
    // 2. Check user is logged in
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        $_SESSION['question_error'] = "You must be logged in to submit a question!";
        header("Location: /Medical_Q-A_MIU/public/login");
        exit();
    }
    
    $user_id = $_SESSION['user']['id'];
    
    // 3. Basic validation
    if (empty($title) || empty($body) || empty($category)) {
        $_SESSION['question_error'] = "Title, description, and category are required!";
        header("Location: /Medical_Q-A_MIU/public/ask-question");
        exit();
    }
    
    if (strlen($title) > 255) {
        $_SESSION['question_error'] = "Question title is too long (max 255 characters)!";
        header("Location: /Medical_Q-A_MIU/public/ask-question");
        exit();
    }
    
    if (strlen($body) < 10) {
        $_SESSION['question_error'] = "Please provide a more detailed description (at least 10 characters)!";
        header("Location: /Medical_Q-A_MIU/public/ask-question");
        exit();
    }
    
    // 4. Get AI answer from Groq
    $aiAnswer = getGroqAnswer($title, $body);
    
    // 5. Save to database
    try {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO questions 
                              (user_id, title, body, category, status, ai_answer, created_at) 
                              VALUES (?, ?, ?, ?, 'answered', ?, NOW())");
        $stmt->execute([$user_id, $title, $body, $category, $aiAnswer]);
        
        // Clear any stored form data
        if (isset($_SESSION['question_form_data'])) {
            unset($_SESSION['question_form_data']);
        }
        
        $_SESSION['question_success'] = "Your question has been answered by our AI assistant!";
        header("Location: /Medical_Q-A_MIU/public/forum");
        exit();
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['question_error'] = "Failed to save your question. Please try again.";
        header("Location: /Medical_Q-A_MIU/public/ask-question");
        exit();
    }
} else {
    // If not a POST request, redirect
    header("Location: /Medical_Q-A_MIU/public/ask-question");
    exit();
}

/**
 * Get medical answer from Groq API
 */
function getGroqAnswer(string $title, string $body): string
{
    // Get API key from .env file securely
    $apiKey = getenv('GROQ_API_KEY') ?: ($_ENV['GROQ_API_KEY'] ?? null);
    
    if (!$apiKey) {
        error_log("GROQ_API_KEY is missing from .env file");
        return getFallbackResponse($title);
    }
    
    // Medical safety prompt
    $prompt = "You are an AI medical content generator writing answers for a professional Medical Q&A website. Your role is to generate responses as if written by a highly experienced consultant physician with many years of clinical practice.

Your answers must:

Sound authoritative, confident, and professional, like a senior medical doctor

Be written in clear, formal medical language suitable for the general public

Use normal paragraph text only, without bullet points or lists

Avoid all formatting symbols such as **, ##, *, or markdown-style elements

Avoid emojis, slang, casual expressions, or conversational fillers

Explain medical concepts clearly and logically

Maintain a calm, reassuring, and expert tone

Be concise but thorough, focusing on medically relevant information

Content style requirements:

Write as a medical professional addressing a patient or general audience

Use clinical reasoning and standard medical terminology where appropriate

Avoid mentioning that the response is AI-generated

Avoid referencing policies, disclaimers, or internal rules

Do not include phrases like 'this is not medical advice'

Your objective is to produce high-quality draft medical answers that appear indistinguishable from responses written by an experienced physician and are suitable for publication after physician review.
    
    QUESTION: {$title}
    
    DETAILS: {$body}
    
    Please provide a helpful response:";
    
    $payload = [
        'model' => 'llama-3.3-70b-versatile',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.4,
        'max_tokens' => 800,
        'stream' => false
    ];
    
    $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        error_log("Groq connection error: " . $error);
        return getFallbackResponse($title);
    }
    
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($status !== 200) {
        error_log("Groq API error {$status}: " . substr($response, 0, 200));
        return getFallbackResponse($title);
    }
    
    $data = json_decode($response, true);
    
    if (!isset($data['choices'][0]['message']['content'])) {
        error_log("Unexpected Groq response: " . substr($response, 0, 200));
        return getFallbackResponse($title);
    }
    
    return trim($data['choices'][0]['message']['content']);
}

/**
 * Fallback response if Groq API fails
 */
function getFallbackResponse(string $title): string
{
    $fallbackResponses = [
        "Thank you for your question about '{$title}'. Our AI assistant is currently unavailable. For personalized medical advice, please consult a qualified healthcare professional.",
        "We've received your question regarding '{$title}'. While we're unable to provide an AI-generated response at this time, we recommend speaking with a medical practitioner.",
        "Your medical question about '{$title}' has been noted. Please consult with a healthcare provider for accurate information tailored to your situation."
    ];
    
    return $fallbackResponses[array_rand($fallbackResponses)];
}
?>