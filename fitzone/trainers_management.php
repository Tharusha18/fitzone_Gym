<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitzone");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Trainer
if (isset($_POST['add_trainer'])) {
    $name = $_POST['name'];
    $proficiency = $_POST['proficiency'];
    $experience_years = $_POST['experience_years'];
    $email = $_POST['email'];

    $image_path = "";
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = "uploads/trainers/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $image_path = $upload_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    $stmt = $conn->prepare("INSERT INTO trainers (name, proficiency, experience_years, email, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $name, $proficiency, $experience_years, $email, $image_path);
    $stmt->execute();
    $stmt->close();
    header("Location: trainers_management.php");
    exit();
}

// Handle Edit Trainer
if (isset($_POST['update_trainer'])) {
    $trainer_id = $_POST['trainer_id'];
    $name = $_POST['name'];
    $proficiency = $_POST['proficiency'];
    $experience_years = $_POST['experience_years'];
    $email = $_POST['email'];

    $image_path = "";
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = "uploads/trainers/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $image_path = $upload_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    } else {
        $res = $conn->query("SELECT image FROM trainers WHERE trainer_id = $trainer_id");
        $row = $res->fetch_assoc();
        $image_path = $row['image'];
    }

    $stmt = $conn->prepare("UPDATE trainers SET name=?, proficiency=?, experience_years=?, email=?, image=? WHERE trainer_id=?");
    $stmt->bind_param("ssissi", $name, $proficiency, $experience_years, $email, $image_path, $trainer_id);
    $stmt->execute();
    $stmt->close();
    header("Location: trainers_management.php");
    exit();
}

// Handle Delete Trainer
if (isset($_GET['delete'])) {
    $trainer_id = intval($_GET['delete']);
    $conn->query("DELETE FROM trainers WHERE trainer_id = $trainer_id");
    header("Location: trainers_management.php");
    exit();
}

// Fetch trainers
$result = $conn->query("SELECT * FROM trainers");
$trainers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $trainers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trainers Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg" style="background-color: #FFD700;">
    <div class="container-fluid">
        
        <span class="navbar-brand mx-auto text-dark fw-bold">Trainers Management</span>
    </div>
</nav>

<div class="container mt-4">

    <!-- Add Trainer Button -->
    <div class="text-end mb-3">
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addModal">Add Trainer</button>
    </div>

    <!-- Trainers Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Proficiency</th>
                        <th>Experience</th>
                        <th>Email</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($trainers)): ?>
                        <?php foreach ($trainers as $trainer): ?>
                            <tr>
                                <td><?= $trainer['trainer_id'] ?></td>
                                <td><?= htmlspecialchars($trainer['name']) ?></td>
                                <td><?= htmlspecialchars($trainer['proficiency']) ?></td>
                                <td><?= $trainer['experience_years'] ?></td>
                                <td><?= htmlspecialchars($trainer['email']) ?></td>
                                <td>
                                    <?php if (!empty($trainer['image']) && file_exists($trainer['image'])): ?>
                                        <img src="<?= $trainer['image'] ?>" width="60" height="60" style="object-fit:cover;">
                                    <?php else: ?>
                                        <span class="text-muted">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $trainer['trainer_id'] ?>">Edit</button>
                                    <a href="?delete=<?= $trainer['trainer_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this trainer?')">Delete</a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $trainer['trainer_id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #FFD700;">
                                            <h5 class="modal-title">Edit Trainer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="trainer_id" value="<?= $trainer['trainer_id'] ?>">
                                                <div class="mb-3">
                                                    <label>Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($trainer['name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Proficiency</label>
                                                    <input type="text" name="proficiency" class="form-control" value="<?= htmlspecialchars($trainer['proficiency']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Experience Years</label>
                                                    <input type="number" name="experience_years" class="form-control" value="<?= $trainer['experience_years'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Email</label>
                                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($trainer['email']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Image</label>
                                                    <input type="file" name="image" class="form-control">
                                                    <?php if (!empty($trainer['image'])): ?>
                                                        <small class="text-muted">Current: <?= basename($trainer['image']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit" name="update_trainer" class="btn btn-warning">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-muted">No trainers found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #FFD700;">
                <h5 class="modal-title">Add Trainer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Proficiency</label>
                        <input type="text" name="proficiency" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Experience Years</label>
                        <input type="number" name="experience_years" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="text-end">
                        <button type="submit" name="add_trainer" class="btn btn-warning">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
