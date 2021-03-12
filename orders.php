<?php

require_once './common.php';

if (!isset($_SESSION['login_user'])) {
    header('Location: ./index.php');
}

$sql = 'SELECT * FROM orders';
$stmt = connection()->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require_once './view/header.view.php'; ?>

<?php if (count($orders)): ?>
    <div class="orders-wrapper">
        <h1 class="order-heading"><?= translate('orders'); ?></h1>
        <?php foreach ($orders as $order): ?>
            <div class="order">
                <div class="order-details">
                    <div><?= translate('id') . ': ' . $order['id']; ?></div>
                    <div><?= translate('date') . ': ' . $order['creation_date']; ?></div>
                    <div><?= $order['customer_details']; ?></div>
                </div>
                <div class="order-products">
                    <?php $orderProductIds = explode(',', $order['purchased_products']); ?>
                    <?php foreach ($orderProductIds as $orderProductId): ?>
                        <?php $orderProduct = getSingleProduct($orderProductId); ?>
                        <?php $productsPrices = json_decode($order['products_prices'], true); ?>
                        <div class="product-item">
                            <div class="product-image order-image">
                                <img src="./images/<?= $orderProduct['image_url']; ?>"
                                     alt="<?= translate('product_image'); ?>">
                            </div>
                            <div class="product-features">
                                <div><?= $orderProduct['title']; ?></div>
                                <div><?= $productsPrices[$orderProduct['id']]; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="total-price"><?= translate('order_total') . ': ' . $order['total_price']; ?></div>
                </div>
            </div>
            <hr>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once './view/footer.view.php'; ?>
