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
$application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $application_id = $_POST['id'];
    $pickup_time = $_POST['pickup_time'];
    $sql = "UPDATE barangay_ids SET pickup_time = ?, approval_status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $pickup_time, $application_id);
    if ($stmt->execute()) {
        echo "<script>
                alert('Pickup schedule set successfully!');
                window.location.href = 'barangay-id.php';
              </script>";
    } else {
        echo "<script>
                alert('Error setting pickup schedule. Please try again.');
                window.history.back();
              </script>";
    }
    $stmt->close();
    $conn->close();
    exit();
}
$sql = "SELECT * FROM barangay_ids WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $application_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();
$stmt->close();
$conn->close();
if (!$record) {
    echo "<script>
            alert('Record not found.');
            window.location.href='barangay-id.php';
          </script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Pickup</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .schedule-form {
            max-width: 400px;
            margin: 50px auto;
            background: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .schedule-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #003566;
        }
        .schedule-form label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }
        .schedule-form input[type="datetime-local"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
        }
        .schedule-form button {
            background-color: #28a745;
            color: #fff;
            padding: 10px 16px;
            border: none;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            border-radius: 5px;
        }
        .schedule-form button:hover {
            background-color: #218838;
        }
        .cancel-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #dc3545;
        }
    </style>
</head>
<body>
<div class="schedule-form">
    <h2>Set Pickup Schedule</h2>
    <form action="schedule_pickup.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
        <label for="pickup_time">Select Date &amp; Time:</label>
        <input type="datetime-local" name="pickup_time" id="pickup_time" required>
        <button type="submit">Save Schedule</button>
    </form>
    <a href="barangay-id.php" class="cancel-link">Cancel</a>
</div>
</body>
</html>
