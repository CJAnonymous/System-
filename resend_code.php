<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$servername = "localhost";
$username = "root";
$password = "";
$database = "barangay";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? null;

    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Invalid email.']);
        exit;
    }

    // Check if email exists in the database
    $sql = "SELECT verification_code FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $verification_code = $user['verification_code'];

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp-relay.brevo.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'a3db59001@smtp-brevo.com'; 
            $mail->Password = 'bccD2b3w%DTm-Zy'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('CJAnonymous640@gmail.com', 'PoblacionLink');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Resend Verification Code';
            $mail->Body = "Your verification code is: <strong>$verification_code</strong>";

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'Verification code resent.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error sending email.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found.']);
    }

    $stmt->close();
}

$conn->close();
?>
