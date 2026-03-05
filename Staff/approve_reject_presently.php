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
        $sql = "UPDATE presently_requests 
                SET approval_status = 'approved', 
                    reject_reason = NULL,
                    rejected_at = NULL 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $application_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Application approved successfully.";
        } else {
            $_SESSION['error'] = "Error approving application: " . $stmt->error;
        }
        $stmt->close();
        header("Location: transaction_history.php");
        exit();

    } elseif ($action === 'reject') {
        $reject_reason = $_POST['reject_reason'] ?? '';
        $sql = "UPDATE presently_requests 
                SET approval_status = 'rejected',
                    reject_reason = ?,
                    rejected_at = NOW(),
                    pickup_date = NULL 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $reject_reason, $application_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Application rejected successfully.";
        } else {
            $_SESSION['error'] = "Error rejecting application: " . $stmt->error;
        }
        $stmt->close();
        header("Location: transaction_history.php");
        exit();

    } elseif ($action === 'schedule_pickup') {
        $pickup_date = $_POST['pickup_date'];
        $sql = "UPDATE presently_requests 
                SET pickup_date = ?, 
                    approval_status = 'pickup scheduled' 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $pickup_date, $application_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Pickup scheduled successfully.";
        } else {
            $_SESSION['error'] = "Error scheduling pickup: " . $stmt->error;
        }
        $stmt->close();
        header("Location: transaction_history.php");
        exit();
        
    } elseif ($action === 'picked_up') {
        $picked_up_by = $_POST['picked_up_by'] ?? 'owner';
        $authorized_person = $_POST['authorized_person_name'] ?? null;
    
        $sql = "UPDATE presently_requests 
                SET approval_status = 'picked_up',
                    picked_up_by = ?,
                    authorized_person_name = ?,
                    picked_up_at = NOW()
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $_SESSION['error'] = "Prepare failed: " . $conn->error;
            header("Location: transaction_history.php");
            exit();
        }
        $stmt->bind_param('ssi', $picked_up_by, $authorized_person, $application_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Document marked as picked up successfully";
        } else {
            $_SESSION['error'] = "Error updating record: " . $stmt->error;
        }
        
        $stmt->close();
        header("Location: transaction_history.php");
        exit();
    }
}

// If no action matched or invalid request
header("Location: transaction_history.php");
exit();
?>
