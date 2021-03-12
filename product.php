<?php

require_once './common.php';

if (!isset($_SESSION['login_user'])) {
    header('Location: ./index.php');
}

if (isset($_POST['id'])) {
    $_SESSION['editProductId'] = $_POST['id'];
    $editProduct = getSingleProduct($_POST['id']);
}

$validation = [
    'title' => '',
    'description' => '',
    'price' => '',
    'titleErr' => '',
    'descriptionErr' => '',
    'priceErr' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    if (empty($_POST['title'])) {
        $validation['titleErr'] = translate('title_required');
    } else {
        $validation['title'] = strip_tags($_POST['title']);
    }

    if (empty($_POST['description'])) {
        $validation['descriptionErr'] = translate('description_required');
    } else {
        $validation['description'] = strip_tags($_POST['description']);
    }

    if (empty($_POST['price'])) {
        $validation['priceErr'] = translate('price_required');
    } else {
        $validation['price'] = strip_tags($_POST['price']);
    }
}

if (isset($_POST['title']) && !empty($_FILES['image']['name']) && !isset($_SESSION['editProductId'])) {
    $imageUrl = sha1_file($_FILES['image']['tmp_name'])
        . '.'
        . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    if (uploadImage() && !$validation['titleErr'] && !$validation['descriptionErr'] && !$validation['priceErr']) {
        $queryValues = [$validation['title'], $validation['description'], $validation['price'], $imageUrl];
        $sql = 'INSERT INTO products (title, description, price, image_url) VALUES (?, ?, ?, ?);';
        $stmt = connection()->prepare($sql);
        $stmt->execute($queryValues);
        header('Location: ./products.php');
    }
}

if (isset($_POST['title']) && isset($_SESSION['editProductId']) && !$validation['titleErr'] && !$validation['descriptionErr'] && !$validation['priceErr']) {
    if (empty($_FILES['image']['name'])) {
        $queryValues = [$validation['title'], $validation['description'], $validation['price']];
        $sql = 'UPDATE products SET title=?, description=?, price=? WHERE id=' . $_SESSION['editProductId'] . ';';
        $stmt = connection()->prepare($sql);
        $stmt->execute($queryValues);
    } elseif (!empty($_FILES['image']['name'])) {
        $imageUrl = sha1_file($_FILES['image']['tmp_name'])
            . '.'
            . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        if (uploadImage()) {
            $queryValues = [$validation['title'], $validation['description'], $validation['price'], $imageUrl];
            $sql = 'UPDATE products SET title=?, description=?, price=?, image_url=? WHERE id='
                . $_SESSION['editProductId']
                . ';';
            $stmt = connection()->prepare($sql);
            $stmt->execute($queryValues);
        }
    }
    unset($_SESSION['editProductId']);
    header('Location: ./products.php');
}

?>

<?php require_once './view/header.view.php'; ?>

<div class="content-wrapper">
    <form action="./product.php" method="post" enctype="multipart/form-data">
        <input
                type="text"
                name="title"
                value="<?= $_POST['title'] ?? ($editProduct['title'] ?? ''); ?>"
                placeholder="<?= translate('title'); ?>"
        >
        <span class="error"><?= $validation['titleErr']; ?></span>
        <br><br>
        <input
                type="text"
                name="description"
                value="<?= $_POST['description'] ?? ($editProduct['description'] ?? ''); ?>"
                placeholder="<?= translate('description'); ?>"
        >
        <span class="error"><?= $validation['descriptionErr']; ?></span>
        <br><br>
        <input
                type="number"
                name="price"
                min="0.00"
                step="0.01"
                value="<?= $_POST['price'] ?? ($editProduct['price'] ?? ''); ?>"
                placeholder="<?= translate('price'); ?>"
        >
        <span class="error"><?= $validation['priceErr']; ?></span>
        <br><br>
        <input
                type="file"
                name="image"
        >
        <br><br>
        <input type="submit" value="<?= translate('save'); ?>">
    </form>
    <a class="go" href="./products.php"><?= translate('products'); ?></a>
</div>

<?php require_once './view/footer.view.php'; ?>
