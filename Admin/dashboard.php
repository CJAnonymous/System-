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

/* -------------------------
   FUNCTION FOR PENDING COUNT
-------------------------- */
function getApplicationCount($table, $status) {
    global $conn;
    $sql = "SELECT COUNT(*) AS count FROM $table WHERE approval_status = '$status'";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    return 0;
}

/* -------------------------
   PENDING REQUEST COUNTS
-------------------------- */
$barangay_clearances_pending      = getApplicationCount('barangay_clearances', 'pending');
$barangay_ids_pending             = getApplicationCount('barangay_ids', 'pending');
$certificate_of_indigency_pending = getApplicationCount('certificate_of_indigency', 'pending');
$certificate_of_residency_pending = getApplicationCount('certificate_of_residency', 'pending');
$presently_requests_pending       = getApplicationCount('presently_requests', 'pending');
$cedula_requests_pending          = getApplicationCount('cedula_requests', 'pending');

/* -------------------------
   NEW REGISTERED USERS
-------------------------- */
$sql_new_users = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$result_new_users = $conn->query($sql_new_users);

/* -------------------------
   USER COUNT BY ROLE (FOR PIE CHART)
-------------------------- */
$user_roles = [];
$sql_roles = "SELECT role, COUNT(*) as total FROM users GROUP BY role";
$result_roles = $conn->query($sql_roles);

/* -------------------------
   PENDING USER APPROVALS
-------------------------- */
$sql_pending = "SELECT id, first_name, last_name, email FROM users WHERE account_status='pending'";
$result_pending = $conn->query($sql_pending);

if ($result_roles && $result_roles->num_rows > 0) {
    while ($row = $result_roles->fetch_assoc()) {
        $user_roles[$row['role']] = $row['total'];
    }
}

$user_roles_json = json_encode($user_roles);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
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
}

.main-content {
    margin-left: 270px;
    padding: 40px;
    width: 100%;
    background-color: #f9fafb;
}

.section-title {
    margin-top: 60px;
    margin-bottom: 25px;
    text-align: center;
    font-size: 30px;
    color: #1e3a8a;
}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    margin-bottom: 40px;
}

table th, table td {
    border: 1px solid #ddd;
    padding: 12px;
}

table th {
    background-color: #003566;
    color: #fff;
}

.section-box {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 60px;
}

