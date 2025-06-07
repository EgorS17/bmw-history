<?php
session_start();
include 'db.php';

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];

$query = "SELECT * FROM drivers ORDER BY name";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Гонщики BMW</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/drivers.css">
    <style>
        .navbar-toggler-icon {
            width: 1.5em;
            height: 1.5em;
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%28255,255,255,0.7%29' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 100% 100%;
        }
        .card-img-top {
            height: 250px;
            object-fit: cover;
        }
        .card {
            height: 100%;
        }
        @media (max-width: 576px) {
            h1 {
                font-size: 1.75rem;
                text-align: center;
            }
            .card-title {
                font-size: 1.1rem;
            }
            .card-body p {
                font-size: 0.9rem;
            }
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

<div class="container mt-5">
    <h1 class="mb-4">Известные гонщики BMW</h1>

    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($driver = $result->fetch_assoc()): ?>
                <div class="col-md-4 col-sm-6 col-12 mb-4">
                    <a href="driver_detail.php?id=<?= $driver['id'] ?>" class="card-link w-100">
                        <div class="card">
                            <div class="img-wrapper">
                                <img src="assets/images/<?= htmlspecialchars($driver['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($driver['name']) ?>">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($driver['name']) ?></h5>
                                <p><strong>Национальность:</strong> <?= htmlspecialchars($driver['nationality']) ?></p>
                                <?php
                                    $birth = new DateTime($driver['birthdate']);
                                    $birth_formatted = $birth->format('d.m.Y');
                                ?>
                                <p><strong>Дата рождения:</strong> <?= $birth_formatted ?></p>

                                <?php if ($isAdmin): ?>
                                    <div class="admin-buttons d-flex flex-column gap-2">
                                        <a href="edit_driver.php?id=<?= $driver['id'] ?>" class="btn btn-outline-warning btn-sm" onclick="event.stopPropagation();">Редактировать</a>
                                        <a href="delete_driver.php?id=<?= $driver['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="event.stopPropagation(); return confirm('Удалить гонщика?')">Удалить</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Гонщики не найдены.</p>
        <?php endif; ?>
    </div>
</div>
<div class="footer text-center mt-5 mb-3 text-muted">
    <p>© <?= date('Y') ?> Спортивная история BMW</p>
</div>
</body>
</html>
