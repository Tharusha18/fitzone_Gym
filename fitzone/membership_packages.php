<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitzone");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert or update logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['add_membership'])) {
        if (!empty($_POST['edit_id'])) {
            // Update existing membership
            $edit_id = intval($_POST['edit_id']);
            $name = $conn->real_escape_string($_POST['name']);
            $price = $conn->real_escape_string($_POST['price']);
            $duration = $conn->real_escape_string($_POST['duration']);
            $benefits = $conn->real_escape_string($_POST['benefits']);
            $promotions = $conn->real_escape_string($_POST['promotions']);

            $query = "UPDATE memberships 
                      SET name = '$name', price = '$price', duration = '$duration', 
                          benefits = '$benefits', special_promotions = '$promotions' 
                      WHERE membership_id = $edit_id";

            if ($conn->query($query)) {
                echo "<script>alert('Membership updated successfully!');</script>";
            } else {
                echo "<script>alert('Error updating: " . $conn->error . "');</script>";
            }
        } else {
            // Add new membership
            $name = $conn->real_escape_string($_POST['name']);
            $price = $conn->real_escape_string($_POST['price']);
            $duration = $conn->real_escape_string($_POST['duration']);
            $benefits = $conn->real_escape_string($_POST['benefits']);
            $promotions = $conn->real_escape_string($_POST['promotions']);

            $query = "INSERT INTO memberships (name, price, duration, benefits, special_promotions, created_at) 
                      VALUES ('$name', '$price', '$duration', '$benefits', '$promotions', NOW())";

            if ($conn->query($query)) {
                echo "<script>alert('Membership added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding: " . $conn->error . "');</script>";
            }
        }
    } elseif (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        $query = "DELETE FROM memberships WHERE membership_id = $delete_id";

        if ($conn->query($query)) {
            echo "<script>alert('Membership deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting: " . $conn->error . "');</script>";
        }
    }
}

// Fetch membership packages
$query = "SELECT * FROM memberships";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Membership Packages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #b2b2b2; /* ash grey */
        }
        .navbar {
            background-color: #343a40 !important;
        }
        .navbar .navbar-brand, .navbar .nav-link, .navbar .fa-home {
            color: white !important;
        }
        .card {
            border-radius: 10px;
            background-color: #f1f1f1;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        table th {
            background-color: #6c757d;
            color: white;
        }
        table tbody tr:hover {
            background-color: #e2e3e5;
        }
        .btn-custom {
            background-color: #6c757d;
            color: white;
        }
        .btn-custom:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <span class="navbar-brand mx-auto fw-bold">Manage Membership Packages</span>
        </a>
    </div>
</nav>

<div class="container mt-4">
    <div class="card p-4">
        <!-- Add/Update Form -->
        <form method="POST" id="membershipForm" class="row g-3">
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="col-md-6">
                <input type="text" name="name" id="name" class="form-control" placeholder="Name" required>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="price" id="price" class="form-control" placeholder="Price (Rs)" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="duration" id="duration" class="form-control" placeholder="Duration (Months)" required>
            </div>
            <div class="col-md-6">
                <textarea name="benefits" id="benefits" class="form-control" placeholder="Benefits" required></textarea>
            </div>
            <div class="col-md-6">
                <textarea name="promotions" id="promotions" class="form-control" placeholder="Special Promotions" required></textarea>
            </div>
            <div class="col-12">
                <button type="submit" name="add_membership" id="formButton" class="btn btn-custom w-100">
                    + Add Membership Package
                </button>
            </div>
        </form>
    </div>

    <!-- Membership Table -->
    <div class="card mt-4 p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Benefits</th>
                        <th>Promotions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $counter = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$counter}</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['duration']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['benefits']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['special_promotions']) . "</td>";
                            echo "<td>
                                <button type='button' class='btn btn-sm btn-primary' onclick='editMembership(" . json_encode($row) . ")'>
                                    <i class='fas fa-edit'></i>
                                </button>
                                <button type='button' class='btn btn-sm btn-danger' onclick='confirmDelete(" . $row['membership_id'] . ")'>
                                    <i class='fas fa-trash'></i>
                                </button>
                            </td>";
                            echo "</tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No membership packages found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form method="POST" id="deleteForm" style="display:none;">
    <input type="hidden" name="delete_id" id="delete_id">
</form>

<script>
    function editMembership(data) {
        document.getElementById('edit_id').value = data.membership_id;
        document.getElementById('name').value = data.name;
        document.getElementById('price').value = data.price;
        document.getElementById('duration').value = data.duration;
        document.getElementById('benefits').value = data.benefits;
        document.getElementById('promotions').value = data.special_promotions;
        document.getElementById('formButton').textContent = "Update Membership Package";
    }

    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this package?")) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteForm').submit();
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
