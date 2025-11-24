<?php
session_start();
include '../includes/db_connect.php';

// Check if the admin is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../pages/login.php");
    exit();
}

$message = "";
$edit_estate = null;

// Add estate
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_estate'])) {
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);

    if (!empty($name) && !empty($location)) {
        $stmt = $conn->prepare("INSERT INTO estates (name, location) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $location);

        if ($stmt->execute()) {
            $message = "✅ Estate added successfully.";
        } else {
            $message = "⚠️ Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "⚠️ Estate name and location cannot be empty.";
    }
}

// Delete estate
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM estates WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "🗑️ Estate deleted successfully.";
    } else {
        $message = "⚠️ Error: " . $conn->error;
    }
    $stmt->close();
}

// Load estate for editing
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM estates WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_estate = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Update estate
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_estate'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);

    if (!empty($name) && !empty($location)) {
        $stmt = $conn->prepare("UPDATE estates SET name = ?, location = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $location, $id);

        if ($stmt->execute()) {
            $message = "✏️ Estate updated successfully.";
        } else {
            $message = "⚠️ Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "⚠️ Estate name and location cannot be empty.";
    }
}

// Fetch all estates
$result = $conn->query("SELECT * FROM estates ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Estates</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-body">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Neighborhood Helper</h2>
        <ul>
            <li><a href="dashboard.php">Manage Issues</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="estate.php" class="active">🏘 Manage Estates</a></li>
            <li><a href="analytics.php">📊 Reports & Analytics</a></li>
            <li><a href="feedback_admin.php">💬 User Feedback</a></li>
            <li><a href="system_settings.php">⚙️ System Settings</a></li>
            <li><a href="map_admin.php">🌍 Issue Map</a></li>
            <li><a href="profile_admin.php">👤 Admin Profile</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>🏘 Manage Estates</h1>

        <?php if (!empty($message)) echo "<p class='msg'>$message</p>"; ?>

        <!-- Add / Edit Estate Form -->
        <?php if ($edit_estate): ?>
            <h3>✏️ Edit Estate</h3>
            <form method="POST" class="form-box">
                <input type="hidden" name="id" value="<?= $edit_estate['id'] ?>">
                
                <label>Estate Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($edit_estate['name']) ?>" required>

                <label>Location:</label>
                <input type="text" name="location" value="<?= htmlspecialchars($edit_estate['location']) ?>" required>

                <button type="submit" name="update_estate">💾 Update Estate</button>
                <a href="estate.php">❌ Cancel</a>
            </form>
        <?php else: ?>
            <h3>➕ Add Estate</h3>
            <form method="POST" class="form-box">
                <label>Estate Name:</label>
                <input type="text" name="name" required>

                <label>Location:</label>
                <input type="text" name="location" required>

                <button type="submit" name="add_estate">➕ Add Estate</button>
            </form>
        <?php endif; ?>

        <hr>

        <!-- Estates List -->
        <h3>Existing Estates</h3>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Estate Name</th>
                <th>Location</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td>
                    <a href="estate.php?edit=<?= $row['id'] ?>">✏️ Edit</a> | 
                    <a href="estate.php?delete=<?= $row['id'] ?>" 
                       onclick="return confirm('Are you sure you want to delete this estate?')">🗑️ Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
