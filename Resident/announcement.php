<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'barangay');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);


$announcements = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['image_path']) {
            $adminImagePath = '../Admin/' . $row['image_path'];
            $staffImagePath = '../Staff/' . $row['image_path'];
            if (file_exists($staffImagePath)) {
                $row['image_path'] = $staffImagePath;
            } elseif (file_exists($adminImagePath)) {
                $row['image_path'] = $adminImagePath;
            }
        }
        $announcements[] = $row;
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Announcements</title>
    <style>
        
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
            display: flex;
            height: 100vh;
            background-color: #f4f4f4;
            color: #333;
            justify-content: center;
            align-items: flex-start;
        }

        .sidebar {
            width: 250px;
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

        .main-content {
            flex-grow: 1;
            padding: 30px 40px;
            margin-left: 250px;
            background-color: #f9fafb;
            transition: margin-left 0.3s ease;
        }

        .main-content h1 {
            text-align: center;
            font-size: 36px;
            color: #003566;
            margin-bottom: 20px;
        }

        .announcement-section {
        display: flex;
        flex-direction: column; 
        gap: 20px;
        margin-top: 20px;
        justify-items: center;
        padding: 0 20px;
        width: 100%;
        max-width: 700px;
        margin: 0 auto;
}

        @media (min-width: 600px) {
            .announcement-section {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 900px) {
            .announcement-section {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .announcement-card {
        background: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        width: 100%;
        max-width: 550px; 
        text-decoration: none;
        color: inherit;
        display: block; 
        margin-bottom: 20px; 
}

        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .announcement-card img {
    width: 100%; 
    height: auto; 
    border-radius: 10px;
    object-fit: fill; 
    max-height: 450px; 
    margin-bottom: 20px; 
}

        .announcement-card h3 {
            font-size: 24px;
            color: #003566;
            margin-bottom: 10px;
        }

        .announcement-card p {
            font-size: 16px;
         color: #555;
    line-height: 1.6;
    margin-bottom: 15px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
        }

        .announcement-card small {
            color: #aaa;
            font-size: 14px;
        }

        @media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    .announcement-card {
        height: auto; 
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

    <div id="announcement" class="main-content" onclick="closeSidebar()">

        <h1>Recent Announcements</h1>

        <div class="announcement-section">
            <?php if (count($announcements) > 0): ?>
                <?php foreach ($announcements as $announcement): ?>
                    <a href="view_announcement.php?id=<?php echo $announcement['id']; ?>" class="announcement-card">
                        <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                        <?php if ($announcement['image_path']): ?>
                            <img src="../Staff/<?php echo $announcement['image_path']; ?>" alt="Announcement Image">
                        <?php endif; ?>
                        <p><small>Created on: <?php echo date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?></small></p>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No announcements available.</p>
            <?php endif; ?>
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
document.body.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const hamburger = document.querySelector('.hamburger');
            if (sidebar.classList.contains('active') && !event.target.closest('.sidebar') && !event.target.closest('.hamburger')) {
                sidebar.classList.remove('active');
                hamburger.classList.remove('open');
            }
        });
</script>
</script>
</body>
</html>
