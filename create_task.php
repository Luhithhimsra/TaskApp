<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Unauthorized"); exit();
}
include "DB_connection.php";
include "app/Model/User.php";
$users = get_all_users($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Task — TaskFlow Pro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<input type="checkbox" id="checkbox">
<?php include "inc/header.php" ?>
<div class="body">
    <?php include "inc/nav.php" ?>
    <section class="section-1">
        <h4 class="title">Create Task</h4>
        <?php if (isset($_GET['error'])): ?>
            <div class="danger"><?=htmlspecialchars($_GET['error'])?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="success"><?=htmlspecialchars($_GET['success'])?></div>
        <?php endif; ?>

        <form class="form-1" method="POST" action="app/add-task.php">
            <div class="input-holder">
                <label>Title *</label>
                <input type="text" name="title" class="input-1" placeholder="Task title" required>
            </div>
            <div class="input-holder">
                <label>Description</label>
                <textarea name="description" class="input-1" placeholder="Task description..." rows="4"></textarea>
            </div>
            <div class="input-holder">
                <label>Due Date</label>
                <input type="date" name="due_date" class="input-1">
            </div>
            <div class="input-holder">
                <label>Priority *</label>
                <select name="priority" class="input-1">
                    <option value="low">🟢 Low</option>
                    <option value="medium" selected>🟡 Medium</option>
                    <option value="high">🔴 High</option>
                </select>
            </div>
            <div class="input-holder">
                <label>Assign To *</label>
                <select name="assigned_to" class="input-1">
                    <option value="0">— Select employee —</option>
                    <?php if ($users): foreach ($users as $u): ?>
                    <option value="<?=$u['id']?>"><?=htmlspecialchars($u['full_name'])?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <button type="submit" class="edit-btn"><i class="fa fa-plus"></i> Create Task</button>
        </form>
    </section>
</div>
<script>
    var active = document.querySelector("#navList li:nth-child(3)");
    if(active) active.classList.add("active");
</script>
</body>
</html>
