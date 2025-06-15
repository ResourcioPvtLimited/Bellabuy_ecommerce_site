<?php
session_start();
require 'smtp/PHPMailerAutoload.php';

// Collect form input
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo "invalid_email";
  exit;
}

// Check if email is already registered
$conn = new mysqli("localhost", "alokito2_sadi", "sadi9507@#", "alokito2_ecom");
$stmt = $conn->prepare("SELECT id FROM cust WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  echo "exists";
  exit;
}

// Save registration details to session
$_SESSION['register_mode'] = 'email';
$_SESSION['name'] = $name;
$_SESSION['email'] = $email;
$_SESSION['password'] = password_hash($password, PASSWORD_DEFAULT);

// Generate OTP
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['otp_time'] = time();

// Setup PHPMailer
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'mail.alokitoscouts.com';
$mail->SMTPAuth = true;
$mail->Username = 'contact@alokitoscouts.com';
$mail->Password = 'sadi9507@#';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('contact@alokitoscouts.com', 'Alokito Scouts');
$mail->addAddress($email, $name);
$mail->isHTML(true);
$mail->Subject = 'Your BellaBuy OTP Code';
$mail->Body = "
<div style='font-family:Arial,sans-serif;padding:20px;'>
  <h2 style='color:#e44a2d;'>Your BellaBuy verification code is:</h2>
  <div style='font-size:30px;font-weight:bold;color:#222;'>$otp</div>
  <p style='margin-top:10px;'>This OTP will expire in 3 minutes. Do not share it with anyone.</p>
</div>
";

// Send the email
if ($mail->send()) {
  echo "sent";
} else {
  echo "fail";
}
