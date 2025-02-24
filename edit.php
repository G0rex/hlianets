<?php
require 'db.php';

// Перевірка параметра `id`
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Невірний запит.");
}

$id = (int)$_GET['id'];

// Отримання даних новини для редагування
$stmt = $pdo->prepare('SELECT title, short_description, content FROM news WHERE id = ?');
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    die("Новина не знайдена.");
}

// Оновлення новини
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $short_description = $_POST['short_description'];
    $content = $_POST['content'];

    if (!$title || !$short_description || !$content || strlen($title) > 255) {
        $error = "Усі поля обов'язкові, а довжина заголовку не повинна перевищувати 255 символів.";
    } else {
        $stmt = $pdo->prepare('UPDATE news SET title = ?, short_description = ?, content = ? WHERE id = ?');
        $stmt->execute([$title, $short_description, $content, $id]);
        header('Location: admin.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагування новини</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Редагувати новину</h1>
<?php if (!empty($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="post">
    <div>
        <label>Заголовок:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($news['title']) ?>" maxlength="255" required>
    </div>
    <div>
        <label>Короткий опис:</label>
        <textarea name="short_description" required><?= htmlspecialchars($news['short_description']) ?></textarea>
    </div>
    <div>
        <label>Текст новини:</label>
        <textarea name="content" required><?= htmlspecialchars($news['content']) ?></textarea>
    </div>
    <button type="submit">Оновити</button>
</form>
<a href="admin.php">Назад</a>
</body>
</html>
