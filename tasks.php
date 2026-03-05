<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Unauthorized"); exit();
}
include "DB_connection.php";
include "app/Model/Task.php";
include "app/Model/User.php";

// --- Filters from GET ---
$filters = [
    'status'   => $_GET['status']   ?? '',
    'priority' => $_GET['priority'] ?? '',
    'search'   => $_GET['search']   ?? '',
];
$page     = max(1, (int)($_GET['page'] ?? 1));

// Fix: safe per_page handling
$per_page = 10;
if (isset($_GET['per_page']) && in_array((int)$_GET['per_page'], [5, 10, 25, 50])) {
    $per_page = (int)$_GET['per_page'];
}

$result = get_all_tasks($conn, $filters, $page, $per_page);
$tasks  = $result['data'];
$users  = get_all_users($conn);

// Build query string for pagination links (preserve filters)
$queryParams = array_filter([
    'status'   => $filters['status'],
    'priority' => $filters['priority'],
    'search'   => $filters['search'],
    'per_page' => $per_page,
]);
$queryString = http_build_query($queryParams);
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Tasks — TaskFlow Pro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<input type="checkbox" id="checkbox">
<?php include "inc/header.php" ?>
<div class="body">
    <?php include "inc/nav.php" ?>
    <section class="section-1">
        <h4 class="title-2">
            <a href="create_task.php" class="btn"><i class="fa fa-plus"></i> Create Task</a>
            <a href="trash.php" class="btn btn-outline"><i class="fa fa-trash"></i> Trash</a>
        </h4>
        <h4 class="title">All Tasks <span class="badge-count"><?=$result['total']?></span></h4>

        <?php if (isset($_GET['success'])): ?>
            <div class="success"><?=htmlspecialchars($_GET['success'])?></div>
        <?php endif; ?>

        <!-- Filter Bar -->
        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="🔍 Search title or description..."
                   value="<?=htmlspecialchars($filters['search'])?>" class="filter-input">
            <select name="status" class="filter-select">
                <option value="">All Status</option>
                <?php foreach(['pending','in_progress','completed'] as $s): ?>
                <option value="<?=$s?>" <?=$filters['status']===$s?'selected':''?>>
                    <?=ucfirst(str_replace('_',' ',$s))?>
                </option>
                <?php endforeach; ?>
            </select>
            <select name="priority" class="filter-select">
                <option value="">All Priorities</option>
                <?php foreach(['low','medium','high'] as $p): ?>
                <option value="<?=$p?>" <?=$filters['priority']===$p?'selected':''?>>
                    <?=ucfirst($p)?>
                </option>
                <?php endforeach; ?>
            </select>
            <select name="per_page" class="filter-select">
                <?php foreach([5,10,25,50] as $n): ?>
                <option value="<?=$n?>" <?=$per_page===$n?'selected':''?>><?=$n?> / page</option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="edit-btn"><i class="fa fa-filter"></i> Filter</button>
            <a href="tasks.php" class="delete-btn">Reset</a>
        </form>

        <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <i class="fa fa-inbox"></i>
                <p>No tasks found.</p>
            </div>
        <?php else: ?>
        <table class="main-table">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Assigned To</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
            <?php $i = ($page - 1) * $per_page; foreach ($tasks as $task): $i++; ?>
            <tr>
                <td><?=$i?></td>
                <td>
                    <strong><?=htmlspecialchars($task['title'])?></strong>
                    <?php if (!empty($task['description'])): ?>
                    <br><small class="text-muted"><?=htmlspecialchars(substr($task['description'], 0, 50))?>...</small>
                    <?php endif; ?>
                </td>
                <td><?=htmlspecialchars($task['assigned_name'] ?? 'Unassigned')?></td>
                <td>
                    <span class="priority-badge priority-<?=$task['priority']?>">
                        <?=ucfirst($task['priority'])?>
                    </span>
                </td>
                <td>
                    <span class="status-badge status-<?=$task['status']?>">
                        <?=ucfirst(str_replace('_', ' ', $task['status']))?>
                    </span>
                </td>
                <td>
                    <?=$task['due_date'] ? date('M d, Y', strtotime($task['due_date'])) : '<em>No deadline</em>'?>
                </td>
                <td>
                    <a href="edit-task.php?id=<?=$task['id']?>" class="edit-btn">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <a href="delete-task.php?id=<?=$task['id']?>" class="delete-btn"
                       onclick="return confirm('Move this task to trash?')">
                        <i class="fa fa-trash"></i> Trash
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Pagination -->
        <?php if ($result['last_page'] > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="tasks.php?<?=$queryString?>&page=<?=$page-1?>" class="page-btn">← Prev</a>
            <?php endif; ?>
            <?php for ($p = 1; $p <= $result['last_page']; $p++): ?>
                <a href="tasks.php?<?=$queryString?>&page=<?=$p?>"
                   class="page-btn <?=$p===$page?'active':''?>"><?=$p?></a>
            <?php endfor; ?>
            <?php if ($page < $result['last_page']): ?>
                <a href="tasks.php?<?=$queryString?>&page=<?=$page+1?>" class="page-btn">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>

    </section>
</div>
<script>
    var active = document.querySelector("#navList li:nth-child(4)");
    if(active) active.classList.add("active");
</script>
</body>
</html>