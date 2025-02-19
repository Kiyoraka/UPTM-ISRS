<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn() && basename($_SERVER['PHP_SELF']) != 'login.php') {
    redirect('../index.html');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <div class="header">
        <div class="logo">
            <img src="../assets/images/uptm-logo.png" alt="UPTM Logo">
        </div>
        <div class="user-menu">
            <span>Welcome, <?php echo $_SESSION['username']; ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <?php endif; ?>