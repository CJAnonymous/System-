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

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$user_role = strtolower($user['role']);

if ($user_role == 'admin') {
    $role_display = 'Admin';
} elseif ($user_role == 'staff') {
    $role_display = 'Staff';
} else {
    $role_display = 'Resident';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
            overflow-x: hidden;
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

        .sidebar a.active {
            background-color: #007bff; /* Highlight color */
            color: white; /* Text color */
            font-weight: bold; /* Optional: Makes it stand out more */
            border-radius: 5px;
            padding: 10px;
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
            text-align: center;
            font-size: 32px;
            color: #1e3a8a;
            margin-bottom: 20px;
        }

        .main-content h1 {
            font-size: 32px;
            color: #003566;
            margin-bottom: 20px;
        }

        .profile-section {
            padding: 30px;
            border-radius: 10px;
        }

        .profile-section h1 {
            color: #003566;
            margin-bottom: 20px;
        }

        .welcome-message {
            font-size: 22px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }

        .profile-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

        .profile-info div {
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-info div span {
            font-weight: bold;
            color: #003566;
        }

        .profile-info div p {
            font-size: 16px;
            color: #333;
        }

        .sidebar a i {
            font-size: 20px;
        }
        
        .hamburger {
    display: block;
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

            .hamburger {
                display: block;
            }
            
        }

        @media (max-width: 768px) {
    .main-content {
        padding: 20px;
    }
}
@media (min-width: 769px) {
    .profile-section {
        background-color: #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
}
.hamburger.open div {
    background-color: white; 
}


.hamburger.open div:nth-child(2) {
    opacity: 100;
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
        <div class="profile-section">
            <h1>Profile Overview</h1>
            <p class="welcome-message">Welcome, <?php echo $role_display . ' ' . $user['first_name']; ?>!</p>

            <div class="profile-info">
                <div>
                    <span>Name:</span>
                    <p><?php echo $user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']; ?></p>
                </div>
                <div>
                    <span>Email:</span>
                    <p><?php echo $user['email']; ?></p>
                </div>
                <div>
                    <span>Contact Number:</span>
                    <p><?php echo $user['contact_number']; ?></p>
                </div>
                <div>
                    <span>Place of Birth:</span>
                    <p><?php echo $user['place_of_birth']; ?></p>
                </div>
                <div>
                    <span>Birthdate:</span>
                    <p><?php echo $user['birthdate']; ?></p>
                </div>
                <div>
                    <span>Gender:</span>
                    <p><?php echo $user['gender']; ?></p>
                </div>
                <div>
                    <span>Status:</span>
                    <p><?php echo $user['status']; ?></p>
                </div>
                <div>
                    <span>Address:</span>
                    <p><?php echo $user['blk_street'] . ', ' . $user['barangay'] . ', ' . $user['city'] . ', ' . $user['province'] . ', ' . $user['region']; ?></p>
                </div>
                <div>
                    <span>Father's Name:</span>
                    <p><?php echo $user['father_first_name'] . ' ' . $user['father_last_name']; ?></p>
                </div>
                <div>
                    <span>Father's Occupation:</span>
                    <p><?php echo $user['father_occupation']; ?></p>
                </div>
                <div>
                    <span>Mother's Name:</span>
                    <p><?php echo $user['mother_first_name'] . ' ' . $user['mother_last_name']; ?></p>
                </div>
                <div>
                    <span>Mother's Occupation:</span>
                    <p><?php echo $user['mother_occupation']; ?></p>
                </div>
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
