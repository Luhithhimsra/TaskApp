<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=Unauthorized"); exit();
}

if (isset($_POST['title'], $_POST['description'], $_POST['assigned_to'], $_POST['due_date'], $_POST['id'], $_POST['priority'])) {
    include "../DB_connection.php";
    include "Model/Task.php";

    function validate($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $id          = (int) $_POST['id'];
    $title       = validate($_POST['title']);
    $description = validate($_POST['description']);
    $assigned_to = (int) $_POST['assigned_to'];
    $due_date    = validate($_POST['due_date']);
    $priority    = in_array($_POST['priority'], ['low','medium','high']) ? $_POST['priority'] : 'medium';

    if (empty($title)) {
        header("Location: ../edit-task.php?id=$id&error=Title is required"); exit();
    }

    update_task($conn, [$title, $description, $assigned_to, $due_date ?: null, $priority, $id]);

    header("Location: ../tasks.php?success=Task updated successfully"); exit();
} else {
    header("Location: ../tasks.php?error=Missing required fields"); exit();
}
