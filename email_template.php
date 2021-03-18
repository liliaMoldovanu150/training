<html>
<head>
    <title><?= translate('order'); ?></title>
</head>
<body>
<table>
    <tr>
        <td colspan="5"><?= translate('customer_name'); ?>: <?= $name; ?></td>
    </tr>
    <tr>
        <td colspan="5"><?= translate('contact_details'); ?>: <?= $details; ?></td>
    </tr>
    <tr>
        <td colspan="5"><?= translate('comments'); ?>: <?= $comment; ?></td>
    </tr>
    <?php foreach ($cartProducts as $cartProduct): ?>
        <tr>
            <td><img style="width: 70px"
                     src="<?= $_SERVER['HTTP_ORIGIN']; ?>/images/<?= $cartProduct['image_url']; ?>"
                     alt="<?= translate('product_image'); ?>"
            </td>
            <td><?= $cartProduct['title']; ?></td>
            <td><?= $cartProduct['description']; ?></td>
            <td><?= $cartProduct['price']; ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="5"><?= translate('total_price'); ?>: <?= $orderTotal; ?></td>
    </tr>
</table>
</body>
</html>

