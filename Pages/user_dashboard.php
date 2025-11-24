<?php
session_start();
include '../includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch issues
$stmt = $conn->prepare("
    SELECT issues.id, issues.title, issues.status, issues.created_at, categories.name AS category_name
    FROM issues
    LEFT JOIN categories ON issues.category_id = categories.id
    WHERE issues.user_id = ?
    ORDER BY issues.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard | Neighborhood Helper</title>
    <link rel="stylesheet" href="../css/style.css?v=3">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f7f9fc;
            display: flex;
        }
        /* Sidebar */
        .sidebar {
            width: 220px;
            background: #004aad;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            padding: 20px;
        }
        .sidebar h2 {
            font-size: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar li {
            margin: 15px 0;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 8px 10px;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Main Content */
        .main-content {
            margin-left: 240px;
            padding: 40px;
            width: 100%;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        header h2 {
            color: #004aad;
        }

        /* Cards */
        .cards {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            flex: 1;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }
        th {
            background: #004aad;
            color: white;
        }
        .status.resolved { color: green; font-weight: bold; }
        .status.pending { color: orange; font-weight: bold; }

        /* About + Reviews */
        .about, .reviews {
            background: white;
            margin-top: 50px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .about h3, .reviews h3 {
            color: #004aad;
            margin-bottom: 15px;
        }
        .review-card {
            background: #f4f6f8;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
        }
        .review-card strong {
            color: #004aad;
        }

        footer {
            text-align: center;
            padding: 20px;
            margin-top: 50px;
            background: #004aad;
            color: white;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Neighborhood Helper</h2>
    <ul>
        <li><a href="user_dashboard.php">🏠 Dashboard</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="report_issue.php">➕ Report Issue</a></li>
        <li><a href="my_issues.php">📋 My Issues</a></li>
        <li><a href="public_issues.php">🌍 Public Issues</a></li>
        <li><a href="view_map.php">🗺 Map</a></li>
        <li><a href="feedback.php">💬 Feedback</a></li>
        <li><a href="logout.php" class="logout">🚪 Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <header>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋</h2>
    </header>

    <!-- Cards -->
    <section class="cards">
        <div class="card">
            <h3>Total Issues</h3>
            <p>
                <?php 
                    $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM issues WHERE user_id=?");
                    $countStmt->bind_param("i", $user_id);
                    $countStmt->execute();
                    echo $countStmt->get_result()->fetch_assoc()['total'];
                ?>
            </p>
        </div>
        <div class="card">
            <h3>Resolved Issues</h3>
            <p>
                <?php 
                    $resolvedStmt = $conn->prepare("SELECT COUNT(*) AS total FROM issues WHERE user_id=? AND status='Resolved'");
                    $resolvedStmt->bind_param("i", $user_id);
                    $resolvedStmt->execute();
                    echo $resolvedStmt->get_result()->fetch_assoc()['total'];
                ?>
            </p>
        </div>
    </section>

    <!-- My Issues -->
    <section class="table-section">
        <h3>📋 My Reported Issues</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                    <td><span class="status <?php echo strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </section>

    <!-- About Section -->
    <section class="about">
        <h3>ℹ️ About Neighborhood Helper</h3>
        <p>
            Neighborhood Helper is a community-driven reporting platform that empowers residents to report local issues such as 
            potholes, streetlight failures, or waste dumping. It bridges the gap between citizens and authorities by 
            promoting accountability and collaboration for a cleaner, safer, and smarter neighborhood.
        </p>
    </section>

    <!-- Why Choose Us -->
    <section class="about">
        <h3>🌟 Why Choose Neighborhood Helper?</h3>
        <ul>
            <li>✅ Simple and quick reporting process.</li>
            <li>📍 Real-time issue tracking and mapping.</li>
            <li>🤝 Connects residents and local authorities directly.</li>
            <li>💬 Transparent feedback and updates.</li>
        </ul>
    </section>

    <!-- Reviews Section -->
    <section class="reviews">
        <h3>💬 What Our Users Say</h3>
        <div class="review-card">
            <strong>Mary W.</strong> ⭐⭐⭐⭐☆<br>
            “I love how fast the county responded to my report about streetlights. This app truly helps!”
        </div>
        <div class="review-card">
            <strong>James K.</strong> ⭐⭐⭐⭐⭐<br>
            “Neighborhood Helper makes it easy to make my voice heard — great job!”
        </div>
        <div class="review-card">
            <strong>Anne N.</strong> ⭐⭐⭐⭐☆<br>
            “Clean design and simple to use. I hope every community adopts this system.”
        </div>
    </section>

    <footer>
        <p>© 2025 Neighborhood Helper | Together for a Better Community 🌍</p>
    </footer>
</div>

</body>
</html>
