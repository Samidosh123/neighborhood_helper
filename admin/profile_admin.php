<?php 
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}
$admin_id = $_SESSION['user_id'];
$message = "";

// Fetch current admin details
$stmt = $conn->prepare("SELECT name, email, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $profilePic = $admin['profile_pic']; // default

    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['profile_pic']['name']);
        $targetFile = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
                $profilePic = "uploads/" . $fileName;
            }
        }
    }

    // Update DB
    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, profile_pic=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $profilePic, $admin_id);

    if ($stmt->execute()) {
        $message = "✅ Profile updated successfully.";
        $_SESSION['name'] = $name; // update session name
    } else {
        $message = "❌ Error updating profile: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        display: flex;
        background: url('../images/bg1.jpg') no-repeat center center fixed; 
        background-size: cover;
        overflow-x: auto; /* Allow horizontal scrolling */
    }
    .sidebar {
        width: 220px;
        background: rgba(30, 41, 59, 0.95);
        color: #fff;
        min-height: 100vh;
        padding: 20px;
    }
    .sidebar ul {
        list-style: none;
        padding: 0;
    }
    .sidebar ul li {
        margin: 15px 0;
    }
    .sidebar ul li a {
        color: #fff;
        text-decoration: none;
        display: block;
        padding: 10px;
        border-radius: 8px;
    }
    .sidebar ul li a:hover,
    .sidebar ul li a.active {
        background: #0f172a;
    }
    .profile-container {
        flex: 1;
        padding: 40px;
        margin: 20px;
        max-width: 800px; /* container width */
        background: rgba(255, 255, 255, 0.85); /* semi-transparent */
        backdrop-filter: blur(10px); /* glass effect */
        border-radius: 12px;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
        text-align: center;
    }
    .profile-container img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 3px solid #fff;
    }
    .profile-container input, 
    .profile-container button {
        width: 80%;
        padding: 10px;
        margin: 8px 0;
        border-radius: 6px;
        border: 1px solid #ccc;
    }
    .profile-container button {
        background: #1e293b;
        color: #fff;
        cursor: pointer;
        border: none;
    }
    .profile-container button:hover {
        background: #334155;
    }
    .message {
        margin-bottom: 15px;
        font-weight: bold;
    }
</style>

</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
            <li><a href="dashboard.php">Manage Issues</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="estate.php">🏘️ Manage Estates</a></li>
            <li><a href="analytics.php">📊 Reports & Analytics</a></li>
            <li><a href="feedback_admin.php">💬 User Feedback</a></li>
            <li><a href="system_settings.php">⚙️ System Settings</a></li>
            <li><a href="map_admin.php">🌍 Issue Map</a></li>
            <li><a href="profile_admin.php" class="active">👤 Admin Profile</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Profile Section -->
    <div class="profile-container">
        <h2>👤 Admin Profile</h2>

        <?php if ($message): ?>
            <p class="message"><?= $message; ?></p>
        <?php endif; ?>

        <img src="../<?= $admin['profile_pic'] ?? 'images/default-avatar.png'; ?>" alt="Profile Picture">

        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" value="<?= htmlspecialchars($admin['name']); ?>" required><br>
            <input type="email" name="email" value="<?= htmlspecialchars($admin['email']); ?>" required><br>
            <input type="file" name="profile_pic" accept="image/*"><br>
            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
