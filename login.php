<?php
require_once 'includes/auth.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Remember who is logged in
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Wrong email or password.';
    }
}

$page_title = 'Login';
include 'includes/header.php';
?>
<h2>Login</h2>

<?php if (isset($_GET['registered'])): ?>
    <p class="alert success">Account created! You can log in now.</p>
<?php endif; ?>
<?php if ($error): ?>
    <p class="alert error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
</form>
<?php include 'includes/footer.php'; ?>
