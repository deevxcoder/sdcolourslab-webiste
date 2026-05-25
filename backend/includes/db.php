<?php
function getDB() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    // Load configuration
    $configPath = __DIR__ . '/config.php';
    $configLoaded = false;
    if (file_exists($configPath)) {
        require_once $configPath;
        $configLoaded = true;
    }

    // 1. Check for environment variable (Heroku/Render/etc)
    $dsn = getenv('DATABASE_URL');
    
    // Check if we are running locally or on the testing server
    $isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', 'localhost:8000', 'localhost:8001', '127.0.0.1', '127.0.0.1:8000', '[::1]', '[::1]:8000', 'sdcolorslab.test', 'backend.sdcolourslab.in']) || php_sapi_name() === 'cli';

    // 2. Use config.php settings if loaded
    if (!$dsn && $configLoaded && defined('DB_NAME')) {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            return $pdo;
        } catch (PDOException $e) {
            // In production, connection failure is fatal
            if (!$isLocal) {
                if (defined('DISPLAY_ERRORS') && DISPLAY_ERRORS) {
                    die("Database Connection Error: " . $e->getMessage());
                } else {
                    die("Database Connection Error. Please contact administrator.");
                }
            }
            // For local dev, fall through to SQLite fallback below
        }
    }

    // 3. Fallback: Default MySQL connection attempt (without config)
    if (!$dsn && !$pdo) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=sdcolourslab", "root", "", [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            return $pdo;
        } catch (PDOException $e) {
            // Fall through to SQLite fallback below
        }
    }

    // 4. SQLite Fallback for Local Dev (or if no MySQL connects locally)
    if (!$dsn && !$pdo && $isLocal) {
        $sqlitePath = __DIR__ . '/../dev.sqlite';
        try {
            $pdo = new PDO("sqlite:$sqlitePath");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Auto-create full schema matching production MySQL
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                role TEXT CHECK(role IN ('admin', 'photographer')) NOT NULL DEFAULT 'photographer',
                phone TEXT,
                studio_name TEXT,
                city TEXT,
                status TEXT CHECK(status IN ('pending', 'approved', 'rejected')) NOT NULL DEFAULT 'pending',
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );");
            
            $pdo->exec("CREATE TABLE IF NOT EXISTS api_tokens (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                token TEXT UNIQUE NOT NULL,
                expires_at TEXT NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            );");

            $pdo->exec("CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                category TEXT,
                description TEXT,
                price REAL NOT NULL DEFAULT 0.00,
                price_alt REAL,
                sizes TEXT,
                features TEXT,
                tag TEXT,
                image TEXT,
                active INTEGER NOT NULL DEFAULT 1,
                sort_order INTEGER NOT NULL DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );");

            $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                photographer_id INTEGER NOT NULL,
                status TEXT CHECK(status IN ('pending', 'processing', 'shipped', 'delivered', 'cancelled')) NOT NULL DEFAULT 'pending',
                total REAL NOT NULL DEFAULT 0.00,
                notes TEXT,
                admin_notes TEXT,
                shipping_address TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            );");

            $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                product_id INTEGER,
                product_name TEXT NOT NULL,
                size TEXT,
                quantity INTEGER NOT NULL DEFAULT 1,
                unit_price REAL NOT NULL,
                notes TEXT,
                FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
            );");

            $pdo->exec("CREATE TABLE IF NOT EXISTS user_addresses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                label TEXT NOT NULL,
                address_line TEXT NOT NULL,
                city TEXT NOT NULL,
                state TEXT NOT NULL,
                pincode TEXT NOT NULL,
                phone TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            );");
            
            // Insert mock admin if users is empty
            if ($pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() == 0) {
                $hash = password_hash('admin123', PASSWORD_DEFAULT);
                $pdo->exec("INSERT INTO users (id, name, email, password_hash, role, status) VALUES (1, 'Admin', 'admin@sdcolours.com', '$hash', 'admin', 'approved')");
            }

            // Seed mock products if empty
            if ($pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() == 0) {
                $pdo->exec("INSERT INTO products (id, name, category, description, price, price_alt, sizes, features, tag, image, active, sort_order) VALUES
                (10, 'Regular Album', 'album', 'Per page printing. Sizes: 12x15, 12x18, 18x24.', 38.00, 61.00, '[\"12x15\",\"12x18\",\"18x24\"]', '[\"Regular Glossy – ₹38/page\",\"Regular Heavy Glossy – ₹46/page\",\"Regular Matt – ₹51/page\",\"Regular Heavy Matt – ₹61/page\"]', NULL, NULL, 1, 10),
                (11, 'Special Album', 'album', 'Per page printing. Sizes: 12x15, 12x18, 18x24.', 52.00, 70.00, '[\"12x15\",\"12x18\",\"18x24\"]', '[\"Ntr Glossy Slim – ₹52/page\",\"Ntr Heavy Glossy – ₹62/page\",\"Ntr Matt Slim – ₹62/page\",\"Ntr Heavy Matt – ₹66/page\",\"Luster – ₹70/page\"]', NULL, NULL, 1, 11),
                (12, 'Metallic Album', 'album', 'Per page printing with premium metallic finishes.', 60.00, 110.00, '[\"12x15\",\"12x18\",\"18x24\"]', '[\"Regular Velvet Sheet – ₹60/page\",\"Ntr Velvet Sheet – ₹72/page\",\"Silky Metallic – ₹90/page\",\"Pearl Metallic – ₹110/page\"]', NULL, NULL, 1, 12),
                (24, 'Leather Combo 2 IN 1 (with Bag)', 'combo', 'Cover Leather Pad, Leather Photo Bag, 8x12 LED Frame & Wall Calendar', 1550.00, NULL, '[\"12x24\",\"12x30\",\"12x36\"]', '[\"Cover Leather Pad\",\"Leather Photo Bag\",\"8x12 LED Frame\",\"Wall Calendar\"]', 'Best Seller', '/images/combos/leather-2in1-bag.jpg', 1, 1),
                (25, 'Acrylic Combo 2 IN 1', 'combo', 'Leather Cover Pad, Full Acrylic', 1250.00, NULL, '[\"12x24\",\"12x30\",\"12x36\"]', '[\"Leather Cover Pad\",\"Full Acrylic\"]', 'Best Seller', '/images/combos/acrylic-2in1.jpg', 1, 2)");
            }

            return $pdo;
        } catch (PDOException $e) {
            die("Fatal Local Database Error: " . $e->getMessage());
        }
    }

    // 5. Handle DATABASE_URL (for cloud hosting fallback)
    if ($dsn) {
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

