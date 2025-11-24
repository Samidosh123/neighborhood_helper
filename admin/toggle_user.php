<?php
session_start();
require_once '../includes/db_connect.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../pages/login.php");
    exit();
}

if(isset($_GET['id']) && isset($_GET['action'])){
    $user_id = intval($_GET['id']);
    $action = $_GET['action'];

    if($action == "deactivate"){
        $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
    } elseif($action == "activate"){
        $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
    }

    if(isset($stmt)){
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: manage_users.php");
exit();
?>
