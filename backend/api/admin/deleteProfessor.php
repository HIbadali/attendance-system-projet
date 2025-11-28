<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    Response::error("Accès refusé.", 401);
}

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["id"] ?? 0);

if (!$id) {
    Response::error("ID professeur manquant.");
}

$db = new Database();
$conn = $db->connect();

// Détacher les cours de ce prof pour éviter les erreurs de clé étrangère
$clearCourses = $conn->prepare("
    UPDATE courses SET professor_id = NULL WHERE professor_id = :id
");
$clearCourses->execute([":id" => $id]);

// Supprimer le professeur
$del = $conn->prepare("DELETE FROM users WHERE id = :id AND role_id = 2");
$del->execute([":id" => $id]);

Response::success(null, "Professeur supprimé.");
