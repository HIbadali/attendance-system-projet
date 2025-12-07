<?php
header("Content-Type: application/json");
require_once "../../config/database.php";
session_start();

$db = new Database();
$conn = $db->connect();

$stmt = $conn->query("SELECT * FROM students ORDER BY last_name ASC");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success"=>true,"data"=>$students]);
exit;
