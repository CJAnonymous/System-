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

$items_per_page = 20;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max($current_page, 1);
$offset = ($current_page - 1) * $items_per_page;

$allowed_filters = [
    'all', 
    'certificate_of_residency', 
    'presently_requests', 
    'barangay_ids', 
    'certificate_of_indigency', 
    'barangay_clearances',
    'cedula_requests' 
];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
if (!in_array($filter, $allowed_filters)) {
    $filter = 'all';
}

if ($filter === 'all') {
    $count_sql = "SELECT SUM(count) as total FROM (
        SELECT COUNT(*) as count FROM certificate_of_residency WHERE user_id = ?
        UNION ALL
        SELECT COUNT(*) FROM presently_requests WHERE user_id = ?
        UNION ALL
        SELECT COUNT(*) FROM barangay_ids WHERE user_id = ?
        UNION ALL
        SELECT COUNT(*) FROM certificate_of_indigency WHERE user_id = ?
        UNION ALL
        SELECT COUNT(*) FROM barangay_clearances WHERE user_id = ?
        UNION ALL
        SELECT COUNT(*) FROM cedula_requests WHERE user_id = ? 
    ) AS counts";
    $stmt_count = $conn->prepare($count_sql);
    $stmt_count->bind_param("iiiiii", 
        $_SESSION['user_id'], 
        $_SESSION['user_id'],
        $_SESSION['user_id'],
        $_SESSION['user_id'],
        $_SESSION['user_id'],
        $_SESSION['user_id']
    );
} else {
    $count_sql = "SELECT COUNT(*) as total FROM $filter WHERE user_id = ?";
    $stmt_count = $conn->prepare($count_sql);
    $stmt_count->bind_param("i", $_SESSION['user_id']);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$total_pending = $row_count['total'] ?? 0;
$total_pages = ceil($total_pending / $items_per_page);
$total_pages = max($total_pages, 1);

if ($current_page > $total_pages) {
    $current_page = $total_pages;
    $offset = ($current_page - 1) * $items_per_page;
}

if ($filter === 'all') {
    $pending_sql = "SELECT * FROM (
        SELECT 'Certificate of Residency' AS type, reference_number, created_at, 
               approval_status, pickup_date, reject_reason, rejected_at 
        FROM certificate_of_residency WHERE user_id = ?
        UNION ALL
        SELECT 'Presently Requests', reference_number, created_at, 
               approval_status, pickup_date, reject_reason, rejected_at 
        FROM presently_requests WHERE user_id = ?
        UNION ALL
        SELECT 'Barangay ID', reference_number, created_at, 
               approval_status, pickup_date, reject_reason, rejected_at 
        FROM barangay_ids WHERE user_id = ?
        UNION ALL
        SELECT 'Certificate of Indigency', reference_number, created_at, 
               approval_status, pickup_date, reject_reason, rejected_at 
        FROM certificate_of_indigency WHERE user_id = ?
        UNION ALL
        SELECT 'Barangay Clearance', reference_number, created_at, 
               approval_status, pickup_date, reject_reason, rejected_at 
        FROM barangay_clearances WHERE user_id = ?
        UNION ALL
        SELECT 'Community Tax Certificate (Cedula)', reference_number, created_at, 
               approval_status, pickup_date, reject_reason, rejected_at 
        FROM cedula_requests WHERE user_id = ?  
    ) AS combined_pending
    ORDER BY created_at DESC LIMIT ? OFFSET ?";
    
    
    $params = array_fill(0, 6, $_SESSION['user_id']); 
    array_push($params, $items_per_page, $offset);
    $types = str_repeat('i', 8); 

} else {
    $type_name = match($filter) {
        'cedula_requests' => 'Community Tax Certificate (Cedula)',
        default => str_replace('_', ' ', ucwords($filter, '_'))
    };
    
    $pending_sql = "SELECT ? AS type, reference_number, created_at, 
                   approval_status, pickup_date, reject_reason, rejected_at 
                   FROM $filter 
                   WHERE user_id = ? 
                   ORDER BY created_at DESC LIMIT ? OFFSET ?";
    
    $params = [
        $type_name, 
        $_SESSION['user_id'], 
        $items_per_page, 
        $offset
    ];
    $types = 'siii'; 
}

$stmt_pending = $conn->prepare($pending_sql);
$stmt_pending->bind_param($types, ...$params); 
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();

$pending_requests = [];
while ($row = $result_pending->fetch_assoc()) {
    $pending_requests[] = $row;
}

$conn->close();

$tables = [
    'certificate_of_residency',
    'presently_requests',
    'barangay_ids',
    'certificate_of_indigency',
    'barangay_clearances',
    'cedula_requests' 
];
$monthly_data = array_fill(1, 12, 0);
$monthly_data_json = json_encode($monthly_data);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
         .filters {
            margin: 20px 0;
            text-align: center;
        }

        .filters select {
            padding: 8px 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: white;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filters select:hover {
            border-color: #007bff;
        }

        .filters select:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,123,255,.25);
        }

        .rejection-details {
            margin-top: 8px;
            padding: 8px;
            background: #ffe6e6;
            border-radius: 4px;
            border: 1px solid #ffb3b3;
        }
        .rejection-details {
    margin-top: 8px;
    padding: 8px;
    background: #ffe6e6;
    border-radius: 4px;
    border: 1px solid #ffb3b3;
}

