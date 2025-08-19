<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: staff_dashboard.php");
    exit();
}

// Fetch user role
$user_id = $_SESSION['user_id'];
$sql = "SELECT role FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: login.php");
    exit();
}

$row = $result->fetch_assoc();
$user_role = $row['role'];

// Check if staff
if ($user_role !== 'staff') {
    echo "Access denied: You do not have permission to view this page.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: white;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        /* Navbar Styling */
        .navbar {
            background: rgba(0, 0, 0, 0.85);
            padding: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }
        .navbar-title {
            color: #00d4ff;
            font-weight: bold;
            font-size: 1.5rem;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        .home-btn {
            background: #00d4ff;
            color: black;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        .home-btn:hover {
            background: #00a3cc;
            color: white;
        }
        .logout-btn {
            background: #ff4d4d;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        .logout-btn:hover {
            background: #e60000;
        }

        /* Dashboard Cards */
        .dashboard-card {
            border: none;
            color: white;
            border-radius: 12px;
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .dashboard-card i {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.6);
        }

        /* Gradient colors for cards */
        .gradient-1 { background: linear-gradient(135deg, #ff7e5f, #feb47b); }
        .gradient-2 { background: linear-gradient(135deg, #6a11cb, #2575fc); }
        .gradient-3 { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .gradient-4 { background: linear-gradient(135deg, #fc4a1a, #f7b733); }
        .gradient-5 { background: linear-gradient(135deg, #8e2de2, #4a00e0); }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.7s ease-in-out;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg position-relative">
    <div class="container-fluid">
       

        <!-- Center Title -->
        <span class="navbar-title"><i class="fas fa-user-cog"></i> Staff Dashboard</span>

        <!-- Home Button on Right -->
        <a class="home-btn ms-auto" href="index.php"><i class="fas fa-home"></i></a>
    </div>
</nav>

<!-- Dashboard Content -->
<div class="container py-5 fade-in">
    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : '';
    if ($page) {
        switch ($page) {
            case 'appointment_management': include 'appointment_management.php'; break;
            case 'classes_management': include 'classes_management.php'; break;
            case 'trainers_management': include 'trainers_management.php'; break;
            case 'memberships': include 'membership_management.php'; break;
            case 'user_queries': include 'user_queries.php'; break;
            default: echo "<h3 class='text-center'>Invalid Page</h3>"; break;
        }
    } else {
        echo '
        <div class="row g-4">
            <div class="col-md-4">
                <a href="staff_dashboard.php?page=appointment_management" class="text-decoration-none">
                    <div class="card dashboard-card gradient-1 text-center">
                        <i class="fas fa-calendar-check"></i>
                        <h5>Appointment Management</h5>
                        <p>Manage and schedule appointments.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="staff_dashboard.php?page=classes_management" class="text-decoration-none">
                    <div class="card dashboard-card gradient-2 text-center">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <h5>Classes Management</h5>
                        <p>Organize and monitor classes.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="staff_dashboard.php?page=trainers_management" class="text-decoration-none">
                    <div class="card dashboard-card gradient-3 text-center">
                        <i class="fas fa-dumbbell"></i>
                        <h5>Trainers Management</h5>
                        <p>View and manage trainer profiles.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="staff_dashboard.php?page=memberships" class="text-decoration-none">
                    <div class="card dashboard-card gradient-4 text-center">
                        <i class="fas fa-id-card"></i>
                        <h5>Memberships</h5>
                        <p>Handle membership plans and renewals.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="staff_dashboard.php?page=user_queries" class="text-decoration-none">
                    <div class="card dashboard-card gradient-5 text-center">
                        <i class="fas fa-question-circle"></i>
                        <h5>User Queries</h5>
                        <p>Respond to user inquiries and issues.</p>
                    </div>
                </a>
            </div>
        </div>';
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
