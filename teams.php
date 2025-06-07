<?php
session_start();
require 'db.php';

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];
$teams = $conn->query("SELECT * FROM teams ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Команды BMW Motorsport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

.hero h1 {
    font-size: 3rem;
    font-weight: bold;
}

.card {
    background-color: #161b22;
    border: none;
    color: #ccc;
    border-radius: 16px;
    overflow: hidden;
    transition: transform 0.3s;
    cursor: pointer;
    height: 100%;
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
.card-title {
    color: #58a6ff;
    font-weight: bold;
}
.card-text {
    flex-grow: 1;
    word-break: break-word;
}
.table th, .table td {
    vertical-align: middle;
}
.btn-outline-warning, .btn-outline-danger {
    width: 48%;
}
.form-control, .form-select {
    background-color: #0d1117;
    color: #e6e6e6;
    border: 1px solid #333;
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
@media (max-width: 576px) {
    .card-title {
        font-size: 1rem;
    }
    .card-text {
        font-size: 0.9rem;
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="section-title mb-4">Команды BMW</h1>

    <div class="row g-4">
        <?php while ($team = $teams->fetch_assoc()): ?>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 d-flex flex-column team-card" data-id="<?= $team['id'] ?>">
                    <img src="assets/images/<?= htmlspecialchars($team['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($team['name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($team['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($team['description']) ?></p>

                        <?php if ($isAdmin): ?>
                            <div class="mt-auto d-flex flex-column gap-2">
                                <a href="edit_team.php?id=<?= $team['id'] ?>" class="btn btn-sm btn-outline-warning w-100">Редактировать</a>
                                <a href="delete_team.php?id=<?= $team['id'] ?>" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Удалить команду?');">Удалить</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    document.querySelectorAll('.team-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('a, button')) return;
            const id = this.dataset.id;
            window.location.href = `team_detail.php?id=${id}`;
        });
    });
</script>

</body>
</html>
