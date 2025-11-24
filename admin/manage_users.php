<?php 
session_start();
require_once '../includes/db_connect.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../pages/login.php");
    exit();
}

// Get all users with status
$result = $conn->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="eng">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin_body">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>⚙️ Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">📂 Manage Issues</a></li>
            <li><a href="manage_users.php" class="active">👥 Manage Users</a></li>
            <li><a href="estate.php">🏘️Manage Estates</a></li>
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
        <h2>👥 Manage Users</h2>
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td>
                                <?php if($row['status'] == 'active'): ?>
                                    <span style="color: green; font-weight: bold;">Active</span>
                                <?php else: ?>
                                    <span style="color: red; font-weight: bold;">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn-edit">✏️ Edit</a>
                                
                                <?php if($row['status'] == 'active'): ?>
                                    <a href="toggle_user.php?id=<?= $row['id'] ?>&action=deactivate" 
                                       class="btn-delete" 
                                       onclick="return confirm('Are you sure you want to deactivate this user?');">🚫 Deactivate</a>
                                <?php else: ?>
                                    <a href="toggle_user.php?id=<?= $row['id'] ?>&action=activate" 
                                       class="btn-edit" 
                                       onclick="return confirm('Are you sure you want to activate this user?');">✅ Activate</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No Users Found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
