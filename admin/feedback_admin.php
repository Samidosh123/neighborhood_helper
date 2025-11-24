<?php 
session_start();
require_once '../includes/db_connect.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Fetch feedback with replies
$sql = "SELECT f.id, f.user_id, f.rating, f.message, f.admin_reply, f.created_at, u.name 
        FROM feedback f 
        JOIN users u ON f.user_id = u.id
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Feedback</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="../css/style.css?v=2025-09-01">
</head>
<body class="reports-page">

    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">🏠 Dashboard</a></li>
            <li><a href="feedback_admin.php" class="active">💬 User Feedback</a></li>
            <li><a href="estate.php">🏘️Manage Estates</a></li>
            <li><a href="analytics.php">📊 Reports & Analytics</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="admin-header">💬 User Feedback</header>
        <main class="admin-main">
            <div class="feedback-container">

                <?php if($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="feedback-card">
                            <p><strong>User:</strong> <?= htmlspecialchars($row['name']) ?></p>
                            <p><strong>Rating:</strong> ⭐ <?= (int)$row['rating'] ?></p>
                            <p><strong>Message:</strong> <?= htmlspecialchars($row['message']) ?></p>
                            <p><small>Posted: <?= htmlspecialchars($row['created_at']) ?></small></p>

                            <?php if(!empty($row['admin_reply'])): ?>
                                <div class="admin-reply">
                                    <strong>Admin Reply:</strong>
                                    <p><?= htmlspecialchars($row['admin_reply']) ?></p>
                                </div>
                            <?php else: ?>
                                <!-- Reply form -->
                                <form method="POST" action="reply_feedback.php" class="reply-form">
                                    <input type="hidden" name="feedback_id" value="<?= (int)$row['id'] ?>">
                                    <textarea name="admin_reply" placeholder="Write your reply..." required></textarea>
                                    <button type="submit" class="btn-primary">Reply</button>
                                </form>
                            <?php endif; ?>

                            <a href="delete_feedback.php?id=<?= (int)$row['id'] ?>" class="btn-danger">Delete</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No feedback available.</p>
                <?php endif; ?>

            </div>
        </main>
    </div>
</body>
</html>
