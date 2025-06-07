<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = (int)$_POST['year'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $decade = floor($year / 10) * 10;

    // Загрузка изображения
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetDir = 'assets/images/';
        $targetPath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $fileName;
        } else {
            die("Ошибка загрузки изображения.");
        }
    }

    $stmt = $conn->prepare("INSERT INTO events (year, decade, title, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $year, $decade, $title, $description, $image);
    $stmt->execute();
    $stmt->close();

    header("Location: timeline.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить событие</title>
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
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">BMW Motorsport</a>
        <a href="timeline.php" class="btn btn-outline-light ms-auto">← Назад к хронологии</a>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="mb-4">Добавить историческое событие</h1>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Год события *</label>
            <input type="number" name="year" class="form-control" placeholder="например, 1999" required>
        </div>

        <div class="col-md-8">
            <label class="form-label">Заголовок события *</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="col-12">
            <label class="form-label">Описание события</label>
            <textarea name="description" class="form-control" rows="5"></textarea>
        </div>

        <div class="col-12">
            <label class="form-label">Фото события</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Сохранить событие</button>
            <a href="timeline.php" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

</body>
</html>