<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'doctor') {
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized. Only doctors can review questions.'
    ]);
    exit;
}

// Get doctor ID from session
$doctorId = $_SESSION['user']['id'] ?? null;
if (!$doctorId) {
    echo json_encode([
        'success' => false,
        'error' => 'User ID not found in session'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
    exit;
}

try {
    $conn = Database::getConnection();
    
    $questionId = $_POST['question_id'] ?? null;
    $action = $_POST['action'] ?? null; // 'approve' or 'disapprove'
    $doctorComment = $_POST['doctor_comment'] ?? null;
    $doctorAnswer = $_POST['doctor_answer'] ?? null;

    if (!$questionId || !$action) {
        echo json_encode([
            'success' => false,
            'error' => 'Question ID and action are required'
        ]);
        exit;
    }

    if ($action === 'approve') {
        $stmt = $conn->prepare("
            UPDATE questions 
            SET doctor_approval_status = 'approved',
                doctor_id = :doctor_id,
                doctor_comment = :doctor_comment,
                doctor_answer = NULL,
                doctor_reviewed_at = NOW()
            WHERE id = :question_id
        ");
        
        $stmt->execute([
            ':doctor_id' => $doctorId,
            ':doctor_comment' => $doctorComment ?: null,
            ':question_id' => $questionId
        ]);

    } elseif ($action === 'disapprove') {
        if (empty($doctorAnswer)) {
            echo json_encode([
                'success' => false,
                'error' => 'Doctor answer is required when disapproving'
            ]);
            exit;
        }

        $stmt = $conn->prepare("
            UPDATE questions 
            SET doctor_approval_status = 'not_approved',
                doctor_id = :doctor_id,
                doctor_answer = :doctor_answer,
                doctor_comment = NULL,
                doctor_reviewed_at = NOW()
            WHERE id = :question_id
        ");
        
        $stmt->execute([
            ':doctor_id' => $doctorId,
            ':doctor_answer' => $doctorAnswer,
            ':question_id' => $questionId
        ]);

    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action. Must be "approve" or "disapprove"'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Question reviewed successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

