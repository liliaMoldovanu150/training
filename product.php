<?php

require_once './common.php';

if (isset($_GET['id'])) {
    $_SESSION['editProductId'] = $_GET['id'];
    $editProduct = productExists($_GET['id'])[0];
}

$titleErr = $descriptionErr = $priceErr = "";
$title = $description = $price = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["title"])) {
        $titleErr = "Title is required";
    } else {
        $title = strip_tags($_POST["title"]);
    }

    if (empty($_POST["description"])) {
        $descriptionErr = "Description is required";
    } else {
        $description = strip_tags($_POST["description"]);
    }

    if (empty($_POST["price"])) {
        $priceErr = "Price is required";
    } else {
        $price = strip_tags($_POST["price"]);
    }
}

if (isset($_POST) && isset($_FILES['image']) && !isset($_SESSION['editProductId'])) {
    $imageUrl = sha1_file($_FILES['image']['tmp_name'])
        . '.'
        . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    if (uploadImage() && !$titleErr && !$descriptionErr && !$priceErr) {
        addItemToDB($title, $description, $price, $imageUrl);
        header('Location: ./products.php');
    }
}


if (isset($_POST) && isset($_SESSION['editProductId']) && !$titleErr && !$descriptionErr && !$priceErr) {
    if (isset($_FILES['image']) && uploadImage()) {
        $imageUrl = sha1_file($_FILES['image']['tmp_name'])
            . '.'
            . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        updateItem($title, $description, $price, $imageUrl);
    }
    unset($_SESSION['editProductId']);
//        header('Location: ./products.php');

}

?>

<?php require_once './view/header.view.php'; ?>

<form action="./product.php" method="post" enctype="multipart/form-data">
    <input
            type="text"
            name="title"
            value="<?= $editProduct['title'] ?? ''; ?>"
            placeholder="Title"
    >
    <span class="error"><?= $titleErr; ?></span>
    <br><br>
    <input
            type="text"
            name="description"
            value="<?= $editProduct['description'] ?? ''; ?>"
            placeholder="Description"
    >
    <span class="error"><?= $descriptionErr; ?></span>
    <br><br>
    <input
            type="number"
            name="price"
            min="0.00"
            step="0.01"
            value="<?= $editProduct['price'] ?? ''; ?>"
            placeholder="Price"
    >
    <span class="error"><?= $priceErr; ?></span>
    <br><br>
    <input
            type="file"
            name="image"
            placeholder="Image"
    >
    <br><br>
    <input type="submit" value="<?= translate('save'); ?>">
</form>
<a href="./products.php"><?= translate('products'); ?></a>

<?php require_once './view/footer.view.php'; ?>
