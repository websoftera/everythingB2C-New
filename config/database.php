<?php
// Local Use Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'everythingb2c');

// Production Use Database configuration
// define('DB_HOST', 'localhost');
// define('DB_USER', 'u141519101_everythingb2c1');
// define('DB_PASS', 'EveryThing@b2c#2025');
// define('DB_NAME', 'u141519101_everythingb2c1');
// Create connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 