<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Invalid announcement ID.";
    exit();
}

$announcement_id = $_GET['id'];

$conn = new mysqli('localhost', 'root', '', 'barangay');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM announcements WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $announcement_id);
$stmt->execute();
$result = $stmt->get_result();
$announcement = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$announcement) {
    echo "Announcement not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Announcement</title>
    <style>
        .sidebar a.active {
    background-color: #007bff; /* Highlight color */
    color: white; /* Text color */
    font-weight: bold; /* Optional: Makes it stand out more */
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
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .announcement-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
        }

        .announcement-container h1 {
            font-size: 30px;
            color: #003566;
            margin-bottom: 15px;
            text-align: center;
        }

        .announcement-container small {
            display: block;
            text-align: center;
            color: #aaa;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .announcement-image {
            display: block;
            max-width: 100%;
            border-radius: 8px;
            margin: 20px auto;
        }

        .announcement-content {
            font-size: 16px;
            color: #555;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #003566;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .back-link:hover {
            background-color: #00509e;
        }

        @media (max-width: 768px) {
            .announcement-container {
                padding: 20px;
            }

            .announcement-container h1 {
                font-size: 24px;
            }

            .back-link {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="announcement-container">
        <h1><?php echo htmlspecialchars($announcement['title']); ?></h1>
        <small>Created on: <?php echo date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?></small>
        <?php if (!empty($announcement['image_path'])): ?>
            <img src="../Staff/<?php echo $announcement['image_path']; ?>" alt="Announcement Image" class="announcement-image" />
        <?php endif; ?>
        <div class="announcement-content">
            <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
        </div>
        <a href="announcement.php" class="back-link">&larr; Back to Announcements</a>
    </div>
</body>
</html>
