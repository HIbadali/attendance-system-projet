<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 2) {
    Response::error("Accès refusé.");
}

$professor_id = $_SESSION["user_id"];

$db = new Database();
$conn = $db->connect();

$query = $conn->prepare("
    SELECT id, title, code
    FROM courses
    WHERE professor_id = :pid
");
$query->execute([":pid" => $professor_id]);

$courses = $query->fetchAll(PDO::FETCH_ASSOC);

Response::success($courses, "Cours récupérés.");
