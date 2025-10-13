<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class Database
{
    private static $conn = null;

    public static function getConnection()
    {
        if (self::$conn === null) {
            try {
                $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
                $dotenv->load();

                $host = $_ENV['DB_HOST'];
                $user = $_ENV['DB_USER'];
                $pass = $_ENV['DB_PASS'];
                $dbname = $_ENV['DB_NAME'];

                // Connect to MySQL server (no DB yet)
                $tempConn = new PDO("mysql:host=$host", $user, $pass);
                $tempConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Create database if not exists
                $tempConn->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                $tempConn = null;

                // Connect to the specific database
                self::$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("❌ Database connection failed: " . $e->getMessage());
            }
        }

        return self::$conn;
    }
}
