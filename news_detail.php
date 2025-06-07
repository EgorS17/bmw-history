<?php
session_start();
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    die("Не указана новость");
}

// Получаем новость
$stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$news = $stmt->get_result()->fetch_assoc();
if (!$news) {
    die("Новость не найдена");
}

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];

// Привязка сущностей
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    foreach (['car', 'driver', 'team', 'series'] as $type) {
        if (!empty($_POST["{$type}_id"])) {
            $relatedId = (int)$_POST["{$type}_id"];
            $stmt = $conn->prepare("INSERT IGNORE INTO article_{$type} (article_id, {$type}_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $id, $relatedId);
            $stmt->execute();
        }
    }
    header("Location: news_detail.php?id=" . $id);
    exit;
}

// Получаем связанные сущности
function getLinked($conn, $type, $article_id) {
    $tableMap = [
        'car' => 'cars',
        'driver' => 'drivers',
        'team' => 'teams',
        'series' => 'series'
    ];
    $table = $tableMap[$type] ?? '';
    if (!$table) return [];

    $result = $conn->query("
        SELECT t.* FROM $table t 
        JOIN article_{$type} at ON at.{$type}_id = t.id 
        WHERE at.article_id = $article_id
    ");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$cars = getLinked($conn, 'car', $id);
$drivers = getLinked($conn, 'driver', $id);
$teams = getLinked($conn, 'team', $id);
$series = getLinked($conn, 'series', $id);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($news['title']) ?></title>
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
            margin-top: 50px;
            margin-bottom: 30px;
            text-align: center;
        }
        .card {
            background-color: #161b22;
            border: none;
            border-radius: 16px;
            color: #ccc;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card-title {
            color: #58a6ff;
            font-size: 1.2rem;
        }
        .btn-outline-primary {
            color: #58a6ff;
            border-color: #58a6ff;
        }
        .btn-outline-primary:hover {
            background-color: #58a6ff;
            color: #0d1117;
        }
        .form-select {
            background-color: #0d1117;
            color: #e6e6e6;
            border: 1px solid #333;
        }
        .form-label {
            color: #ccc;
        }
        .news-image {
            max-height: 400px;
            object-fit: cover;
            border-radius: 16px;
            margin-bottom: 20px;
        }
        hr {
            border-color: #333;
        }
        .navbar {
            background-color: #161b22;
            padding: 0.3rem 0;
            border-bottom: 1px solid #30363d;
        }
        .navbar-brand {
            font-weight: bold;
            color: #58a6ff;
            font-size: 1.25rem;
            line-height: 1.2;
            padding: 0;
            margin: 0;
            text-decoration: none;
        }
        .navbar-brand:hover {
            color: #ffffff !important;
        }
        .card-img-top {
            height: 180px;
            object-fit: cover;
            border-radius: 16px 16px 0 0;
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
            width: 100%;
            height: auto;
            object-fit: cover;
            object-position: top;
            display: block;
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
    <h1 class="mb-3"><?= htmlspecialchars($news['title']) ?></h1>
    <p><?= date('d.m.Y H:i', strtotime($news['created_at'])) ?></p>
    <img src="assets/images/<?= htmlspecialchars($news['image']) ?>" class="img-fluid news-image" alt="<?= htmlspecialchars($news['title']) ?>">
    <div class="mb-5"><?= nl2br(htmlspecialchars($news['content'])) ?></div>

    <?php if (!empty($cars)): ?>
    <h2 class="section-title">Связанные автомобили</h2>
    <div class="row g-4">
        <?php foreach ($cars as $car): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="cars_detail.php?id=<?= $car['id'] ?>" class="card h-100 text-center text-decoration-none text-reset">
                    <?php if (!empty($car['image'])): ?>
                        <img src="assets/images/<?= htmlspecialchars($car['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($car['name']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($car['name']) ?></h5>
                        <?php if (!empty($car['model'])): ?>
                            <p class="card-text"><strong>Модель:</strong> <?= htmlspecialchars($car['model']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($car['year'])): ?>
                            <p class="card-text"><strong>Год:</strong> <?= htmlspecialchars($car['year']) ?></p>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($drivers)): ?>
    <h2 class="section-title">Связанные гонщики</h2>
    <div class="row g-4">
        <?php foreach ($drivers as $driver): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="driver_detail.php?id=<?= $driver['id'] ?>" class="card h-100 text-center text-decoration-none text-reset">
                    <?php if (!empty($driver['image'])): ?>
                        <div class="driver-img-wrapper">
                            <img src="assets/images/<?= htmlspecialchars($driver['image']) ?>" class="driver-img" alt="<?= htmlspecialchars($driver['name']) ?>">
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($driver['name']) ?></h5>
                        <?php if (!empty($driver['nationality'])): ?>
                            <p class="card-text"><strong>Национальность:</strong> <?= htmlspecialchars($driver['nationality']) ?></p>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($teams)): ?>
    <h2 class="section-title">Связанные команды</h2>
    <div class="row g-4">
        <?php foreach ($teams as $team): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="team_detail.php?id=<?= $team['id'] ?>" class="card h-100 text-center text-decoration-none text-reset">
                    <?php if (!empty($team['image'])): ?>
                        <img src="assets/images/<?= htmlspecialchars($team['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($team['name']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($team['name']) ?></h5>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($series)): ?>
    <h2 class="section-title">Связанные серии</h2>
    <div class="row g-4">
        <?php foreach ($series as $ser): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="series_detail.php?id=<?= $ser['id'] ?>" class="card h-100 text-center text-decoration-none text-reset">
                    <?php if (!empty($ser['image'])): ?>
                        <img src="assets/images/<?= htmlspecialchars($ser['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($ser['name']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($ser['name']) ?></h5>
                        <?php if (!empty($ser['description'])): ?>
                            <p class="card-text"><?= htmlspecialchars($ser['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


    <?php if ($isAdmin): ?>
        <hr>
        <h2 class="section-title">Привязать сущности к новости</h2>

        <!-- Автомобиль -->
        <div class="mb-5">
            <h4 class="section-title">Добавить автомобиль</h4>
            <form method="POST" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <select name="car_id" class="form-select">
                        <?php
                        $res = $conn->query("SELECT id, name FROM cars");
                        while ($car = $res->fetch_assoc()):
                        ?>
                            <option value="<?= $car['id'] ?>"><?= htmlspecialchars($car['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-success w-100">Добавить автомобиль</button>
                </div>
            </form>
        </div>

        <!-- Гонщик -->
        <div class="mb-5">
            <h4 class="section-title">Добавить гонщика</h4>
            <form method="POST" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <select name="driver_id" class="form-select">
                        <?php
                        $res = $conn->query("SELECT id, name FROM drivers");
                        while ($driver = $res->fetch_assoc()):
                        ?>
                            <option value="<?= $driver['id'] ?>"><?= htmlspecialchars($driver['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-success w-100">Добавить гонщика</button>
                </div>
            </form>
        </div>

        <!-- Команда -->
        <div class="mb-5">
            <h4 class="section-title">Добавить команду</h4>
            <form method="POST" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <select name="team_id" class="form-select">
                        <?php
                        $res = $conn->query("SELECT id, name FROM teams");
                        while ($team = $res->fetch_assoc()):
                        ?>
                            <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-success w-100">Добавить команду</button>
                </div>
            </form>
        </div>

        <!-- Серия -->
        <div class="mb-5">
            <h4 class="section-title">Добавить серию</h4>
            <form method="POST" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <select name="series_id" class="form-select">
                        <?php
                        $res = $conn->query("SELECT id, name FROM series");
                        while ($series = $res->fetch_assoc()):
                        ?>
                            <option value="<?= $series['id'] ?>"><?= htmlspecialchars($series['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-success w-100">Добавить серию</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
