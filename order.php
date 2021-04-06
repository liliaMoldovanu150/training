<?php

require_once './common.php';

if (!isset($_SESSION['login_user'])) {
    header('Location: ./index.php');
    die();
}

$sql = 'SELECT * FROM orders o 
    INNER JOIN order_products op ON o.order_id = op.order_id 
    INNER JOIN products p ON p.product_id = op.product_id WHERE o.order_id = ?';

$stmt = connection()->prepare($sql);
$stmt->execute([$_GET['id']]);
$orderInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require_once './view/header.view.php'; ?>

<div class="orders-wrapper">
    <?php if ($orderInfo): ?>
        <h1 class="order-heading"><?= translate('order'); ?></h1>
        <div class="order">
            <div class="order-details">
                <div><?= translate('id'); ?>: <?= $orderInfo[0]['order_id']; ?></div>
                <div><?= translate('date'); ?>: <?= $orderInfo[0]['creation_date']; ?></div>
                <div><?= translate('name'); ?>: <?= $orderInfo[0]['customer_name']; ?></div>
                <div><?= translate('contact_details'); ?>: <?= $orderInfo[0]['contact_details']; ?></div>
                <div><?= translate('comments'); ?>: <?= $orderInfo[0]['comments']; ?></div>
            </div>
            <div class="order-products">
                <?php foreach ($orderInfo as $orderProduct): ?>
                    <div class="product-item">
                        <div class="product-image order-image">
                            <img src="./images/<?= $orderProduct['image_url']; ?>"
                                 alt="<?= translate('product_image'); ?>">
                        </div>
                        <div class="product-features">
                            <div><?= $orderProduct['title']; ?></div>
                            <div><?= $orderProduct['product_price']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="total-price">
                    <?= translate('order_total'); ?>: <?= $orderInfo[0]['total_price']; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p><?= translate('no_order'); ?>: <?= $_GET['id']; ?></p>
    <?php endif; ?>
    <hr>
</div>

<?php require_once './view/footer.view.php'; ?>
