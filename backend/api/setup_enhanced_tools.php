<?php
require_once __DIR__ . '/helpers.php';

try {
    $db = getDB();
    
    // Create announcements table
    $db->exec("CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type VARCHAR(50) DEFAULT 'info',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'announcements' initialized.\n";

    // Create lab_settings table
    $db->exec("CREATE TABLE IF NOT EXISTS lab_settings (
        `key` VARCHAR(100) PRIMARY KEY,
        `value` TEXT,
        `description` VARCHAR(255)
    )");
    echo "Table 'lab_settings' initialized.\n";

    // Seed default settings
    $defaults = [
        ['lab_name', 'SD Colours Lab', 'Laboratory Display Name'],
        ['contact_phone', '+91 98765 43210', 'Primary contact number'],
        ['contact_email', 'contact@sdcolourslab.in', 'Primary contact email'],
        ['address', '123, Colour Street, Art City', 'Physical address'],
        ['tax_rate', '18', 'Default GST/Tax rate in %'],
        ['currency_symbol', '₹', 'Currency symbol to display']
    ];

    $stmt = $db->prepare("INSERT IGNORE INTO lab_settings (`key`, `value`, `description`) VALUES (?, ?, ?)");
    foreach ($defaults as $row) {
        $stmt->execute($row);
    }
    echo "Default settings seeded.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
