<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 2) {
    Response::error("Accès refusé.");
}

$data = json_decode(file_get_contents("php://input"), true);

$course_id = $data["course_id"] ?? null;
$session_date = $data["session_date"] ?? null;

if (!$course_id || !$session_date) {
    Response::error("Champs manquants !");
}

$db = new Database();
$conn = $db->connect();

$query = $conn->prepare("
    INSERT INTO attendance_sessions(course_id, session_date)
    VALUES (:cid, :date)
");

$query->execute([
    ":cid" => $course_id,
    ":date" => $session_date
]);

Response::success([], "Session créée !");
