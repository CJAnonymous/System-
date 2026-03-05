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
if (isset($_POST['create_account'])) {
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $place_of_birth = $_POST['place_of_birth'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $status = $_POST['status'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $father_first_name = $_POST['father_first_name'] ?? '';
    $father_last_name = $_POST['father_last_name'] ?? '';
    $father_occupation = $_POST['father_occupation'] ?? '';
    $mother_first_name = $_POST['mother_first_name'] ?? '';
    $mother_last_name = $_POST['mother_last_name'] ?? '';
    $mother_occupation = $_POST['mother_occupation'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? '';
    $username = $_POST['username'] ?? '';
    $password_val = $_POST['password'] ?? '';
    $blk_street = $_POST['blk_street'] ?? '';
    $barangay = $_POST['barangay'] ?? '';
    $city = $_POST['city'] ?? '';
    $province = $_POST['province'] ?? '';
    $region = $_POST['region'] ?? '';
    if (
        !empty($first_name) && !empty($last_name) && !empty($place_of_birth) &&
        !empty($birthdate) && !empty($gender) && !empty($status) && !empty($contact_number) &&
        !empty($email) && !empty($role) && !empty($username) && !empty($password_val) &&
        !empty($blk_street) && !empty($barangay) && !empty($city) &&
        !empty($province) && !empty($region)
    ) {
        $hashed_password = password_hash($password_val, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users 
        (first_name, middle_name, last_name, place_of_birth, birthdate, gender, status, contact_number, father_first_name, father_last_name, father_occupation, mother_first_name, mother_last_name, mother_occupation, email, role, username, password, created_at, blk_street, barangay, city, province, region, account_status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, 'active')";
        $stmt = $conn->prepare($sql);
        $now = date('Y-m-d H:i:s');
        $stmt->bind_param(
            "ssssssssssssssssssssssss",
            $first_name,
            $middle_name,
            $last_name,
            $place_of_birth,
            $birthdate,
            $gender,
            $status,
            $contact_number,
            $father_first_name,
            $father_last_name,
            $father_occupation,
            $mother_first_name,
            $mother_last_name,
            $mother_occupation,
            $email,
            $role,
            $username,
            $hashed_password,
            $now,
            $blk_street,
            $barangay,
            $city,
            $province,
            $region
        );
        $stmt->execute();
        $stmt->close();
        $_SESSION['message'] = "Account created.";
    } else {
        $_SESSION['message'] = "Please fill all required fields.";
    }
}
if (isset($_POST['suspend'])) {
    $user_id = $_POST['suspend'];
    $update_sql = "UPDATE users SET account_status = 'suspended' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
if (isset($_POST['archive'])) {
    $user_id = $_POST['archive'];
    $update_sql = "UPDATE users SET account_status = 'archived' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
if (isset($_POST['unsuspend'])) {
    $user_id = $_POST['unsuspend'];
    $update_sql = "UPDATE users SET account_status = 'active' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
if (isset($_POST['unarchive'])) {
    $user_id = $_POST['unarchive'];
    $update_sql = "UPDATE users SET account_status = 'active' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
$sql = "SELECT * FROM users WHERE account_status != 'archived'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Arial,sans-serif;display:flex;height:100vh;background-color:#f4f4f4}
        .sidebar{overflow-y:auto;z-index:2;width:250px;background-color:#003566;transform:translateX(0);color:#fff;display:flex;flex-direction:column;padding:20px;box-shadow:2px 0 5px rgba(0,0,0,.1);position:fixed;height:100%;transition:transform .3s ease}
        .sidebar img{width:100px;height:auto;border-radius:50%;display:block;margin:0 auto 20px}
        .sidebar h2{text-align:center;font-size:20px;margin-bottom:20px;line-height:1.4}
        .sidebar a{text-decoration:none;color:#fff;font-size:18px;margin:10px 0;padding:10px 15px;border-radius:5px;display:flex;align-items:center;gap:10px;transition:background-color .3s ease}
        .sidebar a:hover{background-color:#00214d}
        .sidebar a.active {
            background-color: #007bff; /* Highlight color */
            color: white; /* Text color */
            font-weight: bold; /* Optional: Makes it stand out more */
            border-radius: 5px;
            padding: 10px;
        }
        .logout-btn{margin-top:auto;text-align:center;padding:10px 15px;background-color:#dc3545;border-radius:5px;color:#fff;text-decoration:none;font-size:18px}
        .logout-btn:hover{background-color:#a71d2a}
        .main-content{overflow-y:auto;flex-grow:1;padding:30px 40px;margin-left:250px;background-color:#f9fafb;transition:margin-left .3s ease}
        .main-content h1{font-size:28px;color:#003566;text-align:center;margin-bottom:20px}
        .create-account-btn{display:inline-block;background-color:#28a745;color:#fff;padding:10px 15px;border-radius:5px;text-decoration:none;margin-bottom:20px}
        .create-account-btn:hover{background-color:#218838}
        table{width:100%;border-collapse:collapse;margin-bottom:20px}
        .table-search{margin:1rem 0; text-align:right;}
        .table-search input{padding:.5rem .75rem; width:250px; border:1px solid #ccc; border-radius:4px;}
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
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>
    <h1>Manage Accounts</h1>
 
    <a href="javascript:void(0)" class="create-account-btn" onclick="openModal('createAccountModal')">
        <i class="fas fa-user-plus"></i> Create New Account
    </a>
    <a href="archived_accounts.php" class="create-account-btn" style="background-color: #6c757d;">
            <i class="fas fa-archive"></i> Archived Accounts
        </a>
    <div class="table-search">
        <input type="text" id="accountSearch" placeholder="Search accounts..." />
    </div>
    <table id="accountsTable">
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
                echo "<tr onclick=\"openModal('userModal" . $row['id'] . "')\">";
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
                    <button type='submit' name='" . ($row['account_status'] === 'suspended' ? 'unsuspend' : 'suspend') . "' value='" . $row['id'] . "' class='action-btn " . ($row['account_status'] === 'suspended' ? 'unsuspend-btn' : 'suspend-btn') . "'>" . ($row['account_status'] === 'suspended' ? 'Unsuspend' : 'Suspend') . "</button>
                </form>
                <form action='' method='POST' style='display:inline-block;'>
                    <button type='submit' name='" . ($row['account_status'] === 'archived' ? 'unarchive' : 'archive') . "' value='" . $row['id'] . "' class='action-btn " . ($row['account_status'] === 'archived' ? 'unarchive-btn' : 'delete-btn') . "'>" . ($row['account_status'] === 'archived' ? 'Unarchive' : 'Archive') . "</button>
                </form>
              </td>";
                echo "</tr>";
                echo "<div id='userModal" . $row['id'] . "' class='modal'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h2>User Information</h2>
                                <span onclick=\"closeModal('userModal" . $row['id'] . "')\">&times;</span>
                            </div>
                            <p><strong>First Name:</strong> " . $row['first_name'] . "</p>
                            <p><strong>Middle Name:</strong> " . $row['middle_name'] . "</p>
                            <p><strong>Last Name:</strong> " . $row['last_name'] . "</p>
                            <p><strong>Place of Birth:</strong> " . $row['place_of_birth'] . "</p>
                            <p><strong>Birthdate:</strong> " . $row['birthdate'] . "</p>
                            <p><strong>Gender:</strong> " . $row['gender'] . "</p>
                            <p><strong>Status:</strong> " . $row['status'] . "</p>
                            <p><strong>Contact Number:</strong> " . $row['contact_number'] . "</p>
                            <p><strong>Father's First Name:</strong> " . $row['father_first_name'] . "</p>
                            <p><strong>Father's Last Name:</strong> " . $row['father_last_name'] . "</p>
                            <p><strong>Father's Occupation:</strong> " . $row['father_occupation'] . "</p>
                            <p><strong>Mother's First Name:</strong> " . $row['mother_first_name'] . "</p>
                            <p><strong>Mother's Last Name:</strong> " . $row['mother_last_name'] . "</p>
                            <p><strong>Mother's Occupation:</strong> " . $row['mother_occupation'] . "</p>
                            <p><strong>Email:</strong> " . $row['email'] . "</p>
                            <p><strong>Role:</strong> " . $row['role'] . "</p>
                            <p><strong>Username:</strong> " . $row['username'] . "</p>
                            <p><strong>Created At:</strong> " . $row['created_at'] . "</p>
                            <p><strong>Block Street:</strong> " . $row['blk_street'] . "</p>
                            <p><strong>Barangay:</strong> " . $row['barangay'] . "</p>
                            <p><strong>City:</strong> " . $row['city'] . "</p>
                            <p><strong>Province:</strong> " . $row['province'] . "</p>
                            <p><strong>Region:</strong> " . $row['region'] . "</p>
                            <p><strong>Account Status:</strong> " . ucfirst($row['account_status']) . "</p>
                        </div>
                      </div>";
            }
        } else {
            echo "<tr><td colspan='4'>No users found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
function filterAccounts() {
    const query = document.getElementById('accountSearch').value.toLowerCase();
    const table = document.getElementById('accountsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for (let row of rows) {
        const cells = row.getElementsByTagName('td');
        let text = '';
        // search username, email, and role columns
        if (cells.length >= 3) {
            text = (cells[0].textContent + ' ' + cells[1].textContent + ' ' + cells[2].textContent).toLowerCase();
        }
        row.style.display = text.indexOf(query) > -1 ? '' : 'none';
    }
}
document.getElementById('accountSearch').addEventListener('keyup', filterAccounts);
</script>

<div id="createAccountModal" class="modal">
    <div class="modal-content create-account-modal-content">
        <div class="modal-header">
            <h2>Create New Account</h2>
            <span onclick="closeModal('createAccountModal')">&times;</span>
        </div>
        <form class="create-account-form" action="" method="POST">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required>
            <label for="middle_name">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required>
            <label for="place_of_birth">Place of Birth</label>
            <input type="text" id="place_of_birth" name="place_of_birth" required>
            <label for="birthdate">Birthdate</label>
            <input type="date" id="birthdate" name="birthdate" required>
            <label for="gender">Gender</label>
            <input type="text" id="gender" name="gender" required>
            <label for="status">Status</label>
            <input type="text" id="status" name="status" required>
            <label for="contact_number">Contact Number</label>
            <input type="text" id="contact_number" name="contact_number" required maxlength="11" oninput="this.value = this.value.replace(/\D/g, '')">
            <label for="father_first_name">Father's First Name</label>
            <input type="text" id="father_first_name" name="father_first_name">
            <label for="father_last_name">Father's Last Name</label>
            <input type="text" id="father_last_name" name="father_last_name">
            <label for="father_occupation">Father's Occupation</label>
            <input type="text" id="father_occupation" name="father_occupation">
            <label for="mother_first_name">Mother's First Name</label>
            <input type="text" id="mother_first_name" name="mother_first_name">
            <label for="mother_last_name">Mother's Last Name</label>
            <input type="text" id="mother_last_name" name="mother_last_name">
            <label for="mother_occupation">Mother's Occupation</label>
            <input type="text" id="mother_occupation" name="mother_occupation">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="">Select role</option>
                <option value="user">User</option>
                <option value="staff">Staff</option>
            </select>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <label for="blk_street">Block Street</label>
            <input type="text" id="blk_street" name="blk_street" required>
            <label for="barangay">Barangay</label>
            <input type="text" id="barangay" name="barangay" required>
            <label for="city">City</label>
            <input type="text" id="city" name="city" required>
            <label for="province">Province</label>
            <input type="text" id="province" name="province" required>
            <label for="region">Region</label>
            <input type="text" id="region" name="region" required>
            <button type="submit" name="create_account">Create Account</button>
        </form>
    </div>
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
