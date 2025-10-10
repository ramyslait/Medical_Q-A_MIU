<?php
session_start();

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
    'feedback' => '../app/views/feedback.php',
    'admin-dashboard' => '../app/views/admin-dashboard.php'
];

if(array_key_exists($uri, $routes)) {
    require_once $routes[$uri];
} else {
    echo "404 Page Not Found";  
}
