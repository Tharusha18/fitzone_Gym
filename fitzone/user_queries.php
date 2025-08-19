<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitzone");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine the status filter
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : 'all';

// Base query
$query = "SELECT query.id, query.user_id, query.subject, query.message, query.status, query.created_at, users.name AS user_name 
          FROM query 
          JOIN users ON query.user_id = users.user_id";

if ($status_filter !== 'all') {
    $query .= " WHERE query.status = ?";
}

$stmt = $conn->prepare($query);

if ($status_filter !== 'all') {
    $stmt->bind_param("s", $status_filter);
}

$stmt->execute();
$result = $stmt->get_result();

// Update status
if (isset($_POST['update_status'])) {
    $query_id = $_POST['query_id'];
    $new_status = $_POST['new_status'];

    $update_query = "UPDATE query SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $new_status, $query_id);
    $update_stmt->execute();

    header("Location: user_queries.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Queries Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #a18cd1, #fbc2eb);
        font-family: 'Segoe UI', sans-serif;
        color: #333;
    }
    .navbar {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
    }
    .navbar-brand {
        font-weight: bold;
        font-size: 1.4rem;
        color: white !important;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }
      .custom-navbar {
    background-color: #343a40; /* Dark background */
    padding-top: 20px;  /* Increase top padding */
    padding-bottom: 20px; /* Increase bottom padding */
    min-height: 80px; /* Set a minimum height */
  }

  .custom-navbar .navbar-brand {
    color: white;
    font-size: 1.5rem; /* Bigger font for better proportion */
  }

  .custom-navbar .navbar-brand:hover {
    color: #ddd;
  }
    .home-btn {
        background: white;
        border-radius: 50%;
        padding: 8px 12px;
        color: #2575fc;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }
    .home-btn:hover {
        background: #f0f0f0;
        transform: scale(1.1);
        color: #6a11cb;
    }
    .container {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        margin-top: 30px;
    }
    .card-header {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: white;
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        border-radius: 12px 12px 0 0;
    }
    .form-label {
        font-weight: 600;
        color: #555;
    }
    .form-select {
        border-radius: 10px;
        padding: 8px;
        border: 2px solid transparent;
        background: #fff;
        transition: all 0.3s ease;
    }
    .form-select:focus {
        border-color: #6a11cb;
        box-shadow: 0 0 8px rgba(106, 17, 203, 0.4);
    }
    table {
        border-radius: 12px;
        overflow: hidden;
    }
    thead {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: white;
    }
    tbody tr:nth-child(even) {
        background-color: #f9f6ff;
    }
    tbody tr:hover {
        background-color: #f0eaff;
        transition: background-color 0.3s ease;
    }
    .btn-gradient {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        transition: all 0.3s ease;
    }
    .btn-gradient:hover {
        background: linear-gradient(135deg, #2575fc, #6a11cb);
        transform: translateY(-2px);
    }
</style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg position-relative custom-navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Query Management</a>
    <div class="ms-auto">
        <!-- Optional right-side content -->
    </div>
  </div>
</nav>

<div class="container">
    <div class="mb-3">
        <label for="statusFilter" class="form-label">Filter by Status:</label>
        <select id="statusFilter" class="form-select" onchange="filterStatus(this.value)">
            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All</option>
            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="read" <?= $status_filter === 'read' ? 'selected' : '' ?>>Read</option>
            <option value="closed" <?= $status_filter === 'closed' ? 'selected' : '' ?>>Closed</option>
        </select>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Query ID</th>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Change Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['user_id']) ?></td>
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['status']) ?></span></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td>
                                <form action="user_queries.php" method="POST">
                                    <input type="hidden" name="query_id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <select name="new_status" class="form-select form-select-sm mb-2" required>
                                        <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="read" <?= $row['status'] === 'read' ? 'selected' : '' ?>>Read</option>
                                        <option value="closed" <?= $row['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-gradient btn-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No queries found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filterStatus(status) {
    window.location.href = "user_queries.php?status=" + status;
}
</script>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
