<?php
/** Escape text before showing user-entered content in HTML. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Save a one-time message in the session. */
function set_flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type,
    ];
}

/** Read and remove the one-time message. */
function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/** Return validation errors for a task form. */
function validate_task(array $data): array
{
    $errors = [];

    if (trim($data['title'] ?? '') === '') {
        $errors[] = 'Title is required.';
    } elseif (strlen(trim($data['title'])) > 150) {
        $errors[] = 'Title must be 150 characters or fewer.';
    }

    if (trim($data['category'] ?? '') === '') {
        $errors[] = 'Category is required.';
    } elseif (strlen(trim($data['category'])) > 50) {
        $errors[] = 'Category must be 50 characters or fewer.';
    }

    $allowedPriorities = ['Low', 'Medium', 'High'];
    if (!in_array($data['priority'] ?? '', $allowedPriorities, true)) {
        $errors[] = 'Please choose a valid priority.';
    }

    $dueDate = $data['due_date'] ?? '';
    $dateObject = DateTime::createFromFormat('Y-m-d', $dueDate);
    $isValidDate = $dateObject && $dateObject->format('Y-m-d') === $dueDate;

    if (!$isValidDate) {
        $errors[] = 'Please enter a valid due date.';
    }

    return $errors;
}

/** Check whether a task is overdue and still incomplete. */
function is_overdue(array $task): bool
{
    if ((int) $task['completed'] === 1 || empty($task['due_date'])) {
        return false;
    }

    return $task['due_date'] < date('Y-m-d');
}

/** Return a CSS class for a priority value. */
function priority_class(string $priority): string
{
    if ($priority === 'High') {
        return 'priority-high';
    }

    if ($priority === 'Medium') {
        return 'priority-medium';
    }

    return 'priority-low';
}
