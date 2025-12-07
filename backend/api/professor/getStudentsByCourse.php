<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 2) {
    Response::error("Access denied");
}

$professor_id = $_SESSION["user_id"];
$course_id = $_GET["course_id"] ?? null;

if (!$course_id) {
    Response::error("Missing course_id");
}

try{
    $db = new Database();
    $conn = $db->connect();

    $query = $conn->prepare("
        SELECT 
            ps.student_id,
            u.first_name,
            u.last_name,
            ps.presence,
            ps.absence,
            ps.participation,
            ps.note,
            ps.best
        FROM professor_students ps
        INNER JOIN users u ON u.id = ps.student_id
        WHERE ps.professor_id=:p AND ps.course_id=:c
        ORDER BY u.last_name ASC
    ");

    $query->execute([
        ":p"=>$professor_id,
        ":c"=>$course_id
    ]);

    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    Response::success($data);
}
catch(Exception $e){
    Response::error("DB Error: ".$e->getMessage());
}
