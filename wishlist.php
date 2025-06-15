<?php
session_start();
include("config.php");

// Initialize wishlist if not exists
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

$uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// AJAX Handling for wishlist actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Wishlist Add/Remove
    if (isset($_POST['pid'])) {
        $pid = (int)$_POST['pid'];
        $type = isset($_POST['type']) ? $_POST['type'] : 'wishlist';
        $response = ['status' => 'error'];

        if ($uid > 0) {
            // For logged-in users
            if ($type === 'wishlist') {
                $exists = $conn->query("SELECT id FROM wishlist WHERE u_id=$uid AND p_id=$pid LIMIT 1");
                if ($exists->num_rows > 0) {
                    $conn->query("DELETE FROM wishlist WHERE u_id=$uid AND p_id=$pid");
                    $status = 'removed';
                } else {
                    $conn->query("INSERT INTO wishlist (u_id, p_id, qty) VALUES ($uid, $pid, 1)");
                    $status = 'added';
                }
                $count = $conn->query("SELECT COUNT(*) AS c FROM wishlist WHERE u_id=$uid")->fetch_assoc()['c'];
            } else {
                // For cart operations
                $exists = $conn->query("SELECT id FROM cart WHERE u_id=$uid AND p_id=$pid LIMIT 1");
                if ($exists->num_rows > 0) {
                    $conn->query("DELETE FROM cart WHERE u_id=$uid AND p_id=$pid");
                    $status = 'removed';
                } else {
                    $conn->query("INSERT INTO cart (u_id, p_id, qty) VALUES ($uid, $pid, 1)");
                    $status = 'added';
                }
                $count = $conn->query("SELECT COUNT(*) AS c FROM cart WHERE u_id=$uid")->fetch_assoc()['c'];
            }
            $response = ['status' => $status, 'type' => $type, 'count' => $count];
        } else {
            // For guests
            $store = &$_SESSION[$type];
            if (in_array($pid, $store)) {
                $store = array_diff($store, [$pid]);
                $status = 'removed';
            } else {
                $store[] = $pid;
                $status = 'added';
            }
            $count = count($store);
            $response = ['status' => $status, 'type' => $type, 'count' => $count];
        }
        
        // If moving from wishlist to cart
        if (isset($_POST['move_to_cart'])) {
            if ($type === 'cart' && $status === 'added') {
                // Remove from wishlist
                if ($uid > 0) {
                    $conn->query("DELETE FROM wishlist WHERE u_id=$uid AND p_id=$pid");
                } else {
                    $_SESSION['wishlist'] = array_diff($_SESSION['wishlist'], [$pid]);
                }
                $wishCount = $uid > 0 
                    ? $conn->query("SELECT COUNT(*) AS c FROM wishlist WHERE u_id=$uid")->fetch_assoc()['c']
                    : count($_SESSION['wishlist']);
                $response['wishlist_count'] = $wishCount;
            }
        }
        
        echo json_encode($response);
        exit;
    }
}

