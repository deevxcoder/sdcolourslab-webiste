<?php
require_once __DIR__ . '/db.php';

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    startSession();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isPhotographer() {
    startSession();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'photographer';
}

function requireLogin($redirect = '/login.php') {
    if (!isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /index.php');
        exit;
    }
}

function requirePhotographer() {
    requireLogin();
    if (!isPhotographer()) {
        header('Location: /index.php');
        exit;
    }
}

function getCurrentUser() {
    startSession();
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function loginUser($email, $password) {
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        if ($user['role'] !== 'admin' && $user['status'] !== 'approved') {
            return ['error' => 'Your account is pending approval by the admin.'];
        }
        startSession();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        return ['success' => true, 'role' => $user['role']];
    }
    return ['error' => 'Invalid email or password.'];
}

function getCartCount() {
    startSession();
    $cart = $_SESSION['cart'] ?? [];
    return array_sum(array_column($cart, 'quantity'));
}
