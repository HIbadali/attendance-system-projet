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
        COUNT(c.id) AS course_count
    FROM users u
    LEFT JOIN courses c ON c.professor_id = u.id
    WHERE u.role_id = 2
    GROUP BY u.id, u.first_name, u.last_name, u.email
    ORDER BY u.last_name, u.first_name
";

$stmt = $conn->prepare($sql);
$stmt->execute();

$professors = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($professors, "Professeurs récupérés.");
