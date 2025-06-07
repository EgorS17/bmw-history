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
    <link rel="stylesheet" href="assets/css/vertical-timeline.css">
    <link rel="stylesheet" href="assets/css/timeline.css">
    <style>
        .navbar {
            background-color: #161b22;
        }
        .navbar-brand {
            font-weight: bold;
            color: #58a6ff;
        }
        .navbar-brand:hover {
            color: #ffffff !important;
        }
        .btn--subtle {
            background: none;
            color: #ffffff;
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
            color: #fcfcfc;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        .btn--danger-subtle:hover {
            background-color: #dc3545;
            color: #ffffff;
        }
        .navbar-toggler-icon {
            width: 1.5em;
            height: 1.5em;
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%28255,255,255,0.7%29' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 100% 100%;
        }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">BMW Motorsport</a>

        <!-- Кнопка для мобильного меню -->
        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Меню, которое сворачивается -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="ms-auto d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center">
                <a href="timeline.php" class="btn btn--subtle">Хронология</a>
                <a href="series.php" class="btn btn--subtle">Серии</a>
                <a href="cars.php" class="btn btn--subtle">Автомобили</a>
                <a href="drivers.php" class="btn btn--subtle">Гонщики</a>
                <a href="teams.php" class="btn btn--subtle">Команды</a>
                <?php if ($isAdmin): ?>
                    <a href="logout.php" class="btn btn--danger-subtle">Выйти</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>


<div class="container my-5">
    <h1 class="mb-4 text-center">История BMW Motorsport</h1>
    <section class="cd-timeline js-cd-timeline">
        <div class="container max-width-lg cd-timeline__container">
            <?php foreach ($events as $event): ?>
                <div class="cd-timeline__block js-cd-block">
                    <div class="cd-timeline__img cd-timeline__img--picture"></div>

                    <div class="cd-timeline__content text-component position-relative">
                        <h2>
                            <a href="event_detail.php?id=<?= $event['id'] ?>" class="text-decoration-none text-info">
                                <?= htmlspecialchars($event['title']) ?>
                            </a>
                        </h2>

                        <p class="color-contrast-medium"><?= nl2br(htmlspecialchars($event['description'])) ?></p>

                        <?php if (!empty($event['image'])): ?>
                            <div class="timeline-image-wrapper">
                                <img src="assets/images/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['title']) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="cd-timeline__date"><?= htmlspecialchars($event['year']) ?></span>
                        </div>

                        <?php if ($isAdmin): ?>
                            <div class="actions d-flex justify-content-end gap-2 mt-2 position-relative z-10">
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

<script src="assets/js/vertical-timeline.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const blocks = document.querySelectorAll(".js-cd-block");

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("js-cd-block--animate");
                }
            });
        }, { threshold: 0.3 });

        blocks.forEach(block => observer.observe(block));
    });
</script>

<div class="footer text-center mt-5 mb-3 text-muted">
    <p>© <?= date('Y') ?> Спортивная история BMW</p>
</div>
</body>
</html>
