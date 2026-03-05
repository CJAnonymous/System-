<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Services</title>
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
        .main-content h1 {
            font-size: 36px;
            color: #003566;
            text-align: center;
            margin-bottom: 30px;
        }

        .request-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .request-menu a {
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            padding: 20px;
            font-size: 18px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 15px;Q
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .request-menu a:hover {
            background-color: #0056b3;
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .request-menu a i {
            font-size: 24px;
        }

        @media (max-width: 600px) {
            .request-menu {
                grid-template-columns: 1fr;
            }
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
    .hamburger {
        display: block;
    }
}

@media (max-width: 768px) {
    .statistics {
        justify-content: center;
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
    <h1><br>Request List</h1>
        <div class="request-menu">
            <a href="barangay-id.php"><i class="fas fa-id-card"></i> List Barangay ID</a>
            <a href="transaction_history.php"><i class="fas fa-clock"></i> Transaction History</a>
            <a href="barangay_clearance.php"><i class="fas fa-file-alt"></i> List Barangay Clearance</a>
            <a href="certificate-of-indigency.php"><i class="fas fa-certificate"></i> List Certificate of Indigency</a>
            <a href="certificate-of-residency.php"><i class="fas fa-home"></i> List Certificate of Residency</a>
            <a href="cedula.php"><i class="fas fa-file-contract"></i> Request Cedula</a>

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
