<?php

require_once './common.php';

if (isset($_GET['action']) == 'logout') {
    unset($_SESSION['login_user']);
}

if (!isset($_SESSION['login_user'])) {
    header('Location: ./index.php');
}

if (isset($_GET['id'])) {
    deleteItem();
    if (array_search($_GET['id'], $_SESSION['id']) >= 0) {
        removeItemFromCart();
    }
    header('Location: ./products.php');
}

$allProducts = getAllProducts();

?>

<?php require_once './view/header.view.php'; ?>

<?php if (count($allProducts)): ?>
    <?php foreach ($allProducts as $product): ?>
        <div class="product-item">
            <div class="product-image">
                <img src="./images/<?= $product['image_url']; ?>" alt="product-image">
            </div>
            <div class="product-features">
                <div><?= $product['title']; ?></div>
                <div><?= $product['description']; ?></div>
                <div><?= $product['price']; ?></div>
            </div>
            <a class="edit" href="./product.php?id=<?= $product['id']; ?>"><?= translate('edit'); ?></a>
            <a href="./products.php?id=<?= $product['id']; ?>"><?= translate('delete'); ?></a>
        </div>
        <br>
    <?php endforeach; ?>
<?php endif; ?>
<div class="actions">
    <a href="./product.php"><?= translate('add'); ?></a>
    <a href="./products.php?action=logout"><?= translate('logout'); ?></a>
</div>

<?php require_once './view/footer.view.php'; ?>
