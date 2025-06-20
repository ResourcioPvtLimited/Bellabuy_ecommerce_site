<?php require "../config.php";
if(isset($_GET['shop_id'])){
    $i_=$_GET['shop_id'];
}else{
     //
}
?>
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
              <h3 class="page-title">Category</h3>
            <form action="submit.php" method="post" enctype="multipart/form-data" class="form-group" style="display: flex;">

        <input type="text" id="text1" name="text1" required class="form-control" placeholder="Category">
       
        <input type="text" id="text2" name="text2" required class="form-control" placeholder="Sub Category (cat1,cat2,....)">
    
        
            <input type="file" id="image" name="image" accept="image/*" required class="form-control">
        
        
        <input type="submit" value="Submit" class="btn btn-success">
    </form>       
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
            <th>Category</th><th>Subcategory</th><th>Delete</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
        // Retrieve data from the database
        
        $sql="SELECT * FROM cat";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $cat = $row['cat'];
                $sub = $row['subcat'];
                

                echo "
                    <tr>
                        <td>" . $id . "</td>
                        <td>" . $cat . "</td>
                        <td>" . $sub . "</td>
                
                        <td><button class='btn btn-danger delete_cat' title='".$id."'>Delete</button></td>
                    </tr>
                ";
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