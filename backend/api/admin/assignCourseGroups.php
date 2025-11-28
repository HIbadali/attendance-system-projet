<?php
header("Content-Type: application/json");

require_once "../../config/database.php";
require_once "../../config/response.php";

session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    Response::error("Accès refusé.", 401);
}

$data = json_decode(file_get_contents("php://input"), true);

$course_id = $data["course_id"] ?? null;
$groups = $data["groups"] ?? null;

if (!$course_id || !$groups) {
    Response::error("Champs manquants.");
}

$db = new Database();
$conn = $db->connect();

// Supprimer anciennes associations
$conn->prepare("DELETE FROM course_groups WHERE course_id = :id")
     ->execute([":id" => $course_id]);

// Ajouter nouvelles associations
$query = $conn->prepare("
    INSERT INTO course_groups (course_id, group_id)
    VALUES (:course_id, :group_id)
");

foreach ($groups as $g) {
    $query->execute([
        ":course_id" => $course_id,
        ":group_id" => $g
    ]);
}

Response::success(null, "Groupes assignés !");
?>
