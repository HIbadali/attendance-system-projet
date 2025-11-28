<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1)
    Response::error("Accès refusé", 401);

$data = json_decode(file_get_contents("php://input"), true);

$id   = intval($data["id"] ?? 0);
$name = trim($data["group_name"] ?? "");

if (!$id || $name === "")
    Response::error("Nom ou ID manquant.");

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("
    UPDATE groups
    SET group_name = :name
    WHERE id = :id
");

$stmt->execute([
    ":name" => $name,
    ":id"   => $id
]);

Response::success(null, "Groupe modifié.");
