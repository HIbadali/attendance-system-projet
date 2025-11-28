<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/response.php";

// Vérifier session
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role_id"])) {
    Response::error("Non connecté");
}

if ($_SESSION["role_id"] != 2) { // 2 = professor
    Response::error("Accès refusé");
}

$teacher_id = $_SESSION["user_id"];

try {
    $db = new Database();
    $conn = $db->connect();

    $query = $conn->prepare("
        SELECT id, title AS name, code 
        FROM courses
        WHERE professor_id = :prof
    ");

    $query->execute([":prof" => $teacher_id]);
    $courses = $query->fetchAll(PDO::FETCH_ASSOC);

    Response::success($courses, "Cours trouvés.");

} catch (Exception $e) {
    Response::error("Erreur serveur : " . $e->getMessage());
}
?>
