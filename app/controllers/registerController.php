<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/db.php';
require_once '../../utils/mailer.php';

$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Save form data to session in case of validation errors
    $_SESSION['form_data'] = $_POST;

    // Basic validation
    if (empty($role) || empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION['register_error'] = "⚠️ All fields are required!";
        header("Location: /Medical_Q-A_MIU/public/register");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = "⚠️ Invalid email format!";
        header("Location: /Medical_Q-A_MIU/public/register");
        exit();
    }

    if ($password !== $confirmPassword) {
        $_SESSION['register_error'] = "⚠️ Passwords do not match!";
        header("Location: /Medical_Q-A_MIU/public/register");
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification details
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

        unset($_SESSION['form_data']); // Clear old input
        $_SESSION['user_email'] = $email;

        if (sendVerificationEmail($email, $fullName, $verification_code)) {
            $_SESSION['register_success'] = "✅ Registration successful! Please check your email for verification.";
            header("Location: /Medical_Q-A_MIU/public/verify-email");
        } else {
            $_SESSION['register_error'] = "⚠️ Registered, but failed to send verification email.";
            header("Location: /Medical_Q-A_MIU/public/register");
        }
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['register_error'] = "⚠️ Email already exists!";
        } else {
            $_SESSION['register_error'] = "❌ Registration failed. Please try again later.";
        }
        header("Location: /Medical_Q-A_MIU/public/register");
        exit();
    }
}
