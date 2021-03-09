<?php

session_start();

require_once './config.php';

if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = array();
}

function connection()
{
    try {
        $pdo = new PDO('mysql:host=' . servname . ';dbname=' . dbname, username, password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        throw $e;
    }
}

function getAllProducts(): array
{
    try {
        $sql = 'SELECT * FROM products';
        $stmt = connection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw $e;
    }
}

function getAvailableProducts(): array
{
    try {
        $cartIds = $_SESSION['id'];
        $inQuery = implode(',', array_fill(0, count($cartIds), '?'));
        $sql = 'SELECT * FROM products WHERE id NOT IN (' . $inQuery . ');';
        $stmt = connection()->prepare($sql);
        foreach ($cartIds as $k => $id) {
            $stmt->bindValue(($k + 1), $id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw $e;
    }
}

function productExists($productId): array
{
    try {
        $sql = 'SELECT * FROM products WHERE id=?;';
        $stmt = connection()->prepare($sql);
        $stmt->bindParam(1, $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw $e;
    }
}

function getCartProducts(): array
{
    $cartIds = $_SESSION['id'];

    if (!count($cartIds)) {
        return [];
    }

    try {
        $inQuery = implode(',', array_fill(0, count($cartIds), '?'));
        $sql = 'SELECT * FROM products WHERE id IN (' . $inQuery . ');';
        $stmt = connection()->prepare($sql);
        foreach ($cartIds as $k => $id) {
            $stmt->bindValue(($k + 1), $id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw $e;
    }
}

function addItemToCart(): array
{
    if (count(productExists($_GET['id']))) {
        array_push($_SESSION['id'], $_GET['id']);
    }
    return $_SESSION['id'];
}

function removeItemFromCart(): array
{
    $key = array_search($_GET['id'], $_SESSION['id']);
    unset($_SESSION['id'][$key]);
    $_SESSION['id'] = array_values($_SESSION['id']);
    return $_SESSION['id'];
}

function removeAllItemsFromCart(): array
{
    $_SESSION['id'] = [];
    return $_SESSION['id'];
}

function translate($label): string
{
    require_once './translations.php';
    return translations[$label];
}

function deleteItemFromDB()
{
    try {
        $sql = 'DELETE FROM products WHERE id=?;';
        $stmt = connection()->prepare($sql);
        $stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

function uploadImage()
{
    try {
        if (
            !isset($_FILES['image']['error']) ||
            is_array($_FILES['image']['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }

        switch ($_FILES['image']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }

        if ($_FILES['image']['size'] > 1000000) {
            throw new RuntimeException('Exceeded filesize limit.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        if (false === $ext = array_search(
                $finfo->file($_FILES['image']['tmp_name']),
                array(
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                ),
                true
            )) {
            throw new RuntimeException('Invalid file format.');
        }

        if (!move_uploaded_file(
            $_FILES['image']['tmp_name'],
            sprintf('./images/%s.%s',
                sha1_file($_FILES['image']['tmp_name']),
                $ext
            )
        )) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        return true;

    } catch (RuntimeException $e) {
        throw $e;
    }
}

function addItemToDB($title, $description, $price, $imageUrl)
{
    try {
        $sql = 'INSERT INTO products (title, description, price, image_url) VALUES (?, ?, ?, ?);';
        $stmt = connection()->prepare($sql);
        $stmt->bindParam(1, $title, PDO::PARAM_STR, 20);
        $stmt->bindParam(2, $description, PDO::PARAM_STR, 250);
        $stmt->bindParam(3, $price, PDO::PARAM_INT);
        $stmt->bindParam(4, $imageUrl, PDO::PARAM_STR, 100);
        $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

function updateItemIncludingImage($title, $description, $price, $imageUrl)
{
    try {
        $sql = 'UPDATE products SET title=?, description=?, price=?, image_url=? WHERE id='
            . $_SESSION['editProductId']
            . ';';
        $stmt = connection()->prepare($sql);
        $stmt->bindParam(1, $title, PDO::PARAM_STR, 20);
        $stmt->bindParam(2, $description, PDO::PARAM_STR, 250);
        $stmt->bindParam(3, $price, PDO::PARAM_INT);
        $stmt->bindParam(4, $imageUrl, PDO::PARAM_STR, 100);
        $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

function updateItemExceptImage($title, $description, $price)
{
    try {
        $sql = 'UPDATE products SET title=?, description=?, price=? WHERE id='
            . $_SESSION['editProductId']
            . ';';
        $stmt = connection()->prepare($sql);
        $stmt->bindParam(1, $title, PDO::PARAM_STR, 20);
        $stmt->bindParam(2, $description, PDO::PARAM_STR, 250);
        $stmt->bindParam(3, $price, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}



