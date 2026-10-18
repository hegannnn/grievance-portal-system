
<?php
require_once '../includes/auth.php';
require_role('staff');

require_once '../config/db.php';

$page_title = 'Complaint Queue';

$sql = "SELECT
            c.id,
            c.title,
            c.priority,
            c.status,
            c.created_at,
            u.name AS student_name,
            cat.name AS category_name
        FROM complaints c
        JOIN users u ON c.user_id = u.id
        JOIN categories cat ON c.category_id = cat.id
        ORDER BY
            CASE c.priority
                WHEN 'High' THEN 1
                WHEN 'Medium' THEN 2
                ELSE 3
            END,
            c.created_at DESC";

$stmt = $pdo->query($sql);
$complaints = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<h1>Staff Complaint Queue</h1>
<p>Review student complaints and manage their status or responses.</p>

<?php if (!empty($complaints)): ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $complaint): ?>
                    <tr>
                        <td><?= (int) $complaint['id'] ?></td>
                        <td><?= htmlspecialchars($complaint['student_name']) ?></td>
                        <td><?= htmlspecialchars($complaint['title']) ?></td>
                        <td><?= htmlspecialchars($complaint['category_name']) ?></td>
                        <td><?= htmlspecialchars($complaint['priority']) ?></td>
                        <td><?= htmlspecialchars($complaint['status']) ?></td>
                        <td><?= htmlspecialchars($complaint['created_at']) ?></td>
                        <td>
                            <a class="btn"
                               href="<?= BASE_URL ?>/staff/manage.php?id=<?= (int) $complaint['id'] ?>">
                                Manage
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>No complaints have been submitted yet.</p>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
