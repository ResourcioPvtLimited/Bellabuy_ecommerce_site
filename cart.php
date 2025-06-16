<?php

include("config.php");

// Initialize session cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

$uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// --- AJAX Handling ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Cart/Wishlist Add/Remove
    if (isset($_POST['pid']) && isset($_POST['type'])) {
        $pid = (int)$_POST['pid'];
        $type = $_POST['type'];
        $response = ['status' => 'error'];

        if ($uid > 0) {
            $table = $type === 'wishlist' ? 'wishlist' : 'cart';
            $exists = $conn->query("SELECT id FROM $table WHERE u_id=$uid AND p_id=$pid LIMIT 1");
            if ($exists->num_rows > 0) {
                $conn->query("DELETE FROM $table WHERE u_id=$uid AND p_id=$pid");
                $status = 'removed';
            } else {
                $conn->query("INSERT INTO $table (u_id, p_id, qty) VALUES ($uid, $pid, 1)");
                $status = 'added';
            }
            $count = $conn->query("SELECT COUNT(*) AS c FROM $table WHERE u_id=$uid")->fetch_assoc()['c'];
            $response = ['status' => $status, $type => $count];
        } else {
            $store = &$_SESSION[$type];
            if (in_array($pid, $store)) {
                $store = array_diff($store, [$pid]);
                $status = 'removed';
            } else {
                $store[] = $pid;
                $status = 'added';
            }
            $count = count($store);
            $response = ['status' => $status, $type => $count];
        }
        echo json_encode($response);
        exit;
    }

    // Quantity update (for logged in users)
    if (isset($_POST['update_quantity'], $_POST['pid'], $_POST['qty']) && $uid > 0) {
        $pid = (int)$_POST['pid'];
        $qty = max(1, (int)$_POST['qty']);
        $conn->query("UPDATE cart SET qty=$qty WHERE u_id=$uid AND p_id=$pid");
        echo json_encode(['status' => 'updated']);
        exit;
    }

    // Remove from cart
    if (isset($_POST['remove_from_cart'], $_POST['pid'])) {
        $pid = (int)$_POST['pid'];
        if ($uid > 0) {
            $conn->query("DELETE FROM cart WHERE u_id=$uid AND p_id=$pid");
        } else {
            $_SESSION['cart'] = array_diff($_SESSION['cart'], [$pid]);
        }
        echo json_encode(['status' => 'removed']);
        exit;
    }

    // Get cart count
    if (isset($_POST['get_cart_count'])) {
        $count = ($uid > 0)
            ? $conn->query("SELECT COUNT(*) AS c FROM cart WHERE u_id=$uid")->fetch_assoc()['c']
            : count($_SESSION['cart']);
        echo json_encode(['count' => $count]);
        exit;
    }
}

