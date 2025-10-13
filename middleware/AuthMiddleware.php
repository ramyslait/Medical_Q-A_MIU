<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function decryptCookie($cookie, $key)
{
    $data = base64_decode($cookie);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    return json_decode($decrypted, true);
}

function requireRole($role)
{
    $key = $_ENV['ENCRYPTION_KEY'] ?? '';

    if (!isset($_COOKIE['user'])) {
        header("Location: /Medical_Q-A_MIU/public/login");
        exit();
    }

    $userData = decryptCookie($_COOKIE['user'], $key);

    if (!$userData || $userData['role'] !== $role) {
        header("Location: /Medical_Q-A_MIU/public/unauthorized");
        exit();
    }
}
function requireAuth()
{
    $key = $_ENV['ENCRYPTION_KEY'] ?? '';

    if (!isset($_COOKIE['user'])) {
        header("Location: /Medical_Q-A_MIU/public/login");
        exit();
    }

    $userData = decryptCookie($_COOKIE['user'], $key);

    if (!$userData) {
        header("Location: /Medical_Q-A_MIU/public/login");
        exit();
    }
}