<?php
// db.php: Підключення до бази даних
$host = '127.0.0.1';
$dbname = 'test';
$username = '';
$password = '';
$dsn = 'mysql:host=' . $host .';dbname=' . $dbname . ';charset=utf8';

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Помилка підключення до бази даних: " . $e->getMessage());
}
