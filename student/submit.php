<?php

require_once '../includes/auth.php';
require_role('student');

$page_title = 'Submit Complaint';

$message = '';
$error = '';

/* Get all complaint categories */
$stmt = $pdo->prepare("SELECT id, name FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

/* Handle form submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $priority = $_POST['priority'] ?? '';

    /* Validate the form */
    if ($title === '' || $description === '' || $category_id === '' || $priority === '') {
        $error = 'Please fill in all fields.';
    } elseif (!in_array($priority, ['Low', 'Medium', 'High'], true)) {
        $error = 'Invalid priority selected.';
    } else {

        /* Insert complaint */
        $stmt = $pdo->prepare(
            "INSERT INTO complaints 
            (user_id, category_id, title, description, priority)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $_SESSION['user_id'],
            $category_id,
            $title,
            $description,
            $priority
        ]);

        $message = 'Complaint submitted successfully.';

        /* Clear the form after successful submission */
        $title = '';
        $description = '';
        $category_id = '';
        $priority = '';
    }
}

include '../includes/header.php';
?>

<h2>Submit a Complaint</h2>

<?php if ($message !== ''): ?>
    <p><?php echo e($message); ?></p>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <p><?php echo e($error); ?></p>
<?php endif; ?>

<form method="POST">

    <label for="title">Complaint Title:</label>
    <input
        type="text"
        id="title"
        name="title"
        maxlength="150"
        value="<?php echo e($title ?? ''); ?>"
        required
    >

    <br><br>

    <label for="category_id">Category:</label>
    <select id="category_id" name="category_id" required>

        <option value="">Select Category</option>

        <?php foreach ($categories as $category): ?>

            <option
                value="<?php echo e($category['id']); ?>"
                <?php echo (($category_id ?? '') == $category['id']) ? 'selected' : ''; ?>
            >
                <?php echo e($category['name']); ?>
            </option>

        <?php endforeach; ?>

    </select>

    <br><br>

    <label for="description">Description:</label>
    <br>

    <textarea
        id="description"
        name="description"
        rows="6"
        cols="50"
        required
    ><?php echo e($description ?? ''); ?></textarea>

    <br><br>

    <label for="priority">Priority:</label>

    <select id="priority" name="priority" required>

        <option value="">Select Priority</option>

        <option value="Low" <?php echo (($priority ?? '') === 'Low') ? 'selected' : ''; ?>>
            Low
        </option>

        <option value="Medium" <?php echo (($priority ?? '') === 'Medium') ? 'selected' : ''; ?>>
            Medium
        </option>

        <option value="High" <?php echo (($priority ?? '') === 'High') ? 'selected' : ''; ?>>
            High
        </option>

    </select>

    <br><br>

    <button type="submit">Submit Complaint</button>

</form>

<?php include '../includes/footer.php'; ?>