<?php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/functions.php';

// Challenge features: search, filters, sorting, counters, category count, overdue highlighting.
$search = trim($_GET['search'] ?? '');
$priority = $_GET['priority'] ?? '';
$status = $_GET['status'] ?? '';
$sort = $_GET['sort'] ?? 'created_desc';

$where = [];
$params = [];

if ($search !== '') {
    $where[] = 'title LIKE ?';
    $params[] = '%' . $search . '%';
}

if (in_array($priority, ['Low', 'Medium', 'High'], true)) {
    $where[] = 'priority = ?';
    $params[] = $priority;
}

if ($status === 'completed') {
    $where[] = 'completed = 1';
} elseif ($status === 'incomplete') {
    $where[] = 'completed = 0';
}

$orderBy = 'created_at DESC';
if ($sort === 'due_asc') {
    $orderBy = 'due_date ASC, created_at DESC';
} elseif ($sort === 'due_desc') {
    $orderBy = 'due_date DESC, created_at DESC';
} elseif ($sort === 'title_asc') {
    $orderBy = 'title ASC';
}

$sql = 'SELECT * FROM tasks';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY ' . $orderBy;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll();

// Counts shown on the dashboard.
$totalTasks = (int) $pdo->query('SELECT COUNT(*) FROM tasks')->fetchColumn();
$completedTasks = (int) $pdo->query('SELECT COUNT(*) FROM tasks WHERE completed = 1')->fetchColumn();
$incompleteTasks = $totalTasks - $completedTasks;

$categoryStmt = $pdo->query('SELECT category, COUNT(*) AS total FROM tasks GROUP BY category ORDER BY category');
$categoryCounts = $categoryStmt->fetchAll();

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Task Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <header class="topbar">
        <div>
            <p class="eyebrow">Web Programming Midterm Project</p>
            <h1>Student Task Manager</h1>
            <p class="subtitle">Plain PHP + MySQL CRUD application</p>
        </div>
        <a class="btn btn-primary" href="create.php">+ Add Task</a>
    </header>

    <?php if ($flash): ?>
        <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <section class="stats-grid">
        <div class="stat-card"><span>Total Tasks</span><strong><?= $totalTasks ?></strong></div>
        <div class="stat-card"><span>Completed</span><strong><?= $completedTasks ?></strong></div>
        <div class="stat-card"><span>Incomplete</span><strong><?= $incompleteTasks ?></strong></div>
    </section>

    <section class="panel">
        <h2>Find Tasks</h2>
        <form class="filters" method="GET" action="index.php">
            <div>
                <label for="search">Search by title</label>
                <input id="search" type="text" name="search" value="<?= e($search) ?>" placeholder="e.g. PHP Assignment">
            </div>
            <div>
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="">All priorities</option>
                    <option value="High" <?= $priority === 'High' ? 'selected' : '' ?>>High</option>
                    <option value="Medium" <?= $priority === 'Medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="Low" <?= $priority === 'Low' ? 'selected' : '' ?>>Low</option>
                </select>
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All statuses</option>
                    <option value="incomplete" <?= $status === 'incomplete' ? 'selected' : '' ?>>Incomplete</option>
                    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <div>
                <label for="sort">Sort</label>
                <select id="sort" name="sort">
                    <option value="created_desc" <?= $sort === 'created_desc' ? 'selected' : '' ?>>Newest first</option>
                    <option value="due_asc" <?= $sort === 'due_asc' ? 'selected' : '' ?>>Due date: earliest</option>
                    <option value="due_desc" <?= $sort === 'due_desc' ? 'selected' : '' ?>>Due date: latest</option>
                    <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Title A-Z</option>
                </select>
            </div>
            <div class="filter-actions">
                <button class="btn btn-primary" type="submit">Apply</button>
                <a class="btn btn-secondary" href="index.php">Reset</a>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="section-heading">
            <div>
                <h2>Task List</h2>
                <p><?= count($tasks) ?> task(s) shown</p>
            </div>
        </div>

        <?php if (!$tasks): ?>
            <div class="empty-state">No tasks matched your current filters.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Task</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr class="<?= is_overdue($task) ? 'overdue-row' : '' ?>">
                            <td>
                                <strong><?= e($task['title']) ?></strong>
                                <?php if ($task['description']): ?>
                                    <div class="description"><?= nl2br(e($task['description'])) ?></div>
                                <?php endif; ?>
                                <?php if (is_overdue($task)): ?>
                                    <span class="overdue-label">Overdue</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($task['category']) ?></td>
                            <td><span class="priority <?= priority_class($task['priority']) ?>"><?= e($task['priority']) ?></span></td>
                            <td><?= e($task['due_date']) ?></td>
                            <td>
                                <?php if ((int) $task['completed'] === 1): ?>
                                    <span class="status status-done">Completed</span>
                                <?php else: ?>
                                    <span class="status status-open">Incomplete</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a class="btn btn-small btn-secondary" href="edit.php?id=<?= (int) $task['id'] ?>">Edit</a>
                                <form method="POST" action="delete.php" onsubmit="return confirm('Delete this task?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                                    <button class="btn btn-small btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel">
        <h2>Category Count</h2>
        <?php if (!$categoryCounts): ?>
            <p>No categories yet.</p>
        <?php else: ?>
            <div class="category-list">
                <?php foreach ($categoryCounts as $row): ?>
                    <span><?= e($row['category']) ?>: <strong><?= (int) $row['total'] ?></strong></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
</body>
</html>
