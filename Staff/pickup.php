<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$db_user = "root";
$db_pass = "";
$database = "barangay";

$conn = new mysqli($servername, $db_user, $db_pass, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$limit = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$start = ($page - 1) * $limit;

$count_sql = "
SELECT COUNT(*) AS total
FROM (
    SELECT id FROM cedula_requests   WHERE approval_status='picked_up'
     UNION ALL
    SELECT id FROM presently_requests       WHERE approval_status='picked_up'
    UNION ALL
    SELECT id FROM certificate_of_residency WHERE approval_status='picked_up'
    UNION ALL
    SELECT id FROM certificate_of_indigency WHERE approval_status='picked_up'
    UNION ALL
    SELECT id FROM barangay_ids             WHERE approval_status='picked_up'
    UNION ALL
    SELECT id FROM barangay_clearances      WHERE approval_status='picked_up'
) AS combined
";
$count_result = $conn->query($count_sql);
$total_rows = 0;
if ($count_result) {
    $row_count = $count_result->fetch_assoc();
    $total_rows = (int)$row_count['total'];
}
$total_pages = ($total_rows > 0) ? ceil($total_rows / $limit) : 1;

$data_sql = "
  SELECT
        id,
        reference_number,
        first_name,
        middle_name,
        last_name,
        created_at,
        approval_status,
        pickup_date,
        'Cedula Request' AS doc_type,
        proof_of_payment_path AS valid_id_path,
        picked_up_by,  
    authorized_person_name,  
    picked_up_at
    FROM cedula_requests
    WHERE approval_status='picked_up'

    UNION ALL
SELECT * FROM (
    SELECT
        id,
        reference_number,
        first_name,
        middle_name,
        last_name,
        created_at,
        approval_status,
         pickup_date,
        'Presently Request' AS doc_type,
        valid_id_path,
        picked_up_by,  
    authorized_person_name,  
    picked_up_at
    FROM presently_requests
    WHERE approval_status='picked_up'

    UNION ALL

    SELECT
        id,
        reference_number,
        first_name,
        middle_name,
        last_name,
        created_at,
        approval_status,
         pickup_date,
        'Certificate of Residency' AS doc_type,
        valid_id_path,
        picked_up_by,  
    authorized_person_name,  
    picked_up_at
    FROM certificate_of_residency
    WHERE approval_status='picked_up'

    UNION ALL

    SELECT
        id,
        reference_number,
        first_name,
        middle_name,
        last_name,
        created_at,
        approval_status,
         pickup_date,
        'Certificate of Indigency' AS doc_type,
        valid_id_path,
        picked_up_by,  
    authorized_person_name, 
    picked_up_at
    FROM certificate_of_indigency
    WHERE approval_status='picked_up'

    UNION ALL

    SELECT
        id,
        reference_number,
        first_name,
        middle_name,   
        last_name,
        created_at,
        approval_status,
         pickup_date,
        'Barangay ID' AS doc_type,
        valid_id_path,
        picked_up_by,  
    authorized_person_name,  
    picked_up_at
    FROM barangay_ids
    WHERE approval_status='picked_up'

    UNION ALL

    SELECT
        id,
        reference_number,
        first_name,
        middle_name,
        last_name,
        created_at,
        approval_status,
         pickup_date,
        'Barangay Clearance' AS doc_type,
        valid_id_path,
        picked_up_by, 
    authorized_person_name, 
    picked_up_at
    FROM barangay_clearances
    WHERE approval_status='picked_up'
    
) AS combined
ORDER BY created_at DESC
LIMIT $start, $limit
";

$result = $conn->query($data_sql);
?>
<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<head>
    <meta charset="UTF-8">
    <title>All Picked-Up Documents</title>
    <style>
        .styled-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            margin: 25px 0;
            font-size: 0.9em;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-width: 800px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .styled-table thead tr {
            background-color: #003566;
            color: #ffffff;
            text-align: left;
            position: sticky;
            top: 0;
        }

        .styled-table th,
        .styled-table td {
            padding: 15px 20px;
            vertical-align: middle;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
            transition: all 0.2s;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f8f9fa;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #003566;
        }

        .styled-table tbody tr:hover {
            background-color: #e9ecef;
            transform: translateX(4px);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-picked_up {
            background-color: #d4edda;
            color: #155724;
        }
     .sidebar a.active {
    background-color: #007bff; 
    color: white; 
    font-weight: bold; 
    border-radius: 5px;
    padding: 10px;
}

       * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
            background-color: #f4f4f4;
            overflow-x: hidden;
        }

        .sidebar {
            overflow-y: auto;
            z-index: 2; 
            width: 270px;
            background-color: #003566;
            transform: translateX(0);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100%;
            transition: transform 0.3s ease;
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
            flex-grow: 1;
            padding: 30px 40px;
            margin-left: 250px;
            background-color: #f9fafb;
            transition: margin-left 0.3s ease;
        }

        .main-content h1 {
            text-align: center;
            font-size: 32px;
            color: #1e3a8a;
            margin-bottom: 20px;
        }

        .section-box {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-gap: 20px;
}

        .section-box div {
            background-color: #1e3a8a;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 200px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .section-box div:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .section-box div h3 {
            font-size: 22px;
            margin-bottom: 10px;
            font-weight: bold;
            color: #f9fafb;
        }

        .section-box div p {
            margin-bottom: 5px;
            color: #e5e7eb;
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

        .hamburger.open div:nth-child(2) {
            opacity: 100;
        }
        @media (max-width: 1024px) {
    .section-box {
        grid-template-columns: repeat(2, 1fr);
    }
}
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }
            .section-box {
        grid-template-columns: 1fr;
    }

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
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="hamburger" onclick="toggleSidebar()">
    <div></div>
    <div></div>
    <div></div>
</div>
<?php include 'sidebar.php'; ?>
<div class="main-content" onclick="closeSidebar()">
    <br><br>
    <h1>All Picked-Up Documents</h1>
    <table class="styled-table">
            <thead>
                <tr>
                    <th>Reference #</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Date Applied</th>
                    <th>Picked Up Date</th>
                    <th>Picked Up By</th>
                    <th>Authorized Person</th>
                    <th>Status</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td style="color: blue;"><?php echo htmlspecialchars($row['reference_number']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                <td><?php echo htmlspecialchars($row['middle_name'] ?: 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                <td>
                    <?php if ($row['picked_up_at']): ?>
                        <?php echo date('M d, Y h:i A', strtotime($row['picked_up_at'])); ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['picked_up_by'] === 'authorized'): ?>
                        Authorized Person
                    <?php else: ?>
                        Document Owner
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['picked_up_by'] === 'authorized' && !empty($row['authorized_person_name'])): ?>
                        <?php echo htmlspecialchars($row['authorized_person_name']); ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <span class="status-badge status-<?php echo $row['approval_status']; ?>">
                        <?php echo ucfirst($row['approval_status']); ?>
                    </span>
                </td>
                <td><?php echo htmlspecialchars($row['doc_type']); ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="10" style="text-align: center; padding: 30px;">
                No picked-up documents found.
            </td>
        </tr>
    <?php endif; ?>
</tbody>
        </table>
    <div class="pagination">
        <?php
        if ($total_pages > 1) {
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = $i == $page ? 'active' : '';
                echo "<a href='?page=$i' class='$active'>$i</a>";
            }
        }
        ?>
    </div>
</div>
<script>
function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const hamburger = document.querySelector('.hamburger');
            sidebar.classList.toggle('active');
            hamburger.classList.toggle('open');
        }
        function closeSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const hamburger = document.querySelector('.hamburger');
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                hamburger.classList.remove('open');
            }
        }
</script>
</body>
</html>
<?php
$conn->close();
?>
