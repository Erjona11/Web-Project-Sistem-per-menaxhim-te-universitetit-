
<?php
// Fillon sesioni nëse nuk është aktiv.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lidhja me databazën.
require_once __DIR__ . "/db.php";

// Kontrollon nëse përdoruesi është kyçur.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Merr ID-në e përdoruesit.
$userId = (int) $_SESSION["user_id"];

// Kontrollon përdoruesin dhe rolin në databazë.
$stmt = $conn->prepare(
    "SELECT user_id, role FROM users WHERE user_id = ?"
);

$stmt->bind_param("i", $userId);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Nëse përdoruesi nuk ekziston, ndërpritet sesioni.
if (!$user) {
    header("Location: logout.php");
    exit;
}

// Ruajmë rolin aktual.
$role = $user["role"];
$_SESSION["role"] = $role;

// Funksion për kontrollimin e roleve.
function requireRole($allowedRoles) {
    global $role;

    if (!in_array($role, $allowedRoles, true)) {
        http_response_code(403);
        exit("403 - Nuk keni autorizim për këtë veprim.");
    }
}

// Funksion për shfaqjen e sigurt të tekstit.
function e($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}
?>
