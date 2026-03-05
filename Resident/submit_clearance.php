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
    die(json_encode([
        'success' => false, 
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $response = ['success' => false, 'message' => ''];

    try {
        // Validate required fields
        if (empty($_POST['purpose'])) {
            throw new Exception('Purpose field is required');
        }

        // Validate file upload
        if (!isset($_FILES['valid_id']) || $_FILES['valid_id']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Valid ID file is required');
        }

        // Process input data
        $purpose = trim($_POST['purpose']);
        $valid_id = $_FILES['valid_id'];

        // File upload handling
        $upload_dir = "uploads/brgy_clearance_uploads/";
        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
        $id_type = filter_input(INPUT_POST, 'id_type', FILTER_SANITIZE_STRING);
        if (empty($id_type)) {
            throw new Exception('Please select an ID type.');
        }
        
        // Process Valid ID
        $valid_id_ext = strtolower(pathinfo($valid_id['name'], PATHINFO_EXTENSION));
        $valid_id_name = $upload_dir . uniqid("ID_", true) . ".$valid_id_ext";
        if (!move_uploaded_file($valid_id['tmp_name'], $valid_id_name)) {
            throw new Exception('Failed to upload valid ID');
        }

        // Get user data
        $user_id = $_SESSION['user_id'];
        $stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $user_result = $stmt_user->get_result();
        
        if ($user_result->num_rows === 0) {
            throw new Exception('User not found');
        }
        $user = $user_result->fetch_assoc();
        $stmt_user->close();

        // Prepare user data
        $address = implode(', ', [
            $user['blk_street'],
            $user['barangay'],
            $user['city'],
            $user['province'],
            $user['region']
        ]);

        // Generate reference number
        $date = date('Ymd');
        $stmt_count = $conn->prepare("SELECT COUNT(*) AS count FROM barangay_clearances WHERE DATE(created_at) = CURDATE()");
        $stmt_count->execute();
        $count_result = $stmt_count->get_result()->fetch_assoc();
        $count = $count_result['count'] + 1;
        $reference_number = "CLR-" . $date . "-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Prepare SQL statement
        $sql = "INSERT INTO barangay_clearances 
        (last_name, first_name, middle_name, address, birthdate, sex, 
         civil_status, purpose, contact_number, valid_id_path, id_type, 
         reference_number, user_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param(
            "sssssssssssss", 
            $user['last_name'],
            $user['first_name'],
            $user['middle_name'],
            $address,
            $user['birthdate'],
            $user['gender'],
            $user['status'],
            $purpose,
            $user['contact_number'],
            $valid_id_name,
            $id_type, // Add this line
            $reference_number,
            $user_id
        );

        // Execute and handle results
        if (!$stmt->execute()) {
            throw new Exception('Database error: ' . $stmt->error);
        }

        // Success response
        $response = [
            'success' => true,
            'refNumber' => $reference_number
        ];

        // Handle response format
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            $_SESSION['success'] = $reference_number;
            header("Location: barangay-clearance.php");
        }
        exit();

    } catch (Exception $e) {
        // Error handling
        $response['message'] = $e->getMessage();
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            $_SESSION['error'] = $e->getMessage();
            header("Location: barangay-clearance.php");
        }
        exit();
    }
}

$conn->close();
?>