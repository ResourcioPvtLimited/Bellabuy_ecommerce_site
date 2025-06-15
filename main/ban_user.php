<?php require "../config.php";?>
<!DOCTYPE html>
<html>
   <head>
      
      <?php require "inc/head.php";?>

      <title>Products</title>
   </head>
   <body>
      <?php require "inc/nav.php";?>
      <div class="flex_">
         <?php require "inc/sidebar.php";?>
         <div class="right">
          <div class="padding">
              <h3 class="page-title">All Products</h3>
                  
<br>
<!-- Your HTML and PHP code -->
<!-- Assuming you have your database connection established and session started -->

<!-- Input field for search -->
<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names.." class="form-control">

<!-- Table to display data -->
<table class="table table-striped padding" id="tab">
    <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Name</th>
            <th>Last</th>
            <th>Company</th>
            <th>Phone</th>
            <th>State</th>
            <th>City</th>
            <th>Add1</th>
            <th>Add2</th>
            <th>Pincode</th>
            <th>Landmark</th>
         
             <th>Unban</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Retrieve data from the database
       
        $sql = "SELECT * FROM cust where ban!='0'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $email = $row['email'];
                $name = $row['name'];
                $lname = $row['lname'];
                $company = $row['company'];
                $phone = $row['phone'];
                $state = $row['state'];
                $city = $row['city'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $pincode = $row['pincode'];
                $landmark = $row['landmark'];
                $ban = $row['ban'];

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
                        <td>" . $landmark . "</td>
                        <th><button class='btn btn-danger unban_user' title='".$id."'>Unban</button></th>
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