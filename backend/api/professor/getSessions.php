<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];

$db = new Database();
$conn = $db->connect();

$sql = "SELECT s.id,
               s.session_date,
               c.code  AS course_code,
               c.title AS course_title
        FROM attendance_sessions s
        JOIN courses c ON c.id = s.course_id
        WHERE c.professor_id = :pid
        ORDER BY s.session_date DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([":pid" => $user_id]);

echo json_encode([
    "success" => true,
    "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
