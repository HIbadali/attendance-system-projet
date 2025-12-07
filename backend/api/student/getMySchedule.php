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
        sch.day,
        sch.start_time,
        sch.end_time
    FROM student_schedule sch
    JOIN courses c ON sch.course_id = c.id
    WHERE sch.student_user_id = :sid
    ORDER BY FIELD(sch.day,'Lundi','Mardi','Mercredi','Jeudi','Vendredi'), sch.start_time
");
$q->execute([":sid" => $student_id]);
$r = $q->fetchAll(PDO::FETCH_ASSOC);

Response::success($r);
