<?php

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
    
    <?php include('assets/css/wishlist.css'); ?>
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
    $discount = ($product['max_price'] > 0) 
        ? round((($product['max_price'] - $product['price']) / $product['max_price']) * 100) 
        : 0;

    $brand = $conn->query("SELECT name, logo FROM brands WHERE id={$product['brands_id']}")->fetch_assoc();

    $inCart = ($uid > 0) 
        ? $conn->query("SELECT id FROM cart WHERE u_id=$uid AND p_id={$product['id']}")->num_rows > 0 
        : in_array($product['id'], $_SESSION['cart']);

    $inWishlist = ($uid > 0) 
        ? $conn->query("SELECT id FROM wishlist WHERE u_id=$uid AND p_id={$product['id']}")->num_rows > 0 
        : in_array($product['id'], $_SESSION['wishlist']);
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