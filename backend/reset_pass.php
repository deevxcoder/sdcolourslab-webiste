<?php
$hash = password_hash('photo123', PASSWORD_DEFAULT);
$pdo = new PDO('mysql:host=localhost;dbname=sdcolourslab', 'root', '');
$stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
$stmt->execute([$hash, 'babygamersofficial@gmail.com']);
echo 'Rows updated: ' . $stmt->rowCount() . PHP_EOL;
echo 'New hash: ' . $hash . PHP_EOL;
