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
        $payment_method = $_POST['payment_method'] ?? 'cash';
        $gcash_reference = $_POST['gcash_reference'] ?? null;

        // Validate payment method
        if (!in_array($payment_method, ['cash', 'gcash'])) {
            throw new Exception('Invalid payment method');
        }

        // Validate GCash reference (only for GCash payments)
        if ($payment_method === 'gcash') {
            if (empty($gcash_reference) || !preg_match('/^\d{13}$/', $gcash_reference)) {
                throw new Exception('Valid 13-digit GCash reference number is required');
            }
        }

        // Get user data
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT first_name, last_name, middle_name, blk_street, barangay, city, province, region FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            throw new Exception('User not found');
        }

        // Construct present_address
        $present_address = implode(', ', [
            $user['blk_street'],
            $user['barangay'],
            $user['city'],
            $user['province'],
            $user['region']
        ]);

        // Generate reference number
        $date = date('Ymd');
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM cedula_requests WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['count'] + 1;
        $reference_number = "CED-" . $date . "-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Handle file upload for GCash only
        $proof_of_payment_path = null;
        if ($payment_method === 'gcash') {
            if (!isset($_FILES['payment_proof'])) {
                throw new Exception('Payment proof is required for GCash');
            }

            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . basename($_FILES['payment_proof']['name']);
            if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $uploadFile)) {
                $proof_of_payment_path = $uploadFile;
            } else {
                throw new Exception('Failed to upload payment proof');
            }
        }

        // Dynamically build SQL based on payment method
        $sql = "INSERT INTO cedula_requests 
            (user_id, reference_number, payment_method, gcash_reference, first_name, last_name, middle_name, present_address, proof_of_payment_path, approval_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

        $stmt = $conn->prepare($sql);
        
        // For cash, set GCash fields to NULL
        $gcash_reference = ($payment_method === 'cash') ? null : $gcash_reference;
        $proof_of_payment_path = ($payment_method === 'cash') ? null : $proof_of_payment_path;

        $stmt->bind_param("issssssss", 
        $user_id,
        $reference_number,
        $payment_method,
        $gcash_reference,
        $user['first_name'],
        $user['last_name'],
        $user['middle_name'],
        $present_address,
        $proof_of_payment_path
    );

        if (!$stmt->execute()) {
            throw new Exception('Database error: ' . $stmt->error);
        }

        $response['success'] = true;
        $response['reference_number'] = $reference_number;

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$conn->close();
?>