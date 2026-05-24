<?php
$local = __DIR__ . '/includes/config.local.php';
$prod  = __DIR__ . '/includes/config.php';

echo "Local config path: $local\n";
echo "Exists: " . (file_exists($local) ? "YES" : "NO") . "\n";
echo "Readable: " . (is_readable($local) ? "YES" : "NO") . "\n";

echo "Prod config path: $prod\n";
echo "Exists: " . (file_exists($prod) ? "YES" : "NO") . "\n";
echo "Readable: " . (is_readable($prod) ? "YES" : "NO") . "\n";
