<?php

session_start();

require_once './config.php';

try {

    $GLOBALS['pdo'] = new PDO('mysql:host=' . $servername . ';dbname=' . $dbname, $username, $password);

    $GLOBALS['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Could not connect to database.' . $e->getMessage();
}

