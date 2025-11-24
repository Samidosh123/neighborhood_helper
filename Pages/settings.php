<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success = $error = "";

// ✅ Fetch current user info
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
if (!$stmt) {
    die("❌ SQL error: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// ✅ Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username !== '' && $email !== '') {
        if ($password !== '') {
            // Update with password
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?");
            if (!$update) {
                die("❌ SQL error: " . $conn->error);
            }
            $update->bind_param("sssi", $name, $email, $hashed, $user_id);
        } else {
            // Update without password
            $update = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
            if (!$update) {
                die("❌ SQL error: " . $conn->error);
            }
            $update->bind_param("ssi", $name, $email, $user_id);
        }

        if ($update->execute()) {
            $success = "✅ Settings updated successfully!";
        } else {
            $error = "❌ Update failed: " . $update->error;
        }
        $update->close();
    } else {
        $error = "⚠️ Username and Email cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="settings-body">
    <div class="settings-page">
        <h2>⚙ Account Settings</h2>

        <?php if ($success): ?><p class="success-message"><?= htmlspecialchars($success) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error-message"><?= htmlspecialchars($error) ?></p><?php endif; ?>

        <form method="POST" action="settings.php">
            <label>Username</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>New Password </label>
            <input type="password" name="password" placeholder="Enter new password">

            <button type="submit">Save Changes</button>
        </form>

        <p class="mt"><a href="profile.php">👤 Back to Profile</a></p>
        <p><a href="user_dashboard.php">⬅ Back to Dashboard</a></p>
    </div>
</body>
</html>
