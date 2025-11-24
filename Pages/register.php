<?php
include '../includes/db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = "user"; // default role

    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        // Show success message, then redirect
        $message = "✅ Registration successful! Redirecting to login...";
        echo "<script>
                setTimeout(function(){
                    window.location.href = 'login.php';
                }, 2000); // 2 seconds
              </script>";
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="slideshow"></div> <!-- background slideshow -->

    <div class="auth-container">
        <h2>Register</h2>

        <form action="register.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>

            <!-- 👇 Password with toggle -->
            <div class="password-wrapper">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword()">👁</span>
            </div>

            <button type="submit">Register</button>
        </form>

        <!-- 👇 Show success/error messages -->
        <?php if (!empty($message)) : ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById("password");
            const icon = document.querySelector(".toggle-password");
            if (pwd.type === "password") {
                pwd.type = "text";
                icon.textContent = "🙈"; // change icon when visible
            } else {
                pwd.type = "password";
                icon.textContent = "👁"; // revert back
            }
        }
    </script>
</body>
</html>
