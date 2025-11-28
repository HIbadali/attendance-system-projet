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
    Response::error("ID du cours manquant.");

$db = new Database();
$conn = $db->connect();

// Supprimer les liens groupes-cours
$delLinks = $conn->prepare("DELETE FROM course_groups WHERE course_id = :id");
$delLinks->execute([":id" => $id]);

// Supprimer les sessions associées ? (optionnel, selon ta BDD)
// $delSessions = $conn->prepare("DELETE FROM sessions WHERE course_id = :id");
// $delSessions->execute([":id" => $id]);

// Supprimer le cours
$delCourse = $conn->prepare("DELETE FROM courses WHERE id = :id");
$delCourse->execute([":id" => $id]);

Response::success(null, "Cours supprimé.");
