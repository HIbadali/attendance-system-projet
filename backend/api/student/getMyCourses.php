<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 3) {
    Response::error("Accès réservé aux étudiants.", 403);
}

try {
    $db = new Database();
    $conn = $db->connect();
    $studentId = $_SESSION["user_id"];

    $stmt = $conn->prepare("
        SELECT
            c.id,
            c.title,
            c.code,
            ps.presence,
            ps.absence,
            ps.participation,
            ps.note,
            ps.best
        FROM professor_students ps
        JOIN courses c ON c.id = ps.course_id
        WHERE ps.student_id = :sid
        ORDER BY c.title
    ");
    $stmt->execute([":sid" => $studentId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    Response::success($rows);

} catch (Throwable $e) {
    Response::error("DB Error: " . $e->getMessage());
}
