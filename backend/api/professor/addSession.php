<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";
session_start();

$data = json_decode(file_get_contents("php://input"), true);

$course_id = $data["course_id"] ?? null;
$date      = $data["date"] ?? null;

if (!$course_id || !$date) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

try {
    $db = new Database();
    $conn = $db->connect();

    // Si session_date est un DATETIME, MySQL acceptera 'YYYY-MM-DD' (= 00:00:00)
    $sql = "INSERT INTO attendance_sessions (course_id, session_date)
            VALUES (:cid, :sdate)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":cid"   => $course_id,
        ":sdate" => $date
    ]);

    echo json_encode(["success" => true, "message" => "Session created"]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
