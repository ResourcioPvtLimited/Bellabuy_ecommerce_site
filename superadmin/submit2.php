<?php
// Include database configuration
include "../config.php";

// Generate unique filename for uploaded image
function generateUniqueFileName($prefix, $extension) {
    $timestamp = time();
    $random = mt_rand(10000, 99999);
    return $prefix . '_' . $timestamp . '_' . $random . '.' . $extension;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input fields
    $t1 = mysqli_real_escape_string($conn, $_POST['text3']);
    $t2 = mysqli_real_escape_string($conn, $_POST['text4']);
    $link = mysqli_real_escape_string($conn, $_POST['text5']);

    // File upload handling
    $imageName = $_FILES['image1']['name'];
    $imageTmpName = $_FILES['image1']['tmp_name'];
    $imageError = $_FILES['image1']['error'];

    // Proceed if upload is OK
    if ($imageError === UPLOAD_ERR_OK) {
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $uniqueImageName = generateUniqueFileName('image1', $imageExtension);

        $uploadDir = "../banner/";
        $uploadPath = $uploadDir . $uniqueImageName;

        // Move uploaded file
        if (move_uploaded_file($imageTmpName, $uploadPath)) {
            // Insert into database
            $sql = "INSERT INTO banner (big, t1, t2, link)
                    VALUES ('$uniqueImageName', '$t1', '$t2', '$link')";

            if ($conn->query($sql) === TRUE) {
                echo "✅ Banner uploaded successfully.";
            } else {
                echo "❌ Database Error: " . $conn->error;
            }
        } else {
            echo "❌ Failed to move uploaded file.";
        }
    } else {
        echo "❌ File upload error. Code: $imageError";
    }

    // Close connection
    $conn->close();
}
?>
