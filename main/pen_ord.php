<!DOCTYPE html>
<html>
   <head>
      
      <?php require "inc/head.php";?>
      <title>Pending Orders</title>
   </head>
   <body>
      
      <?php require "inc/nav.php";?>
      <div class="flex_">
        
         <?php require "inc/sidebar.php";?>
         <div class="right">
          <div class="padding">
                     <h3 class="page-title">Pending Orders</h3><br>
                  
            
            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names.." class="form-control">

<!-- Table to display data -->
<table class="table table-striped padding" id="tab">
               <thead>
                  <tr>
                     <th>SL NO</th>
                     <th>Order ID</th><th>Order Date</th><th>Coupon Code</th><th>Discount</th><th>Status</th>
                     <th>Tracking ID</th>
                     <th>Update</th>
                     
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $shop_id=$_SESSION['shop_id'];
                     $sql = "SELECT distinct(order_id) as ord_,order_time,t_id,coupon,discount,paid FROM orders WHERE status='ordered' or status='picked'";
                     $result = $conn->query($sql);
                     $sl=1;
                     if ($result->num_rows > 0) {
                         while ($row = $result->fetch_assoc()) {
                             
                             $ord_ = $row['ord_'];$order_time = $row['order_time'];
                             $t_id_=$row['t_id'];$coupon=$row['coupon'];$discount=(int)$row['discount'];
                              $paid = $row['paid'];
                             
                     
                             echo "
                                   <tr>
                                 <td>" . $sl . "</td>
                                 <td><a href='order_details.php?type=ordered&id=".$ord_."'>" . $ord_ . "</a></td><td>" . $order_time . "</td><td>" . $coupon . "</td><td>" . $discount . "</td><td>" . $paid . "</td>
                                 <td><input type='text' class='form-control my_tracking_id' placeholder='Tracking ID' value=".$t_id_."></td>
                                 <td><button class='btn btn-success update_tracking' title=".$ord_.">Update</button></td>
                                 
                             </tr>
                             ";$sl+=1;
                         }
                     } else {
                         echo "<tr><td colspan='9'>0 results</td></tr>";
                     }
                     ?>
                     
               </tbody>
            </table>
            <script>
function myFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("tab");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1]; // Get the second column (Food Name)
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>
            </div>
         </div>
      </div>
      </div>
   </body>
</html>