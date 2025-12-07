<?php
header("Content-Type: application/json");

require_once "../../config/database.php";
require_once "../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    Response::error("Accès refusé.", 401);
}

if (!isset($_GET["session_id"])) {
    Response::error("session_id manquant.");
}

$student_id = $_SESSION["user_id"];
$session_id = intval($_GET["session_id"]);

$db = new Database();
$conn = $db->connect();

/*
    Vérifie si l’étudiant a un enregistrement de présence pour la session
*/
$query = $conn->prepare("
    SELECT status 
    FROM attendance_records
    WHERE student_id = :student_id
      AND session_id = :session_id
");

$query->execute([
    ":student_id" => $student_id,
    ":session_id" => $session_id
]);

$attendance = $query->fetch(PDO::FETCH_ASSOC);

if (!$attendance) {
    $attendance = ["status" => "absent"]; // par défaut
}

Response::success($attendance, "Présence récupérée.");
?>
