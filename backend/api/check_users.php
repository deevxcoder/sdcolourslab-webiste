<?php
require_once __DIR__ . '/helpers.php';

try {
    $db = getDB();
    $stmt = $db->query("SELECT id, name, email, role, status FROM users");
    $users = $stmt->fetchAll();
    
    echo "Current Users in Database:\n";
    foreach ($users as $u) {
        echo "ID: {$u['id']} | Name: {$u['name']} | Email: {$u['email']} | Role: {$u['role']} | Status: {$u['status']}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
