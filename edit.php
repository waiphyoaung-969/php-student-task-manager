<?php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    die('Invalid task ID.');
}

$stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = ?');
$stmt->execute([$id]);
$task = $stmt->fetch();

if (!$task) {
    http_response_code(404);
    die('Task not found.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('Your session expired. Please try again.', 'error');
        header('Location: index.php');
        exit;
    }

    $updatedTask = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'category' => trim($_POST['category'] ?? ''),
        'priority' => $_POST['priority'] ?? '',
        'due_date' => $_POST['due_date'] ?? '',
    ];
    $completed = isset($_POST['completed']) ? 1 : 0;

    $errors = validate_task($updatedTask);

    if (!$errors) {
        $sql = 'UPDATE tasks
                SET title = ?, description = ?, category = ?, priority = ?, due_date = ?, completed = ?
                WHERE id = ?';
        $update = $pdo->prepare($sql);
        $update->execute([
            $updatedTask['title'],
            $updatedTask['description'],
            $updatedTask['category'],
            $updatedTask['priority'],
            $updatedTask['due_date'],
            $completed,
            $id,
        ]);

        set_flash('Task updated successfully.');
        header('Location: index.php');
        exit;
    }

    $task = array_merge($task, $updatedTask, ['completed' => $completed]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-shell">
    <a class="back-link" href="index.php">&larr; Back to tasks</a>
    <div class="panel form-panel">
        <h1>Edit Task</h1>
        <p class="subtitle">Update task #<?= (int) $task['id'] ?>.</p>

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

        <form method="POST" action="edit.php?id=<?= (int) $task['id'] ?>" novalidate>
            <?= csrf_field() ?>
            <label for="title">Title *</label>
            <input id="title" type="text" name="title" maxlength="150" value="<?= e($task['title']) ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?= e($task['description']) ?></textarea>

            <label for="category">Category *</label>
            <input id="category" type="text" name="category" maxlength="50" value="<?= e($task['category']) ?>" required>

            <label for="priority">Priority *</label>
            <select id="priority" name="priority" required>
                <option value="High" <?= $task['priority'] === 'High' ? 'selected' : '' ?>>High</option>
                <option value="Medium" <?= $task['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="Low" <?= $task['priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
            </select>

            <label for="due_date">Due Date *</label>
            <input id="due_date" type="date" name="due_date" value="<?= e($task['due_date']) ?>" required>

            <label class="checkbox-row">
                <input type="checkbox" name="completed" value="1" <?= (int) $task['completed'] === 1 ? 'checked' : '' ?>>
                Mark as completed
            </label>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Update Task</button>
                <a class="btn btn-secondary" href="index.php">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
