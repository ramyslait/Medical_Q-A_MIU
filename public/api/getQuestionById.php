<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $conn = Database::getConnection();
    
    $questionId = $_GET['id'] ?? null;
    
    if (!$questionId) {
        echo json_encode([
            "success" => false,
            "error" => "Question ID is required"
        ]);
        exit;
    }

    // Query question with user information
    $stmt = $conn->prepare("
        SELECT 
            q.id,
            q.title,
            q.body,
            q.category,
            q.status,
            q.created_at,
            q.ai_answer,
            u.name as user_name,
            u.email as user_email,
            u.id as user_id
        FROM questions q
        LEFT JOIN users u ON q.user_id = u.id
        WHERE q.id = :id
    ");
    $stmt->execute([':id' => $questionId]);
    $question = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$question) {
        echo json_encode([
            "success" => false,
            "error" => "Question not found"
        ]);
        exit;
    }

    // Format the response
    $result = [
        'id' => $question['id'],
        'title' => $question['title'],
        'body' => $question['body'],
        'category' => $question['category'],
        'status' => $question['status'],
        'created_at' => $question['created_at'],
        'ai_answer' => $question['ai_answer'],
        'user_name' => $question['user_name'],
        'user_email' => $question['user_email'],
        'user_id' => $question['user_id'],
        'time_ago' => timeAgo($question['created_at'])
    ];

    echo json_encode([
        'success' => true,
        'question' => $result
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    return floor($time/31536000) . ' years ago';
}
?>

