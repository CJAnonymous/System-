<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "barangay";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = $_POST['first_name'] ?? null;
    $middle_name = $_POST['middle_name'] ?? null;
    $last_name = $_POST['last_name'] ?? null;
    $blk_street = $_POST['blk_street'] ?? null;
    $region = $_POST['region'] ?? "National Capital Region";
    $city = $_POST['city'] ?? "Quezon City";
    $barangay = $_POST['barangay'] ?? "Poblacion 1";
    $birthdate = $_POST['birthdate'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $contact_number = $_POST['contact_number'] ?? null;
    $email = $_POST['email'] ?? null;
    $username_input = $_POST['username'] ?? null;
    $password_input = $_POST['password'] ?? null;
    $repeat_password = $_POST['repeat_password'] ?? null;

    // Check if passwords match
    if ($password_input !== $repeat_password) {
        echo "<script>
                alert('Passwords do not match. Please try again.');
                window.history.back();
              </script>";
        exit;
    }

    // Check if email or username already exists
    $check_sql = "SELECT id FROM users WHERE email = ? OR username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $email, $username_input);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>
                alert('Email or username already exists.');
                window.history.back();
              </script>";
        exit;
    }
    $check_stmt->close();

    // Hash password
    $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);

    // Insert user with pending status (requires admin approval)
    $sql = "INSERT INTO users (
                first_name, middle_name, last_name, blk_street, region, city, barangay,
                birthdate, gender, contact_number, email, username, password,
                verified, verification_code, account_status, role
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, '', 'pending', 'Resident')";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssssssssss",
        $first_name,
        $middle_name,
        $last_name,
        $blk_street,
        $region,
        $city,
        $barangay,
        $birthdate,
        $gender,
        $contact_number,
        $email,
        $username_input,
        $hashed_password
    );

    if ($stmt->execute()) {
        echo "<script>
                alert('Registration successful! Your account is pending approval by an administrator.');
                window.location.href='login.php';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

?>
