<?php
session_start(); // ✅ start the session
require_once '../../config/db.php';

$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if email exists in session
    if (!isset($_SESSION['user_email'])) {
        die("⚠️ Session expired. Please register again.");
    }

    $email = $_SESSION['user_email'];
    $enteredCode = trim($_POST['verification_code']);

    // Basic validation
    if (empty($enteredCode) || !preg_match('/^\d{6}$/', $enteredCode)) {
        die("⚠️ Please enter a valid 6-digit code.");
    }

    // Fetch user from database
    $stmt = $pdo->prepare("SELECT id, verification_code, verification_expires_at, is_verified FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("⚠️ User not found.");
    }

    if ($user['is_verified'] == 1) {
        die("✅ Your email is already verified. You can log in.");
    }

    // Check if code is expired
    $currentTime = date('Y-m-d H:i:s');
    if ($currentTime > $user['verification_expires_at']) {
        die("⚠️ Verification code has expired. Please request a new code.");
    }

    // Check if code matches
    if ($enteredCode == $user['verification_code']) {
        // Update user as verified
        $update = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = :id");
        $update->execute([':id' => $user['id']]);

        // Optional: remove email from session
        unset($_SESSION['user_email']);
        header("Location: /Medical_Q-A_MIU/public/login");
         exit();
    } else {
        die("❌ Incorrect verification code. Please try again.");
    }
} else {
    die("⚠️ Invalid request method.");
}
