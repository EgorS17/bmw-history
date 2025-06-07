<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $model = $_POST['model'];
  $year = $_POST['year'];
  $description = $_POST['description']; 
  $image = $_POST['image'];

  $stmt = $conn->prepare("INSERT INTO cars (name, model, year, description, image) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("ssiss", $name, $model, $year, $description, $image);
  $stmt->execute();
  $stmt->close();

  header("Location: cars.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Добавить автомобиль</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h1>Добавить спортивный автомобиль BMW</h1>
  <form method="post">
    <div class="mb-3">
      <label for="name" class="form-label">Название</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="model" class="form-label">Модель</label>
      <input type="text" name="model" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="year" class="form-label">Год выпуска</label>
      <input type="number" name="year" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">Описание</label>
      <textarea name="description" class="form-control" rows="3" required></textarea>
    </div>
    <div class="mb-3">
      <label for="image" class="form-label">Ссылка на изображение</label>
      <input type="text" name="image" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Добавить</button>
    <a href="cars.php" class="btn btn-secondary">Назад к списку</a>
  </form>
</body>
</html>