<?php
// Database connection settings.
// For hosting, prefer a single Railway public MySQL URL if provided.
// Supported env vars: MYSQL_PUBLIC_URL or DATABASE_URL.
// Fallback: separate DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD.
// Local XAMPP defaults are used when none are set.

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
    die(
        'Database connection failed. '
        . 'On Vercel, add MYSQL_PUBLIC_URL using the Railway MySQL public connection URL, '
        . 'or add DB_HOST, DB_PORT, DB_NAME, DB_USER, and DB_PASSWORD.'
    );
}
