<?php
// editUser.php - FIXED VERSION
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (empty($data)) {
        echo json_encode(['success' => false, 'error' => 'No data received']);
        exit;
    }
    
    $userId = $data['user_id'] ?? null;
    $name = $data['name'] ?? null;
    $email = $data['email'] ?? null;
    $role = $data['role'] ?? null;
    $status = $data['status'] ?? null;
    
    if (!$userId) {
        echo json_encode(['success' => false, 'error' => 'User ID required']);
        exit;
    }
    
    // Connect to database
    require_once __DIR__ . '/../../config/db.php';
    $conn = Database::getConnection();
    
    // Build update query
    $updates = [];
    $params = [':id' => $userId];
    
    if ($name) {
        $updates[] = "name = :name";
        $params[':name'] = $name;
    }
    
    if ($email) {
        $updates[] = "email = :email";
        $params[':email'] = $email;
    }
    
    if ($role) {
        $updates[] = "role = :role";
        $params[':role'] = $role;
    }
    
    if ($status) {
        // Use is_verified column instead of is_active
        // active = 1 (verified), suspended = 0 (not verified)
        $updates[] = "is_verified = :status";
        $params[':status'] = ($status === 'active') ? 1 : 0;
    }
    
    if (empty($updates)) {
        echo json_encode(['success' => false, 'error' => 'No changes provided']);
        exit;
    }
    
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $success = $stmt->execute($params);
    
    echo json_encode([
        'success' => $success,
        'message' => 'User updated successfully',
        'rows_affected' => $stmt->rowCount()
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>