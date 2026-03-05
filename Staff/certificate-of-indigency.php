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

$sql = "SELECT * FROM certificate_of_indigency 
        WHERE approval_status IN ('pending', 'approved','pickup scheduled')
        $date_condition
        LIMIT $start, $limit";$result = $conn->query($sql);

$count_sql = "SELECT COUNT(*) FROM certificate_of_indigency 
              WHERE approval_status IN ('pending', 'approved','pickup scheduled')
              $date_condition";$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);
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
            .hamburger {
                display: block;
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
    margin-bottom: 20px;
}

.filter-btn {
    background-color: #007bff !important;
    color: white !important;
    padding: 8px 15px !important;
    transition: background-color 0.3s ease;
    border-radius: 4px;
}

.filter-btn:hover {
    background-color: #0056b3 !important;
}

.clear-btn {
    background-color: #6c757d !important;
    color: white !important;
    padding: 8px 15px !important;
    transition: background-color 0.3s ease;
    border-radius: 4px;
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
    <h1>Pending and Processing Barangay Indigency Applications</h1>
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
                <th>Purpose</th>
                <th>Valid ID</th>
                <th>Status</th>
                <th>Submitted on</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['reference_number']; ?></td>
                        <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td><?php echo $row['purpose']; ?></td>
                        <td>
    <img src="../Resident/<?php echo $row['valid_id_path']; ?>" 
         alt="Valid ID" width="100"
         onclick="openImageModal('../Resident/<?php echo $row['valid_id_path']; ?>')"
         style="cursor: pointer;">
</td>
<td>
    <?php 
    $status = strtolower(trim($row['approval_status']));
    echo ucfirst($status); 
    
    if ($status === 'rejected' && !empty($row['reject_reason'])): 
    ?>
        <div class="rejection-details" style="margin-top: 8px; padding: 8px; background: #ffe6e6; border-radius: 4px;">
            <div class="rejection-reason" style="color: #cc0000; font-size: 0.9em;">
                <?php echo htmlspecialchars($row['reject_reason']); ?>
            </div>
            <?php if (!empty($row['rejected_at'])): ?>
                <div class="rejection-date" style="color: #666; font-size: 0.85em; margin-top: 4px;">
                    <?php echo date('M j, Y h:i A', strtotime($row['rejected_at'])); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</td>
                        <td><?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?> </td> <!-- Display Submitted on date -->

                        <td>
    <?php if ($row['approval_status'] === 'pending'): ?>
        <button type="button" class="btn approve-btn" onclick="approveApplication(<?php echo $row['id']; ?>)">Approve</button>
        <button type="button" class="btn reject-btn" onclick="openRejectModal(<?php echo $row['id']; ?>)">Reject</button>
    <?php elseif ($row['approval_status'] === 'approved' && (empty($row['pickup_date']) || $row['pickup_date'] === '0000-00-00')): ?>
        <button type="button" class="btn approve-btn" onclick="openScheduleModal(<?php echo $row['id']; ?>)">Schedule Pickup</button>
    <?php elseif ($row['approval_status'] === 'pickup scheduled'): ?>
        <button type="button" class="btn approve-btn" onclick="openPickupModal(<?php echo $row['id']; ?>)">Picked Up</button>
            <?php endif; ?>
</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No applications available.</td>
                </tr>
            <?php endif; ?>
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
<div id="imageModal" class="lightbox">
    <span class="close-button" onclick="closeImageModal()">&times;</span>
    <img id="modalImage" src="" alt="Valid ID">
</div>
<div id="rejectModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:20px; border-radius:5px; box-shadow:0 0 20px rgba(0,0,0,0.2); z-index:1000;">
    <h3>Reject Application</h3>
    <form id="rejectForm" action="approve_rejectindigency.php" method="POST">
        <input type="hidden" id="rejectApplicationId" name="id">
        <label for="rejectReason">Reason for Rejection:</label>
        <textarea id="rejectReason" name="reject_reason" rows="4" required style="width: 100%; margin: 10px 0; padding: 8px;"></textarea>
        <div style="text-align: right;">
            <button type="button" onclick="closeRejectModal()" class="btn" style="background: #6c757d; margin-right: 5px;">Cancel</button>
            <button type="submit" name="action" value="reject" class="btn reject-btn">Submit Rejection</button>
        </div>
    </form>
</div>
<div id="scheduleModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; border-radius:5px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
    <h3>Schedule Pickup</h3>
    <form id="scheduleForm">
        <input type="hidden" id="applicationId" name="id">
        <label for="pickupDate">Pickup Date:</label>
        <input type="date" id="pickupDate" name="pickup_date" required>
        <button type="button" onclick="submitPickupDate()">Submit</button>
        <button type="button" onclick="closeScheduleModal()">Cancel</button>
    </form>
</div>

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

function openPickupModal(id) {
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

    fetch('approve_rejectindigency.php', {
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

    function approveApplication(id) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'approve_rejectindigency.php';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'approve';
        
        form.appendChild(idInput);
        form.appendChild(actionInput);
        document.body.appendChild(form);
        form.submit();
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
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').style.display = 'flex';
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
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
        alert("Please select both pickup date and time.");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "approve_rejectindigency.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert("Pickup date scheduled successfully!");
            location.reload();
        }
    };
    xhr.send(`id=${id}&action=schedule_pickup&pickup_date=${pickupDate}`);
}
</script>
</body>
</html>

<?php
$conn->close();
?>
