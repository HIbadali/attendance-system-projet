<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data["email"] ?? null;
$password = $data["password"] ?? null;

$db = new Database();
$conn = $db->connect();

$query = $conn->prepare("
    SELECT users.*, roles.name AS role_name 
    FROM users
    JOIN roles ON roles.id = users.role_id
    WHERE email = :email
    LIMIT 1
");
$query->execute([":email" => $email]);

$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    Response::error("Utilisateur introuvable.");
}

if (!password_verify($password, $user["password"])) {
    Response::error("Mot de passe incorrect.");
}

$_SESSION["user_id"] = $user["id"];
$_SESSION["role_id"] = $user["role_id"];
$_SESSION["role_name"] = $user["role_name"];

Response::success(["role" => $user["role_name"]]);
