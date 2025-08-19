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

// Status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$query = "SELECT * FROM appointments";
if ($status_filter !== 'all') {
    $query .= " WHERE status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}
$query .= " ORDER BY appointment_date, created_at";
$result = mysqli_query($conn, $query);

// Delete appointment
if (isset($_POST['delete'])) {
    $appointment_id = $_POST['appointment_id'];
    $stmt = $conn->prepare("DELETE FROM appointments WHERE appointment_id = ?");
    $stmt->bind_param("i", $appointment_id);
    if ($stmt->execute()) {
        echo "<script>alert('Appointment deleted successfully'); location.reload();</script>";
    } else {
        echo "<script>alert('Failed to delete appointment');</script>";
    }
    exit();
}

// Edit appointment
if (isset($_POST['edit'])) {
    $appointment_id = $_POST['appointment_id'];
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE appointment_id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result_edit = $stmt->get_result();
    $appointment = $result_edit->fetch_assoc();
}

// Update appointment
if (isset($_POST['update'])) {
    $appointment_id = $_POST['appointment_id'];
    $appointment_date = $_POST['appointment_date'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE appointments SET appointment_date = ?, status = ? WHERE appointment_id = ?");
    $stmt->bind_param("ssi", $appointment_date, $status, $appointment_id);
    if ($stmt->execute()) {
        echo "<script>alert('Appointment updated successfully'); location.href=location.href;</script>";
    } else {
        echo "<script>alert('Failed to update appointment');</script>";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Appointment Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background-color: #f8f0e3; /* Light brown background */
        font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
        background: linear-gradient(90deg, #b68d40, #d4a373);
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
        background-color: #b68d40;
        color: white;
    }
    .table-hover tbody tr:hover {
        background-color: #f3e9dc;
    }
    .btn-primary {
        background-color: #b68d40;
        border-color: #b68d40;
    }
    .btn-primary:hover {
        background-color: #a67c33;
        border-color: #a67c33;
    }
</style>
<script>
function filterByStatus(status) {
    const urlParams = new URLSearchParams(window.location.search);
    if (status === 'all') {
        urlParams.delete('status');
    } else {
        urlParams.set('status', status);
    }
    window.location.search = urlParams.toString();
}
</script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container d-flex justify-content-center position-relative">
        <span class="navbar-brand mx-auto">Appointment Management</span>

    </div>
</nav>

<div class="container mt-4">

    <!-- Filter -->
    <div class="mb-4">
        <label for="status" class="me-2 fw-bold">Filter by Status:</label>
        <select id="status" class="form-select w-auto d-inline" onchange="filterByStatus(this.value)">
            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All</option>
            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $status_filter === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="canceled" <?= $status_filter === 'canceled' ? 'selected' : '' ?>>Canceled</option>
        </select>
    </div>

    <!-- Update Form -->
    <?php if (isset($appointment)): ?>
        <div class="card mb-4">
            <div class="card-header bg-light fw-bold">Update Appointment</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Appointment Date</label>
                        <input type="datetime-local" name="appointment_date" class="form-control"
                               value="<?= date('Y-m-d\TH:i', strtotime($appointment['appointment_date'])) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" <?= $appointment['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $appointment['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="completed" <?= $appointment['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="canceled" <?= $appointment['status'] === 'canceled' ? 'selected' : '' ?>>Canceled</option>
                        </select>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered m-0">
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>User ID</th>
                            <th>Trainer ID</th>
                            <th>Class ID</th>
                            <th>Appointment Date</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $row['appointment_id'] ?></td>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= $row['trainer_id'] ?? 'N/A' ?></td>
                                    <td><?= $row['class_id'] ?? 'N/A' ?></td>
                                    <td><?= $row['appointment_date'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $row['status'] === 'pending' ? 'warning' : ($row['status'] === 'confirmed' ? 'info' : ($row['status'] === 'completed' ? 'success' : 'danger')) ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                                            <button type="submit" name="edit" class="btn btn-sm btn-warning">Edit</button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                                            <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center">No appointments found</td></tr>
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
