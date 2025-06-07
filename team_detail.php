<?php
session_start();
require 'db.php';

$team_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$team_id) {
    die("Команда не найдена.");
}

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];

$stmt = $conn->prepare("SELECT * FROM teams WHERE id = ?");
$stmt->bind_param("i", $team_id);
$stmt->execute();
$team = $stmt->get_result()->fetch_assoc();
if (!$team) {
    die("Команда не найдена");
}

$drivers = $conn->query("SELECT d.*, dt.start_year, dt.end_year FROM drivers d JOIN driver_team dt ON d.id = dt.driver_id WHERE dt.team_id = $team_id")->fetch_all(MYSQLI_ASSOC);
$series = $conn->query("SELECT s.*, ts.start_year, ts.end_year FROM series s JOIN team_series ts ON s.id = ts.series_id WHERE ts.team_id = $team_id")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($team['name']) ?> — Команда</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

        .section-title {
            color: #58a6ff;
            font-weight: bold;
            margin-top: 60px;
            margin-bottom: 30px;
        }

        .card {
            background-color: #161b22 !important;
            border: none;
            color: #ccc;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .driver-img-wrapper {
            padding: 0.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #0d1117;
        }

        .driver-img {
            max-width: 100%;
            max-height: 220px;
            height: auto;
            width: auto;
            object-fit: contain;
            display: block;
        }

        a.text-reset {
            text-decoration: none;
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row align-items-center mb-5 flex-column flex-md-row text-center text-md-start">
        <div class="col-md-5 mb-4 mb-md-0">
            <img src="assets/images/<?= htmlspecialchars($team['image']) ?>" alt="<?= htmlspecialchars($team['name']) ?>" class="img-fluid rounded w-100" style="max-height: 400px; object-fit: cover;">
        </div>
        <div class="col-md-7">
            <h1 class="text-info mb-3"><?= htmlspecialchars($team['name']) ?></h1>
            <p class="lead"><?= nl2br(htmlspecialchars($team['description'])) ?></p>
        </div>
    </div>

    <?php if (!empty($drivers)): ?>
        <h2 class="section-title text-center mt-5">Гонщики команды</h2>
        <div class="row g-4">
            <?php foreach ($drivers as $d): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="driver_detail.php?id=<?= $d['id'] ?>" class="text-decoration-none text-reset">
                        <div class="card h-100 d-flex flex-column">
                            <div class="driver-img-wrapper">
                                <img src="assets/images/<?= htmlspecialchars($d['image']) ?>" class="driver-img" alt="<?= htmlspecialchars($d['name']) ?>">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($d['name']) ?></h5>
                                <p class="card-text">Годы участия: <?= $d['start_year'] ?> - <?= $d['end_year'] ?? 'н.в.' ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($series)): ?>
        <h2 class="section-title text-center">Серии, в которых участвовала команда</h2>
        <div class="row g-4">
            <?php foreach ($series as $s): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="series_detail.php?id=<?= $s['id'] ?>" class="text-decoration-none text-reset">
                        <div class="card h-100 d-flex flex-column">
                            <img src="assets/images/<?= htmlspecialchars($s['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($s['name']) ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($s['name']) ?></h5>
                                <p class="card-text"><?= $s['start_year'] ?> - <?= $s['end_year'] ?? 'н.в.' ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="footer text-center mt-5 mb-3 text-muted">
    <p>© <?= date('Y') ?> Спортивная история BMW</p>
</div>

</body>
</html>
