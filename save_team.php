<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    die("Доступ запрещен");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    // Проверка, загружено ли новое изображение
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'assets/images/';
        $filename = basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            // Обновление с новым изображением
            $stmt = $conn->prepare("UPDATE teams SET name = ?, description = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $description, $filename, $id);
        } else {
            die("Ошибка при загрузке изображения.");
        }
    } else {
        // Обновление без изменения изображения
        $stmt = $conn->prepare("UPDATE teams SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
    }

    if ($stmt->execute()) {
        header("Location: teams.php");
        exit();
    } else {
        die("Ошибка при сохранении данных.");
    }
} else {
    die("Неверный метод запроса.");
}
