
<?php
session_start();

// Collect input
$name = $_POST['name'];
$phone = $_POST['phone'];
$password = $_POST['password'];

// Validate phone number (Bangladeshi format: 11 digits, starts with 01)
if (!preg_match('/^01[0-9]{9}$/', $phone)) {
  echo "invalid_phone";
  exit;
}

// Check if already registered
$conn = new mysqli("localhost", "alokito2_sadi", "sadi9507@#", "alokito2_ecom");
$stmt = $conn->prepare("SELECT id FROM cust WHERE phone = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  echo "exists";
  exit;
}

// Store user info in session
$_SESSION['register_mode'] = 'phone';
$_SESSION['name'] = $name;
$_SESSION['phone'] = $phone;
$_SESSION['password'] = password_hash($password, PASSWORD_DEFAULT);

// Generate OTP and timestamp
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['otp_time'] = time();

// Prepare SMS
$api_key = "CXVCHq5dYOzFrJglBems";
$sender_id = "8809617614828";
$message = urlencode("Your BellaBuy verification code is : $otp");

// Send via GET API
$url = "http://bulksmsbd.net/api/smsapi?api_key=$api_key&type=text&number=$phone&senderid=$sender_id&message=$message";
$response = file_get_contents($url);

// Log API response
file_put_contents("sms_debug.log", date("Y-m-d H:i:s") . " => $response\n", FILE_APPEND);

// Success/failure response to JS
if (strpos($response, 'SMS Sent') !== false || strpos($response, 'Success') !== false) {
  echo "sent";
} else {
  echo "fail: $response";
}
