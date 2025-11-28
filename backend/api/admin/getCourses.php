<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1)
    Response::error("Accès refusé", 401);

$db = new Database();
$conn = $db->connect();

$sql = "
    SELECT 
        c.id,
        c.name,
        c.code,
        c.professor_id,
        CONCAT(u.first_name, ' ', u.last_name) AS professor_name,
        GROUP_CONCAT(DISTINCT g.group_name ORDER BY g.group_name SEPARATOR ', ') AS groups_label
    FROM courses c
    LEFT JOIN users u ON u.id = c.professor_id
    LEFT JOIN course_groups cg ON cg.course_id = c.id
    LEFT JOIN groups g ON g.id = cg.group_id
    GROUP BY c.id, c.name, c.code, c.professor_id, professor_name
    ORDER BY c.name
";

$stmt = $conn->prepare($sql);
$stmt->execute();

$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($courses);
