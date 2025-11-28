<?php
header("Content-Type: application/json");

require_once "../../config/database.php";
require_once "../../config/response.php";

session_start();

// Vérifier professeur connecté
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "professor") {
    Response::error("Accès refusé.", 401);
}

if (!isset($_GET["course_id"])) {
    Response::error("course_id manquant.");
}

$course_id = intval($_GET["course_id"]);

$db = new Database();
$conn = $db->connect();

/*
    LOGIQUE :
    1. Trouver les groupes associés au cours via course_groups
    2. Trouver les étudiants de ces groupes via student_groups
    3. Récupérer les infos des étudiants dans users
*/

$query = $conn->prepare("
    SELECT u.id, u.first_name, u.last_name, u.email
    FROM users u
    JOIN student_groups sg ON sg.student_id = u.id
    JOIN course_groups cg ON cg.group_id = sg.group_id
    WHERE cg.course_id = :course_id
      AND u.role_id = 3   -- 3 = student
    ORDER BY u.last_name ASC
");

$query->execute([":course_id" => $course_id]);

$students = $query->fetchAll(PDO::FETCH_ASSOC);

Response::success($students, "Étudiants récupérés.");
