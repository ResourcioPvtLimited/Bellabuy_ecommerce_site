<?php
require "../config.php";

// Handle approve/cancel
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    // Fetch the request details
    $request = $conn->query("SELECT * FROM withdraw_requests WHERE id='$id'")->fetch_assoc();
    $shop_id = $request['shop_id'];
    $amount = floatval($request['amount']);

    if ($action == 'success' && $request['status'] == 'pending') {
        // Only approve if it's still pending
        $conn->query("UPDATE shop SET 
            withdrawal_balance = withdrawal_balance + $amount, 
            total_balance = total_balance - $amount 
            WHERE id = $shop_id");

        $conn->query("UPDATE withdraw_requests SET status='success' WHERE id=$id");
    } elseif ($action == 'cancel' && $request['status'] == 'pending') {
        // Only cancel if it's still pending
        $conn->query("UPDATE withdraw_requests SET status='cancel' WHERE id=$id");
    }
}

// Fetch all requests
$result = $conn->query("SELECT * FROM withdraw_requests ORDER BY requested_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <?php require "inc/head.php"; ?>
    <title>Withdraw Requests</title>
</head>
<body>
<?php require "inc/nav.php"; ?>
<div class="flex_">
    <?php require "inc/sidebar.php"; ?>
    <div class="right">
        <div class="padding">
            <h3 class="page-title">Withdrawal Requests</h3><br>

            <table class="table table-striped padding" id="tab">
                <thead>
                    <tr>
                        <th>Shop ID</th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['shop_id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['number'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= number_format($row['amount'], 2) ?> ৳</td>
                            <td><strong><?= ucfirst($row['status']) ?></strong></td>
                            <td><?= $row['requested_at'] ?></td>
                            <td>
                                <?php if ($row['status'] == 'pending'): ?>
                                    <a href="?action=success&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                                    <a href="?action=cancel&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
                                <?php else: ?>
                                    <em>Done</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
