
<?php
require_once "auth.php";
$error = "";
$message = "";
$students = [];
$courses = [];
$grades = [];
$canManage = in_array($role, ["profesor", "admin"], true);

if ($role === "profesor") {
    $stmt = $conn->prepare("SELECT c.course_id,c.name FROM courses c INNER JOIN professors p ON c.professor_id=p.professor_id WHERE p.user_id=?");
    $stmt->bind_param("i",$userId);
} elseif ($role === "admin") {
    $stmt = $conn->prepare("SELECT course_id,name FROM courses");
} else {
    $stmt = null;
}
if ($stmt) {
    $stmt->execute();
    $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
$allowedIds = array_map("intval",array_column($courses,"course_id"));

if ($canManage) {
    $sql = "SELECT s.student_id,s.first_name,s.last_name,e.course_id FROM students s INNER JOIN enrollments e ON s.student_id=e.student_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$canManage) {
        http_response_code(403);
        exit("Nuk keni autorizim.");
    }
    $studentId = (int)($_POST["student_id"] ?? 0);
    $courseId = (int)($_POST["course_id"] ?? 0);
    $grade = $_POST["grade"] ?? "";
    $validStudent = false;
    foreach ($students as $student) {
        if ((int)$student["student_id"] === $studentId && (int)$student["course_id"] === $courseId) {
            $validStudent = true;
            break;
        }
    }
    if (!$validStudent || !in_array($courseId,$allowedIds,true)) {
        $error = "Studenti ose lenda nuk eshte e vlefshme.";
    } elseif (!is_numeric($grade) || (float)$grade < 5 || (float)$grade > 10) {
        $error = "Nota duhet te jete nga 5 deri ne 10.";
    } else {
        $grade = (float)$grade;
        $stmt = $conn->prepare("SELECT grade_id FROM grades WHERE student_id=? AND course_id=?");
        $stmt->bind_param("ii",$studentId,$courseId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($existing) {
            $stmt = $conn->prepare("UPDATE grades SET grade=?,exam_date=CURDATE() WHERE grade_id=?");
            $stmt->bind_param("di",$grade,$existing["grade_id"]);
        } else {
            $stmt = $conn->prepare("INSERT INTO grades (student_id,course_id,grade,exam_date) VALUES (?,?,?,CURDATE())");
            $stmt->bind_param("iid",$studentId,$courseId,$grade);
        }
        $stmt->execute();
        $stmt->close();
        $message = "Nota u ruajt me sukses.";
    }
}

if ($role === "student") {
    $sql = "SELECT c.name AS course_name,g.grade,g.exam_date FROM grades g INNER JOIN courses c ON g.course_id=c.course_id INNER JOIN students s ON g.student_id=s.student_id WHERE s.user_id=? ORDER BY c.name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$userId);
} elseif ($role === "profesor") {
    $sql = "SELECT s.first_name,s.last_name,c.name AS course_name,g.grade,g.exam_date FROM grades g INNER JOIN students s ON g.student_id=s.student_id INNER JOIN courses c ON g.course_id=c.course_id INNER JOIN professors p ON c.professor_id=p.professor_id WHERE p.user_id=? ORDER BY c.name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$userId);
} else {
    $stmt = $conn->prepare("SELECT s.first_name,s.last_name,c.name AS course_name,g.grade,g.exam_date FROM grades g INNER JOIN students s ON g.student_id=s.student_id INNER JOIN courses c ON g.course_id=c.course_id ORDER BY c.name");
}
$stmt->execute();
$grades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notat - Student Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <span>Student</span> Hub
            </div>
            <a href="dashboard.php" class="login-btn">Kthehu</a>
        </nav>
    </header>

    <main class="dashboard">
        <h1>Menaxhimi i notave</h1>

        <?php if ($error): ?>
            <p><?= e($error) ?></p>
        <?php endif; ?>

        <?php if ($message): ?>
            <p><?= e($message) ?></p>
        <?php endif; ?>

        <?php if ($canManage): ?>
            <div class="dashboard-card">
                <h3>Vendos noten</h3>

                <form method="POST">
                    <p>
                        <select name="course_id" required>
                            <option value="">Zgjedh lenden</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= e($course["course_id"]) ?>">
                                    <?= e($course["name"]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <select name="student_id" required>
                            <option value="">Zgjedh studentin</option>
                            <?php foreach ($students as $student): ?>
                                <?php if (in_array((int)$student["course_id"], $allowedIds, true)): ?>
                                    <option value="<?= e($student["student_id"]) ?>">
                                        <?= e($student["first_name"] . " " . $student["last_name"]) ?>
                                        (Lenda <?= e($student["course_id"]) ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <input type="number" name="grade" min="5" max="10" step="1" placeholder="Nota" required>
                    </p>

                    <button type="submit" class="login-btn">Ruaj noten</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <?php foreach ($grades as $item): ?>
                <div class="dashboard-card">
                    <h3><?= e($item["course_name"]) ?></h3>

                    <?php if ($canManage): ?>
                        <p>
                            Studenti: <?= e($item["first_name"] . " " . $item["last_name"]) ?>
                        </p>
                    <?php endif; ?>

                    <p>Nota: <?= e($item["grade"]) ?></p>
                    <p>Data: <?= e($item["exam_date"] ?? "-") ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($grades)): ?>
            <p>Nuk ka nota te regjistruara.</p>
        <?php endif; ?>
    </main>
</body>
</html>
