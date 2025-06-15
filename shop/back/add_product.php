<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $productName = $_POST["productName"];
    $description = $_POST["description"];
    $productPrice = $_POST["productPrice"];
    $subcat=$_POST['subcategory']; $specs=$_POST['specs'];
    $productDiscount = $_POST["productDiscount"]; $size = $_POST["size"]; 
    $cat = $_POST["cat_"];
     $num = $_POST["num"];
     $state = $_POST["state"];
$price=intval(((100-intval($productDiscount))*intval($productPrice))/100);
    // Handle file uploads
    $image1 = str_replace(' ', '-', time() . $_FILES["image1"]["name"]);
$image2 = str_replace(' ', '-', time() . $_FILES["image2"]["name"]);
$image3 = str_replace(' ', '-', time() . $_FILES["image3"]["name"]);
$image4 = str_replace(' ', '-', time() . $_FILES["image4"]["name"]);


    // Upload images to a specific directory
    $uploadDirectory = "../../prod/"; // Change this to your directory
    move_uploaded_file($_FILES["image1"]["tmp_name"], $uploadDirectory . $image1);
    move_uploaded_file($_FILES["image2"]["tmp_name"], $uploadDirectory . $image2);
    move_uploaded_file($_FILES["image3"]["tmp_name"], $uploadDirectory . $image3);
    move_uploaded_file($_FILES["image4"]["tmp_name"], $uploadDirectory . $image4);
$shop_id=$_SESSION['shop_id'];
$shop_name=$_SESSION['shop_name'];
    

    // Insert data into the database
    $query = "INSERT INTO item (name, max_price, img1, img2, img3, img4, discount, des_short,price,cat,shop,shop_id,size,subcat,num,specs,state) VALUES ('$productName', $productPrice, '$image1', '$image2', '$image3', '$image4', $productDiscount, '$description','$price','$cat','$shop_name','$shop_id','$size','$subcat','$num','$specs','$state')";

    if ($conn->query($query) === TRUE) {
        // Insertion successful
        header('Location:../add_product.php');
    } else {
        // Insertion failed
        echo "Error adding product: " . $conn->error;
    }

    $conn->close();
}
?>
