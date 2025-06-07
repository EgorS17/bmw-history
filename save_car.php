<?php
include 'db.php';

$name = $_POST['name'];
$model = $_POST['model'];
$year = $_POST['year'];
$description = $_POST['description'];
$image = $_POST['image'];

$sql = "INSERT INTO cars (name, model, year, description, image)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiss", $name, $model, $year, $description, $image);

if ($stmt->execute()) {
    header("Location: cars.php");
    exit();
} else {
    echo "Ошибка: " . $stmt->error;
}
?>