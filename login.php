<?php
session_start();
if (isset($_SESSION['role'])) { header("Location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login — TaskFlow Pro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-container">
    <div class="login-box">
        <h2>TaskFlow <span style="color:#4ade80">Pro</span></h2>
        <p>Sign in to manage your tasks</p>

        <?php if (isset($_GET['error'])): ?>
            <div class="danger"><?=htmlspecialchars($_GET['error'])?></div>
        <?php endif; ?>

        <form method="POST" action="app/login.php">
            <div class="input-holder">
                <label>Username</label>
                <input type="text" name="user_name" class="input-1" placeholder="Enter username" required autofocus>
            </div>
            <div class="input-holder">
                <label>Password</label>
                <input type="password" name="password" class="input-1" placeholder="Enter password" required>
            </div>
            <button type="submit" class="login-btn"><i class="fa fa-sign-in"></i> Sign In</button>
        </form>

        <div style="margin-top:20px;padding:14px;background:#f9fafb;border-radius:8px;font-size:12px;color:#6b7280">
            <strong>Demo accounts:</strong><br>
            Admin: <code>admin</code> / <code>admin123</code><br>
            Employee: <code>john</code> / <code>password123</code>
        </div>
    </div>
</div>
</body>
</html>
