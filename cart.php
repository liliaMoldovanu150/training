<?php

require_once 'common.php';

$cartProducts = getCartProducts();
?>

<?php require_once 'view/header.view.php'; ?>

<?php if (!empty($cartProducts)): ?>
    <?php foreach ($cartProducts as $cartProduct): ?>
        <?php echo showProducts('remove', $cartProduct); ?>
    <?php endforeach; ?>
<?php else: ?>
    <p>Your cart is empty.</p>
<?php endif; ?>
    <form>
        <input type="text" name="name" placeholder="Name">
        <input type="text" name="details" placeholder="Contact details">
        <textarea name="comment" placeholder="Comments"></textarea>
        <input type="submit" value="Checkout">
    </form>
    <a href="index.php">Go to Index</a>

<?php require_once 'view/footer.view.php'; ?>