<?php
session_start();
require_once '../../config/db.php';
require_once '../../utils/mailer.php'; // include sendVerificationEmailsendResetPasswordCode() and generateResetCode()


$base = '/Medical_Q-A_MIU/public';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $_SESSION['forgot_error'] = "⚠️ Please enter your email.";
        header("Location: /Medical_Q-A_MIU/public/forgetPassword");

        exit();
    }

    try {
        $conn = Database::getConnection();

        // Check if user exists
        $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['forgot_error'] = "⚠️ No account found with that email.";
            header("Location: /Medical_Q-A_MIU/public/forgetPassword");
            exit();
        }

        // Generate a 6-character reset code
        $resetCode = generateResetCode();
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // 1 hour expiry

        // Save reset code and expiry in DB
        $updateStmt = $conn->prepare("UPDATE users SET reset_token = :token, reset_expires_at = :expiry WHERE id = :id");
        $updateStmt->execute([
            'token' => $resetCode,
            'expiry' => $expiry,
            'id' => $user['id']
        ]);

        // Send the reset code via email
        $mailSent = sendResetPasswordCode($email, $user['name'], $resetCode);

        if ($mailSent) {
            // ✅ Set a session variable to allow access to submitCode.php
            $_SESSION['reset_request_sent'] = true;
            header("Location: {$base}/submitCode");

            exit();
        } else {
            $_SESSION['forgot_error'] = "⚠️ Failed to send the reset code. Please try again later.";
            header("Location: {$base}/forgetPassword");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Forgot Password Error: " . $e->getMessage());
        $_SESSION['forgot_error'] = "⚠️ Something went wrong. Please try again.";
        header("Location: {$base}/forgetPassword");
        exit();
    }
} else {
    // Redirect if not POST
    header("Location: {$base}/forgetPassword");
    exit();
}
