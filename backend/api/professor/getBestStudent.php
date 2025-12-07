<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";
session_start();

$course = $_GET["course_id"] ?? null;

if (!$course) {
    echo json_encode(["success" => false]);
    exit;
}

try {
    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("
        SELECT s.name, s.lastname 
        FROM best_students b
        JOIN students s ON s.id = b.student_id
        WHERE b.course_id = ?
        LIMIT 1
    ");
    $stmt->execute([$course]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "data" => $row]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
