<?php
require_once __DIR__."/../../config/database.php";
require_once __DIR__."/../../config/response.php";

$session_id = $_POST["session_id"];

$db = new Database();
$conn = $db->connect();

$sql = "UPDATE attendance_sessions SET status='closed' WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([":id" => $session_id]);

Response::success("Session clôturée.");
