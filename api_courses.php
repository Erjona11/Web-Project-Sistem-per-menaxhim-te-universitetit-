
<?php
require_once "auth.php";

header("Content-Type: application/json; charset=utf-8");

if ($role === "student") {
    $sql = "SELECT c.course_id,c.name,c.code,c.credits,c.semester FROM courses c INNER JOIN enrollments e ON c.course_id=e.course_id INNER JOIN students s ON e.student_id=s.student_id WHERE s.user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$userId);
} elseif ($role === "profesor") {
    $sql = "SELECT c.course_id,c.name,c.code,c.credits,c.semester FROM courses c INNER JOIN professors p ON c.professor_id=p.professor_id WHERE p.user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$userId);
} else {
    $stmt = $conn->prepare("SELECT course_id,name,code,credits,semester FROM courses");
}

$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode([
    "success" => true,
    "courses" => $courses
],JSON_UNESCAPED_UNICODE);
?>
