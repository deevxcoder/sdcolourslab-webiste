<?php
/**
 * SD Colours Lab - Live Server Database Repair Script
 * Instructions:
 * 1. Upload this file to your 'api/' folder on the live server.
 * 2. Visit: https://sdcolourslab.in/api/repair_database.php
 * 3. Delete this file immediately after running for security.
 */

// Use the existing config if possible, or define manually
require_once __DIR__ . '/../includes/db.php';

try {
    $db = getDB();
    echo "<h2>SD Colours Lab - Database Repair</h2>";
    echo "Connected to database: OK<br>";

    // 1. Create api_tokens table
    $db->exec("CREATE TABLE IF NOT EXISTS api_tokens (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        token VARCHAR(255) UNIQUE NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table 'api_tokens': OK<br>";

    // 2. Create announcements table
    $db->exec("CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type VARCHAR(50) DEFAULT 'info',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table 'announcements': OK<br>";

    // 3. Create lab_settings table
    $db->exec("CREATE TABLE IF NOT EXISTS lab_settings (
        `key` VARCHAR(100) PRIMARY KEY,
        `value` TEXT,
        `description` VARCHAR(255)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table 'lab_settings': OK<br>";

    // 4. Seed default settings
    $defaults = [
        ['lab_name', 'SD Colours Lab', 'Laboratory Display Name'],
        ['contact_phone', '+91 9078066947', 'Primary contact number'],
        ['contact_email', 'admin@sdcolours.com', 'Primary contact email'],
        ['currency_symbol', '₹', 'Currency symbol']
    ];
    $stmt = $db->prepare("INSERT IGNORE INTO lab_settings (`key`, `value`, `description`) VALUES (?, ?, ?)");
    foreach ($defaults as $row) {
        $stmt->execute($row);
    }
    echo "Default settings: OK<br>";

    // 5. Reset Admin Password to 'admin123'
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = 'admin@sdcolours.com'");
    $stmt->execute([$newHash]);
    echo "Admin password reset to 'admin123': OK<br>";

    echo "<p style='color:green'><b>Success! All repairs completed. Please upload the fixes and delete this script.</b></p>";

} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
