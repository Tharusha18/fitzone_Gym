<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitzone");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle trainer update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_trainer'])) {
    // Debug: Show received POST data
    // echo "<pre>"; print_r($_POST); print_r($_FILES); echo "</pre>";

    $trainer_id = intval($_POST["trainer_id"]);
    $name = trim($_POST["name"]);
    $proficiency = trim($_POST["proficiency"]);
    $experience_years = intval($_POST["experience_years"]);
    $email = trim($_POST["email"]);
    $image_path = "";

    // Get current image from DB in case no new image is uploaded
    $current_image = "";
    $result = $conn->query("SELECT image FROM trainers WHERE trainer_id = $trainer_id");
    if ($result && $row = $result->fetch_assoc()) {
        $current_image = $row["image"];
    }

    // Handle file upload if new image provided
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/trainers/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $image_path = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            die("Error uploading image. Please check folder permissions.");
        }
    } else {
        // No new image uploaded → use current image
        $image_path = $current_image;
    }

    // Prepare and execute update query
    $stmt = $conn->prepare("UPDATE trainers SET name=?, proficiency=?, experience_years=?, email=?, image=? WHERE trainer_id=?");
    $stmt->bind_param("ssissi", $name, $proficiency, $experience_years, $email, $image_path, $trainer_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Trainer updated successfully!'); window.location.href='trainers_management.php';</script>";
        } else {
            echo "<script>alert('No changes were made.'); window.location.href='trainers_management.php';</script>";
        }
    } else {
        echo "Error updating trainer: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
