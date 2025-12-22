<?php
// api/activateUser.php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $userId = $data['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode(['success' => false, 'error' => 'User ID required']);
        exit;
    }
    
    require_once __DIR__ . '/../../config/db.php';
    $conn = Database::getConnection();
    
    // Activate user by setting is_verified = 1
    $sql = "UPDATE users SET is_verified = 1 WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $success = $stmt->execute([':id' => $userId]);
    
    echo json_encode([
        'success' => $success,
        'message' => 'User activated successfully',
        'rows_affected' => $stmt->rowCount()
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>