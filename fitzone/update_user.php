<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

// Validate input
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = trim($_POST['role'] ?? '');

if ($user_id <= 0 || empty($name) || empty($email) || empty($role)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format."]);
    exit;
}

$valid_roles = ["Admin", "Staff", "Customer"];
if (!in_array($role, $valid_roles)) {
    echo json_encode(["status" => "error", "message" => "Invalid role selected."]);
    exit;
}

// Prepare SQL to prevent SQL injection
$stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE user_id = ?");
$stmt->bind_param("sssi", $name, $email, $role, $user_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "User updated successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
