<?php
$today = date("d-m-Y");
include "../../back/smtp/PHPMailerAutoload.php";
include "../../config.php";$shop_id=$_SESSION['shop_id'];
if($_POST['action']=="login"){
  $email=$_POST['email'];
  $pass=$_POST['pass'];
 

    if ($email=="onkar@gmail.com" && $pass=="123") {
  echo 1;$_SESSION['type']="admin";
  
} else {
  echo "Wrong Credentials!";
}
}elseif($_POST['action']=="disable_product"){
   $id = $_POST['id'];
  
   $sql = "UPDATE `item` SET `disable`='1' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="enable_product"){
   $id = $_POST['id'];
    

   $sql = "UPDATE `item` SET `disable`='0' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="review_abuse"){
   $id = $_POST['id'];
    

   $sql = "DELETE  FROM review WHERE id='$id'";
   if($conn->query($sql)===TRUE){
      /**/
   $sql="UPDATE item set reviews=reviews-1 where id='$id'";
   if($conn->query($sql)===TRUE){echo 1;}else{echo "Error!";}
   /**/
    }else{echo $conn->error;}

}elseif($_POST['action']=="review_decline"){
   $id = $_POST['id'];
    

   $sql = "UPDATE `review` SET `abuse`='0' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="ban_user"){
   $id = $_POST['id'];
    

   $sql = "UPDATE `cust` SET `ban`='1' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="unban_user"){
   $id = $_POST['id'];
    

   $sql = "UPDATE `cust` SET `ban`='0' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
      echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="ban_shop"){
   $id = $_POST['id'];
    

   $sql = "UPDATE `shop` SET `ban`='1' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
        /**/
   $sql="UPDATE item set disable=1 where shop_id='$id'";
   if($conn->query($sql)===TRUE){echo 1;}else{echo "Error!";}
   /**/
    }else{echo $conn->error;}

}elseif($_POST['action']=="unban_shop"){
   $id = $_POST['id'];
    

   $sql = "UPDATE `shop` SET `ban`='0' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
        /**/
    $sql="UPDATE item set disable=0 where shop_id='$id'";
   if($conn->query($sql)===TRUE){echo 1;}else{echo "Error!";}
   /**/
    }else{echo $conn->error;}

}elseif($_POST['action']=="decline_shop"){
   $id = $_POST['id'];
    

   $sql = "delete from `shop` WHERE id='$id'";
   if($conn->query($sql)===TRUE){
       echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="accept_shop"){
   $id = $_POST['id'];
    

   $sql = "UPDATE `shop` SET `pending`='0' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
        echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="pick__"){
   $id = $_POST['id'];$t_ = $_POST['t_'];
    

   $sql = "UPDATE `orders` SET `status`='picked',pickup_time='$today',t_id='$t_' WHERE order_id='$id'";
   if($conn->query($sql)===TRUE){
        /***********************/













        function smtp_mailer($to,$subject, $msg,$email,$pass,$host,$sender){
      try{
            $mail = new PHPMailer(); 
            $mail->IsSMTP(); 
            $mail->SMTPAuth = true; 
            $mail->SMTPSecure = 'tls'; 
            $mail->Host = $host;
            $mail->Port = 587; 
            $mail->IsHTML(true);
            $mail->CharSet = 'UTF-8';
            //$mail->SMTPDebug = 2; 
            $mail->Username = $email;
            $mail->Password = $pass;
            $mail->setFrom($email, $sender);
            $mail->Subject = $subject;
            $mail->Body =$msg;
            $mail->AddAddress($to);
            $mail->SMTPOptions=array('ssl'=>array(
            'verify_peer'=>false,
            'verify_peer_name'=>false,
            'allow_self_signed'=>false
            ));
              if (!$mail->Send()) {
                        throw new Exception($mail->ErrorInfo);
                    } else {
                        return "1";
                    }
                } catch (Exception $e) {
                    return "0 - " . $e->getMessage();
                }
            }
$itemsArray = [];
$sql="SELECT email FROM cust WHERE id = (SELECT distinct(u_id) FROM orders WHERE `order_id` = '$id') LIMIT 0, 25";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $user_mail = $row['email'];
    }
 }

 $sql = "SELECT item.name as name, orders.qty as qty, orders.price as price FROM orders, item WHERE orders.order_id='$id' and orders.p_id=item.id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = $row['name'];
        $qty = $row['qty'];
        $price = $row['price'];
        
        $itemArray = ["qty" => $qty, "price" => $price, "item_name" => $name];
        $itemsArray[] = $itemArray;
    }
}

        $body =
        '<!DOCTYPE html>
        <html lang="en">

        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Details</title>


        <!-- Inline Styles -->
        <style>
        /* Add your custom inline styles here */
        body {
        font-family: Arial, sans-serif;
        }

        .container {

        }

        table {
        width: 100%;
        border-collapse: collapse;

        }

        th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        }

        th {
        background-color: #f2f2f2;
        }
        </style>
        </head>

        <body>

        <div class="container">

        <table>
        <thead>
        <tr>
        <th>Order ID</th>
        <th>Date</th>
        <th>Email</th>
        <th>Tracking ID</th>

        </tr>

        </thead>
        <tbody>
        <tr>
        <td>' .
        $id .
        '</td>
        <td>' .
        $today .
        '</td>
        <td>' .
        $user_mail .
        '</td><td>' .
        $t_ .
        '</td>

        </tr>


        <!-- Add more rows as needed -->
        </tbody>
        </table>

        <table>
        <thead>
        <tr>
        <th>No.</th>
        <th>Name</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
        </tr>
        </thead>
        <tbody>';
        $counter = 1;
        foreach ($itemsArray as $item) {
        $body .=
        '<tr>
        <td>' .
        $counter .
        '</td>
        <td>' .
        $item["item_name"] .
        '</td>
        
        <td>' .
        $item["qty"] .
        '</td>
        <td>' .
        $item["price"] .
        '</td>
        <td>' .
        $item["qty"] * $item["price"] .
        '</td>
        </tr>';
        $counter++;
        }

        $body .=
        '</tbody>
        </table>

        </div>

        </body>

        </html>

        ';
