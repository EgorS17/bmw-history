<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM drivers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $driver = $result->fetch_assoc();

    if (!$driver) {
        die("Гонщик не найден.");
    }
} else {
    die("Не указан id гонщика.");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать гонщика</title>
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
        img.preview {
            max-width: 300px;
            border-radius: 8px;
            margin-top: 15px;
            border: 1px solid #444;
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
    <h1 class="mb-4">Редактировать гонщика</h1>

    <form method="POST" action="save_driver.php" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="id" value="<?= $driver['id'] ?>">

        <div class="col-md-6">
            <label class="form-label">Имя *</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($driver['name']) ?>" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Дата рождения *</label>
            <input type="date" name="birthdate" class="form-control" value="<?= htmlspecialchars($driver['birthdate']) ?>" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Национальность</label>
            <input type="text" name="nationality" class="form-control" value="<?= htmlspecialchars($driver['nationality']) ?>">
        </div>

        <div class="col-12">
            <label class="form-label">Биография</label>
            <textarea name="biography" class="form-control" rows="5"><?= htmlspecialchars($driver['biography']) ?></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Текущее изображение</label><br>
            <?php if ($driver['image']): ?>
                <img src="assets/images/<?= htmlspecialchars($driver['image']) ?>" class="preview" alt="image">
            <?php else: ?>
                <p class="text-muted">Нет изображения</p>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label">Загрузить новое изображение</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="drivers.php" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

</body>
</html>
