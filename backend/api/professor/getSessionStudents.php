<?php
header("Content-Type: application/json");
session_start();
require_once "../../config/database.php";

$db=new Database();
$conn=$db->connect();

$stmt=$conn->prepare("
SELECT u.id, u.first_name, u.last_name
FROM users u
JOIN group_members gm ON gm.student_id=u.id
JOIN attendance_sessions s ON s.group_id=gm.group_id
WHERE s.id=:sid
");
$stmt->execute([":sid"=>$_GET["session_id"]]);

echo json_encode(["success"=>true,"data"=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
