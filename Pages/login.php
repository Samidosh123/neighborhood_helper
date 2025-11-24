<?php
session_start();
include '../includes/db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];


            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            }elseif($user['role'] == 'user'){
                header("Location: ../pages/user_dashboard.php");
            }
             else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "No account found with this email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="slideshow"></div>
    <!-- Centered Login Card -->
    <div class="auth-container">
        <h2>Login</h2>

        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <?php if (!empty($message)) : ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <p>Don't have an account? <a href="register.php">Sign up</a></p>
    </div>
</body>
</html>
