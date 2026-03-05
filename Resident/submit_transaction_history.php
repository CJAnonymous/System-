<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "barangay";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    $_SESSION['error'] = "Internal server error. Please try again later.";
    header("Location: transaction_history.php");
    exit();
}
$id_type = filter_input(INPUT_POST, 'id_type', FILTER_SANITIZE_STRING);
if (empty($id_type)) {
    $_SESSION['error'] = "Please select an ID type.";
    header("Location: transaction_history.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $purpose = trim($_POST['purpose'] ?? '');

    if (empty($purpose)) {
        $_SESSION['error'] = "Purpose field cannot be empty.";
        header("Location: transaction_history.php");
        exit();
    }

    if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] == UPLOAD_ERR_OK) {
        $valid_id = $_FILES['valid_id'];
        $upload_dir = "uploads/presently_valid_id_uploaded/"; // directory name left unchanged for storage


        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);  
        }

        $file_name = basename($valid_id["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png'];
        if (!in_array($file_ext, $allowed_ext)) {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
            header("Location: transaction_history.php");
            exit();
        }

        $max_file_size = 5 * 1024 * 1024;
        if ($valid_id["size"] > $max_file_size) {
            $_SESSION['error'] = "File size exceeds the limit of 5MB.";
            header("Location: transaction_history.php");
            exit();
        }

        $new_file_name = uniqid("VALID_ID_") . "." . $file_ext;
        $valid_id_path = $upload_dir . $new_file_name;

        if (!move_uploaded_file($valid_id["tmp_name"], $valid_id_path)) {
            $_SESSION['error'] = "Error uploading valid ID. Please try again.";
            header("Location: transaction_history.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $user_sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($user_sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            $_SESSION['error'] = "Internal server error. Please try again later.";
            header("Location: transaction_history.php");
            exit();
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        if ($user_result->num_rows === 1) {
            $user = $user_result->fetch_assoc();
        } else {
            $_SESSION['error'] = "User not found. Please login again.";
            header("Location: login.php");
            exit();
        }
        $stmt->close();

        $present_address = "{$user['blk_street']}, {$user['barangay']}, {$user['city']}, {$user['province']}, {$user['region']}";

        // Generate Reference Number
        $date = date('Ymd');
        $count_sql = "SELECT COUNT(*) as count FROM presently_requests WHERE DATE(created_at) = CURDATE()";
        $count_result = $conn->query($count_sql);
        $count = 1;
        if ($count_result && $count_row = $count_result->fetch_assoc()) {
            $count = $count_row['count'] + 1;
        }
        $reference_number = "PRST-" . $date . "-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Insert into Database
        $sql = "INSERT INTO presently_requests 
        (first_name, middle_name, last_name, present_address, purpose, valid_id_path, id_type, user_id, reference_number) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            $_SESSION['error'] = "Internal server error. Please try again later.";
            header("Location: transaction_history.php");
            exit();
        }

        $stmt->bind_param(
            "sssssssis", 
            $user['first_name'], 
            $user['middle_name'],
            $user['last_name'], 
            $present_address, 
            $purpose, 
            $valid_id_path, 
            $id_type, // Add this line
            $user_id,
            $reference_number
        );

        if ($stmt->execute()) {
            $_SESSION['success'] = $reference_number;
            header("Location: transaction_history.php");
        } else {
            error_log("Execute failed: " . $stmt->error);
            $_SESSION['error'] = "Error submitting your request. Please try again.";
            header("Location: transaction_history.php");
        }

        $stmt->close();
        $conn->close();
    } else {
        $_SESSION['error'] = "Please attach a valid ID.";
        header("Location: transaction_history.php");
        exit();
    }
}
?>
