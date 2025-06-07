<?php
session_start();
include 'db.php';

// Проверка прав администратора
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    die("Доступ запрещён.");
}

// Проверка наличия ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Некорректный ID гонщика.");
}

$id = (int)$_GET['id'];

// Получаем информацию о гонщике (для удаления изображения)
$stmt = $conn->prepare("SELECT image FROM drivers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();

if (!$driver) {
    die("Гонщик не найден.");
}

// Удаление изображения, если существует
if (!empty($driver['image']) && file_exists("assets/images/" . $driver['image'])) {
    unlink("assets/images/" . $driver['image']);
}

// Удаляем запись из базы
$deleteStmt = $conn->prepare("DELETE FROM drivers WHERE id = ?");
$deleteStmt->bind_param("i", $id);
$deleteStmt->execute();

header("Location: drivers.php");
exit();
