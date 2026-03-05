<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "barangay");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$sql = "
SELECT 
    'Barangay Clearance' AS document_type,
    reference_number,
    created_at,
    approval_status,
    pickup_date
FROM barangay_clearances
WHERE user_id = $user_id

UNION ALL

SELECT 
    'Barangay ID' AS document_type,
    reference_number,
    created_at,
    approval_status,
    pickup_date
FROM barangay_ids
WHERE user_id = $user_id

UNION ALL

SELECT 
    'Cedula' AS document_type,
    reference_number,
    created_at,
    approval_status,
    pickup_date
FROM cedula_requests
WHERE user_id = $user_id

UNION ALL

SELECT 
    'Certificate of Indigency' AS document_type,
    reference_number,
    created_at,
    approval_status,
    pickup_date
FROM certificate_of_indigency
WHERE user_id = $user_id

UNION ALL

SELECT 
    'Certificate of Residency' AS document_type,
    reference_number,
    created_at,
    approval_status,
    pickup_date
FROM certificate_of_residency
WHERE user_id = $user_id

UNION ALL

SELECT 
    'Presently' AS document_type,
    reference_number,
    created_at,
    approval_status,
    pickup_date
FROM presently_requests
WHERE user_id = $user_id

ORDER BY created_at DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transaction History</title>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
}

.main-content {
    padding: 30px;
    margin-left: 250px;
}

h1 {
    text-align: center;
    color: #003566;
}

.table-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background-color: #003566;
    color: white;
    padding: 12px;
}

td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

tr:hover {
    background-color: #f1f1f1;
}

.status-approved {
    color: green;
    font-weight: bold;
}

.status-rejected {
    color: red;
    font-weight: bold;
}

.status-pending {
    color: orange;
    font-weight: bold;
}
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h1>Transaction History</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Document Type</th>
                    <th>Reference Number</th>
                    <th>Date Requested</th>
                    <th>Status</th>
                    <th>Pickup Date</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['document_type']; ?></td>
                        <td><?php echo $row['reference_number']; ?></td>
                        <td><?php echo date("F d, Y", strtotime($row['created_at'])); ?></td>
                        <td>
                            <?php
                                $status = strtolower($row['approval_status']);

                                if ($status == 'approved') {
                                    echo "<span class='status-approved'>Approved</span>";
                                } elseif ($status == 'rejected') {
                                    echo "<span class='status-rejected'>Rejected</span>";
                                } elseif ($status == 'pending') {
                                    echo "<span class='status-pending'>Pending</span>";
                                } else {
                                    echo ucfirst($row['approval_status']);
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if (!empty($row['pickup_date'])) {
                                    echo date("F d, Y", strtotime($row['pickup_date']));
                                } else {
                                    echo "Not Scheduled";
                                }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No transactions found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>