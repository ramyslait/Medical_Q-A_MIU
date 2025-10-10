<?php
require_once '../config/db.php'; // correct path

try {
    // Get the database connection
    $conn = Database::getConnection();

    // Create the users table if it doesn’t exist
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
    echo "✅ Users table checked or created successfully.";
} catch (PDOException $e) {
    echo "❌ Error creating table: " . $e->getMessage();
}
