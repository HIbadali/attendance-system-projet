<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";

session_start();

$data = json_decode(file_get_contents("php://input"), true);

$session_id = $data["session_id"] ?? null;
$students   = $data["students"] ?? null;

if (!$session_id || !$students) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

// Remove previous attendance for that session
$conn->prepare("DELETE FROM attendance WHERE session_id = :sid")
     ->execute([":sid" => $session_id]);

// Insert new attendance rows
$q = $conn->prepare("
    INSERT INTO attendance (session_id, student_id, status)
    VALUES (:sid, :st, :state)
");

foreach ($students as $s) {
    $q->execute([
        ":sid"   => $session_id,
        ":st"    => $s["student_id"],
        ":state" => $s["status"]
    ]);
}

echo json_encode(["success" => true, "message" => "Attendance saved"]);
