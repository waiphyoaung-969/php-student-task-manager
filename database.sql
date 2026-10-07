CREATE DATABASE IF NOT EXISTS student_task_manager
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE student_task_manager;

DROP TABLE IF EXISTS tasks;

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    category VARCHAR(50) NOT NULL,
    priority VARCHAR(20) NOT NULL,
    due_date DATE NOT NULL,
    completed TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tasks (title, description, category, priority, due_date, completed) VALUES
('PHP CRUD Midterm', 'Complete and test the Student Task Manager project.', 'Web Programming', 'High', '2026-10-01', 0),
('Database Review', 'Review SELECT, INSERT, UPDATE, DELETE and prepared statements.', 'Database', 'Medium', '2026-10-12', 0),
('Read Chapter Notes', 'Review course notes before the next class.', 'Study', 'Low', '2026-10-15', 1);