<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    Response::error("Accès refusé.", 401);
}

$data = json_decode(file_get_contents("php://input"), true);

$first_name = trim($data["first_name"] ?? "");
$last_name  = trim($data["last_name"] ?? "");
$email      = trim($data["email"] ?? "");

if ($first_name === "" || $last_name === "" || $email === "") {
    Response::error("Tous les champs sont obligatoires.");
}

$db = new Database();
$conn = $db->connect();

// Vérifier email unique
$check = $conn->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$check->execute([":email" => $email]);
if ($check->fetch()) {
    Response::error("Cet email est déjà utilisé.");
}

// mot de passe par défaut : "password"
$hashedPassword = password_hash("password", PASSWORD_DEFAULT);

$insert = $conn->prepare("
    INSERT INTO users (first_name, last_name, email, password, role_id)
    VALUES (:first_name, :last_name, :email, :password, 2)
");

$insert->execute([
    ":first_name" => $first_name,
    ":last_name"  => $last_name,
    ":email"      => $email,
    ":password"   => $hashedPassword
]);

Response::success(null, "Professeur ajouté (mot de passe par défaut : password).");
