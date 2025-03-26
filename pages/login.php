<?php 
include('../includes/db.php');  // Include database connection
session_start();

$error_message = "";  // Initialize error message

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL query
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Successful login
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        $_SESSION['user_fullname'] = $user['fullname']; // Store user fullname in session
        header("Location: ../pages/tasks.php"); // Redirect to the main page
        exit();
    } else {
        // Invalid login
        $error_message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskfyer - Login</title>
    <link rel="shortcut icon" href="logo3.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <img src="../images/logo3.jpg" alt="Product Image">
        <div class="company-name">Taskfyer</div>
    </nav>

    <div class="container">
        <!-- Main Content -->
        <main class="main-content">
            <div class="login-box">
                <h1>Welcome Back to Taskfyer</h1>
                <form action="login.php" method="POST">
                    <h3>Login to Your Account</h3>
                    <p>Don't have an account? <a href="../pages/register.php">Register here</a></p>

                    <input type="email" name="email" placeholder="  Email" required>

                    <div class="password-box">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <?php if (!empty($error_message)): ?>
                        <p class="error-message"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    <button type="submit" class="login-btn" name="login">Login Now</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var toggleButton = document.querySelector(".toggle-password i");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleButton.classList.remove("fa-eye");
                toggleButton.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                toggleButton.classList.remove("fa-eye-slash");
                toggleButton.classList.add("fa-eye");
            }
        }
    </script>

</body>
</html>
