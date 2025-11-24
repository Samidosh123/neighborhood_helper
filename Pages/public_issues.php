<?php 
session_start();
include '../includes/db_connect.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$result = $conn->query("
    SELECT issues.id, users.name, issues.title, issues.description, 
           categories.name AS category_name, issues.status, issues.image, issues.created_at 
    FROM issues 
    JOIN users ON issues.user_id = users.id 
    LEFT JOIN categories ON issues.category_id = categories.id
    ORDER BY issues.created_at DESC
");


    if(!$result){
        die("query failed:". $conn->error);
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Public Issues-Neighborhood Helper</title>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body class="public-issues-page">
        <!--sidebar-->
          <!-- Sidebar -->
    <div class="sidebar">
        <h2>Neighborhood Helper</h2>
        <ul>
            <li><a href="user_dashboard.php">🏠 Dashboard</a></li>
            <li><a href="profile.php">👤 My Profile</a></li>
            <li><a href="report_issue.php">➕ Report Issue</a></li>
            <li><a href="my_issues.php">📌 My Issues</a></li>
            <li><a href="public_issues.php" class="active">🌍 Public Issues</a></li>
            <li><a href="map.php">🗺 View Issue Map</a></li>
            <li><a href="feedback.php">💬 Feedback & Rating</a></li>
            <li><a href="logout.php" class="logout">🚪 Logout</a></li>
        </ul>
    </div>
    <!--main content-->
    <div class="main-content">
        <h2>🌍 Public Issues</h2>
        <p>Here you are able to view all issues reported in the community</p>

        <div class="issue-list">
             <?php while($row = $result->fetch_assoc()): ?>
                <div class="issue-card">
                    <h3><?= htmlspecialchars($row['title']); ?></h3>
                    <p><strong>Reported by:</strong> <?= htmlspecialchars($row['name']); ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($row['category_name']??'Uncategorized'); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="status <?= strtolower($row['status']); ?>">
                            <?= $row['status']; ?>
                        </span>
                    </p>
                    <p><?= nl2br(htmlspecialchars($row['description'])); ?></p>
                    <?php if(!empty($row['image'])): ?>
                        <img src="../<?= $row['image']; ?>" alt="Issue Image" width="200">
                    <?php endif; ?>
                    <p><small>Reported on <?= $row['created_at']; ?></small></p>
        </div>
        <?php endwhile; ?>
    </div>
    </div>
    </body>
</html>