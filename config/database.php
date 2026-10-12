<?php
$hostName = explode(':', $_SERVER['HTTP_HOST'] ?? 'localhost')[0];
$isLocal = in_array($hostName, ['localhost', '127.0.0.1'], true);

$databaseConfig = $isLocal ? [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    // Recovered from the September 23 local data copy; original database retained.
    'name' => 'everythingb2c_recovered_20260923',
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

$pdo = null;
$connectionError = null;

// Hostinger can occasionally refuse a newly opened MySQL connection. Retry a
// couple of times before showing a 503 so a brief database hiccup does not
// make a category link appear broken on mobile or desktop.
for ($attempt = 1; $attempt <= 3; $attempt++) {
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
        break;
    } catch (PDOException $e) {
        $connectionError = $e;
        if ($attempt < 3) {
            usleep(200000);
        }
    }
}

if ($pdo === null) {
    // Keep diagnostics in the server PHP error log, not the public response.
    error_log('Database connection failed after 3 attempts: ' . $connectionError->getMessage());
    http_response_code(503);
    if (!headers_sent()) {
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: no-store');
        header('Retry-After: 30');
    }
    exit('The website is temporarily unable to connect to its database. Please try again shortly.');
}
?>
