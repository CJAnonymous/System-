<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['title']) && isset($_POST['content'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $user_id = $_SESSION['user_id'];
        $image_path = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_ext, $allowed_exts)) {
            $upload_dir = __DIR__ . '/uploads/announcements/';
            
            // Create directory if not exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $new_image_name = uniqid('', true) . '.' . $image_ext;
            $image_path = 'uploads/announcements/' . $new_image_name; // path for DB

            if (!move_uploaded_file($image_tmp_name, $upload_dir . $new_image_name)) {
                echo "<script>alert('Failed to upload image.');</script>";
                exit();
            }
        } else {
            echo "<script>alert('Invalid image file type.');</script>";
            exit();
        }
    }

        $conn = new mysqli('localhost', 'root', '', 'barangay');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if (!empty($title) && !empty($content)) {
            $sql = "INSERT INTO announcements (title, content, image_path, created_by) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $title, $content, $image_path, $user_id);

            if ($stmt->execute()) {
                echo "<script>alert('Announcement created successfully!');</script>";
            } else {
                echo "<script>alert('Failed to create announcement.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Title and Content are required.');</script>";
        }
        $conn->close();
    }
}

if (isset($_POST['delete_announcement']) && isset($_POST['announcement_id'])) {
    $announcement_id = $_POST['announcement_id'];

    $conn = new mysqli('localhost', 'root', '', 'barangay');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "DELETE FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $announcement_id);
    if ($stmt->execute()) {
        $stmt->close();

        $sql = "SELECT image_path FROM announcements WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $announcement_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $announcement = $result->fetch_assoc();
        
        if ($announcement && $announcement['image_path']) {
            $image_path = $announcement['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);  
            }
        }

        echo "<script>alert('Announcement deleted successfully!');</script>";
    } else {
        echo "<script>alert('Failed to delete the announcement.');</script>";
    }

    $stmt->close();
    $conn->close();
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
            $adminImagePath = $row['image_path'];
            $staffImagePath = '../Staff/' . $row['image_path'];
            if (file_exists($adminImagePath)) {
                $row['image_path'] = $adminImagePath;
            } elseif (file_exists($staffImagePath)) {
                $row['image_path'] = $staffImagePath;
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
    <title>Create Announcement</title>
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
    background-color: #f7f7f7;
    display: flex;
    height: 100vh;
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
    margin-bottom: 20px;
}

.form-container {
    background-color: #ffffff;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    margin-bottom: 30px;
    width: 100%;
}

.form-container input,
.form-container textarea,
.form-container button {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ddd;
    box-sizing: border-box;
}

.form-container button {
    background-color: #003566;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s ease;
}

.form-container button:hover {
    background-color: #00214d;
}

.form-container label {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 10px;
}

.announcement-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
    width: 100%;
}

.announcement-card {
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    width: 300px;
    text-align: center;
}

.announcement-card:hover {
    transform: translateY(-5px);
}

.announcement-card img {
    max-width: 100%;
    border-radius: 8px;
    margin-top: 15px;
}

.announcement-card h3 {
    font-size: 20px;
    color: #003566;
    margin-bottom: 10px;
}

.announcement-card p {
    font-size: 16px;
    color: #555;
    line-height: 1.6;
}

.announcement-card small {
    color: #aaa;
    font-size: 14px;
}

.announcement-list h2 {
    text-align: center;
    width: 100%;
    font-size: 24px;
    color: #003566;
    margin-bottom: 20px;
}

.delete-btn {
    background-color: red;
    color: white;
    font-size: 16px;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
}

.delete-btn:hover {
    background-color: #a71d2a;
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
<br>
    <br>
    <div class="form-container">
        <h1>Create Announcement</h1>
        <form action="announcement.php" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="6" required></textarea>

            <label for="image">Attach Image (optional):</label>
            <input type="file" id="image" name="image" accept="image/*">

            <button type="submit">Create Announcement</button>
        </form>
    </div>

    <div class="announcement-list">
        <h2>Recent Announcements</h2>
        <?php if (count($announcements) > 0): ?>
            <?php foreach ($announcements as $announcement): ?>
                <div class="announcement-card">
                    <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                    <?php if ($announcement['image_path']): ?>
                        <img src="<?php echo $announcement['image_path']; ?>" alt="Announcement Image">
                    <?php endif; ?>
                    <p><small>Created on: <?php echo date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?></small></p>
                    
                    <form action="announcement.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                        <input type="hidden" name="announcement_id" value="<?php echo $announcement['id']; ?>">
                        <button type="submit" name="delete_announcement" class="delete-btn">Delete</button>
                    </form>
                </div>
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
</script>

</body>
</html>
