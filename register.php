<?php
require_once 'includes/auth.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm'] ?? '';
    $staff_code = trim($_POST['staff_code'] ?? '');

    // Validation
    if ($name === '') {
        $errors[] = 'Please enter your name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Everyone is a student unless they know the staff code
    $role = 'student';
    if ($staff_code !== '') {
        if ($staff_code === STAFF_CODE) {
            $role = 'staff';
        } else {
            $errors[] = 'Wrong staff code.';
        }
    }

    // Check the email is not already used
    if (!$errors) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = 'This email is already registered.';
        }
    }

    // All good - save the user (password is stored hashed, never plain text)
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $insert->execute([$name, $email, $hash, $role]);
        header('Location: login.php?registered=1');
        exit;
    }
}

$page_title = 'Register';
include 'includes/header.php';
?>
<h2>Create an account</h2>

<?php foreach ($errors as $error): ?>
    <p class="alert error"><?= e($error) ?></p>
<?php endforeach; ?>

<form method="post">
    <label>Full name</label>
    <input type="text" name="name" value="<?= e($name) ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= e($email) ?>" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Confirm password</label>
    <input type="password" name="confirm" required>

    <label>Staff code (leave empty if you are a student)</label>
    <input type="text" name="staff_code">

    <button type="submit">Register</button>
</form>
<?php include 'includes/footer.php'; ?>
