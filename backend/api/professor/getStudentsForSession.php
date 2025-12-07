<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";

session_start();

$session_id = $_GET["session_id"] ?? null;
if (!$session_id) {
    echo json_encode(["success" => false, "message" => "Missing session_id"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

/*
   LOGIC :
   - session → belongs to a course
   - get students assigned to that course
*/

$sql = "
SELECT s.id, s.first_name, s.last_name
FROM students s
JOIN student_course sc ON sc.student_id = s.id
JOIN attendance_sessions sess ON sess.course_id = sc.course_id
WHERE sess.id = :sid
ORDER BY s.last_name
";

$stmt = $conn->prepare($sql);
$stmt->execute([":sid" => $session_id]);

echo json_encode([
    "success" => true,
    "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
