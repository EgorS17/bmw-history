<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $query = "SELECT * FROM cars WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $car = mysqli_fetch_assoc($result);
    if (!$car) {
        die("Автомобиль не найден.");
    }
} else {
    die("Некорректный ID.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $model = trim($_POST['model']);
    $year = (int)$_POST['year'];
    $description = trim($_POST['description']);
    $image = $car['image'];

    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = 'assets/images/' . $fileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $fileName;
        } else {
            die("Ошибка загрузки изображения.");
        }
    }

    $updateQuery = "UPDATE cars SET name='$name', model='$model', year='$year', description='$description', image='$image' WHERE id=$id";
    if (mysqli_query($conn, $updateQuery)) {
        header('Location: cars.php');
        exit;
    } else {
        die("Ошибка обновления: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать автомобиль</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        input.form-control, textarea.form-control {
            background-color: #0d1117;
            color: #e6e6e6;
            border: 1px solid #333;
        }
        input::placeholder {
            color: #888;
        }
        img.preview {
            max-width: 300px;
            border-radius: 8px;
            margin-top: 15px;
            border: 1px solid #444;
        }
        .navbar-brand:hover {
            color: #ffffff !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">BMW Motorsport</a>
        <a href="cars.php" class="btn btn-outline-light ms-auto">← Назад к автомобилям</a>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="mb-4">Редактировать автомобиль</h1>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Название *</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($car['name']) ?>" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Модель *</label>
            <input type="text" name="model" class="form-control" value="<?= htmlspecialchars($car['model']) ?>" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Год *</label>
            <input type="number" name="year" class="form-control" value="<?= htmlspecialchars($car['year']) ?>" required>
        </div>

        <div class="col-12">
            <label class="form-label">Описание</label>
            <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($car['description']) ?></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Текущее изображение</label><br>
            <?php if ($car['image']): ?>
                <img src="assets/images/<?= htmlspecialchars($car['image']) ?>" class="preview" alt="image">
            <?php else: ?>
                <p class="text-muted">Нет изображения</p>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label">Загрузить новое изображение</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="cars.php" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

</body>
</html>
