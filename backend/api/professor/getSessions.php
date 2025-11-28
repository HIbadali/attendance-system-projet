<?php
header("Content-Type: application/json");
session_start();
require_once "../../config/database.php";

$db=new Database();
$conn=$db->connect();

$stmt=$conn->prepare("
SELECT s.id, s.date, s.time,
       c.title AS course_name,
       g.group_name
FROM attendance_sessions s
JOIN courses c ON c.id=s.course_id
JOIN groups g ON g.id=s.group_id
WHERE c.professor_id=:p
ORDER BY s.id DESC
");
$stmt->execute([":p"=>$_SESSION["user_id"]]);

echo json_encode(["success"=>true,"data"=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
