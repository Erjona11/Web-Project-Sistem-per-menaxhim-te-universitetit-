
<?php
require_once "auth.php";
$courses = [];

if ($role === "student") {
    $sql = "SELECT c.course_id, c.name, c.code, c.credits, c.semester
    FROM courses c
    INNER JOIN enrollments e ON c.course_id = e.course_id
    INNER JOIN students s ON e.student_id = s.student_id
    WHERE s.user_id = ?
    ORDER BY c.semester, c.name";
} elseif ($role === "profesor") {
    $sql = "SELECT c.course_id, c.name, c.code, c.credits, c.semester
    FROM courses c
    INNER JOIN professors p ON c.professor_id = p.professor_id
    WHERE p.user_id = ?
    ORDER BY c.semester, c.name";
} elseif ($role === "admin") {
    $sql = "SELECT course_id, name, code, credits, semester
    FROM courses ORDER BY semester, name";
} else {
    http_response_code(403);
    exit("Nuk keni autorizim.");
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    exit("Gabim gjate marrjes se lendeve.");
}
if ($role !== "admin") {
    $stmt->bind_param("i", $userId);
}
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lëndët - Student Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo"><span>Student</span> Hub</div>
        <a href="dashboard.php" class="login-btn">Kthehu</a>
    </nav>
</header>
<main class="dashboard">
    <h1>Lëndët e mia</h1>
    <p>Lista e lëndëve të regjistruara në sistem.</p>
    <?php if (empty($courses)): ?>
        <p>Nuk u gjet asnjë lëndë për këtë përdorues.</p>
    <?php else: ?>
        <div class="dashboard-grid">
            <?php foreach ($courses as $course): ?>
                <div class="dashboard-card">
                    <h3><?= e($course["name"]) ?></h3>
                    <p>Kodi: <?= e($course["code"]) ?></p>
                    <p>ECTS: <?= e($course["credits"]) ?></p>
                    <p>Semestri: <?= e($course["semester"] ?? "-") ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
