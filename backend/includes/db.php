<?php
function getDB() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    // Load configuration
    $configPath = __DIR__ . '/config.php';
    if (file_exists($configPath)) {
        // require_once $configPath; // Temporarily bypassed for local SQLite dev
    }

    // 1. Check for environment variable (Heroku/Render/etc)
    $dsn = getenv('DATABASE_URL');
    
    // 2. Use config.php settings if loaded
    if (!$dsn && defined('DB_NAME')) {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            return $pdo;
        } catch (PDOException $e) {
            if (defined('DISPLAY_ERRORS') && DISPLAY_ERRORS) {
                die("Database Connection Error: " . $e->getMessage());
            } else {
                die("Database Connection Error. Please contact administrator.");
            }
        }
    }

    // 3. Last Resort: Default Local Fallback with SQLite auto-creation
    if (!$dsn) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=sdcolourslab", "root", "", [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            return $pdo;
        } catch (PDOException $e) {
            // SQLite Fallback for Local Dev
            $sqlitePath = __DIR__ . '/../dev.sqlite';
            $pdo = new PDO("sqlite:$sqlitePath");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Auto-create schema if empty
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT, role TEXT, status TEXT, password_hash TEXT);");
            $pdo->exec("CREATE TABLE IF NOT EXISTS orders (id INTEGER PRIMARY KEY AUTOINCREMENT, photographer_id INTEGER, status TEXT, total REAL, created_at TEXT DEFAULT CURRENT_TIMESTAMP);");
            $pdo->exec("CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, category TEXT, description TEXT, price REAL, price_alt REAL, sizes TEXT, features TEXT, tag TEXT, image TEXT, active INTEGER, sort_order INTEGER, created_at TEXT DEFAULT CURRENT_TIMESTAMP);");
            
            // Insert mock admin if users is empty
            if ($pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() == 0) {
                $hash = password_hash('admin123', PASSWORD_DEFAULT);
                $pdo->exec("INSERT INTO users (name, email, role, status, password_hash) VALUES ('Admin', 'admin@sdcolours.com', 'admin', 'approved', '$hash')");
            }
            return $pdo;
        }
    }

    // 4. Handle DATABASE_URL (for cloud hosting)
    if (strpos($dsn, 'postgres') === 0) {
        $parsed = parse_url($dsn);
        $pdoDsn = "pgsql:host={$parsed['host']};port=" . ($parsed['port'] ?? 5432) . ";dbname=" . ltrim($parsed['path'], '/') . ";sslmode=disable";
        $pdo = new PDO($pdoDsn, $parsed['user'], $parsed['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } else {
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    return $pdo;
}

function getLEDFrameDiscountPrice($qty, $features, $basePrice) {
    if (!is_array($features)) {
        return (float)$basePrice;
    }
    
    $activePrice = (float)$basePrice;
    $maxQtyThreshold = 0;
    
    foreach ($features as $f) {
        if (preg_match('/Qty\s+(\d+)\+:\s*₹?\s*(\d+)/iu', $f, $matches)) {
            $threshold = (int)$matches[1];
            $price = (float)$matches[2];
            if ($qty >= $threshold && $threshold > $maxQtyThreshold) {
                $maxQtyThreshold = $threshold;
                $activePrice = $price;
            }
        }
    }
    
    return $activePrice;
}

