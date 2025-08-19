<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add class
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $schedule = $_POST['schedule'];
    $stmt = $conn->prepare("INSERT INTO classes (name, description, schedule) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $description, $schedule);
    if ($stmt->execute()) {
        echo "<script>alert('Class added successfully'); location.href=location.href;</script>";
    } else {
        echo "<script>alert('Failed to add class');</script>";
    }
    exit();
}

// Delete class
if (isset($_POST['delete'])) {
    $class_id = $_POST['class_id'];
    $stmt = $conn->prepare("DELETE FROM classes WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    if ($stmt->execute()) {
        echo "<script>alert('Class deleted successfully'); location.href=location.href;</script>";
    } else {
        echo "<script>alert('Failed to delete class');</script>";
    }
    exit();
}

// Edit class
if (isset($_POST['edit'])) {
    $class_id = $_POST['class_id'];
    $stmt = $conn->prepare("SELECT * FROM classes WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result_edit = $stmt->get_result();
    $class = $result_edit->fetch_assoc();
}

// Update class
if (isset($_POST['update'])) {
    $class_id = $_POST['class_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $schedule = $_POST['schedule'];
    $stmt = $conn->prepare("UPDATE classes SET name = ?, description = ?, schedule = ? WHERE class_id = ?");
    $stmt->bind_param("sssi", $name, $description, $schedule, $class_id);
    if ($stmt->execute()) {
        echo "<script>alert('Class updated successfully'); location.href=location.href;</script>";
    } else {
        echo "<script>alert('Failed to update class');</script>";
    }
    exit();
}

// Fetch classes
$result = mysqli_query($conn, "SELECT * FROM classes ORDER BY schedule");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Class Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background-color: #f0f8ff; /* Light blue background */
        font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
        background: linear-gradient(90deg, #007bff, #66b2ff);
    }
    .navbar-brand {
        color: white !important;
        font-weight: 600;
        font-size: 1.4rem;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0px 4px 20px rgba(0,0,0,0.05);
        border: none;
    }
    .table thead {
        background-color: #007bff;
        color: white;
    }
    .table-hover tbody tr:hover {
        background-color: #e6f2ff;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0069d9;
        border-color: #0062cc;
    }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container d-flex justify-content-center position-relative">
        <span class="navbar-brand mx-auto">Class Management</span>
    </div>
</nav>

<div class="container mt-4">

    <!-- Add or Update Form -->
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">
            <?= isset($class) ? 'Update Class' : 'Add Class' ?>
        </div>
        <div class="card-body">
            <form method="POST">
                <?php if(isset($class)): ?>
                    <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label">Class Name</label>
                    <input type="text" name="name" class="form-control" value="<?= isset($class) ? htmlspecialchars($class['name']) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" required><?= isset($class) ? htmlspecialchars($class['description']) : '' ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Schedule</label>
                    <input type="datetime-local" name="schedule" class="form-control" value="<?= isset($class) ? date('Y-m-d\TH:i', strtotime($class['schedule'])) : '' ?>" required>
                </div>
                <button type="submit" name="<?= isset($class) ? 'update' : 'add' ?>" class="btn btn-primary"><?= isset($class) ? 'Update' : 'Add' ?></button>
                <?php if(isset($class)): ?>
                    <a href="?" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Classes Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered m-0">
                    <thead>
                        <tr>
                            <th>Class ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Schedule</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $row['class_id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                    <td><?= $row['schedule'] ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="class_id" value="<?= $row['class_id'] ?>">
                                            <button type="submit" name="edit" class="btn btn-sm btn-warning">Edit</button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="class_id" value="<?= $row['class_id'] ?>">
                                            <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No classes found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
