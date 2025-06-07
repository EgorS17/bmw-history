<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $birthdate = $_POST['birthdate'];
    $nationality = trim($_POST['nationality']);
    $biography = trim($_POST['biography']);

    // Загрузка изображения
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetDirectory = 'assets/images/';
        $targetPath = $targetDirectory . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $fileName;
        } else {
            die('Ошибка загрузки файла.');
        }
    }

    // Вставка в базу
    $stmt = $conn->prepare("INSERT INTO drivers (name, biography, birthdate, nationality, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $biography, $birthdate, $nationality, $image);
    $stmt->execute();
    $stmt->close();

    header('Location: drivers.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить гонщика</title>
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
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">BMW Motorsport</a>
        <a href="drivers.php" class="btn btn-outline-light ms-auto">← Назад к гонщикам</a>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="mb-4">Добавить нового гонщика</h1>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Имя гонщика *</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Фотография</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Дата рождения</label>
            <input type="date" name="birthdate" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Национальность</label>
            <input type="text" name="nationality" class="form-control">
        </div>

        <div class="col-12">
            <label class="form-label">Биография</label>
            <textarea name="biography" class="form-control" rows="5"></textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Сохранить гонщика</button>
            <a href="drivers.php" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

</body>
</html>