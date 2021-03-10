<?php

require_once './common.php';

if (!isset($_SESSION['login_user'])) {
    header('Location: ./index.php');
}

if (isset($_POST['id'])) {
    $_SESSION['editProductId'] = $_POST['id'];
    $editProduct = productExists($_POST['id'])[0];
}

$titleErr = $descriptionErr = $priceErr = "";
$title = $description = $price = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
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

if (isset($_POST['title']) && !empty($_FILES['image']['name']) && !isset($_SESSION['editProductId'])) {
    $imageUrl = sha1_file($_FILES['image']['tmp_name'])
        . '.'
        . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    if (uploadImage() && !$titleErr && !$descriptionErr && !$priceErr) {
        $queryValues = [$title, $description, $price, $imageUrl];
        try {
            $sql = 'INSERT INTO products (title, description, price, image_url) VALUES (?, ?, ?, ?);';
            $stmt = connection()->prepare($sql);
            $stmt->execute($queryValues);
        } catch (PDOException $e) {
            throw $e;
        }
        header('Location: ./products.php');
    }
}

if (isset($_POST['title']) && isset($_SESSION['editProductId']) && !$titleErr && !$descriptionErr && !$priceErr) {
    if (empty($_FILES['image']['name'])) {
        $queryValues = [$title, $description, $price];
        try {
            $sql = 'UPDATE products SET title=?, description=?, price=? WHERE id=' . $_SESSION['editProductId'] . ';';
            $stmt = connection()->prepare($sql);
            $stmt->execute($queryValues);
        } catch (PDOException $e) {
            throw $e;
        }
    } elseif (!empty($_FILES['image']['name'])) {
        $imageUrl = sha1_file($_FILES['image']['tmp_name'])
            . '.'
            . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        if (uploadImage()) {
            $queryValues = [$title, $description, $price, $imageUrl];
            try {
                $sql = 'UPDATE products SET title=?, description=?, price=?, image_url=? WHERE id='
                    . $_SESSION['editProductId']
                    . ';';
                $stmt = connection()->prepare($sql);
                $stmt->execute($queryValues);
            } catch (PDOException $e) {
                throw $e;
            }
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
        >
        <br><br>
        <input type="submit" value="<?= translate('save'); ?>">
    </form>
    <a class="go" href="./products.php"><?= translate('products'); ?></a>
</div>

<?php require_once './view/footer.view.php'; ?>
