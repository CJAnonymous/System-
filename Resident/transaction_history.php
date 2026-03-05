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
{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            height: 100vh;
        }

        .sidebar {
    overflow-y: auto;
            width: 250px;
            background-color: #003566;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
        }


        .sidebar img {
            width: 100px;
            height: auto;
            border-radius: 50%;
            display: block;
            margin: 0 auto 20px;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            margin: 10px 0;
            padding: 10px 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #00214d;
        }

        .logout-btn {
            margin-top: auto;
            text-align: center;
            padding: 10px 15px;
            background-color: #DC3545;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        .logout-btn:hover {
            background-color: #a71d2a;
        }

        .main-content {
            overflow-y: auto;
            flex-grow: 1;
            padding: 30px 40px;
            margin-left: 250px;
            background-color: #f9fafb;
            transition: margin-left 0.3s ease;
        }


        .main-content h1 {
            font-size: 30px;
            color: #003566;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #003566;
            color: white;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            color: white;
            font-size: 14px;
        }

        .approve-btn {
            background-color: #28a745;
        }

        .approve-btn:hover {
            background-color: #218838;
        }

        .reject-btn {
            background-color: #dc3545;
        }

        .reject-btn:hover {
            background-color: #c82333;
        }

        .lightbox {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        .lightbox img {
            max-width: 90%;
            max-height: 80%;
        }

        .lightbox a.close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: white;
            font-size: 30px;
            text-decoration: none;
        }

        .lightbox:target {
            display: flex;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .pagination a {
            text-decoration: none;
            margin: 0 5px;
            padding: 8px 12px;
            color: #003566;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .pagination a:hover {
            background-color: #003566;
            color: white;
        }

        .pagination .active {
            background-color: #003566;
            color: white;
        }

        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            text-align: center;
        }

        .status.pending {
            background-color: #ffc107;
            color: #333;
        }

        .status.on-process {
            background-color: #17a2b8;
            color: white;
        }

        .status.picked-up {
            background-color: #28a745;
            color: white;
        }

        .status.rejected {
            background-color: #dc3545;
            color: white;
        }
        @media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0;
    }
}

.hamburger {
    display: none;
    cursor: pointer;
    position: fixed;
    top: 15px;
    left: 20px;
    z-index: 3;
}

.hamburger div {
    width: 30px;
    height: 3px;
    background-color: #003566;
    margin: 5px;
    transition: 0.3s;
}
.hamburger.open div {
    background-color: white; 
    
}
@media (max-width: 768px) {
    .hamburger {
        display: block;
    }
}
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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