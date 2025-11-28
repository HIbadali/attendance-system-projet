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

// Récupération des données envoyées en POST
$data = json_decode(file_get_contents("php://input"), true);

$course_id     = $data["course_id"] ?? null;
$session_date  = $data["session_date"] ?? null;

if (!$course_id || !$session_date) {
    Response::error("Paramètres manquants.");
}

$db = new Database();
$conn = $db->connect();

// Vérifier si le professeur possède ce cours
$check = $conn->prepare("
    SELECT id FROM courses
    WHERE id = :course_id AND professor_id = :prof_id
");

$check->execute([
    ":course_id" => $course_id,
    ":prof_id" => $professor_id
]);

if ($check->rowCount() === 0) {
    Response::error("Ce cours ne vous appartient pas.");
}

// Création de la session
$query = $conn->prepare("
    INSERT INTO attendance_sessions (course_id, session_date, status)
    VALUES (:course_id, :session_date, 'open')
");

$query->execute([
    ":course_id" => $course_id,
    ":session_date" => $session_date
]);

Response::success(null, "Session créée avec succès.");
?>
