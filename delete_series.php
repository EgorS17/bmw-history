<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Проверка — есть ли автомобили, привязанные к этой серии
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM car_series WHERE series_id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkStmt->bind_result($carCount);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($carCount > 0) {
        die('Ошибка: Нельзя удалить серию, к которой привязаны автомобили.');
    }

    // Удаляем серию
    $stmt = $conn->prepare("DELETE FROM series WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: series.php');
exit();
?>