<?php
require "../config.php";
session_start();

// AJAX actions
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
   if ($_POST['action'] === "toggle_stock") {
      $id = intval($_POST['id']);
      $stock = intval($_POST['stock']);
      echo $conn->query("UPDATE item SET stock='$stock' WHERE id='$id'") ? 1 : 0;
      exit;
   }

   if ($_POST['action'] === "disable_product") {
      $id = intval($_POST['id']);
      echo $conn->query("UPDATE item SET disable='1' WHERE id='$id'") ? 1 : 0;
      exit;
   }

   if ($_POST['action'] === "update_price") {
      $id = intval($_POST['id']);
      $price = floatval($_POST['p']);
      $max = floatval($_POST['max']);
      echo $conn->query("UPDATE item SET price='$price', max_price='$max' WHERE id='$id'") ? 1 : 0;
      exit;
   }
}
?>
<!DOCTYPE html>
<html>
<head>
   <?php require "inc/head.php"; ?>
   <title>Products</title>
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <style>
      td, th { vertical-align: middle !important; }
      .custom-toast {
         position: fixed;
         top: 20px;
         right: 20px;
         z-index: 9999;
         min-width: 250px;
         border-radius: 8px;
         box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
         animation: fadeInRight 0.5s ease;
      }
      .toast-success { background-color: #28a745; color: white; }
      .toast-error { background-color: #dc3545; color: white; }
      @keyframes fadeInRight {
         from { opacity: 0; transform: translateX(100px); }
         to { opacity: 1; transform: translateX(0); }
      }
   </style>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <script type="text/javascript">
   $(document).ready(function () {
      $(".edit_price").click(function(){
         var row = $(this).closest("tr");
         var p = row.find(".new_price").val();
         var max = row.find(".have_max_price").attr("title");
         var id = row.attr("title");
         $.post("", {action:"update_price", p:p, id:id, max:max}, function (res) {
            showToast(res == 1 ? "Price updated successfully" : res, res == 1);
         });
      });

      $(".in_, .out_").click(function () {
         var button = $(this);
         var id = button.attr("title");
         var stock = button.hasClass("in_") ? 1 : 0;
         $.post("", {action: "toggle_stock", id: id, stock: stock}, function(res) {
            if (res == 1) {
               if (stock === 1) {
                  button.removeClass("in_ btn-warning").addClass("out_ btn-success").text("In Stock");
               } else {
                  button.removeClass("out_ btn-success").addClass("in_ btn-warning").text("Out Of Stock");
               }
               showToast("Stock status updated", true);
            } else {
               showToast("Stock update failed", false);
            }
         });
      });

      $(".disable_product").click(function () {
         var btn = $(this);
         var id = btn.attr("title");
         $.post("", {action: "disable_product", id: id}, function (res) {
            if (res == 1) {
               btn.closest("tr").fadeOut();
               showToast("Product disabled", true);
            } else {
               showToast("Failed to disable product", false);
            }
         });
      });

      $("#myInput").on("keyup", function () {
         var value = $(this).val().toUpperCase();
         $("#tab tbody tr").filter(function () {
            $(this).toggle($(this).find("td:eq(2)").text().toUpperCase().indexOf(value) > -1)
         });
      });
   });

   function showToast(message, isSuccess = true) {
      const toast = $(`
         <div class="toast custom-toast ${isSuccess ? 'toast-success' : 'toast-error'}" role="alert">
            <div class="toast-body">${message}</div>
         </div>
      `);
      $("body").append(toast);
      toast.toast({ delay: 3000 });
      toast.toast('show');
      setTimeout(() => { toast.fadeOut(() => toast.remove()); }, 3000);
   }
   </script>
</head>
<body>
  
      <?php require "inc/sidebar.php"; ?>
     
         <div class="content">
            <h3 class="page-title">All Products</h3><br>
            <input type="text" id="myInput" placeholder="Search for names.." class="form-control">
            <table class="table table-striped padding" id="tab">
               <thead>
                  <tr>
                     <th>Image</th>
                     <th>ID</th>
                     <th>Product</th>
                     <th>Available Stock</th>
                     <th>Price</th>
                     <th>Discount</th>
                     <th>Reviews</th>
                     <th>Disable</th>
                     <th>Stock</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $shop_id = $_SESSION['shop_id'];
                     $sql = "SELECT * FROM `item` WHERE shop_id='$shop_id' AND disable='0' And status ='accepted' ORDER BY id DESC";
                     $result = $conn->query($sql);

                     if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                           $id = $row['id'];
                           $name = $row['name'];
                           $price = $row['price'];
                           $reviews = htmlentities($row['reviews']);
                           $star = $row['star'];
                           $discount = $row['discount'];
                           $max_price = $row['max_price'];
                           $sto_ = $row['stock'];
                           $num = $row['num'];
                           $img1 = $row['img1'];
                           $imgPath = !empty($img1) ? "../prod/" . $img1 : "../prod/no-image.png";

                           $td_ = ($sto_ == '0') 
                              ? "<td><button class='btn btn-warning in_' title='".$id."'>Out Of Stock</button></td>" 
                              : "<td><button class='btn btn-success out_' title='".$id."'>In Stock</button></td>";

                           echo "
                           <tr title='" . $id . "'>
                              <td><img src='" . $imgPath . "' style='height:50px;width:50px;object-fit:cover;border-radius:5px;'></td>
                              <td class='have_max_price' title='" . $max_price . "'>" . $id . "</td>
                              <td class='have_discount' title='" . $discount . "'><a href='single.php?id={$id}'>" . $name . "</a></td>
                              <td>" . $num . "</td>
                              <td>" . $max_price . "</td>
                              <td>" . $discount . "</td>
                              <td>" . $reviews . "</td>
                              <td><button class='btn btn-danger disable_product' title='" . $id . "'>Disable</button></td>
                              " . $td_ . "
                           </tr>
                           ";
                        }
                     } else {
                        echo "<tr><td colspan='9'>0 results</td></tr>";
                     }
                  ?>
               </tbody>
            </table>
         </div>

</body>
</html>
