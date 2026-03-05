<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$firstName = $_SESSION['first_name']; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;900&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #003566;
      font-family: 'Poppins', sans-serif;
      overflow: hidden;
    }

    .welcome-container {
      text-align: center;
      color: white;
    }

    .logo {
      max-width: 300px;
      margin-bottom: 20px;
      opacity: 0;
      animation: fadeIn 2s ease-in forwards;
    }

    .welcome-message {
      font-size: 6em;
      font-weight: 900;
      margin-bottom: 20px;
      text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
      text-transform: uppercase;
      opacity: 0;
      animation: fadeIn 3s ease-in 3.5s forwards;
    }

    .resident-message {
      font-size: 4.5em;
      font-weight: 900;
      margin-top: 10px;
      text-transform: uppercase;
      opacity: 0;
      animation: fadeIn 3s ease-in 7s forwards;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
  <div class="welcome-container">
    <img src="logo.png" alt="Logo" class="logo">
    
    <div class="welcome-message">WELCOME</div>
    
    <div class="resident-message">
      <?php echo "RESIDENT, " . strtoupper(htmlspecialchars($firstName)); ?>
    </div>
  </div>

  <script>
    setTimeout(function() {
      window.location.href = "dashboard.php";
    }, 12000);
  </script>
</body>
</html>
