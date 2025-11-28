<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1)
    Response::error("Accès refusé", 401);

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data["group_name"] ?? "");

if ($name === "")
    Response::error("Nom du groupe requis.");

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("INSERT INTO groups (group_name) VALUES (:name)");
$stmt->execute([":name" => $name]);

Response::success(null, "Groupe ajouté.");
