<?php
include 'db.php';

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$event_id) {
    die("Событие не найдено.");
}

// Получаем существующее событие
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    die("Событие не найдено.");
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = (int)$_POST['year'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $decade = floor($year / 10) * 10;
    $image = $event['image'];

    // Если загружено новое изображение
    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = 'assets/images/' . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $fileName;
        } else {
            die("Ошибка загрузки изображения.");
        }
    }

    // Обновляем запись
    $stmt = $conn->prepare("UPDATE events SET year = ?, decade = ?, title = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bind_param("iisssi", $year, $decade, $title, $description, $image, $event_id);
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
    <title>Редактировать событие</title>
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
            margin-bottom: 15px;
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
        <a href="timeline.php" class="btn btn-outline-light ms-auto">← Назад к хронологии</a>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="mb-4">Редактировать событие</h1>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Год события *</label>
            <input type="number" name="year" class="form-control" value="<?= htmlspecialchars($event['year']) ?>" required>
        </div>

        <div class="col-md-8">
            <label class="form-label">Заголовок события *</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required>
        </div>

        <div class="col-12">
            <label class="form-label">Описание</label>
            <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($event['description']) ?></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Текущее изображение</label><br>
            <?php if ($event['image']): ?>
                <img src="assets/images/<?= htmlspecialchars($event['image']) ?>" class="preview" alt="image">
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
            <a href="timeline.php" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

</body>
</html>