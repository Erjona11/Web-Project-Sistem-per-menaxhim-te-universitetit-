
<?php
require_once "auth.php";
$error = "";
$message = "";
$edit = null;

if ($role === "profesor") {
    $stmt = $conn->prepare("SELECT c.course_id, c.name FROM courses c INNER JOIN professors p ON c.professor_id = p.professor_id WHERE p.user_id = ?");
    $stmt->bind_param("i", $userId);
} elseif ($role === "admin") {
    $stmt = $conn->prepare("SELECT course_id, name FROM courses");
} else {
    $stmt = null;
}

$myCourses = [];
if ($stmt) {
    $stmt->execute();
    $myCourses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$allowedIds = array_map("intval", array_column($myCourses, "course_id"));
$canManage = in_array($role, ["profesor", "admin"], true);

if ($_SERVER["REQUEST_METHOD"] === "POST" && $canManage) {
    $action = $_POST["action"] ?? "";
    $id = (int)($_POST["id"] ?? 0);
    $courseId = (int)($_POST["course_id"] ?? 0);
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $deadline = $_POST["deadline"] ?? "";

    if ($action === "save") {
        if ($title === "" || !in_array($courseId, $allowedIds, true)) {
            $error = "Ploteso titullin dhe zgjedh lenden.";
        } elseif ($deadline !== "" && !DateTime::createFromFormat("!Y-m-d", $deadline)) {
            $error = "Data nuk eshte e vlefshme.";
        } elseif ($id > 0) {
            $stmt = $conn->prepare("UPDATE assignments SET title = ?, description = ?, deadline = NULLIF(?, '') WHERE assignment_id = ? AND course_id = ?");
            $stmt->bind_param("sssii", $title, $description, $deadline, $id, $courseId);
            $stmt->execute();
            $message = $stmt->affected_rows > 0 ? "Detyra u perditesua." : "Nuk u ndryshua asnje detyre.";
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO assignments (course_id, title, description, deadline) VALUES (?, ?, ?, NULLIF(?, ''))");
            $stmt->bind_param("isss", $courseId, $title, $description, $deadline);
            $stmt->execute();
            $stmt->close();
            $message = "Detyra u shtua.";
        }
    } elseif ($action === "delete") {
        if ($id > 0 && in_array($courseId, $allowedIds, true)) {
            $stmt = $conn->prepare("DELETE FROM assignments WHERE assignment_id = ? AND course_id = ?");
            $stmt->bind_param("ii", $id, $courseId);
            $stmt->execute();
            $message = $stmt->affected_rows > 0 ? "Detyra u fshi." : "Detyra nuk u gjet.";
            $stmt->close();
        } else {
            $error = "Nuk keni autorizim.";
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    http_response_code(403);
    exit("Nuk keni autorizim.");
}

if ($canManage && isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM assignments WHERE assignment_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row && in_array((int)$row["course_id"], $allowedIds, true)) {
        $edit = $row;
    }
    $stmt->close();
}

if ($role === "student") {
    $sql = "SELECT a.*, c.name AS course_name FROM assignments a INNER JOIN courses c ON a.course_id = c.course_id INNER JOIN enrollments e ON e.course_id = c.course_id INNER JOIN students s ON s.student_id = e.student_id WHERE s.user_id = ? ORDER BY a.deadline";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
} elseif ($role === "profesor") {
    $sql = "SELECT a.*, c.name AS course_name FROM assignments a INNER JOIN courses c ON a.course_id = c.course_id INNER JOIN professors p ON p.professor_id = c.professor_id WHERE p.user_id = ? ORDER BY a.deadline";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
} else {
    $stmt = $conn->prepare("SELECT a.*, c.name AS course_name FROM assignments a INNER JOIN courses c ON a.course_id = c.course_id ORDER BY a.deadline");
}

if ($role !== "admin") {
    $stmt->execute();
} else {
    $stmt->execute();
}
$assignments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detyrat - Student Hub</title>
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
    <h1>Detyrat</h1>
    <?php if ($error): ?><p><?= e($error) ?></p><?php endif; ?>
    <?php if ($message): ?><p><?= e($message) ?></p><?php endif; ?>

    <?php if ($canManage): ?>
        <div class="dashboard-card" style="margin-bottom:25px;">
            <h3><?= $edit ? "Ndrysho detyren" : "Shto detyre" ?></h3>
            <form method="POST">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= e($edit["assignment_id"] ?? 0) ?>">
                <p><input type="text" name="title" placeholder="Titulli" value="<?= e($edit["title"] ?? "") ?>" required></p>
                <p><textarea name="description" placeholder="Pershkrimi"><?= e($edit["description"] ?? "") ?></textarea></p>
                <p>
                    <select name="course_id" required>
                        <option value="">Zgjedh lenden</option>
                        <?php foreach ($myCourses as $course): ?>
                            <option value="<?= e($course["course_id"]) ?>" <?= (int)($edit["course_id"] ?? 0) === (int)$course["course_id"] ? "selected" : "" ?>>
                                <?= e($course["name"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p><input type="date" name="deadline" value="<?= e($edit["deadline"] ?? "") ?>"></p>
                <button type="submit" class="login-btn">Ruaj</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="dashboard-grid">
        <?php foreach ($assignments as $assignment): ?>
            <div class="dashboard-card">
                <h3><?= e($assignment["title"]) ?></h3>
                <p>Lenda: <?= e($assignment["course_name"]) ?></p>
                <p><?= e($assignment["description"] ?? "") ?></p>
                <p>Afati: <?= e($assignment["deadline"] ?? "Pa afat") ?></p>
                <?php if ($canManage): ?>
                    <a href="assignments.php?edit=<?= (int)$assignment["assignment_id"] ?>">Ndrysho</a>
                    <form method="POST" style="margin-top:10px;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$assignment["assignment_id"] ?>">
                        <input type="hidden" name="course_id" value="<?= (int)$assignment["course_id"] ?>">
                        <button type="submit" onclick="return confirm('A deshiron ta fshish detyren?')">Fshi</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (empty($assignments)): ?><p>Nuk ka detyra te regjistruara.</p><?php endif; ?>
</main>
</body>
</html>
