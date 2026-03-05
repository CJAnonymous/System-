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

$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';
$date_condition = $filter_date ? " AND DATE(created_at) = '$filter_date'" : '';
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$sql = "SELECT * FROM cedula_requests 
        WHERE approval_status IN ('pending', 'approved', 'pickup scheduled')
        $date_condition
        LIMIT $start, $limit";
$result = $conn->query($sql);

$count_sql = "SELECT COUNT(*) FROM cedula_requests 
              WHERE approval_status IN ('pending', 'approved', 'pickup scheduled')
              $date_condition";
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cedula Applications</title>
    <style>
        .sidebar a.active {
    background-color: #007bff; 
    color: white;
    font-weight: bold; 
    border-radius: 5px;
    padding: 10px;
}
#scheduleModal, #rejectModal {
    z-index: 1001;
}

.lightbox {
    z-index: 1002;
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
            width: 270px;
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
        .cancel-btn{
            background-color: gray;
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
    .main-content {
        padding: 20px;
    }
}


@media (max-width: 768px) {
    .hamburger {
        display: block;
    }
}
.close-button {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 30px;
    color: white;
    cursor: pointer;
}
.filter-form {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-btn {
    background-color: #007bff !important;
    color: white !important;
    padding: 8px 15px !important;
    transition: background-color 0.3s ease;
}

.filter-btn:hover {
    background-color: #0056b3 !important;
}

.clear-btn {
    background-color: #6c757d !important;
    color: white !important;
    padding: 8px 15px !important;
    transition: background-color 0.3s ease;
}

.clear-btn:hover {
    background-color: #5a6268 !important;
}

.btn i {
    margin-right: 5px;
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
    <h1>Pending and Processing Cedula Applications</h1>
    <div class="filters" style="margin-bottom: 20px;">
    <form method="GET" action="" class="filter-form">
        <label style="margin-right: 10px; color: #003566;">Filter by Date: </label>
        <input type="date" name="filter_date" value="<?php echo $filter_date; ?>" 
               style="padding: 8px; border-radius: 4px; border: 1px solid #ddd; margin-right: 10px;">
               
        <button type="submit" class="btn filter-btn">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <?php if($filter_date): ?>
            <button type="button" class="btn clear-btn" onclick="window.location.href='?'">
                <i class="fas fa-times"></i> Clear
            </button>
        <?php endif; ?>
    </form>
</div>    
    <table>
        <thead>
            <tr>
                <th>Reference Number</th>
                <th>Name</th>
                <th>Address</th>
                <th>Payment Method</th>
                <th>GCash Reference</th>
                <th>Payment Proof</th>
                <th>Status</th>
                <th>Submitted on</th>
                <th>Pickup Date/Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['reference_number']; ?></td>
                        <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                        <td><?php echo $row['present_address']; ?></td>
                        <td><?php echo $row['payment_method']; ?></td>
                        <td><?php echo $row['gcash_reference'] ?? 'N/A'; ?></td>
                        <td>
                            <?php if (!empty($row['proof_of_payment_path'])): ?>
                                <img src="../Resident/<?php echo $row['proof_of_payment_path']; ?>" 
                                     alt="Payment Proof" width="100"
                                     onclick="openImageModal('../Resident/<?php echo $row['proof_of_payment_path']; ?>')"
                                     style="cursor: pointer;">
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $status = strtolower(trim($row['approval_status']));
                            echo ucfirst($status); 
                            
                            if ($status === 'rejected' && !empty($row['reject_reason'])): 
                            ?>
                                <div class="rejection-details">
                                    <?php echo htmlspecialchars($row['reject_reason']); ?>
                                    <?php if (!empty($row['rejected_at'])): ?>
                                        <div class="rejection-date">
                                            <?php echo date('M j, Y h:i A', strtotime($row['rejected_at'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?></td>
                        <td>
                            <?php if ($row['pickup_date'] && $row['pickup_date'] !== '0000-00-00'): ?>
                                <?php echo date('M j, Y', strtotime($row['pickup_date'])); ?>
                                <?php if (!empty($row['pickup_time'])): ?>
                                    <br>at <?php echo date('h:i A', strtotime($row['pickup_time'])); ?>
                                <?php endif; ?>
                            <?php else: ?>
                                Not scheduled
                            <?php endif; ?>
                        </td>
                        <td>
    <?php if (trim(strtolower($row['approval_status'])) === 'pending'): ?>
        <button class="btn approve-btn" onclick="approveApplication(<?php echo $row['id']; ?>)">Approve</button>
        <button class="btn reject-btn" onclick="openRejectModal(<?php echo $row['id']; ?>)">Reject</button>
    <?php elseif (trim(strtolower($row['approval_status'])) === 'approved' && ($row['pickup_date'] === '0000-00-00' || is_null($row['pickup_date']))): ?>
        <button class="btn approve-btn" onclick="openScheduleModal(<?php echo $row['id']; ?>)">Schedule Pickup</button>
    <?php elseif (trim(strtolower($row['approval_status'])) === 'pickup scheduled'): ?>
        <button class="btn approve-btn" onclick="markAsPickedUp(<?php echo $row['id']; ?>)">Picked Up</button>
    <?php endif; ?>
</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">No applications available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="scheduleModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; border-radius:5px; box-shadow:0 4px 8px rgba(0,0,0,0.1); z-index:1001;">
    <h3>Schedule Pickup</h3>
    <form id="scheduleForm">
        <input type="hidden" id="applicationId" name="id">
        <div style="margin-bottom: 15px;">
            <label for="pickupDate">Pickup Date:</label>
            <input type="date" id="pickupDate" name="pickup_date" required>
        </div>
        <button type="button" class="btn approve-btn" onclick="submitPickupSchedule()">Submit</button>
        <button type="button" class="btn cancel-btn" onclick="closeScheduleModal()">Cancel</button>
    </form>
</div>

<div id="rejectModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:20px; border-radius:5px; box-shadow:0 0 20px rgba(0,0,0,0.2); z-index:1000;">
    <h3>Reject Application</h3>
    <form id="rejectForm">
        <input type="hidden" id="rejectApplicationId" name="id">
        <label for="rejectReason">Reason for Rejection:</label>
        <textarea id="rejectReason" name="reject_reason" rows="4" required style="width: 100%; margin: 10px 0; padding: 8px;"></textarea>
        <div style="text-align: right;">
            <button type="button" onclick="closeRejectModal()" class="btn" style="background: #6c757d; margin-right: 5px;">Cancel</button>
            <button type="button" onclick="rejectApplication(document.getElementById('rejectApplicationId').value)" class="btn reject-btn">Submit Rejection</button>
        </div>
    </form>
</div>


<!-- Add this modal near other modals -->
<div id="pickupModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; border-radius:5px; box-shadow:0 4px 8px rgba(0,0,0,0.1); z-index:1001;">
    <h3>Confirm Pickup</h3>
    <form id="pickupForm">
        <input type="hidden" id="pickupApplicationId" name="id">
        <div style="margin-bottom: 15px;">
            <label>Picked Up By:</label><br>
            <label><input type="radio" name="picked_up_by" value="owner" checked onclick="toggleAuthorizedField()"> Document Owner</label>
            <label><input type="radio" name="picked_up_by" value="authorized" onclick="toggleAuthorizedField()"> Authorized Person</label>
        </div>
        <div id="authorizedField" style="margin-bottom:15px;display:none;">
            <label>Authorized Person Name:</label>
            <input type="text" id="authorizedName" name="authorized_person_name">
        </div>
        <button type="button" class="btn approve-btn" onclick="submitPickupConfirmation()">Confirm</button>
        <button type="button" class="btn cancel-btn" onclick="closePickupModal()">Cancel</button>
    </form>
</div>
<script>    
function markAsPickedUp(id) {
    document.getElementById('pickupApplicationId').value = id;
    document.getElementById('pickupModal').style.display = 'block';
}
function toggleAuthorizedField() {
    const authorizedField = document.getElementById('authorizedField');
    const isAuthorized = document.querySelector('input[name="picked_up_by"]:checked').value === 'authorized';
    authorizedField.style.display = isAuthorized ? 'block' : 'none';
    if (!isAuthorized) document.getElementById('authorizedName').value = '';
}

function submitPickupConfirmation() {
    const id = document.getElementById('pickupApplicationId').value;
    const pickedUpBy = document.querySelector('input[name="picked_up_by"]:checked').value;
    const authorizedName = document.getElementById('authorizedName').value;

    if (pickedUpBy === 'authorized' && !authorizedName) {
        alert("Please enter authorized person's name");
        return;
    }

    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', 'picked_up');
    formData.append('picked_up_by', pickedUpBy);
    if (authorizedName) formData.append('authorized_person_name', authorizedName);

    fetch('approve_reject_cedula.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            alert("Pickup confirmed successfully!");
            location.reload();
        } else {
            alert("Error confirming pickup");
        }
    })
    .catch(error => console.error('Error:', error));
}

function closePickupModal() {
    document.getElementById('pickupModal').style.display = 'none';
    document.getElementById('authorizedName').value = '';
    document.querySelector('input[name="picked_up_by"][value="owner"]').checked = true;
}


function submitPickupSchedule() {
    const id = document.getElementById('applicationId').value;
    const pickupDate = document.getElementById('pickupDate').value;

    if (!pickupDate) {
        alert("Please select a date.");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "approve_reject_cedula.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert("Pickup date scheduled successfully!");
            location.reload();
        }
    };
    xhr.send(`id=${id}&action=schedule_pickup&pickup_date=${pickupDate}`);
}


    function approveApplication(id) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "approve_reject_cedula.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (this.status === 200) {
                alert("Application approved successfully!");
                location.reload();
            } else {
                alert("Error approving application.");
            }
        };
        xhr.send(`id=${id}&action=approve`);
    }