echo smtp_mailer($user_mail, "Shipped - Order #" . $id, '<p>All your products have been shipped. Visit website to see more details.</p>'.$body,$mail_email,$mail_pass,$mail_host,$mail_sender);










        /***********************/
    }else{echo $conn->error;}

}elseif($_POST['action']=="del__"){
   $id = $_POST['id'];
    

   $sql = "UPDATE `orders` SET `status`='delivered',del_time='$today' WHERE order_id='$id'";
   if($conn->query($sql)===TRUE){
         /**********************/

        function smtp_mailer($to,$subject, $msg,$email,$pass,$host,$sender){
      try{
            $mail = new PHPMailer(); 
            $mail->IsSMTP(); 
            $mail->SMTPAuth = true; 
            $mail->SMTPSecure = 'tls'; 
            $mail->Host = $host;
            $mail->Port = 587; 
            $mail->IsHTML(true);
            $mail->CharSet = 'UTF-8';
            //$mail->SMTPDebug = 2; 
            $mail->Username = $email;
            $mail->Password = $pass;
            $mail->setFrom($email,$sender);
            $mail->Subject = $subject;
            $mail->Body =$msg;
            $mail->AddAddress($to);
            $mail->SMTPOptions=array('ssl'=>array(
            'verify_peer'=>false,
            'verify_peer_name'=>false,
            'allow_self_signed'=>false
            ));
              if (!$mail->Send()) {
                        throw new Exception($mail->ErrorInfo);
                    } else {
                        return "1";
                    }
                } catch (Exception $e) {
                    return "0 - " . $e->getMessage();
                }
            }
$itemsArray = [];
$sql="SELECT email FROM cust WHERE id = (SELECT distinct(u_id) FROM orders WHERE `order_id` = '$id') LIMIT 0, 25";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $user_mail = $row['email'];
    }
 }

 $sql = "SELECT item.name as name, orders.qty as qty,orders.order_id as o_,orders.t_id as t_id, orders.price as price FROM orders, item WHERE orders.order_id='$id' and orders.p_id=item.id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = $row['name'];
        $qty = $row['qty'];
        $price = $row['price'];$order_id = $row['o_'];$t_id = $row['t_id'];
        
        $itemArray = ["qty" => $qty, "price" => $price, "item_name" => $name];
        $itemsArray[] = $itemArray;
    }
}

        $body =
        '<!DOCTYPE html>
        <html lang="en">

        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Details</title>


        <!-- Inline Styles -->
        <style>
        /* Add your custom inline styles here */
        body {
        font-family: Arial, sans-serif;
        }

        .container {

        }

        table {
        width: 100%;
        border-collapse: collapse;

        }

        th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        }

        th {
        background-color: #f2f2f2;
        }
        </style>
        </head>

        <body>

        <div class="container">

        <table>
        <thead>
        <tr>
        <th>Order ID</th>
        <th>Date</th>
        <th>Email</th>
        <th>Tracking ID</th>

        </tr>

        </thead>
        <tbody>
        <tr>
        <td>' .
        $order_id .
        '</td>
        <td>' .
        $today .
        '</td>
        <td>' .
        $user_mail .
        '</td><td>' .
        $t_id .
        '</td>

        </tr>


        <!-- Add more rows as needed -->
        </tbody>
        </table>

        <table>
        <thead>
        <tr>
        <th>No.</th>
        <th>Name</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
        </tr>
        </thead>
        <tbody>';
        $counter = 1;
        foreach ($itemsArray as $item) {
        $body .=
        '<tr>
        <td>' .
        $counter .
        '</td>
        <td>' .
        $item["item_name"] .
        '</td>
        
        <td>' .
        $item["qty"] .
        '</td>
        <td>' .
        $item["price"] .
        '</td>
        <td>' .
        $item["qty"] * $item["price"] .
        '</td>
        </tr>';
        $counter++;
        }

        $body .=
        '</tbody>
        </table>

        </div>

        </body>

        </html>

        ';
