<?php
require 'db.php';

$series_id = isset($_GET['series_id']) ? (int)$_GET['series_id'] : 0;

if (!$series_id) {
    echo "Серия не найдена.";
    exit;
}

// Получаем список всех автомобилей, которые ещё не привязаны к этой серии
$query = "
    SELECT c.id, c.name
    FROM cars c
    LEFT JOIN car_series cs ON c.id = cs.car_id AND cs.series_id = ?
    WHERE cs.car_id IS NULL
";
$stmt_list = $conn->prepare($query);
$stmt_list->bind_param("i", $series_id);
$stmt_list->execute();
$cars_result = $stmt_list->get_result();

// Обработка формы добавления автомобиля
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $car_id = (int)$_POST['car_id'];

    if ($car_id) {
        $stmt_insert = $conn->prepare("INSERT INTO car_series (car_id, series_id) VALUES (?, ?)");
        $stmt_insert->bind_param("ii", $car_id, $series_id);
        $stmt_insert->execute();
        $stmt_insert->close();

        header("Location: series_detail.php?id=" . $series_id);
        exit();
    } else {
        echo "Ошибка: необходимо выбрать автомобиль.";
    }
}

$stmt_list->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить автомобиль в серию</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">BMW Motorsport</a>
        <a href="series.php" class="btn btn-outline-light ms-auto">← Назад</a>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="mb-4">Добавить автомобиль в гоночную серию</h1>

    <form action="add_car_to_series.php?series_id=<?= $series_id ?>" method="POST">
        <div class="mb-3">
            <label for="car_id" class="form-label">Выберите автомобиль</label>
            <select id="car_id" name="car_id" class="form-select" required>
                <option value="">Выберите автомобиль</option>
                <?php while ($car = $cars_result->fetch_assoc()): ?>
                    <option value="<?= $car['id'] ?>"><?= htmlspecialchars($car['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Добавить автомобиль</button>
    </form>
</div>

</body>
</html>