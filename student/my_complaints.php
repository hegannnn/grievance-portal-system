<?php

require_once '../includes/auth.php';
require_role('student');

$page_title = 'My Complaints';

/* Get only complaints submitted by the logged-in student */
$stmt = $pdo->prepare(
    "SELECT 
        c.id,
        c.title,
        c.priority,
        c.status,
        c.created_at,
        cat.name AS category_name
     FROM complaints c
     JOIN categories cat ON c.category_id = cat.id
     WHERE c.user_id = ?
     ORDER BY c.created_at DESC"
);

$stmt->execute([$_SESSION['user_id']]);

$complaints = $stmt->fetchAll();

include '../includes/header.php';
?>

<h2>My Complaints</h2>

<?php if (count($complaints) === 0): ?>

    <p>You have not submitted any complaints yet.</p>

<?php else: ?>

    <table border="1" cellpadding="8" cellspacing="0">

        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($complaints as $complaint): ?>

                <tr>

                    <td>
                        <?php echo e($complaint['id']); ?>
                    </td>

                    <td>
                        <?php echo e($complaint['title']); ?>
                    </td>

                    <td>
                        <?php echo e($complaint['category_name']); ?>
                    </td>

                    <td>
                        <?php echo e($complaint['priority']); ?>
                    </td>

                    <td>
                        <?php echo e($complaint['status']); ?>
                    </td>

                    <td>
                        <?php echo e($complaint['created_at']); ?>
                    </td>

                    <td>
                        <a href="view.php?id=<?php echo e($complaint['id']); ?>">
                            View
                        </a>
                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>