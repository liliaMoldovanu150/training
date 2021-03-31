<?php

require_once './common.php';

if (!isset($_SESSION['login_user'])) {
    header('Location: ./index.php');
    die();
}

if (isset($_GET['id'])) {
    $_SESSION['editProductId'] = $_GET['id'];
    $editProduct = getSingleProduct($_GET['id']);
}

$pdo = connection();

$title = $description = $price = '';

$validation = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    if (empty($_POST['title'])) {
        $validation['titleErr'] = translate('title_required');
    } else {
        $title = strip_tags($_POST['title']);
    }

    if (empty($_POST['description'])) {
        $validation['descriptionErr'] = translate('description_required');
    } else {
        $description = strip_tags($_POST['description']);
    }

    if (empty($_POST['price'])) {
        $validation['priceErr'] = translate('price_required');
    } else {
        $price = strip_tags($_POST['price']);
    }
}

$editMode = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['editProductId']);
$addMode = $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['editProductId']);

$imageUrl = null;

if (!empty($_FILES['image']['name'])) {
    $imageUrl = sha1_file($_FILES['image']['tmp_name'])
        . '.'
        . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
}

if ($addMode && !$imageUrl) {
    $validation['imageErr'] = translate('image_required');
}

if ($addMode && !array_filter($validation) && uploadImage()) {
    $queryValues = [$title, $description, $price, $imageUrl];
    $sql = 'INSERT INTO products (title, description, price, image_url) VALUES (?, ?, ?, ?);';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($queryValues);
    header('Location: ./products.php');
    die();
}

if ($editMode && !array_filter($validation) && !$imageUrl) {
    $queryValues = [$title, $description, $price];
    $sql = 'UPDATE products SET title = ?, description = ?, price = ? WHERE product_id = '
        . $_SESSION['editProductId']
        . ';';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($queryValues);
    unset($_SESSION['editProductId']);
    header('Location: ./products.php');
    die();
}

if ($editMode && !array_filter($validation) && uploadImage()) {
    $queryValues = [$title, $description, $price, $imageUrl];
    $sql = 'UPDATE products SET title = ?, description = ?, price = ?, image_url = ? WHERE product_id = '
        . $_SESSION['editProductId']
        . ';';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($queryValues);
    unset($_SESSION['editProductId']);
    header('Location: ./products.php');
    die();
}

?>

<?php require_once './view/header.view.php'; ?>

<div class="content-wrapper">
    <form action="./product.php" method="post" enctype="multipart/form-data">
        <input
                type="text"
                name="title"
                value="<?= $editProduct['title'] ?? $title; ?>"
                placeholder="<?= translate('title'); ?>"
        >
        <?php if (isset($validation['titleErr'])): ?>
            <span class="error"><?= $validation['titleErr']; ?></span>
        <?php endif; ?>
        <br><br>
        <input
                type="text"
                name="description"
                value="<?= $editProduct['description'] ?? $description; ?>"
                placeholder="<?= translate('description'); ?>"
        >
        <?php if (isset($validation['descriptionErr'])): ?>
            <span class="error"><?= $validation['descriptionErr']; ?></span>
        <?php endif; ?>
        <br><br>
        <input
                type="number"
                name="price"
                min="0.00"
                step="0.01"
                value="<?= $editProduct['price'] ?? $price; ?>"
                placeholder="<?= translate('price'); ?>"
        >
        <?php if (isset($validation['priceErr'])): ?>
            <span class="error"><?= $validation['priceErr']; ?></span>
        <?php endif; ?>
        <br><br>
        <input
                type="file"
                name="image"
        >
        <?php if (isset($validation['imageErr'])): ?>
            <span class="error"><?= $validation['imageErr']; ?></span>
        <?php endif; ?>
        <br><br>
        <input type="submit" value="<?= translate('save'); ?>">
    </form>
    <a class="go" href="./products.php"><?= translate('products'); ?></a>
</div>

<?php require_once './view/footer.view.php'; ?>
