<?php
// Include this at the top of EVERY page. It starts the session,
// connects to the database and gives us the login/role helper functions.
session_start();
require_once __DIR__ . '/../config/db.php';

// Shortcut for safely printing user text on a page (stops XSS)
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Use on pages that need any logged in user
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

// Use on pages that only one role may see, e.g. require_role('staff');
// This is our "middleware": it runs before the page does anything else.
function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        die('Access denied. This page is only for ' . e($role) . ' accounts.');
    }
}
