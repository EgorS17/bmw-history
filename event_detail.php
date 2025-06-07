<?php
include 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "Событие не найдено.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #0d1117;
            color: #e6e6e6;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background-color: #161b22;
        }

        .navbar-brand {
            font-weight: bold;
            color: #58a6ff;
        }

        .content-container {
            max-width: 900px;
            margin: 50px auto;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .event-meta {
            font-size: 0.95rem;
            color: #9ca3af;
            margin-bottom: 2rem;
        }

        .badge-decade {
            background-color: #1f2937;
            color: #9ca3af;
            padding: 3px 10px;
            font-size: 0.85rem;
            border-radius: 12px;
            margin-left: 10px;
        }

        .event-image {
            width: 100%;
            height: auto;
            max-height: 450px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            margin-top: 2rem;
        }

        .event-description {
            line-height: 1.7;
            font-size: 1.05rem;
            color: #ddd;
        }

        .video-wrapper iframe,
        .video-wrapper video {
            width: 100%;
            max-height: 450px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .btn-back {
            margin-top: 40px;
        }

        .navbar-brand:hover {
            color: #ffffff !important;
        }
        .navbar {
            background-color: #161b22;
        }
        .navbar-brand {
            color: #58a6ff !important;
            font-weight: bold;
        }
        .navbar-brand:hover {
            color: #ffffff !important;
        }
        .btn--subtle {
            background: none;
            color: #ffffff !important;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        .btn--subtle:hover {
            background-color: #ffffff;
            color: #0d1117;
        }

        .btn--danger-subtle {
            background: none;
            color: #fcfcfc !important;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        .btn--danger-subtle:hover {
            background-color: #dc3545;
            color: #ffffff;
        }

        .navbar-brand {
            color: #58a6ff !important;
            font-weight: bold;
        }
        .navbar-brand:hover {
            color: #ffffff !important;
        }

    </style>
</head>
<body>

<!-- Навигация -->
<nav class="navbar navbar-expand-lg" style="background-color: #161b22;">
    <div class="container">
        <a class="navbar-brand" href="index.php">BMW Motorsport</a>

        <div class="d-flex ms-auto gap-3 align-items-center">
            <a href="timeline.php" class="btn btn--subtle">Хронология</a>
            <a href="series.php" class="btn btn--subtle">Серии</a>
            <a href="cars.php" class="btn btn--subtle">Автомобили</a>
            <a href="drivers.php" class="btn btn--subtle">Гонщики</a>
            <a href="teams.php" class="btn btn--subtle">Команды</a>
            <?php if (isset($isAdmin) && $isAdmin): ?>
                <a href="logout.php" class="btn btn--danger-subtle">Выйти</a>
            <?php endif; ?>
        </div>
    </div>
</nav>


<div class="container content-container">
    <h1><?= htmlspecialchars($event['title']) ?></h1>
    <div class="event-meta">
        <?= $event['year'] ?> год
        <span class="badge-decade"><?= $event['decade'] ?>-е</span>
    </div>

    <?php if (!empty($event['image'])): ?>
        <img src="assets/images/<?= htmlspecialchars($event['image']) ?>" class="event-image" alt="Изображение события">
    <?php endif; ?>

    <h2 class="section-title">Описание</h2>
    <div class="event-description">
        <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
    </div>

    <?php if (!empty($event['video_url'])): ?>
        <h2 class="section-title">Видео события</h2>
        <div class="video-wrapper my-4">
            <?php if (str_contains($event['video_url'], 'youtube.com') || str_contains($event['video_url'], 'youtu.be')): ?>
                <div class="ratio ratio-16x9">
                    <iframe src="<?= htmlspecialchars($event['video_url']) ?>" title="Видео события" allowfullscreen></iframe>
                </div>
            <?php else: ?>
                <video controls>
                    <source src="<?= htmlspecialchars($event['video_url']) ?>" type="video/mp4">
                    Ваш браузер не поддерживает видео.
                </video>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <a href="timeline.php" class="btn btn-outline-secondary btn-back">← Назад</a>
</div>

</body>
</html>
