<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'barangay');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch latest 5 announcements
$sql = "SELECT id, title, content, image_path, created_at 
        FROM announcements 
        ORDER BY created_at DESC 
        LIMIT 5";

$result = $conn->query($sql);

$announcements = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Fix image path (Admin or Staff folder)
        if (!empty($row['image_path'])) {
            $adminPath = 'Admin/' . $row['image_path'];
            $staffPath = 'Staff/' . $row['image_path'];

            if (file_exists($staffPath)) {
                $row['image_path'] = $staffPath;
            } elseif (file_exists($adminPath)) {
                $row['image_path'] = $adminPath;
            } else {
                $row['image_path'] = null;
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
<title>Login Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    height: 100vh;
    background: url('building.png') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
}

.overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(to right, rgba(0,0,0,0.85) 55%, rgba(255,255,255,0.95) 100%);
    z-index: 1;
}

.content {
    position: relative;
    z-index: 2;
    display: flex;
    width: 100%;
    max-width: 1400px;
    height: 90vh;
}

/* LEFT SIDE */
.left-side {
    width: 50%;
    padding: 40px;
    overflow-y: auto;
    color: white;
}

.left-side h2 {
    margin-bottom: 25px;
    font-size: 28px;
}

.announcement-card {
    background: rgba(255,255,255,0.12);
    padding: 20px;
    margin-bottom: 25px;
    border-radius: 12px;
    backdrop-filter: blur(6px);
    transition: 0.3s ease;
}

.announcement-card:hover {
    transform: translateY(-5px);
}

.announcement-card img {
    width: 100%;
    border-radius: 10px;
    margin-bottom: 15px;
    max-height: 250px;
    object-fit: cover;
}

.announcement-card h3 {
    margin-bottom: 8px;
    font-size: 18px;
}

.announcement-card p {
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 8px;
}

.announcement-card small {
    font-size: 12px;
    color: #ddd;
}

/* RIGHT SIDE LOGIN */
.right-side {
    width: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-box {
    background: rgba(255,255,255,0.97);
    padding: 50px;
    border-radius: 15px;
    width: 100%;
    max-width: 450px;
    text-align: center;
}

.logo {
    width: 150px;
    margin-bottom: 20px;
}

.login-box h2 {
    margin-bottom: 25px;
    color: #333;
}

.input-group {
    margin-bottom: 20px;
}

.input-group input {
    width: 100%;
    padding: 12px;
    border: none;
    border-bottom: 2px solid #ccc;
    font-size: 16px;
    outline: none;
}

.input-group input:focus {
    border-bottom: 2px solid #007BFF;
}

.btn {
    width: 100%;
    padding: 14px;
    background-color: #007BFF;
    border: none;
    border-radius: 25px;
    color: white;
    font-size: 16px;
    cursor: pointer;
}

.btn:hover {
    background-color: #0056b3;
}

.register-link {
    display: block;
    margin-top: 12px;
    font-size: 14px;
    text-decoration: none;
    color: #007BFF;
}

.register-link:hover {
    color: #0056b3;
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .content {
        flex-direction: column;
        height: auto;
    }

    .left-side, .right-side {
        width: 100%;
    }

    .left-side {
        height: 350px;
    }
}
</style>
</head>
<body>

<div class="overlay"></div>

<div class="content">

    <!-- LEFT SIDE ANNOUNCEMENTS -->
    <div class="left-side">
        <h2>📢 Recent Announcements</h2>

        <?php if (!empty($announcements)): ?>
            <?php foreach ($announcements as $announcement): ?>
                <div class="announcement-card">

                    <?php if (!empty($announcement['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($announcement['image_path']); ?>" alt="Announcement Image">
                    <?php endif; ?>

                    <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>

                    <p>
                        <?php echo substr(htmlspecialchars($announcement['content']), 0, 150); ?>...
                    </p>

                    <small>
                        <?php echo date('F j, Y', strtotime($announcement['created_at'])); ?>
                    </small>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No announcements available.</p>
        <?php endif; ?>
    </div>

    <!-- RIGHT SIDE LOGIN -->
    <div class="right-side">
        <div class="login-box">
            <img src="logo.png" alt="Logo" class="logo">
            <h2>SIGN IN YOUR ACCOUNT HERE</h2>

            <form action="login_process.php" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit" class="btn">Login</button>

                <a href="forgot_password.php" class="register-link">Forgot Password?</a>
                <a href="register.php" class="register-link">Don't have an account? Register here</a>
            </form>
        </div>
    </div>

</div>

</body>
</html>