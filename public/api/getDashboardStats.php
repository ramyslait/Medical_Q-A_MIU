<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $conn = Database::getConnection();

    // Get approved questions count
    $stmt = $conn->query("SELECT COUNT(*) as total FROM questions WHERE doctor_approval_status = 'approved'");
    $approvedCount = $stmt->fetch()['total'];

    // Get total reviewed questions count (where status is not pending)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM questions WHERE doctor_approval_status != 'pending' AND doctor_approval_status IS NOT NULL");
    $reviewedQuestions = $stmt->fetch()['total'];

    // Get total questions count
    $stmt = $conn->query("SELECT COUNT(*) as total FROM questions");
    $totalQuestions = $stmt->fetch()['total'];

    // Calculate accuracy rate (approved questions / reviewed questions * 100)
    // Only calculate if there are reviewed questions
    $accuracyRate = $reviewedQuestions > 0 ? round(($approvedCount / $reviewedQuestions) * 100, 1) : 0;

    // Get recent activity (last 5 questions and last 3 users)
    $stmt = $conn->query("
        SELECT 
            'question' as type,
            q.id,
            q.title as text,
            q.created_at,
            u.name as user_name,
            q.doctor_approval_status,
            CONCAT('Question: ', q.title, ' (', COALESCE(q.doctor_approval_status, 'pending'), ')') as description
        FROM questions q
        LEFT JOIN users u ON q.user_id = u.id
        ORDER BY q.created_at DESC
        LIMIT 5
    ");
    $recentQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("
        SELECT 
            'user' as type,
            id,
            name as text,
            created_at,
            name as user_name,
            CONCAT('New user registered: ', name) as description
        FROM users
        ORDER BY created_at DESC
        LIMIT 3
    ");
    $recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combine and sort by date
    $recentActivity = array_merge($recentQuestions, $recentUsers);
    usort($recentActivity, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    $recentActivity = array_slice($recentActivity, 0, 5);

    // Format activity for frontend
    $formattedActivity = array_map(function($item) {
        $timeAgo = timeAgo($item['created_at']);
        $icon = $item['type'] === 'question' ? 'fas fa-question' : 'fas fa-user-plus';
        $text = $item['type'] === 'question' ? 'New question submitted' : 'New user registered';
        
        // Add approval status to question description
        if ($item['type'] === 'question') {
            $status = isset($item['doctor_approval_status']) ? $item['doctor_approval_status'] : 'pending';
            $statusText = ucfirst($status);
            $description = "Question: " . $item['text'] . " ($statusText)";
        } else {
            $description = $item['description'];
        }
        
        return [
            'type' => $item['type'],
            'text' => $text,
            'meta' => $item['user_name'] . ' • ' . $timeAgo,
            'icon' => $icon,
            'description' => $description
        ];
    }, $recentActivity);

    echo json_encode([
        'success' => true,
        'stats' => [
            'answersProvided' => (int)$approvedCount,
            'accuracyRate' => $accuracyRate,
            'totalQuestions' => (int)$totalQuestions,
            'reviewedQuestions' => (int)$reviewedQuestions, // Optional: include for debugging
            'approvalRate' => $reviewedQuestions > 0 ? round(($approvedCount / $reviewedQuestions) * 100, 1) . '%' : 'N/A' // Optional: formatted rate
        ],
        'recentActivity' => $formattedActivity
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