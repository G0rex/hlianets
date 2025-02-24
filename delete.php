<?php
require 'db.php';

// Перевірка параметра `id`
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Невірний запит.");
}

$id = (int)$_GET['id'];

// Видалення новини
$stmt = $pdo->prepare('DELETE FROM news WHERE id = ?');
$stmt->execute([$id]);

// Повернення на адміністративну сторінку
header('Location: admin.php');
exit;
?>
