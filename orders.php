<?php

require_once './common.php';

try {
    $sql = 'SELECT * FROM orders';
    $stmt = connection()->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    throw $e;
}

?>

<?php require_once './view/header.view.php'; ?>

<?php if (count($orders)): ?>
    <div class="orders-wrapper">
        <h1 class="order-heading">Orders</h1>
        <?php foreach ($orders as $order): ?>
            <div class="order">
                <div class="order-details">
                    <div>ID: <?= $order['id']; ?></div>
                    <div>Date: <?= $order['creation_date']; ?></div>
                    <div><?= $order['customer_details']; ?></div>
                </div>
                <div class="order-products">
                    <?php $orderProductIds = explode(',', $order['purchased_products']); ?>
                    <?php foreach ($orderProductIds as $orderProductId): ?>
                        <?php $orderProduct = productExists($orderProductId)[0]; ?>
                        <?php $productsPrices = json_decode($order['products_prices'], true); ?>
                        <div class="product-item">
                            <div class="product-image order-image">
                                <img src="./images/<?= $orderProduct['image_url']; ?>"
                                     alt="<?= translate('product_image'); ?>">
                            </div>
                            <div class="product-features">
                                <div><?= ucfirst($orderProduct['title']); ?></div>
                                <div><?= $productsPrices[$orderProduct['id']]; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="total-price">Order Total: <?= $order['total_price']; ?></div>
                </div>
            </div>
            <hr>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once './view/footer.view.php'; ?>
