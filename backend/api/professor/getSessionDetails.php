<?php
header("Content-Type: application/json");
session_start();
require_once "../../config/database.php";

$db=new Database();
$conn=$db->connect();

$stmt=$conn->prepare("
SELECT s.id, c.title AS course_name, g.group_name
FROM attendance_sessions s
JOIN courses c ON c.id=s.course_id
JOIN groups g ON g.id=s.group_id
WHERE s.id=:sid
");
$stmt->execute([":sid"=>$_GET["session_id"]]);
$session=$stmt->fetch(PDO::FETCH_ASSOC);

$st=$conn->prepare("
SELECT u.first_name, u.last_name,
   CASE WHEN a.status='present' THEN 1 ELSE 0 END AS present
FROM users u
JOIN group_members gm ON gm.student_id=u.id
LEFT JOIN attendance a ON a.student_id=u.id AND a.session_id=:sid
WHERE gm.group_id=(SELECT group_id FROM attendance_sessions WHERE id=:sid)
");
$st->execute([":sid"=>$_GET["session_id"]]);

$session["students"]=$st->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success"=>true,"data"=>$session]);
