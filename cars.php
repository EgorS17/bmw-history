<?php
session_start();
include 'db.php';

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];

$search = $_GET['search'] ?? '';
$year_from = (int)($_GET['year_from'] ?? 1960);
$year_to = (int)($_GET['year_to'] ?? 2024);
$view = ($_GET['view'] ?? 'grid') === 'table' ? 'table' : 'grid';

$query = "SELECT * FROM cars WHERE 1";
$params = [];
$types = '';

if ($search) {
    $query .= " AND (name LIKE ? OR model LIKE ? OR year LIKE ?)";
    $searchTerm = "%" . $search . "%";
    $params[] = &$searchTerm;
    $params[] = &$searchTerm;
    $params[] = &$searchTerm;
    $types .= 'sss';
}

if ($year_from && $year_to) {
    $query .= " AND year BETWEEN ? AND ?";
    $params[] = &$year_from;
    $params[] = &$year_to;
    $types .= 'ii';
}

$query .= " ORDER BY year ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    array_unshift($params, $types);
    call_user_func_array([$stmt, 'bind_param'], $params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Гоночные автомобили BMW</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>
    <link rel="stylesheet" href="assets/css/cars.css">
    <style>
        .hero {
            background: url('assets/images/hero.avif') no-repeat center center;
            background-size: cover;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 0 1rem;
            text-shadow: 0 0 10px rgba(0,0,0,0.8);
        }
        .hero h1 {
            font-size: 2rem;
        }
        @media (min-width: 576px) {
            .hero {
                height: 300px;
            }
            .hero h1 {
                font-size: 2.5rem;
            }
        }
        .navbar-toggler-icon {
            width: 1.5em;
            height: 1.5em;
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%28255,255,255,0.7%29' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 100% 100%;
        }
        .card-img-top {
            object-fit: cover;
            height: 200px;
        }
        @media (max-width: 575.98px) {
            .card-body {
                font-size: 0.9rem;
            }
            .card-title {
                font-size: 1.1rem;
            }
        }
        td {
            word-break: break-word;
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
                    <a href="add_car.php" class="btn btn--danger-subtle">Добавить авто</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="hero">
    <h1>Гоночные автомобили BMW</h1>
</div>

<div class="container mt-5">
    <form method="GET" action="cars.php" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Поиск по имени, модели или году" value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="view" class="form-select" onchange="this.form.submit()">
                <option value="grid" <?= $view === 'grid' ? 'selected' : '' ?>>Карточки</option>
                <option value="table" <?= $view === 'table' ? 'selected' : '' ?>>Таблица</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Поиск</button>
        </div>
        <div class="col-12">
            <div id="year-slider"></div>
            <input type="hidden" id="year_from" name="year_from" value="<?= htmlspecialchars($year_from) ?>">
            <input type="hidden" id="year_to" name="year_to" value="<?= htmlspecialchars($year_to) ?>">
        </div>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <?php if ($view === 'grid'): ?>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4 d-flex">
                        <div class="card h-100 d-flex flex-column w-100 bg-dark text-white">
                            <a href="cars_detail.php?id=<?= $row['id'] ?>" class="text-decoration-none text-reset">
                                <img src="assets/images/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
                                    <p><strong>Модель:</strong> <?= htmlspecialchars($row['model']) ?></p>
                                    <p><strong>Год:</strong> <?= htmlspecialchars($row['year']) ?></p>
                                </div>
                            </a>
                            <?php if ($isAdmin): ?>
                                <div class="card-footer mt-auto d-flex flex-column gap-2 p-3">
                                    <a href="edit_car.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning w-100"> Редактировать</a>
                                    <a href="delete_car.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Удалить?')"> Удалить</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-dark table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Модель</th>
                            <th>Год</th>
                            <th>Описание</th>
                            <?php if ($isAdmin): ?>
                                <th>Действия</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php mysqli_data_seek($result, 0); ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['model']) ?></td>
                                <td><?= htmlspecialchars($row['year']) ?></td>
                                <td><?= htmlspecialchars(mb_strimwidth($row['description'], 0, 120, '...')) ?></td>
                                <?php if ($isAdmin): ?>
                                    <td>
                                        <a href="edit_car.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning me-1">✏️</a>
                                        <a href="delete_car.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?')">🗑</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-center">Автомобили не найдены.</p>
    <?php endif; ?>
</div>

<div class="footer text-center mt-5 mb-3 text-muted">
    <p>© <?= date('Y') ?> Спортивная история BMW</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var yearSlider = document.getElementById('year-slider');
    var inputYearFrom = document.getElementById('year_from');
    var inputYearTo = document.getElementById('year_to');

    noUiSlider.create(yearSlider, {
        start: [<?= $year_from ?>, <?= $year_to ?>],
        connect: true,
        step: 1,
        range: { 'min': 1960, 'max': 2024 },
        tooltips: [true, true],
        format: {
            to: value => Math.round(value),
            from: value => Math.round(value)
        }
    });

    yearSlider.noUiSlider.on('update', function(values, handle) {
        inputYearFrom.value = values[0];
        inputYearTo.value = values[1];
    });
});
</script>

</body>
</html>