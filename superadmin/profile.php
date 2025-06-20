<!DOCTYPE html>
<html>
   <head>
      
      <?php require "inc/head.php";?>

      <title>Profile</title>
   </head>
   <body>
      
      <?php require "inc/nav.php";?>
      <div class="flex_">
         
         <?php require "inc/sidebar.php";?>
         <div class="right">
          <div class="padding">
            
            <?php
                  $shop_id=$_SESSION['shop_id'];
                     $sql = "SELECT * FROM shop where id='$shop_id'";
                     $result = $conn->query($sql);
                   
                     if ($result->num_rows > 0) {
                         while ($row = $result->fetch_assoc()) {
                             
                             $name = $row['name']; $email = $row['email'];
                             $phone = $row['phone'];$adr = $row['address'];
                             
                         }
                     }
                     ?>
                     <div class="flex_">
            <form method="post" class="update_profile" style="flex:1">
               <h3 class="page-title">Update Profile</h3><br>
               <div>
                  
                  <div class="mb-3">
                     <label for="productName" class="form-label">Shop Name</label>
                     <input required type="text" class="form-control name" id="productName" name="name" placeholder="Name" value="<?php echo $name;?>">
                  </div>
                  <div class="mb-3">
                     <label for="des" class="form-label">Email</label>
                     <input required type="email" class="form-control email" id="productDiscount" name="productDiscount" placeholder="Email" value="<?php echo $email;?>">
                  </div>
                  <div class="mb-3">
                     <label for="productPrice" class="form-label">Phone</label>
                     <input required type="number" class="form-control phone" id="productPrice" name="productPrice" placeholder="Phone" value="<?php echo $phone;?>">
                  </div>
                  <div class="mb-3">
                     <label for="productDiscount" class="form-label">Address</label>
                     <textarea class="form-control adr" name="description" placeholder="Address" required ><?php echo $adr;?></textarea>
                  </div>
                  <button class="btn btn-primary " type="submit">Update</button>
                  
               </div>
            </form>
            <div class="space"></div>
            <form method="post" class="update_pass" style="flex:1">
               <h3>Change Password</h3><br>
               <div>
                  
                  <div class="mb-3">
                     <label for="productName" class="form-label">New Password</label>
                     <input required type="password" class="form-control pass" id="productName" name="name" placeholder="New Password">
                  </div>
                  
                  <button class="btn btn-primary " type="submit">Change Password</button>
                  
               </div>
            </form>
         </div>
            </div>
         </div>
      </div>
      </div>
   </body>
</html>