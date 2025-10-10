<?php
session_start();
require_once '../../config/db.php';

$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1️⃣ Check if fields are empty
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "⚠️ Please fill in all fields.";
        header("Location: /Medical_Q-A_MIU/public/login");
        exit();
    }

    // 2️⃣ Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "⚠️ Invalid email format.";
        header("Location: /Medical_Q-A_MIU/public/login");
        exit();
    }

    // 3️⃣ Fetch user from database
    $stmt = $pdo->prepare("SELECT id, name, password, is_verified, role FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['login_error'] = "❌ Email not found. Please register first.";
        header("Location: /Medical_Q-A_MIU/public/login");
        exit();
    }

    // 4️⃣ Check if user is verified
    if ($user['is_verified'] == 0) {
        $_SESSION['login_error'] = "⚠️ Your email is not verified. Please check your email for the verification code.";
        header("Location: /Medical_Q-A_MIU/public/login");
        exit();
    }

    // 5️⃣ Verify password
    if (!password_verify($password, $user['password'])) {
        $_SESSION['login_error'] = "❌ Incorrect password. Please try again.";
        header("Location: /Medical_Q-A_MIU/public/login");
        exit();
    }

    // 6️⃣ Login successful
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_email'] = $email;

    // 7️⃣ Set cookie for navbar (3 days)
    $cookieData = [
        'id' => $user['id'],
        'name' => $user['name'],
        'role' => $user['role'],
        'email' => $user['email']
    ];
    setcookie('user', json_encode($cookieData), time() + 3*24*60*60, "/", "", false, true); // 3 days, HttpOnly

    // 8️⃣ Redirect to home/dashboard
    header("Location: /Medical_Q-A_MIU/public/home");
    exit();

} else {
    $_SESSION['login_error'] = "⚠️ Invalid request method.";
    header("Location: /Medical_Q-A_MIU/public/login");
    exit();
}
