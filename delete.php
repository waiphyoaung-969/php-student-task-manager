<?php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed.');
}

if (!verify_csrf_token()) {
    set_flash('Your session expired. Please try again.', 'error');
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('Invalid task ID.', 'error');
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM tasks WHERE id = ?');
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    set_flash('Task not found.', 'error');
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ?');
$stmt->execute([$id]);

set_flash('Task deleted successfully.');
header('Location: index.php');
exit;
