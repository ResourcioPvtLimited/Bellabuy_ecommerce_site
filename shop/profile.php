<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "alokito2_sadi", "sadi9507@#", "alokito2_ecom");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$shop_id = $_SESSION['shop_id'] ?? 0;
if (!$shop_id) {
    die("Unauthorized access");
}

// Fetch shop info with error handling
$shop_query = $conn->query("SELECT * FROM shop WHERE id='$shop_id'");
if (!$shop_query) {
    die("Error fetching shop data: " . $conn->error);
}
$shop = $shop_query->fetch_assoc();
if (!$shop) {
    die("Shop not found");
}

// Get shop stats
$total_products = 0;
$completed_orders = 0;
$average_rating = 0.0;

$product_result = $conn->query("SELECT COUNT(*) as total FROM item WHERE shop_id='$shop_id'");
if ($product_result) {
    $total_products = $product_result->fetch_assoc()['total'];
}

$order_result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE shop_id='$shop_id' AND status='completed'");
if ($order_result) {
    $completed_orders = $order_result->fetch_assoc()['total'];
}

$rating_result = $conn->query("SELECT AVG(rating) as avg FROM reviews WHERE shop_id='$shop_id'");
if ($rating_result) {
    $average_rating = $rating_result->fetch_assoc()['avg'] ?? 0.0;
}

// Delete old image helper
function deleteOldImage($filename) {
    if ($filename && file_exists("Vendor_profile/" . $filename)) {
        unlink("Vendor_profile/" . $filename);
    }
}

// Handle profile and password updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    $response = ['status' => 'error', 'msg' => 'Something went wrong'];

    if ($_POST['ajax'] === 'update_profile') {
        $name = $conn->real_escape_string($_POST['name']);
        $vendor_name = $conn->real_escape_string($_POST['vendor_name']);
        $address = $conn->real_escape_string($_POST['address']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $email = $conn->real_escape_string($_POST['email']);

        $shop_logo = $shop['shop_logo'];
        $shop_banner = $shop['shop_banner'];

        if (!empty($_FILES['shop_logo']['name'])) {
            deleteOldImage($shop_logo);
            $logoPath = "Vendor_profile/" . time() . basename($_FILES['shop_logo']['name']);
            if (move_uploaded_file($_FILES["shop_logo"]["tmp_name"], $logoPath)) {
                $shop_logo = basename($logoPath);
            }
        }

        if (!empty($_FILES['shop_banner']['name'])) {
            deleteOldImage($shop_banner);
            $bannerPath = "Vendor_profile/" . time() . basename($_FILES['shop_banner']['name']);
            if (move_uploaded_file($_FILES["shop_banner"]["tmp_name"], $bannerPath)) {
                $shop_banner = basename($bannerPath);
            }
        }

        $stmt = $conn->prepare("UPDATE shop SET name=?, vendor_name=?, address=?, phone=?, email=?, shop_logo=?, shop_banner=? WHERE id=?");
        $stmt->bind_param("sssssssi", $name, $vendor_name, $address, $phone, $email, $shop_logo, $shop_banner, $shop_id);

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'msg' => '✅ Profile updated successfully!'];
            $shop = $conn->query("SELECT * FROM shop WHERE id='$shop_id'")->fetch_assoc();
        } else {
            $response['msg'] = '❌ Error updating profile';
        }
    } elseif ($_POST['ajax'] === 'change_password') {
        $current_pass = $conn->real_escape_string($_POST['current_password']);
        $new_pass = $conn->real_escape_string($_POST['new_password']);
        $confirm_pass = $conn->real_escape_string($_POST['confirm_password']);

        $check = $conn->query("SELECT password FROM shop WHERE id='$shop_id'")->fetch_assoc();
        if (!password_verify($current_pass, $check['password'])) {
            $response['msg'] = '❌ Current password is incorrect';
        } elseif ($new_pass !== $confirm_pass) {
            $response['msg'] = '❌ New passwords do not match';
        } else {
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $conn->query("UPDATE shop SET password='$hashed_pass' WHERE id='$shop_id'");
            $response = ['status' => 'success', 'msg' => '✅ Password changed successfully!'];
        }
    }

    echo json_encode($response);
    exit;
}

