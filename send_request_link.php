<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer

// Database connection parameters
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "barangay";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the form submission only if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and trim the email address
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    // Basic email validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        header("Location: forgot_password.php");
        exit();
    }

    // Check if the provided email exists in the users table
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        $_SESSION['error'] = "Server error.";
        header("Location: forgot_password.php");
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a secure reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", time() + 3600); // Token expires in 1 hour

        // Update the user's record with the reset token and expiry
        $update_sql = "UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            error_log("Update prepare failed: " . $conn->error);
            $_SESSION['error'] = "Server error.";
            header("Location: forgot_password.php");
            exit();
        }
        $update_stmt->bind_param("sss", $token, $expiry, $email);
        $update_stmt->execute();

        if ($update_stmt->affected_rows === 0) {
            error_log("Update failed: No rows affected. Email: " . $email);
        }

        // Prepare PHPMailer to send the reset link email
        $mail = new PHPMailer(true);
        try {
            // Server settings for SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp-relay.brevo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'a3db59001@smtp-brevo.com';
            $mail->Password   = 'bccD2b3w%DTm-Zy';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('CJAnonymous640@gmail.com', 'PoblacionLink');
            $mail->addAddress($email);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Link';
            $mail->Body    = "Hello,<br><br>
                              You requested a password reset. Click the link below to reset your password:<br><br>
                              <a href='http://localhost/poblacion/reset_password.php?token=" . urlencode($token) . "'>Reset Password</a><br><br>
                              This link will expire in 1 hour.<br><br>
                              If you did not request this, please ignore this email.";

            $mail->send();
            $_SESSION['message'] = "Reset link sent to your email.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error sending email: " . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['error'] = "Email not found.";
    }

    // Close statements and connection
    $stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    $conn->close();

    // Redirect back to the forgot password page
    header("Location: forgot_password.php");
    exit();
}
?>