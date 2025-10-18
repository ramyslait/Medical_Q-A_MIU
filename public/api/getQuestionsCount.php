<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $conn = Database::getConnection();

    // Get total questions count
    $stmt = $conn->query("SELECT COUNT(*) as total FROM questions");
    $totalCount = $stmt->fetch()['total'];

    // Get pending questions count
    $stmt = $conn->query("SELECT COUNT(*) as pending FROM questions WHERE status = 'pending'");
    $pendingCount = $stmt->fetch()['pending'];

    // Get answered questions count
    $stmt = $conn->query("SELECT COUNT(*) as answered FROM questions WHERE status = 'answered'");
    $answeredCount = $stmt->fetch()['answered'];

    // Get closed questions count
    $stmt = $conn->query("SELECT COUNT(*) as closed FROM questions WHERE status = 'closed'");
    $closedCount = $stmt->fetch()['closed'];

    echo json_encode([
        'success' => true,
        'counts' => [
            'total' => (int)$totalCount,
            'pending' => (int)$pendingCount,
            'answered' => (int)$answeredCount,
            'closed' => (int)$closedCount
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
