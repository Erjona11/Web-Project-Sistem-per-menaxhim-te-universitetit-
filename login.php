
<?php
session_start();
require_once "db.php";

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["role"] = $user["role"];
        header("Location: dashboard.php");
        exit;
    }

    $error = "Emaili ose fjalëkalimi është gabim.";
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>Student <span>Hub</span></h1>
            <h2>Mirë se erdhe!</h2>
            <p>Hyr në llogarinë tënde akademike</p>

            <?php if ($error): ?>
                <div class="login-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Shkruaj emailin" required>

                <label for="password">Fjalëkalimi</label>
                <input type="password" id="password" name="password" placeholder="Shkruaj fjalëkalimin" required>

                <button type="submit">Hyr në platformë</button>
            </form>

            <a href="index.php" class="back-link">Kthehu në ballinë</a>
        </div>
    </div>
</body>
</html>