function rejectApplication(id) {
    const rejectReason = document.getElementById('rejectReason').value;
    if (!rejectReason) {
        alert("Please provide a reason for rejection.");
        return;
    }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "approve_reject_cedula.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (this.status === 200) {
                alert("Application rejected successfully!");
                location.reload();
            } else {
                alert("Error rejecting application.");
            }
        };
        xhr.send(`id=${id}&action=reject&reject_reason=${encodeURIComponent(rejectReason)}`);
    }

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
function openRejectModal(id) {
    document.getElementById('rejectApplicationId').value = id;
    document.getElementById('rejectModal').style.display = 'block';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

window.onclick = function(event) {
    const rejectModal = document.getElementById('rejectModal');
    if (event.target === rejectModal) {
        closeRejectModal();
    }
}
    </script>
<script>
function openScheduleModal(id) {
    document.getElementById('applicationId').value = id;
    document.getElementById('scheduleModal').style.display = 'block';
}
function closeScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'none';
}
function submitPickupDate() {
    const id = document.getElementById('applicationId').value;
    const pickupDate = document.getElementById('pickupDate').value;
    if (!pickupDate) {
        alert("Please select a date.");
        return;
    }
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "approve_reject_cedula.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert("Pickup date scheduled successfully!");
            location.reload();
        }
    };
    xhr.send(`id=${id}&action=schedule_pickup&pickup_date=${pickupDate}`);
}

function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').style.display = 'flex';
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}

</script>
</body>
</html>