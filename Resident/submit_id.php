<?php
// Always return JSON and suppress accidental output
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception('Invalid request method.');
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "barangay";

    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed.');
    }

    // Sanitize input data
    $emergency_name = filter_input(INPUT_POST, 'emergency_name', FILTER_SANITIZE_STRING);
    $emergency_address = filter_input(INPUT_POST, 'emergency_address', FILTER_SANITIZE_STRING);
    $emergency_contact = filter_input(INPUT_POST, 'emergency_contact', FILTER_SANITIZE_STRING);
    $relationship = filter_input(INPUT_POST, 'relationship', FILTER_SANITIZE_STRING);
    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);
    // id_type is collected in the form but not yet stored in the database
    $id_type = filter_input(INPUT_POST, 'id_type', FILTER_SANITIZE_STRING);

    if (empty($payment_method)) {
        throw new Exception('Payment method is required.');
    }

    // we don't validate id_type because the table doesn't have a column yet

    // Only cash payments are supported now
    if ($payment_method === 'cash') {
        $payment_success = true;
    } else {
        throw new Exception('Invalid payment method. Only cash is accepted.');
    }

    if ($payment_success) {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            throw new Exception('User not logged in.');
        }
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {
            throw new Exception('User not found.');
        }

        $user = $result->fetch_assoc();
        $last_name = $user['last_name'];
        $first_name = $user['first_name'];
        $middle_name = $user['middle_name'] ?? '';
        $blk_street = $user['blk_street'] ?? '';
        $barangay = $user['barangay'] ?? '';
        $city = $user['city'] ?? '';
        $province = $user['province'] ?? '';
        $region = $user['region'] ?? '';
        $address = "$blk_street, $barangay, $city, $province, $region";
        $stmt->close();

        // Process valid ID upload
        $upload_dir = "uploads/brgy_uploaded_valid_id/";
        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
            throw new Exception('Failed to create upload directory.');
        }

        if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['valid_id']['tmp_name'];
            $file_ext = strtolower(pathinfo($_FILES['valid_id']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png'];

            if (!in_array($file_ext, $allowed_ext)) {
                throw new Exception('Invalid file type. Only JPG, JPEG, and PNG are allowed.');
            }

            $new_file_name = uniqid("ID_") . ".$file_ext";
            $target_file = $upload_dir . $new_file_name;

            if (!move_uploaded_file($file_tmp, $target_file)) {
                throw new Exception('Failed to upload file.');
            }

            $valid_id_path = $target_file;
        } else {
            throw new Exception('Please attach a valid ID.');
        }

        // Generate reference number and insert into database
        $date = date('Ymd');
        $reference_number = "BRG-" . $date . "-" . str_pad(mt_rand(1, 9999), 4, "0", STR_PAD_LEFT);

        // note: there are 11 data columns (excluding created_at), so provide 11 placeholders
        $sql = "INSERT INTO barangay_ids (
            last_name, first_name, middle_name, address,
            emergency_name, emergency_address, emergency_contact,
            relationship, valid_id_path, user_id, reference_number, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            // 11 string values
            "sssssssssss",
            $last_name, $first_name, $middle_name, $address,
            $emergency_name, $emergency_address, $emergency_contact,
            $relationship, $valid_id_path, $user_id, $reference_number
        );

        if (!$stmt->execute()) {
            throw new Exception('Failed to save record.');
        }

        $stmt->close();
        $conn->close();

        echo json_encode(['success' => true, 'reference_number' => $reference_number]);
        exit();
    }
} catch (Exception $e) {
    error_log('submit_id.php error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
}

// fallback
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
exit();
?>