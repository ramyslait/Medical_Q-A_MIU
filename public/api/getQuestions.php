<?php
require_once __DIR__ . '/../../config/db.php';

// Set UTF-8 headers
header('Content-Type: application/json; charset=utf-8');

try {
    $conn = Database::getConnection();
    
    // FORCE UTF-8 encoding on the connection
    $conn->exec("SET NAMES 'utf8mb4'");
    $conn->exec("SET CHARACTER SET utf8mb4");
    
    // Build query with optional filters
    $whereConditions = [];
    $params = [];
// In the WHERE conditions section of getQuestions.php, add:
if (isset($_GET['doctor_approval_status']) && !empty($_GET['doctor_approval_status'])) {
    $statusValue = $_GET['doctor_approval_status'];
    if ($statusValue === 'pending') {
        // Include both NULL and 'pending' values
        $whereConditions[] = "(q.doctor_approval_status IS NULL OR q.doctor_approval_status = 'pending')";
    } else {
        $whereConditions[] = "q.doctor_approval_status = :doctor_approval_status";
        $params[':doctor_approval_status'] = $statusValue;
    }
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
        // Ensure all string fields are UTF-8 encoded
        return [
            'id' => $q['id'],
            'title' => mb_convert_encoding($q['title'] ?? '', 'UTF-8', 'UTF-8'),
            'body' => mb_convert_encoding($q['body'] ?? '', 'UTF-8', 'UTF-8'),
            'category' => $q['category'],
            'status' => $q['status'],
            'created_at' => $q['created_at'],
            'ai_answer' => mb_convert_encoding($q['ai_answer'] ?? '', 'UTF-8', 'UTF-8'),
            'doctor_approval_status' => $q['doctor_approval_status'] ?? 'pending',
            'doctor_answer' => mb_convert_encoding($q['doctor_answer'] ?? '', 'UTF-8', 'UTF-8'),
            'doctor_comment' => mb_convert_encoding($q['doctor_comment'] ?? '', 'UTF-8', 'UTF-8'),
            'doctor_reviewed_at' => $q['doctor_reviewed_at'],
            'doctor_name' => mb_convert_encoding($q['doctor_name'] ?? '', 'UTF-8', 'UTF-8'),
            'doctor_id' => $q['doctor_id'],
            'user_name' => mb_convert_encoding($q['user_name'] ?? '', 'UTF-8', 'UTF-8'),
            'user_email' => $q['user_email'],
            'preview' => mb_substr(mb_convert_encoding($q['body'] ?? '', 'UTF-8', 'UTF-8'), 0, 100) . 
                         (mb_strlen($q['body'] ?? '') > 100 ? '...' : ''),
            'time_ago' => timeAgo($q['created_at'])
        ];
    }, $rows);

    // Use JSON_UNESCAPED_UNICODE to preserve Arabic characters
    echo json_encode([
        'success' => true,
        'questions' => $questions,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
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