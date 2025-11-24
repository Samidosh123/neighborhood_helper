<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$success = $error = "";

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $message = trim($_POST['message'] ?? '');
    $rating  = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

    if ($message !== '' && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, message, rating) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $message, $rating);
        if ($stmt->execute()) {
            $success = "✅ Feedback sent successfully!";
        } else {
            $error = "❌ Failed to send feedback: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "⚠️ Please provide feedback text and a rating between 1 and 5.";
    }
}

// Fetch user's feedback with admin replies
$listStmt = $conn->prepare("SELECT message, rating, created_at, admin_reply 
                            FROM feedback 
                            WHERE user_id = ? 
                            ORDER BY created_at DESC");

if (!$listStmt) {
    die("SQL prepare failed: " . $conn->error);
}

$listStmt->bind_param("i", $_SESSION['user_id']);
$listStmt->execute();
$myFeedback = $listStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Feedback & Rating</title>
    <link rel="stylesheet" href="../css/style.css?v=6">
</head>
<body>
    <div class="feedback-page">
        <h2>💬 Feedback & Rating</h2>

        <?php if ($success): ?><p class="success-message"><?= htmlspecialchars($success) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error-message"><?= htmlspecialchars($error) ?></p><?php endif; ?>

        <form method="POST" action="feedback.php" class="feedback-form">
            <label for="message">Your feedback</label>
            <textarea id="message" name="message" rows="4" placeholder="Tell us what's working and what to improve..." required></textarea>

            <label>Your rating</label>
            <div class="rating-stars">
                <input type="radio" id="star5" name="rating" value="5"><label for="star5" title="5 stars">★</label>
                <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 stars">★</label>
                <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 stars">★</label>
                <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 stars">★</label>
                <input type="radio" id="star1" name="rating" value="1" checked><label for="star1" title="1 star">★</label>
            </div>

            <button type="submit">Submit Feedback</button>
        </form>

        <h3 class="mt">My Previous Feedback</h3>
        <ul class="feedback-list">
            <?php if ($myFeedback && $myFeedback->num_rows > 0): ?>
                <?php while ($row = $myFeedback->fetch_assoc()): ?>
                    <li>
                        <span class="rating-inline">
                            <?= str_repeat("★", (int)$row['rating']) . str_repeat("☆", 5 - (int)$row['rating']); ?>
                        </span>
                        <div class="fb-text"><?= nl2br(htmlspecialchars($row['message'])) ?></div>
                        <small class="fb-time"><?= htmlspecialchars($row['created_at']) ?></small>

                        <?php if (!empty($row['admin_reply'])): ?>
                            <div class="admin-response">
                                <strong>Admin reply:</strong> <?= nl2br(htmlspecialchars($row['admin_reply'])) ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>No feedback yet.</li>
            <?php endif; ?>
        </ul>

        <p class="mt"><a class="back-link" href="user_dashboard.php">⬅ Back to dashboard</a></p>
    </div>
</body>
</html>
