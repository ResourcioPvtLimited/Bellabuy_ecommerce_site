<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database configuration
include "../config.php";

// Function to generate a unique filename
function generateUniqueFilename($extension) {
    return uniqid('image_', true) . '.' . $extension;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the database connection is established (assuming it's defined in config.php)
    if (!isset($conn)) {
        die("Database connection failed.");
    }
    
    // Handle text inputs
    $text1 = $_POST['text1'];
    $text2 = $_POST['text2'];
    
    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = '../cat/';
        
        // Ensure the uploads directory exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Get the original file extension
        $fileInfo = pathinfo($_FILES['image']['name']);
        $fileExtension = $fileInfo['extension'];
        
        // Generate a unique filename
        $uniqueFilename = generateUniqueFilename($fileExtension);
        $uploadFile = $uploadDir . $uniqueFilename;
        
        // Move uploaded file to the destination directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            // Insert details into the database
            $stmt = $conn->prepare("INSERT INTO cat (cat, subcat, logo) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $text1, $text2, $uploadFile);
            
            if ($stmt->execute()) {
                header('Location:add_cat.php');
            } else {
                echo "Failed to insert details into the database: " . $conn->error;
            }
            
            $stmt->close();
        } else {
            echo "File upload failed!<br>";
        }
    } else {
        echo "No file uploaded or there was an upload error.<br>";
    }
} else {
    echo "Invalid request method.";
}

// Close the database connection
$conn->close();
?>
