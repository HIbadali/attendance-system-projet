<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1)
    Response::error("Accès refusé", 401);

$data = json_decode(file_get_contents("php://input"), true);

$id           = intval($data["id"] ?? 0);
$name         = trim($data["name"] ?? "");
$code         = trim($data["code"] ?? "");
$professor_id = intval($data["professor_id"] ?? 0);
$group_ids    = $data["group_ids"] ?? [];

if (!$id || $name === "" || $code === "" || !$professor_id)
    Response::error("Données incomplètes.");

$db = new Database();
$conn = $db->connect();

// Update course
$stmt = $conn->prepare("
    UPDATE courses
    SET name = :name, code = :code, professor_id = :prof_id
    WHERE id = :id
");
$stmt->execute([
    ":name"    => $name,
    ":code"    => $code,
    ":prof_id" => $professor_id,
    ":id"      => $id
]);

// Clear groups
$del = $conn->prepare("DELETE FROM course_groups WHERE course_id = :id");
$del->execute([":id" => $id]);

// Re-insert groups
if (!empty($group_ids)) {
    $link = $conn->prepare("
        INSERT INTO course_groups (course_id, group_id)
        VALUES (:course_id, :group_id)
    ");

    foreach ($group_ids as $gid) {
        $link->execute([
            ":course_id" => $id,
            ":group_id"  => $gid
        ]);
    }
}

Response::success(null, "Cours modifié.");
