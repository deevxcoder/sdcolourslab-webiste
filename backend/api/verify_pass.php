<?php
require_once __DIR__ . '/helpers.php';

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT password_hash FROM users WHERE email = ?");
    $stmt->execute(['admin@sdcolours.com']);
    $user = $stmt->fetch();
    
    if ($user) {
        $matches = password_verify('admin123', $user['password_hash']);
        echo "Password 'admin123' for admin@sdcolours.com: " . ($matches ? "MATCHES" : "DOES NOT MATCH") . "\n";
    } else {
        echo "User admin@sdcolours.com not found.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
