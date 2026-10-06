<?php
// Database connection settings.
// Local XAMPP defaults are used when Vercel environment variables are not set.
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'student_task_manager';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$port = getenv('DB_PORT') ?: '3306';

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
        . 'If this is the Vercel deployment, add DB_HOST, DB_PORT, DB_NAME, DB_USER, '
        . 'and DB_PASSWORD in Vercel Project Settings > Environment Variables.'
    );
}
