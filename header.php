<?php
require_once __DIR__ . '/functions.php';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Page') ?> | Inventory System</title>
</head>
<body>

<nav>
    <a href="dashboard.php">Dashboard</a> |
    <a href="inventory.php">Products</a>
    <?php if (is_admin()): ?>
        | <a href="users.php">Users</a>
    <?php endif; ?>
    |
    <form method="POST" action="logout.php" style="display:inline;">
        <?= csrf_field() ?>
        <button type="submit">Logout (<?= e($_SESSION['username'] ?? '') ?>)</button>
    </form>
</nav>
<hr>

<?php if ($flash): ?>
    <p style="color: <?= $flash['type'] === 'error' ? 'red' : 'green' ?>;">
        <?= e($flash['message']) ?>
    </p>
<?php endif; ?>