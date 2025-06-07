<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $full_description = trim($_POST['full_description']);

    // Проверка обязательных полей
    if (empty($name)) {
        die('Ошибка: Название серии обязательно.');
    }

    // Обработка загрузки изображения
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $fileName = basename($_FILES['image']['name']);
        $targetDirectory = 'assets/images/';
        $targetPath = $targetDirectory . time() . '_' . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = basename($targetPath);
        } else {
            die('Ошибка при загрузке файла.');
        }
    }

    // Добавляем в базу данных
    $stmt = $conn->prepare("INSERT INTO series (name, description, full_description, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $description, $full_description, $image);
    $stmt->execute();
    $stmt->close();

    header('Location: series.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить серию</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<div class="container mt-5">
    <h1 class="mb-4">Добавить новую гоночную серию</h1>
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Название серии *</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Изображение (любой файл)</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Краткое описание</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Полное описание</label>
            <textarea name="full_description" class="form-control" rows="5"></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Добавить серию</button>
            <a href="series.php" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

</body>
</html>