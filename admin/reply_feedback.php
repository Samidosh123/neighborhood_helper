<?php
session_start();
require_once '../includes/db_connect.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $feedback_id = intval($_POST['feedback_id']);
    $admin_reply = trim($_POST['admin_reply']);

    if(!empty($admin_reply)){
        $stmt = $conn->prepare("UPDATE feedback SET admin_reply = ? WHERE id = ?");
        $stmt->bind_param("si", $admin_reply, $feedback_id);

        if($stmt->execute()){
            header("Location: feedback_admin.php?success=1");
        } else {
            header("Location: feedback_admin.php?error=1");
        }
        $stmt->close();
    }
}
