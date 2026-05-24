<?php
/**
 * Database Migration Script
 * Adds missing columns and tables for checkout functionality
 * Run once: https://backend.sdcolourslab.in/migrate.php?key=sdcolours2026
 */

if (($_GET['key'] ?? '') !== 'sdcolours2026') {
    http_response_code(403);
    die('Unauthorized');
}

require_once __DIR__ . '/includes/db.php';
$db = getDB();

$results = [];

// 1. Add shipping_address column to orders table if missing
try {
    $db->exec("ALTER TABLE `orders` ADD COLUMN `shipping_address` TEXT DEFAULT NULL");
    $results[] = "✅ Added 'shipping_address' column to orders table.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false || strpos($e->getMessage(), 'already exists') !== false) {
        $results[] = "ℹ️ Column 'shipping_address' already exists in orders.";
    } else {
        $results[] = "❌ Error adding 'shipping_address': " . $e->getMessage();
    }
}

// 2. Create user_addresses table if it doesn't exist
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `user_addresses` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` int(10) UNSIGNED NOT NULL,
        `label` varchar(150) NOT NULL,
        `address_line` text NOT NULL,
        `city` varchar(100) NOT NULL,
        `state` varchar(100) NOT NULL,
        `pincode` varchar(20) NOT NULL,
        `phone` varchar(20) DEFAULT NULL,
        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `fk_addr_user` (`user_id`),
        CONSTRAINT `fk_addr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $results[] = "✅ Created 'user_addresses' table (or already exists).";
} catch (PDOException $e) {
    $results[] = "❌ Error creating 'user_addresses': " . $e->getMessage();
}

// Verify orders table columns
try {
    $cols = $db->query("SHOW COLUMNS FROM `orders`")->fetchAll(PDO::FETCH_COLUMN);
    $results[] = "ℹ️ Orders columns: " . implode(', ', $cols);
} catch (PDOException $e) {
    $results[] = "⚠️ Could not fetch orders columns: " . $e->getMessage();
}

// Verify user_addresses table
try {
    $count = $db->query("SELECT COUNT(*) FROM user_addresses")->fetchColumn();
    $results[] = "ℹ️ user_addresses rows: " . $count;
} catch (PDOException $e) {
    $results[] = "❌ Could not query user_addresses: " . $e->getMessage();
}

echo "<!DOCTYPE html><html><head><title>Migration</title></head><body>";
echo "<h2>DB Migration Results</h2><ul>";
foreach ($results as $r) {
    echo "<li>" . htmlspecialchars($r) . "</li>";
}
echo "</ul><p><strong>Done!</strong> You can delete migrate.php now.</p></body></html>";
