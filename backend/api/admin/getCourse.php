<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1)
    Response::error("Accès refusé", 401);

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if (!$id) Response::error("ID du cours manquant.");

$db = new Database();
$conn = $db->connect();

// Cours
$stmtCourse = $conn->prepare("SELECT * FROM courses WHERE id = :id");
$stmtCourse->execute([":id" => $id]);
$course = $stmtCourse->fetch(PDO::FETCH_ASSOC);

if (!$course) Response::error("Cours introuvable.");

// Groupes du cours
$stmtGroups = $conn->prepare("
    SELECT group_id 
    FROM course_groups 
    WHERE course_id = :id
");
$stmtGroups->execute([":id" => $id]);
$rows = $stmtGroups->fetchAll(PDO::FETCH_ASSOC);

$group_ids = array_map(function($r) { return (int)$r["group_id"]; }, $rows);

Response::success([
    "course"     => $course,
    "group_ids"  => $group_ids
]);
