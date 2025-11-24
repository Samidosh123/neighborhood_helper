<?php 
session_start();
include '../includes/db_connect.php';

// Check if the admin is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../pages/login.php");
    exit();
}

// Handle status/category update
if(isset($_POST['update_status'])){
    $issue_id = $_POST['issue_id'];
    $new_status = $_POST['status'];
    $new_category = $_POST['category_id'];

    $stmt = $conn->prepare("UPDATE issues SET status = ?, category_id = ? WHERE id = ?");
    $stmt->bind_param("sii", $new_status, $new_category, $issue_id);
    $stmt->execute();
}

// Fetch issues with category names
$result = $conn->query("
    SELECT issues.id, users.name AS user_name, issues.title, issues.description,
           issues.status, issues.image, issues.created_at, issues.category_id,
           categories.name AS category_name
    FROM issues
    JOIN users ON issues.user_id = users.id
    LEFT JOIN categories ON issues.category_id = categories.id
    ORDER BY issues.created_at DESC
");

if(!$result){
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background: #1e293b;
            color: #fff;
            padding: 20px;
            overflow-y: auto;
        }

        /* Main content area */
        .main-content {
            margin-left: 240px;
            padding: 20px;
            background: #f4f6f9;
            min-height: 100vh;
            overflow-x: auto;
        }

        h1 {
            margin-bottom: 20px;
        }

        /* Table styling */
        .issue-table {
            border-collapse: collapse;
            width: 100%;
            min-width: 1200px;
        }

        .issue-table th, .issue-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .issue-table th {
            background: #e5e7eb;
            font-weight: 600;
        }

        /* Description cell */
        .description-cell {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Fix overflow hiding form elements */
        .issue-table td {
            overflow: visible !important;
            white-space: normal !important;
        }

        /* Form styling in table */
        .issue-table form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .issue-table select {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #fff;
        }

        .issue-table button {
            padding: 6px 12px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
        }

        .issue-table button:hover {
            background: #1d4ed8;
        }

        /* Status badges */
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
        }

        .status.pending { background: #facc15; color: #000; }
        .status.in-progress { background: #3b82f6; color: #fff; }
        .status.resolved { background: #22c55e; color: #fff; }

        /* Sidebar links */
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 12px 0;
        }
        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
        }
        .sidebar ul li a.active {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body class="dashboard-body">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Neighborhood Helper</h2>
        <ul>
            <li><a href="dashboard.php" class="active">Manage Issues</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="estate.php">🏘️ Manage Estates</a></li>
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
        <h1>Issue Management</h1>
        <table class="issue-table">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Title</th>
                <th>Description</th>
                <th>Category</th>
                <th>Image</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['user_name']); ?></td>
                    <td><?= htmlspecialchars($row['title']); ?></td>
                    <td class="description-cell" title="<?= htmlspecialchars($row['description']); ?>">
                        <?= htmlspecialchars($row['description']); ?>
                    </td>
                    <td><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                    <td>
                        <?php if(!empty($row['image'])): ?>
                            <img src="../<?= $row['image']; ?>" alt="Issue Image" width="100">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><span class="status <?= strtolower(str_replace(' ', '-', $row['status'])); ?>"><?= $row['status']; ?></span></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="issue_id" value="<?= $row['id']; ?>">

                            <!-- Category Dropdown -->
                            <select name="category_id">
                                <?php 
                                $cats = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                                while($cat = $cats->fetch_assoc()):
                                    $selected = ($row['category_id'] == $cat['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $cat['id']; ?>" <?= $selected; ?>><?= $cat['name']; ?></option>
                                <?php endwhile; ?>
                            </select>

                            <!-- Status Dropdown -->
                            <select name="status">
                                <option value="Pending" <?= ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="In Progress" <?= ($row['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Resolved" <?= ($row['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                            </select>

                            <button type="submit" name="update_status">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>  
