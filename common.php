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

function deleteItem()
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


