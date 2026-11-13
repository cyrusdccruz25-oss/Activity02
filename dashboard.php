<?php
require_once 'config.php';
require_once 'includes/functions.php';

require_login();

$stats = $pdo->query(
    "SELECT COUNT(*) AS total_products,
            COALESCE(SUM(quantity), 0) AS total_quantity,
            COALESCE(SUM(quantity * price), 0) AS total_value
     FROM products"
)->fetch();

$pendingUsers = 0;
if (is_admin()) {
    $pendingUsers = (int) $pdo->query(
        "SELECT COUNT(*) FROM users WHERE status = 'pending'"
    )->fetchColumn();
}

$pageTitle = 'Dashboard';
require 'includes/header.php';
?>

<h1>Dashboard</h1>
<p>Welcome, <b><?= e($_SESSION['username']) ?></b> (<?= e($_SESSION['role']) ?>)</p>

<ul>
    <li>Total products: <?= (int) $stats['total_products'] ?></li>
    <li>Total items in stock: <?= (int) $stats['total_quantity'] ?></li>
    <li>Total inventory value: ₱<?= number_format((float) $stats['total_value'], 2) ?></li>
    <?php if (is_admin()): ?>
        <li>Accounts waiting for approval: <?= $pendingUsers ?>
            (<a href="users.php">manage users</a>)</li>
    <?php endif; ?>
</ul>

<p><a href="inventory.php">Go to Products</a></p>

<?php require 'includes/footer.php'; ?>