<?php
header("Content-Type: application/json");
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Non connecté"]);
    exit;
}

require_once __DIR__ . "/../../config/database.php";

$db = new Database();
$conn = $db->connect();

$course_id = $_GET["course_id"] ?? null;

if (!$course_id) {
    echo json_encode(["success" => false, "message" => "ID cours manquant"]);
    exit;
}

$query = $conn->prepare("
    SELECT id, date, start_time, end_time
    FROM attendance_sessions
    WHERE course_id = :id
    ORDER BY date DESC
");
$query->execute([":id" => $course_id]);

$sessions = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "data" => $sessions]);
exit;
