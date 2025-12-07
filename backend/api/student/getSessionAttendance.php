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

$session_id = $_GET["session_id"] ?? null;
$student_id = $_SESSION["user_id"];

if (!$session_id) {
    echo json_encode(["success" => false, "message" => "Session manquante"]);
    exit;
}

$query = $conn->prepare("
    SELECT status, justification
    FROM attendance_records
    WHERE session_id = :sid AND student_id = :uid
");
$query->execute([
    ":sid" => $session_id,
    ":uid" => $student_id
]);

$data = $query->fetch(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "data" => $data]);
exit;
