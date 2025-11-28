<?php
header("Content-Type: application/json");

require_once "../../config/database.php";
require_once "../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    Response::error("Accès refusé.", 401);
}

if (!isset($_GET["course_id"])) {
    Response::error("course_id manquant.");
}

$course_id = intval($_GET["course_id"]);

$db = new Database();
$conn = $db->connect();

// Récupérer les sessions du cours
$query = $conn->prepare("
    SELECT id, session_date, status
    FROM attendance_sessions
    WHERE course_id = :course_id
    ORDER BY session_date DESC
");

$query->execute([":course_id" => $course_id]);

$sessions = $query->fetchAll(PDO::FETCH_ASSOC);

Response::success($sessions, "Sessions récupérées.");
?>
