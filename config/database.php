<?php
$hostName = explode(':', $_SERVER['HTTP_HOST'] ?? 'localhost')[0];
$isLocal = in_array($hostName, ['localhost', '127.0.0.1'], true);

$databaseConfig = $isLocal ? [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'name' => 'everythingb2c',
] : [
    'host' => 'localhost',
    'user' => 'u728317772_everythingb2c',
    'pass' => 'a^0y9oZ8',
    'name' => 'u728317772_everythingb2c',
];

define('DB_HOST', getenv('DB_HOST') ?: $databaseConfig['host']);
define('DB_USER', getenv('DB_USER') ?: $databaseConfig['user']);
define('DB_PASS', getenv('DB_PASS') ?: $databaseConfig['pass']);
define('DB_NAME', getenv('DB_NAME') ?: $databaseConfig['name']);

// Create connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
