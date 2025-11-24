<?php 
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../pages/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="dashboard-body">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Neighborhood Helper</h2>
        <ul>
            <li><a href="dashboard.php">📋 Manage Issues</a></li>
            <li><a href="manage_users.php">👥 Manage Users</a></li>
            <li><a href="analytics.php">📊 Reports & Analytics</a></li>
            <li><a href="feedback_admin.php">💬 User Feedback</a></li>
            <li><a href="system_settings.php" class="active">⚙️ System Settings</a></li>
            <li><a href="map_admin.php">🌍 Issue Map</a></li>
            <li><a href="profile_admin.php">👤 Admin Profile</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h1>System Settings</h1>

        <!-- Settings Navigation -->
        <ul class="settings-menu">
               <li><a href="system_settings.php?page=categories" 
           class="<?php echo (isset($_GET['page']) && $_GET['page']=='categories') ? 'active' : ''; ?>">
           📂 Categories</a></li>

    <li><a href="system_settings.php?page=roles" 
           class="<?php echo (isset($_GET['page']) && $_GET['page']=='roles') ? 'active' : ''; ?>">
           🔑 Roles</a></li>

    <li><a href="system_settings.php?page=general" 
           class="<?php echo (isset($_GET['page']) && $_GET['page']=='general') ? 'active' : ''; ?>">
           ⚙️ General</a></li>
        </ul>

        <div class="settings-content">
            <?php 
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                switch ($page) {
                    case "categories":
                        include("categories.php");
                        break;
                    case "roles":
                        include("roles.php");
                        break;
                    case "general":
                        include("general.php");
                        break;
                    default:
                        echo "<p>Invalid page access.</p>";
                }
            } else {
                echo "<p>Select a settings option from the menu above.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
