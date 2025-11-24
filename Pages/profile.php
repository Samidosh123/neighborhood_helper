<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

function getInitials($name) {
    $words = explode(" ", $name);
    $initials = "";
    foreach ($words as $w) {
        $initials .= strtoupper($w[0]);
    }
    return substr($initials, 0, 2); // only 2 letters
}

$initials = getInitials($user['name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>👤 My Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
     <div class="slideshow">
        <div class="slide" style="background-image: url('../images/bg1.jpg');"></div>
        <div class="slide" style="background-image: url('../images/bg2.jpg');"></div>
        <div class="slide" style="background-image: url('../images/bg3.jpg');"></div>
        <div class="overlay"></div> <!-- Gradient overlay -->
    </div>
    <div class="profile-page">
        <!-- Avatar with initials only -->
        <div class="avatar-initials"><?= $initials ?></div>

        <h2><?= htmlspecialchars($user['name']) ?></h2>

        <div class="profile-info">
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Joined:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
        </div>

        <div class="profile-actions">
            <a href="settings.php">⚙ Go to Settings</a>
            <a href="user_dashboard.php">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
