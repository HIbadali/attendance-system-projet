<?php
header("Content-Type: application/json");
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Non connecté"]);
    exit;
}

require_once __DIR__ . "/../../config/database.php";

$db = new Database();
$conn = $db->connect();

$student_id = $_SESSION["user_id"];

$query = $conn->prepare("
    SELECT c.id, c.title, c.code
    FROM courses c
    JOIN student_groups sg ON sg.course_id = c.id
    WHERE sg.student_id = :id
");
$query->execute([":id" => $student_id]);

$courses = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "data" => $courses]);
exit;
