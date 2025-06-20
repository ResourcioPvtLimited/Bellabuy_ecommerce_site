<?php require "../config.php";?>
<!DOCTYPE html>
<html>
   <head>
      
      <?php require "inc/head.php";?>

      <title>Coupons</title>
   </head>
   <body>
      <?php require "inc/nav.php";?>
      <div class="flex_">
         <?php require "inc/sidebar.php";?>
         <div class="right">
          <div class="padding">
              <h3 class="page-title">Manage Coupons</h3>
                  
<br>
 <form class="coup flex_" method="post">
    <input type="text" name="code" class="form-control" placeholder="Code">
    <input type="number"  name="discount" class="form-control" placeholder="Discount" >

    <select  name="type_" class="form-control">
      <option value="PERCENT">PERCENT</option>
      <option value="AMOUNT">AMOUNT</option>
    </select>

    <input type="number"  name="maxUse" class="form-control" placeholder="Max Use">
    
  
    <input  name="description" class="form-control" placeholder="Description">
 <select  class="form-control" name="cond">
    <option value="3">-- CONDITION--</option>
      <option value="1">1</option>
      <option value="2">2</option><option value="3">3</option>
    </select>
    

    <input type="number"  name="maxCart" class="form-control" placeholder="Max Cart Value" >

    <button type="submit" class="btn btn-success">Create Coupon</button>
  </form>
 
<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names.." class="form-control">

<!-- Table to display data -->
<table class="table table-striped padding" id="tab">
    <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Discount</th>
            <th>Type</th>
            <th>Max Use</th>
            <th>Used Yet</th>
            <th>Expired</th>
            <th>Date</th>
            <th>Description</th>
            <th>Condition</th>
            <th>Cart Value(2)</th>
            <th>Expire</th>
         
             <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Retrieve data from the database
        $sql = "SELECT * FROM coupon";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $email = $row['code'];
                $name = $row['discount'];
                $lname = $row['type'];
                $company = $row['max_use'];
                $phone = $row['used_yet'];
                $state = $row['expired'];
                $city = $row['date'];
                $address1 = $row['des'];
                $address2 = $row['cond'];
                $pincode = $row['max_cart'];
                

                echo "
                    <tr>
                        <td>" . $id . "</td>
                        <td>" . $email . "</td>
                        <td>" . $name . "</td>
                        <td>" . $lname . "</td>
                        <td>" . $company . "</td>
                        <td>" . $phone . "</td>
                        <td>" . $state . "</td>
                        <td>" . $city . "</td>
                        <td>" . $address1 . "</td>
                        <td>" . $address2 . "</td>
                        <td>" . $pincode . "</td>
                        <td><button class='btn btn-warning expire_coupon' title='".$id."'>Expire</button></td>
                        <th><button class='btn btn-danger delete_coupon' title='".$id."'>Delete</button></th>
                    </tr>
                ";
            }
        } else {
            echo "<tr><td colspan='13'>0 results</td></tr>";
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