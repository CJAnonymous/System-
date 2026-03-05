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

// Get only pending counts for each request type
$barangay_clearances_pending      = getApplicationCount('barangay_clearances', 'pending');
$barangay_ids_pending             = getApplicationCount('barangay_ids', 'pending');
$certificate_of_indigency_pending = getApplicationCount('certificate_of_indigency', 'pending');
$certificate_of_residency_pending = getApplicationCount('certificate_of_residency', 'pending');
$presently_requests_pending       = getApplicationCount('presently_requests', 'pending');
$cedula_requests_pending          = getApplicationCount('cedula_requests', 'pending');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Pending Requests</title>
  <!-- Font Awesome for Icons -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    rel="stylesheet"
  />
  <style>
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
  <div class="hamburger" onclick="toggleSidebar()">
    <div></div>
    <div></div>
    <div></div>
  </div>

  <?php include 'sidebar.php'; ?>

  <div class="main-content" onclick="closeSidebar()">
    <h1>Dashboard - Pending Requests</h1>
    <div class="section-box">
    <div class="card">
      <div class="card-content">
      <span class="status"><?php echo $barangay_clearances_pending; ?></span>
      <br>
      <br>
        <h3>Barangay Clearances</h3>
      </div>
      <div class="card-icon">
        <i class="fas fa-file-alt"></i>
      </div>
    </div>
    <div class="card">
      <div class="card-content">
      <span class="status"><?php echo $barangay_ids_pending; ?></span>
      <br>
      <br>
        <h3>Barangay IDs</h3>
      </div>
      <div class="card-icon">
        <i class="fas fa-id-card"></i>
      </div>
    </div>
    <div class="card">
      <div class="card-content">
      <span class="status"><?php echo $certificate_of_indigency_pending; ?></span>
      <br>
      <br>
        <h3>Certificate of Indigency</h3>
      </div>
      <div class="card-icon">
        <i class="fas fa-certificate"></i>
      </div>
    </div>
    <div class="card">
      <div class="card-content">
      <span class="status"><?php echo $certificate_of_residency_pending; ?></span>
      <br>
      <br>
        <h3>Certificate of Residency</h3>
      </div>
      <div class="card-icon">
        <i class="fas fa-home"></i>
      </div>
    </div>
    <div class="card">
      <div class="card-content">
      <span class="status"><?php echo $presently_requests_pending; ?></span>
      <br>
      <br>
        <h3>Presently Requests</h3>
      </div>
      <div class="card-icon">
        <i class="fas fa-clock"></i>
      </div>
    </div>
    <div class="card">
      <div class="card-content">
      <span class="status"><?php echo $cedula_requests_pending; ?></span>
      <br>
      <br>
        <h3>Cedula Requests</h3>
      </div>
      <div class="card-icon">
        <i class="fas fa-file-contract"></i>
      </div>
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
