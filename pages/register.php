<?php
include('../includes/db.php');  // Database connection
session_start();

if (isset($_POST['register'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = 'user'; // Default role for users

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<script>alert('Email is already registered!');</script>";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fullname, $email, $password, $role]);

        // Log the user in after successful registration
        $_SESSION['user_id'] = $conn->lastInsertId();
        header("Location: ../pages/login.php"); // Redirect to homepage
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskfyer - Register</title>
    <link rel="shortcut icon" href="logo3.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../css/register.css">
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
            <div class="register-box">
                <h1>Welcome to Taskfyer</h1>
                <form action="register.php" method="POST">
                    <h3>Register for an Account</h3>
                    <p>Create an account. Already have an account? <a href="../pages/login.php">Login here</a></p>

                    <input type="text" name="fullname" placeholder="  Full Name" required>
                    <input type="email" name="email" placeholder="  Email" required>

                    <div class="password-box">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    <button type="submit" name="register" class="register-btn">Register Now</button>
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
