<?php
// Database settings. Change these if your MySQL setup is different.
$host   = 'localhost';
$dbname = 'grievance_portal';
$dbuser = 'root';
$dbpass = '';   // XAMPP has an empty password by default

// Folder name of the project inside htdocs (used to build links)
define('BASE_URL', '/grievance-portal');

// Anyone who enters this code while registering becomes a staff member
define('STAFF_CODE', 'STAFF2026');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Could not connect to the database. Is MySQL running? ' . $e->getMessage());
}
