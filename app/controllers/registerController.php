<?php
// Safe session start
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
} 

require_once '../../config/db.php';
require_once '../../utils/mailer.php';

// Use these variables to allow dependency injection in tests
$pdo = $pdo ?? Database::getConnection();
$mailer = $mailer ?? 'sendVerificationEmail';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    $_SESSION['form_data'] = $_POST;

    // Basic validation
    if (empty($role) || empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION['register_error'] = "⚠️ All fields are required!";
        $redirect = '/Medical_Q-A_MIU/public/register';
        goto end;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = "⚠️ Invalid email format!";
        $redirect = '/Medical_Q-A_MIU/public/register';
        goto end;
    }

    if ($password !== $confirmPassword) {
        $_SESSION['register_error'] = "⚠️ Passwords do not match!";
        $redirect = '/Medical_Q-A_MIU/public/register';
        goto end;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $verification_code = rand(100000, 999999);
    $verification_expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, verification_code, verification_expires_at)
                           VALUES (:name, :email, :password, :role, :code, :expires)");

    try {
        $stmt->execute([
            ':name' => $fullName,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role === 'provider' ? 'doctor' : 'user',
            ':code' => $verification_code,
            ':expires' => $verification_expires_at
        ]);

        unset($_SESSION['form_data']);
        $_SESSION['user_email'] = $email;

        if ($mailer($email, $fullName, $verification_code)) {
            $_SESSION['register_success'] = "✅ Registration successful! Please check your email for verification.";
            $redirect = '/Medical_Q-A_MIU/public/verify-email';
        } else {
            $_SESSION['register_error'] = "⚠️ Registered, but failed to send verification email.";
            $redirect = '/Medical_Q-A_MIU/public/register';
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['register_error'] = "⚠️ Email already exists!";
        } else {
            $_SESSION['register_error'] = "❌ Registration failed. Please try again later.";
        }
        $redirect = '/Medical_Q-A_MIU/public/register';
    }

    end:
    // Only redirect in real requests, not in tests
    if (!defined('PHPUNIT_RUNNING')) {
        header("Location: $redirect");
        exit();
    }
}
