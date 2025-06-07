<?php
session_start();
include 'db.php';

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>BMW Motorsport — Главная</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

        .hero {
            background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
            url('assets/images/index2.jpg') no-repeat center center;
            background-size: cover;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero h1 {
            font-size: 3.5rem;
            text-shadow: 0 0 20px rgba(0,0,0,0.7);
        }

        .section-title {
            color: #58a6ff;
            font-weight: bold;
            margin-top: 60px;
            margin-bottom: 30px;
        }

        .feature-card {
            background-color: #161b22;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            transition: transform 0.3s;
            height: 100%;
            text-decoration: none;
            display: block;
            color: #e6e6e6;
        }
        .feature-card:hover {
            transform: scale(1.03);
            box-shadow: 0 0 12px rgba(88, 166, 255, 0.3);
            color: #58a6ff;
        }
        .feature-icon {
            font-size: 3rem;
            color: #58a6ff;
            margin-bottom: 10px;
        }
        .feature-label {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .card {
            background-color: #161b22 !important;
            border: none !important;
            color: #ccc !important;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #444;
        }
        .card-body {
            display: flex;
            flex-direction: column;
        }
        .card-text {
            flex-grow: 1;
        }
        .btn-outline-primary {
            color: #58a6ff;
            border-color: #58a6ff;
        }
        .btn-outline-primary:hover {
            background-color: #58a6ff;
            color: #0d1117;
        }

        .card {
            background-color: #161b22 !important;
            color: #ccc !important;
            border: none !important;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            width: 100%;
            border-bottom: 1px solid #444;
        }
        
        .driver-img-top {
            object-position: top;
        }

        .driver-img-wrapper {
            height: 250px;
            overflow: hidden;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            background-color: #0d1117;
            border-bottom: 1px solid #222;
        }
        .driver-img {
            height: 100%;
            width: auto;
            object-fit: cover;
            object-position: top;
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



<section class="hero">
    <div>
        <h1>Погрузись в гоночное наследие BMW</h1>
        <p class="lead">Серии, автомобили, гонщики, история</p>
    </div>
</section>

<div class="container my-5">

   <!-- Иконки -->
<div class="row g-4 justify-content-center text-center mb-4">
    <div class="col-md-2">
        <a href="timeline.php" class="feature-card">
            <i class="fas fa-clock-rotate-left feature-icon"></i>
            <div class="feature-label">Хронология</div>
        </a>
    </div>
    <div class="col-md-2">
        <a href="series.php" class="feature-card">
            <i class="fas fa-flag-checkered feature-icon"></i>
            <div class="feature-label">Серии</div>
        </a>
    </div>
    <div class="col-md-2">
        <a href="cars.php" class="feature-card">
            <i class="fas fa-car-side feature-icon"></i>
            <div class="feature-label">Авто</div>
        </a>
    </div>
    <div class="col-md-2">
        <a href="drivers.php" class="feature-card">
            <i class="fas fa-user feature-icon"></i>
            <div class="feature-label">Гонщики</div>
        </a>
    </div>
    <div class="col-md-2">
        <a href="teams.php" class="feature-card">
            <i class="fas fa-people-group feature-icon"></i> <!-- Или fa-users -->
            <div class="feature-label">Команды</div>
        </a>
    </div>
</div>


    <!-- Новости -->
    <h2 class="section-title text-center">Новости</h2>
    <div class="row g-4 mb-5">
        <?php
        $news = $conn->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 6");
        if ($news && $news->num_rows > 0):
            while ($item = $news->fetch_assoc()): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card h-100 d-flex flex-column">
                        <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['title']) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($item['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(mb_strimwidth($item['preview'], 0, 100, '...')) ?></p>
                            <a href="news_detail.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary mt-auto">Читать далее</a>
                        </div>
                    </div>
                </div>
            <?php endwhile;
        else: ?>
            <p class="text-center text-muted">Новостей пока нет.</p>
        <?php endif; ?>
    </div>

    <!-- Серии -->
    <h2 class="section-title text-center">Популярные серии</h2>
    <div class="row g-4">
        <?php
        $series = $conn->query("SELECT * FROM series ORDER BY id DESC LIMIT 3");
        while ($row = $series->fetch_assoc()):
        ?>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 d-flex flex-column">
                    <img src="assets/images/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
                        <a href="series.php" class="btn btn-sm btn-outline-primary mt-auto">Подробнее</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Автомобили -->
    <h2 class="section-title text-center mt-5">Знаковые автомобили</h2>
    <div class="row g-4">
        <?php
        $cars = $conn->query("SELECT * FROM cars ORDER BY id DESC LIMIT 3");
        while ($car = $cars->fetch_assoc()):
        ?>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 d-flex flex-column">
                    <img src="assets/images/<?= htmlspecialchars($car['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($car['name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($car['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($car['description']) ?></p>
                        <a href="cars.php" class="btn btn-sm btn-outline-primary mt-auto">Подробнее</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Гонщики -->
    <h2 class="section-title text-center mt-5">Гонщики</h2>
<div class="row g-4">
    <?php
    $drivers = $conn->query("SELECT * FROM drivers ORDER BY RAND() LIMIT 3");
    while ($d = $drivers->fetch_assoc()):
    ?>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 d-flex flex-column">
                <div class="driver-img-wrapper">
                    <img src="assets/images/<?= htmlspecialchars($d['image']) ?>" class="driver-img" alt="<?= htmlspecialchars($d['name']) ?>">
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($d['name']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($d['biography']) ?></p>
                    <a href="drivers.php" class="btn btn-sm btn-outline-primary mt-auto">Подробнее</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>


    <!-- Команды -->
    <h2 class="section-title text-center mt-5">Команды</h2>
    <div class="row g-4 mb-5">
        <?php
        $teams = $conn->query("SELECT * FROM teams ORDER BY id DESC LIMIT 3");
        while ($team = $teams->fetch_assoc()):
        ?>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 d-flex flex-column">
                    <img src="assets/images/<?= htmlspecialchars($team['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($team['name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($team['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($team['description']) ?></p>
                        <a href="series.php" class="btn btn-sm btn-outline-primary mt-auto">Подробнее</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

</div>
<div class="footer text-center mt-5 mb-3 text-muted">
    <p>© <?= date('Y') ?> Спортивная история BMW</p>
</div>
</body>
</html>
