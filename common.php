<?php

require_once './config.php';

try {

    $pdo = new PDO('mysql:host=' . $servername . ';dbname=' . $dbname, $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Could not connect to database.' . $e->getMessage();
}