<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Удаление записи из базы данных
    $deleteQuery = "DELETE FROM cars WHERE id = $id";
    if (mysqli_query($conn, $deleteQuery)) {
        header('Location: cars.php');
        exit;
    } else {
        echo "Ошибка при удалении автомобиля.";
    }
}   