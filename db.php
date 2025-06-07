<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "bmw_sports";

// Создаем соединение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
  die("Ошибка подключения: " . $conn->connect_error);
}
?>