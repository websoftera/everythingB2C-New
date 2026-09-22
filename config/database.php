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

// Reuse connections on hosting to avoid Hostinger's new-connection rate limit.
// Keep local development non-persistent; DB_PERSISTENT can override either default.
$persistentSetting = getenv('DB_PERSISTENT');
$usePersistentConnection = $persistentSetting === false
    ? !$isLocal
    : filter_var($persistentSetting, FILTER_VALIDATE_BOOLEAN);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => $usePersistentConnection,
        ]
    );
} catch (PDOException $e) {
    // Keep diagnostics in the server PHP error log, not the public response.
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(503);
    if (!headers_sent()) {
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: no-store');
        header('Retry-After: 30');
    }
    exit('The website is temporarily unable to connect to its database. Please try again shortly.');
}
?>