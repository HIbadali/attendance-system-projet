<?php
header("Content-Type: application/json");
session_start();
require_once "../../config/database.php";

$db=new Database();
$conn=$db->connect();

$stmt=$conn->prepare("SELECT * FROM groups");
$stmt->execute();

echo json_encode(["success"=>true,"data"=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
