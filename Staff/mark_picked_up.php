<?php
session_start();
$id = $_POST['id'];

$conn = new mysqli("localhost", "root", "", "barangay");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pickup_time = date("Y-m-d H:i:s");
$sql = "UPDATE certificate_of_residency SET approval_status = 'picked_up', pickup_time = '$pickup_time' WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    header("Location: certificate-of-residency.php");
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
