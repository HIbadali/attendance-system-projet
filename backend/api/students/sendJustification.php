<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../../config/database.php";

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Non connecté"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

$student_id = $_SESSION["user_id"];
$data = json_decode(file_get_contents("php://input"), true);

$session_id = $data["session_id"] ?? null;
$justification = $data["justification"] ?? null;

if (!$session_id || !$justification) {
    echo json_encode(["success" => false, "message" => "Champs manquants"]);
    exit;
}

$query = $conn->prepare("
    UPDATE attendance_records
    SET justification = :j
    WHERE student_id = :sid AND session_id = :sess
");

$query->execute([
    ":j" => $justification,
    ":sid" => $student_id,
    ":sess" => $session_id
]);

echo json_encode(["success" => true, "message" => "Justification envoyée"]);
exit;
