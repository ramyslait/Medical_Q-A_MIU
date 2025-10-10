<?php
session_start();
require_once '../../config/db.php';

$base = '/Medical_Q-A_MIU/public';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['reset_error'] = "⚠️ Invalid request method.";
    header("Location: {$base}/submitCode");
    exit();
}

$reset_code = trim($_POST['reset_code'] ?? '');

// Basic validation
if (empty($reset_code) || strlen($reset_code) !== 6) {
    $_SESSION['reset_error'] = "⚠️ Please enter the 6-character reset code.";
    header("Location: {$base}/submitCode");
    exit();
}

$reset_code = strtoupper($reset_code);

try {
    $pdo = Database::getConnection();

    // Find user with this reset token
    $stmt = $pdo->prepare("SELECT id, email, reset_expires_at FROM users WHERE reset_token = :token LIMIT 1");
    $stmt->execute([':token' => $reset_code]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['reset_error'] = "❌ Invalid reset code. Please check the code and try again.";
        header("Location: {$base}/submitCode");
        exit();
    }

    // Check expiry
    if (!empty($user['reset_expires_at']) && strtotime($user['reset_expires_at']) < time()) {
        $_SESSION['reset_error'] = "⏰ This reset code has expired. Please request a new code.";
        header("Location: {$base}/forgetPassword");
        exit();
    }

    // Success: store user id/email in session for the reset form and redirect
    $_SESSION['reset_user_id'] = $user['id'];
    $_SESSION['reset_user_email'] = $user['email'];
    $_SESSION['reset_success'] = "✅ Code verified. You may now reset your password.";

    header("Location: {$base}/resetPassword");
    exit();
} catch (PDOException $e) {
    error_log("Submit Reset Code Error: " . $e->getMessage());
    $_SESSION['reset_error'] = "⚠️ Something went wrong. Please try again.";
    header("Location: {$base}/submitCode");
    exit();
}
