<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1)
    Response::error("Accès refusé", 401);

$data = json_decode(file_get_contents("php://input"), true);

$name         = trim($data["name"] ?? "");
$code         = trim($data["code"] ?? "");
$professor_id = intval($data["professor_id"] ?? 0);
$group_ids    = $data["group_ids"] ?? [];

if ($name === "" || $code === "" || !$professor_id)
    Response::error("Nom, code et professeur sont obligatoires.");

$db = new Database();
$conn = $db->connect();

// Insert course
$stmt = $conn->prepare("
    INSERT INTO courses (name, code, professor_id)
    VALUES (:name, :code, :prof_id)
");
$stmt->execute([
    ":name"   => $name,
    ":code"   => $code,
    ":prof_id"=> $professor_id
]);

$course_id = $conn->lastInsertId();

// Link with groups
if (!empty($group_ids)) {
    $link = $conn->prepare("
        INSERT INTO course_groups (course_id, group_id)
        VALUES (:course_id, :group_id)
    ");

    foreach ($group_ids as $gid) {
        $link->execute([
            ":course_id" => $course_id,
            ":group_id"  => $gid
        ]);
    }
}

Response::success(null, "Cours ajouté.");
