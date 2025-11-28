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
    Response::error("ID étudiant manquant.");
}

$db = new Database();
$conn = $db->connect();

// On enlève d'abord les liens groupe-étudiant
$delLink = $conn->prepare("DELETE FROM student_groups WHERE student_id = :id");
$delLink->execute([":id" => $id]);

// Puis on supprime l'utilisateur
$delUser = $conn->prepare("DELETE FROM users WHERE id = :id AND role_id = 3");
$delUser->execute([":id" => $id]);

Response::success(null, "Étudiant supprimé.");
