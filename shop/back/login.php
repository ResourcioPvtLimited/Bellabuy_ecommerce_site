<?php
include "../../config.php";$shop_id=$_SESSION['shop_id'];
$today = date("d-m-Y");
if($_POST['action']=="login"){
  $email=$_POST['email'];
  $pass=$_POST['pass'];
  $sql = "SELECT * FROM shop where email='$email' and password='$pass' and pending='0'";
    $result = $conn->query($sql);

    if ($result->num_rows> 0) {
  
  while($row = $result->fetch_assoc()) {
    $_SESSION['shop_id']=$row['id'];
    $_SESSION['shop_name']=$row['name'];
    echo 1;
  }
} else {
  echo "Wrong Credentials!";
}
}
elseif($_POST['action']=="update_profile"){$shop_id=$_SESSION['shop_id'];
  $email=$_POST['email'];
  $name=$_POST['name'];$phone=$_POST['phone'];$adr=$_POST['adr'];
  $sql = "UPDATE `shop` SET `name`='$name',`email`='$email',`phone`='$phone',`address`='$adr' WHERE id='$shop_id'";
    if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo "Error!";}

}elseif($_POST['action']=="update_pass"){
  $pass=$_POST['pass'];

  $sql = "UPDATE `shop` SET `password`='$pass' WHERE id='$shop_id'";
    if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo "Error!";}

}elseif($_POST['action']=="join"){
   $shopName = $_POST['shopName'];
    $emailAddress = $_POST['emailAddress'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $shopAddress = $_POST['shopAddress'];$lat = $_POST['lat'];$lon = $_POST['lon'];

   $sql = "INSERT INTO `shop`(`name`, `email`, `phone`, `address`, `password`, `lat`, `lon`) VALUES ('$shopName','$emailAddress','$phone','$shopAddress','$password','$lat','$lon')";
   if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="update_product"){
   $name = $_POST['name'];
    $des = $_POST['des'];
    $max = $_POST['max'];$num = $_POST['num'];
    $dis = $_POST['dis']; $size = $_POST['size'];$specs = $_POST['specs'];
    $price =intval(((100-intval($dis))*intval($max))/100);
$my_=$_SESSION['my_'];
   $sql = "UPDATE `item` SET `name`='$name',`price`='$price',`discount`='$dis',des_short='$des',`max_price`='$max',size='$size',num='$num',specs='$specs' WHERE id='$my_'";
   if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="disable_product"){
   $id = $_POST['id'];
  
   $sql = "UPDATE `item` SET `disable`='1' WHERE id='$id' and shop_id='$shop_id'";
   if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="enable_product"){
   $id = $_POST['id'];
    

   $sql = "UPDATE `item` SET `disable`='0' WHERE id='$id' and shop_id='$shop_id'";
   if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="review_abuse"){
   $id = $_POST['id'];
    

   $sql = "UPDATE `review` SET `abuse`='1' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="update_price"){
   $id = $_POST['id'];
    $sellingPrice=$_POST['p'];
    
    $maxPrice=$_POST['max'];
if ($maxPrice > 0) {
    $dis = (($maxPrice - $sellingPrice) / $maxPrice) * 100;
} else {
    $dis = 0; // Handle the case where maxPrice is 0 to avoid division by zero
}
   $sql = "UPDATE `item` SET `price`='$sellingPrice',discount='$dis' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="pick__"){
   $id = $_POST['id'];$t_ = $_POST['t_'];
    

   $sql = "UPDATE `orders` SET `status`='picked',pickup_time='$today',t_id='$t_' WHERE order_id='$id'";
   if($conn->query($sql)===TRUE){
        echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="out_"){
   $id = $_POST['id'];
    

   $sql = "UPDATE item SET stock='0' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
        echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="in_"){
   $id = $_POST['id'];
    

   $sql = "UPDATE item SET stock='1' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
        echo 1;
    }else{echo $conn->error;}

}
?>