<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load PHPMailer classes
require '../back/smtp/PHPMailerAutoload.php'; // Adjust the path as needed

// Include configuration file
require "../config.php";

// Handle CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: api_key, secret_key, Content-Type");

// Handle preflight requests for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Define the SMTP mailer function
function smtp_mailer($to, $subject, $msg, $email, $pass, $host, $sender) {
    $mail = new  PHPMailer();
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = $host;
    $mail->Port = 587;
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = $email;
    $mail->Password = $pass;
    $mail->setFrom($email, $sender);
    $mail->Subject = $subject;
    $mail->Body =$msg;
    $mail->addAddress($to);
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => false,
        ],
    ];
    return $mail->send() ? "1" : "0";
}

// Handle incoming POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (isset($data['to']) && isset($data['subject']) && isset($data['message'])) {
        $to = $data['to'];
        $subject = $data['subject'];
        $message = $data['message'];

        // Send email
        $result = smtp_mailer($to, $subject, $message, $mail_email, $mail_pass, $mail_host, $mail_sender);
        if ($result === "1") {
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Email sent successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to send email']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>
