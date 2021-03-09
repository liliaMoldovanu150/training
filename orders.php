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
    <div class="order">
        <?php foreach ($orders as $order): ?>

            <div class="order-details">
                <div><?= $order['id']; ?></div>
                <div><?= $order['creation_date']; ?></div>
                <div><?= $order['customer_details']; ?></div>
            </div>
            <div class="order-products">
            <?php $orderProductIds = explode(',', $order['purchased_products']); ?>
            <?php foreach ($orderProductIds as $orderProductId): ?>
                <?php $orderProduct = productExists($orderProductId)[0]; ?>
                <div class="product-item">
                    <div class="product-image">
                        <img src="./images/<?= $orderProduct['image_url']; ?>" alt="<?= translate('product_image'); ?>">
                    </div>
                    <div class="product-features">
                        <div><?= $orderProduct['title']; ?></div>
                        <div><?= $orderProduct['description']; ?></div>
                        <div><?= $orderProduct['price']; ?></div>
                    </div>
                </div>
                <br>
                </div>


            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once './view/footer.view.php'; ?>
