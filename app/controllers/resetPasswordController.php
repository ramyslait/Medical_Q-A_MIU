<?php
session_start();
require_once '../../config/db.php';

$base = '/Medical_Q-A_MIU/public';

// Ensure the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['reset_error'] = "⚠️ Invalid request method.";
    header("Location: {$base}/resetPassword");
    exit();
}

// Get user id from session (set when code was verified)
$user_id = $_SESSION['reset_user_id'] ?? null;
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmpassword'] ?? ''; // matches your form

// Check if session user_id exists
if (!$user_id) {
    $_SESSION['reset_error'] = "⚠️ No reset session found. Please request a new reset code.";
    header("Location: {$base}/forgetPassword");
    exit();
}

// Validate passwords
if (empty($password) || empty($confirmPassword)) {
    $_SESSION['reset_error'] = "⚠️ Please fill in both password fields.";
    header("Location: {$base}/resetPassword");
    exit();
}

if ($password !== $confirmPassword) {
    $_SESSION['reset_error'] = "❌ Passwords do not match.";
    header("Location: {$base}/resetPassword");
    exit();
}

if (strlen($password) < 8) {
    $_SESSION['reset_error'] = "⚠️ Password must be at least 8 characters long.";
    header("Location: {$base}/resetPassword");
    exit();
}

try {
    $pdo = Database::getConnection();

    // Fetch user from DB to ensure they exist
    $stmt = $pdo->prepare("SELECT id, reset_token, reset_expires_at FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['reset_error'] = "⚠️ User not found. Please request a new reset code.";
        header("Location: {$base}/forgetPassword");
        exit();
    }

    // Optional: check if token expired
    if (!empty($user['reset_expires_at']) && strtotime($user['reset_expires_at']) < time()) {
        $_SESSION['reset_error'] = "⏰ This reset code has expired. Please request a new one.";
        header("Location: {$base}/forgetPassword");
        exit();
    }

    // Hash new password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update password and clear reset token
    $updateStmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_expires_at = NULL WHERE id = :id");
    $updateStmt->execute([
        ':password' => $hashedPassword,
        ':id' => $user_id
    ]);

    // Clear session variables
    unset($_SESSION['reset_user_id']);
    unset($_SESSION['reset_user_email']); // optional if you stored it

    $_SESSION['reset_success'] = "✅ Your password has been updated. You can now log in with your new password.";
    header("Location: {$base}/login");
    exit();

} catch (PDOException $e) {
    error_log("Reset Password Error: " . $e->getMessage());
    $_SESSION['reset_error'] = "⚠️ Something went wrong while updating your password. Please try again.";
    header("Location: {$base}/resetPassword");
    exit();
}
