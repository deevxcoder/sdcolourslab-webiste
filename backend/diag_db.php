<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain');

// Direct connection test - bypass getDB() to see exact error
$host = 'localhost';
$user = 'u953522373_root';
$pass = 'sd@SS@132B';
$dbname = 'u953522373_sdcolourslab';

echo "=== Hostinger DB Connection Test ===\n\n";
echo "Host: $host\nUser: $user\nDB: $dbname\n\n";

try {
    // First try connecting WITHOUT a specific DB to check credentials
    $pdo = new PDO("mysql:host=$host", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "✓ Credentials are VALID - Connected to MySQL\n\n";
    
    // Check if our database exists
    $dbs = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Available databases:\n";
    foreach ($dbs as $db) {
        echo "  - $db" . ($db === $dbname ? " ← TARGET" : "") . "\n";
    }
    echo "\n";
    
    if (in_array($dbname, $dbs)) {
        echo "✓ Database '$dbname' EXISTS\n\n";
        
        // Connect to it and check tables
        $pdo2 = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $tables = $pdo2->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables in database:\n";
        foreach ($tables as $t) { echo "  - $t\n"; }
        
        if (in_array('users', $tables)) {
            $count = $pdo2->query("SELECT COUNT(*) FROM users")->fetchColumn();
            echo "\n✓ users table has $count rows\n";
        } else {
            echo "\n✗ 'users' table NOT found - needs to be imported\n";
        }
    } else {
        echo "✗ Database '$dbname' does NOT exist\n";
        echo "  → Create it in Hostinger cPanel > MySQL Databases\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Connection FAILED: " . $e->getMessage() . "\n";
    echo "\nThis likely means:\n";
    echo "  1. Wrong credentials\n";
    echo "  2. User doesn't have permissions\n";
}