// Fetch products
$products = $conn->query("SELECT * FROM item WHERE shop_id='$shop_id' LIMIT 4");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Profile - Modern Design</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php require "inc/head.php"; ?>
    
     <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #4285f4;
            --secondary-blue: #1976d2;
            --light-blue: #e3f2fd;
            --success-green: #4caf50;
            --warning-orange: #ff9800;
            --danger-red: #f44336;
            --text-dark: #333333;
            --text-light: #666666;
            --bg-light: #f8f9fa;
            --white: #ffffff;
            --border-light: #e0e0e0;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 5px 20px rgba(0,0,0,0.15);
        }

       

        /* Header Section */
        .profile-header {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 30px;
            position: relative;
        }

        .cover-section {
            height: 250px;
            background: linear-gradient(rgba(66, 133, 244, 0.7), rgba(66, 133, 244, 0.7)), 
                        url('https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-avatar {
            position: absolute;
            bottom: -60px;
            left: 40px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid var(--white);
            background: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow);
            z-index: 10;
            transition: all 0.3s ease;
        }

        .profile-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .profile-avatar i {
            font-size: 40px;
            color: var(--white);
        }

        .profile-info {
            padding: 80px 40px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            background: var(--white);
        }

        .shop-details h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        .shop-details p {
            color: var(--text-light);
            font-size: 16px;
        }

        .rating {
            display: flex;
            align-items: center;
            margin-top: 8px;
        }

        .rating i {
            color: var(--warning-orange);
            margin-right: 3px;
            font-size: 14px;
        }

        .rating span {
            color: var(--text-light);
            font-size: 14px;
            margin-left: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--secondary-blue);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-blue);
            border: 2px solid var(--primary-blue);
        }

        .btn-outline:hover {
            background: var(--primary-blue);
            color: var(--white);
        }

        .btn-danger {
            background: var(--danger-red);
            color: var(--white);
        }

        .btn-danger:hover {
            background: #d32f2f;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        .profile-form {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow);
            height: fit-content;
        }

        .form-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 25px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            padding-bottom: 10px;
        }

        .form-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-blue);
            border-radius: 3px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-light);
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(66, 133, 244, 0.1);
        }

        .form-control:disabled {
            background: var(--bg-light);
            color: var(--text-light);
        }

        /* Products Section */
        .products-section {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow);
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .product-card {
            border: 2px solid var(--border-light);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            background: var(--white);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-blue);
        }

        .product-image {
            height: 150px;
            background: linear-gradient(45deg, var(--light-blue), var(--primary-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-image i {
            font-size: 40px;
            color: var(--primary-blue);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--danger-red);
            color: var(--white);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
        }

        .product-info {
            padding: 15px;
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-price {
            color: var(--primary-blue);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .product-stats {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-light);
            margin-bottom: 10px;
        }

        .product-actions {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            border-radius: 15px;
            padding: 20px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--white);
        }

        .stat-icon.blue {
            background: var(--primary-blue);
        }

        .stat-icon.green {
            background: var(--success-green);
        }

        .stat-icon.orange {
            background: var(--warning-orange);
        }

        .stat-info h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: var(--text-light);
            font-size: 14px;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: var(--white);
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            padding: 30px;
            box-shadow: var(--shadow-hover);
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-light);
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-light);
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            background: var(--bg-light);
            color: var(--danger-red);
        }

        /* Notification */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 15px 25px;
            border-radius: 10px;
            color: var(--white);
            display: none;
            box-shadow: var(--shadow-hover);
            animation: slideIn 0.3s ease, fadeOut 0.5s 2.5s forwards;
        }

        .notification.success {
            background: var(--success-green);
        }

        .notification.error {
            background: var(--danger-red);
        }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }

        @keyframes fadeOut {
            to { opacity: 0; transform: translateX(100%); }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .main-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .profile-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
                padding: 80px 20px 30px;
            }

            .action-buttons {
                width: 100%;
                justify-content: flex-end;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
            }

            .profile-avatar {
                left: 20px;
                width: 100px;
                height: 100px;
            }

            .profile-avatar i {
                font-size: 30px;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <?php require_once 'inc/sidebar.php'; ?>

   <div class="content">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="cover-section" style="background-image: url('Vendor_profile/<?= $shop['shop_banner'] ?>');">
                
                    <?php if($shop['shop_logo']): ?>
                        <img class="profile-avatar" src="Vendor_profile/<?= $shop['shop_logo'] ?>" alt="Shop Logo">
                    <?php else: ?>
                        <i class="fas fa-store"></i>
                    <?php endif; ?>
                
            </div>
            <div class="profile-info">
                <div class="shop-details">
                    <h1 id="shopNameDisplay"><?= htmlspecialchars($shop['name']) ?></h1>
                    <p id="shopLocationDisplay"><?= htmlspecialchars($shop['address']) ?></p>
                    <div class="rating">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <?php if($i <= floor($average_rating)): ?>
                                <i class="fas fa-star"></i>
                            <?php elseif($i == ceil($average_rating) && $average_rating - floor($average_rating) >= 0.5): ?>
                                <i class="fas fa-star-half-alt"></i>
                            <?php else: ?>
                                <i class="far fa-star"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <span><?= number_format($average_rating, 1) ?> (<?= $completed_orders ?> reviews)</span>
                    </div>
                </div>
                <div class="action-buttons">
                    <button type="button" id="editToggleBtn" class="btn btn-outline">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                    <button type="button" id="changePasswordBtn" class="btn btn-outline">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                    <button type="submit" id="saveBtn" class="btn btn-primary" disabled>
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $total_products ?></h3>
                    <p>Total Products</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $completed_orders ?></h3>
                    <p>Orders Completed</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($average_rating, 1) ?>/5</h3>
                    <p>Customer Rating</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Profile Form -->
            <div class="profile-form">
                <h2 class="form-title">
                    <i class="fas fa-user-circle"></i>
                    Shop Profile
                </h2>
                
                <form id="profileForm" enctype="multipart/form-data">
                    <input type="hidden" name="ajax" value="update_profile">
                    
                    <div class="form-group">
                        <label for="shopName">Shop Name:</label>
                        <input type="text" id="shopName" name="name" class="form-control" value="<?= htmlspecialchars($shop['name']) ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label for="vendorName">Vendor Name:</label>
                        <input type="text" id="vendorName" name="vendor_name" class="form-control" value="<?= htmlspecialchars($shop['vendor_name']) ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label for="shopAddress">Shop Address:</label>
                        <textarea id="shopAddress" name="address" class="form-control" rows="3" disabled><?= htmlspecialchars($shop['address']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number:</label>
                        <input type="tel" id="phoneNumber" name="phone" class="form-control" value="<?= htmlspecialchars($shop['phone']) ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($shop['email']) ?>" disabled>
                    </div>
                    
                    <input type="file" id="shopLogoInput" name="shop_logo" accept="image/*" style="display: none;">
                    <input type="file" id="shopBannerInput" name="shop_banner" accept="image/*" style="display: none;">
                </form>
            </div>

            <!-- Products Section -->
            <div class="products-section">
                <div class="products-header">
                    <h2 class="form-title">
                        <i class="fas fa-box-open"></i>
                        Featured Products
                    </h2>
                    <a href="add_product.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                </div>
                
                <div class="products-grid" id="productsGrid">
    <?php if($products->num_rows > 0): ?>
        <?php while($product = $products->fetch_assoc()): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="../prod/<?= $product['img1'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="product-badge">HOT</div>
                </div>
                <div class="product-info">
                    <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                    <div class="product-price">৳<?= number_format($product['price'], 2) ?></div>
                    <div class="product-stats">
                        <span><i class="fas fa-shopping-cart"></i> <?= $product['num'] ?> sold</span>
                        <span><i class="fas fa-star"></i> <?= $product['star'] ?></span>
                    </div>
                    <div class="product-actions">
                        <a href="single.php?id=<?= $product['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
            <p>No products found. Add your first product to get started.</p>
        </div>
    <?php endif; ?>
</div>

                <?php if($products->num_rows > 0): ?>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="products.php" class="btn btn-outline">
                        <i class="fas fa-eye"></i> View All Products
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal-overlay" id="passwordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-key"></i> Change Password
                </h3>
                <button type="button" class="close-btn" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="passwordForm">
                <input type="hidden" name="ajax" value="change_password">
                
                <div class="form-group">
                    <label for="currentPassword">Current Password</label>
                    <input type="password" id="currentPassword" name="current_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" name="new_password" class="form-control" required>
                    <small style="color: var(--text-light); font-size: 12px;">Must be at least 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <input type="password" id="confirmPassword" name="confirm_password" class="form-control" required>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px;">
                    <button type="button" class="btn btn-outline" id="cancelChangePassword">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Element references
            const editToggleBtn = document.getElementById('editToggleBtn');
            const saveBtn = document.getElementById('saveBtn');
            const changePasswordBtn = document.getElementById('changePasswordBtn');
            const passwordModal = document.getElementById('passwordModal');
            const closeModalBtn = document.getElementById('closeModal');
            const cancelPasswordBtn = document.getElementById('cancelChangePassword');
            const profileForm = document.getElementById('profileForm');
            const passwordForm = document.getElementById('passwordForm');
            const notification = document.getElementById('notification');
            const formInputs = profileForm.querySelectorAll('input, textarea');
            const shopLogoInput = document.getElementById('shopLogoInput');
            const shopBannerInput = document.getElementById('shopBannerInput');
            const profileAvatar = document.querySelector('.profile-avatar');
            const coverSection = document.querySelector('.cover-section');

            // Update display elements
            function updateDisplayInfo() {
                const shopName = document.getElementById('shopName').value;
                const shopAddress = document.getElementById('shopAddress').value;
                document.getElementById('shopNameDisplay').textContent = shopName || 'Shop Name';
                document.getElementById('shopLocationDisplay').textContent = shopAddress || 'Shop Location';
            }

            // Edit toggle functionality
            editToggleBtn.addEventListener('click', function() {
                const isEditing = saveBtn.disabled;
                
                if (isEditing) {
                    // Enable editing
                    formInputs.forEach(input => input.disabled = false);
                    saveBtn.disabled = false;
                    editToggleBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
                    editToggleBtn.classList.remove('btn-outline');
                    editToggleBtn.classList.add('btn-danger');
                } else {
                    // Cancel editing
                    formInputs.forEach(input => input.disabled = true);
                    saveBtn.disabled = true;
                    editToggleBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Profile';
                    editToggleBtn.classList.remove('btn-danger');
                    editToggleBtn.classList.add('btn-outline');
                    
                    // Reset form values
                    document.getElementById('shopName').value = '<?= addslashes($shop['name']) ?>';
                    document.getElementById('vendorName').value = '<?= addslashes($shop['vendor_name']) ?>';
                    document.getElementById('shopAddress').value = '<?= addslashes($shop['address']) ?>';
                    document.getElementById('phoneNumber').value = '<?= addslashes($shop['phone']) ?>';
                    document.getElementById('email').value = '<?= addslashes($shop['email']) ?>';
                    updateDisplayInfo();
                }
            });

            // Password modal functionality
            changePasswordBtn.addEventListener('click', function() {
                passwordModal.classList.add('active');
            });

            [closeModalBtn, cancelPasswordBtn].forEach(btn => {
                btn.addEventListener('click', function() {
                    passwordModal.classList.remove('active');
                    passwordForm.reset();
                });
            });

            // Close modal when clicking outside
            passwordModal.addEventListener('click', function(e) {
                if (e.target === passwordModal) {
                    passwordModal.classList.remove('active');
                    passwordForm.reset();
                }
            });

            // Profile form submission
            saveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Add loading state
                saveBtn.innerHTML = '<span class="spinner"></span> Saving...';
                saveBtn.disabled = true;
                
                // Submit form via AJAX
                const formData = new FormData(profileForm);
                
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const res = JSON.parse(response);
                            showNotification(res.msg, res.status);
                            
                            if (res.status === 'success') {
                                // Disable form after successful save
                                formInputs.forEach(input => input.disabled = true);
                                saveBtn.disabled = true;
                                editToggleBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Profile';
                                editToggleBtn.classList.remove('btn-danger');
                                editToggleBtn.classList.add('btn-outline');
                                
                                // Refresh page to show updated data
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                                saveBtn.disabled = false;
                            }
                        } catch (e) {
                            showNotification('Error processing response', 'error');
                            saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                            saveBtn.disabled = false;
                        }
                    },
                    error: function() {
                        showNotification('Error submitting form', 'error');
                        saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                        saveBtn.disabled = false;
                    }
                });
            });

            // Password form submission
            passwordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const currentPassword = document.getElementById('currentPassword').value;
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                // Validation
                if (newPassword !== confirmPassword) {
                    showNotification('❌ New passwords do not match', 'error');
                    return;
                }
                
                if (newPassword.length < 6) {
                    showNotification('❌ Password must be at least 6 characters', 'error');
                    return;
                }
                
                // Add loading state
                const submitBtn = passwordForm.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<span class="spinner"></span> Changing...';
                submitBtn.disabled = true;
                
                // Submit via AJAX
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: $(passwordForm).serialize(),
                    success: function(response) {
                        try {
                            const res = JSON.parse(response);
                            showNotification(res.msg, res.status);
                            
                            if (res.status === 'success') {
                                passwordModal.classList.remove('active');
                                passwordForm.reset();
                            }
                        } catch (e) {
                            showNotification('Error processing response', 'error');
                        }
                        submitBtn.innerHTML = '<i class="fas fa-save"></i> Change Password';
                        submitBtn.disabled = false;
                    },
                    error: function() {
                        showNotification('Error submitting form', 'error');
                        submitBtn.innerHTML = '<i class="fas fa-save"></i> Change Password';
                        submitBtn.disabled = false;
                    }
                });
            });

            // Notification function
            function showNotification(message, type) {
                notification.className = `notification ${type}`;
                notification.textContent = message;
                notification.style.display = 'block';
                
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 3000);
            }

            // Initialize display info
            updateDisplayInfo();

            // Real-time validation for password form
            const newPasswordInput = document.getElementById('newPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            
            function validatePasswords() {
                const newPassword = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                if (confirmPassword && newPassword !== confirmPassword) {
                    confirmPasswordInput.style.borderColor = 'var(--danger-red)';
                } else {
                    confirmPasswordInput.style.borderColor = 'var(--border-light)';
                }
            }
            
            newPasswordInput.addEventListener('input', validatePasswords);
            confirmPasswordInput.addEventListener('input', validatePasswords);

            // Form input change detection for profile form
            formInputs.forEach(input => {
                input.addEventListener('input', updateDisplayInfo);
            });

            // Image upload handlers
            profileAvatar.addEventListener('click', function() {
                if (!saveBtn.disabled) {
                    shopLogoInput.click();
                }
            });

            coverSection.addEventListener('click', function(e) {
                if (!saveBtn.disabled && e.target === coverSection) {
                    shopBannerInput.click();
                }
            });

            shopLogoInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profileAvatar.innerHTML = `<img src="${e.target.result}" alt="Shop Logo">`;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            shopBannerInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        coverSection.style.backgroundImage = `url(${e.target.result})`;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    url: 'delete_product.php',
                    method: 'POST',
                    data: { id: productId },
                    success: function(response) {
                        try {
                            const res = JSON.parse(response);
                            alert(res.message);
                            if (res.success) {
                                location.reload();
                            }
                        } catch (e) {
                            alert('Error processing response');
                        }
                    },
                    error: function() {
                        alert('Error deleting product');
                    }
                });
            }
        }
    </script>
</body>
</html>