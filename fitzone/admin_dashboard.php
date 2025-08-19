<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitZone Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(90deg, #4a00e0, #8e2de2);
            padding: 0.7rem 1rem;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.2);
        }
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.25rem;
        }
        .navbar .btn {
            border-radius: 50px;
            font-weight: 500;
        }

        /* Card styles */
        .dashboard-card {
            border: none;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            height: 230px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            transition: all 0.4s ease;
        }
        /* Different gradients */
        .gradient-1 { background: linear-gradient(135deg, #ffecd2, #fcb69f); }
        .gradient-2 { background: linear-gradient(135deg, #a1c4fd, #c2e9fb); }
        .gradient-3 { background: linear-gradient(135deg, #fbc2eb, #a6c1ee); }
        .gradient-4 { background: linear-gradient(135deg, #ffd6a5, #fdffb6); }
        .gradient-5 { background: linear-gradient(135deg, #cfd9df, #e2ebf0); }
        .gradient-6 { background: linear-gradient(135deg, #ff9a9e, #fecfef); }
        .gradient-7 { background: linear-gradient(135deg, #d4fc79, #96e6a1); }

        .dashboard-card i {
            font-size: 2.5rem;
            color: #333;
        }
        .dashboard-card h5 {
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        .dashboard-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 0 25px rgba(142, 45, 226, 0.4), 0 0 50px rgba(142, 45, 226, 0.2);
        }

        /* Open button */
        .open-btn {
            background-color: #4a00e0;
            color: #fff;
            border-radius: 30px;
            padding: 8px 18px;
            font-weight: 500;
            border: none;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s ease;
        }
        .open-btn:hover {
            background-color: #8e2de2;
            color: #fff;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <i class="fas fa-dumbbell"></i> FitZone Fitness Center  Admin Dashboard
        </a>
        <div class="d-flex ms-auto">
           
            <a class="btn btn-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</nav>

<!-- Dashboard Cards -->
<div class="container my-5">
    <div class="row g-4">

        <div class="col-md-4">
            <div class="dashboard-card gradient-1" data-href="user_management.php">
                <div>
                    <i class="fas fa-users"></i>
                    <h5>User Management</h5>
                </div>
                <a class="open-btn" href="user_management.php">Open</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card gradient-2" data-href="appointment_management.php">
                <div>
                    <i class="fas fa-calendar-check"></i>
                    <h5>Appointment Management</h5>
                </div>
                <a class="open-btn" href="appointment_management.php">Open</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card gradient-3" data-href="classes_management.php">
                <div>
                    <i class="fas fa-dumbbell"></i>
                    <h5>Classes Management</h5>
                </div>
                <a class="open-btn" href="classes_management.php">Open</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card gradient-4" data-href="trainers_management.php">
                <div>
                    <i class="fas fa-user-tie"></i>
                    <h5>Trainers Management</h5>
                </div>
                <a class="open-btn" href="trainers_management.php">Open</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card gradient-5" data-href="memberships.php">
                <div>
                    <i class="fas fa-id-card"></i>
                    <h5>Memberships</h5>
                </div>
                <a class="open-btn" href="membership_management.php">Open</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card gradient-6" data-href="membership_packages.php">
                <div>
                    <i class="fas fa-box"></i>
                    <h5>Membership Packages</h5>
                </div>
                <a class="open-btn" href="membership_packages.php">Open</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card gradient-7" data-href="user_queries.php">
                <div>
                    <i class="fas fa-envelope"></i>
                    <h5>User Queries</h5>
                </div>
                <a class="open-btn" href="user_queries.php">Open</a>
            </div>
        </div>

    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Make entire card clickable except the Open button
    document.querySelectorAll('.dashboard-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.classList.contains('open-btn')) {
                window.location.href = this.getAttribute('data-href');
            }
        });
    });
</script>
</body>
</html>
