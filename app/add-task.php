<?php
session_start();
if (!isset($_SESSION['role']) || !isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=Unauthorized"); exit();
}

if (isset($_POST['title'], $_POST['description'], $_POST['assigned_to'], $_POST['due_date'], $_POST['priority'])) {
    include "../DB_connection.php";
    include "Model/Task.php";
    include "Model/Notification.php";

    function validate($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $title       = validate($_POST['title']);
    $description = validate($_POST['description']);
    $assigned_to = (int) $_POST['assigned_to'];
    $due_date    = validate($_POST['due_date']);
    $priority    = in_array($_POST['priority'], ['low','medium','high']) ? $_POST['priority'] : 'medium';

    if (empty($title)) {
        header("Location: ../create_task.php?error=Title is required"); exit();
    }
    if ($assigned_to === 0) {
        header("Location: ../create_task.php?error=Please select an employee"); exit();
    }

    insert_task($conn, [$title, $description, $assigned_to, $due_date ?: null, $priority]);

    $notif_data = ["'$title' has been assigned to you. Please review and start working on it.", $assigned_to, 'New Task Assigned'];
    insert_notification($conn, $notif_data);

    header("Location: ../create_task.php?success=Task created successfully"); exit();
} else {
    header("Location: ../create_task.php?error=Missing required fields"); exit();
}
