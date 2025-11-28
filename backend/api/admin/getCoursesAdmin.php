<?php
header("Content-Type: application/json");

require_once "../../config/database.php";
require_once "../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    Response::error("Accès refusé.", 401);
}

$db = new Database();
$conn = $db->connect();

$query = $conn->prepare("
    SELECT c.id, c.title, c.code,
           u.first_name AS prof_first, u.last_name AS prof_last
    FROM courses c
    LEFT JOIN users u ON u.id = c.professor_id
    ORDER BY c.title ASC
");

$query->execute();
$courses = $query->fetchAll(PDO::FETCH_ASSOC);

Response::success($courses, "Cours récupérés.");
?>
