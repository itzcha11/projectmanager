<?php
session_start();

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Manager</title>
    <link rel="stylesheet" href="/projectmanager/css/style.css">
</head>
<body>
<header>
    <h1>Project Manager</h1>

    <!-- Centered links -->
    <nav class="main-nav">
        <a href="/projectmanager/index.php">Home</a>
        <?php if (isset($_SESSION['uid'])): ?>
            <a href="/projectmanager/project_add.php">Add Project</a>
        <?php endif; ?>
    </nav>

    <!-- Right-aligned user links -->
    <nav class="user-nav">
        <?php if (isset($_SESSION['uid'])): ?>
            <a href="/projectmanager/logout.php?csrf_token=<?= $_SESSION['csrf_token'] ?>">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
        <?php else: ?>
            <a href="/projectmanager/register.php">Register</a>
            <a href="/projectmanager/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<main>