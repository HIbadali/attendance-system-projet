<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/database.php";
session_start();

/*
    Returns students of professor's courses with attendance %
*/

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

try {
    $db = new Database();
    $conn = $db->connect();
    $prof_id = $_SESSION["user_id"];

    $sql = "
    SELECT s.id as student_id, s.name, s.lastname, c.id as course_id, c.title,
           COUNT(a.id) as total_sessions,
           SUM(a.present) as total_present
    FROM students s
    JOIN student_groups sg ON sg.student_id = s.id
    JOIN course_groups cg ON cg.group_id = sg.group_id
    JOIN courses c ON c.id = cg.course_id
    LEFT JOIN attendance_records a ON a.student_id = s.id AND a.course_id = c.id
    WHERE c.professor_id = :prof_id
    GROUP BY s.id, c.id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prof_id", $prof_id);
    $stmt->execute();

    $students = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $attendance = ($row["total_sessions"] > 0)
                        ? round(($row["total_present"] / $row["total_sessions"]) * 100)
                        : 0;

        /* Automatic English comments */
        $comment = "Undefined";

        if ($attendance >= 90)      $comment = "Excellent performance!";
        else if ($attendance >= 80) $comment = "Strong attendance, keep it up!";
        else if ($attendance >= 70) $comment = "Good, but room for improvement.";
        else if ($attendance >= 50) $comment = "Needs improvement, inconsistent attendance.";
        else                        $comment = "Critical attendance problem.";

        $students[] = [
            "student_id" => $row["student_id"],
            "course_id" => $row["course_id"],
            "name" => $row["name"] . " " . $row["lastname"],
            "course" => $row["title"],
            "attendance" => $attendance,
            "comment" => $comment
        ];
    }

    echo json_encode(["success" => true, "data" => $students]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
