<?php
require 'db.php';

// Додати новину
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $short_description = $_POST['short_description'];
    $content = $_POST['content'];

    if (!$title || !$short_description || !$content || strlen($title) > 255) {
        $error = "Усі поля обов'язкові, а довжина заголовку не повинна перевищувати 255 символів.";
    } else {
        $stmt = $pdo->prepare('INSERT INTO news (title, short_description, content) VALUES (?, ?, ?)');
        $stmt->execute([$title, $short_description, $content]);
        header('Location: admin.php');
        exit;
    }
}

// Отримання списку новин
$news = $pdo->query('SELECT id, title FROM news ORDER BY created_at DESC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Адміністративна панель</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Адміністративна панель</h1>

<!-- Форма для створення новини -->
<h2>Додати новину</h2>
<?php if (!empty($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="post">
    <div>
        <label>Заголовок:</label>
        <input type="text" name="title" maxlength="255" required>
    </div>
    <div>
        <label>Короткий опис:</label>
        <textarea name="short_description" required></textarea>
    </div>
    <div>
        <label>Текст новини:</label>
        <textarea name="content" required></textarea>
    </div>
    <button type="submit">Додати</button>
</form>

<!-- Список новин для редагування/видалення -->
<h2>Список новин</h2>
<ul>
    <?php foreach ($news as $item): ?>
        <li>
            <?= htmlspecialchars($item['title']) ?>
            <a href="edit.php?id=<?= $item['id'] ?>">Редагувати</a>
            <a href="delete.php?id=<?= $item['id'] ?>" onclick="return confirm('Ви впевнені?');">Видалити</a>
        </li>
    <?php endforeach; ?>
</ul>
</body>
</html>
