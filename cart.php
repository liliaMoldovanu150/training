<?php

require_once './common.php';

if (isset($_POST['id']) && $_POST['action'] === 'remove') {
    removeItemFromCart();
}

if (isset($_POST['id']) && $_POST['action'] === 'add' && count(getSingleProduct($_POST['id']))) {
    array_push($_SESSION['id'], $_POST['id']);
    header('Location: ./index.php');
    die();
}

$pdo = connection();

if (!count($_SESSION['id'])) {
    $cartProducts = [];
} else {
    $inQuery = implode(',', array_fill(0, count($_SESSION['id']), '?'));
    $sql = 'SELECT * FROM products WHERE product_id IN (' . $inQuery . ');';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($_SESSION['id']);
    $cartProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$name = $details = $comment = '';

$validation = [
    'nameErr' => '',
    'detailsErr' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'checkout') {
    if (empty($_POST['name'])) {
        $validation['nameErr'] = translate('name_required');
    } else {
        $name = strip_tags($_POST['name']);
    }

    if (empty($_POST['details'])) {
        $validation['detailsErr'] = translate('details_required');
    } else {
        $details = strip_tags($_POST['details']);
    }

    if (!empty($_POST['comment'])) {
        $comment = strip_tags($_POST['comment']);
    }
}

$totalPrice = 0;
foreach ($cartProducts as $cartProduct) {
    $totalPrice += $cartProduct['price'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'checkout' && !array_filter($validation)) {

    ob_start();
    include('email_template.php');
    $message = ob_get_contents();
    ob_end_clean();

    $headers = [
        'From' => 'example@example.com',
        'MIME-Version' => '1.0',
        'Content-type' => 'text/html; charset=iso-8859-1'
    ];

    $email = mail(MANAGER_EMAIL, translate('order'), $message, $headers);

    $queryValues = [date('Y-m-d H:i:s'), $name, $details, $comment, $totalPrice];
    $sql = 'INSERT INTO orders
                    (creation_date,
                    customer_name,
                    contact_details,
                    comments,
                    total_price)
                    VALUES (?, ?, ?, ?, ?);';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($queryValues);
    $orderId = $pdo->lastInsertId();
    $purchasedProducts = $_SESSION['id'];

    foreach ($cartProducts as $cartProduct) {
        $stmt = $pdo->prepare('INSERT INTO order_products VALUES (?, ?, ?);');
        $stmt->execute([$orderId, $cartProduct['product_id'], $cartProduct['price']]);
    }

    $_SESSION['id'] = [];
    header('Location: ./index.php');
    die();
}

?>

<?php require_once './view/header.view.php'; ?>

<div class="content-wrapper">
    <?php if (count($cartProducts)): ?>
        <?php foreach ($cartProducts as $cartProduct): ?>
            <div class="product-item">
                <div class="product-image">
                    <img src="./images/<?= $cartProduct['image_url']; ?>" alt="<?= translate('product_image'); ?>">
                </div>
                <div class="product-features">
                    <div><?= $cartProduct['title']; ?></div>
                    <div><?= $cartProduct['description']; ?></div>
                    <div><?= $cartProduct['price']; ?></div>
                </div>
                <form action="./cart.php" method="post">
                    <input type="hidden" name="id" value="<?= $cartProduct['product_id']; ?>">
                    <input type="hidden" name="action" value="remove">
                    <input type="submit" value="<?= translate('remove'); ?>">
                </form>
            </div>
            <br>
        <?php endforeach; ?>
        <div class="total-price"><?= translate('total_price'); ?>: <?= $totalPrice; ?></div>
    <?php else: ?>
        <p class="message"><?= translate('empty_cart'); ?></p>
    <?php endif; ?>
    <form class="cart-form" action="./cart.php" method="post">
        <input type="hidden" name="action" value="checkout">
        <input
                type="text"
                name="name"
                value="<?= $name; ?>"
                placeholder="<?= translate('name'); ?>"
        >
        <?php if ($validation['nameErr']): ?>
            <span class="error"><?= $validation['nameErr']; ?></span>
        <?php endif; ?>
        <br><br>
        <input
                type="text"
                name="details"
                value="<?= $details; ?>"
                placeholder="<?= translate('contact_details'); ?>"
        >
        <?php if ($validation['detailsErr']): ?>
            <span class="error"><?= $validation['detailsErr']; ?></span>
        <?php endif; ?>
        <br><br>
        <textarea
                rows="4"
                name="comment"
                placeholder="<?= translate('comments'); ?>"><?= $comment; ?></textarea>
        <br><br>
        <input type="submit" value="<?= translate('checkout'); ?>">
    </form>
    <a class="go" href="./index.php"><?= translate('go_to_index'); ?></a>
</div>

<?php require_once './view/footer.view.php'; ?>
