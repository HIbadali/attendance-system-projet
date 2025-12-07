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
$prof_id    = $_SESSION["user_id"];

if (!$student_id || !$course_id) {
    Response::error("Missing values");
}

try {
    $db = new Database();
    $conn = $db->connect();

    // Reset best for others
    $q1 = $conn->prepare("
        UPDATE professor_students 
        SET best = 0
        WHERE course_id = :c AND professor_id = :p
    ");
    $q1->execute([":c"=>$course_id, ":p"=>$prof_id]);

    // Set best here
    $q2 = $conn->prepare("
        UPDATE professor_students 
        SET best = 1
        WHERE student_id = :s AND course_id = :c AND professor_id = :p
    ");
    $q2->execute([
        ":s"=>$student_id,
        ":c"=>$course_id,
        ":p"=>$prof_id
    ]);

    Response::success(null,"Best updated");

} catch (Exception $e) {
    Response::error("DB Error: ".$e->getMessage());
}
