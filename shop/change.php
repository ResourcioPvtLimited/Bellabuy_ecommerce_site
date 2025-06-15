<?php
include "../config.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = '../prod/';
    $p_i=$_POST['p_i'];$i__=$_POST['id'];

    function handleFileUpload($fileInputName,$col,$conn) {
       global $uploadDir;global $p_i;global $i__;
       unlink($uploadDir.$p_i);


        if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$fileInputName]['tmp_name'];
            $fileName = time().$_FILES[$fileInputName]['name'];
            $destPath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
            $sql="UPDATE item set $col='$fileName' where id='$i__'";
            $conn->query($sql);
                return "Image uploaded successfully: $fileName";
            } else {
                return "Error uploading $fileName";
            }
        } else {
            return "No image uploaded or an error occurred for $fileInputName";
        }
        
    }

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Handle different actions based on the 'action' value
    switch ($action) {
        case 'file1':
            $response = handleFileUpload('file1','img1',$conn);
            break;
        case 'file2':
            $response = handleFileUpload('file2','img2',$conn);
            break;
        case 'file3':
            $response = handleFileUpload('file3','img3',$conn);
            break;
        case 'file4':
            $response = handleFileUpload('file4','img4',$conn);
            break;
        default:
            $response = 'Invalid action';
            break;
    }

    // Output the response
    echo $response;
} else {
    // Invalid request method
    echo "Invalid request.";
}
?>
