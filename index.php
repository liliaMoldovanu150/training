<?php

require_once './common.php';

if (!count($_SESSION['id'])) {
    $products = getAllProducts();
} else {
    $products = getAvailableProducts();
}

if (isset($_GET['id'])) {
    addItemToCart();
    header('Location: ./index.php');
}

?>

<?php require_once './view/header.view.php'; ?>

<?php if (count($products)): ?>
    <?php foreach ($products as $product): ?>
        <div class="product-item">
            <div class="product-image">
                <img src="./images/<?= $product['image_url']; ?>" alt="product-image">
            </div>
            <div class="product-features">
                <div><?= $product['title']; ?></div>
                <div><?= $product['description']; ?></div>
                <div><?= $product['price']; ?></div>
            </div>
            <a href="./index.php?id=<?= $product['id']; ?>"><?= translate('add'); ?></a>
        </div>
        <br>
    <?php endforeach; ?>
<?php else: ?>
    <p class="message">All products were added to cart.</p>
<?php endif; ?>
<a class="to-cart" href="./cart.php"><?= translate('go_to_cart'); ?></a>

<?php require_once './view/footer.view.php'; ?>


