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
        g.id,
        g.group_name,
        COUNT(sg.student_id) AS student_count
    FROM groups g
    LEFT JOIN student_groups sg ON sg.group_id = g.id
    GROUP BY g.id, g.group_name
    ORDER BY g.group_name ASC
";

$stmt = $conn->prepare($sql);
$stmt->execute();

$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($groups);
