<?php
require "../config.php";
require "inc/head.php";
require "inc/nav.php";
require "inc/sidebar.php";

// Fetch shop info
if (isset($_GET['shop_id'])) {
    $shop_id = $_GET['shop_id'];
    $result = $conn->query("SELECT * FROM shop WHERE id = '$shop_id'");
    if ($result->num_rows == 1) {
        $shop = $result->fetch_assoc();
    } else {
        die("Shop not found.");
    }
} else {
    die("No shop ID provided.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $percentage = $_POST['percentage'];
    $total_earning = $_POST['total_earning'];

    $stmt = $conn->prepare("UPDATE shop SET name=?, email=?, address=?, percentage=?, total_earning=? WHERE id=?");
    $stmt->bind_param("sssddi", $name, $email, $address, $percentage, $total_earning, $shop_id);
    if ($stmt->execute()) {
        echo "<script>alert('Shop updated successfully'); location.href='shop.php';</script>";
    } else {
        echo "<script>alert('Update failed');</script>";
    }
}
?>

<div class="right">
    <div class="padding">
        <h3 class="page-title">Edit Shop #<?php echo $shop_id; ?></h3>
        <form method="post">
            <label>Name</label><br>
            <input type="text" name="name" value="<?php echo $shop['name']; ?>" class="form-control" required><br>

            <label>Email</label><br>
            <input type="email" name="email" value="<?php echo $shop['email']; ?>" class="form-control" required><br>

            <label>Address</label><br>
            <input type="text" name="address" value="<?php echo $shop['address']; ?>" class="form-control" required><br>

            <label>Percentage</label><br>
            <input type="number" step="0.01" name="percentage" value="<?php echo $shop['percentage']; ?>" class="form-control"><br>

            <label>Total Earning</label><br>
            <input type="number" step="0.01" name="total_earning" value="<?php echo $shop['total_earning']; ?>" class="form-control"><br>

            <button type="submit" class="btn btn-primary">Update Shop</button>
        </form>
    </div>
</div>
</body>
</html>
