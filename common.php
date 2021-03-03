<?php

session_start();

require_once './config.php';

try {

    $GLOBALS['pdo'] = new PDO('mysql:host=' . $servername . ';dbname=' . $dbname, $username, $password);

    $GLOBALS['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Could not connect to database.' . $e->getMessage();
}

function getAllProducts()
{
    try {
        $sql = 'SELECT * FROM products';
        $stmt = $GLOBALS['pdo']->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Could not fetch products.' . $e->getMessage();
    }
}

function getAvailableProducts()
{
    try {
        $cartIds = $_SESSION['id'];
        $inQuery = implode(',', array_fill(0, count($cartIds), '?'));
        $sql = 'SELECT * FROM products WHERE id NOT IN(' . $inQuery . ');';
        $stmt = $GLOBALS['pdo']->prepare($sql);
        foreach ($cartIds as $k => $id) {
            $stmt->bindValue(($k+1), $id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Could not fetch products.' . $e->getMessage();
    }
}

function getCartProducts()
{
    try {
        $cartIds = $_SESSION['id'];
        $inQuery = implode(',', array_fill(0, count($cartIds), '?'));
        $sql = 'SELECT * FROM products WHERE id IN(' . $inQuery . ');';
        $stmt = $GLOBALS['pdo']->prepare($sql);
        foreach ($cartIds as $k => $id) {
            $stmt->bindValue(($k+1), $id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Could not fetch products.' . $e->getMessage();
    }
}

function showProducts($action, $product)
{
    $linkName = ucwords($action);
    return <<<HTML
        <div class="product-item">
        <div class="product-image">
        <img src="{$product['image_url']}" alt="product-image">
        </div>
        <div class="product-features">
        <div>{$product['title']}</div>
        <div>{$product['description']}</div>
        <div>{$product['price']}</div>
        </div>
        <a href="action.php?action={$action}&id={$product['id']}">{$linkName}</a>
        </div>
        <br>
    HTML;
}
