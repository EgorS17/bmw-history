<?php
session_start();
include 'db.php';

$events = [];
$result = $conn->query("SELECT * FROM events ORDER BY year ASC");
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Хронология BMW Motorsport</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/vertical-timeline.css"> <!-- Локально скачанный CSS -->

    <style>
        body {
            background-color: #0d1117;
            color: #e6e6e6;
            font-family: 'Segoe UI', sans-serif;
        }

        h1 {
            color: #e6e6e6;
        }

        .cd-timeline__content {
            background-color: #161b22;
            color: #e6e6e6;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }

        .cd-timeline__date {
            color: #58a6ff;
            font-weight: bold;
        }

        .cd-timeline__img {
            background-color: #58a6ff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cd-timeline__img img {
            width: 24px;
            height: 24px;
        }

        .timeline-image-wrapper {
            width: 100%;
            height: 250px;
            overflow: hidden;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .timeline-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .btn--subtle {
            background: none;
            color: #58a6ff;
            border: 1px solid #58a6ff;
            transition: background-color 0.2s ease;
        }

        .btn--subtle:hover {
            background-color: #1f6feb;
            color: #ffffff;
        }

        .navbar-brand:hover {
            color: #ffffff !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg" style="background-color: #161b22;">
    <div class="container">
        <a class="navbar-brand" href="index.php" style="font-weight: bold; color: #58a6ff;">BMW Motorsport</a>
        <div class="ms-auto d-flex gap-2">
            <?php if ($isAdmin): ?>
                <a href="add_event.php" class="btn btn-outline-light">+ Добавить событие</a>
                <a href="logout.php" class="btn btn-outline-danger">Выйти</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h1 class="mb-4 text-center">История BMW Motorsport</h1>
    <section class="cd-timeline js-cd-timeline">
        <div class="container max-width-lg cd-timeline__container">
            <?php foreach ($events as $event): ?>
                <div class="cd-timeline__block">
                    <div class="cd-timeline__img cd-timeline__img--picture">
                        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Иконка">
                    </div>

                    <div class="cd-timeline__content text-component">
                        <h2><?= htmlspecialchars($event['title']) ?></h2>
                        <p class="color-contrast-medium"><?= nl2br(htmlspecialchars($event['description'])) ?></p>

                        <?php if (!empty($event['image'])): ?>
                            <div class="timeline-image-wrapper">
                                <img src="assets/images/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['title']) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="cd-timeline__date"><?= htmlspecialchars($event['year']) ?></span>
                            <a href="event_detail.php?id=<?= $event['id'] ?>" class="btn btn--subtle btn-sm">Подробнее</a>
                        </div>

                        <?php if ($isAdmin): ?>
                            <div class="actions d-flex justify-content-end gap-2 mt-2">
                                <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-outline-warning">✏</a>
                                <a href="delete_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить событие?')">🗑</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<script src="assets/js/vertical-timeline.js"></script> <!-- Локальный JS плагина -->
</body>
</html>
