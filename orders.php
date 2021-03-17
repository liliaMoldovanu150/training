<?php

require_once './common.php';

if (!isset($_SESSION['login_user'])) {
    header('Location: ./index.php');
    die();
}

$pdo = connection();

$sql = 'SELECT * FROM orders';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($orders as $key => $order) {
    $sql = 'SELECT * FROM products INNER JOIN order_products ON order_products.order_id='
        . $order['order_id']
        . ' WHERE order_products.product_id=products.product_id;';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $orderProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $orders[$key]['orderProducts'] = $orderProducts;
}

?>

<?php require_once './view/header.view.php'; ?>

<?php if (count($orders)): ?>
    <div class="orders-wrapper">
        <h1 class="order-heading"><?= translate('orders'); ?></h1>
        <?php foreach ($orders as $order): ?>
            <div class="order">
                <div class="order-details">
                    <div><?= translate('id'); ?>: <?= $order['order_id']; ?></div>
                    <div><?= translate('date'); ?>: <?= $order['creation_date']; ?></div>
                    <div><?= $order['customer_details']; ?></div>
                </div>
                <div class="order-products">
                    <?php foreach ($order['orderProducts'] as $orderProduct): ?>
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
                    <div class="total-price"><?= translate('order_total'); ?>: <?= $order['total_price']; ?></div>
                </div>
            </div>
            <hr>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once './view/footer.view.php'; ?>
