<!DOCTYPE html>
<html>
   <head>
      <?php require "inc/head.php";?>
      <title>Product Details</title>
   </head>
   <body>
      
      <?php require "inc/nav.php";?>
      <div class="flex_">
         
         <?php require "inc/sidebar.php";?>
         <div class="right">
          <div class="padding">
            <h3 class="page-title">Update Product</h3>
                  <br>
          
            <?php
                  $shop_id=$_SESSION['shop_id'];$p_id=$_GET['id']; $_SESSION['my_']=$p_id;
                     $sql = "SELECT * FROM item WHERE shop_id='$shop_id' and id='$p_id'";
                     $result = $conn->query($sql);
                     
                     if ($result->num_rows > 0) {
                         while ($row = $result->fetch_assoc()) {
                            $name= $row['name'];
                            
                            $dis= $row['des_short'];
                            $price= $row['max_price'];
                            $discount= $row['discount'];

                            
                         }
                     } else {
                         header('Location:../404.php');
                     }
                     ?>
            <form  class="update_product">
               <div>
                  
                  <div class="mb-3">
                     <label for="productName" class="form-label">Name</label>
                     <input required type="text" class="form-control name" id="productName" name="productName" placeholder="Name" value="<?php echo $name;?>">
                  </div>
                  <div class="mb-3">
                     <label for="des" class="form-label">Product Description</label>
                     <textarea class="form-control des" name="description" placeholder="Description" required><?php echo $dis;?></textarea>
                  </div>
                  <div class="mb-3">
                     <label for="productPrice" class="form-label">Price Including Discount</label>
                     <input required type="number" class="form-control max" id="productPrice" name="productPrice" placeholder="Price" value="<?php echo $price;?>">
                  </div>
                  <div class="mb-3">
                     <label for="productDiscount" class="form-label">Discount in %</label>
                     <input required type="number" class="form-control dis" id="productDiscount" name="productDiscount" placeholder="Discount" value="<?php echo $discount;?>">
                  </div>
                  <button class="btn btn-primary" type="submit">Update</button>
                  <p class='btn btn-danger disable_product' title='<?php echo $p_id;?>'>Disable</p>
               </div>
            </form>
            </div>
         </div>
      </div>
      </div>
   </body>
</html>