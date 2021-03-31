<?php

require_once './common.php';

if (isset($_POST['action']) == 'logout') {
    unset($_SESSION['login_user']);
}

if (!isset($_SESSION['login_user'])) {
    header('Location: ./index.php');
    die();
}

if (isset($_POST['id'])) {
    $sql = 'DELETE FROM products WHERE product_id = ?;';
    $stmt = connection()->prepare($sql);
    $stmt->execute([$_POST['id']]);

    if (in_array($_POST['id'], $_SESSION['id'])) {
        removeItemFromCart();
    }
    header('Location: ./products.php');
    die();
}

$allProducts = getAllProducts();

?>

<?php require_once './view/header.view.php'; ?>

<div class="content-wrapper">
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
            <a href="./product.php?id=<?= $product['product_id']; ?>"><?= translate('edit'); ?></a>
            <form action="./products.php" method="post">
                <input type="hidden" name="id" value="<?= $product['product_id']; ?>">
                <input type="submit" value="<?= translate('delete'); ?>">
            </form>
        </div>
        <br>
    <?php endforeach; ?>
    <div class="actions">
        <a href="./product.php"><?= translate('add'); ?></a>
        <form action="./products.php" method="post">
            <input type="hidden" name="action" value="logout">
            <input type="submit" value="<?= translate('logout'); ?>">
        </form>
    </div>
</div>

<?php require_once './view/footer.view.php'; ?>