// Load cart items
if ($uid > 0) {
    $cartItems = $conn->query("
        SELECT c.id AS cart_id, c.qty, i.*, b.name AS brand_name, b.logo AS brand_logo
        FROM cart c 
        JOIN item i ON c.p_id = i.id 
        LEFT JOIN brands b ON i.brands_id = b.id 
        WHERE c.u_id = $uid
    ");
    $cartCount = $conn->query("SELECT COUNT(*) AS c FROM cart WHERE u_id=$uid")->fetch_assoc()['c'];
} else {
    $cartItems = [];
    if (!empty($_SESSION['cart'])) {
        $cartIds = implode(",", array_map('intval', $_SESSION['cart']));
        $cartItems = $conn->query("
            SELECT i.*, b.name AS brand_name, b.logo AS brand_logo 
            FROM item i 
            LEFT JOIN brands b ON i.brands_id = b.id 
            WHERE i.id IN ($cartIds)
        ");
    }
    $cartCount = count($_SESSION['cart']);
}

// Wishlist count
$wishCount = ($uid > 0)
    ? $conn->query("SELECT COUNT(*) AS c FROM wishlist WHERE u_id=$uid")->fetch_assoc()['c']
    : count($_SESSION['wishlist']);

// Featured products
$featuredProducts = $conn->query("SELECT * FROM item ORDER BY RAND() LIMIT 20");

// Load coupons
$couponQuery = $conn->query("SELECT code, discount FROM coupon WHERE expired = '0'");
$coupons = [];
while ($row = $couponQuery->fetch_assoc()) {
    $coupons[$row['code']] = (float)$row['discount'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Bag</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    
    <?php include('assets/css/cart.css'); ?>
  </style>
</head>
<body>
  <!-- Header Section -->
  <div class="shop-header">
    <a href="index.php" class="back-button">
      <i class="fas fa-arrow-left"></i>
    </a>
    <h1 class="page-title">My Bag</h1>
    <div style="width: 24px;"></div>
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
    <!-- Progress Indicator -->
    <div class="progress-indicator">
      <div class="progress-step">
        <div class="step-number"><i class="fa-solid fa-bag-shopping"></i></div>
        <div class="step-label"></div>
      </div>
      
      <div class="progress-line inactive"></div>
      <div class="progress-step">
        <div class="step-number inactive"><i class="fa-solid fa-check-circle"></i></div>
        <div class="step-label inactive"></div>
      </div>
    </div>

    <!-- Cart Container -->
    <div class="cart-container">
      <?php if(($uid > 0 && $cartItems->num_rows > 0) || (!empty($_SESSION['cart']) && $cartItems->num_rows > 0)): ?>
        <div class="select-all">
          <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
          <label for="selectAll">Select all items</label>
          <span class="edit">Edit</span>
        </div>

        <!-- Product Cards -->
        <?php while($item = $cartItems->fetch_assoc()): 
          $discount = round((($item['max_price'] - $item['price']) / $item['max_price']) * 100);
          $sizes = !empty($item['size']) ? explode(",", $item['size']) : ['S', 'M', 'L', 'XL'];
          $colors = !empty($item['colors']) ? explode(",", $item['colors']) : ['Red', 'Blue', 'Black'];
        ?>
          <div class="cart-card" data-price="<?= $item['price'] ?>" data-original-price="<?= $item['max_price'] ?>" data-id="<?= $item['id'] ?>">
            <div class="product-image-main">
              <img src="prod/<?= $item['img1'] ?>" alt="<?= $item['name'] ?>" class="product-image">
            </div>
            <div class="card-info">
              <span class="product-badge new">NEW</span>
              <h3 class="product-title"><?= $item['name'] ?></h3>
              <p class="product-seller">Brand: <?= $item['brand_name'] ?></p>
              <div class="product-options">
                <select class="size" onchange="updateCartSummary()">
                  <?php foreach($sizes as $size): ?>
                    <option value="<?= trim($size) ?>">Size: <?= trim($size) ?></option>
                  <?php endforeach; ?>
                </select>
                <select class="color" onchange="updateCartSummary()">
                  <?php foreach($colors as $color): ?>
                    <option value="<?= trim($color) ?>"><?= trim($color) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="product-price-container">
                <div class="product-price">BDT<?= $item['price'] ?> <span class="original-price">BDT<?= $item['max_price'] ?></span></div>
                <div class="quantity-control">
                  <button class="quantity-btn minus" onclick="adjustQuantity(this, -1)">-</button>
                  <span class="quantity-value"><?= $uid > 0 ? $item['qty'] : 1 ?></span>
                  <button class="quantity-btn plus" onclick="adjustQuantity(this, 1)">+</button>
                </div>
                <input type="checkbox" class="product-select" onchange="updateCartSummary()" checked>
              </div>
            </div>
            <div class="card-actions">
              <i class="fas fa-trash" onclick="removeProduct(this, <?= $item['id'] ?>)"></i>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="empty-cart">
          <i class="fas fa-shopping-bag"></i>
          <h3>Your bag is empty</h3>
          <p>Looks like you haven't added anything to your bag yet</p>
          <button class="shop-now-btn" onclick="window.location.href='index.php'">Shop Now</button>
        </div>
      <?php endif; ?>
    </div>

    <?php if(($uid > 0 && $cartItems->num_rows > 0) || (!empty($_SESSION['cart']) && $cartItems->num_rows > 0)): ?>
      <!-- Voucher Section -->
      <div class="voucher-section">
        <div class="voucher-header">
          <i class="fas fa-tag"></i>
          <h3>Apply Voucher Code</h3>
        </div>
        <div class="voucher-input-group">
          <input type="text" class="voucher-input" id="voucherCode" placeholder="Enter voucher code">
          <button class="voucher-btn" onclick="applyVoucher()">Apply</button>
        </div>
        <div id="voucherMessage" style="margin-top: 10px; font-size: 12px; color: var(--success-color); display: none;"></div>
      </div>

      <!-- Order Summary -->
      <div class="order-summary">
        <h3 class="summary-header">Order Summary</h3>
        <div class="summary-row">
          <span>Subtotal (<span id="itemCount">0</span> items)</span>
          <span class="summary-value" id="subtotal">BDT0</span>
        </div>
        <div class="summary-row">
          <span>Delivery Fee</span>
          <span class="summary-value" id="deliveryFee">BDT0</span>
        </div>
        <div class="summary-row">
          <span>Discount</span>
          <span class="summary-value" id="discount">-BDT0</span>
        </div>
        <div class="summary-row">
          <span>Voucher Discount</span>
          <span class="summary-value" id="voucherDiscount">-BDT0</span>
        </div>
        <div class="summary-row total">
          <span>Total</span>
          <span class="summary-value total" id="total">BDT0</span>
        </div>
      </div>

      <!-- Featured Products Section -->
      <div class="dropdown-product">
        <details class="dropdown-section">
          <summary class="Featured-title">Featured Product</summary>
          <div class="products">
            <?php while($product = $featuredProducts->fetch_assoc()): 
              $discount = round((($product['max_price'] - $product['price']) / $product['max_price'] * 100));
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
                        <?= $inCart ? 'Remove' : 'Add To Cart' ?>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        </details>
      </div>
    <?php endif; ?>
  </div>

  <?php if(($uid > 0 && $cartItems->num_rows > 0) || (!empty($_SESSION['cart']) && $cartItems->num_rows > 0)): ?>
    <!-- Action Buttons -->
    <div class="action-buttons">
      <div class="selection-summary">
        <span><span class="selected-count">0</span> item(s) selected</span>
        <span>Total: <span class="total-price">BDT0</span></span>
      </div>
      <button class="place-order-btn" onclick="placeOrder()">Place Order</button>
    </div>
  <?php endif; ?>
<?php
// Load all valid coupons from the database
$couponQuery = $conn->query("SELECT code, discount FROM coupon WHERE expired = '0'");
$coupons = [];
while ($row = $couponQuery->fetch_assoc()) {
    $coupons[$row['code']] = (float)$row['discount'];
}
?>

<script>
const validCoupons = <?= json_encode($coupons); ?>;
let appliedCoupon = null;
let voucherDiscount = 0;
let firstOrderDiscount = <?= ($uid > 0 && $conn->query("SELECT COUNT(*) as c FROM orders WHERE u_id = $uid")->fetch_assoc()['c'] == 0) ? 100 : 0 ?>;

document.addEventListener('DOMContentLoaded', function () {
  updateCartSummary();
  attachCartButtonListeners();
  attachWishlistButtonListeners();
});

function toggleSelectAll() {
  const selectAll = document.getElementById('selectAll');
  document.querySelectorAll('.product-select').forEach(cb => cb.checked = selectAll.checked);
  document.querySelectorAll('.cart-card').forEach(card => card.classList.toggle('selected', selectAll.checked));
  updateCartSummary();
}

function adjustQuantity(button, change) {
  const quantityElement = button.parentElement.querySelector('.quantity-value');
  let quantity = parseInt(quantityElement.textContent) + change;
  quantityElement.textContent = Math.max(1, quantity);
  updateCartSummary();

  const cartCard = button.closest('.cart-card');
  const productId = cartCard.dataset.id;
  const newQuantity = quantityElement.textContent;

  <?php if ($uid > 0): ?>
  fetch('', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `update_quantity=true&pid=${productId}&qty=${newQuantity}`
  });
  <?php endif; ?>
}

function removeProduct(button, productId) {
  if (confirm('Are you sure you want to remove this item from your cart?')) {
    button.closest('.cart-card').remove();

    <?php if ($uid > 0): ?>
    fetch('', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `remove_from_cart=true&pid=${productId}`
    }).then(() => updateCartCount());
    <?php else: ?>
    fetch('', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `pid=${productId}&type=cart`
    }).then(() => updateCartCount());
    <?php endif; ?>

    updateCartSummary();
    if (document.querySelectorAll('.cart-card').length === 0) location.reload();
  }
}

function updateCartCount() {
  fetch('', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'get_cart_count=true'
  })
  .then(res => res.json())
  .then(data => {
    document.querySelector('.cart-count').textContent = data.count;
  });
}

