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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $father_first_name = $_POST['father_first_name'];
    $father_last_name = $_POST['father_last_name'];
    $father_occupation = $_POST['father_occupation'];
    $mother_first_name = $_POST['mother_first_name'];
    $mother_last_name = $_POST['mother_last_name'];
    $mother_occupation = $_POST['mother_occupation'];

    $update_sql = "UPDATE users SET 
        father_first_name = ?, 
        father_last_name = ?, 
        father_occupation = ?, 
        mother_first_name = ?, 
        mother_last_name = ?, 
        mother_occupation = ? 
        WHERE id = ?";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssi", $father_first_name, $father_last_name, $father_occupation, $mother_first_name, $mother_last_name, $mother_occupation, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$user_role = strtolower($user['role']) == 'staff' ? 'Staff' : 'Resident';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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

        .sidebar a i {
            font-size: 20px;
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
        .profile-section {
            padding: 30px;
            border-radius: 10px;
        }
        .profile-section h1 {
            color: #003566;
            margin-bottom: 20px;
            text-align: center;
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
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .profile-info div {
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .profile-info div span {
            font-weight: bold;
            color: #003566;
        }
        .profile-info div input {
            width: calc(100% - 40px);
            padding: 5px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: none;
        }
        .profile-info div i {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #007bff;
            cursor: pointer;
        }
        .profile-info div p, .profile-info div input {
            font-size: 16px;
            color: #333;
        }
        .editable i {
            display: block;
        }
        .save-btn {
            grid-column: span 2;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            display: none;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
    .profile-info {
        grid-template-columns: 1fr;
    }

    .save-btn {
        grid-column: span 1;
    }
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
            <h1><br>Profile Overview</h1>
            <p class="welcome-message">Welcome, <?php echo $user_role . ' ' . $user['first_name']; ?>!</p>

            <form method="post" class="profile-info" id="profileForm">
                <div>
                    <span>First Name:</span>
                    <p><?php echo $user['first_name']; ?></p>
                </div>
                <div>
                    <span>Middle Name:</span>
                    <p><?php echo $user['middle_name']; ?></p>
                </div>
                <div>
                    <span>Last Name:</span>
                    <p><?php echo $user['last_name']; ?></p>
                </div>
                <div>
                    <span>Blk & Street:</span>
                    <p><?php echo $user['blk_street']; ?></p>
                </div>
                <div>
                    <span>Region:</span>
                    <p><?php echo $user['region']; ?></p>
                </div>
                <div>
                    <span>Province:</span>
                    <p><?php echo $user['province']; ?></p>
                </div>
                <div>
                    <span>City:</span>
                    <p><?php echo $user['city']; ?></p>
                </div>
                <div>
                    <span>Barangay:</span>
                    <p><?php echo $user['barangay']; ?></p>
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
                    <span>Contact Number:</span>
                    <p><?php echo $user['contact_number']; ?></p>
                </div>
                <div>
                    <span>Email:</span>
                    <p><?php echo $user['email']; ?></p>
                </div>
                <div>
                    <span>Role:</span>
                    <p><?php echo $user_role; ?></p>
                </div>
                <div class="editable">
                    <span>Father's Name:</span>
                    <p><?php echo $user['father_first_name'] . ' ' . $user['father_last_name']; ?></p>
                    <input type="text" name="father_first_name" value="<?php echo $user['father_first_name']; ?>" required>
                    <input type="text" name="father_last_name" value="<?php echo $user['father_last_name']; ?>" required>
                    <i class="fas fa-edit"></i>
                </div>
                <div class="editable">
                    <span>Father's Occupation:</span>
                    <p><?php echo $user['father_occupation']; ?></p>
                    <input type="text" name="father_occupation" value="<?php echo $user['father_occupation']; ?>" required>
                    <i class="fas fa-edit"></i>
                </div>
                <div class="editable">
                    <span>Mother's Name:</span>
                    <p><?php echo $user['mother_first_name'] . ' ' . $user['mother_last_name']; ?></p>
                    <input type="text" name="mother_first_name" value="<?php echo $user['mother_first_name']; ?>" required>
                    <input type="text" name="mother_last_name" value="<?php echo $user['mother_last_name']; ?>" required>
                    <i class="fas fa-edit"></i>
                </div>
                <div class="editable">
                    <span>Mother's Occupation:</span>
                    <p><?php echo $user['mother_occupation']; ?></p>
                    <input type="text" name="mother_occupation" value="<?php echo $user['mother_occupation']; ?>" required>
                    <i class="fas fa-edit"></i>
                </div>
                <button type="submit" class="save-btn">Save Changes</button>
            </form>
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
        document.querySelectorAll('.editable').forEach(div => {
            const editIcon = div.querySelector('i');
            const inputs = div.querySelectorAll('input');
            const p = div.querySelector('p');
            const saveBtn = document.querySelector('.save-btn');

            editIcon.addEventListener('click', function () {
                inputs.forEach(input => input.style.display = 'block');
                p.style.display = 'none';
                saveBtn.style.display = 'block';
            });
        });
    </script>

</body>
</html>

<?php
$conn->close();
?>
