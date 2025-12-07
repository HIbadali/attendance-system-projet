<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];

$db = new Database();
$conn = $db->connect();

$sql = "SELECT id, code, title 
        FROM courses 
        WHERE professor_id = :pid
        ORDER BY code";

$stmt = $conn->prepare($sql);
$stmt->execute([":pid" => $user_id]);

echo json_encode([
    "success" => true,
    "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
