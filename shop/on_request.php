<?php
require "../config.php";
session_start();

$shop_id = $_SESSION['shop_id'] ?? 0;
if (!$shop_id) die("Unauthorized access");
?>

<!DOCTYPE html>
<html>
<head>
    <?php require "inc/head.php"; ?>
    <title>My Product Status</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        td, th { vertical-align: middle !important; }
        .prod-img {
            height: 50px;
            width: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .status-badge {
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
        }
        .status-accepted {
            background-color: #28a745; /* Green */
        }
        .status-other {
            background-color: #dc3545; /* Red for anything else */
        }
    </style>
</head>
<body>

<?php require "inc/sidebar.php"; ?>

<div class="content">
    <h3 class="page-title">My Product Status</h3><br>
    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search product name..." class="form-control mb-3">

    <table class="table table-striped" id="tab">
        <thead>
            <tr>
                <th>Image</th>
                <th>ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Person</th>
                <th>Discount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM item WHERE shop_id='$shop_id' ORDER BY id DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $name = $row['name'];
                $price = $row['price'];
                $person = $row['person'];
                $discount = $row['discount'];
                $status = $row['status'];
                $img1 = $row['img1'];
                $imgPath = !empty($img1) ? "../prod/" . $img1 : "../prod/no-image.png";

                $statusClass = ($status === 'accepted') ? 'status-accepted' : 'status-other';

                echo "
                    <tr>
                        <td><img src='{$imgPath}' class='prod-img'></td>
                        <td>{$id}</td>
                        <td>{$name}</td>
                        <td>{$price}</td>
                        <td>{$person}</td>
                        <td>{$discount}%</td>
                        <td><span class='status-badge {$statusClass}'>" . ucfirst($status) . "</span></td>
                    </tr>
                ";
            }
        } else {
            echo "<tr><td colspan='7'>No products found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
function myFunction() {
    var input = document.getElementById("myInput");
    var filter = input.value.toUpperCase();
    var table = document.getElementById("tab");
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName("td")[2];
        if (td) {
            var txtValue = td.textContent || td.innerText;
            tr[i].style.display = (txtValue.toUpperCase().indexOf(filter) > -1) ? "" : "none";
        }
    }
}
</script>

</body>
</html>
