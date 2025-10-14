<?php
require_once '../config/db.php'; // your database connection file

try {
    // Get the database connection
    $conn = Database::getConnection();

    // Enable foreign key constraints (important for MySQL)
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Create the users table
    $sql = "
        CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        verification_code VARCHAR(6),
        verification_expires_at DATETIME DEFAULT NULL, -- 🕒 email verification expiry
        reset_token VARCHAR(6) DEFAULT NULL,          -- 🕒 password reset token
        reset_expires_at DATETIME DEFAULT NULL,        -- 🕒 password reset expiry
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        role ENUM('user', 'admin', 'doctor') DEFAULT 'user',
        is_verified TINYINT(1) DEFAULT 0              -- 0 = not verified, 1 = verified
        );
    ";
    $conn->exec($sql);

    // Create the questions table
    $sql = "
        CREATE TABLE IF NOT EXISTS questions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            body TEXT NOT NULL,
            category VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('pending', 'answered', 'closed') DEFAULT 'pending',
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ";
    $conn->exec($sql);

    echo "✅ Tables 'users' and 'questions' created or verified successfully.";
} catch (PDOException $e) {
    echo "❌ Error creating tables: " . $e->getMessage();
}
?>
