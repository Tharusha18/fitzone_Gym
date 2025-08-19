<?php
// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "fitzone"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message and edit variables
$message = "";
$edit_data = null;

// Handle delete request via POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM membership_registrations WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Record deleted successfully.";
    } else {
        $message = "Failed to delete the record.";
    }
}

// Handle edit request via POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['load_edit'])) {
    $edit_id = intval($_POST['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM membership_registrations WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
}

// Handle add/update requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['edit_member'])) {
        $edit_id = intval($_POST['edit_id']);
        $full_name = trim($_POST['full_name']);
        $address = trim($_POST['address']);
        $age = intval($_POST['age']);
        $gender = trim($_POST['gender']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $package = trim($_POST['package']);
        $status = trim($_POST['status']);

        $stmt = $conn->prepare("UPDATE membership_registrations SET full_name = ?, address = ?, age = ?, gender = ?, phone = ?, email = ?, package = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssisssssi", $full_name, $address, $age, $gender, $phone, $email, $package, $status, $edit_id);
        if ($stmt->execute()) {
            $message = "Record updated successfully.";
        } else {
            $message = "Failed to update the record.";
        }
    } elseif (isset($_POST['add_member'])) {
        $full_name = trim($_POST['full_name']);
        $address = trim($_POST['address']);
        $age = intval($_POST['age']);
        $gender = trim($_POST['gender']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $package = trim($_POST['package']);
        $status = trim($_POST['status']);

        $stmt = $conn->prepare("INSERT INTO membership_registrations (full_name, address, age, gender, phone, email, package, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssss", $full_name, $address, $age, $gender, $phone, $email, $package, $status);
        if ($stmt->execute()) {
            $message = "Member added successfully.";
        } else {
            $message = "Failed to add member.";
        }
    }
}

// Fetch membership records
$sql = "SELECT * FROM membership_registrations";
$result = $conn->query($sql);

// Fetch packages for the dropdown
$packages_sql = "SELECT name FROM memberships";
$packages_result = $conn->query($packages_sql);
$packages = [];
if ($packages_result->num_rows > 0) {
    while ($package_row = $packages_result->fetch_assoc()) {
        $packages[] = $package_row['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Membership Management</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<style>
    body {
        background-color: #f4f9f4;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
        margin-top: 20px;
        background: #ffffff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0px 6px 20px rgba(0,0,0,0.1);
    }
    h1 {
        color: #2e7d32;
        font-weight: bold;
        margin-bottom: 25px;
    }
    .table th {
        background-color: #2e7d32 !important;
        color: white;
    }
    .btn-success {
        background-color: #2e7d32;
        border: none;
    }
    .btn-success:hover {
        background-color: #256427;
    }
    .message {
        padding: 12px;
        border-radius: 6px;
        font-weight: 500;
    }
    .message.success {
        background-color: #2e7d32;
        color: white;
    }
    .message.error {
        background-color: #c62828;
        color: white;
    }
    .navbar-brand {
        flex: 1;
        text-align: center;
    }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container-fluid d-flex align-items-center">
    <div style="width:40px;"></div> <!-- spacer to keep center alignment -->
    <a class="navbar-brand m-auto" href="#">Membership Management</a>

    </a>
  </div>
</nav>

<div class="container mt-3">

    <?php if ($message): ?>
        <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?> mb-3">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Add/Edit Form -->
    <form method="POST" class="row g-3 mb-4">
        <h4 class="text-success"><?= $edit_data ? 'Edit Member' : 'Add Member' ?></h4>
        <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? '' ?>">

        <div class="col-md-6">
            <input type="text" name="full_name" class="form-control" placeholder="Full Name" value="<?= htmlspecialchars($edit_data['full_name'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
            <input type="text" name="address" class="form-control" placeholder="Address" value="<?= htmlspecialchars($edit_data['address'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="age" class="form-control" placeholder="Age" value="<?= htmlspecialchars($edit_data['age'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
            <select name="gender" class="form-select" required>
                <option value="male" <?= (isset($edit_data['gender']) && $edit_data['gender'] === 'male') ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= (isset($edit_data['gender']) && $edit_data['gender'] === 'female') ? 'selected' : '' ?>>Female</option>
                <option value="other" <?= (isset($edit_data['gender']) && $edit_data['gender'] === 'other') ? 'selected' : '' ?>>Other</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="phone" class="form-control" placeholder="Phone" value="<?= htmlspecialchars($edit_data['phone'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
            <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($edit_data['email'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
            <select name="package" class="form-select" required>
                <option value="">Select Package</option>
                <?php foreach ($packages as $package): ?>
                    <option value="<?= htmlspecialchars($package) ?>" <?= (isset($edit_data['package']) && $edit_data['package'] === $package) ? 'selected' : '' ?>><?= htmlspecialchars($package) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select" required>
                <option value="pending" <?= (isset($edit_data['status']) && $edit_data['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="accept" <?= (isset($edit_data['status']) && $edit_data['status'] === 'accept') ? 'selected' : '' ?>>Accept</option>
                <option value="cancel" <?= (isset($edit_data['status']) && $edit_data['status'] === 'cancel') ? 'selected' : '' ?>>Cancel</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" name="<?= $edit_data ? 'edit_member' : 'add_member' ?>" class="btn btn-success">
                <?= $edit_data ? 'Update Member' : 'Add Member' ?>
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Address</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Package</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td><?= htmlspecialchars($row['age']) ?></td>
                            <td><?= htmlspecialchars($row['gender']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['package']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
                                    <button class="btn btn-sm btn-success" type="submit" name="load_edit"><i class="fas fa-edit"></i></button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <button class="btn btn-sm btn-danger" type="submit"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="10" class="text-center">No memberships found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
