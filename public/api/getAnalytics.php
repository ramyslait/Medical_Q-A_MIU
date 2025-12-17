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

    // Get answers provided over last 7 days
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

    // Get status distribution
    $stmt = $conn->query("
        SELECT 
            status,
            COUNT(*) as count
        FROM questions
        GROUP BY status
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

    // Calculate average response time (for questions with answers, assume immediate response for now)
    // Since questions are answered immediately when created, response time is near 0
    // In a real system, you'd track when the answer was generated
    $avgResponseTime = 0; // Questions are answered immediately, so response time is minimal

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

    echo json_encode([
        'success' => true,
        'analytics' => [
            'userGrowth' => $userGrowth,
            'questionVolume' => $questionVolume,
            'answersProvided' => $answersProvided,
            'categoryDistribution' => $categoryDistribution,
            'statusDistribution' => $statusDistribution,
            'monthlyUsers' => $monthlyUsers,
            'monthlyQuestions' => $monthlyQuestions,
            'avgResponseTime' => $avgResponseTime,
            'topCategories' => $topCategories
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>

