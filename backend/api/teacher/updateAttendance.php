<?php
header("Content-Type: application/json");

require_once "../../config/database.php";
require_once "../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "professor") {
    Response::error("Accès refusé.", 401);
}

$data = json_decode(file_get_contents("php://input"), true);

$session_id = $data["session_id"] ?? null;
$students = $data["students"] ?? null;

if (!$session_id || !$students) {
    Response::error("Paramètres manquants.");
}

$db = new Database();
$conn = $db->connect();

// Supprimer l'ancienne présence
$conn->prepare("DELETE FROM attendance_records WHERE session_id = :id")->execute([
    ":id" => $session_id
]);

// Réinsertion propre
$query = $conn->prepare("
    INSERT INTO attendance_records (session_id, student_id, status)
    VALUES (:session_id, :student_id, :status)
");

foreach ($students as $s) {
    $query->execute([
        ":session_id" => $session_id,
        ":student_id" => $s["student_id"],
        ":status" => $s["status"]
    ]);
}

Response::success(null, "Présence enregistrée avec succès.");
