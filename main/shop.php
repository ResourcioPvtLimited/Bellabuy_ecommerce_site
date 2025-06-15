<?php require "../config.php";

if(isset($_GET['shop_id'])){
    $i_ = $_GET['shop_id'];
    $sql = "SELECT * FROM shop WHERE ban='0' AND pending='0' AND id='$i_'";
} else {
    $sql = "SELECT * FROM shop WHERE ban='0' AND pending='0'";
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php require "inc/head.php"; ?>
    <title>Active Shops</title>
</head>
<body>
<?php require "inc/nav.php"; ?>
<div class="flex_">
    <?php require "inc/sidebar.php"; ?>
    <div class="right">
        <div class="padding">
            <h3 class="page-title">Active Shops</h3>
            <br>

            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names..." class="form-control">

            <table class="table table-striped padding" id="tab">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Locate</th>
                        <th>Ban Shop</th>
                        <th>Percentage</th>
                        <th>Total Earning</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $id = $row['id'];
                            $name = $row['name'];
                            $email = $row['email'];
                            $phone = $row['phone'];
                            $address = $row['address'];
                            $lat = $row['lat'];
                            $lon = $row['lon'];
                            $percentage = $row['percentage'];
                            $total_earning = $row['total_earning'];

                            echo "
                                <tr>
                                    <td>{$id}</td>
                                    <td><a href='products.php?shop_id={$id}'>{$name}</a></td>
                                    <td>{$email}</td>
                                    <td>{$phone}</td>
                                    <td>{$address}</td>
                                    <td>{$lat}</td>
                                    <td>{$lon}</td>
                                    <td><a href='https://maps.apple.com/?q={$lat},{$lon}' target='_blank'><button class='btn btn-success'>Map</button></a></td>
                                    <td><button class='btn btn-danger ban_shop' title='{$id}'>Ban</button></td>
                                    <td>{$percentage}%</td>
                                    <td>$ {$total_earning}</td>
                                    <td><a href='edit_shop.php?shop_id={$id}'><button class='btn btn-warning'>Edit</button></a></td>
                                </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='12'>0 results</td></tr>";
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

                for (i = 1; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[1]; // Shop name column
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
                    }
                }
            }
            </script>

        </div>
    </div>
</div>
</body>
</html>
