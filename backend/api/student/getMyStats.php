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
            COALESCE(SUM(presence), 0)       AS total_presence,
            COALESCE(SUM(absence), 0)        AS total_absence,
            COALESCE(SUM(participation), 0)  AS total_participation,
            ROUND(AVG(note), 2)              AS avg_note
        FROM professor_students
        WHERE student_id = :sid
    ");
    $stmt->execute([":sid" => $studentId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $row = [
            "total_presence"      => 0,
            "total_absence"       => 0,
            "total_participation" => 0,
            "avg_note"            => 0
        ];
    }

    Response::success($row);

} catch (Throwable $e) {
    Response::error("DB Error: " . $e->getMessage());
}
