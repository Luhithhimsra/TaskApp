<?php
// Dashboard — shows task statistics for admin and employee roles
// Admin sees all task counts, employee sees only their own tasks
session_start();
if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    header("Location: login.php?error=Please login first"); exit();
}

include "DB_connection.php";
include "app/Model/Task.php";
include "app/Model/User.php";

if ($_SESSION['role'] == "admin") {
    $num_task    = count_tasks($conn);
    $num_users   = count_users($conn);
    $pending     = count_pending_tasks($conn);
    $in_progress = count_in_progress_tasks($conn);
    $completed   = count_completed_tasks($conn);
    $overdue     = count_tasks_overdue($conn);
    $due_today   = count_tasks_due_today($conn);
} else {
    $num_my_task = count_my_tasks($conn, $_SESSION['id']);
    $pending     = count_my_pending_tasks($conn, $_SESSION['id']);
    $in_progress = count_my_in_progress_tasks($conn, $_SESSION['id']);
    $completed   = count_my_completed_tasks($conn, $_SESSION['id']);
    $overdue     = count_my_tasks_overdue($conn, $_SESSION['id']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard — TaskFlow Pro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<input type="checkbox" id="checkbox">
<?php include "inc/header.php" ?>
<div class="body">
    <?php include "inc/nav.php" ?>
    <section class="section-1">
        <h4 class="title">Dashboard</h4>

        <?php if ($_SESSION['role'] == "admin"): ?>
        <div class="dashboard">
            <div class="dashboard-item">
                <i class="fa fa-users"></i>
                <span><?=$num_users?> Employees</span>
            </div>
            <div class="dashboard-item">
                <i class="fa fa-tasks"></i>
                <span><?=$num_task?> Total Tasks</span>
            </div>
            <div class="dashboard-item">
                <i class="fa fa-square-o"></i>
                <span><?=$pending?> Pending</span>
            </div>
            <div class="dashboard-item">
                <i class="fa fa-spinner"></i>
                <span><?=$in_progress?> In Progress</span>
            </div>
            <div class="dashboard-item">
                <i class="fa fa-check-square-o"></i>
                <span><?=$completed?> Completed</span>
            </div>
            <div class="dashboard-item">
                <i class="fa fa-exclamation-triangle" style="color:#ef4444"></i>
                <span><?=$overdue?> Overdue</span>
            </div>
            <div class="dashboard-item">
                <i class="fa fa-clock-o" style="color:#f59e0b"></i>
                <span><?=$due_today?> Due Today</span>
            </div>
        </div>

        <div style="margin-top:24px">
            <a href="tasks.php" class="btn"><i class="fa fa-tasks"></i> View All Tasks</a>
            &nbsp;
            <a href="create_task.php" class="btn"><i class="fa fa-plus"></i> Create Task</a>
        </div>

        <?php else: ?>
        <div class="dashboard">
            <div class="dashboard-item">
                <i class="fa fa-tasks"></i>
                <span><?=$num_my_task?> My Tasks</span>
            </div>
            <div class="dashboard-item">
                <i class="fa fa-square-o"></i>
                <span><?=$pending?> Pending</span>
            </div>
            <div class="dashboard-item">
                <i class="fa fa-spinner"></i>
                <span><?=$in_progress?> In Progress</span>
            </div>
            <div class="dashboard-item">
                <i class="fa fa-check-square-o"></i>
                <span><?=$completed?> Completed</span>
            </div>
            <div class="dashboard-item">
                <i class="fa fa-exclamation-triangle" style="color:#ef4444"></i>
                <span><?=$overdue?> Overdue</span>
            </div>
        </div>

        <div style="margin-top:24px">
            <a href="my_task.php" class="btn"><i class="fa fa-tasks"></i> View My Tasks</a>
        </div>
        <?php endif; ?>

    </section>
</div>

<script>
    var active = document.querySelector("#navList li:nth-child(1)");
    if(active) active.classList.add("active");
</script>
</body>
</html>