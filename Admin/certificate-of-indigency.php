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


$limit = 10; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_sql = "SELECT COUNT(*) FROM certificate_of_indigency";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);

$sql = "SELECT * FROM certificate_of_indigency LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Indigency</title>
    <style>
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
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            overflow-y: auto;
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
                padding: 20px;
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

        @media (max-width: 768px) {
            .main-content {
                overflow-x: auto; 
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
        <br>
        <br>
        <h1>Pending Certificate of Indigency Applications</h1>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Purpose</th>
                    <th>Valid ID</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status = strtolower(trim($row['approval_status']));
                        
                        switch ($status) {
                            case 'pending':
                                $status_class = 'pending';
                                $display_status = 'To Be Approved';
                                break;
                            case 'approved':
                                $status_class = 'on-process';
                                $display_status = 'On Process';
                                break;
                            case 'picked_up':
                                $status_class = 'picked-up';
                                $display_status = 'Picked Up';
                                break;
                            case 'rejected':
                                $status_class = 'rejected';
                                $display_status = 'Rejected';
                                break;
                            default:
                                $status_class = '';
                                $display_status = 'Unknown';
                        }

                        echo "<tr>";
                        echo "<td>" . $row['first_name'] . " " . $row['middle_name'] . " " . $row['last_name'] . "</td>";
                        echo "<td>" . $row['address'] . "</td>";
                        echo "<td>" . $row['purpose'] . "</td>";
                        echo "<td><a href='#lightbox" . $row['id'] . "'><img src='../Resident/" . $row['valid_id_path'] . "' alt='Photo' width='100'></a></td>";
                        echo "<td><span class='status $status_class'>$display_status</span></td>";

                        echo "</tr>";

                        echo "<div id='lightbox" . $row['id'] . "' class='lightbox'>
                               <img src='../Resident/" . $row['valid_id_path'] . "' alt='Photo'>
                                <a href='#' class='close'>X</a>
                              </div>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No pending applications.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = $i == $page ? 'active' : '';
                echo "<a href='?page=$i' class='$active'>$i</a>";
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
