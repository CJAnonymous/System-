<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "barangay";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            // New check: only active accounts can login
            if ($user['account_status'] == 'pending') {
                echo "<script>
                        alert('Your account is pending approval by an administrator.');
                        window.history.back();
                      </script>";
                exit;
            }
            if ($user['account_status'] == 'rejected') {
                echo "<script>
                        alert('Your account registration was rejected.');
                        window.history.back();
                      </script>";
                exit;
            }

            // Check if the account is suspended or archived
            if ($user['account_status'] == 'suspended' || $user['account_status'] == 'archived') {
                echo "<script>
                        alert('You cannot login, your account was suspended.');
                        window.history.back();
                      </script>";
                exit;
            }

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_name'] = $user['first_name']; 

            // Redirect based on role
            $redirectPage = '';
            switch ($user['role']) {
                case 'Resident':
                    $redirectPage = 'Resident/welcome.php';
                    break;
                case 'Staff':
                    $redirectPage = 'Staff/welcome.php';
                    break;
                case 'Admin':
                    $redirectPage = 'Admin/welcome.php';
                    break;
                }
                header("Location: $redirectPage");
                exit;
                }
            };
        };
    $stmt->close();

$conn->close();
?>
