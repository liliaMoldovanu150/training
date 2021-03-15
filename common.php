<?php

session_start();

require_once './config.php';

if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = [];
}

function connection()
{
    $pdo = new PDO('mysql:host=' . SERV_NAME . ';dbname=' . DBNAME, USERNAME, PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

function getAllProducts(): array
{
    $sql = 'SELECT * FROM products';
    $stmt = connection()->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSingleProduct($productId): array
{
    $sql = 'SELECT * FROM products WHERE product_id=?;';
    $stmt = connection()->prepare($sql);
    $stmt->execute([$productId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
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
    if (
        !isset($_FILES['image']['error']) ||
        is_array($_FILES['image']['error'])
    ) {
        throw new RuntimeException(translate('invalid_parameters'));
    }

    switch ($_FILES['image']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException(translate('no_file'));
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException(translate('exceeded_limit'));
        default:
            throw new RuntimeException(translate('unknown_errors'));
    }

    if ($_FILES['image']['size'] > 1000000) {
        throw new RuntimeException(translate('exceeded_limit'));
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
        throw new RuntimeException(translate('invalid_file_format'));
    }

    if (!move_uploaded_file(
        $_FILES['image']['tmp_name'],
        sprintf('./images/%s.%s',
            sha1_file($_FILES['image']['tmp_name']),
            $ext
        )
    )) {
        throw new RuntimeException(translate('failed_to_move_file'));
    }
    return true;
}




