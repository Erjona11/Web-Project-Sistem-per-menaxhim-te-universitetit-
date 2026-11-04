
<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: logout.php");
    exit;
}

$role = $user["role"];
$_SESSION["role"] = $role;

if ($role === "student") {
    $stmt = $conn->prepare("SELECT first_name, last_name FROM students WHERE user_id = ?");
} elseif ($role === "profesor") {
    $stmt = $conn->prepare("SELECT first_name, last_name FROM professors WHERE user_id = ?");
} else {
    $stmt = null;
}

$name = "";

if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $person = $stmt->get_result()->fetch_assoc();

    if ($person) {
        $name = $person["first_name"] . " " . $person["last_name"];
    }
}

$roles = [
    "student" => "Student",
    "profesor" => "Profesor",
    "admin" => "Administrator"
];

$title = $roles[$role] ?? "Përdorues";
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paneli - Student Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><span>Student</span> Hub</div>
            <a href="logout.php" class="login-btn">Dil</a>
        </nav>
    </header>

    <main class="dashboard">
        <h1>Përshëndetje <?= htmlspecialchars($title) ?>!</h1>
        <p>Mirë se erdhe <?= htmlspecialchars($name) ?> në platformën Student Hub.</p>

        <div class="dashboard-grid">
            <?php if ($role === "student"): ?>
                <div class="dashboard-card"><h3>Orari im</h3><p>Shiko orarin e ligjëratave.</p></div>
                <div class="dashboard-card"><h3>Lëndët e mia</h3><p>Shiko lëndët e regjistruara.</p></div>
                <a href="assignments.php" class="dashboard-card"><h3>Detyrat</h3><p>Kontrollo detyrat dhe afatet.</p></a>
                <a href="grades.php" class="dashboard-card"><h3>Notat e mia</h3><p>Shiko rezultatet akademike.</p></a>
                <div class="dashboard-card"><h3>Provimet</h3><p>Kontrollo datat e provimeve.</p></div>
                <div class="dashboard-card"><h3>Materialet</h3><p>Materialet për studim.</p></div>
            <?php elseif ($role === "profesor"): ?>
                <div class="dashboard-card"><h3>Lëndët e mia</h3><p>Menaxho lëndët që ligjëron.</p></div>
                <div class="dashboard-card"><h3>Studentët</h3><p>Shiko studentët e regjistruar.</p></div>
                <a href="grades.php" class="dashboard-card"><h3>Menaxhimi i notave</h3><p>Vendos dhe përditëso notat.</p></a>
                <a href="assignments.php" class="dashboard-card"><h3>Detyrat</h3><p>Krijo dhe menaxho detyra.</p></a>
                <div class="dashboard-card"><h3>Njoftimet</h3><p>Publiko njoftime akademike.</p></div>
                <div class="dashboard-card"><h3>Konsultimet</h3><p>Menaxho konsultimet.</p></div>
            <?php elseif ($role === "admin"): ?>
                <div class="dashboard-card"><h3>Përdoruesit</h3><p>Menaxho llogaritë e përdoruesve.</p></div>
                <div class="dashboard-card"><h3>Departamentet</h3><p>Menaxho departamentet.</p></div>
                <div class="dashboard-card"><h3>Lëndët</h3><p>Menaxho lëndët universitare.</p></div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
