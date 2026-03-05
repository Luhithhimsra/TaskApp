<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Unauthorized"); exit();
}
include "DB_connection.php";
include "app/Model/Task.php";
$trashed = get_trashed_tasks($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trash — TaskFlow Pro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<input type="checkbox" id="checkbox">
<?php include "inc/header.php" ?>
<div class="body">
    <?php include "inc/nav.php" ?>
    <section class="section-1">
        <h4 class="title">🗑 Trash <span class="badge-count"><?=count($trashed)?></span></h4>

        <?php if (isset($_GET['success'])): ?>
            <div class="success"><?=htmlspecialchars($_GET['success'])?></div>
        <?php endif; ?>

        <?php if (empty($trashed)): ?>
            <div class="empty-state"><i class="fa fa-check-circle"></i><p>Trash is empty.</p></div>
        <?php else: ?>
        <table class="main-table">
            <tr>
                <th>#</th><th>Title</th><th>Description</th>
                <th>Assigned To</th><th>Priority</th><th>Deleted At</th><th>Action</th>
            </tr>
            <?php $i = 0; foreach ($trashed as $task): ?>
            <tr class="trashed-row">
                <td><?=++$i?></td>
                <td><?=htmlspecialchars($task['title'])?></td>
                <td><?=htmlspecialchars(substr($task['description'] ?? '', 0, 60))?>...</td>
                <td><?=htmlspecialchars($task['assigned_name'] ?? 'Unassigned')?></td>
                <td><span class="priority-badge priority-<?=$task['priority']?>"><?=ucfirst($task['priority'])?></span></td>
                <td><?=date('M d, Y', strtotime($task['deleted_at']))?></td>
                <td>
                    <a href="restore-task.php?id=<?=$task['id']?>" class="edit-btn">
                        <i class="fa fa-undo"></i> Restore
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </section>
</div>
<script>
    var active = document.querySelector("#navList li:last-child");
    if(active) active.classList.add("active");
</script>
</body>
</html>
