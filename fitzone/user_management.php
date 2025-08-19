<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $role = $_POST['role'];

    if (strlen($name) < 3) {
        echo "<script>alert('Name must be at least 3 characters');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format');</script>";
    } elseif (strlen($pass) < 6) {
        echo "<script>alert('Password must be at least 6 characters');</script>";
    } else {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed, $role);
        echo $stmt->execute() 
            ? "<script>alert('User added successfully!'); window.location.href='user_management.php';</script>"
            : "<script>alert('Error adding user');</script>";
        $stmt->close();
    }
}

// Handle Edit User (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $id = intval($_POST['user_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (strlen($name) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Validation failed";
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE user_id=?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);

    echo $stmt->execute() ? "User updated successfully!" : "Error updating user";
    $stmt->close();
    exit;
}

// Handle Delete User (AJAX)
if (isset($_POST['delete_user'])) {
    $id = intval($_POST['delete_user']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $id);
    echo $stmt->execute() ? "User deleted successfully!" : "Error deleting user";
    $stmt->close();
    exit;
}

// Fetch Users
$result = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Management</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
    }
    .navbar-brand {
        font-weight: bold;
        color: white !important;
        font-size: 1.5rem;
    }
    .home-btn {
        background: white;
        color: #0d6efd;
        border-radius: 5px;
        padding: 5px 10px;
        font-weight: bold;
        text-decoration: none;
    }
    .home-btn:hover {
        background: #e9ecef;
    }
    .card {
        border: none;
        border-radius: 10px;
        background: white;
        box-shadow: 0 6px 18px rgba(0,0,0,0.05);
    }
    .table th {
        background: #0d6efd;
        color: white;
    }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-3">
    <span class="navbar-brand mx-auto">User Management</span>
</nav>

<div class="container py-4">
    <!-- Add User -->
    <div class="card p-4 mb-4">
        <h4 class="mb-3">Add New User</h4>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-3"><input type="text" class="form-control" name="name" placeholder="Full Name" required></div>
                <div class="col-md-3"><input type="email" class="form-control" name="email" placeholder="Email" required></div>
                <div class="col-md-3"><input type="password" class="form-control" name="password" placeholder="Password" required></div>
                <div class="col-md-2">
                    <select class="form-select" name="role" required>
                        <option>Admin</option>
                        <option>Staff</option>
                        <option>Customer</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid"><button type="submit" name="add_user" class="btn btn-primary">Add</button></div>
            </div>
        </form>
    </div>

    <!-- User Table -->
    <div class="card p-4">
        <h4 class="mb-3">Existing Users</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): while ($row = $result->fetch_assoc()): ?>
                        <tr id="row-<?= $row['user_id']; ?>">
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= htmlspecialchars($row['role']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="openEditModal(<?= $row['user_id']; ?>, '<?= htmlspecialchars($row['name']); ?>', '<?= htmlspecialchars($row['email']); ?>', '<?= $row['role']; ?>')">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $row['user_id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="4" class="text-center">No users found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" id="edit_role" class="form-select" required>
                            <option>Admin</option>
                            <option>Staff</option>
                            <option>Customer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="edit_user" class="btn btn-primary w-100">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
let editModal = new bootstrap.Modal(document.getElementById('editModal'));

function openEditModal(id, name, email, role) {
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;
    editModal.show();
}

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    formData.append('edit_user', '1');
    fetch('user_management.php', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(msg => {
        alert(msg);
        if (msg.includes("successfully")) location.reload();
    });
});

function deleteUser(id) {
    if (confirm("Are you sure?")) {
        let fd = new FormData();
        fd.append('delete_user', id);
        fetch('user_management.php', { method: 'POST', body: fd })
        .then(res => res.text())
        .then(msg => {
            alert(msg);
            if (msg.includes("successfully")) document.getElementById(`row-${id}`).remove();
        });
    }
}
</script>
</body>
</html>
<?php $conn->close(); ?>
