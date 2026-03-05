<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();

try {

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception('Invalid request method.');
    }

    $conn = new mysqli("localhost", "root", "", "barangay");
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Sanitize input
    $maglalakad  = filter_input(INPUT_POST, 'maglalakad', FILTER_SANITIZE_SPECIAL_CHARS);
    $kaano_ano   = filter_input(INPUT_POST, 'kaano_ano', FILTER_SANITIZE_SPECIAL_CHARS);
    $saan_ipapasa = filter_input(INPUT_POST, 'saan_ipapasa', FILTER_SANITIZE_SPECIAL_CHARS);
    $purpose     = filter_input(INPUT_POST, 'purpose', FILTER_SANITIZE_SPECIAL_CHARS);
    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'cash';

    if (!$maglalakad || !$kaano_ano || !$saan_ipapasa || !$purpose) {
        throw new Exception('Missing required indigency details.');
    }

    if ($payment_method !== 'cash') {
        throw new Exception('Invalid payment method. Only cash is accepted.');
    }

    // Check login
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in.');
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        throw new Exception('User not found.');
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    $address = $user['blk_street'] . ", " .
               $user['barangay'] . ", " .
               $user['city'] . ", " .
               $user['province'] . ", " .
               $user['region'];

    // File Upload
    $upload_dir = "uploads/brgy_uploaded_valid_id/";

    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception('Failed to create upload directory.');
        }
    }

    if (!isset($_FILES['valid_id']) || $_FILES['valid_id']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Please attach a valid ID.');
    }

    $file_ext = strtolower(pathinfo($_FILES['valid_id']['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if (!in_array($file_ext, $allowed_ext)) {
        throw new Exception('Invalid file type. Only JPG, JPEG, and PNG are allowed.');
    }

    $new_file_name = uniqid("ID_") . "." . $file_ext;
    $target_file = $upload_dir . $new_file_name;

    if (!move_uploaded_file($_FILES['valid_id']['tmp_name'], $target_file)) {
        throw new Exception('Failed to upload file.');
    }

    // Insert record
    $reference_number = "BRG-" . date('Ymd') . "-" . str_pad(mt_rand(1,9999),4,"0",STR_PAD_LEFT);
    $pickup_time = date('H:i:s');

    $stmt = $conn->prepare("
        INSERT INTO certificate_of_indigency (
            last_name, first_name, middle_name, address,
            maglalakad, kaano_ano, saan_ipapasa, purpose,
            valid_id_path, pickup_time, user_id, reference_number, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "ssssssssssis",
        $user['last_name'],
        $user['first_name'],
        $user['middle_name'],
        $address,
        $maglalakad,
        $kaano_ano,
        $saan_ipapasa,
        $purpose,
        $target_file,
        $pickup_time,
        $user_id,
        $reference_number
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to save record: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    echo json_encode([
        'success' => true,
        'reference_number' => $reference_number
    ]);
    exit();

} catch (Exception $e) {

    error_log('submit_indigency.php error: ' . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit();
}
?>