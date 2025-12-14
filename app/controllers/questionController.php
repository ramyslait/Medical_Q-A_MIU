<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';

$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['questionTitle'] ?? '');
    $body = trim($_POST['questionDescription'] ?? '');
    $category = $_POST['questionCategory'] ?? '';
    $urgency = $_POST['urgency'] ?? 'normal';
    $age = $_POST['age'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $followUp = isset($_POST['followUp']) ? 1 : 0;

    // Save form data to session in case of validation errors
    $_SESSION['question_form_data'] = $_POST;

    // Basic validation
    if (empty($title) || empty($body) || empty($category)) {
        $_SESSION['question_error'] = "⚠️ Title, description, and category are required!";
        header("Location: /Medical_Q-A_MIU/public/ask-question");
        exit();
    }

    // Validate title length
    if (strlen($title) > 255) {
        $_SESSION['question_error'] = "⚠️ Question title is too long (max 255 characters)!";
        header("Location: /Medical_Q-A_MIU/public/ask-question");
        exit();
    }

    // Validate body length
    if (strlen($body) < 10) {
        $_SESSION['question_error'] = "⚠️ Please provide a more detailed description (at least 10 characters)!";
        header("Location: /Medical_Q-A_MIU/public/ask-question");
        exit();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        $_SESSION['question_error'] = "⚠️ You must be logged in to submit a question!";
        header("Location: /Medical_Q-A_MIU/public/login");
        exit();
    }

    $user_id = $_SESSION['user']['id'];

    // Prepare the question data for insertion
    $stmt = $pdo->prepare("INSERT INTO questions (user_id, title, body, category, created_at, status) 
                           VALUES (:user_id, :title, :body, :category, NOW(), 'pending')");

    try {
        $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':body' => $body,
            ':category' => $category
        ]);

        // Get the inserted question ID
        $question_id = $pdo->lastInsertId();
        // Attempt to generate an AI draft answer and store it as pending approval
        try {
            // Ensure helper is available
            require_once __DIR__ . '/../../utils/openai.php';

            // Add AI-related columns to `questions` table if they don't exist
            $colsStmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'questions'");
            $colsStmt->execute();
            $existing = $colsStmt->fetchAll(PDO::FETCH_COLUMN);

            $needed = [];
            if (!in_array('ai_answer', $existing)) $needed[] = "ADD COLUMN ai_answer TEXT NULL";
            if (!in_array('ai_generated', $existing)) $needed[] = "ADD COLUMN ai_generated TINYINT(1) NOT NULL DEFAULT 0";
            if (!in_array('ai_approved', $existing)) $needed[] = "ADD COLUMN ai_approved TINYINT(1) NOT NULL DEFAULT 0";

            if (!empty($needed)) {
                $alterSql = "ALTER TABLE questions " . implode(', ', $needed);
                $pdo->exec($alterSql);
            }

            // Build prompt for the AI (include title + body)
            $prompt = "Question title: " . $title . "\n\nDetails: " . $body . "\n\nProvide a concise, helpful, and careful medical response. Include a short safety disclaimer stating this is informational and not a substitute for professional care.";

            $aiAnswer = generate_ai_answer($prompt);

            if ($aiAnswer !== false && $aiAnswer !== null) {
                $update = $pdo->prepare("UPDATE questions SET ai_answer = :ai_answer, ai_generated = 1, ai_approved = 0 WHERE id = :id");
                $update->execute([':ai_answer' => $aiAnswer, ':id' => $question_id]);
                $_SESSION['question_success'] = "✅ Your question has been submitted. An AI draft answer has been generated and is pending doctor approval.";
            } else {
                // AI failed — keep the question in pending state for manual answer
                $_SESSION['question_success'] = "✅ Your question has been submitted successfully! Our medical experts will review it and provide an answer soon.";
            }
        } catch (Exception $e) {
            error_log('AI generation error: ' . $e->getMessage());
            $_SESSION['question_success'] = "✅ Your question has been submitted successfully! Our medical experts will review it and provide an answer soon.";
        }

        // Clear form data from session
        unset($_SESSION['question_form_data']);

        // Redirect to forum or a success page
        header("Location: /Medical_Q-A_MIU/public/forum");
        exit();
    } catch (PDOException $e) {
        // Log the error for debugging
        error_log("Question submission error: " . $e->getMessage());
        
        $_SESSION['question_error'] = "❌ Failed to submit your question. Please try again later.";
        header("Location: /Medical_Q-A_MIU/public/ask-question");
        exit();
    }
} else {
    // If not a POST request, redirect to ask-question page
    header("Location: /Medical_Q-A_MIU/public/ask-question");
    exit();
}
?>
