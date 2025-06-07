<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $birthdate = $_POST['birthdate'];
    $nationality = trim($_POST['nationality']);
    $biography = trim($_POST['biography']);

    // Текущее изображение
    $res = mysqli_query($conn, "SELECT image FROM drivers WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
    $image = $row['image'];

    // Новое изображение
    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = 'assets/images/' . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $fileName;
        } else {
            die("Ошибка загрузки изображения.");
        }
    }

    $stmt = $conn->prepare("UPDATE drivers SET name = ?, biography = ?, birthdate = ?, nationality = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $name, $biography, $birthdate, $nationality, $image, $id);
    $stmt->execute();

    header("Location: drivers.php");
    exit();
}
?>
