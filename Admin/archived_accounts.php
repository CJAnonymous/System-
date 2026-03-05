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

// Handle unarchive action
if (isset($_POST['unarchive'])) {
    $user_id = $_POST['unarchive'];
    $update_sql = "UPDATE users SET account_status = 'active' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

// Fetch archived accounts
$sql = "SELECT * FROM users WHERE account_status = 'archived'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Accounts</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
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
        .main-content{overflow-y:auto;flex-grow:1;padding:30px 40px;margin-left:250px;background-color:#f9fafb;transition:margin-left .3s ease}
        .main-content h1{font-size:28px;color:#003566;text-align:center;margin-bottom:20px}
        .create-account-btn{display:inline-block;background-color:#28a745;color:#fff;padding:10px 15px;border-radius:5px;text-decoration:none;margin-bottom:20px}
        .create-account-btn:hover{background-color:#218838}
        table{width:100%;border-collapse:collapse;margin-bottom:20px}
        table,th,td{border:1px solid #ddd}
        th,td{padding:12px;text-align:left}
        th{background-color:#003566;color:#fff}
        .action-btn{padding:8px 12px;margin:5px;cursor:pointer;border-radius:5px;transition:background-color .3s ease}
        .suspend-btn{background-color:#ffc107;color:#fff}
        .delete-btn{background-color:#dc3545;color:#fff}
        .action-btn:hover{opacity:.8}
        @media(max-width:768px){
            .sidebar{transform:translateX(-100%)}
            .sidebar.active{transform:translateX(0)}
            .main-content{margin-left:0;padding:20px}
        }
        .hamburger{display:none;cursor:pointer;position:fixed;top:15px;left:20px;z-index:3}
        .hamburger div{width:30px;height:3px;background-color:#003566;margin:5px;transition:.3s}
        .hamburger.open div{background-color:#fff}
        @media(max-width:768px){.hamburger{display:block}}
        @media(max-width:768px){.main-content{overflow-x:auto}}
        tr:hover{cursor:pointer;background-color:#f1f1f1}
        .modal{display:none;position:fixed;z-index:999;left:0;top:0;width:100%;height:100%;background-color:rgba(0,0,0,.8);justify-content:center;align-items:center}
        .modal-content{background-color:#fff;padding:20px;border-radius:5px;max-width:500px;width:100%}
        .modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
        .modal-header h2{margin:0}
        .modal-header span{cursor:pointer;font-size:28px;font-weight:700}
        .create-account-modal-content{
            max-width:600px;
            max-height:80vh;
            overflow-y:auto
        }
        .create-account-form{display:flex;flex-direction:column;gap:10px}
        .create-account-form label{font-weight:700}
        .create-account-form input,.create-account-form select{padding:8px;font-size:16px;width:100%}
        .create-account-form button{padding:10px;background-color:#28a745;border:none;color:#fff;cursor:pointer;font-size:16px;border-radius:5px}
        .create-account-form button:hover{background-color:#218838}
        .message{margin:10px 0;color:red;text-align:center;font-weight:700}
        .unsuspend-btn {
    background-color: #28a745; 
    color: #fff;
}
.unarchive-btn {
    background-color: #17a2b8; 
}    </style>
</head>
<body>
<div class="hamburger" onclick="toggleSidebar()">
    <div></div>
    <div></div>
    <div></div>
</div>
<?php include 'sidebar.php'; ?>
<div class="main-content" onclick="closeSidebar()">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Archived Accounts</h1>
        <a href="manageaccount.php" class="create-account-btn">
            <i class="fas fa-arrow-left"></i> Back to Manage Accounts
        </a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Address</th>
                <th>Contact Number</th>
                <th>Account Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['role'] . "</td>";
                    echo "<td>" 
                         . $row['blk_street'] . ", " 
                         . $row['barangay'] . ", "
                         . $row['city'] . ", "
                         . $row['province'] . ", "
                         . $row['region']
                         . "</td>";
                    echo "<td>" . $row['contact_number'] . "</td>";
                    echo "<td>" . ucfirst($row['account_status']) . "</td>";
                    echo "<td>
                            <form action='' method='POST' style='display:inline-block;'>
                                <button type='submit' name='unarchive' value='" . $row['id'] . "' class='action-btn unarchive-btn'>Unarchive</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No archived accounts found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<script>
function openModal(m){document.getElementById(m).style.display='flex'}
function closeModal(m){document.getElementById(m).style.display='none'}
function toggleSidebar(){const s=document.querySelector('.sidebar');const h=document.querySelector('.hamburger');s.classList.toggle('active');h.classList.toggle('open')}
function closeSidebar(){const s=document.querySelector('.sidebar');const h=document.querySelector('.hamburger');if(window.innerWidth<=768){s.classList.remove('active');h.classList.remove('open')}}
</script>
</body>
</html>
<?php
$conn->close();
?>