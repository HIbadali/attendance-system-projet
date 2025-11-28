<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    Response::error("Accès refusé.", 401);
}

$db = new Database();
$conn = $db->connect();

$sql = "
    SELECT 
        u.id,
        u.first_name,
        u.last_name,
        u.email,
        g.group_name
    FROM users u
    LEFT JOIN student_groups sg ON sg.student_id = u.id
    LEFT JOIN groups g ON g.id = sg.group_id
    WHERE u.role_id = 3
    ORDER BY u.last_name, u.first_name
";

$stmt = $conn->prepare($sql);
$stmt->execute();

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($students, "Étudiants récupérés.");