function applyVoucher() {
  const voucherCode = document.getElementById('voucherCode').value.trim();
  const messageBox = document.getElementById('voucherMessage');

  if (validCoupons[voucherCode]) {
    appliedCoupon = voucherCode;
    voucherDiscount = validCoupons[voucherCode];
    messageBox.textContent = 'Voucher applied successfully!';
    messageBox.style.color = 'green';
  } else {
    appliedCoupon = null;
    voucherDiscount = 0;
    messageBox.textContent = 'Invalid voucher code';
    messageBox.style.color = 'red';
  }

  messageBox.style.display = 'block';
  updateCartSummary();
}

function updateCartSummary() {
  const cards = document.querySelectorAll('.cart-card');
  let subtotal = 0, totalItems = 0, discount = 0, selectedCount = 0;

  cards.forEach(card => {
    const isSelected = card.querySelector('.product-select').checked;
    const price = parseFloat(card.dataset.price);
    const original = parseFloat(card.dataset.originalPrice);
    const qty = parseInt(card.querySelector('.quantity-value').textContent);

    if (isSelected) {
      subtotal += price * qty;
      discount += (original - price) * qty;
      totalItems += qty;
      selectedCount++;
      card.classList.add('selected');
    } else {
      card.classList.remove('selected');
    }
  });

  const deliveryFee = selectedCount > 0 ? 120 : 0;
  const total = Math.max(0, subtotal + deliveryFee - discount - firstOrderDiscount - voucherDiscount);

  document.querySelector('.selected-count').textContent = selectedCount;
  document.querySelector('.total-price').textContent = `BDT${total.toFixed(0)}`;
  document.getElementById('itemCount').textContent = totalItems;
  document.getElementById('subtotal').textContent = `BDT${subtotal.toFixed(0)}`;
  document.getElementById('deliveryFee').textContent = `BDT${deliveryFee}`;
  document.getElementById('discount').textContent = `-BDT${discount.toFixed(0)}`;
  document.getElementById('voucherDiscount').textContent = `-BDT${voucherDiscount.toFixed(0)}`;
  document.getElementById('total').textContent = `BDT${total.toFixed(0)}`;
  document.getElementById('selectAll').checked = selectedCount === cards.length && cards.length > 0;
}

