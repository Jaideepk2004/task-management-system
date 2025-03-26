<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow requests from any origin (change to specific domain if needed)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
session_start();
include('../includes/db.php'); // Include database connection

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access. Please log in."]);
    exit();
}

$user_fullname = $_SESSION['user_fullname']; // Retrieve user's full name (if needed)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['taskTitle']);
    $description = trim($_POST['taskDescription']);
    $priority = trim($_POST['taskPriority']);
    $date = trim($_POST['taskDueDate']);

    // Validate input
    if (empty($title) || empty($priority) || empty($date)) {
        echo json_encode(["success" => false, "message" => "Title, priority, and date are required!"]);
        exit();
    }

    // Ensure priority is valid (matching ENUM values in MySQL)
    $valid_priorities = ['low', 'medium', 'high'];
    if (!in_array($priority, $valid_priorities)) {
        echo json_encode(["success" => false, "message" => "Invalid priority value!"]);
        exit();
    }

    try {
        // Insert task into the database
        $stmt = $conn->prepare("INSERT INTO tasks (title, description, priority, date) VALUES (:title, :description, :priority, :date)");
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':priority', $priority, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Task created successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }   
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskfyer - Register</title>
    <link rel="shortcut icon" href="../images/logo3.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../css/taskpage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <img src="../images/logo3.jpg" alt="Logo">
        <div class="company-name">Taskfyer</div>
        <a href="pages/register.php" class="auth-btn">
            <div class="profile-box">
                <i class="fa-solid fa-circle-user"></i><h2><?php echo htmlspecialchars($user_fullname)?></h2>
            </div>
        </a>
    </nav>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-icons">
                <a href="../pages/tasks.php" class="icon"><i class="fa-solid fa-grip"></i></a>
                <a href="#" class="icon"><i class="fa-solid fa-clipboard-check"></i></a>
                <a href="#" class="icon"><i class="fa-regular fa-clock"></i></a>
            </div>
        </aside>
        <!-- Big Container -->
        <div class="big-container">
            <div class="task-header-container">
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="low">Low</button>
                    <button class="filter-btn" data-filter="medium">Medium</button>
                    <button class="filter-btn" data-filter="high">High</button>
                </div>
            
                <div class="container">
                    <!-- Task List -->
                    <div class="task-cards"></div>
                    <div id="tasksContainer"></div>
                </div>
            </div>
        </div>
        <!-- Right Sidebar -->
        <aside class="right-sidebar">
            <div class="user-profile">
                <img src="../images/logo3.jpg" alt="User">
                <div>
                    <p>Hello,</p>
                    <h2><?php echo htmlspecialchars($user_fullname)?></h2>
                </div>
            </div>
        
            <div class="task-stats">
                <div class="stat-box">
                    <p>Total Tasks</p>
                    <span id="total-tasks">0</span>
                </div>
                <div class="stat-box">
                    <p>In Progress</p>
                    <span id="in-progress">0</span>
                </div>
                <div class="stat-box">
                    <p>Open Tasks</p>
                    <span id="open-tasks">0</span>
                </div>
                <div class="stat-box">
                    <p>Completed</p>
                    <span id="completed-tasks">0</span>
                </div>
            </div>
        
            <h3 class="activity">Completed vs Pending Tasks</h3>
            <div class="chart-container">
                <canvas id="progressChart"></canvas>
                <p>Task completion improved by 12% this month</p>
            </div>
        
            <button class="signout-btn">Sign Out</button>
        </aside>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/taskpage.js"></script>
    <script src="../js/completed_tasks.js"></script>
</body>
</html>
