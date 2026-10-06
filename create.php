<?php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/functions.php';

$task = [
    'title' => '',
    'description' => '',
    'category' => '',
    'priority' => '',
    'due_date' => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'category' => trim($_POST['category'] ?? ''),
        'priority' => $_POST['priority'] ?? '',
        'due_date' => $_POST['due_date'] ?? '',
    ];

    $errors = validate_task($task);

    if (!$errors) {
        $sql = 'INSERT INTO tasks (title, description, category, priority, due_date, completed)
                VALUES (?, ?, ?, ?, ?, 0)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $task['title'],
            $task['description'],
            $task['category'],
            $task['priority'],
            $task['due_date'],
        ]);

        set_flash('Task added successfully.');
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-shell">
    <a class="back-link" href="index.php">&larr; Back to tasks</a>
    <div class="panel form-panel">
        <h1>Add New Task</h1>
        <p class="subtitle">Create a new academic task.</p>

        <?php if ($errors): ?>
            <div class="flash error">
                <strong>Please fix the following:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="create.php" novalidate>
            <label for="title">Title *</label>
            <input id="title" type="text" name="title" maxlength="150" value="<?= e($task['title']) ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?= e($task['description']) ?></textarea>

            <label for="category">Category *</label>
            <input id="category" type="text" name="category" maxlength="50" value="<?= e($task['category']) ?>" placeholder="e.g. Web Programming" required>

            <label for="priority">Priority *</label>
            <select id="priority" name="priority" required>
                <option value="">Select priority</option>
                <option value="High" <?= $task['priority'] === 'High' ? 'selected' : '' ?>>High</option>
                <option value="Medium" <?= $task['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="Low" <?= $task['priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
            </select>

            <label for="due_date">Due Date *</label>
            <input id="due_date" type="date" name="due_date" value="<?= e($task['due_date']) ?>" required>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Save Task</button>
                <a class="btn btn-secondary" href="index.php">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
