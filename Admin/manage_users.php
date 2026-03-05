<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "barangay");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Approve / Reject
if (isset($_GET['action']) && isset($_GET['id'])) {

    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == "approve") {
        $conn->query("UPDATE users SET account_status='active', verified=1 WHERE id=$id");
    }

    if ($action == "reject") {
        $conn->query("UPDATE users SET account_status='rejected' WHERE id=$id");
    }

    // return to dashboard after processing
    header("Location: dashboard.php");
    exit();
}

// Get all pending users
$result = $conn->query("SELECT * FROM users WHERE account_status='pending'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
</head>
<body>
    
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td>
        <a href="?action=approve&id=<?php echo $row['id']; ?>">Approve</a> |
        <a href="?action=reject&id=<?php echo $row['id']; ?>">Reject</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

<?php $conn->close(); ?>