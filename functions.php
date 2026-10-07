<?php
/** Escape text before showing user-entered content in HTML. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Generate or retrieve the current CSRF token for the session. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/** Render a hidden CSRF field. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Verify a submitted CSRF token. */
function verify_csrf_token(): bool
{
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !isset($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
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

    $title = trim((string) ($data['title'] ?? ''));
    $category = trim((string) ($data['category'] ?? ''));
    $priority = $data['priority'] ?? '';
    $dueDate = trim((string) ($data['due_date'] ?? ''));

    if ($title === '') {
        $errors[] = 'Title is required.';
    } elseif (strlen($title) > 150) {
        $errors[] = 'Title must be 150 characters or fewer.';
    }

    if ($category === '') {
        $errors[] = 'Category is required.';
    } elseif (strlen($category) > 50) {
        $errors[] = 'Category must be 50 characters or fewer.';
    }

    $allowedPriorities = ['Low', 'Medium', 'High'];
    if (!in_array($priority, $allowedPriorities, true)) {
        $errors[] = 'Please choose a valid priority.';
    }

    if ($dueDate === '') {
        $errors[] = 'Please enter a valid due date.';
    } else {
        $dateObject = DateTime::createFromFormat('Y-m-d', $dueDate);
        $isValidDate = $dateObject && $dateObject->format('Y-m-d') === $dueDate;

        if (!$isValidDate) {
            $errors[] = 'Please enter a valid due date.';
        }
    }

    return $errors;
}

/** Check whether a task is overdue and still incomplete. */
function is_overdue(array $task): bool
{
    if ((int) ($task['completed'] ?? 0) === 1 || empty($task['due_date'])) {
        return false;
    }

    $dueDate = DateTimeImmutable::createFromFormat('Y-m-d', (string) $task['due_date']);
    if ($dueDate === false) {
        return false;
    }

    return $dueDate < new DateTimeImmutable('today');
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