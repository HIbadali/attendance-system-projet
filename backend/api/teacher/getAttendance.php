<?php
header("Content-Type: application/json");

require_once "../../config/database.php";
require_once "../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "professor") {
    Response::error("Accès refusé.", 401);
}

if (!isset($_GET["session_id"])) {
    Response::error("session_id manquant.");
}

$session_id = intval($_GET["session_id"]);

$db = new Database();
$conn = $db->connect();

// Récupérer les présences
$query = $conn->prepare("
    SELECT student_id, status 
    FROM attendance_records 
    WHERE session_id = :session_id
");

$query->execute([":session_id" => $session_id]);

$records = $query->fetchAll(PDO::FETCH_ASSOC);

Response::success($records, "Présence récupérée.");
