<?php

require_once 'common.php';

if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = array();
}

if (!empty($_GET['id'])) {
    array_push($_SESSION['id'], $_GET['id']);
    header('Location: index.php');
}

try {
    $cartIds = $_SESSION['id'];
    $inQuery = implode(',', array_fill(0, count($cartIds), '?'));
    $condition = empty($_SESSION['id']) ? '' : ' WHERE id NOT IN(' . $inQuery . ');';
    $sql = 'SELECT * FROM products' . $condition;
    $stmt = $GLOBALS['pdo']->prepare($sql);
    foreach ($cartIds as $k => $id) {
        $stmt->bindValue(($k+1), $id);
    }
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Could not fetch products.' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style.css">
        <title>Products</title>
    </head>
    <body>
        <div class="container">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <div class="product-image">
                            <img src="<?= $product['image_url']; ?>" alt="product-image">
                        </div>
                        <div class="product-features">
                            <div><?= $product['title']; ?></div>
                            <div><?= $product['description']; ?></div>
                            <div><?= $product['price']; ?></div>
                        </div>
                        <a href="index.php?id=<?= $product['id']; ?>">Add</a>
                    </div>
                    <br>
                <?php endforeach; ?>
            <?php else: ?>
                <p>All products were added to cart.</p>
            <?php endif; ?>
            <a class="to-cart" href="cart.php">Go to cart</a>
        </div>
    </body>
</html>



