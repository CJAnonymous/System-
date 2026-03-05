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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $sql = "UPDATE certificate_of_indigency 
                SET approval_status = 'approved', 
                    reject_reason = NULL,
                    rejected_at = NULL 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
    } elseif ($action === 'reject') {
        $reject_reason = $_POST['reject_reason'] ?? '';
        $sql = "UPDATE certificate_of_indigency 
                SET approval_status = 'rejected',
                    reject_reason = ?,
                    rejected_at = NOW(),
                    pickup_date = NULL 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $reject_reason, $id);
    } elseif ($action === 'schedule_pickup') {
        $pickup_date = $_POST['pickup_date'];
        $sql = "UPDATE certificate_of_indigency 
                SET pickup_date = ?, 
                    approval_status = 'pickup scheduled' 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $pickup_date, $id);
    } elseif ($action === 'picked_up') {
        // Handle pickup confirmation
        $picked_up_by = $_POST['picked_up_by'] ?? 'owner'; // Default to 'owner'
        $authorized_person_name = $_POST['authorized_person_name'] ?? null;

        // Validate authorized person name if picked up by authorized person
        if ($picked_up_by === 'authorized' && empty($authorized_person_name)) {
            $_SESSION['error'] = "Please provide the name of the authorized person.";
            header("Location: certificate-of-indigency.php");
            exit();
        }

        $sql = "UPDATE certificate_of_indigency 
                SET approval_status = 'picked_up',
                    picked_up_by = ?,
                    authorized_person_name = ?,
                    picked_up_at = NOW()
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $_SESSION['error'] = "Prepare failed: " . $conn->error;
            header("Location: certificate-of-indigency.php");
            exit();
        }
        $stmt->bind_param('ssi', $picked_up_by, $authorized_person_name, $id);
    }

    // Execute the prepared statement
    if ($stmt->execute()) {
        $_SESSION['success'] = "Action completed successfully";
    } else {
        $_SESSION['error'] = "Error processing request: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: certificate-of-indigency.php");
    exit();
}

// If no action matched or invalid request
header("Location: certificate-of-indigency.php");
exit();
?>