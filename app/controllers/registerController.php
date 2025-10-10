<?php
require_once '../../config/db.php';

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

    // Prepare SQL statement (safe from SQL injection)
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, verification_code, verification_expires_at)
                    VALUES (:name, :email, :password, :role, :verification_code, :verification_expires_at)");

    try {
        $stmt->execute([
            ':name' => $fullName,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role === 'provider' ? 'doctor' : 'user', // map roles
            ':verification_code' => $verification_code,
            ':verification_expires_at' => $verification_expires_at
        ]);

        echo "✅ Registration successful! Please verify your email.";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // duplicate email
            echo "⚠️ Email already exists.";
        } else {
            echo "❌ Registration failed: " . $e->getMessage();
        }
    }
}
