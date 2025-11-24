<?php
session_start();
require_once '../includes/db_connect.php';

// Only admin can delete
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

// Check if id is provided
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = intval($_GET['id']);

// Prevent deleting yourself (optional)
if ($user_id == $_SESSION['user_id']) {
    header("Location: manage_users.php?error=You cannot delete your own account");
    exit();
}

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: manage_users.php?success=User deleted successfully");
} else {
    header("Location: manage_users.php?error=Failed to delete user");
}
exit();
?>
