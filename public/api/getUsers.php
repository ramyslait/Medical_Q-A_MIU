<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $conn = Database::getConnection();

    // Query available columns and map to frontend shape
    $stmt = $conn->query("SELECT id, name, email, role, is_verified, created_at FROM users");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $users = array_map(function ($u) {
        return [
            'id' => $u['id'],
            'name' => $u['name'],
            'email' => $u['email'],
            'role' => $u['role'],
            // Map is_verified -> status expected by frontend
            'status' => ((int)($u['is_verified'] ?? 0) === 1) ? 'active' : 'pending',
            // Map created_at -> join_date key used by frontend (then normalized in JS)
            'join_date' => $u['created_at'] ?? null,
            // Optional avatar; frontend will provide a fallback if null
            'avatar' => $u['avatar'] ?? null,
        ];
    }, $rows);

    echo json_encode([
        'success' => true,
        'users' => $users,
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