.rejection-reason {
    color: #cc0000;
    font-size: 0.9em;
}

.rejection-date {
    color: #666;
    font-size: 0.85em;
    margin-top: 4px;
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
            height: 100vh;
            background-color: #f4f4f4;
        }

        .sidebar {
            z-index: 2;
            width: 250px;
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

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
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
            transform: translateX(0);
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

        h1 {
            font-size: 28px;
            color: #003566;
            text-align: center;
            margin-bottom: 20px;
        }

        .ref-number {
            font-weight: bold;
            color: #007BFF;
        }

        .statistics {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .stat-box {
            text-align: center;
            background-color: #007BFF;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 10px;
            width: 180px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-box h2 {
            margin-bottom: 10px;
            font-size: 24px;
        }

        .chart-container {
            max-width: 800px;
            margin: 20px auto;
        }

        canvas {
            max-width: 100%;
        }

        .pending-requests-container {
            margin-top: 40px;
        }

        .pending-requests-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #003566;
        }

        .pending-requests-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            max-width: 1000px;
        }

        .pending-requests-table th, .pending-requests-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        .pending-requests-table th {
            background-color: #007BFF;
            color: white;
        }

        .pending-requests-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .pending-requests-table tr:hover {
            background-color: #ddd;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 5px;
        }

        .pagination a, .pagination span {
            color: #007BFF;
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 2px;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination .active {
            background-color: #007BFF;
            color: white;
            border: 1px solid #007BFF;
        }

        .pagination .disabled {
            color: #ccc;
            pointer-events: none;
            border: 1px solid #ddd;
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

            .statistics {
                justify-content: center;
            }

            .stat-box {
                width: 100%;
            }

            .chart-container {
                max-width: 100%;
                padding: 0 20px;
            }

            .pending-requests-table th, .pending-requests-table td {
                padding: 8px;
                font-size: 14px;
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
        .status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.9em;
    display: inline-block;
}

.status-picked {
    background-color: #28a745;
    color: white;
}

.status-approved {
    background-color: #90EE90;
    color: #1a531b;
}

.status-pending {
    background-color: #6c757d;
    color: white;
}
.status-rejected {
    background-color: #dc3545; /* Red */
    color: white;
}

.status-scheduled {
    background-color: #ffc107; 
    color: #000;
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

    <div class="pending-requests-container">
            <h2>List of Pending Requests</h2>
            <?php if (!empty($pending_requests)): ?>
                <table class="pending-requests-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Reference Number</th>
                            <th>Submitted On</th>
                            <th>Pick Up Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_requests as $request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['type']); ?></td>
                                <td class="ref-number"><?php echo htmlspecialchars($request['reference_number']); ?></td>
                                <td><?php echo date('F d, Y', strtotime($request['created_at'])); ?></td>
                                <td>
    <?php 
    $pickupDate = $request['pickup_date'];
    if (!empty($pickupDate) && $pickupDate !== '0000-00-00' && strtotime($pickupDate) !== false) {
        echo date('F d, Y', strtotime($pickupDate));
    } else {
        echo 'Not Scheduled';
    }
    ?>
</td>
<td>
    <?php 
    $status = $request['approval_status'];
    $status_label = ucfirst(str_replace('_', ' ', $status));
    $status_class = '';
    
    if ($status === 'picked_up') {
        $status_class = 'status-picked';
        $status_label = 'Picked Up';
    } elseif ($status === 'pending') {
        $status_class = 'status-pending';
    } elseif ($status === 'approved') {
        $status_class = 'status-approved';
    } elseif ($status === 'rejected') {
        $status_class = 'status-rejected';
    } elseif ($status === 'pickup scheduled') {
        $status_class = 'status-scheduled';
        $status_label = 'Pickup scheduled';
    }
    ?>
    <span class="status-badge <?php echo $status_class; ?>">
        <?php echo $status_label; ?>
    </span>
    
    <?php if ($status === 'rejected' && !empty($request['reject_reason'])): ?>
        <div class="rejection-details">
            <div class="rejection-reason">
                <?php echo htmlspecialchars($request['reject_reason']); ?>
            </div>
            <?php if (!empty($request['rejected_at'])): ?>
                <div class="rejection-date">
                    <?php echo date('M j, Y h:i A', strtotime($request['rejected_at'])); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</td>

</tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php 
                        $pagination_url = "?filter=$filter&page=";
                        if ($current_page > 1): ?>
                            <a href="<?= $pagination_url . ($current_page - 1) ?>">&laquo; Previous</a>
                        <?php else: ?>
                            <span class="disabled">&laquo; Previous</span>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $current_page): ?>
                                <span class="active"><?= $i ?></span>
                            <?php else: ?>
                                <a href="<?= $pagination_url . $i ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <a href="<?= $pagination_url . ($current_page + 1) ?>">Next &raquo;</a>
                        <?php else: ?>
                            <span class="disabled">Next &raquo;</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p style="text-align: center; color: #555;">You have no requests.</p>
            <?php endif; ?>
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
    <script>
        const monthlyData = <?php echo $monthly_data_json; ?>;

        const ctx = document.getElementById('applicationsChart').getContext('2d');
        const applicationsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ],
                datasets: [{
                    label: 'Applications Submitted',
                    data: Object.values(monthlyData),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
