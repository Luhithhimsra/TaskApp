<!-- TaskFlow Pro — Task Management System built with PHP, PDO and MySQL -->

# TaskFlow Pro

A task management web app built with PHP, PDO, and MySQL. It supports two roles — admin and employee — and handles the full task lifecycle from creation through to completion, including a trash and restore system.

---

## Features

- Secure login with session-based authentication using password hashing
- Admin and Employee role separation
- Full CRUD on tasks — create, read, update, and delete
- Soft deletes — tasks move to a trash bin instead of being permanently removed
- Restore trashed tasks back to active
- Priority levels — Low, Medium, High
- Filter tasks by status and priority, with keyword search
- Pagination on all task listing pages
- Notifications when a task is assigned to an employee
- Employee dashboard showing personal task statistics

---

## Tech Stack

| Layer    | Technology        |
|----------|-------------------|
| Backend  | PHP 8.0+ with PDO |
| Database | MySQL / MariaDB   |
| Frontend | HTML, CSS, JS     |
| Auth     | PHP Sessions      |

---

## Setup (Laragon)

### 1. Place project files

Copy the project folder into your Laragon web root:

```
D:/laragon/www/taskflow/
```

### 2. Import the database

Open HeidiSQL or phpMyAdmin, then import the file:

```
task_management_db.sql
```

This will create the database and load the sample data automatically.

### 3. Configure database connection

Open `DB_connection.php` and update the credentials if needed:

```php
$sName   = "localhost";
$uName   = "root";
$pass    = "";
$db_name = "task_management_db";
```

### 4. Open in browser

```
http://localhost/taskflow/
```

---

## Environment Variables

This project does not use a .env file. All database configuration lives in `DB_connection.php`. Refer to `.env.example` for the list of values you need before running the project. Do not commit real credentials to version control.

---

## Demo Accounts

| Role     | Username | Password |
|----------|----------|----------|
| Admin    | admin    | password |
| Employee | john     | password |
| Employee | trump    | password |

---

## Project Structure

```
taskflow/
├── app/
│   ├── Model/
│   │   ├── Task.php                  # Task queries — CRUD, filters, pagination, soft delete
│   │   ├── User.php                  # User queries
│   │   └── Notification.php         # Notification queries
│   ├── add-task.php                  # Handles create task form submission
│   ├── update-task.php               # Handles edit task form submission
│   ├── update-task-employee.php      # Employee status update handler
│   └── login.php                     # Handles login form submission
├── inc/
│   ├── header.php                    # Top navigation bar
│   └── nav.php                       # Sidebar navigation
├── css/
│   └── style.css                     # All styles
├── img/
│   └── user.png
├── index.php                         # Dashboard
├── login.php                         # Login page
├── tasks.php                         # Admin: all tasks with filters and pagination
├── create_task.php                   # Admin: create task form
├── edit-task.php                     # Admin: edit task form
├── delete-task.php                   # Admin: soft delete handler
├── restore-task.php                  # Admin: restore from trash
├── trash.php                         # Admin: trash view
├── my_task.php                       # Employee: personal task list
├── edit-task-employee.php            # Employee: status update form
├── user.php                          # Admin: manage users
├── DB_connection.php                 # PDO database connection
├── task_management_db.sql            # Database schema and sample data
└── README.md
```

---

## Assumptions

Soft delete is used for all task removals. Nothing is permanently deleted through the UI. Only admins can create, edit, and delete tasks. Employees can only update the status of tasks assigned to them. Pagination defaults to 10 items per page and priority defaults to medium if not specified.
