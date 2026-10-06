<?php
// Database connection settings.
//
// Local development (Laragon/XAMPP):
//   http://localhost/... or http://*.test
//   loads config.local.php if it exists.
//   This keeps your local password out of GitHub.
//
// Hosted deployment (Vercel):
//   uses MYSQL_PUBLIC_URL or DATABASE_URL when available.
//   You can also use DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD.

$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = (
    $httpHost === 'localhost'
    || $httpHost === '127.0.0.1'
    || str_starts_with($httpHost, 'localhost:')
    || str_starts_with($httpHost, '127.0.0.1:')
    || str_ends_with($httpHost, '.test')
);

if ($isLocal) {
    // Default Laragon / XAMPP values
    $host = '127.0.0.1';
    $port = '3306';
    $dbname = 'student_task_manager';
    $username = 'root';
    $password = '';

    // Optional local-only overrides.
    // Create config.local.php from config.local.example.php.
    $localConfigFile = __DIR__ . '/config.local.php';

    if (file_exists($localConfigFile)) {
        $localConfig = require $localConfigFile;

        if (is_array($localConfig)) {
            $host = $localConfig['host'] ?? $host;
            $port = $localConfig['port'] ?? $port;
            $dbname = $localConfig['dbname'] ?? $dbname;
            $username = $localConfig['username'] ?? $username;
            $password = $localConfig['password'] ?? $password;
        }
    }
} else {
    $databaseUrl = getenv('MYSQL_PUBLIC_URL') ?: getenv('DATABASE_URL');

    if ($databaseUrl) {
        $parts = parse_url($databaseUrl);

        if ($parts === false || empty($parts['host']) || empty($parts['path'])) {
            die('Database connection failed: invalid MYSQL_PUBLIC_URL / DATABASE_URL.');
        }

        $host = $parts['host'];
        $port = $parts['port'] ?? 3306;
        $dbname = ltrim($parts['path'], '/');
        $username = $parts['user'] ?? '';
        $password = $parts['pass'] ?? '';
    } else {
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '3306';
        $dbname = getenv('DB_NAME') ?: 'student_task_manager';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';
    }
}

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    if ($isLocal) {
        die(
            'Local database connection failed. '
            . 'Check config.local.php, start MySQL in Laragon, and make sure the '
            . 'student_task_manager database exists.'
        );
    }

    die(
        'Database connection failed. '
        . 'On Vercel, add MYSQL_PUBLIC_URL using the Railway MySQL public connection URL, '
        . 'or add DB_HOST, DB_PORT, DB_NAME, DB_USER, and DB_PASSWORD.'
    );
}
