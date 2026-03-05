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
    <title>Contact Barangay Poblacion 1</title>
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
            width: 250px;
            background-color: #003566;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
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
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .main-content h1 {
            font-size: 28px;
            color: #003566;
            text-align: center;
            margin-bottom: 20px;
        }

        .contact-info {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .contact-info h2 {
            text-align: center;
            font-size: 24px;
            color: #003566;
            margin-bottom: 20px;
        }

        .contact-info p {
            font-size: 18px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .contact-info i {
            font-size: 24px;
            color: #003566;
        }

        .contact-info a {
            color: #007BFF;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1>Contact Us</h1>
        <div class="contact-info">
            <p>We value your feedback and inquiries. Reach out to Barangay Poblacion 1 for any questions, suggestions, or assistance.</p>
            <p>
                <i class="fab fa-facebook"></i>
                <strong>Facebook Page:</strong> 
                <a href="https://www.facebook.com/" target="_blank">Barangay Poblacion 1 Official Group Page</a>
            </p>
            <p>
                <i class="fas fa-envelope"></i>
                <strong>Email:</strong> barangaypoblacion@gmail.com
            </p>
            <p>
                <i class="fas fa-phone"></i>
                <strong>Contact:</strong> 09311111019
            </p>
            <p>
                <i class="fas fa-phone-square-alt"></i>
                <strong>Tel:</strong> 77777777
            </p>
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
