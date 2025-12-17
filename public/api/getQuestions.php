<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $conn = Database::getConnection();

    // Query questions with user information
    $stmt = $conn->query("
        SELECT 
            q.id,
            q.title,
            q.body,
            q.category,
            q.status,
            q.created_at,
            q.ai_answer,
            u.name as user_name,
            u.email as user_email
        FROM questions q
        LEFT JOIN users u ON q.user_id = u.id
        ORDER BY q.created_at DESC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $questions = array_map(function ($q) {
        return [
            'id' => $q['id'],
            'title' => $q['title'],
            'body' => $q['body'],
            'category' => $q['category'],
            'status' => $q['status'],
            'created_at' => $q['created_at'],
            'ai_answer' => $q['ai_answer'],
            'user_name' => $q['user_name'],
            'user_email' => $q['user_email'],
            'preview' => substr($q['body'], 0, 100) . (strlen($q['body']) > 100 ? '...' : ''),
            'time_ago' => timeAgo($q['created_at'])
        ];
    }, $rows);

    echo json_encode([
        'success' => true,
        'questions' => $questions,
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
