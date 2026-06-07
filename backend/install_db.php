<?php
/**
 * SD Colours Lab - Database Auto-Installer
 * Run this once to set up the database on the live server.
 * DELETE THIS FILE after successful installation!
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host   = 'localhost';
$user   = 'u953522373_root';
$pass   = 'sd@SS@132B';
$dbname = 'u953522373_sdcolourslab';

echo "<pre>\n=== SD Colours Lab - DB Installer ===\n\n";

try {
    // Connect without selecting DB first
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    echo "✓ Connected to MySQL server\n\n";

    // Create DB if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '$dbname' ready\n\n";

    // Switch to our DB
    $pdo->exec("USE `$dbname`");

    // Create tables
    echo "Creating tables...\n";

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` varchar(150) NOT NULL,
        `email` varchar(200) NOT NULL,
        `password_hash` varchar(255) NOT NULL,
        `role` enum('admin','photographer') NOT NULL DEFAULT 'photographer',
        `phone` varchar(20) DEFAULT NULL,
        `studio_name` varchar(200) DEFAULT NULL,
        `city` varchar(100) DEFAULT NULL,
        `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "  ✓ users table\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS `api_tokens` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` int(10) UNSIGNED NOT NULL,
        `token` varchar(100) NOT NULL,
        `expires_at` datetime NOT NULL,
        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `token` (`token`),
        KEY `fk_tokens_user` (`user_id`),
        CONSTRAINT `fk_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "  ✓ api_tokens table\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS `products` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` varchar(200) NOT NULL,
        `category` varchar(100) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `price` decimal(10,2) NOT NULL DEFAULT 0.00,
        `price_alt` decimal(10,2) DEFAULT NULL,
        `sizes` longtext DEFAULT NULL,
        `features` longtext DEFAULT NULL,
        `tag` varchar(100) DEFAULT NULL,
        `image` varchar(300) DEFAULT NULL,
        `active` tinyint(1) NOT NULL DEFAULT 1,
        `sort_order` int(11) NOT NULL DEFAULT 0,
        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "  ✓ products table\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS `orders` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `photographer_id` int(10) UNSIGNED DEFAULT NULL,
        `status` enum('pending','paid','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
        `total` decimal(10,2) NOT NULL DEFAULT 0.00,
        `notes` text DEFAULT NULL,
        `admin_notes` text DEFAULT NULL,
        `shipping_address` text DEFAULT NULL,
        `manual_studio_name` varchar(200) DEFAULT NULL,
        `manual_phone` varchar(20) DEFAULT NULL,
        `manual_size` varchar(50) DEFAULT NULL,
        `discount_percent` decimal(5,2) DEFAULT 0.00,
        `discount_amount` decimal(10,2) DEFAULT 0.00,
        `secure_key` varchar(64) DEFAULT NULL,
        `net_pay` decimal(10,2) DEFAULT 0.00,
        `drive_link` varchar(500) DEFAULT NULL,
        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
        `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `fk_orders_user` (`photographer_id`),
        CONSTRAINT `fk_orders_user` FOREIGN KEY (`photographer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "  ✓ orders table\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
        `key` varchar(100) NOT NULL,
        `value` text DEFAULT NULL,
        PRIMARY KEY (`key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "  ✓ settings table\n";
    
    // Seed default settings if empty
    $checkSettings = $pdo->query("SELECT COUNT(*) FROM `settings`")->fetchColumn();
    if ($checkSettings == 0) {
        $stmtS = $pdo->prepare("INSERT INTO `settings` (`key`, `value`) VALUES (?, ?)");
        $stmtS->execute(['phone_number', '8895838987, 8260754410']);
        $stmtS->execute(['whatsapp_number', '8895838987']);
        $stmtS->execute(['lab_address', 'Madhusudan marg, Naredi Tower Complex (In front of Raymond showroom) RKL- 769001 (ODISHA)']);
        $branches = [
            ['name' => 'Corporate Office', 'address' => 'Madhusudan marg, Naredi Tower Complex, RKL- 769001 (ODISHA)'],
            ['name' => 'Sambalpur Branch', 'address' => 'Budharaja, Sambalpur - 768004']
        ];
        $stmtS->execute(['branches', json_encode($branches)]);
        echo "    ✓ settings table defaults seeded\n";
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS `order_items` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `order_id` int(10) UNSIGNED NOT NULL,
        `product_id` int(10) UNSIGNED DEFAULT NULL,
        `product_name` varchar(200) NOT NULL,
        `size` varchar(100) DEFAULT NULL,
        `quantity` int(11) NOT NULL DEFAULT 1,
        `unit_price` decimal(10,2) NOT NULL,
        `notes` text DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `fk_items_order` (`order_id`),
        CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "  ✓ order_items table\n\n";

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Seed admin user if not exists
    $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $chk->execute(['admin@sdcolours.com']);
    if (!$chk->fetch()) {
        // admin123 hashed
        $pdo->exec("INSERT INTO users (id,name,email,password_hash,role,status) VALUES (1,'Admin','admin@sdcolours.com','\$2y\$10\$r1fPpbPkZsP6kPfuuTEuzeO4prNF2Yl8YvTzFAgwOibvOQUysCZje','admin','approved')");
        echo "✓ Admin user seeded (admin@sdcolours.com / admin123)\n";
    } else {
        echo "✓ Admin user already exists\n";
    }

    // Check product count
    $count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($count == 0) {
        // Import products from the SQL data
        $pdo->exec("INSERT INTO products (id,name,category,description,price,price_alt,sizes,features,tag,image,active,sort_order) VALUES
        (10,'Regular Album','album','Per page printing. Sizes: 12x15, 12x18, 18x24.',38.00,61.00,'[\"12x15\",\"12x18\",\"18x24\"]','[\"Regular Glossy – ₹38/page\",\"Regular Heavy Glossy – ₹46/page\",\"Regular Matt – ₹51/page\",\"Regular Heavy Matt – ₹61/page\"]',NULL,NULL,1,10),
        (11,'Special Album','album','Per page printing. Sizes: 12x15, 12x18, 18x24.',52.00,70.00,'[\"12x15\",\"12x18\",\"18x24\"]','[\"Ntr Glossy Slim – ₹52/page\",\"Ntr Heavy Glossy – ₹62/page\",\"Ntr Matt Slim – ₹62/page\",\"Ntr Heavy Matt – ₹66/page\",\"Luster – ₹70/page\"]',NULL,NULL,1,11),
        (12,'Metallic Album','album','Per page printing with premium metallic finishes.',60.00,110.00,'[\"12x15\",\"12x18\",\"18x24\"]','[\"Regular Velvet Sheet – ₹60/page\",\"Ntr Velvet Sheet – ₹72/page\",\"Silky Metallic – ₹90/page\",\"Pearl Metallic – ₹110/page\"]',NULL,NULL,1,12),
        (24,'Leather Combo 2 IN 1 (with Bag)','combo','Cover Leather Pad, Leather Photo Bag, 8x12 LED Frame & Wall Calendar',1550.00,NULL,'[\"12x24\",\"12x30\",\"12x36\"]','[\"Cover Leather Pad\",\"Leather Photo Bag\",\"8x12 LED Frame\",\"Wall Calendar\"]','Best Seller','/images/combos/leather-2in1-bag.jpg',1,1),
        (25,'Acrylic Combo 2 IN 1','combo','Leather Cover Pad, Full Acrylic',1250.00,NULL,'[\"12x24\",\"12x30\",\"12x36\"]','[\"Leather Cover Pad\",\"Full Acrylic\"]','Best Seller','/images/combos/acrylic-2in1.jpg',1,2)");
        echo "✓ Sample products seeded\n";
    } else {
        echo "✓ Products already exist ($count records)\n";
    }

    // Reset auto increment
    $pdo->exec("ALTER TABLE users AUTO_INCREMENT = 100");
    $pdo->exec("ALTER TABLE products AUTO_INCREMENT = 100");
    $pdo->exec("ALTER TABLE orders AUTO_INCREMENT = 100");
    $pdo->exec("ALTER TABLE order_items AUTO_INCREMENT = 100");

    echo "\n=== INSTALLATION COMPLETE ===\n";
    echo "Admin: admin@sdcolours.com / admin123\n";
    echo "\n⚠ DELETE this file (install_db.php) for security!\n";

} catch (PDOException $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nPlease create the database manually in Hostinger cPanel > MySQL Databases\n";
}

echo "</pre>";
