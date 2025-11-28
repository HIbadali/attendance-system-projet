<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1)
    Response::error("Accès refusé", 401);

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["id"] ?? 0);

if (!$id)
    Response::error("ID manquant.");

$db = new Database();
$conn = $db->connect();

// On supprime d'abord les associations étudiants-groupe
$clear = $conn->prepare("DELETE FROM student_groups WHERE group_id = :id");
$clear->execute([":id" => $id]);

// Puis le groupe
$stmt = $conn->prepare("DELETE FROM groups WHERE id = :id");
$stmt->execute([":id" => $id]);

Response::success(null, "Groupe supprimé.");