function placeOrder() {
  const selectedCount = parseInt(document.querySelector('.selected-count').textContent);
  if (selectedCount === 0) return alert('Please select at least one item to place an order.');

  const selectedProducts = [];
  document.querySelectorAll('.cart-card.selected').forEach(card => {
    selectedProducts.push({
      id: card.dataset.id,
      quantity: parseInt(card.querySelector('.quantity-value').textContent),
      size: card.querySelector('.size').value,
      color: card.querySelector('.color').value
    });
  });

  const form = document.createElement('form');
  form.method = 'POST';
  form.action = 'checkout.php';

  const productsInput = document.createElement('input');
  productsInput.type = 'hidden';
  productsInput.name = 'products';
  productsInput.value = JSON.stringify(selectedProducts);
  form.appendChild(productsInput);

  const totalInput = document.createElement('input');
  totalInput.type = 'hidden';
  totalInput.name = 'total';
  totalInput.value = document.getElementById('total').textContent.replace('BDT', '').trim();
  form.appendChild(totalInput);

  if (appliedCoupon) {
    const couponInput = document.createElement('input');
    couponInput.type = 'hidden';
    couponInput.name = 'coupon';
    couponInput.value = appliedCoupon;
    form.appendChild(couponInput);
  }

  const firstOrderInput = document.createElement('input');
  firstOrderInput.type = 'hidden';
  firstOrderInput.name = 'first_order_discount';
  firstOrderInput.value = firstOrderDiscount;
  form.appendChild(firstOrderInput);

  document.body.appendChild(form);
  form.submit();
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
          this.textContent = 'Add To Cart';
          this.classList.remove('added');
        }
        document.querySelector('.cart-count').textContent = data.cart;
        if (window.location.pathname.includes('cart.php')) location.reload();
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
        document.querySelector('.wishlist-count').textContent = data.wishlist;
      });
    });
  });
}

document.querySelectorAll('.product-card').forEach(card => {
  card.addEventListener('click', function (e) {
    if (!e.target.closest('button') && !e.target.closest('.wishlist-icon')) {
      window.location.href = 'single_product.php?id=' + this.dataset.id;
    }
  });
});
</script>


</body>
</html>