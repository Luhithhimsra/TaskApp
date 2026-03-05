<?php
// Restore handler — recovers soft deleted tasks back to active state
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Unauthorized"); exit();
}

include "DB_connection.php";
include "app/Model/Task.php";

if (!isset($_GET['id'])) {
    header("Location: trash.php"); exit();
}

$id = (int) $_GET['id'];
restore_task($conn, $id);
header("Location: trash.php?success=Task restored successfully");
exit();
