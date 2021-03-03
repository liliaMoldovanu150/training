<?php

require_once 'common.php';

if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = array();
}

if (!empty($_GET['id'])) {
    if ($_GET['action'] == 'add') {
        array_push($_SESSION['id'], $_GET['id']);
        header('Location: index.php');
    } elseif ($_GET['action'] == 'remove') {
        $key = array_search(2, $_SESSION['id']);
        unset($_SESSION['id'][$key]);
        header('Location: cart.php');
    }
}


