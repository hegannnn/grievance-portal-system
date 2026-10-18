<?php

require_once '../includes/auth.php';
require_role('student');

$page_title = 'View Complaint';

$complaint_id = $_GET['id'] ?? '';

/* Check that a valid complaint ID was provided */
if (!ctype_digit($complaint_id)) {
    http_response_code(400);
    die('Invalid complaint ID.');
}

/*
 * Get the complaint only if it belongs to the logged-in student.
 * This prevents one student from viewing another student's complaint.
 */
$stmt = $pdo->prepare(
    "SELECT
        c.id,
        c.title,
        c.description,
        c.priority,
        c.status,
        c.created_at,
        c.updated_at,
        cat.name AS category_name
     FROM complaints c
     JOIN categories cat ON c.category_id = cat.id
     WHERE c.id = ? AND c.user_id = ?"
);

$stmt->execute([
    $complaint_id,
    $_SESSION['user_id']
]);

$complaint = $stmt->fetch();

/* Complaint does not exist or does not belong to this student */
if (!$complaint) {
    http_response_code(404);
    die('Complaint not found.');
}

/* Get all staff responses for this complaint */
$stmt = $pdo->prepare(
    "SELECT
        r.message,
        r.created_at,
        u.name AS staff_name
     FROM responses r
     JOIN users u ON r.staff_id = u.id
     WHERE r.complaint_id = ?
     ORDER BY r.created_at ASC"
);

$stmt->execute([$complaint_id]);

$responses = $stmt->fetchAll();

include '../includes/header.php';
?>

<h2>Complaint Details</h2>

<p>
    <strong>Complaint ID:</strong>
    <?php echo e($complaint['id']); ?>
</p>

<p>
    <strong>Title:</strong>
    <?php echo e($complaint['title']); ?>
</p>

<p>
    <strong>Category:</strong>
    <?php echo e($complaint['category_name']); ?>
</p>

<p>
    <strong>Priority:</strong>
    <?php echo e($complaint['priority']); ?>
</p>

<p>
    <strong>Status:</strong>
    <?php echo e($complaint['status']); ?>
</p>

<p>
    <strong>Description:</strong>
</p>

<p>
    <?php echo nl2br(e($complaint['description'])); ?>
</p>

<p>
    <strong>Submitted on:</strong>
    <?php echo e($complaint['created_at']); ?>
</p>

<p>
    <strong>Last updated:</strong>
    <?php echo e($complaint['updated_at']); ?>
</p>

<hr>

<h3>Staff Responses</h3>

<?php if (count($responses) === 0): ?>

    <p>No staff responses yet.</p>

<?php else: ?>

    <?php foreach ($responses as $response): ?>

        <div>
            <p>
                <strong>
                    <?php echo e($response['staff_name']); ?>
                </strong>
            </p>

            <p>
                <?php echo nl2br(e($response['message'])); ?>
            </p>

            <small>
                <?php echo e($response['created_at']); ?>
            </small>
        </div>

        <hr>

    <?php endforeach; ?>

<?php endif; ?>

<p>
    <a href="my_complaints.php">Back to My Complaints</a>
</p>

<?php include '../includes/footer.php'; ?>