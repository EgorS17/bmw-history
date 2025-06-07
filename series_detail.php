<?php
session_start();
require 'db.php';

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];
$series_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$series_id) {
    echo "Серия не найдена.";
    exit;
}

// Привязка автомобиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin && !empty($_POST['car_id'])) {
    $stmt = $conn->prepare("INSERT IGNORE INTO car_series (car_id, series_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $_POST['car_id'], $series_id);
    $stmt->execute();
    header("Location: series_detail.php?id=" . $series_id);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM series WHERE id = ?");
$stmt->bind_param("i", $series_id);
$stmt->execute();
$series_result = $stmt->get_result();
$series = $series_result->fetch_assoc();

if (!$series) {
    echo "Серия не найдена.";
    exit;
}

$logo_file = null;
switch ($series['name']) {
    case 'WTCC': $logo_file = 'wtcc1.jpg'; break;
    case '24h Nürburgring': $logo_file = 'nurb.jpg'; break;
    case 'DTM': $logo_file = 'dtm1.jpg'; break;
    case 'Formula BMW': $logo_file = 'f1.jpg'; break;
    case 'GT World Challenge': $logo_file = 'gtwc.jpg'; break;
    case 'IMSA': $logo_file = 'gtp.jpg'; break;
}

$stats_stmt = $conn->prepare("SELECT ss.*, d.name AS driver_name, d.id AS driver_id, t.name AS team_name FROM series_stats ss LEFT JOIN drivers d ON ss.driver_id = d.id LEFT JOIN teams t ON ss.team_id = t.id WHERE ss.series_id = ?");
$stats_stmt->bind_param("i", $series_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$stats = $stats_result->fetch_assoc();

$cars_stmt = $conn->prepare("SELECT c.id, c.name, c.model, c.year, c.image FROM cars c INNER JOIN car_series cs ON c.id = cs.car_id WHERE cs.series_id = ? ORDER BY c.year ASC");
$cars_stmt->bind_param("i", $series_id);
$cars_stmt->execute();
$cars_result = $cars_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($series['name']) ?> — Детали серии</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background-color: #0d1117; color: #e6e6e6; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #161b22; }
        .navbar-brand { font-weight: bold; color: #58a6ff; }
        .series-title { font-size: 2.5rem; font-weight: bold; color: #58a6ff; }
        .series-description { background-color: #161b22; padding: 25px; border-radius: 12px; box-shadow: 0 0 10px rgba(88, 166, 255, 0.1); margin-bottom: 40px; line-height: 1.7; }
        .car-card { background-color: #161b22; border-radius: 16px; overflow: hidden; transition: transform 0.3s; box-shadow: 0 0 15px rgba(0,0,0,0.3); }
        .car-card:hover { transform: scale(1.03); }
        .car-card img { width: 100%; height: 200px; object-fit: contain; background-color: #0d1117; padding: 10px; }
        .car-info { padding: 15px; }
        .car-info h5 { color: #58a6ff; }
        .btn-outline-danger { width: 100%; }
        .section-title { font-size: 1.8rem; margin-bottom: 20px; color: #58a6ff; }
        .form-select { background-color: #0d1117; color: #e6e6e6; border: 1px solid #333; }
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
        .btn--subtle {
            background: none;
            color: #ffffff !important;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .btn--subtle:hover {
            background-color: #ffffff !important;
            color: #0d1117 !important; /* ВАЖНО: задаём цвет текста */
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
            <?php if (isset($isAdmin) && $isAdmin): ?>
                <a href="logout.php" class="btn btn--danger-subtle">Выйти</a>
            <?php endif; ?>
        </div>
    </div>
</nav>


<div class="container mt-5">
    <div class="series-description">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <?php if ($logo_file): ?>
                <img src="assets/images/<?= $logo_file ?>" alt="Логотип" style="height: 60px;" class="rounded shadow-sm">
            <?php endif; ?>
            <h1 class="series-title m-0"><?= htmlspecialchars($series['name']) ?></h1>
        </div>
        <?php if ($series['description']): ?><p class="mt-3"><em><?= htmlspecialchars($series['description']) ?></em></p><?php endif; ?>
        <?php if ($series['full_description']): ?><p class="mt-4"><?= nl2br(htmlspecialchars($series['full_description'])) ?></p><?php endif; ?>
    </div>

    <?php if ($stats): ?>
        <div class="series-description mt-4">
            <h2 class="section-title">Статистика серии</h2>
            <ul>
                <li><strong>Годы участия:</strong> <?= htmlspecialchars($stats['years']) ?></li>
                <li><strong>Гонок:</strong> <?= $stats['races'] ?></li>
                <li><strong>Победы:</strong> <?= $stats['wins'] ?></li>
                <li><strong>Подиумы:</strong> <?= $stats['podiums'] ?></li>
                <li><strong>Лучший автомобиль:</strong> <?= htmlspecialchars($stats['best_car']) ?></li>
                <li><strong>Лучший гонщик:</strong>
    <?php
    if (!empty($stats['driver_name']) && $stats['driver_id']) {
        echo "<a href='driver_detail.php?id=" . $stats['driver_id'] . "' class='text-info text-decoration-none'>" . htmlspecialchars($stats['driver_name']) . "</a>";
    } else {
        echo "Не указан";
    }
    ?>
</li>
                <li><strong>Команда-лидер:</strong> <?= htmlspecialchars($stats['team_name']) ?></li>
            </ul>
        </div>
    <?php endif; ?>

    <h2 class="section-title">Автомобили в серии</h2>
    <?php if ($cars_result->num_rows > 0): ?>
        <div class="row">
            <?php while ($car = $cars_result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="car-card position-relative">
                        <a href="cars_detail.php?id=<?= $car['id'] ?>" class="d-block text-decoration-none text-reset">
                            <img src="assets/images/<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['name']) ?>">
                            <div class="car-info">
                                <h5><?= htmlspecialchars($car['name']) ?></h5>
                                <p><strong>Модель:</strong> <?= htmlspecialchars($car['model']) ?></p>
                                <p><strong>Год выпуска:</strong> <?= htmlspecialchars($car['year']) ?></p>
                            </div>
                        </a>
                        <?php if ($isAdmin): ?>
                            <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-dark bg-opacity-75">
                                <a href="remove_car_from_series.php?car_id=<?= $car['id'] ?>&series_id=<?= $series_id ?>" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Точно убрать автомобиль из серии?');">Убрать из серии</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>В этой серии пока нет автомобилей.</p>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
        <h2 class="section-title">Добавить автомобиль в серию</h2>
        <form method="POST" class="row g-3 align-items-center mb-5">
            <div class="col-md-6">
                <select name="car_id" class="form-select">
                    <?php
                    $res = $conn->query("SELECT id, name FROM cars ORDER BY name ASC");
                    while ($car = $res->fetch_assoc()): ?>
                        <option value="<?= $car['id'] ?>"><?= htmlspecialchars($car['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-success w-100">Добавить автомобиль</button>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
