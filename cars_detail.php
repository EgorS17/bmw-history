<?php
session_start();
require 'db.php';

$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$car_id) {
    die("Автомобиль не найден.");
}

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    if (!empty($_POST['series_id'])) {
        $stmt = $conn->prepare("INSERT IGNORE INTO car_series (car_id, series_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $car_id, $_POST['series_id']);
        $stmt->execute();
    }
    if (!empty($_POST['driver_id'])) {
        $stmt = $conn->prepare("INSERT IGNORE INTO driver_car (car_id, driver_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $car_id, $_POST['driver_id']);
        $stmt->execute();
    }
    header("Location: cars_detail.php?id=" . $car_id);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$car = $stmt->get_result()->fetch_assoc();
if (!$car) {
    die("Автомобиль не найден");
}

$series = $conn->query("SELECT s.* FROM series s INNER JOIN car_series cs ON s.id = cs.series_id WHERE cs.car_id = $car_id")->fetch_all(MYSQLI_ASSOC);
$drivers = $conn->query("SELECT d.* FROM drivers d INNER JOIN driver_car dc ON d.id = dc.driver_id WHERE dc.car_id = $car_id")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($car['name']) ?> — Детали автомобиля</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
             background-color: #0d1117; 
             color: #e6e6e6; 
             font-family: 'Segoe UI', sans-serif;
        }
        .section-title {
             color: #58a6ff; 
             font-weight: bold;
             margin-top: 40px;
        }
        .form-select { 
            background-color: #0d1117; 
            color: #e6e6e6;
             border: 1px solid #333;
        }
        .btn-outline-success { 
            border-color: #58a6ff; 
            color: #58a6ff; 
        }
        .btn-outline-success:hover {
            background-color: #58a6ff;
            color: #0d1117; 
        }
        .card {
            background-color: #161b22 !important;
            border: none !important; color: #ccc !important;
            border-radius: 16px;
            overflow: hidden; 
            transition: transform 0.3s ease; 
        }
        .card:hover { 
            transform: scale(1.02); 
        }
        .card-img-top { 
            height: 200px; 
            object-fit: cover; 
            width: 100%; 
            border-bottom: 1px solid #444; 
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
        <style>
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
        .navbar .btn--subtle {
            color: #ffffff !important;
        }
        .navbar .btn--subtle:hover {
            background-color: #ffffff !important;
            color: #0d1117 !important;
        }
        .navbar-brand {
            color: #58a6ff !important;
            font-weight: bold;
        }
        .navbar-brand:hover {
            color: #ffffff !important;
        }

</style>

    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg" style="background-color: #161b22;">
    <div class="container">
        <a class="navbar-brand" href="index.php">BMW Motorsport</a>

        <div class="d-flex ms-auto gap-3 align-items-center">
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
</nav>


<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img src="assets/images/<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['name']) ?>" class="img-fluid rounded shadow mb-4">
        </div>
        <div class="col-md-6">
            <h1 class="mb-4 text-info"><?= htmlspecialchars($car['name']) ?> (<?= htmlspecialchars($car['model']) ?>)</h1>
            <p><strong>Год выпуска:</strong> <?= htmlspecialchars($car['year']) ?></p>
            <p><?= nl2br(htmlspecialchars($car['description'])) ?></p>
        </div>
    </div>

    <?php if (!empty($series)): ?>
        <h2 class="section-title">Связанные серии</h2>
        <div class="row g-4">
            <?php foreach ($series as $s): ?>
                <div class="col-md-4">
                    <a href="series_detail.php?id=<?= $s['id'] ?>" class="card h-100 text-center text-decoration-none text-reset bg-dark text-light">
                        <?php if (!empty($s['image'])): ?>
                            <img src="assets/images/<?= htmlspecialchars($s['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($s['name']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($s['name']) ?></h5>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($drivers)): ?>
        <h2 class="section-title">Связанные гонщики</h2>
        <div class="row g-4">
            <?php foreach ($drivers as $d): ?>
                <div class="col-md-4">
                    <a href="driver_detail.php?id=<?= $d['id'] ?>" class="card h-100 text-center text-decoration-none text-reset">
                        <?php if (!empty($d['image'])): ?>
                            <div class="driver-img-wrapper">
                                <img src="assets/images/<?= htmlspecialchars($d['image']) ?>" class="driver-img" alt="<?= htmlspecialchars($d['name']) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($d['name']) ?></h5>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
        <h2 class="section-title">Привязать сущности</h2>
        <form method="POST" class="row g-3 align-items-center mb-4">
            <div class="col-md-6">
                <label class="form-label">Добавить серию</label>
                <select name="series_id" class="form-select">
                    <?php $res = $conn->query("SELECT id, name FROM series"); while ($row = $res->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-success w-100">Добавить серию</button>
            </div>
        </form>

        <form method="POST" class="row g-3 align-items-center">
            <div class="col-md-6">
                <label class="form-label">Добавить гонщика</label>
                <select name="driver_id" class="form-select">
                    <?php $res = $conn->query("SELECT id, name FROM drivers"); while ($row = $res->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-success w-100">Добавить гонщика</button>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>