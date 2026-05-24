<?php
/**
 * AUTO-SWITCH CONFIGURATION
 * Detects if running on localhost or a live server.
 */

$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', 'localhost:8000', 'localhost:8001', '127.0.0.1', '127.0.0.1:8000', '[::1]', '[::1]:8000', 'sdcolorslab.test']) || php_sapi_name() === 'cli';

if ($isLocal) {
    // LOCAL SETTINGS (Laragon / XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'sdcolourslab');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DISPLAY_ERRORS', true);
} else {
    // PRODUCTION SETTINGS (cPanel)
    define('DB_HOST', 'localhost'); // Usually 'localhost' on cPanel
    define('DB_NAME', 'u953522373_sdcolourslab'); // Your cPanel database name
    define('DB_USER', 'u953522373_root');      // Your cPanel database user
    define('DB_PASS', 'sd@SS@132B');     // Your cPanel database password
    define('DISPLAY_ERRORS', false); // Hide errors in production
}
