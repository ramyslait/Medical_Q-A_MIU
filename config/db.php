<?php
class Database
{
    private static $conn = null;

    private static $host = "localhost";
    private static $user = "root";
    private static $pass = "";
    private static $dbname = "mediacal_q-a"; // ⚠️ make sure the name has no typos

    public static function getConnection()
    {
        if (self::$conn === null) {
            try {
                // Step 1: Connect to MySQL server (no DB yet)
                $tempConn = new PDO(
                    "mysql:host=" . self::$host,
                    self::$user,
                    self::$pass
                );
                $tempConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Step 2: Create the database if it doesn’t exist
                $dbName = self::$dbname;
                $tempConn->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                $tempConn = null; // Close temporary connection

                // Step 3: Connect to the database
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbname,
                    self::$user,
                    self::$pass
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                die("❌ Database connection failed: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
?>
