<?php
session_start();
if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    header("Location: login.php?error=Unauthorized"); exit();
}
include "DB_connection.php";
include "app/Model/Task.php";

$filters  = [
    'status'   => $_GET['status']   ?? '',
    'priority' => $_GET['priority'] ?? '',
    'search'   => $_GET['search']   ?? '',
];
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$result   = get_all_tasks_by_employee($conn, $_SESSION['id'], $filters, $page, $per_page);
$tasks    = $result['data'];

$queryParams = array_filter(['status'=>$filters['status'],'priority'=>$filters['priority'],'search'=>$filters['search']]);
$queryString = http_build_query($queryParams);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Tasks — TaskFlow Pro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<input type="checkbox" id="checkbox">
<?php include "inc/header.php" ?>
<div class="body">
    <?php include "inc/nav.php" ?>
    <section class="section-1">
        <h4 class="title">My Tasks <span class="badge-count"><?=$result['total']?></span></h4>

        <?php if (isset($_GET['success'])): ?>
            <div class="success"><?=htmlspecialchars($_GET['success'])?></div>
        <?php endif; ?>

        <!-- Filter Bar -->
        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="🔍 Search..." class="filter-input"
                   value="<?=htmlspecialchars($filters['search'])?>">
            <select name="status" class="filter-select">
                <option value="">All Status</option>
                <?php foreach(['pending','in_progress','completed'] as $s): ?>
                <option value="<?=$s?>" <?=$filters['status']===$s?'selected':''?>><?=ucfirst(str_replace('_',' ',$s))?></option>
                <?php endforeach; ?>
            </select>
            <select name="priority" class="filter-select">
                <option value="">All Priorities</option>
                <?php foreach(['low','medium','high'] as $p): ?>
                <option value="<?=$p?>" <?=$filters['priority']===$p?'selected':''?>><?=ucfirst($p)?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="edit-btn"><i class="fa fa-filter"></i> Filter</button>
            <a href="my_task.php" class="delete-btn">Reset</a>
        </form>

        <?php if (empty($tasks)): ?>
            <div class="empty-state"><i class="fa fa-inbox"></i><p>No tasks found.</p></div>
        <?php else: ?>
        <table class="main-table">
            <tr>
                <th>#</th><th>Title</th><th>Priority</th><th>Status</th><th>Due Date</th><th>Action</th>
            </tr>
            <?php $i = ($page-1)*$per_page; foreach ($tasks as $task): $i++; ?>
            <tr>
                <td><?=$i?></td>
                <td><?=htmlspecialchars($task['title'])?></td>
                <td><span class="priority-badge priority-<?=$task['priority']?>"><?=ucfirst($task['priority'])?></span></td>
                <td><span class="status-badge status-<?=$task['status']?>"><?=ucfirst(str_replace('_',' ',$task['status']))?></span></td>
                <td><?=$task['due_date'] ? date('M d, Y', strtotime($task['due_date'])) : '<em>No deadline</em>'?></td>
                <td><a href="edit-task-employee.php?id=<?=$task['id']?>" class="edit-btn"><i class="fa fa-edit"></i> Update</a></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Pagination -->
        <?php if ($result['last_page'] > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="my_task.php?<?=$queryString?>&page=<?=$page-1?>" class="page-btn">← Prev</a>
            <?php endif; ?>
            <?php for ($p=1; $p<=$result['last_page']; $p++): ?>
                <a href="my_task.php?<?=$queryString?>&page=<?=$p?>" class="page-btn <?=$p===$page?'active':''?>"><?=$p?></a>
            <?php endfor; ?>
            <?php if ($page < $result['last_page']): ?>
                <a href="my_task.php?<?=$queryString?>&page=<?=$page+1?>" class="page-btn">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </section>
</div>
<script>
    var active = document.querySelector("#navList li:nth-child(2)");
    if(active) active.classList.add("active");
</script>
</body>
</html>
