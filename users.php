<?php
require_once 'config.php';
require_once 'includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id === (int) $_SESSION['user_id']) {
        flash('You cannot change your own account.', 'error');
    } elseif (in_array($action, ['approve', 'revoke'], true)) {
        $status = $action === 'approve' ? 'approved' : 'pending';
        $pdo->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$status, $id]);
        flash('User updated.');
    }

    header('Location: users.php');
    exit;
}

$users = $pdo->query("SELECT id, username, status, role, created_at FROM users ORDER BY status ASC, id DESC")->fetchAll();

$pageTitle = 'Users';
require 'includes/header.php';
?>

<h1>Users</h1>

<table border="1" cellpadding="6" cellspacing="0">
    <tr>
        <th>ID</th><th>Username</th><th>Role</th><th>Status</th><th>Registered</th><th>Action</th>
    </tr>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= (int) $u['id'] ?></td>
            <td><?= e($u['username']) ?></td>
            <td><?= e($u['role']) ?></td>
            <td><?= e($u['status']) ?></td>
            <td><?= e($u['created_at']) ?></td>
            <td>
                <?php if ((int) $u['id'] !== (int) $_SESSION['user_id']): ?>
                    <form method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                        <?php if ($u['status'] === 'pending'): ?>
                            <button type="submit" name="action" value="approve">Approve</button>
                        <?php else: ?>
                            <button type="submit" name="action" value="revoke">Revoke</button>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php require 'includes/footer.php'; ?>