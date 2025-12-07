<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 2) {
    Response::error("Access denied");
}

$data = json_decode(file_get_contents("php://input"), true);

$student_id = $data["student_id"] ?? null;
$course_id  = $data["course_id"] ?? null;
$field      = $data["field"] ?? null;
$value      = $data["value"] ?? null;

$allowed = ["presence","absence","participation","note"];
if (!in_array($field, $allowed)) {
    Response::error("Invalid field");
}

if (!$student_id || !$course_id) {
    Response::error("Missing data");
}

try {
    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("
        UPDATE professor_students
        SET $field = :v
        WHERE student_id=:s AND course_id=:c
    ");

    $stmt->execute([
        ":v"=>$value,
        ":s"=>$student_id,
        ":c"=>$course_id
    ]);

    Response::success(null,"Updated");
} 
catch(Exception $e){
    Response::error("DB Error: ".$e->getMessage());
}
