<?php
session_start();
require 'db.php';

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];

$query = "SELECT id, name, description, image FROM series ORDER BY name";
$result = $conn->query($query);

$series = [];
while ($row = $result->fetch_assoc()) {
    $series[] = $row;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Гоночные серии BMW</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

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

        .custom-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .custom-card:hover {
            transform: scale(1.02);
        }

        .series-card img {
            width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: cover;
        }

        @media (min-width: 768px) {
            .series-card {
                flex-direction: row;
            }
        }

        @media (max-width: 767.98px) {
            .series-card {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">BMW Motorsport</a>
        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="ms-auto d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center">
                <a href="timeline.php" class="btn btn--subtle">Хронология</a>
                <a href="series.php" class="btn btn--subtle">Серии</a>
                <a href="cars.php" class="btn btn--subtle">Автомобили</a>
                <a href="drivers.php" class="btn btn--subtle">Гонщики</a>
                <a href="teams.php" class="btn btn--subtle">Команды</a>
                <?php if ($isAdmin): ?>
                    <a href="logout.php" class="btn btn--danger-subtle">Выйти</a>
                    <a href="add_series.php" class="btn btn--danger-subtle">Добавить авто</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4 px-3">
    <h2 class="mb-4 text-center">Популярные гоночные серии</h2>
    <div class="swiper mySwiper mb-5">
        <div class="swiper-wrapper">
            <?php foreach ($series as $s): ?>
                <div class="swiper-slide">
                    <a href="series_detail.php?id=<?= $s['id'] ?>" class="text-decoration-none">
                        <div class="card shadow-sm bg-dark text-white custom-card">
                            <img src="assets/images/<?= htmlspecialchars($s['image']) ?>" class="card-img-top" style="height: 220px; object-fit: cover;" alt="<?= htmlspecialchars($s['name']) ?>">
                            <div class="card-body p-3">
                                <h5 class="card-title"><?= htmlspecialchars($s['name']) ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</div>

<h3 class="my-5 text-center text-info">Основные гоночные серии BMW</h3>
<div class="container py-3 px-3">
    <div class="row g-4">
        <?php foreach ($series as $s): ?>
            <div class="col-12 col-md-6">
                <div class="card bg-dark text-white d-flex shadow-sm custom-card series-card" data-id="<?= $s['id'] ?>">
                    <img src="assets/images/<?= htmlspecialchars($s['image']) ?>" alt="<?= htmlspecialchars($s['name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($s['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars(mb_strimwidth($s['description'], 0, 150, '...')) ?></p>
                        <div class="mt-auto d-flex flex-column gap-2">
                            <?php if ($isAdmin): ?>
                                <a href="edit_series.php?id=<?= $s['id'] ?>" class="btn btn-outline-warning btn-sm">Редактировать</a>
                                <a href="delete_series.php?id=<?= $s['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Удалить серию?')">Удалить</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    const swiper = new Swiper('.mySwiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        centeredSlides: true,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            0: { slidesPerView: 1 },
            576: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            992: { slidesPerView: 2 }
        }
    });

    document.querySelectorAll('.series-card').forEach(card => {
        card.addEventListener('click', function (e) {
            if (e.target.closest('a, button')) return;
            const id = card.dataset.id;
            window.location.href = `series_detail.php?id=${id}`;
        });
    });
</script>

<div class="footer text-center mt-5 mb-3 text-muted">
    <p>© <?= date('Y') ?> Спортивная история BMW</p>
</div>
</body>
</html>
