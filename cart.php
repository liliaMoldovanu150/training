<?php

require_once './common.php';

if (isset($_POST['id']) && $_POST['action'] === 'remove') {
    removeItemFromCart();
}

if (isset($_POST['id']) && $_POST['action'] === 'add' && count(getSingleProduct($_POST['id']))) {
    array_push($_SESSION['id'], $_POST['id']);
    header('Location: ./index.php');
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

$validation = [
    'name' => '',
    'details' => '',
    'comment' => '',
    'nameErr' => '',
    'detailsErr' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'checkout') {
    if (empty($_POST['name'])) {
        $validation['nameErr'] = translate('name_required');
    } else {
        $validation['name'] = strip_tags($_POST['name']);
    }

    if (empty($_POST['details'])) {
        $validation['detailsErr'] = translate('details_required');
    } else {
        $validation['details'] = strip_tags($_POST['details']);
    }

    if (!empty($_POST['comment'])) {
        $validation['comment'] = strip_tags($_POST['comment']);
    }
}

$totalPrice = 0;
$email = null;
$prices = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && $_POST['action'] === 'checkout'
    && !$validation['nameErr']
    && !$validation['detailsErr']
) {
    $productNumber = 0;
    $orderTotal = 0;

    $message = '<html><body>';
    $message .= '<table>';
    $message .= '<tr><td colspan="5">' . translate('customer_name') . ': ' . $validation['name'] . '</td></tr>';
    $message .= '<tr><td colspan="5">'
        . translate('contact_details') . ': '
        . $validation['details'] . '</td></tr>';
    $message .= '<tr><td colspan="5">' . translate('comments') . ': ' . $validation['comment'] . '</td></tr>';

    foreach ($cartProducts as $cartProduct) {
        $prices[$cartProduct['product_id']] = $cartProduct['price'];
        $orderTotal += $cartProduct['price'];
        $message .= '<tr><td>' . ++$productNumber . '</td>';
        $message .= '<td><img src="' . $_SERVER['HTTP_ORIGIN'] . '/images/' . $cartProduct['image_url'] . '" ';
        $message .= 'alt="' . translate('product_image') . '"></td>';
        $message .= '<td>' . $cartProduct['title'] . '</td>';
        $message .= '<td>' . $cartProduct['description'] . '</td>';
        $message .= '<td>' . $cartProduct['price'] . '</td>';
        $message .= '</tr>';
    }

    $message .= '<tr><td colspan="5">' . translate('total_price') . ': ' . $orderTotal . '</td></tr>';
    $message .= '</table>';
    $message .= '</body></html>';

    $headers = [
        'From' => 'example@example.com',
        'Content-type' => 'text/html; charset=iso-8859-1'
    ];

    $email = mail(MANAGER_EMAIL, translate('order'), $message, $headers);

    $customerDetails = translate('name') . ': ' . $validation['name'] . '; '
        . translate('contact_details') . ': ' . $validation['details'] . '; '
        . translate('comments') . ': ' . $validation['comment'];
    $productsPrices = json_encode($prices);
    $queryValues = [date('Y:m:d'), $customerDetails, $productsPrices, $orderTotal];
    $sql = 'INSERT INTO orders
                    (creation_date,
                    customer_details,
                    products_prices,
                    total_price)
                    VALUES (?, ?, ?, ?);';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($queryValues);
    $orderId = $pdo->lastInsertId();
    $purchasedProducts = $_SESSION['id'];

    foreach ($purchasedProducts as $purchasedProduct) {
        $stmt = $pdo->prepare('INSERT INTO order_products VALUES (?, ?);');
        $stmt->execute([$orderId, $purchasedProduct]);
    }

    $_SESSION['id'] = [];
    header('Location: ./index.php');
}

?>

<?php require_once './view/header.view.php'; ?>

<div class="content-wrapper">
    <?php if (count($cartProducts)): ?>
        <?php foreach ($cartProducts as $cartProduct): ?>
            <?php $totalPrice += $cartProduct['price']; ?>
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
        <div class="total-price"><?= translate('total_price') . ': ' . $totalPrice; ?></div>
    <?php else: ?>
        <p class="message"><?= translate('empty_cart'); ?></p>
    <?php endif; ?>
    <form class="cart-form" action="./cart.php" method="post">
        <input type="hidden" name="action" value="checkout">
        <input
                type="text"
                name="name"
                value="<?= isset($_POST['name']) && !$email ? $_POST['name'] : ''; ?>"
                placeholder="<?= translate('name'); ?>"
        >
        <span class="error"><?= $validation['nameErr']; ?></span>
        <br><br>
        <input
                type="text"
                name="details"
                value="<?= isset($_POST['details']) && !$email ? $_POST['details'] : ''; ?>"
                placeholder="<?= translate('contact_details'); ?>"
        >
        <span class="error"><?= $validation['detailsErr']; ?></span>
        <br><br>
        <textarea
                rows="4"
                name="comment"
                placeholder="<?= translate('comments'); ?>"><?= isset($_POST['comment']) && !$email
                ? $_POST['comment']
                : ''; ?></textarea>
        <br><br>
        <input type="submit" value="<?= translate('checkout'); ?>">
    </form>
    <a class="go" href="./index.php"><?= translate('go_to_index'); ?></a>
</div>

<?php require_once './view/footer.view.php'; ?>
