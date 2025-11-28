<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    Response::error("Accès refusé.", 401);
}

$data = json_decode(file_get_contents("php://input"), true);

$id         = intval($data["id"] ?? 0);
$first_name = trim($data["first_name"] ?? "");
$last_name  = trim($data["last_name"] ?? "");
$email      = trim($data["email"] ?? "");

if (!$id || $first_name === "" || $last_name === "" || $email === "") {
    Response::error("Données incomplètes.");
}

$db = new Database();
$conn = $db->connect();

// Vérifier email unique (sauf lui-même)
$check = $conn->prepare("
    SELECT id FROM users 
    WHERE email = :email AND id != :id
    LIMIT 1
");
$check->execute([
    ":email" => $email,
    ":id"    => $id
]);
if ($check->fetch()) {
    Response::error("Cet email est déjà utilisé par un autre utilisateur.");
}

$update = $conn->prepare("
    UPDATE users
    SET first_name = :first_name,
        last_name  = :last_name,
        email      = :email
    WHERE id = :id AND role_id = 3
");

$update->execute([
    ":first_name" => $first_name,
    ":last_name"  => $last_name,
    ":email"      => $email,
    ":id"         => $id
]);

Response::success(null, "Étudiant modifié.");
