<?php
require 'db.php';

// Налаштування пагінації
$newsPerPage = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $newsPerPage;

// Змінні для фільтрування
$fromDate = isset($_GET['from_date'])
    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from_date'])
        ? $_GET['from_date']
        : null;
$toDate = isset($_GET['to_date'])
    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to_date'])
        ? $_GET['to_date']
        : null;
$keyword = isset($_GET['keyword'])
    ? trim($_GET['keyword'])
    : null;


// Формування запиту
$query = 'SELECT id, title, short_description, created_at FROM news WHERE 1=1';
$params = [];

// Фільтрація за датою
if ($fromDate) {
    $query .= ' AND created_at >= ?';
    $params[] = $fromDate;
}
if ($toDate) {
    $query .= ' AND created_at <= ?';
    $params[] = $toDate . ' 23:59:59';
}

// Пошук за ключовими словами
if ($keyword) {
    $query .= ' AND (title LIKE ? OR short_description LIKE ?)';
    $params[] = '%' . $keyword . '%';
    $params[] = '%' . $keyword . '%';
}

// Сортування і обмеження
$query .= ' ORDER BY created_at DESC LIMIT ' . $newsPerPage . ' OFFSET ' . $offset;

// Виконання запиту
$stmt = $pdo->prepare($query);
if (!$stmt->execute($params)) {
    $errorInfo = $stmt->errorInfo();
    echo 'SQLSTATE код помилки: ' . $errorInfo[0] . '<br>';
    echo 'Код драйвера БД: ' . $errorInfo[1] . '<br>';
    echo 'Повідомлення про помилку: ' . $errorInfo[2];
}

$news = $stmt->fetchAll();

// Отримання загальної кількості новин (для пагінації)
$countQuery = 'SELECT COUNT(*) FROM news WHERE 1=1';
$countParams = [];

// Фільтрація для лічильника
if ($fromDate) {
    $countQuery .= ' AND created_at >= ?';
    $countParams[] = $fromDate;
}
if ($toDate) {
    $countQuery .= ' AND created_at <= ?';
    $countParams[] = $toDate . ' 23:59:59';
}
if ($keyword) {
    $countQuery .= ' AND (title LIKE ? OR short_description LIKE ?)';
    $countParams[] = '%' . $keyword . '%';
    $countParams[] = '%' . $keyword . '%';
}

$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($countParams);
$totalNews = $countStmt->fetchColumn();
$totalPages = ceil($totalNews / $newsPerPage);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Головна сторінка</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Список новин</h1>

<!-- Форма пошуку та фільтру за датою -->
<form method="get" action="index.php">
    <label for="search">Пошук:</label>
    <input type="text" name="keyword" id="search" value="<?= htmlspecialchars($keyword ?? '') ?>" placeholder="Введіть ключове слово">

    <label for="from_date">Від:</label>
    <input type="date" name="from_date" id="from_date" value="<?= htmlspecialchars($fromDate ?? '') ?>">

    <label for="to_date">До:</label>
    <input type="date" name="to_date" id="to_date" value="<?= htmlspecialchars($toDate ?? '') ?>">

    <button type="submit">Застосувати</button>
</form>

<!-- Виведення новин -->
<?php if (!empty($news)): ?>
    <?php foreach ($news as $item): ?>
        <article>
            <h2><?= htmlspecialchars($item['title']) ?></h2>
            <p><em><?= $item['created_at'] ?></em></p>
            <p><?= nl2br(htmlspecialchars($item['short_description'])) ?></p>
            <a href="news_details.php?id=<?= $item['id'] ?>">Детальніше</a>
        </article>
    <?php endforeach; ?>
<?php else: ?>
    <p>Новини не знайдено.</p>
<?php endif; ?>

<!-- Пагінація -->
<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&from_date=<?= htmlspecialchars($fromDate ?? '') ?>&to_date=<?= htmlspecialchars($toDate ?? '') ?>&keyword=<?= htmlspecialchars($keyword ?? '') ?>" <?= $i === $page ? 'class="active"' : '' ?>>
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
</body>
</html>