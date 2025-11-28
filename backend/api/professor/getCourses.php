<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

$db = new Database();
$conn = $db->connect();

$query = $conn->prepare("SELECT id, title, code FROM courses ORDER BY title");
$query->execute();

$courses = $query->fetchAll(PDO::FETCH_ASSOC);

Response::success($courses);
