<?php

require_once './common.php';

if (!isset($_SESSION['login_user'])) {
    header('Location: ./index.php');
    die();
}

$pdo = connection();

$sql = 'SELECT o.order_id, o.total_price FROM orders o;';

$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require_once './view/header.view.php'; ?>

<div class="orders-wrapper">
    <h1 class="order-heading"><?= translate('orders'); ?></h1>
    <?php foreach ($orders as $order): ?>
        <a style="text-decoration: none; color: black" href="./order.php?id=<?= $order['order_id']; ?>">
            <div class="order" style="display: flex; justify-content: space-around">
                <div><?= translate('id'); ?>: <?= $order['order_id']; ?></div>
                <div class="total-price"><?= translate('order_total'); ?>: <?= $order['total_price']; ?></div>
            </div>
        </a>
        <hr>
    <?php endforeach; ?>
</div>

<?php require_once './view/footer.view.php'; ?>
