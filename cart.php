<?php

require_once './common.php';

$cartProducts = getCartProducts();

if (isset($_GET['id'])) {
    removeItemFromCart();
    header('Location: ./cart.php');
}

$nameErr = $detailsErr = "";
$name = $details = $comment = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = strip_tags($_POST["name"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["details"])) {
        $detailsErr = "Contact details are required";
    } else {
        $details = strip_tags($_POST["details"]);
    }

    if (empty($_POST["comment"])) {
        $comment = "";
    } else {
        $comment = strip_tags($_POST["comment"]);
    }
}

$totalPrice = 0;
$email = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$nameErr && !$detailsErr) {
    ob_start(); ?>
    <html lang="en">
    <head>
        <title>Order</title>
    </head>
    <body>
    <p>Customer Name: <?= $name; ?></p>
    <p>Contact details: <?= $details; ?></p>
    <p>Comments: <?= $comment; ?></p>
    <?php if (count($cartProducts)): ?>
        <?php foreach ($cartProducts as $cartProduct): ?>
            <?php $totalPrice += $cartProduct['price']; ?>
            <div class="product-item">
                <div class="product-image">
                    <img
                            src="<?= $_SERVER['HTTP_ORIGIN'] . '/images/' . $cartProduct['image_url']; ?>"
                            alt="product-image"
                    >
                </div>
                <div class="product-features">
                    <div><?= $cartProduct['title']; ?></div>
                    <div><?= $cartProduct['description']; ?></div>
                    <div><?= $cartProduct['price']; ?></div>
                </div>
            </div>
            <br>
        <?php endforeach; ?>
        <div class="total-price">Total Price: <?= $totalPrice; ?></div>
    <?php endif; ?>
    </body>
    </html>
    <?php $emailMessage = ob_get_contents();
    ob_end_clean();

    $headers = 'From: example@example.com' . "\r\n" .
        'Content-type: text/html; charset=iso-8859-1';

    $email = mail(managerEmail, 'Order', $emailMessage, $headers);

    if ($email) {
        removeAllItemsFromCart();
        header('Location: ./index.php');
    }
}
?>

<?php require_once './view/header.view.php'; ?>

<?php if (count($cartProducts)): ?>
    <?php foreach ($cartProducts as $cartProduct): ?>
        <?php $totalPrice += $cartProduct['price']; ?>
        <div class="product-item">
            <div class="product-image">
                <img src="./images/<?= $cartProduct['image_url']; ?>" alt="product-image">
            </div>
            <div class="product-features">
                <div><?= $cartProduct['title']; ?></div>
                <div><?= $cartProduct['description']; ?></div>
                <div><?= $cartProduct['price']; ?></div>
            </div>
            <a href="./cart.php?id=<?= $cartProduct['id']; ?>"><?= translate('remove'); ?></a>
        </div>
        <br>
    <?php endforeach; ?>
    <div class="total-price">Total Price: <?= $totalPrice; ?></div>
<?php else: ?>
    <p class="message">Your cart is empty.</p>
<?php endif; ?>
<form action="./cart.php" method="post">
    <input
            type="text"
            name="name"
            value="<?= isset($_POST['name']) && !$email ? $_POST['name'] : ''; ?>"
            placeholder="Name"
    >
    <span class="error"><?= $nameErr; ?></span>
    <br><br>
    <input
            type="text"
            name="details"
            value="<?= isset($_POST['details']) && !$email ? $_POST['details'] : ''; ?>"
            placeholder="Contact details"
    >
    <span class="error"><?= $detailsErr; ?></span>
    <br><br>
    <textarea
            rows="4"
            name="comment"
            placeholder="Comments"><?= isset($_POST['comment']) && !$email ? $_POST['comment'] : ''; ?>
        </textarea>
    <br><br>
    <input type="submit" value="<?= translate('checkout'); ?>">
</form>
<a href="./index.php"><?= translate('go_to_index'); ?></a>

<?php require_once './view/footer.view.php'; ?>
