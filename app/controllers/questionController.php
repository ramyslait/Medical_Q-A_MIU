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

        // Clear form data from session
        unset($_SESSION['question_form_data']);

        // Set success message
        $_SESSION['question_success'] = "✅ Your question has been submitted successfully! Our medical experts will review it and provide an answer soon.";
        
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
