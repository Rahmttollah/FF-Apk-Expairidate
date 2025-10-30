<?php
// Railway automatically provides database URL
$db_url = getenv('DATABASE_URL');

if ($db_url) {
    // Parse Railway database URL
    $dbparts = parse_url($db_url);
    $host = $dbparts['host'];
    $port = $dbparts['port'];
    $username = $dbparts['user'];
    $password = $dbparts['pass'];
    $database = ltrim($dbparts['path'], '/');
    
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
} else {
    // Local development
    $dsn = "mysql:host=localhost;dbname=expiry_system;charset=utf8mb4";
    $username = "root";
    $password = "";
}

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Default admin credentials (change after setup)
define('ADMIN_USERNAME', 'RNR');
define('ADMIN_PASSWORD', 'RNR6677');
?>