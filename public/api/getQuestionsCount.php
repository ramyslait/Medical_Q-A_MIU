<?php
// getQuestionsCount.php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $conn = Database::getConnection();
    
    // Get counts by doctor_approval_status
    $query = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN doctor_approval_status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN doctor_approval_status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN doctor_approval_status = 'not_approved' THEN 1 ELSE 0 END) as not_approved,
        SUM(CASE WHEN doctor_approval_status IS NULL OR doctor_approval_status = '' THEN 1 ELSE 0 END) as no_status
        FROM questions";
    
    $stmt = $conn->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'counts' => [
            'total' => (int)$result['total'],
            'pending' => (int)$result['pending'],
            'approved' => (int)$result['approved'],
            'not_approved' => (int)$result['not_approved'],
            'no_status' => (int)$result['no_status']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>