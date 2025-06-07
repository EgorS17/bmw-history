<?php
include 'db.php';

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id) {
    // Удаляем событие и все связи (ON DELETE CASCADE работает)
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: timeline.php");
exit();