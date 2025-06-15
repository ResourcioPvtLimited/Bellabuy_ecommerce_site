<?php
// DB Connection
$conn = new mysqli("localhost", "alokito2_sadi", "sadi9507@#", "alokito2_ecom");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get inputs
$type = $_GET['type'] ?? '';
$value = $_GET['value'] ?? '';

if (!in_array($type, ['email', 'phone'])) {
    echo "invalid";
    exit;
}

// Prepare and execute query
$stmt = $conn->prepare("SELECT id FROM cust WHERE $type = ?");
$stmt->bind_param("s", $value);
$stmt->execute();
$stmt->store_result();

// Return result
if ($stmt->num_rows > 0) {
    echo "exists";
} else {
    echo "ok";
}
?>
