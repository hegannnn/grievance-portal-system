<?php
// Shared top part of every page. Set $page_title before including it.
if (!isset($page_title)) { $page_title = 'Grievance Portal'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> - Grievance Portal</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <a class="brand" href="<?= BASE_URL ?>/index.php">Grievance Portal</a>
    <div class="nav-links">
        <?php if (is_logged_in()): ?>
            <?php if ($_SESSION['role'] === 'student'): ?>
                <a href="<?= BASE_URL ?>/student/submit.php">New Complaint</a>
                <a href="<?= BASE_URL ?>/student/my_complaints.php">My Complaints</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/staff/queue.php">Complaint Queue</a>
            <?php endif; ?>
            <span class="user-name"><?= e($_SESSION['name']) ?> (<?= e($_SESSION['role']) ?>)</span>
            <a href="<?= BASE_URL ?>/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login.php">Login</a>
            <a href="<?= BASE_URL ?>/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
<div class="container">
