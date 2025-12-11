<?php
include 'includes/header.php'; // starts session

// CSRF protection for logout
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed");
}

session_destroy();
header("Location: index.php");
exit();