<?php
session_start();
require 'db.php';

$driver_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];

if (!$driver_id) {
    echo "Гонщик не найден.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    if (isset($_POST['car_id'])) {
        $car_id = (int)$_POST['car_id'];
        $stmt = $conn->prepare("INSERT INTO driver_car (driver_id, car_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $driver_id, $car_id);
        $stmt->execute();
    }

    if (isset($_POST['team_id'])) {
        $team_id = (int)$_POST['team_id'];
        $stmt = $conn->prepare("INSERT INTO driver_team (driver_id, team_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $driver_id, $team_id);
        $stmt->execute();
    }

    header("Location: driver_detail.php?id=" . $driver_id);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM drivers WHERE id = ?");
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$driver_result = $stmt->get_result();
$driver = $driver_result->fetch_assoc();

if (!$driver) {
    echo "Гонщик не найден.";
    exit;
}

$cars_stmt = $conn->prepare("
    SELECT c.id, c.name, c.model, c.year, c.image
    FROM cars c
    INNER JOIN driver_car dc ON dc.car_id = c.id
    WHERE dc.driver_id = ?
");
$cars_stmt->bind_param("i", $driver_id);
$cars_stmt->execute();
$cars_result = $cars_stmt->get_result();

$teams_stmt = $conn->prepare("
    SELECT t.id, t.name, t.image
    FROM teams t
    INNER JOIN driver_team dt ON dt.team_id = t.id
    WHERE dt.driver_id = ?
");
$teams_stmt->bind_param("i", $driver_id);
$teams_stmt->execute();
$teams_result = $teams_stmt->get_result();

$allCars = $conn->query("SELECT id, name FROM cars ORDER BY name");
$allTeams = $conn->query("SELECT id, name FROM teams ORDER BY name");

// Флаги по национальности
$flags = [
    'UK' => '🇬🇧',
    'Germany' => '🇩🇪',
    'Italy' => '🇮🇹',
    'France' => '🇫🇷',
    'Russia' => '🇷🇺',
    'USA' => '🇺🇸',
    'Japan' => '🇯🇵',
    'Spain' => '🇪🇸',
    'Brazil' => '🇧🇷',
    'Finland' => '🇫🇮',
    'Canada' => '🇨🇦',
    'Netherlands' => '🇳🇱',
];
$flag = $flags[$driver['nationality']] ?? '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($driver['name']) ?> — Гонщик BMW</title>
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
        .driver-photo-box {
            width: 250px;
            height: 250px;
            border: 4px solid #58a6ff;
            border-radius: 16px;
            overflow: hidden;
            background-color: #0d1117;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .driver-photo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
            color: #58a6ff;
            text-align: left;
        }
        .card {
            background-color: #161b22;
            border: none;
            color: #ccc;
            border-radius: 16px;
            overflow: hidden;
        }
        .card-img-top {
            height: 180px;
            object-fit: cover;
        }

        .driver-info {
            text-align: left !important;
        }

        .driver-info p, .driver-info h4 {
            text-align: left !important;
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

    <div class="row mb-5 align-items-start g-1">
        <!-- Фото и имя -->
        <div class="col-md-4">
            <h2 class="mb-3"><?= htmlspecialchars($driver['name']) ?></h2>
            <div class="driver-photo-box">
                <img src="assets/images/<?= htmlspecialchars($driver['image']) ?>" alt="<?= htmlspecialchars($driver['name']) ?>">
            </div>
        </div>

        <!-- Информация -->
        <div class="col-md-8">
            <div class="driver-info">
                <p><strong>Национальность:</strong> <?= $flag ?> <?= htmlspecialchars($driver['nationality']) ?></p>

                <?php if ($driver['birthdate']): ?>
                    <p><strong>Дата рождения:</strong> <?= (new DateTime($driver['birthdate']))->format('d.m.Y') ?></p>
                <?php endif; ?>

                <?php if (!empty($driver['biography'])): ?>
                    <h4 class="section-title mt-4">Биография</h4>
                    <p><?= nl2br(htmlspecialchars($driver['biography'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mb-5">
    <h4 class="section-title">Автомобили гонщика</h4>
    <?php if ($cars_result->num_rows > 0): ?>
        <div class="row">
            <?php while ($car = $cars_result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <a href="cars_detail.php?id=<?= $car['id'] ?>" class="card h-100 text-decoration-none text-reset d-block">
                        <?php if (!empty($car['image'])): ?>
                            <img src="assets/images/<?= htmlspecialchars($car['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($car['name']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($car['name']) ?></h5>
                            <p class="card-text"><strong>Модель:</strong> <?= htmlspecialchars($car['model']) ?></p>
                            <p class="card-text"><strong>Год:</strong> <?= htmlspecialchars($car['year']) ?></p>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Автомобили не найдены.</p>
    <?php endif; ?>
</div>


<div class="mb-5">
    <h4 class="section-title">Команды гонщика</h4>
    <?php if ($teams_result->num_rows > 0): ?>
        <div class="row">
            <?php while ($team = $teams_result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <a href="team_detail.php?id=<?= $team['id'] ?>" class="card h-100 text-center text-decoration-none text-reset d-block">
                        <?php if (!empty($team['image'])): ?>
                            <img src="assets/images/<?= htmlspecialchars($team['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($team['name']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($team['name']) ?></h5>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Команды не найдены.</p>
    <?php endif; ?>
</div>


    <?php if ($isAdmin): ?>
        <div class="mb-5">
            <h4 class="section-title">Добавить автомобиль гонщику</h4>
            <form method="POST" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <select name="car_id" class="form-select">
                        <?php while ($car = $allCars->fetch_assoc()): ?>
                            <option value="<?= $car['id'] ?>"><?= htmlspecialchars($car['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-success w-100">Добавить автомобиль</button>
                </div>
            </form>
        </div>

        <div class="mb-5">
            <h4 class="section-title">Добавить команду гонщику</h4>
            <form method="POST" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <select name="team_id" class="form-select">
                        <?php while ($team = $allTeams->fetch_assoc()): ?>
                            <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-success w-100">Добавить команду</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

</div>
</body>
</html>