// Load wishlist items
if ($uid > 0) {
    $wishlistItems = $conn->query("
        SELECT w.id AS wish_id, w.qty, i.*, b.name AS brand_name, b.logo AS brand_logo
        FROM wishlist w 
        JOIN item i ON w.p_id = i.id 
        LEFT JOIN brands b ON i.brands_id = b.id 
        WHERE w.u_id = $uid
    ");
    $wishCount = $conn->query("SELECT COUNT(*) AS c FROM wishlist WHERE u_id=$uid")->fetch_assoc()['c'];
} else {
    $wishlistItems = [];
    if (!empty($_SESSION['wishlist'])) {
        $wishIds = implode(",", array_map('intval', $_SESSION['wishlist']));
        $wishlistItems = $conn->query("
            SELECT i.*, b.name AS brand_name, b.logo AS brand_logo 
            FROM item i 
            LEFT JOIN brands b ON i.brands_id = b.id 
            WHERE i.id IN ($wishIds)
        ");
    }
    $wishCount = count($_SESSION['wishlist']);
}

// Cart count
$cartCount = ($uid > 0)
    ? $conn->query("SELECT COUNT(*) AS c FROM cart WHERE u_id=$uid")->fetch_assoc()['c']
    : count($_SESSION['cart']);

// Featured products
$featuredProducts = $conn->query("SELECT * FROM item ORDER BY RAND() LIMIT 20");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Wishlist</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #6C63FF;
      --secondary-color: #4D44DB;
      --accent-color: #FF6584;
      --light-gray: #f7f7f7;
      --medium-gray: #E0E0E0;
      --dark-gray: #757575;
      --text-dark: #212121;
      --text-light: #424242;
      --white: #FFFFFF;
      --success-color: #4CAF50;
      --warning-color: #FF9800;
      --error-color: #F44336;
      --discount-color: #FF3E6C;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: var(--light-gray);
      padding-bottom: 50px;
      color: var(--text-dark);
    }
    
    .main-content {
      padding: 16px;
    }

    /* Header Styles */
    .shop-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0px;
      background: #ffffff;
      height: 60px;
      padding: 0 20px;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .header-icons {
      display: flex;
      gap: 15px;
    }
    
    .header-icons i {
      font-size: 20px;
      color: var(--secondary);
    }
    
    .cart-count, .wishlist-count {
      position: absolute;
      margin-top: -9px;
      margin-left: -6px;
      background-color: #f57c26;
      color: white;
      font-size: 12px;
      font-weight: bold;
      padding: 2px 6px;
      border-radius: 50%;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      pointer-events: none;
      z-index: 1;
    }
    
    .back-button {
      display: flex;
      align-items: center;
      color: var(--text-dark);
      text-decoration: none;
      font-weight: 500;
    }

    .back-button i {
      margin-right: 8px;
      font-size: 18px;
    }

    .page-title {
      left: 50%;
      transform: translateX(70%);
      font-size: 20px;
      font-weight: 600;
    }

    /* Wishlist Card Styles */
    .wishlist-container {
      margin-bottom: 20px;
    }

    .wishlist-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: var(--white);
      padding: 12px 16px;
      border-radius: 12px;
      margin-bottom: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .wishlist-header h2 {
      font-size: 18px;
      color: var(--primary-color);
    }

    .wishlist-header .item-count {
      background-color: var(--primary-color);
      color: white;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 14px;
    }

    .wishlist-card {
      background-color: var(--white);
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 12px;
      display: flex;
      gap: 16px;
      position: relative;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .wishlist-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .wishlist-card.selected {
      border: 1px solid var(--primary-color);
      background-color: rgba(108, 99, 255, 0.05);
    }
    
    .product-checkbox {
      margin-top: 40px;
    }

    .product-checkbox input[type="checkbox"] {
      width: 18px;
      height: 18px;
      accent-color: var(--primary-color);
    }
    
    .product-image-main {
      padding: 0px;
      align-items: center;
      justify-content: center;
      vertical-align: middle;
      max-width: 30%;
    }

    .product-image-main img {
      width: 100%;
      height: 100%;
      border-radius: 8px;
      object-fit: contain;
      justify-content: center;
      align-items: center;
    }

    .card-info {
      flex: 1;
    }

    .product-badge {
      display: inline-block;
      padding: 4px 8px;
      background-color: rgba(76, 175, 80, 0.1);
      color: var(--success-color);
      font-size: 10px;
      font-weight: 600;
      border-radius: 4px;
      margin-bottom: 6px;
    }

    .product-badge.new {
      background-color: rgba(108, 99, 255, 0.1);
      color: var(--primary-color);
    }

    .product-badge.sale {
      background-color: rgba(255, 152, 0, 0.1);
      color: var(--warning-color);
    }

    .product-title {
      font-weight: 600;
      font-size: 14px;
      margin-bottom: 4px;
    }

    .product-subtitle {
      font-size: 12px;
      color: var(--text-light);
      margin-bottom: 4px;
    }

    .product-seller {
      font-size: 11px;
      color: var(--dark-gray);
      margin-bottom: 12px;
    }

    .product-options {
      display: flex;
      gap: 8px;
      margin-bottom: 12px;
    }

    .product-options select {
      border-radius: 6px;
      padding: 6px 8px;
      font-size: 12px;
      border: 1px solid var(--medium-gray);
      background-color: var(--white);
      font-weight: 500;
      color: var(--text-dark);
      min-width: 80px;
    }

    .product-price-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }

    .product-price {
      font-size: 15px;
      font-weight: 700;
    }

    .product-price .original-price {
      color: var(--discount-color);
      font-size: 10px;
      margin-left: 6px;
      text-decoration: line-through;
      font-weight: 500;
    }

    /* Action Buttons */
    .action-buttons {
      display: flex;
      gap: 12px;
      margin-top: 15px;
    }

    .add-to-cart-btn, .buy-now-btn {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      text-align: center;
      font-size: 14px;
    }

    .add-to-cart-btn {
      background-color: white;
      color: var(--primary-color);
      border: 2px solid var(--primary-color);
    }

    .add-to-cart-btn:hover {
      background-color: var(--primary-color);
      color: white;
    }

    .buy-now-btn {
      background-color: var(--primary-color);
      color: white;
      border: 2px solid var(--primary-color);
    }

    .buy-now-btn:hover {
      background-color: var(--secondary-color);
      border-color: var(--secondary-color);
    }

    /* Combined Action Button */
    .combined-action-btn {
      width: 100%;
      background: var(--text-dark);
      color: var(--white);
      border: none;
      border-radius: 1px;
      padding: 14px;
      font-weight: 500;
      font-size: 22px;
      cursor: pointer;
      transition: background 0.3s;
      display: none;
      margin-top: 15px;
    }

    .combined-action-btn:hover {
      background: var(--secondary-color);
    }

    /* Empty Wishlist State */
    .empty-wishlist {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      text-align: center;
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .empty-wishlist i {
      font-size: 60px;
      color: var(--medium-gray);
      margin-bottom: 20px;
    }

    .empty-wishlist h3 {
      font-size: 18px;
      margin-bottom: 8px;
      color: var(--text-dark);
    }

    .empty-wishlist p {
      font-size: 14px;
      color: var(--text-light);
      margin-bottom: 20px;
    }

    .shop-now-btn {
      background: var(--primary-color);
      color: var(--white);
      border: none;
      border-radius: 8px;
      padding: 12px 24px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }

    .shop-now-btn:hover {
      background: var(--secondary-color);
    }

    /* Featured Products Section */
    .dropdown-product {
      margin-top: 20px;
      max-width: 100%;
      font-family: 'Segoe UI', sans-serif;
    }

    details.dropdown-section {
      border: 1px solid #e2e2e2;
      border-radius: 12px;
      background: #fff;
      padding:10px 0px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }

    summary {
      font-size: 18px;
      font-weight: 600;
      cursor: pointer;
      list-style: none;
      display: flex;
      align-items: center;
      padding: 10px;
    }

    summary::marker {
      display: none;
    }

    summary::after {
      content: "▼";
      font-size: 14px;
      margin-left: 10px;
      transition: transform 0.3s ease;
    }

    details[open] summary::after {
      transform: rotate(180deg);
    }

    /* Optional hover effect */
    details.dropdown-section:hover {
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
    }

    .products {
      margin-top: 10px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      padding: 0 15px;
    }

    .product-card {
      border-radius: 16px;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover {
      transform: translateY(-5px);
    }

    .product-image {
      width: 100%;
      height: 150px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      border: 2px 2px 0 0;
      border-radius: 8px;
      background: #d9d8d8;
      overflow: hidden;
      border-bottom: 1px solid var(--s);
    }

    .product-info {
      padding: 18px 16px;
      background: #fff;
    }

    .product-name {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 8px;
      color: #080808;
    }

    .product-price {
      font-size: 18px;
      font-weight: 700;
      color: #222;
      margin-bottom: 6px;
    }

    .product-original {
      font-size: 14px;
      color: #999;
      text-decoration: line-through;
      margin-top: 6px;
      display: inline-block;
    }

    .product-discount {
      color: #C25400;
      font-weight: 600;
      font-size: 14px;
      margin-left: 10px;
    }

    .product-image img {
      width: 300px;
      height: 300px;
      transition: .1s ease;
      object-fit: cover;
    }

    .product_img .transform:hover {
      width: 340px;
      height: 340px;
    }

    .brand {
      display: flex;
      align-items: center;
      vertical-align: middle;
      margin-top: 15px;
      justify-content: space-between;
    }

    .brand img { 
      width: auto;  
      height: 25px;
    }

    .add-to-cart {
      display: flex;
      justify-content: center;
    }

    .add-to-cart button {
      margin-top: -7px;
      margin-left: 5px;
      font-size: 10px;
      padding: 5px 15px;
      border: 2px solid #000;
      border-radius: 30px;
      background: #fff;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.3s ease, color 0.3s ease, transform 0.2s ease;
    }

    .add-to-cart button:hover {
      background: #000;
      color: #fff;
      transform: scale(1.05);
    }

    .label-launch {
      position: absolute;
      top: 0px;
      left: 0px;
      background-color: #7A3FFC;
      color: white;
      padding: 5px 8px;
      border-bottom-right-radius:8px ;
      font-size: 10px;
    }

    .wishlist-icon {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 25px;
      color: #141414;
      cursor: pointer;
      transition: color 0.3s ease, transform 0.3s ease;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      align-items: center;
      text-align: center;
      vertical-align: middle;
    }

    .wishlist-icon:hover {
      color: #E91E63;
      transform: scale(1.2);
    }

    .wishlist-icon.added {
      color: #E91E63;
    }

    .rating {
      position: absolute;
      bottom: 12px;
      left: 12px;
      font-size: 15px;
      color: #444;
      cursor: pointer;
      transition: color 0.3s ease, transform 0.3s ease;
      background-color: rgb(255, 255, 255);
      border-radius: 5px;
      width: 70px;
      height: 25px;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      vertical-align: middle;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .rating i{
      margin-top: -3px;
      margin-right: 4px;
      color: #fdc417;
    }

    /* Selection Summary */
    .selection-summary {
      display: flex;
      justify-content: space-between;
      margin-bottom: 12px;
      padding: 0 8px;
      font-size: 18px;
    }

    .selected-count {
      color: var(--primary-color);
      font-weight: 600;
    }

    /* Responsive Adjustments */
    @media (min-width: 768px) {
      body {
        max-width: 500px;
        margin: 0 auto;
      }
    }

    @media (max-width: 410px) {
      .product-price {
        font-size: 12px;
      }

      .product-price .original-price {
        font-size: 8px;
      }
    }
  </style>
</head>
<body>
  <!-- Header Section -->
  <div class="shop-header">
    <a href="index.php" class="back-button">
      <i class="fas fa-arrow-left"></i>
    </a>
    <h1 class="page-title">My Wishlist</h1>
    <div class="header-icons">
      <a href="wishlist.php" class="wishlist-icon-container">
        <i class="fas fa-heart"></i>
        <span class="wishlist-count"><?= $wishCount ?></span>
      </a>
      <a href="cart.php" class="cart-icon-container">
        <i class="fas fa-shopping-bag"></i>
        <span class="cart-count"><?= $cartCount ?></span>
      </a>
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="main-content">
    <!-- Wishlist Container -->
    <div class="wishlist-container">
      <?php if(($uid > 0 && $wishlistItems->num_rows > 0) || (!empty($_SESSION['wishlist']) && $wishlistItems->num_rows > 0)): ?>
        <div class="wishlist-header">
          <h2>Your Wishlist</h2>
          <span class="item-count"><?= $wishCount ?> items</span>
        </div>

        <!-- Select All -->
        <div class="select-all">
          <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
          <label for="selectAll">Select all items</label>
        </div>

        <!-- Product Cards -->
        <?php while($item = $wishlistItems->fetch_assoc()): 
          $discount = round((($item['max_price'] - $item['price']) / $item['max_price']) * 100);
          $sizes = !empty($item['size']) ? explode(",", $item['size']) : ['S', 'M', 'L', 'XL'];
          $colors = !empty($item['colors']) ? explode(",", $item['colors']) : ['Red', 'Blue', 'Black'];
        ?>
          <div class="wishlist-card" data-price="<?= $item['price'] ?>" data-original-price="<?= $item['max_price'] ?>" data-id="<?= $item['id'] ?>">
            <div class="product-checkbox">
              <input type="checkbox" class="product-select" onchange="updateSelectionSummary()" checked>
            </div>
            <div class="product-image-main">
              <img src="prod/<?= $item['img1'] ?>" alt="<?= $item['name'] ?>">
            </div>
            <div class="card-info">
              <span class="product-badge new">NEW</span>
              <h3 class="product-title"><?= $item['name'] ?></h3>
              <p class="product-seller">Brand: <?= $item['brand_name'] ?></p>
              <div class="product-options">
                <select class="size">
                  <?php foreach($sizes as $size): ?>
                    <option value="<?= trim($size) ?>">Size: <?= trim($size) ?></option>
                  <?php endforeach; ?>
                </select>
                <select class="color">
                  <?php foreach($colors as $color): ?>
                    <option value="<?= trim($color) ?>"><?= trim($color) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="product-price-container">
                <div class="product-price">BDT<?= $item['price'] ?> <span class="original-price">BDT<?= $item['max_price'] ?></span></div>
              </div>
              <div class="action-buttons">
                <button class="add-to-cart-btn" onclick="event.stopPropagation(); addToCart(<?= $item['id'] ?>, true)">Add to Cart</button>
                <button class="buy-now-btn" onclick="event.stopPropagation(); buyNow(<?= $item['id'] ?>)">Buy Now</button>
              </div>
            </div>
            <div class="card-actions">
              <i class="fas fa-trash" onclick="event.stopPropagation(); removeFromWishlist(<?= $item['id'] ?>)"></i>
            </div>
          </div>
        <?php endwhile; ?>
        
        <!-- Combined Action Button -->
        <button class="combined-action-btn" id="combinedActionBtn">Add Selected to Cart</button>
      <?php else: ?>
        <div class="empty-wishlist">
          <i class="fas fa-heart"></i>
          <h3>Your wishlist is empty</h3>
          <p>Save your favorite items here to view them later</p>
          <button class="shop-now-btn" onclick="window.location.href='index.php'">Start Shopping</button>
        </div>
      <?php endif; ?>
    </div>

    <!-- Featured Products Section -->
    <div class="dropdown-product">
      <details class="dropdown-section">
        <summary class="Featured-title">Featured Product</summary>
        <div class="products">
          <?php while($product = $featuredProducts->fetch_assoc()): 
            $discount = round((($product['max_price'] - $product['price']) / $product['max_price']) * 100);
            $brand = $conn->query("SELECT name, logo FROM brands WHERE id={$product['brands_id']}")->fetch_assoc();
            $inCart = ($uid > 0) ? $conn->query("SELECT id FROM cart WHERE u_id=$uid AND p_id={$product['id']}")->num_rows > 0 : in_array($product['id'], $_SESSION['cart']);
            $inWishlist = ($uid > 0) ? $conn->query("SELECT id FROM wishlist WHERE u_id=$uid AND p_id={$product['id']}")->num_rows > 0 : in_array($product['id'], $_SESSION['wishlist']);
          ?>
            <div class="product-card" data-id="<?= $product['id'] ?>">
              <div class="product-image">
                <img src="prod/<?= $product['img1'] ?>" alt="<?= $product['name'] ?>">
                <div class="label-launch">Just Launched</div>
                <div class="wishlist-icon wish-btn <?= $inWishlist ? 'added' : '' ?>" 
                     data-id="<?= $product['id'] ?>" 
                     data-type="wishlist">
                  <?= $inWishlist ? '♥' : '♡' ?>
                </div>
                <div class="rating"><i class="fa fa-star"></i> <?= $product['star'] ?></div>
              </div>
              <div class="product-info">
                <div class="product-name"><?= $product['name'] ?></div>
                <span class="product-price">BDT <?= $product['price'] ?></span>
                <div>
                  <span class="product-original">BDT <?= $product['max_price'] ?></span>
                  <span class="product-discount"><?= $discount ?>% OFF</span>
                </div>
                <div class="brand">
                  <div class="product-brand"><img src="<?= $brand['logo'] ?>" alt="<?= $brand['name'] ?>"></div>
                  <div class="add-to-cart">
                    <button class="cart-btn <?= $inCart ? 'added' : '' ?>" 
                            data-id="<?= $product['id'] ?>" 
                            data-type="cart">
                      <?= $inCart ? 'Remove' : 'Add' ?>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </details>
    </div>
  </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  updateSelectionSummary();
  attachCartButtonListeners();
  attachWishlistButtonListeners();
});

function toggleSelectAll() {
  const selectAll = document.getElementById('selectAll');
  document.querySelectorAll('.product-select').forEach(cb => cb.checked = selectAll.checked);
  document.querySelectorAll('.wishlist-card').forEach(card => card.classList.toggle('selected', selectAll.checked));
  updateSelectionSummary();
}

function updateSelectionSummary() {
  const selectedCount = document.querySelectorAll('.product-select:checked').length;
  document.querySelector('.selected-count').textContent = selectedCount;
  
  // Show/hide combined action button
  const combinedBtn = document.getElementById('combinedActionBtn');
  if (selectedCount > 0) {
    combinedBtn.style.display = 'block';
    combinedBtn.textContent = selectedCount > 1 
      ? `Add ${selectedCount} Items to Cart` 
      : 'Add Selected to Cart';
  } else {
    combinedBtn.style.display = 'none';
  }
}

function addToCart(productId, removeFromWishlist = false) {
  fetch('', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `pid=${productId}&type=cart${removeFromWishlist ? '&move_to_cart=true' : ''}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'added') {
      // Update cart count
      document.querySelector('.cart-count').textContent = data.count;
      
      // If moving from wishlist to cart
      if (removeFromWishlist) {
        // Update wishlist count
        if (data.wishlist_count !== undefined) {
          document.querySelector('.wishlist-count').textContent = data.wishlist_count;
        }
        
        // Remove the item from wishlist display
        const card = document.querySelector(`.wishlist-card[data-id="${productId}"]`);
        if (card) card.remove();
        
        // Update wishlist count display
        const remainingItems = document.querySelectorAll('.wishlist-card').length;
        document.querySelector('.item-count').textContent = `${remainingItems} items`;
        
        // If no more items, show empty state
        if (remainingItems === 0) {
          location.reload();
        }
      }
      
      // Show success message
      alert('Product added to cart!');
    } else {
      alert('Product removed from cart!');
    }
  });
}

function addSelectedToCart() {
  const selectedCards = document.querySelectorAll('.wishlist-card.selected');
  if (selectedCards.length === 0) return;
  
  selectedCards.forEach(card => {
    const productId = card.dataset.id;
    addToCart(productId, true);
  });
}

function buyNow(productId) {
  // Add to cart first
  fetch('', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `pid=${productId}&type=cart&move_to_cart=true`
  })
  .then(res => res.json())
  .then(data => {
    // Update counts
    document.querySelector('.cart-count').textContent = data.count;
    if (data.wishlist_count !== undefined) {
      document.querySelector('.wishlist-count').textContent = data.wishlist_count;
    }
    
    // Redirect to checkout
    window.location.href = 'checkout.php';
  });
}

function removeFromWishlist(productId) {
  if (confirm('Are you sure you want to remove this item from your wishlist?')) {
    fetch('', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `pid=${productId}&type=wishlist`
    })
    .then(res => res.json())
    .then(data => {
      document.querySelector('.wishlist-count').textContent = data.count;
      // Remove the element from the DOM
      const card = document.querySelector(`.wishlist-card[data-id="${productId}"]`);
      if (card) card.remove();
      
      // Update wishlist count display
      const remainingItems = document.querySelectorAll('.wishlist-card').length;
      document.querySelector('.item-count').textContent = `${remainingItems} items`;
      
      // If no more items, show empty state
      if (remainingItems === 0) {
        location.reload();
      }
    });
  }
}

function attachCartButtonListeners() {
  document.querySelectorAll('.cart-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      const productId = this.dataset.id;
      const type = this.dataset.type;

      fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `pid=${productId}&type=${type}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'added') {
          this.textContent = 'Remove';
          this.classList.add('added');
        } else {
          this.textContent = 'Add to Cart';
          this.classList.remove('added');
        }
        document.querySelector('.cart-count').textContent = data.count;
      });
    });
  });
}

function attachWishlistButtonListeners() {
  document.querySelectorAll('.wish-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      const productId = this.dataset.id;
      const type = this.dataset.type;

      fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `pid=${productId}&type=${type}`
      })
      .then(res => res.json())
      .then(data => {
        this.innerHTML = data.status === 'added' ? '♥' : '♡';
        this.classList.toggle('added', data.status === 'added');
        document.querySelector('.wishlist-count').textContent = data.count;
        
        // If adding to wishlist from featured products, refresh the page to show in wishlist
        if (data.status === 'added' && type === 'wishlist') {
          location.reload();
        }
      });
    });
  });
}

document.querySelectorAll('.product-card').forEach(card => {
  card.addEventListener('click', function (e) {
    if (!e.target.closest('button') && !e.target.closest('.wishlist-icon')) {
      window.location.href = 'wishlist_slider.php?id=' + this.dataset.id;
    }
  });
});

// Combined action button event
document.getElementById('combinedActionBtn').addEventListener('click', function(e) {
  e.preventDefault();
  addSelectedToCart();
});
</script>

</body>
</html>