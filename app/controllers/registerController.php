<?php
session_start(); // ✅ start the session
require_once '../../config/db.php';
require_once '../../utils/mailer.php'; // include sendVerificationEmail

$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Basic validation
    if (empty($role) || empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        die("All fields are required!");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format!");
    }

    if ($password !== $confirmPassword) {
        die("Passwords do not match!");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification code
    $verification_code = rand(100000, 999999);
    $verification_expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Prepare SQL statement
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, verification_code, verification_expires_at)
                    VALUES (:name, :email, :password, :role, :verification_code, :verification_expires_at)");

    try {
        $stmt->execute([
            ':name' => $fullName,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role === 'provider' ? 'doctor' : 'user',
            ':verification_code' => $verification_code,
            ':verification_expires_at' => $verification_expires_at
        ]);

        // Save user email in session for verification
        $_SESSION['user_email'] = $email;

        if (sendVerificationEmail($email, $fullName, $verification_code)) {
            // Redirect to verification page
            header("Location: /Medical_Q-A_MIU/public/verify-email");
            exit();
        } else {
            echo "⚠️ Registration successful, but failed to send verification email.";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // duplicate email
            echo "⚠️ Email already exists.";
        } else {
            echo "❌ Registration failed: " . $e->getMessage();
        }
    }
}
