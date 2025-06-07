<?php
require 'db.php';

if (isset($_GET['series_id']) && isset($_GET['car_id'])) {
    $series_id = (int)$_GET['series_id'];
    $car_id = (int)$_GET['car_id'];

    $stmt = $conn->prepare("DELETE FROM car_series WHERE car_id = ? AND series_id = ?");
    $stmt->bind_param("ii", $car_id, $series_id);
    $stmt->execute();
    $stmt->close();
}

header('Location: series_detail.php?id=' . (int)$_GET['series_id']);
exit();
?>
