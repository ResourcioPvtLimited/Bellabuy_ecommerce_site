<?php
require "../config.php";
require "inc/head.php";
session_start();

if (!isset($_SESSION['shop_id'])) {
    die("Unauthorized access");
}

$ord_id = $_GET['id'] ?? '';
$status = $_GET['type'] ?? '';

if (!$ord_id) {
    die("Invalid request");
}

$shop_id = $_SESSION['shop_id'];

// Get order details
$order_sql = "SELECT o.*, c.name, c.lname, c.email, c.phone, c.address1, c.address2, c.city, c.state, c.pincode, 
              c.landmark, c.company, s.name as shop_name
              FROM orders o
              JOIN cust c ON o.u_id = c.id
              JOIN shop s ON o.shop_id = s.id
              WHERE o.order_id = '$ord_id' AND o.shop_id = '$shop_id'
              LIMIT 1";

$order_result = $conn->query($order_sql);
$order = $order_result->fetch_assoc();

// Get order items
$items_sql = "SELECT o.*, i.name, i.brand, i.img1, i.colour, i.price as original_price
              FROM orders o
              JOIN item i ON o.p_id = i.id
              WHERE o.order_id = '$ord_id' AND o.shop_id = '$shop_id'";
$items_result = $conn->query($items_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $ord_id; ?> - Vendor Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
        }
        
       
        
        .vendor-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .order-status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-ordered {
            background-color: #7f8c8d;
            color: white;
        }
        
        .status-processing {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .status-shipped {
            background-color: #9b59b6;
            color: white;
        }
        
        .status-delivered {
            background-color: var(--success-color);
            color: white;
        }
        
        .status-cancelled {
            background-color: var(--danger-color);
            color: white;
        }
        
        .order-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .order-card h4 {
            color: var(--primary-color);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: 600;
            width: 150px;
            color: #7f8c8d;
        }
        
        .info-value {
            flex: 1;
        }
        
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
            margin: 20px 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e0e0e0;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #e0e0e0;
        }
        
        .timeline-item.completed::before {
            background-color: var(--success-color);
        }
        
        .timeline-item.active::before {
            background-color: var(--secondary-color);
        }
        
        .timeline-date {
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
        }
        
        .payment-row.total {
            font-weight: 600;
            border-bottom: none;
            font-size: 18px;
            margin-top: 10px;
        }
        
        .action-btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
            }
            
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <?php require "inc/sidebar.php"; ?>

   <div class="content">
        <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Order #<?php echo $ord_id; ?></h2>
            <span class="order-status-badge status-<?php echo strtolower($order['status']); ?>">
                <?php echo ucfirst($order['status']); ?>
            </span>
        </div>

        <!-- Order Timeline -->
        <div class="order-card">
            <h4>Order Status Timeline</h4>
            <div class="timeline">
                <div class="timeline-item completed">
                    <h5>Order Placed</h5>
                    <p class="timeline-date"><?php echo date('M j, Y g:i A', strtotime($order['order_time'])); ?></p>
                </div>
                
                <?php if ($order['pickup_time']): ?>
                <div class="timeline-item <?php echo ($order['status'] == 'delivered' || $order['status'] == 'shipped') ? 'completed' : ''; ?>">
                    <h5>Picked Up</h5>
                    <p class="timeline-date"><?php echo date('M j, Y g:i A', strtotime($order['pickup_time'])); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($order['status'] == 'shipped' || $order['status'] == 'delivered'): ?>
                <div class="timeline-item <?php echo ($order['status'] == 'delivered') ? 'completed' : 'active'; ?>">
                    <h5>Shipped</h5>
                    <?php if ($order['t_id']): ?>
                    <p>Tracking: <?php echo $order['t_id']; ?></p>
                    <?php endif; ?>
                    <?php if ($order['pickup_time']): ?>
                    <p class="timeline-date"><?php echo date('M j, Y g:i A', strtotime($order['pickup_time'])); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($order['status'] == 'delivered'): ?>
                <div class="timeline-item completed">
                    <h5>Delivered</h5>
                    <p class="timeline-date"><?php echo date('M j, Y g:i A', strtotime($order['del_time'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="d-flex flex-wrap mt-3">
                <button class="btn btn-primary action-btn" onclick="printInvoice()">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
                
                <div class="dropdown action-btn">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-sync-alt"></i> Update Status
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                        <li><a class="dropdown-item" href="#" onclick="updateStatus('processing')">Processing</a></li>
                        <li><a class="dropdown-item" href="#" onclick="updateStatus('shipped')">Packed</a></li>
                        <li><a class="dropdown-item" href="#" onclick="updateStatus('delivered')">Shipted</a></li>
                      
                    </ul>
                </div>
                
              

            </div>
        </div>

        <div class="row">
            <!-- Customer Information -->
            <div class="col-md-6">
                <div class="order-card">
                    <h4>Customer Information</h4>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?php echo $order['name'] . ' ' . $order['lname']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo $order['email']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value"><?php echo $order['phone']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Company:</span>
                        <span class="info-value"><?php echo $order['company'] ?: 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Shipping Information -->
            <div class="col-md-6">
                <div class="order-card">
                    <h4>Shipping Information</h4>
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value">
                            <?php echo $order['address1']; ?><br>
                            <?php if ($order['address2']) echo $order['address2'] . '<br>'; ?>
                            <?php echo $order['city'] . ', ' . $order['state'] . ' ' . $order['pincode']; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Landmark:</span>
                        <span class="info-value"><?php echo $order['landmark'] ?: 'N/A'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value"><?php echo strtoupper($order['paid']); ?></span>
                    </div>
                    <?php if ($order['t_id']): ?>
                    <div class="info-row">
                        <span class="info-label">Tracking Number:</span>
                        <span class="info-value"><?php echo $order['t_id']; ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="order-card">
            <h4>Order Items</h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 1;
                        $subtotal = 0;
                        
                        if ($items_result && $items_result->num_rows > 0) {
                            while ($item = $items_result->fetch_assoc()) {
                                $total = $item['price'] * $item['qty'];
                                $subtotal += $total;
                                $imgPath = !empty($item['img1']) ? "../prod/" . $item['img1'] : "../prod/no-image.png";
                        ?>
                        <tr>
                            <td><?php echo $sl++; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $imgPath; ?>" class="product-img me-3">
                                    <div>
                                        <a href="../single-product.php?id=<?php echo $item['p_id']; ?>" target="_blank">
                                            <?php echo $item['name']; ?>
                                        </a>
                                        <?php if ($item['discount']): ?>
                                        <div class="text-success small">
                                            <?php echo $item['discount']; ?>% OFF
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $item['brand'] ?: 'N/A'; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['qty']; ?></td>
                            <td><?php echo $item['size'] ?: 'N/A'; ?></td>
                            <td><?php echo $item['colour'] ?: 'N/A'; ?></td>
                            <td>$<?php echo number_format($total, 2); ?></td>
                        </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="8" class="text-center">No items found in this order</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Order Summary -->
            <div class="row justify-content-end mt-4">
                <div class="col-md-5">
                    <div class="order-card">
                        <h4>Order Summary</h4>
                        <div class="payment-row">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <?php if ($order['discount']): ?>
                        <div class="payment-row">
                            <span>Discount (<?php echo $order['coupon']; ?>):</span>
                            <span class="text-danger">-$<?php echo number_format($order['discount'], 2); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="payment-row">
                            <span>Shipping:</span>
                            <span>$5.00</span>
                        </div>
                        <div class="payment-row">
                            <span>Tax:</span>
                            <span>$<?php echo number_format($subtotal * 0.1, 2); ?></span>
                        </div>
                        <div class="payment-row total">
                            <span>Total:</span>
                            <span>$<?php echo number_format($subtotal + ($subtotal * 0.1) + 5 - ($order['discount'] ?? 0), 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
    </div>

    <!-- Tracking Modal -->
    <div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackingModalLabel">Add Tracking Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="trackingForm">
                        <div class="mb-3">
                            <label for="trackingNumber" class="form-label">Tracking Number</label>
                            <input type="text" class="form-control" id="trackingNumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="carrier" class="form-label">Shipping Carrier</label>
                            <select class="form-select" id="carrier" required>
                                <option value="">Select Carrier</option>
                                <option value="fedex">FedEx</option>
                                <option value="ups">UPS</option>
                                <option value="usps">USPS</option>
                                <option value="dhl">DHL</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveTracking()">Save Tracking</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Refund Modal -->
    <div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel">Process Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="refundForm">
                        <div class="mb-3">
                            <label for="refundAmount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="refundAmount" 
                                   value="<?php echo number_format($subtotal + ($subtotal * 0.1) + 5 - ($order['discount'] ?? 0), 2); ?>" 
                                   step="0.01" min="0" max="<?php echo number_format($subtotal + ($subtotal * 0.1) + 5 - ($order['discount'] ?? 0), 2); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="refundReason" class="form-label">Reason</label>
                            <select class="form-select" id="refundReason" required>
                                <option value="">Select Reason</option>
                                <option value="customer_request">Customer Request</option>
                                <option value="out_of_stock">Out of Stock</option>
                                <option value="defective">Defective Product</option>
                                <option value="wrong_item">Wrong Item Shipped</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="refundNote" class="form-label">Notes</label>
                            <textarea class="form-control" id="refundNote" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="processRefund()">Process Refund</button>
                </div>
            </div>
        </div>
    </div>
   </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printInvoice() {
            // In a real implementation, this would open a print-friendly version
            alert('Invoice printing would be implemented here');
        }
        
        function updateStatus(newStatus) {
            if (confirm(`Are you sure you want to update this order to "${newStatus}"?`)) {
                // In a real implementation, this would make an AJAX call
                alert(`Order status would be updated to ${newStatus}`);
                window.location.reload();
            }
        }
        
        function saveTracking() {
            const trackingNumber = document.getElementById('trackingNumber').value;
            const carrier = document.getElementById('carrier').value;
            
            if (!trackingNumber || !carrier) {
                alert('Please fill in all fields');
                return;
            }
            
            // In a real implementation, this would make an AJAX call
            alert(`Tracking info would be saved: ${trackingNumber} (${carrier})`);
            
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('trackingModal'));
            modal.hide();
            
            window.location.reload();
        }
        
        function processRefund() {
            const amount = document.getElementById('refundAmount').value;
            const reason = document.getElementById('refundReason').value;
            const note = document.getElementById('refundNote').value;
            
            if (!amount || !reason) {
                alert('Please fill in all required fields');
                return;
            }
            
            if (confirm(`Are you sure you want to process a refund of $${amount}?`)) {
                // In a real implementation, this would make an AJAX call
                alert(`Refund would be processed for $${amount} with reason: ${reason}`);
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('refundModal'));
                modal.hide();
                
                window.location.reload();
            }
        }
    </script>
</body>
</html>