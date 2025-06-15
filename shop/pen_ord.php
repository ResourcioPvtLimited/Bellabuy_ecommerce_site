<?php
require "../config.php";
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <?php require "inc/head.php"; ?>
    <title>Pending Orders</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        td, th {
            vertical-align: middle !important;
        }
        .prod-img {
            height: 50px;
            width: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .custom-toast {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: none;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
   
        <?php require "inc/sidebar.php"; ?>
       
            <div class="content">
                <h3 class="page-title">Pending Orders</h3><br>
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by Order ID or Product Name..." class="form-control mb-3">

                <table class="table table-striped padding" id="tab">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order ID</th>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Coupon</th>
                            <th>Discount</th>
                            <th>Tracking ID</th>
                            <th>Order Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $shop_id = $_SESSION['shop_id'];
                        $sql = "
                            SELECT o.order_id, o.order_time, o.t_id, o.coupon, o.discount, o.status, o.price, o.qty,
                                   i.name AS product_name, i.img1
                            FROM orders o
                            LEFT JOIN item i ON o.p_id = i.id
                            WHERE o.shop_id='$shop_id' AND (o.status='ordered' OR o.status='picked')
                            ORDER BY o.order_time DESC
                        ";
                        $result = $conn->query($sql);
                        $sl = 1;

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $order_id = $row['order_id'];
                                $order_time = $row['order_time'];
                                $product = $row['product_name'] ?? 'Unknown';
                                $img = !empty($row['img1']) ? "../prod/" . $row['img1'] : "../prod/no-image.png";
                                $qty = $row['qty'];
                                $price = $row['price'];
                                $status = ucfirst($row['status']);
                                $coupon = $row['coupon'] ?? '-';
                                $discount = $row['discount'] ?? '0';
                                $t_id = $row['t_id'] ?? '';

                                echo "
                                    <tr>
                                        <td>{$sl}</td>
                                        <td><a href='order_details.php?type=ordered&id={$order_id}'>{$order_id}</a></td>
                                        <td><img src='{$img}' class='prod-img'></td>
                                        <td>{$product}</td>
                                        <td>{$qty}</td>
                                        <td>{$price}</td>
                                        <td>{$status}</td>
                                        <td>{$coupon}</td>
                                        <td>{$discount}</td>
                                        <td>
                                            <div class='d-flex align-items-center'>
                                                <input type='text' class='form-control form-control-sm tracking-value' value='{$t_id}' readonly style='width: 120px; margin-right: 5px;'>
                                                <button class='btn btn-sm btn-outline-secondary copy-btn' title='Copy' data-value='{$t_id}'>📋</button>
                                            </div>
                                        </td>
                                        <td>{$order_time}</td>
                                        <td><a href='order_details.php?type=ordered&id={$order_id}' class='btn btn-primary btn-sm'>Details</a></td>
                                    </tr>
                                ";
                                $sl++;
                            }
                        } else {
                            echo "<tr><td colspan='12'>No pending orders found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="custom-toast">Tracking ID copied!</div>

    <script>
        function myFunction() {
            const input = document.getElementById("myInput").value.toUpperCase();
            const rows = document.querySelector("#tab tbody").getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                const orderId = rows[i].getElementsByTagName("td")[1];
                const product = rows[i].getElementsByTagName("td")[3];

                if (orderId && product) {
                    const idText = orderId.textContent || orderId.innerText;
                    const prodText = product.textContent || product.innerText;
                    const match = idText.toUpperCase().includes(input) || prodText.toUpperCase().includes(input);
                    rows[i].style.display = match ? "" : "none";
                }
            }
        }

        // Copy to clipboard
        $(document).on('click', '.copy-btn', function () {
            const value = $(this).data('value');
            if (!value) return;

            navigator.clipboard.writeText(value).then(function () {
                $('#toast').stop(true, true).fadeIn().delay(2000).fadeOut();
            }, function () {
                alert("Copy failed!");
            });
        });
    </script>
</body>
</html>
