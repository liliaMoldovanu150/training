<?php

session_start();

require_once './config.php';

if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = [];
}

function connection()
{
    try {
        $pdo = new PDO('mysql:host=' . SERV_NAME . ';dbname=' . DBNAME, USERNAME, PASSWORD);
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

function getCartProducts($cartIds): array
{
    if (!count($cartIds)) {
        return [];
    }

    try {
        $inQuery = implode(',', array_fill(0, count($cartIds), '?'));
        $sql = 'SELECT * FROM products WHERE id IN (' . $inQuery . ');';
        $stmt = connection()->prepare($sql);
        $stmt->execute($cartIds);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw $e;
    }
}


function removeItemFromCart(): array
{
    $key = array_search($_POST['id'], $_SESSION['id']);
    unset($_SESSION['id'][$key]);
    $_SESSION['id'] = array_values($_SESSION['id']);
    return $_SESSION['id'];
}

function translate($label): string
{
    require_once './translations.php';
    return TRANSLATIONS[$label];
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




