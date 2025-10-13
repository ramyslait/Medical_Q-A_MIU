<?php
session_start();
require_once '../../config/db.php';
require_once '../../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$pdo = Database::getConnection();

// Encryption key from .env
$encryption_key = $_ENV['ENCRYPTION_KEY'] ?? '';

function encryptCookie($data, $key)
{
    $iv = random_bytes(16); // 16 bytes IV for AES-256-CBC
    $encrypted = openssl_encrypt(json_encode($data), 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted); // prepend IV
}

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
    $stmt = $pdo->prepare("SELECT id, name, email, password, is_verified, role FROM users WHERE email = :email");
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
        $_SESSION['login_error'] = "❌ Incorrect email or password. Please try again.";
        header("Location: /Medical_Q-A_MIU/public/login");
        exit();
    }

    // 6️⃣ Login successful
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_email'] = $email;

    // 7️⃣ Encrypted cookie with only ID + Role
    $cookieData = [
        'id' => $user['id'],
        'role' => $user['role'],
        'name' => $user['name']
    ];
    $encryptedCookie = encryptCookie($cookieData, $encryption_key);
    setcookie('user', $encryptedCookie, time() + 3 * 24 * 60 * 60, "/", "", false, true); // 3 days, HttpOnly

    // 8️⃣ Redirect based on role
    if (strtolower($user['role']) === 'admin') {
        header("Location: /Medical_Q-A_MIU/public/admin-dashboard");
    } else {
        header("Location: /Medical_Q-A_MIU/public/home");
    }
    exit();

} else {
    $_SESSION['login_error'] = "⚠️ Invalid request method.";
    header("Location: /Medical_Q-A_MIU/public/login");
    exit();
}
