<?php

require_once './common.php';

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
            <a href="./product.php?id=<?= $product['id']; ?>"><?= translate('edit'); ?></a>
            <a href="./index.php?id=<?= $product['id']; ?>"><?= translate('delete'); ?></a>
        </div>
        <br>
    <?php endforeach; ?>
<?php endif; ?>
<a href="./product.php"><?= translate('add'); ?></a>
<a href="./products.php"><?= translate('logout'); ?></a>

<?php require_once './view/footer.view.php'; ?>
