<?php

require_once './common.php';

if (!count($_SESSION['id'])) {
    $products = getAllProducts();
} else {
    $cartIds = $_SESSION['id'];
    $inQuery = implode(',', array_fill(0, count($cartIds), '?'));
    $sql = 'SELECT * FROM products WHERE product_id NOT IN (' . $inQuery . ');';
    $stmt = connection()->prepare($sql);
    $stmt->execute($cartIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<?php require_once './view/header.view.php'; ?>

<div class="content-wrapper">
    <?php if (count($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="product-item">
                <div class="product-image">
                    <img src="./images/<?= $product['image_url']; ?>" alt="<?= translate('product_image'); ?>">
                </div>
                <div class="product-features">
                    <div><?= $product['title']; ?></div>
                    <div><?= $product['description']; ?></div>
                    <div><?= $product['price']; ?></div>
                </div>
                <form action="./cart.php" method="post">
                    <input type="hidden" name="id" value="<?= $product['product_id']; ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="submit" value="<?= translate('add'); ?>">
                </form>
            </div>
            <br>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="message"><?= translate('all_added'); ?></p>
    <?php endif; ?>
    <a class="go" href="./cart.php"><?= translate('go_to_cart'); ?></a>
</div>

<?php require_once './view/footer.view.php'; ?>


