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
        $sql = "UPDATE cedula_requests 
                SET approval_status = 'approved', 
                    reject_reason = NULL,
                    rejected_at = NULL 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
    } elseif ($action === 'approve') {
        $sql = "UPDATE cedula_requests 
                SET approval_status = 'approved', 
                    reject_reason = NULL,
                    rejected_at = NULL,
                    pickup_date = NULL,  // Reset pickup_date
                    pickup_time = NULL   // Reset pickup_time
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
    } elseif ($action === 'reject') {
        $reject_reason = $_POST['reject_reason'] ?? '';
        $sql = "UPDATE cedula_requests 
                SET approval_status = 'rejected',
                    reject_reason = ?,
                    rejected_at = NOW(),
                    pickup_date = NULL,
                    pickup_time = NULL 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $reject_reason, $id);
    } elseif ($action === 'schedule_pickup') {
        $pickup_date = $_POST['pickup_date'];
        $sql = "UPDATE cedula_requests 
                SET pickup_date = ?,
                    approval_status = 'pickup scheduled' 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $pickup_date, $id);
   // Update the 'picked_up' case
} elseif ($action === 'picked_up') {
    $picked_up_by = $_POST['picked_up_by'] ?? 'owner';
    $authorized_person = $_POST['authorized_person_name'] ?? null;

    $sql = "UPDATE cedula_requests 
            SET approval_status = 'picked_up',
                picked_up_at = NOW(),   
                picked_up_by = ?,
                authorized_person_name = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['error'] = "Prepare failed: " . $conn->error;
        header("Location: cedula.php");
        exit();
    }
    $stmt->bind_param('ssi', $picked_up_by, $authorized_person, $id);


}

    if ($stmt->execute()) {
        $_SESSION['success'] = "Action completed successfully";
    } else {
        $_SESSION['error'] = "Error processing request: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
    header("Location: cedula.php");
    exit();
}
?>