<?php
header("Content-Type: application/json");

require_once "../../config/database.php";
require_once "../../config/response.php";

session_start();

// Vérification professeur connecté
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "professor") {
    Response::error("Accès refusé.", 401);
}

$professor_id = $_SESSION["user_id"];

// Vérifier si course_id est fourni
if (!isset($_GET["course_id"])) {
    Response::error("course_id manquant.");
}

$course_id = intval($_GET["course_id"]);

$db = new Database();
$conn = $db->connect();

// Vérifier que le professeur possède bien CE cours
$check = $conn->prepare("
    SELECT id FROM courses 
    WHERE id = :course_id AND professor_id = :professor_id
");

$check->execute([
    ":course_id" => $course_id,
    ":professor_id" => $professor_id
]);

if ($check->rowCount() === 0) {
    Response::error("Ce cours ne vous appartient pas.");
}

// Récupérer toutes les sessions du cours
$query = $conn->prepare("
    SELECT id, session_date, status 
    FROM attendance_sessions 
    WHERE course_id = :course_id
    ORDER BY session_date DESC
");

$query->execute([":course_id" => $course_id]);

$sessions = $query->fetchAll(PDO::FETCH_ASSOC);

// Réponse finale
Response::success($sessions, "Sessions récupérées.");
