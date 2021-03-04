<?php

require_once './common.php';

$cartProducts = getCartProducts();

if (isset($_GET['id'])) {
    $key = array_search($_GET['id'], $_SESSION['id']);
    unset($_SESSION['id'][$key]);
    $_SESSION['id'] = array_values($_SESSION['id']);
    header('Location: cart.php');
}

?>

<?php require_once './view/header.view.php'; ?>

<?php if (count($cartProducts)): ?>
    <?php foreach ($cartProducts as $cartProduct): ?>
        <div class="product-item">
            <div class="product-image">
                <img src="<?= $cartProduct['image_url']; ?>" alt="product-image">
            </div>
            <div class="product-features">
                <div><?= $cartProduct['title']; ?></div>
                <div><?= $cartProduct['description']; ?></div>
                <div><?= $cartProduct['price']; ?></div>
            </div>
            <a href="cart.php?id=<?= $cartProduct['id']; ?>"><?= translate('remove'); ?></a>
        </div>
        <br>
    <?php endforeach; ?>
<?php else: ?>
    <p>Your cart is empty.</p>
<?php endif; ?>
    <form action="cart.php" method="post">
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="details" placeholder="Contact details" required>
        <textarea name="comment" placeholder="Comments"></textarea>
        <input type="submit" value="<?= translate('checkout'); ?>">
    </form>
    <a href="index.php"><?= translate('go_to_index'); ?></a>

<?php require_once './view/footer.view.php'; ?>