<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $conn = Database::getConnection();

    // Get user growth over last 7 days
    $userGrowth = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) <= ?");
        $stmt->execute([$date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $userGrowth[] = (int)$result['count'];
    }

    // Get question volume over last 7 days
    $questionVolume = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM questions WHERE DATE(created_at) = ?");
        $stmt->execute([$date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $questionVolume[] = (int)$result['count'];
    }

    // Get answers provided over last 7 days (AI answers)
    $answersProvided = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM questions WHERE DATE(created_at) = ? AND ai_answer IS NOT NULL AND ai_answer != ''");
        $stmt->execute([$date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $answersProvided[] = (int)$result['count'];
    }

    // Get category distribution
    $stmt = $conn->query("
        SELECT 
            category,
            COUNT(*) as count
        FROM questions
        WHERE category IS NOT NULL AND category != ''
        GROUP BY category
        ORDER BY count DESC
    ");
    $categoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $categoryDistribution = [];
    foreach ($categoryData as $row) {
        $categoryDistribution[] = [
            'category' => $row['category'],
            'count' => (int)$row['count']
        ];
    }

    // UPDATED: Get doctor approval status distribution (instead of regular status)
    $stmt = $conn->query("
        SELECT 
            CASE 
                WHEN doctor_approval_status IS NULL OR doctor_approval_status = '' THEN 'pending'
                ELSE doctor_approval_status 
            END as status,
            COUNT(*) as count
        FROM questions
        GROUP BY 
            CASE 
                WHEN doctor_approval_status IS NULL OR doctor_approval_status = '' THEN 'pending'
                ELSE doctor_approval_status 
            END
    ");
    $statusData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $statusDistribution = [];
    foreach ($statusData as $row) {
        $statusDistribution[] = [
            'status' => $row['status'],
            'count' => (int)$row['count']
        ];
    }

    // Get user registration by month (last 6 months)
    $monthlyUsers = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
        $stmt->execute([$month]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $monthlyUsers[] = [
            'month' => date('M Y', strtotime($month . '-01')),
            'count' => (int)$result['count']
        ];
    }

    // Get questions by month (last 6 months)
    $monthlyQuestions = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM questions WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
        $stmt->execute([$month]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $monthlyQuestions[] = [
            'month' => date('M Y', strtotime($month . '-01')),
            'count' => (int)$result['count']
        ];
    }

    // UPDATED: Calculate average doctor review response time
    $stmt = $conn->query("
        SELECT 
            AVG(TIMESTAMPDIFF(MINUTE, created_at, doctor_reviewed_at)) as avg_time
        FROM questions 
        WHERE doctor_reviewed_at IS NOT NULL 
        AND doctor_approval_status IN ('approved', 'not_approved')
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $avgResponseTime = $result['avg_time'] ? round((float)$result['avg_time']) : 0;

    // Get top categories
    $stmt = $conn->query("
        SELECT 
            category,
            COUNT(*) as count
        FROM questions
        WHERE category IS NOT NULL AND category != ''
        GROUP BY category
        ORDER BY count DESC
        LIMIT 5
    ");
    $topCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // NEW: Calculate doctor approval rate/accuracy
    $stmt = $conn->query("
        SELECT 
            SUM(CASE WHEN doctor_approval_status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN doctor_approval_status IN ('approved', 'not_approved') THEN 1 ELSE 0 END) as reviewed,
            COUNT(*) as total
        FROM questions
    ");
    $approvalData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $approvalRate = 0;
    if ($approvalData['reviewed'] > 0) {
        $approvalRate = round(($approvalData['approved'] / $approvalData['reviewed']) * 100, 1);
    }

    echo json_encode([
        'success' => true,
        'analytics' => [
            'userGrowth' => $userGrowth,
            'questionVolume' => $questionVolume,
            'answersProvided' => $answersProvided,
            'categoryDistribution' => $categoryDistribution,
            'statusDistribution' => $statusDistribution, // Now shows doctor_approval_status
            'monthlyUsers' => $monthlyUsers,
            'monthlyQuestions' => $monthlyQuestions,
            'avgResponseTime' => $avgResponseTime, // Now shows doctor review time
            'topCategories' => $topCategories,
            'approvalRate' => $approvalRate, // New: Doctor approval rate
            'approvalStats' => [ // New: Additional approval stats
                'approved' => (int)$approvalData['approved'],
                'reviewed' => (int)$approvalData['reviewed'],
                'total' => (int)$approvalData['total'],
                'pending_review' => (int)$approvalData['total'] - (int)$approvalData['reviewed']
            ]
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>