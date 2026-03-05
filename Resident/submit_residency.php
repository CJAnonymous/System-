<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "barangay";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        // =========================
        // VALIDATE PURPOSE
        // =========================
        if (empty($_POST['purpose'])) {
            throw new Exception("Purpose is required.");
        }

        $purpose = trim($_POST['purpose']);
        $id_type = trim($_POST['id_type'] ?? '');

        if (empty($id_type)) {
            throw new Exception("Please select an ID type.");
        }

        // =========================
        // VALIDATE FILE
        // =========================
        if (!isset($_FILES['valid_id'])) {
            throw new Exception("Valid ID file missing.");
        }

        if ($_FILES['valid_id']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload error code: " . $_FILES['valid_id']['error']);
        }

        $valid_id = $_FILES['valid_id'];

        // Allowed file types
        $allowed = ['jpg', 'jpeg', 'png'];
        $file_ext = strtolower(pathinfo($valid_id['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed)) {
            throw new Exception("Only JPG, JPEG, PNG files allowed.");
        }

        if ($valid_id['size'] > 5 * 1024 * 1024) {
            throw new Exception("File size must be below 5MB.");
        }

        // =========================
        // CREATE UPLOAD DIRECTORY
        // =========================
        $upload_dir = "uploads/residency_uploads/";

        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                throw new Exception("Failed to create upload folder.");
            }
        }

        if (!is_writable($upload_dir)) {
            throw new Exception("Upload folder is not writable.");
        }

        // Unique filename
        $valid_id_name = $upload_dir . uniqid("RESID_", true) . "." . $file_ext;

        if (!move_uploaded_file($valid_id['tmp_name'], $valid_id_name)) {
            throw new Exception("Failed to move uploaded file.");
        }

        // =========================
        // GET USER DATA
        // =========================
        $user_id = $_SESSION['user_id'];

        $stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $result = $stmt_user->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("User not found.");
        }

        $user = $result->fetch_assoc();
        $stmt_user->close();

        $address = implode(', ', [
            $user['blk_street'],
            $user['barangay'],
            $user['city'],
            $user['province'],
            $user['region']
        ]);

        // =========================
        // GENERATE REFERENCE NUMBER
        // =========================
        $date = date('Ymd');

        $stmt_count = $conn->prepare(
            "SELECT COUNT(*) as count FROM certificate_of_residency WHERE DATE(created_at) = CURDATE()"
        );
        $stmt_count->execute();
        $count = $stmt_count->get_result()->fetch_assoc()['count'] + 1;
        $stmt_count->close();

        $reference_number = "RES-" . $date . "-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        // =========================
        // INSERT DATA
        // =========================
        $sql = "INSERT INTO certificate_of_residency
                (last_name, first_name, middle_name, address, purpose, 
                 valid_id_path, reference_number, user_id, id_type)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("SQL Error: " . $conn->error);
        }

        $stmt->bind_param(
            "sssssssis",
            $user['last_name'],
            $user['first_name'],
            $user['middle_name'],
            $address,
            $purpose,
            $valid_id_name,
            $reference_number,
            $user_id,
            $id_type
        );

        if (!$stmt->execute()) {
            throw new Exception("Insert failed: " . $stmt->error);
        }

        $stmt->close();

        $_SESSION['success'] = $reference_number;
        header("Location: certificate-of-residency.php");
        exit();

    } catch (Exception $e) {

        $_SESSION['error'] = $e->getMessage();
        header("Location: certificate-of-residency.php");
        exit();
    }
}

$conn->close();
?>
