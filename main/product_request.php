<?php
require "../config.php";
session_start();

// Handle enable product AJAX request
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['action'] === "enable_product") {
    $id = intval($_POST['id']);
    $query = "UPDATE item SET disable='0', status='accepted' WHERE id='$id'";
    echo $conn->query($query) ? 1 : 0;
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require "inc/head.php"; ?>
    <title>On Request Products</title>
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
        .prod-img {
            height: 50px;
            width: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<?php require "inc/nav.php"; ?>
<div class="flex_">
    <?php require "inc/sidebar.php"; ?>
    <div class="right">
        <div class="padding">
    <h3 class="page-title">On Request Products</h3><br>
    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for product name..." class="form-control mb-3">

    <table class="table table-striped" id="tab">
        <thead>
            <tr>
                <th>Image</th>
                <th>ID</th>
                <th>Product Name</th>
                 <th>Shop Name</th>
                  <th>Shop ID</th>
                <th>Price</th>
                <th>Person</th>
                <th>Discount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM `item` WHERE status='on request'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $name = $row['name'];
                $shop_name = $row['shop'];
                $shop_id = $row['shop_id'];
                $price = $row['price'];
                $person = $row['person'];
                $discount = $row['discount'];
                $img1 = $row['img1'];
                $imgPath = !empty($img1) ? "../prod/" . $img1 : "../prod/no-image.png";

                echo "
                    <tr title='{$id}'>
                        <td><img src='{$imgPath}' class='prod-img'></td>
                        <td>{$id}</td>
                        <td><a href='single.php?id={$id}'>{$name}</a></td>
                        <td>{$shop_name}</td>
                        <td>{$shop_id}</td>
                        <td>{$price}</td>
                        <td>{$person}</td>
                        <td>{$discount}%</td>
                        <td><button class='btn btn-success enable_product' title='{$id}'>Accept</button></td>
                    </tr>
                ";
            }
        } else {
            echo "<tr><td colspan='7'>No products found with status 'on request'.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <div id="toastContainer"></div>

    <script>
    function myFunction() {
        var input = document.getElementById("myInput");
        var filter = input.value.toUpperCase();
        var table = document.getElementById("tab");
        var tr = table.getElementsByTagName("tr");

        for (var i = 0; i < tr.length; i++) {
            var td = tr[i].getElementsByTagName("td")[2];
            if (td) {
                var txtValue = td.textContent || td.innerText;
                tr[i].style.display = (txtValue.toUpperCase().indexOf(filter) > -1) ? "" : "none";
            }
        }
    }

    function showToast(message, success = true) {
        const toast = $(`<div class="toast custom-toast ${success ? 'toast-success' : 'toast-error'}" role="alert"><div class="toast-body">${message}</div></div>`);
        $("#toastContainer").append(toast);
        toast.toast({ delay: 3000 });
        toast.toast('show');
        setTimeout(() => { toast.fadeOut(() => toast.remove()); }, 3000);
    }

    $(document).ready(function () {
        $(".enable_product").click(function () {
            if (!confirm("Are you sure you want to enable this product?")) return;

            var btn = $(this);
            var id = btn.attr("title");

            $.post("", { action: "enable_product", id: id }, function (res) {
                if (res == 1) {
                    btn.closest("tr").fadeOut();
                    showToast("Product enabled successfully", true);
                } else {
                    showToast("Failed to enable product", false);
                }
            });
        });
    });
    </script>
</div>
    </div>
</div>

</body>
</html>