echo smtp_mailer($user_mail, "Delivered - Order #" . $id, '<p>All your products have been delivered successfully!. Visit website to see more details.</p>'.$body,$mail_email,$mail_pass,$mail_host,$mail_sender);










        /***********************/
    }else{echo $conn->error;}

}elseif($_POST['action']=="expire_coupon"){
   $id = $_POST['id'];
    

   $sql = "UPDATE `coupon` SET `expired`='1' WHERE id='$id'";
   if($conn->query($sql)===TRUE){
        echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="delete_coupon"){
   $id = $_POST['id'];
    

   $sql = "DELETE FROM coupon where id='$id'";
   if($conn->query($sql)===TRUE){
        echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="delete_cat"){
   $id = $_POST['id'];
    

   $sql = "DELETE FROM cat where id='$id'";
   if($conn->query($sql)===TRUE){
        echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="delete_banner"){
   $id = $_POST['id'];
    

   $sql = "DELETE FROM banner where id='$id'";
   if($conn->query($sql)===TRUE){
        echo 1;
    }else{echo $conn->error;}

}elseif($_POST['action']=="create_coup"){
   $code = $_POST['code'];
    $discount = $_POST['discount'];
    $type = $_POST['type_'];
    $maxUse = $_POST['maxUse'];
    $description = $_POST['description'];
    $condition = $_POST['cond'];  // Assuming 'condition' is the correct field name
    $maxCart = $_POST['maxCart'];

    // Assuming other fields are not mentioned in your form

    // Construct the SQL statement
    $sql = "INSERT INTO `coupon` (`code`, `discount`, `type`, `max_use`, `des`, `cond`, `max_cart`) 
            VALUES ('$code', '$discount', '$type', '$maxUse', '$description', '$condition', '$maxCart')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo 1; // Echo 1 if the query was successful
    } else {
        echo $conn->error; // Echo the error message if there was an issue with the query
    }

}
?>