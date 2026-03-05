<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Poblacion 1</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        .logo-container {
            opacity: 0;
            animation: breathe 20s ease-in-out infinite, redirect 20s forwards;
        }

        @keyframes breathe {
            100%, 50% {
                opacity: 2;
            }
            50% {
                opacity: 0.3;
            }
        }

        @keyframes redirect {
            100% {
                opacity: 1;
            }
        }

        .logo {
            width: 500px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="logo.png" alt="Logo" class="logo">
    </div>

    <script>
        setTimeout(() => {
            window.location.href = "login.php";
        }, 9000);
    </script>
</body>
</html>
