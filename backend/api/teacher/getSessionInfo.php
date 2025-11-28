<?php
header("Content-Type: application/json");

require_once "../../config/database.php";
require_once "../../config/response.php";

session_start();

// Vérification professeur connecté
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "professor") {
    Response::error("Accès refusé.", 401);
}

if (!isset($_GET["session_id"])) {
    Response::error("session_id manquant.");
}

$session_id = intval($_GET["session_id"]);

$db = new Database();
$conn = $db->connect();

// Obtenir infos de la session
$query = $conn->prepare("
    SELECT s.id, s.course_id, s.session_date, s.status, c.professor_id
    FROM attendance_sessions s
    JOIN courses c ON c.id = s.course_id
    WHERE s.id = :session_id
");

$query->execute([":session_id" => $session_id]);

$session = $query->fetch(PDO::FETCH_ASSOC);

if (!$session) {
    Response::error("Session introuvable.");
}

// Vérifier que le professeur possède ce cours
if ($session["professor_id"] != $_SESSION["user_id"]) {
    Response::error("Vous n'avez pas accès à cette session.");
}

Response::success($session, "Session trouvée.");
?>
