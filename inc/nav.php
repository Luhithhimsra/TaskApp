<nav class="side-bar">
    <div class="user-p">
        <img src="img/user.png" alt="user">
        <h4>@<?=htmlspecialchars($_SESSION['username'])?></h4>
    </div>

    <?php if ($_SESSION['role'] === 'employee'): ?>
    <!-- Employee Navigation -->
    <ul id="navList">
        <li><a href="index.php"><i class="fa fa-tachometer"></i><span>Dashboard</span></a></li>
        <li><a href="my_task.php"><i class="fa fa-tasks"></i><span>My Tasks</span></a></li>
        <li><a href="profile.php"><i class="fa fa-user"></i><span>Profile</span></a></li>
        <li><a href="notifications.php"><i class="fa fa-bell"></i><span>Notifications</span></a></li>
        <li><a href="logout.php"><i class="fa fa-sign-out"></i><span>Logout</span></a></li>
    </ul>
    <?php else: ?>
    <!-- Admin Navigation -->
    <ul id="navList">
        <li><a href="index.php"><i class="fa fa-tachometer"></i><span>Dashboard</span></a></li>
        <li><a href="user.php"><i class="fa fa-users"></i><span>Manage Users</span></a></li>
        <li><a href="create_task.php"><i class="fa fa-plus"></i><span>Create Task</span></a></li>
        <li><a href="tasks.php"><i class="fa fa-tasks"></i><span>All Tasks</span></a></li>
        <li><a href="trash.php"><i class="fa fa-trash"></i><span>Trash</span></a></li>
        <li><a href="logout.php"><i class="fa fa-sign-out"></i><span>Logout</span></a></li>
    </ul>
    <?php endif; ?>
</nav>
