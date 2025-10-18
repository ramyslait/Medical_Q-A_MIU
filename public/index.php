<?php
session_start();
require_once '../middleware/AuthMiddleware.php';
require_once '../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// ✅ Global user check (like app.use() in Node.js)
$user = null;
if (isset($_COOKIE['user'])) {
    $user = decryptCookie($_COOKIE['user'], $_ENV['ENCRYPTION_KEY']);
    if ($user && is_array($user)) {
        $_SESSION['user'] = $user; // store it for use in views or other controllers
    } else {
        unset($_SESSION['user']);
    }
} else {
    unset($_SESSION['user']);
}

// ----------------------------
// Routing logic
// ----------------------------

// Get the requested route
$uri = isset($_GET['url']) ? $_GET['url'] : 'home';
$uri = rtrim($uri, '/');

// Define routes
$routes = [
    '' => '../app/views/home.php',
    'home' => '../app/views/home.php',
    'login' => '../app/views/login.php',
    'register' => '../app/views/register.php',
    'forum' => '../app/views/forum.php',
    'ask-question' => '../app/views/ask-question.php',
    'submit-question' => '../app/controllers/questionController.php',
    'feedback' => '../app/views/feedback.php',
    'admin-dashboard' => '../app/views/admin-dashboard.php',
    'verify-email' => '../app/views/verify-email.php',
    'forgetPassword' => '../app/views/forgetPassword.php',
    'submitCode' => '../app/views/submitCode.php',
    'resetPassword' => '../app/views/resetPassword.php',
    'logout' => '../app/controllers/logoutController.php',
    '404' => '../app/views/404.php'
];

// ✅ Check if route exists
if (array_key_exists($uri, $routes)) {
    // 🧩 Protect certain routes
    switch ($uri) {
        case 'forum':
        case 'ask-question':
        case 'feedback':
            requireAuth(); // Must be logged in
            break;

        case 'admin-dashboard':
            requireRole('admin'); // Only admin allowed
            break;
    }

    // ✅ Include the requested view
    require_once $routes[$uri];
} else {
    header("Location: /Medical_Q-A_MIU/public/404");
    exit();
}
