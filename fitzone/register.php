<?php
// Set the content type to JSON for API-like response
header('Content-Type: application/json');

// Turn mysqli warnings into exceptions for better error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Initialize response array
$response = [];

// Database connection details
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "fitzone";

$conn = null; // Initialize connection variable

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset('utf8mb4');

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $errors = [];

        // Read and sanitize inputs
        $name     = trim($_POST['name'] ?? '');
        $address  = trim($_POST['address'] ?? '');
        $ageRaw   = $_POST['age'] ?? '';
        $gender   = trim($_POST['gender'] ?? '');
        $phoneRaw = $_POST['phone'] ?? '';
        $email    = trim($_POST['email'] ?? '');
        $passwordRaw = $_POST['password'] ?? '';
        $package  = trim($_POST['package'] ?? '');
        $status   = 'pending'; // Default status for new registrations

        // --- Server-Side Validation ---
        if (mb_strlen($name) < 3) {
            $errors[] = "Full name must be at least 3 characters.";
        }
        if (mb_strlen($address) < 5) {
            $errors[] = "Address is required (min 5 characters).";
        }
        $age = filter_var($ageRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 13, 'max_range' => 100]]);
        if ($age === false) {
            $errors[] = "Age must be a number between 13 and 100.";
        }
        $allowedGenders = ['Male', 'Female', 'Other'];
        if (!in_array($gender, $allowedGenders, true)) {
            $errors[] = "Please select a valid gender.";
        }
        $phoneDigits = preg_replace('/[^\d]/', '', $phoneRaw);
        if (!preg_match('/^0\d{9}$/', $phoneDigits)) {
            $errors[] = "Phone number must be 10 digits and start with 0.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please provide a valid email address.";
        }
        if (strlen($passwordRaw) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        }
        if (empty($package)) {
            $errors[] = "Please select a membership package.";
        }

        // If initial validations pass, check for duplicate email
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM membership_registrations WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "This email address is already registered.";
            }
            $stmt->close();
        }

        // --- Handle Result ---
        if (!empty($errors)) {
            // If there are errors, add them to the response
            $response['error'] = "Please fix the following issues:\n• " . implode("\n• ", $errors);
        } else {
            // If everything is valid, proceed with insertion
            $hashedPassword = password_hash($passwordRaw, PASSWORD_BCRYPT);
            
            $sql = "INSERT INTO membership_registrations 
                    (full_name, address, age, gender, phone, email, password, package, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssissssss",
                $name,
                $address,
                $age,
                $gender,
                $phoneDigits,
                $email,
                $hashedPassword,
                $package,
                $status
            );

            if ($stmt->execute()) {
                $response['success'] = "🎉 Registration successful!";
            } else {
                // This case is unlikely due to mysqli_report, but good to have
                $response['error'] = "Failed to save registration data.";
            }
            $stmt->close();
        }
    } else {
        $response['error'] = 'Invalid request method.';
    }
} catch (mysqli_sql_exception $e) {
    // Catch any database exceptions (connection, query errors)
    // Log the detailed error for the developer
    error_log("Database Error: " . $e->getMessage());
    // Provide a generic error to the user
    $response['error'] = "A database error occurred. Please try again later.";
} catch (Exception $e) {
    // Catch any other general errors
    error_log("General Error: " . $e->getMessage());
    $response['error'] = "An unexpected error occurred. Please try again later.";
} finally {
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}

// Echo the final response as a JSON string
echo json_encode($response);
?>
