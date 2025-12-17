<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $conn = Database::getConnection();

    // Build query with optional filters
    $whereConditions = [];
    $params = [];

    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $whereConditions[] = "q.status = :status";
        $params[':status'] = $_GET['status'];
    }

    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $whereConditions[] = "q.category = :category";
        $params[':category'] = $_GET['category'];
    }

    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

    // Query questions with user information and doctor review data
    $sql = "
        SELECT 
            q.id,
            q.title,
            q.body,
            q.category,
            q.status,
            q.created_at,
            q.ai_answer,
            q.doctor_approval_status,
            q.doctor_answer,
            q.doctor_comment,
            q.doctor_reviewed_at,
            u.name as user_name,
            u.email as user_email,
            d.name as doctor_name,
            d.id as doctor_id
        FROM questions q
        LEFT JOIN users u ON q.user_id = u.id
        LEFT JOIN users d ON q.doctor_id = d.id
        $whereClause
        ORDER BY q.created_at DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
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
            'doctor_approval_status' => $q['doctor_approval_status'] ?? 'pending',
            'doctor_answer' => $q['doctor_answer'],
            'doctor_comment' => $q['doctor_comment'],
            'doctor_reviewed_at' => $q['doctor_reviewed_at'],
            'doctor_name' => $q['doctor_name'],
            'doctor_id' => $q['doctor_id'],
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
