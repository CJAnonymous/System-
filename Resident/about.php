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
    <title>About Barangay Poblacion 1 </title>
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
            overflow-y: auto;
            flex-grow: 1;
            padding: 30px 40px;
            margin-left: 250px;
            background-color: #f9fafb;
            transition: margin-left 0.3s ease;
        }

        .main-content h1 {
            font-size: 28px;
            color: #003566;
            text-align: center;
            margin-bottom: 20px;
        }

        .about-section img {
            max-width: 80%;
            height: auto;
            margin: 20px auto;
            display: block;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .about-section p {
            font-size: 18px;
            color: #333;
            line-height: 1.6;
            text-align: justify;
            margin: 20px auto;
            max-width: 800px;
        }

        .sidebar a i {
            font-size: 20px;
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

    .stat-box {
        width: 100%;
    }

    .chart-container {
        max-width: 100%;
        padding: 0 20px;
    }
}

/* Table Styles */
.officials-table {
    margin: 40px auto;
    max-width: 800px;
    overflow-x: auto;
}

.officials-table h2 {
    color: #003566;
    text-align: center;
    margin-bottom: 25px;
    font-size: 24px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #003566;
    color: white;
    font-weight: bold;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}

td {
    color: #333;
}

td:first-child {
    font-weight: 500;
    color: #003566;
}

@media (max-width: 768px) {
    .officials-table {
        margin: 20px 10px;
    }
    
    th, td {
        padding: 12px;
        font-size: 14px;
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
    <h1><br>About Barangay Poblacion 1</h1>
    <div class="officials-table">
            <h2>Barangay Officials</h2>
            <table>
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PUNONG BARANGAY</td>
                        <td>DJ KHALEED</td>
                    </tr>
                    <tr>
                        <td rowspan="7">KAGAWAD</td>
                        <td>ARIANA GRANDE</td>
                    </tr>
                    <tr><td>ALFONSO S MANLUCTAO</td></tr>
                    <tr><td>NELSON C ALACANTARA</td></tr>
                    <tr><td>ARNOLD S FRANCISCO</td></tr>
                    <tr><td>BIENVENIDO DG SANTOS</td></tr>
                    <tr><td>RAMIL T BORRE</td></tr>
                    <tr><td>ROGELIO A TERNIDA</td></tr>
                    <tr>
                        <td>SK CHAIRMAN</td>
                        <td>CHRISTIAN </td>
                    </tr>
                    <tr>
                        <td>SECRETARY</td>
                        <td>UWA</td>
                    </tr>
                    <tr>
                        <td>TREASURER</td>
                        <td>VHONG REVILLA</td>
                    </tr>
                </tbody>
            </table>
        </div>
    <div class="about-section">
        <img src="okay.jpg" alt="Barangay Poblacion 1">
        <p>
            Barangay Poblacion 1 is a small, close-knit community known for its warm hospitality and vibrant local culture. <br>
            Enjoy the convenience of nearby schools, all within a safe and secure environment. With well-maintained parks and active community engagement, <br>Barangay Poblacion 1 offers a high quality of life in a tranquil setting. Join us and experience the perfect blend of tradition and modern living. Welcome home!
        </p>
        
          <!-- Officials Table -->
          
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
