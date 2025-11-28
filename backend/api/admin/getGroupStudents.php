<?php
header("Content-Type: application/json");

require_once "../../config/database.php";
require_once "../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    Response::error("Accès refusé.", 401);
}

if (!isset($_GET["group_id"])) {
    Response::error("group_id manquant.");
}

$group_id = intval($_GET["group_id"]);

$db = new Database();
$conn = $db->connect();

$query = $conn->prepare("
    SELECT u.id, u.first_name, u.last_name, u.email
    FROM users u
    JOIN student_groups sg ON sg.student_id = u.id
    WHERE sg.group_id = :group_id
");

$query->execute([":group_id" => $group_id]);

$students = $query->fetchAll(PDO::FETCH_ASSOC);

Response::success($students, "Étudiants du groupe récupérés.");
?>
