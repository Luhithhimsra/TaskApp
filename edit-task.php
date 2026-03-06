<?php
// Edit task form — admin only, allows updating title, description, priority, due date and assignment

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Unauthorized"); exit();
}
include "DB_connection.php";
include "app/Model/Task.php";
include "app/Model/User.php";

if (!isset($_GET['id'])) { header("Location: tasks.php"); exit(); }
$id   = (int)$_GET['id'];
$task = get_task_by_id($conn, $id);
if (!$task) { header("Location: tasks.php?error=Task not found"); exit(); }
$users = get_all_users($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Task — TaskFlow Pro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<input type="checkbox" id="checkbox">
<?php include "inc/header.php" ?>
<div class="body">
    <?php include "inc/nav.php" ?>
    <section class="section-1">
        <h4 class="title">Edit Task <a href="tasks.php" class="btn btn-outline">← Back</a></h4>
        <?php if (isset($_GET['error'])): ?>
            <div class="danger"><?=htmlspecialchars($_GET['error'])?></div>
        <?php endif; ?>

        <form class="form-1" method="POST" action="app/update-task.php">
            <input type="hidden" name="id" value="<?=$task['id']?>">
            <div class="input-holder">
                <label>Title *</label>
                <input type="text" name="title" class="input-1" value="<?=htmlspecialchars($task['title'])?>" required>
            </div>
            <div class="input-holder">
                <label>Description</label>
                <textarea name="description" class="input-1" rows="4"><?=htmlspecialchars($task['description'] ?? '')?></textarea>
            </div>
            <div class="input-holder">
                <label>Due Date</label>
                <input type="date" name="due_date" class="input-1" value="<?=$task['due_date']?>">
            </div>
            <div class="input-holder">
                <label>Priority</label>
                <select name="priority" class="input-1">
                    <?php foreach(['low'=>'🟢 Low','medium'=>'🟡 Medium','high'=>'🔴 High'] as $val=>$label): ?>
                    <option value="<?=$val?>" <?=$task['priority']===$val?'selected':''?>><?=$label?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-holder">
                <label>Assign To</label>
                <select name="assigned_to" class="input-1">
                    <option value="0">— Select employee —</option>
                    <?php if ($users): foreach ($users as $u): ?>
                    <option value="<?=$u['id']?>" <?=$task['assigned_to']==$u['id']?'selected':''?>>
                        <?=htmlspecialchars($u['full_name'])?>
                    </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <button type="submit" class="edit-btn"><i class="fa fa-save"></i> Update Task</button>
        </form>
    </section>
</div>
<script>
    var active = document.querySelector("#navList li:nth-child(4)");
    if(active) active.classList.add("active");
</script>
</body>
</html>
