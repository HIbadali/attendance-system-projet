<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1)
    Response::error("Accès refusé", 401);

$db = new Database();
$conn = $db->connect();

// Profs
$stmtProf = $conn->prepare("
    SELECT id, first_name, last_name 
    FROM users 
    WHERE role_id = 2
    ORDER BY last_name, first_name
");
$stmtProf->execute();
$professors = $stmtProf->fetchAll(PDO::FETCH_ASSOC);

// Groupes
$stmtGroup = $conn->prepare("
    SELECT id, group_name 
    FROM groups 
    ORDER BY group_name
");
$stmtGroup->execute();
$groups = $stmtGroup->fetchAll(PDO::FETCH_ASSOC);

Response::success([
    "professors" => $professors,
    "groups"     => $groups
]);
