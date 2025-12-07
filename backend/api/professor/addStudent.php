<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../../config/database.php";

// Validate POST method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
    exit;
}

// Read JSON received
$data = json_decode(file_get_contents("php://input"), true);

// Validate fields
if (!isset($data["course_id"]) || !isset($data["first_name"]) || !isset($data["last_name"])) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$course_id   = intval($data["course_id"]);
$first_name  = trim($data["first_name"]);
$last_name   = trim($data["last_name"]);
$email       = strtolower(str_replace(" ", ".", $first_name . "." . $last_name)) . "@attendance.com";

$professor_id = $_SESSION["user_id"]; // ⭐ IMPORTANT

// DB Connection
$db = new Database();
$conn = $db->connect();

// 1️⃣ Add student in USERS (if not exists)
$stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$stmt->execute([$email]);
$existing = $stmt->fetch();

if ($existing) {
    $student_id = $existing["id"];
} else {
    $stmt = $conn->prepare("INSERT INTO users(first_name,last_name,email,password,role_id) VALUES (?,?,?,?,3)");
    $stmt->execute([$first_name, $last_name, $email, '$2y$10$Av123456789demoHash']);
    $student_id = $conn->lastInsertId();
}

// 2️⃣ Link student to course (prevent duplicates)
$stmt = $conn->prepare("
    SELECT id FROM professor_students 
    WHERE student_id=? AND course_id=? LIMIT 1
");
$stmt->execute([$student_id, $course_id]);

if (!$stmt->fetch()) {
    $stmt = $conn->prepare("
        INSERT INTO professor_students(student_id, course_id, professor_id, presence, absence, participation, note, best) 
        VALUES (?, ?, ?, 0, 0, 0, 0, 0)
    ");
    $stmt->execute([$student_id, $course_id, $professor_id]);
}

// 3️⃣ SUCCESS
echo json_encode([
    "success" => true,
    "message" => "Student added successfully",
    "student_id" => $student_id,
    "email" => $email
]);
exit;
?>
