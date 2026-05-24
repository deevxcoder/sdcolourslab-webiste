<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If the request starts with /api and is not a physical file/directory, route to api/index.php
if (strpos($uri, '/api') === 0) {
    $filePath = __DIR__ . $uri;
    if (!file_exists($filePath) || is_dir($filePath)) {
        $_SERVER['SCRIPT_NAME'] = '/api/index.php';
        require_once __DIR__ . '/api/index.php';
        exit;
    }
}

// Fallback to normal behavior for static files and standard PHP scripts
return false;
