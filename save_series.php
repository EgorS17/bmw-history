<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    // Получаем текущее изображение
    $res = mysqli_query($conn, "SELECT image FROM series WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
    $image = $row['image'];

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

    $stmt = $conn->prepare("UPDATE series SET name = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $description, $image, $id);
    $stmt->execute();

    header("Location: series.php");
    exit();
}
?>
