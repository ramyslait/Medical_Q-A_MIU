<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $conn = Database::getConnection();
    
    $userId = $_GET['id'] ?? null;
    
    if (!$userId) {
        echo json_encode(['success' => false, 'error' => 'User ID required']);
        exit;
    }
    
    // Get user details
    $sql = "SELECT 
                id,
                name,
                email,
                role,
                CASE 
                    WHEN is_verified = 1 THEN 'active'
                    ELSE 'pending'
                END as status,
                created_at as joinDate
            FROM users 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'User not found'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>