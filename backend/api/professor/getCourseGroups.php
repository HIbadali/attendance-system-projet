<?php
require_once __DIR__."/../../config/database.php";
require_once __DIR__."/../../config/response.php";

if (!isset($_GET["course_id"])) 
    Response::error("ID cours manquant.");

$course_id = intval($_GET["course_id"]);

$db = new Database();
$conn = $db->connect();

$sql = "
    SELECT g.id, g.group_name
    FROM course_groups cg
    JOIN groups g ON g.id = cg.group_id
    WHERE cg.course_id = :cid
";
$stmt = $conn->prepare($sql);
$stmt->execute([":cid" => $course_id]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::success($data);
