
<?php
require_once '../includes/auth.php';
require_role('staff');

$page_title = 'Manage Complaint';
$error = '';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: queue.php');
    exit;
}

// Update complaint status
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['update_status'])) {

    $status = $_POST['status'] ?? '';

    $allowed_statuses = ['Open', 'In-Progress', 'Resolved'];

    if (!in_array($status, $allowed_statuses, true)) {
        $error = 'Invalid complaint status.';
    } else {
        $stmt = $pdo->prepare(
            "UPDATE complaints
             SET status = ?, updated_at = NOW()
             WHERE id = ?"
        );
        $stmt->execute([$status, $id]);

        header("Location: manage.php?id=$id&updated=1");
        exit;
    }
}

// Add a staff response
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['add_response'])) {

    $message = trim($_POST['message'] ?? '');

    if ($message === '') {
        $error = 'Response comment cannot be empty.';
    } elseif (mb_strlen($message) > 2000) {
        $error = 'Response must not exceed 2000 characters.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO responses (complaint_id, staff_id, message)
             VALUES (?, ?, ?)"
        );
        $stmt->execute([
            $id,
            $_SESSION['user_id'],
            $message
        ]);

        header("Location: manage.php?id=$id&responded=1");
        exit;
    }
}

// Get complaint details and student name
$stmt = $pdo->prepare(
    "SELECT c.*, u.name AS student_name,
            cat.name AS category_name
     FROM complaints c
     JOIN users u ON c.user_id = u.id
     JOIN categories cat ON c.category_id = cat.id
     WHERE c.id = ?"
);
$stmt->execute([$id]);
$complaint = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$complaint) {
    http_response_code(404);
    exit('Complaint not found.');
}

// Get previous staff responses in chronological order
$stmt = $pdo->prepare(
    "SELECT r.message, r.created_at, u.name AS staff_name
     FROM responses r
     JOIN users u ON r.staff_id = u.id
     WHERE r.complaint_id = ?
     ORDER BY r.created_at ASC, r.id ASC"
);
$stmt->execute([$id]);
$responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="container">
    <h2>Manage Complaint #<?= (int)$complaint['id'] ?></h2>

    <?php if ($error !== ''): ?>
        <p class="error">
            <?= e($error) ?>
        </p>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <p class="success">Complaint status updated successfully.</p>
    <?php endif; ?>

    <?php if (isset($_GET['responded'])): ?>
        <p class="success">Response added successfully.</p>
    <?php endif; ?>

    <h3>Complaint Details</h3>

    <p><strong>Student:</strong>
        <?= e($complaint['student_name']) ?></p>

    <p><strong>Category:</strong>
        <?= e($complaint['category_name']) ?></p>

    <p><strong>Title:</strong>
        <?= e($complaint['title']) ?></p>

    <p><strong>Description:</strong><br>
        <?= nl2br(e($complaint['description'])) ?></p>

    <p><strong>Priority:</strong>
        <?= e($complaint['priority']) ?></p>

    <p><strong>Current Status:</strong>
        <?= e($complaint['status']) ?></p>

    <p><strong>Submitted:</strong>
        <?= e($complaint['created_at']) ?></p>

    <hr>

    <h3>Update Status</h3>

    <form method="POST"
          action="manage.php?id=<?= (int)$id ?>">

        <label for="status">Complaint Status</label>

        <select name="status" id="status" required>
            <?php
            $statuses = ['Open', 'In-Progress', 'Resolved'];
            foreach ($statuses as $status):
            ?>
                <option value="<?= e($status) ?>"
                    <?= $complaint['status'] === $status
                        ? 'selected' : '' ?>>
                    <?= e($status) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="update_status">
            Update Status
        </button>
    </form>

    <hr>

    <h3>Add Response</h3>

    <form method="POST"
          action="manage.php?id=<?= (int)$id ?>">

        <label for="message">Response Comment</label>

        <textarea name="message" id="message"
                  rows="4" maxlength="2000"
                  required></textarea>

        <button type="submit" name="add_response">
            Submit Response
        </button>
    </form>

    <hr>

    <h3>Previous Responses</h3>

    <?php if (count($responses) > 0): ?>

        <?php foreach ($responses as $response): ?>
            <div class="response">
                <p>
                    <strong><?= e($response['staff_name']) ?></strong>
                    <small>
                        <?= e($response['created_at']) ?>
                    </small>
                </p>

                <p><?= nl2br(e($response['message'])) ?></p>
            </div>
            <hr>
        <?php endforeach; ?>

    <?php else: ?>
        <p>No responses have been added yet.</p>
    <?php endif; ?>

    <p><a href="queue.php">Back to Staff Queue</a></p>
</div>

<?php require_once '../includes/footer.php'; ?>