<?php
header("Content-Type: application/json");
session_start();

require_once "../../config/database.php";

if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!=="professor"){
    echo json_encode(["success"=>false,"message"=>"Accès refusé"]); exit;
}

$session_id = $_GET["session_id"];

$db = new Database(); $conn = $db->connect();

$q = $conn->prepare("
SELECT u.id, u.first_name, u.last_name 
FROM users u
JOIN group_members gm ON gm.student_id = u.id
JOIN attendance_sessions s ON s.group_id = gm.group_id
WHERE s.id = :sid
");
$q->execute([":sid"=>$session_id]);

echo json_encode(["success"=>true,"data"=>$q->fetchAll(PDO::FETCH_ASSOC)]);