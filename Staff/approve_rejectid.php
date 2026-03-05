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
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $application_id = $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $sql = "UPDATE barangay_ids SET approval_status = 'approved', 
                    reject_reason = NULL,
                    rejected_at = NULL 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $application_id);

    } elseif ($action === 'schedule_pickup') {
        $pickup_date = $_POST['pickup_date'];
        $sql = "UPDATE barangay_ids 
                SET pickup_date = ?, 
                    approval_status = 'pickup scheduled' 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $pickup_date, $application_id);

    } elseif ($action === 'picked_up') {
       $sql = "UPDATE barangay_ids 
                SET approval_status = 'picked_up' 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $application_id);

    } elseif ($action === 'reject') {
        $reject_reason = $_POST['reject_reason'] ?? '';
        $sql = "UPDATE barangay_ids 
                SET approval_status = 'rejected',
                    reject_reason = ?,
                    rejected_at = NOW(),
                    pickup_date = NULL 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $reject_reason, $application_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Action completed successfully";
    } else {
        $_SESSION['error'] = "Error processing request: " . $conn->error;
    }
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: barangay-id.php");
    exit();
}
?>
