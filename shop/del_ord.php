<?php
require "../config.php";
session_start();
?>
<!DOCTYPE html>
<html>
   <head>
      <?php require "inc/head.php"; ?>
      <title>Delivered Orders</title>
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
      <style>
         td, th { vertical-align: middle !important; }
         .prod-img {
            height: 50px;
            width: 50px;
            object-fit: cover;
            border-radius: 5px;
         }
      </style>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   </head>
   <body>
  
         <?php require "inc/sidebar.php"; ?>
         
            <div class="content">
               <h3 class="page-title">Delivered Orders</h3>
               <br>
               <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by name or order ID..." class="form-control mb-3">

               <table class="table table-striped padding" id="tab">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Order ID</th>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Status</th>
                        <th>Order Date</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                     $shop_id = $_SESSION['shop_id'];
                     $sql = "
                        SELECT o.order_id, o.order_time, o.size, o.price, o.qty, o.status, o.discount, o.coupon,
                               i.name AS product_name, i.img1
                        FROM orders o
                        LEFT JOIN item i ON o.p_id = i.id
                        WHERE o.shop_id = '$shop_id' AND o.status = 'delivered'
                        ORDER BY o.order_time DESC
                     ";
                     $result = $conn->query($sql);
                     $sl = 1;

                     if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                           $order_id = $row['order_id'];
                           $order_time = $row['order_time'];
                           $product = $row['product_name'];
                           $img = !empty($row['img1']) ? "../prod/" . $row['img1'] : "../prod/no-image.png";
                           $qty = $row['qty'];
                           $size = $row['size'] ?: '-';
                           $price = $row['price'];
                           $discount = $row['discount'] ?: '0';
                           $status = ucfirst($row['status']);

                           echo "
                              <tr>
                                 <td>{$sl}</td>
                                 <td><a href='order_details.php?type=delivered&id={$order_id}'>{$order_id}</a></td>
                                 <td><img src='{$img}' class='prod-img'></td>
                                 <td>{$product}</td>
                                 <td>{$qty}</td>
                                 <td>{$size}</td>
                                 <td>{$price}</td>
                                 <td>{$discount}</td>
                                 <td>{$status}</td>
                                 <td>{$order_time}</td>
                              </tr>
                           ";
                           $sl++;
                        }
                     } else {
                        echo "<tr><td colspan='10'>No delivered orders found.</td></tr>";
                     }
                     ?>
                  </tbody>
               </table>

               <script>
               function myFunction() {
                  const input = document.getElementById("myInput").value.toUpperCase();
                  const rows = document.querySelector("#tab tbody").getElementsByTagName("tr");

                  for (let i = 0; i < rows.length; i++) {
                     const orderId = rows[i].getElementsByTagName("td")[1];
                     const product = rows[i].getElementsByTagName("td")[3];

                     if (orderId && product) {
                        const orderText = orderId.textContent || orderId.innerText;
                        const prodText = product.textContent || product.innerText;
                        const match = orderText.toUpperCase().includes(input) || prodText.toUpperCase().includes(input);
                        rows[i].style.display = match ? "" : "none";
                     }
                  }
               }
               </script>
            </div>
        
   </body>
</html>