.card {
    background-color: #1e3a8a;
    color: white;
    padding: 25px;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status {
    background-color: #facc15;
    color: black;
    padding: 8px 15px;
    border-radius: 10px;
    font-size: 20px;
    font-weight: bold;
}

/* approval buttons */
.btn-approve, .btn-reject {
    display: inline-block;
    padding: 5px 12px;
    font-size: 0.9rem;
    border-radius: 5px;
    text-decoration: none;
    color: #fff;
    transition: background-color 0.2s ease;
    margin-right: 5px;
}
.btn-approve { background-color: #28a745; }
.btn-approve:hover { background-color: #218838; }
.btn-reject { background-color: #dc3545; }
.btn-reject:hover { background-color: #c82333; }

/* search input */
.table-search{margin:1rem 0; text-align:right;}
.table-search input{padding:.5rem .75rem; width:250px; border:1px solid #ccc; border-radius:4px;}

.card-icon {
    font-size: 55px;
    opacity: 0.3;
}

.chart-container {
    width: 500px;
    margin: auto;
}
/* Reset & Base Styles */
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
    /* Sidebar Styles */
    .sidebar {
      overflow-y: auto;
      z-index: 2;
      width: 270px;
      background-color: #003566;
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
    .sidebar a.active {
      background-color: #007bff;
      color: white;
      font-weight: bold;
      border-radius: 5px;
      padding: 10px;
    }
    .logout-btn {
      margin-top: auto;
      text-align: center;
      padding: 10px 15px;
      background-color: #dc3545;
      border-radius: 5px;
      color: white;
      text-decoration: none;
      font-size: 18px;
    }
    .logout-btn:hover {
      background-color: #a71d2a;
    }
    
    /* Hamburger Menu for Mobile */
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
    /* Responsive Breakpoints */
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
    .main-content {
      flex-grow: 1;
      padding: 10px;  
      margin-left: 270px;
      background-color: #f9fafb;
      transition: margin-left 0.3s ease;
    }
    .main-content h1 {
      text-align: center;
      font-size: 40px;
      color: #1e3a8a;
      margin-bottom: 90px;
    }
    .section-box {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }
    .card {
      background-color: #1e3a8a;
      color: white;
      padding: 20px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-height: 250px;  
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .card-content h3 {
      font-size: 30px;
      margin-bottom: 10px;
    }
    .card-content p {
      font-size: 25px;
    }
    .status {
      padding: 5px 10px;
      border-radius: 10px;
      font-size: 40px;
      font-weight: bold;
      background-color: #facc15;
      color: black;
      display: inline-block;
    }
    .card-icon {
      font-size: 110px;  
      opacity: 0.3;
    }
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
     <!-- PENDING REQUESTS -->
    <h1 class="section-title">Pending Requests</h1>

    <div class="section-box">

        <div class="card">
            <div>
                <span class="status"><?php echo $barangay_clearances_pending; ?></span>
                <h3>Barangay Clearances</h3>
            </div>
            <div class="card-icon"><i class="fas fa-file-alt"></i></div>
        </div>

        <div class="card">
            <div>
                <span class="status"><?php echo $barangay_ids_pending; ?></span>
                <h3>Barangay IDs</h3>
            </div>
            <div class="card-icon"><i class="fas fa-id-card"></i></div>
        </div>

        <div class="card">
            <div>
                <span class="status"><?php echo $certificate_of_indigency_pending; ?></span>
                <h3>Certificate of Indigency</h3>
            </div>
            <div class="card-icon"><i class="fas fa-certificate"></i></div>
        </div>

        <div class="card">
            <div>
                <span class="status"><?php echo $certificate_of_residency_pending; ?></span>
                <h3>Certificate of Residency</h3>
            </div>
            <div class="card-icon"><i class="fas fa-home"></i></div>
        </div>

        <div class="card">
            <div>
                <span class="status"><?php echo $presently_requests_pending; ?></span>
                <h3>Presently Requests</h3>
            </div>
            <div class="card-icon"><i class="fas fa-clock"></i></div>
        </div>

        <div class="card">
            <div>
                <span class="status"><?php echo $cedula_requests_pending; ?></span>
                <h3>Cedula Requests</h3>
            </div>
            <div class="card-icon"><i class="fas fa-file-contract"></i></div>
        </div>

    </div>

    <!-- PENDING USER APPROVALS -->
    <h1 class="section-title">Pending User Approvals</h1>
    <div class="table-search">
        <input type="text" id="pendingSearch" placeholder="Search pending users..." />
    </div>
    <table id="pendingTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $counter = 1; 
        if ($result_pending && $result_pending->num_rows > 0): 
            while($row = $result_pending->fetch_assoc()): 
        ?>
            <tr>
                <td><?php echo $counter++; ?></td>
                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                    <a class="btn-approve" href="manage_users.php?action=approve&id=<?php echo $row['id']; ?>">Approve</a>
                    <a class="btn-reject" href="manage_users.php?action=reject&id=<?php echo $row['id']; ?>">Reject</a>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr>
                <td colspan="4">No pending users</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- NEW REGISTERED USERS -->
    <h1 class="section-title">New Registered Users</h1>
    <div class="table-search">
        <input type="text" id="newSearch" placeholder="Search registered users..." />
    </div>
    <table id="newTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered At</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $counter = 1; 
        if ($result_new_users && $result_new_users->num_rows > 0): 
            while($row = $result_new_users->fetch_assoc()): 
        ?>
            <tr>
                <td><?php echo $counter++; ?></td>
                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
            </tr>
        <?php endwhile; else: ?>
            <tr>
                <td colspan="5">No new registrations</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

   

    <!-- PIE CHART -->
    <h1 class="section-title">User Registration Overview</h1>

    <div class="chart-container">
        <canvas id="userPieChart"></canvas>
    </div>

</div>

<script>
const userData = <?php echo $user_roles_json ?: '{}'; ?>;

const labels = Object.keys(userData);
const dataValues = Object.values(userData);

const ctx = document.getElementById('userPieChart').getContext('2d');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: labels,
        datasets: [{
            data: dataValues,
            backgroundColor: [
                '#1e3a8a',
                '#facc15',
                '#dc2626',
                '#16a34a',
                '#9333ea',
                '#0ea5e9'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<script>
// filter table rows by text in first two columns (name/email)
function filterTable(inputId, tableId) {
    const query = document.getElementById(inputId).value.toLowerCase();
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for (let row of rows) {
        const cells = row.getElementsByTagName('td');
        let text = '';
        if (cells.length >= 2) {
            text = (cells[1].textContent + ' ' + (cells[2] ? cells[2].textContent : '')).toLowerCase();
        }
        row.style.display = text.indexOf(query) > -1 ? '' : 'none';
    }
}
document.getElementById('pendingSearch').addEventListener('keyup', ()=>filterTable('pendingSearch','pendingTable'));
document.getElementById('newSearch').addEventListener('keyup', ()=>filterTable('newSearch','newTable'));
</script>

</body>
</html>

<?php $conn->close(); ?>