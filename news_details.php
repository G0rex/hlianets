<?php
require 'db.php';

// Перевірка параметра `id`
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Невірний запит.");
}

$id = (int)$_GET['id'];

// Отримання новини за ID
$stmt = $pdo->prepare('SELECT title, content, created_at FROM news WHERE id = ?');
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    die("Новина не знайдена.");
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($news['title']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1><?= htmlspecialchars($news['title']) ?></h1>
<p><em><?= $news['created_at'] ?></em></p>
<p><?= nl2br(htmlspecialchars($news['content'])) ?></p>
<a href="index.php">Назад до списку</a>
</body>
</html>
