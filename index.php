<?php
require_once 'includes/auth.php';

// Logged in users go straight to their own home page
if (is_logged_in()) {
    if ($_SESSION['role'] === 'staff') {
        header('Location: staff/queue.php');
    } else {
        header('Location: student/my_complaints.php');
    }
    exit;
}

$page_title = 'Home';
include 'includes/header.php';
?>
<h1>Online Grievance Redressal System</h1>
<p>Raise hostel, academic or facility complaints and track them until they are resolved.</p>
<p>
    <a class="btn" href="login.php">Login</a>
    <a class="btn" href="register.php">Register</a>
</p>
<?php include 'includes/footer.php'; ?>
