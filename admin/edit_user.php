<?php 
session_start();
require_once '../includes/db_connect.php';

// check if admin is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header('Location: ../pages/login.php');
    exit();
}

// check if user id is provided
if(!isset($_GET['id'])){
    header('Location: manage_users.php');
    exit();
}

$user_id = $_GET['id'];

// fetch user details
$stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    echo "User not found";
    exit();
}
$user = $result->fetch_assoc();

// update user if form submitted
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $update = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
    $update->bind_param("sssi", $name, $email, $role, $user_id);

    if($update->execute()){
        header('Location: manage_users.php?success=1');
        exit();
    }else{
        echo "Error updating user";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
    <div class="main-content edit-user-page">
        <h1>Edit User</h1>
        <form method="POST" class="edit-user-form">
            <label>Full Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <button type="submit" class="btn-edit">💾 Save Changes</button>
            <a href="manage_users.php" class="btn-cancel">❌ Cancel</a>
        </form>
    </div>
</body>
</html>
