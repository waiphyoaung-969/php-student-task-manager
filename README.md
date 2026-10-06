# Student Task Manager - PHP CRUD Practical Exam

**Student Name:** Wai Phyo Aung  
**Student ID:** [Add your Student ID before submission]

## Project Overview

This is my Student Task Manager for the Web Programming practical exam. I built it with plain PHP, MySQL/MariaDB, HTML, and CSS. It supports all four CRUD operations: create, read, update, and delete.

## Technologies Used

- Plain PHP
- MySQL / MariaDB
- HTML
- CSS
- PDO for database access

No framework or external library is used.

## Assignment Requirements Implemented

- Create a new task using an HTML form and POST
- Read and display tasks from the database
- Update an existing task
- Delete an existing task
- Variables and PHP data types
- Arrays
- `if / else` conditions
- Loops with `foreach`
- Reusable functions in `functions.php`
- `require` for shared files
- GET and POST request handling
- HTML forms
- Server-side input validation
- PDO database connection
- SQL `SELECT`, `INSERT`, `UPDATE`, and `DELETE`
- Prepared statements for queries with user input
- PHP sessions for flash messages
- Safe output with `htmlspecialchars()` through the `e()` function

## Challenge Features Prepared

The current project includes several of the challenge examples from the exam sheet so I can use the one assigned by my instructor:

- Highlight overdue incomplete tasks
- Filter completed or incomplete tasks
- Sort by due date
- Count completed tasks
- Filter by priority
- Search by title
- Validate due dates
- Display category counts

The main prepared challenge in the interface is **highlighting overdue incomplete tasks**. I will use and explain the challenge that my instructor assigns during the exam.

## File Structure

```text
php-student-task-manager/
├── index.php
├── create.php
├── edit.php
├── delete.php
├── db.php
├── functions.php
├── style.css
├── database.sql
└── README.md
```

## Setup Instructions

1. Put the project folder inside the web server directory, for example `htdocs` in XAMPP.
2. Start Apache and MySQL/MariaDB.
3. Open phpMyAdmin.
4. Import `database.sql`.
5. Check the database settings in `db.php`.
6. Open the project in a browser, for example `http://localhost/php-student-task-manager/`.

Default database settings in `db.php`:

```text
host: localhost
database: student_task_manager
username: root
password: empty
```

If my MySQL username or password is different, I will change it in `db.php`.

## AI-Use Reflection

**AI tool(s) used:** ChatGPT

### Three examples of how AI helped me

1. I used ChatGPT to help me plan a clear CRUD file structure and PDO database connection.
2. I used ChatGPT to explain prepared statements, server-side validation, PHP sessions, and safe output so I could apply them in my project.
3. I used ChatGPT to review my project structure and suggest tests for creating, editing, deleting, searching, filtering, and checking overdue tasks.

### One AI-generated suggestion or piece of code that I changed or rejected

**What was it?**  
An early delete example used a normal GET link such as `delete.php?id=1`.

**Why did I change/reject it?**  
I changed deletion to use a POST form because deleting a task changes the database. I did not want a task to be deleted only because someone opened or refreshed a URL.

### The part of this application I understand least

The part I need to practice most is the filtering query in `index.php`. It builds the `WHERE` conditions depending on the selected filters and sends the matching values to a prepared statement. I understand the basic flow, but I want more practice changing it by myself before the code defense.

## Basic Test Checklist

- [ ] Import `database.sql` without errors.
- [ ] Open `index.php` and see the task list.
- [ ] Create a new task.
- [ ] Submit empty required fields and confirm server-side validation appears.
- [ ] Edit a task and mark it completed.
- [ ] Delete a task and confirm it disappears.
- [ ] Search by title.
- [ ] Filter by priority.
- [ ] Filter by completed/incomplete status.
- [ ] Sort by due date.
- [ ] Confirm overdue incomplete tasks are highlighted.
- [ ] Add my Student ID before final submission.
