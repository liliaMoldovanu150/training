<?php

require_once './common.php';

if (isset($_POST['action']) == 'logout') {
    unset($_SESSION['login_user']);
}

if (!isset($_SESSION['login_user'])) {
    header('Location: ./index.php');
}

if (isset($_POST['id'])) {
    $sql = 'DELETE FROM products WHERE product_id=?;';
    $stmt = connection()->prepare($sql);
    $stmt->execute([$_POST['id']]);

    if (array_search($_POST['id'], $_SESSION['id']) >= 0) {
        removeItemFromCart();
    }
    header('Location: ./products.php');
}

$allProducts = getAllProducts();

?>

<?php require_once './view/header.view.php'; ?>

<div class="content-wrapper">
    <?php if (count($allProducts)): ?>
        <?php foreach ($allProducts as $product): ?>
            <div class="product-item">
                <div class="product-image">
                    <img src="./images/<?= $product['image_url']; ?>" alt="<?= translate('product_image'); ?>">
                </div>
                <div class="product-features">
                    <div><?= $product['title']; ?></div>
                    <div><?= $product['description']; ?></div>
                    <div><?= $product['price']; ?></div>
                </div>
                <form action="./product.php" method="post">
                    <input type="hidden" name="id" value="<?= $product['product_id']; ?>">
                    <input type="submit" value="<?= translate('edit'); ?>">
                </form>
                <form action="./products.php" method="post">
                    <input type="hidden" name="id" value="<?= $product['product_id']; ?>">
                    <input type="submit" value="<?= translate('delete'); ?>">
                </form>
            </div>
            <br>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="actions">
        <form action="./product.php" method="post">
            <input type="submit" value="<?= translate('add'); ?>">
        </form>
        <form action="./products.php" method="post">
            <input type="hidden" name="action" value="logout">
            <input type="submit" value="<?= translate('logout'); ?>">
        </form>
    </div>
</div>

<?php require_once './view/footer.view.php'; ?>
