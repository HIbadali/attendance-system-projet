<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

if (!isset($_SESSION["user_id"])) {
    Response::error("Not logged in");
}

$db = new Database();
$conn = $db->connect();

$student_id = $_SESSION["user_id"];

$q = $conn->prepare("
    SELECT 
        c.title,
        ps.best
    FROM professor_students ps
    JOIN courses c ON ps.course_id = c.id
    WHERE ps.student_user_id = :sid AND ps.best = 1
");
$q->execute([":sid" => $student_id]);
$r = $q->fetchAll(PDO::FETCH_ASSOC);

Response::success($r);
