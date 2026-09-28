<?php

$host = 'localhost';
$db = 'travel';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    echo "Connecting...\n";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected successfully\n";
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int) $e->getCode());
}
