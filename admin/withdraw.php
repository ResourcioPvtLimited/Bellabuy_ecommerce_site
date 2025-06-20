<?php
require "../config.php";
session_start();
$shop_id = $_SESSION['shop_id'] ?? 0;

// Security check
if (!$shop_id) {
    die("Unauthorized access.");
}

// Fetch shop info
$shop = $conn->query("SELECT name, email, total_balance FROM shop WHERE id='$shop_id'")->fetch_assoc();
$name = $shop['name'];
$email = $shop['email'];
$balance = floatval($shop['total_balance']);

// Fetch pending withdrawals
$pending_result = $conn->query("SELECT SUM(amount) as total_pending FROM withdraw_requests WHERE shop_id='$shop_id' AND status='pending'");
$pending_row = $pending_result->fetch_assoc();
$pending_amount = floatval($pending_row['total_pending']);
$available_balance = $balance - $pending_amount;

// Handle AJAX form
if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
    $number = $_POST['number'];
    $amount = floatval($_POST['amount']);

    if ($amount <= $available_balance && $amount > 0) {
        $stmt = $conn->prepare("INSERT INTO withdraw_requests (shop_id, name, number, email, amount) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssd", $shop_id, $name, $number, $email, $amount);
        $stmt->execute();
        echo "<div class='alert alert-success'>✅ Thank you! Your withdrawal request has been submitted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Your withdrawal amount is bigger than your total balance including pending payments.</div>";
    }
    exit;
}

// Handle AJAX history reload
if (isset($_POST['reload_history']) && $_POST['reload_history'] === '1') {
    $history = $conn->query("SELECT * FROM withdraw_requests WHERE shop_id='$shop_id' ORDER BY requested_at DESC");
    echo "<table class='table table-bordered'>
            <thead>
                <tr><th>No.</th><th>Amount</th><th>Status</th><th>Requested At</th></tr>
            </thead><tbody>";
    $no = 1;
    while ($row = $history->fetch_assoc()) {
        echo "<tr>
                <td>" . $no++ . "</td>
                <td>" . number_format($row['amount'], 2) . " ৳</td>
                <td><strong>" . ucfirst($row['status']) . "</strong></td>
                <td>" . $row['requested_at'] . "</td>
            </tr>";
    }
    echo "</tbody></table>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require "inc/head.php"; ?>
    <title>Withdraw Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f6fa;
        }

        

        h1, h3, h4 {
            color: #333;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 0.65rem 0.75rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 1rem;
            box-sizing: border-box;
        }

        label {
            font-weight: 500;
            margin-bottom: 0.3rem;
            display: inline-block;
            color: #444;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
            padding: 0.65rem 1.2rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        table th, table td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f0f0f0;
            font-weight: 600;
        }

        .table-striped tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .alert {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }

        /* Responsive */
        @media screen and (max-width: 760px) {
            .content {
                padding: 1rem;
            }

            .form-control {
                font-size: 0.95rem;
                padding: 0.6rem 0.75rem;
            }

            .btn-primary {
                width: 100%;
                text-align: center;
            }

            table th, table td {
                font-size: 0.9rem;
                padding: 0.6rem;
            }

            h1, h3, h4 {
                font-size: 1.25rem;
            }
        }

        @media screen and (max-width: 460px) {
            h1, h3, h4 {
                font-size: 1.1rem;
            }

            .btn-primary {
                font-size: 0.95rem;
            }

            label {
                font-size: 0.95rem;
            }

            .form-control {
                font-size: 0.9rem;
            }
        }

        @media screen and (max-width: 360px) {
            .form-control, .btn-primary {
                font-size: 0.85rem;
            }

            table th, table td {
                font-size: 0.8rem;
                padding: 0.5rem;
            }

            h3, h4 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

<?php require "inc/sidebar.php"; ?>

<div class="content">
    <h1>Dashboard</h1>
    <h3>Withdraw Request</h3><br>

    <form id="withdrawForm">
        <label>Name</label>
        <input type="text" name="name" value="<?= $name ?>" class="form-control" disabled>

        <label>Email</label>
        <input type="email" name="email" value="<?= $email ?>" class="form-control" disabled>

        <label>Phone / Number</label>
        <input type="text" name="number" class="form-control" required>

        <label>Total Balance: <strong><?= number_format($balance, 2) ?> ৳</strong></label><br>
        <label>Pending Requests: <strong><?= number_format($pending_amount, 2) ?> ৳</strong></label><br>
        <label>Available Balance for Withdraw: <strong><?= number_format($available_balance, 2) ?> ৳</strong></label><br>

        <label>Amount to Withdraw</label>
        <input type="number" step="0.01" name="amount" class="form-control" required>

        <div id="response"></div>

        <button class="btn btn-primary" type="submit">Submit Request</button>
    </form>

    <hr>
    <h4>Request History</h4>
    <div id="history">
        <?php
        $history = $conn->query("SELECT * FROM withdraw_requests WHERE shop_id='$shop_id' ORDER BY requested_at DESC");
        echo "<table class='table table-striped padding'>
                <thead>
                    <tr><th>No.</th><th>Amount</th><th>Status</th><th>Requested At</th></tr>
                </thead><tbody>";
        $no = 1;
        while ($row = $history->fetch_assoc()) {
            echo "<tr>
                    <td>" . $no++ . "</td>
                    <td>" . number_format($row['amount'], 2) . " ৳</td>
                    <td><strong>" . ucfirst($row['status']) . "</strong></td>
                    <td>" . $row['requested_at'] . "</td>
                </tr>";
        }
        echo "</tbody></table>";
        ?>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#withdrawForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&ajax=1';

        $.post('', formData, function (response) {
            $('#response').html(response);

            if (response.includes('✅')) {
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        });
    });
});
</script>

</body>
</html>
