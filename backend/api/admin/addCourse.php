<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";

session_start();

// ensure admin
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 1) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$code = $data["code"] ?? null;
$title = $data["title"] ?? null;
$professor_id = $data["professor_id"] ?? null;

if (!$code || !$title || !$professor_id) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

$sql = "INSERT INTO courses (code, title, professor_id) 
        VALUES (:c, :t, :pid)";
$stmt = $conn->prepare($sql);

$ok = $stmt->execute([
    ":c" => $code,
    ":t" => $title,
    ":pid" => $professor_id
]);

echo json_encode([
    "success" => $ok,
    "message" => $ok ? "Course created" : "Insert failed"
]);
