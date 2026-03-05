<?php
// Soft delete handler — marks task as deleted without removing from database
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Unauthorized"); exit();
}

include "DB_connection.php";
include "app/Model/Task.php";

if (!isset($_GET['id'])) {
    header("Location: tasks.php"); exit();
}

$id   = (int) $_GET['id'];
$task = get_task_by_id($conn, $id);

if (!$task) {
    header("Location: tasks.php?error=Task not found"); exit();
}

soft_delete_task($conn, $id);
header("Location: tasks.php?success=Task moved to trash");
exit();
