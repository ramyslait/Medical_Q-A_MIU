<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../utils/mailer.php'; // ADD THIS LINE

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
$doctorName = $_SESSION['user']['name'] ?? 'Doctor'; // ADD THIS
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

    // GET QUESTION DETAILS FOR EMAIL - ADD THIS SECTION
    $getQuestionStmt = $conn->prepare("
        SELECT q.*, u.email, u.name as user_name 
        FROM questions q 
        LEFT JOIN users u ON q.user_id = u.id 
        WHERE q.id = :question_id
    ");
    $getQuestionStmt->execute([':question_id' => $questionId]);
    $question = $getQuestionStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$question) {
        echo json_encode([
            'success' => false,
            'error' => 'Question not found'
        ]);
        exit;
    }

    $emailSent = false;
    $emailError = '';

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

        // SEND APPROVAL EMAIL - ADD THIS
        if (!empty($question['email'])) {
            $aiAnswer = $question['ai_answer'] ?? "Your question has been reviewed and approved by our medical team.";
            
            $emailSent = sendDoctorApprovalEmail(
                $question['email'],
                $question['user_name'],
                $question['body'],
                $aiAnswer,
                $doctorName,
                $doctorComment
            );
            
            if (!$emailSent) {
                $emailError = 'Approval email failed to send';
            }
        }

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

        // SEND DISAPPROVAL EMAIL - ADD THIS
        if (!empty($question['email'])) {
            // Check if disapproval function exists, otherwise use approval function
            if (function_exists('sendDoctorDisapprovalEmail')) {
                $emailSent = sendDoctorDisapprovalEmail(
                    $question['email'],
                    $question['user_name'],
                    $question['body'],
                    $doctorAnswer,
                    $doctorName
                );
            } else {
                // Fallback to approval email with custom message
                $emailSent = sendDoctorApprovalEmail(
                    $question['email'],
                    $question['user_name'],
                    $question['body'],
                    $doctorAnswer,
                    $doctorName,
                    "AI answer was not approved. Doctor has provided this answer instead."
                );
            }
            
            if (!$emailSent) {
                $emailError = 'Disapproval email failed to send';
            }
        }

    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action. Must be "approve" or "disapprove"'
        ]);
        exit;
    }

    // UPDATE RESPONSE WITH EMAIL STATUS - ADD THIS
    $response = [
        'success' => true,
        'message' => 'Question reviewed successfully'
    ];
    
    if ($emailSent) {
        $response['email_sent'] = true;
        $response['email_message'] = 'Email sent to user';
    } elseif ($emailError) {
        $response['email_sent'] = false;
        $response['email_error'] = $emailError;
    } elseif (empty($question['email'])) {
        $response['email_sent'] = false;
        $response['email_error'] = 'No user email found';
    }
    
    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>