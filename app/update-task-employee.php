<?php
session_start();
if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    header("Location: ../login.php?error=Unauthorized"); exit();
}

if (isset($_POST['status'], $_POST['id'])) {
    include "../DB_connection.php";
    include "Model/Task.php";

    $id     = (int) $_POST['id'];
    $status = in_array($_POST['status'], ['pending','in_progress','completed']) ? $_POST['status'] : 'pending';

    update_task_status($conn, [$status, $id]);
    header("Location: ../my_task.php?success=Task status updated"); exit();
} else {
    header("Location: ../my_task.php?error=Invalid request"); exit();
}
