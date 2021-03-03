<?php

require_once 'common.php';

if (empty($_SESSION['id'])) {
    $products = getAllProducts();
} else {
    $products = getAvailableProducts();
}
?>

<?php require_once 'view/header.view.php'; ?>

<?php if (!empty($products)): ?>
    <?php foreach ($products as $product): ?>
        <?php echo showProducts('add', $product); ?>
    <?php endforeach; ?>
<?php else: ?>
    <p>All products were added to cart.</p>
<?php endif; ?>
    <a class="to-cart" href="cart.php">Go to cart</a>

<?php require_once 'view/footer.view.php'; ?>


