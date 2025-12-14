<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once '../config/db.php';
$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /Medical_Q-A_MIU/public/admin-dashboard');
    exit();
}

$user = $_SESSION['user'] ?? null;
if (!$user || !in_array($user['role'] ?? '', ['admin', 'doctor'])) {
    $_SESSION['admin_error'] = 'You are not authorized to perform that action.';
    header('Location: /Medical_Q-A_MIU/public/admin-dashboard');
    exit();
}

$question_id = intval($_POST['question_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($question_id <= 0) {
    $_SESSION['admin_error'] = 'Invalid question id.';
    header('Location: /Medical_Q-A_MIU/public/admin-dashboard');
    exit();
}

try {
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE questions SET ai_approved = 1, status = 'answered' WHERE id = :id");
        $stmt->execute([':id' => $question_id]);
        $_SESSION['admin_success'] = 'AI answer approved.';
    } else {
        // reject: clear ai_answer and mark generated flag off so doctors can answer manually
        $stmt = $pdo->prepare("UPDATE questions SET ai_approved = 0, ai_generated = 0, ai_answer = NULL WHERE id = :id");
        $stmt->execute([':id' => $question_id]);
        $_SESSION['admin_success'] = 'AI answer rejected and cleared.';
    }
} catch (PDOException $e) {
    error_log('Approve AI answer error: ' . $e->getMessage());
    $_SESSION['admin_error'] = 'Failed to update answer status.';
}

header('Location: /Medical_Q-A_MIU/public/admin-dashboard');
exit();
?>
