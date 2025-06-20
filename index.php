<?php

include("config.php");

// Initialize session cart and wishlist if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

$uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Handle AJAX for cart/wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['pid']) && isset($_POST['type'])) {
        $pid = (int)$_POST['pid'];
        $type = $_POST['type'];
        $table = $type === 'wishlist' ? 'wishlist' : 'cart';
        $response = ['status' => 'error'];
        
        // Check if user is logged in
        if ($uid > 0) {
            $exists = $conn->query("SELECT id FROM `$table` WHERE u_id=$uid AND p_id=$pid LIMIT 1");
            if ($exists->num_rows > 0) {
                $conn->query("DELETE FROM `$table` WHERE u_id=$uid AND p_id=$pid");
                $status = 'removed';
            } else {
                $conn->query("INSERT INTO `$table` (u_id, p_id, qty) VALUES ($uid, $pid, 1)");
                $status = 'added';
            }
            
            $cartCount = $conn->query("SELECT COUNT(*) AS c FROM cart WHERE u_id=$uid")->fetch_assoc()['c'];
            $wishCount = $conn->query("SELECT COUNT(*) AS c FROM wishlist WHERE u_id=$uid")->fetch_assoc()['c'];
            
            $response = ['status' => $status, 'cart' => $cartCount, 'wishlist' => $wishCount];
        } 
        // Handle guest users
        else {
            if ($type === 'cart') {
                if (in_array($pid, $_SESSION['cart'])) {
                    $_SESSION['cart'] = array_diff($_SESSION['cart'], [$pid]);
                    $status = 'removed';
                } else {
                    $_SESSION['cart'][] = $pid;
                    $status = 'added';
                }
                $cartCount = count($_SESSION['cart']);
            } else {
                if (in_array($pid, $_SESSION['wishlist'])) {
                    $_SESSION['wishlist'] = array_diff($_SESSION['wishlist'], [$pid]);
                    $status = 'removed';
                } else {
                    $_SESSION['wishlist'][] = $pid;
                    $status = 'added';
                }
                $wishCount = count($_SESSION['wishlist']);
            }
            
            $response = ['status' => $status, 'cart' => $cartCount, 'wishlist' => $wishCount];
        }
        
        echo json_encode($response);
        exit;
    }
    // Handle login requirement check
    elseif (isset($_POST['check_login'])) {
        echo json_encode(['logged_in' => ($uid > 0)]);
        exit;
    }
    // Handle product filtering
    elseif (isset($_POST['filter_gender'])) {
        $gender = $_POST['filter_gender'];
        
        // Filter products
        $filter = $gender ? "WHERE person='$gender'" : "";
        $products = $conn->query("SELECT * FROM item $filter ORDER BY RAND()");
        
        // Filter categories
        $catFilter = $gender ? "WHERE person='$gender'" : "";
        $categories = $conn->query("SELECT * FROM cat $catFilter");
        
        // Filter brands
        $brandFilter = $gender ? "WHERE person='$gender' AND tag='top_brands'" : "WHERE tag='top_brands'";
        $brands = $conn->query("SELECT * FROM brands $brandFilter");
        
        // Generate products HTML
        $productsHTML = '';
        while ($item = $products->fetch_assoc()) {
            $id = $item['id'];
            $brand = $conn->query("SELECT name, logo FROM brands WHERE id={$item['brands_id']} LIMIT 1")->fetch_assoc();
            
            // Check if item is in cart/wishlist
            $inCart = false;
            $inWishlist = false;
            
            if ($uid > 0) {
                $inCart = $conn->query("SELECT id FROM cart WHERE u_id=$uid AND p_id=$id")->num_rows > 0;
                $inWishlist = $conn->query("SELECT id FROM wishlist WHERE u_id=$uid AND p_id=$id")->num_rows > 0;
            } else {
                $inCart = in_array($id, $_SESSION['cart']);
                $inWishlist = in_array($id, $_SESSION['wishlist']);
            }
            
            $productsHTML .= "<div class='product-card'>
                <div class='product-image'>
                    <img src='prod/{$item['img1']}' alt='{$item['name']}'>
                    <div class='label-launch'>Just Launched</div>
                    <div class='wishlist-icon wish-btn " . ($inWishlist ? 'added' : '') . "' data-id='{$id}' data-type='wishlist'>" . ($inWishlist ? '✓' : '+') . "</div>
                    <div class='rating'><i class='fa fa-star'></i> {$item['star']}</div>
                </div>
                <div class='product-info'>
                    <div class='product-name'>{$item['name']}</div>
                    <div class='product-price'>BDT {$item['price']}</div>
                    <div><span class='product-original'>BDT {$item['max_price']}</span> <span class='product-discount'>{$item['discount']}% OFF</span></div>
                    <div class='brand'>
                        <div class='brand-logo'>
                            <img src='{$brand['logo']}' alt='" . htmlspecialchars($brand['name']) . "'>
                        </div>
                        <div class='add-to-cart'>
                            <button class='cart-btn " . ($inCart ? 'added' : '') . "' 
                                    data-id='{$id}' 
                                    data-type='cart'>" .
                                ($inCart ? 'Remove from Cart' : 'Add to Cart') .
                            "</button>
                        </div>
                    </div>
                </div>
            </div>";
        }
        
        // Generate categories HTML
        $categoriesHTML = '';
        while ($cat = $categories->fetch_assoc()) {
            $categoriesHTML .= "<div class='category-slide'><a href='category.php?id={$cat['id']}'><img src='{$cat['logo']}' alt='{$cat['cat']}'><p>{$cat['cat']}</p></a></div>";
        }
        
        // Generate brands HTML
        $brandsHTML = '';
        while ($brand = $brands->fetch_assoc()) {
            $brandsHTML .= "<div class='brand-slide'><a href='brandsprofile.php?id={$brand['id']}'><img src='{$brand['logo']}' alt='{$brand['name']}'></a></div>";
        }
        
        echo json_encode([
            'products' => $productsHTML,
            'categories' => $categoriesHTML,
            'brands' => $brandsHTML
        ]);
        exit;
    }
}

// Transfer guest cart to user after login
if ($uid > 0 && !empty($_SESSION['guest_cart'])) {
    foreach ($_SESSION['guest_cart'] as $pid) {
        $conn->query("INSERT INTO cart (u_id, p_id, qty) VALUES ($uid, $pid, 1) 
                     ON DUPLICATE KEY UPDATE qty = qty + 1");
    }
    unset($_SESSION['guest_cart']);
    
    // Update counts after transfer
    $cartCount = $conn->query("SELECT COUNT(*) AS c FROM cart WHERE u_id=$uid")->fetch_assoc()['c'];
    $wishCount = $conn->query("SELECT COUNT(*) AS c FROM wishlist WHERE u_id=$uid")->fetch_assoc()['c'];
} 
// Initialize counts
else {
    if ($uid > 0) {
        $cartCount = $conn->query("SELECT COUNT(*) AS c FROM cart WHERE u_id=$uid")->fetch_assoc()['c'];
        $wishCount = $conn->query("SELECT COUNT(*) AS c FROM wishlist WHERE u_id=$uid")->fetch_assoc()['c'];
    } else {
        $cartCount = count($_SESSION['cart']);
        $wishCount = count($_SESSION['wishlist']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SION Fashion</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="assets/css/tect.css"/>
  <link rel="stylesheet" href="../assets/css/index.css"/>
</head>
<body>

<!-- Header -->
<header>
  <div class="logo">SION</div>

  <div class="search-bar">
    <form action="inc/search.php" method="GET">
      <input type="text" name="search" placeholder="Search for products..." required>
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
  </div>

  <div class="header-icons">
    <a href="./user/wishlist.html" class="wishlist-icon-container">
      <i class="fas fa-heart"></i>
      <span class="wishlist-count"><?= $wishCount ?></span>
    </a>
    <a href="./user/cart.html" class="cart-icon-container">
      <i class="fas fa-shopping-bag"></i>
      <span class="cart-count"><?= $cartCount ?></span>
    </a>
  </div>
</header>

<!-- Gender Filter -->
<div class="gender-filter gender-buttons">
  <button class="gender-btn" data-gender="men">Men</button>
  <button class="gender-btn" data-gender="women">Women</button>
</div>

<!-- Categories -->
<div class="categories category-slider" id="category-container">
  <?php
  $catQ = $conn->query("SELECT * FROM cat");
  while ($cat = $catQ->fetch_assoc()) {
    echo "<div class='category-slide'><a href='category.php?id={$cat['id']}'><img src='{$cat['logo']}' alt='{$cat['cat']}'><p>{$cat['cat']}</p></a></div>";
  }
  ?>
</div>

<!-- Banners -->
<div class="slider-container">
  <div class="banner-slider">
    <?php
    $bannerQ = $conn->query("SELECT * FROM banner");
    while ($b = $bannerQ->fetch_assoc()) {
      echo "<div class='banner-slide'><a href='{$b['link']}' target='_blank'><img src='prod/{$b['big']}' alt='{$b['t1']}'><div class='banner-content'><h2>{$b['t1']}</h2><h1>{$b['t2']}</h1><button class='banner-btn'>SHOP NOW</button></div></a></div>";
    }
    ?>
  </div>
  
  <!-- Banner Navigation Arrows -->
  <div class="banner-nav">
    <button class="banner-nav-btn prev-banner"><i class="fas fa-chevron-left"></i></button>
    <button class="banner-nav-btn next-banner"><i class="fas fa-chevron-right"></i></button>
  </div>
  
  <!-- Banner Dots Indicator -->
  <div class="banner-dots">
    <?php
    $bannerCount = $conn->query("SELECT COUNT(*) AS count FROM banner")->fetch_assoc()['count'];
    for ($i = 0; $i < $bannerCount; $i++) {
      echo "<div class='banner-dot" . ($i === 0 ? ' active' : '') . "' data-index='$i'></div>";
    }
    ?>
  </div>
</div>

<!-- Top Brands -->
<div class="section-title">Top <span>Brands</span></div>
<div class="slider-wrapper">
  <div class="brand-slider" id="brand-container">
    <?php
    $brandQ = $conn->query("SELECT * FROM brands WHERE tag='top_brands'");
    while ($brand = $brandQ->fetch_assoc()) {
      echo "<div class='brand-slide'><a href='brandsprofile.php?id={$brand['id']}'><img src='{$brand['logo']}' alt='{$brand['name']}'></a></div>";
    }
    ?>
  </div>
</div>

<!-- Big Deals -->
<div class="bigdeal-main">
  <div class="big-deals-title">Big Deals</div>
  <div class="big-deals-subtitle">Don't miss out on our exclusive deals!</div>
  <div class="deals-grid">
    <?php
    $deal = $conn->query("SELECT * FROM big_deals LIMIT 1")->fetch_assoc();
    for ($i = 1; $i <= 4; $i++) {
      $img = trim($deal["image$i"]);
      $link = $deal["link$i"];
      if (!empty($img)) {
        echo "<div class='deal-card'><a href='{$link}' target='_blank'><img src='img/{$img}' alt='Big Deal {$i}'></a></div>";
      }
    }
    ?>
  </div>
</div>

<!-- Products -->
<div class="section-title">Discover <span>Products</span></div>
<div class="products" id="product-container">
  <?php
  $products = $conn->query("SELECT * FROM item ORDER BY RAND()");
  
  while ($item = $products->fetch_assoc()) {
    $id = $item['id'];
    $brand = $conn->query("SELECT name, logo FROM brands WHERE id={$item['brands_id']} LIMIT 1")->fetch_assoc();
    
    // Check if item is in cart/wishlist
    $inCart = false;
    $inWishlist = false;
    
    if ($uid > 0) {
      $inCart = $conn->query("SELECT id FROM cart WHERE u_id=$uid AND p_id=$id")->num_rows > 0;
      $inWishlist = $conn->query("SELECT id FROM wishlist WHERE u_id=$uid AND p_id=$id")->num_rows > 0;
    } else {
      $inCart = in_array($id, $_SESSION['cart']);
      $inWishlist = in_array($id, $_SESSION['wishlist']);
    }
    
    echo "<div class='product-card'>
            <div class='product-image'>
              <img src='prod/{$item['img1']}' alt='{$item['name']}'>
              <div class='label-launch'>Just Launched</div>
              <div class='wishlist-icon wish-btn " . ($inWishlist ? 'added' : '') . "' data-id='{$id}' data-type='wishlist'>" . ($inWishlist ? '✓' : '+') . "</div>
              <div class='rating'><i class='fa fa-star'></i> {$item['star']}</div>
            </div>
            <div class='product-info'>
              <div class='product-name'>{$item['name']}</div>
              <div class='product-price'>BDT {$item['price']}</div>
              <div><span class='product-original'>BDT {$item['max_price']}</span> <span class='product-discount'>{$item['discount']}% OFF</span></div>
              
              
              <div class='brand'>
        <div class='brand-logo'>
          <img src='{$brand['logo']}' alt='" . htmlspecialchars($brand['name']) . "'>
        </div>
        <div class='add-to-cart'>
          <button class='cart-btn " . ($inCart ? 'added' : '') . "' 
                  data-id='{$id}' 
                  data-type='cart'>" .
            ($inCart ? 'Remove from Cart' : 'Add to Cart') .
          "</button>
        </div>
      </div>
        </div></div>";
  }
  ?>
</div>

<!-- Footer -->
<footer>
    <a href="#" class="footer-btn">
        <i class="fas fa-tag"></i>
        <span>Offer</span>
    </a>
    <a href="#" class="footer-btn">
        <i class="fas fa-chart-line"></i>
        <span>Trending</span>
    </a>
    <a href="#" class="footer-btn active">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="#" class="footer-btn">
        <i class="fas fa-comment"></i>
        <span>Message</span>
    </a>
    <a href="#" class="footer-btn">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
</footer>

<!-- Login Modal -->
<div class="modal" id="loginModal">
  <div class="modal-content">
    <span class="close-modal" id="closeModal">&times;</span>
    <h2>Login Required</h2>
    <p>You need to login to add items to your cart or wishlist.</p>
    <div class="modal-buttons">
      <button class="modal-btn login-btn" onclick="window.location.href='login/login.php'">Login</button>
      <button class="modal-btn register-btn" onclick="window.location.href='login/register.php'">Register</button>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const loginModal = document.getElementById('loginModal');
  const closeModal = document.getElementById('closeModal');
  
  // Close modal
  closeModal.addEventListener('click', () => {
    loginModal.style.display = 'none';
  });
  
  // Close modal when clicking outside
  window.addEventListener('click', (e) => {
    if (e.target === loginModal) {
      loginModal.style.display = 'none';
    }
  });
  
  // Cart and wishlist button functionality
  function attachButtonListeners() {
    document.querySelectorAll('.cart-btn, .wish-btn').forEach(btn => {
      btn.addEventListener('click', async () => {
        // First check if user is logged in
        const isLoggedIn = await checkLoginStatus();
        
        if (!isLoggedIn) {
          loginModal.style.display = 'flex';
          return;
        }
        
        const pid = btn.dataset.id;
        const type = btn.dataset.type;
        
        fetch('', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `pid=${pid}&type=${type}`
        })
        .then(res => res.json())
        .then(data => {
          if (type === 'cart') {
            if (data.status === 'added') {
              btn.textContent = 'ADDED!';
              btn.classList.add('added');
              setTimeout(() => {
                btn.textContent = 'Remove from Cart';
              }, 1000);
            } else {
              btn.textContent = 'Add to Cart';
              btn.classList.remove('added');
            }
            document.querySelector('.cart-count').textContent = data.cart;
          } else {
            if (data.status === 'added') {
              btn.textContent = '✓';
              btn.classList.add('added');
            } else {
              btn.textContent = '+';
              btn.classList.remove('added');
            }
            document.querySelector('.wishlist-count').textContent = data.wishlist;
          }
        });
      });
    });
  }
  
  // Initial attachment of listeners
  attachButtonListeners();
  
  // Function to check login status
  async function checkLoginStatus() {
    try {
      const response = await fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'check_login=true'
      });
      
      const data = await response.json();
      return data.logged_in;
    } catch (error) {
      console.error('Error checking login status:', error);
      return false;
    }
  }

  // Gender filter functionality
  const genderButtons = document.querySelectorAll('.gender-btn');
  genderButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Update active state
      genderButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');
      
      // Get filter value
      const gender = this.dataset.gender;
      
      // Send AJAX request to filter products, categories, and brands
      fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `filter_gender=${gender}`
      })
      .then(res => res.json())
      .then(data => {
        // Update products
        document.getElementById('product-container').innerHTML = data.products;
        
        // Update categories
        document.getElementById('category-container').innerHTML = data.categories;
        
        // Update brands
        document.getElementById('brand-container').innerHTML = data.brands;
        
        // Reattach event listeners to new buttons
        attachButtonListeners();
        
        // Reset brand slider auto-scroll
        resetBrandSlider();
      });
    });
  });

  /******************************************************************************
   * BANNER SLIDER FUNCTIONALITY - IMPROVED WITH TOUCH AND CLICK CONTROLS
   * 
   * Features:
   * 1. Auto-sliding every 5 seconds
   * 2. Navigation arrows for manual control
   * 3. Dot indicators showing current slide
   * 4. Touch swipe support for mobile devices
   * 5. Pause on hover/interaction
   ******************************************************************************/
  const bannerSlider = document.querySelector('.banner-slider');
  const bannerSlides = document.querySelectorAll('.banner-slide');
  const bannerDots = document.querySelectorAll('.banner-dot');
  const prevBtn = document.querySelector('.prev-banner');
  const nextBtn = document.querySelector('.next-banner');
  let currentBannerSlide = 0;
  let bannerInterval;
  let isDragging = false;
  let startPos = 0;
  let currentTranslate = 0;
  let prevTranslate = 0;
  let animationID;
  
  // Set initial slide positions
  bannerSlides.forEach((slide, index) => {
    slide.style.transform = `translateX(${index * 100}%)`;
  });
  
  // Function to update banner position
  function updateBannerPosition() {
    bannerSlider.style.transform = `translateX(-${currentBannerSlide * 100}%)`;
    updateDots();
  }
  
  // Function to update dot indicators
  function updateDots() {
    bannerDots.forEach((dot, index) => {
      dot.classList.toggle('active', index === currentBannerSlide);
    });
  }
  
  // Function to go to specific slide
  function goToSlide(index) {
    currentBannerSlide = (index + bannerSlides.length) % bannerSlides.length;
    updateBannerPosition();
    resetBannerInterval();
  }
  
  // Function to go to next slide
  function nextSlide() {
    currentBannerSlide = (currentBannerSlide + 1) % bannerSlides.length;
    updateBannerPosition();
    resetBannerInterval();
  }
  
  // Function to go to previous slide
  function prevSlide() {
    currentBannerSlide = (currentBannerSlide - 1 + bannerSlides.length) % bannerSlides.length;
    updateBannerPosition();
    resetBannerInterval();
  }
  
  // Function to start auto-sliding
  function startBannerInterval() {
    bannerInterval = setInterval(nextSlide, 5000);
  }
  
  // Function to reset interval (when user interacts)
  function resetBannerInterval() {
    clearInterval(bannerInterval);
    startBannerInterval();
  }
  
  // Initialize banner slider
  function initBannerSlider() {
    updateDots();
    startBannerInterval();
    
    // Mouse/touch event listeners
    bannerSlider.addEventListener('mousedown', dragStart);
    bannerSlider.addEventListener('touchstart', dragStart);
    
    bannerSlider.addEventListener('mousemove', drag);
    bannerSlider.addEventListener('touchmove', drag);
    
    bannerSlider.addEventListener('mouseup', dragEnd);
    bannerSlider.addEventListener('mouseleave', dragEnd);
    bannerSlider.addEventListener('touchend', dragEnd);
    
    // Navigation buttons
    prevBtn.addEventListener('click', prevSlide);
    nextBtn.addEventListener('click', nextSlide);
    
    // Dot indicators
    bannerDots.forEach((dot, index) => {
      dot.addEventListener('click', () => goToSlide(index));
    });
    
    // Pause on hover
    bannerSlider.addEventListener('mouseenter', () => clearInterval(bannerInterval));
    bannerSlider.addEventListener('mouseleave', startBannerInterval);
  }
  
  // Touch/drag functionality
  function dragStart(e) {
    if (e.type === 'touchstart') {
      startPos = e.touches[0].clientX;
    } else {
      startPos = e.clientX;
      e.preventDefault();
    }
    
    isDragging = true;
    clearInterval(bannerInterval);
    
    // Get current translateX value
    const style = window.getComputedStyle(bannerSlider);
    const matrix = new WebKitCSSMatrix(style.transform);
    prevTranslate = matrix.m41;
    currentTranslate = prevTranslate;
    
    // Cancel any ongoing animations
    if (animationID) {
      cancelAnimationFrame(animationID);
    }
  }
  
  function drag(e) {
    if (!isDragging) return;
    
    const currentPosition = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
    const diff = currentPosition - startPos;
    currentTranslate = prevTranslate + diff;
    
    // Apply the translation
    bannerSlider.style.transform = `translateX(${currentTranslate}px)`;
  }
  
  function dragEnd() {
    if (!isDragging) return;
    isDragging = false;
    
    // Determine if we should change slide based on drag distance
    const movedBy = currentTranslate - prevTranslate;
    
    if (movedBy < -50 && currentBannerSlide < bannerSlides.length - 1) {
      // Swiped left
      nextSlide();
    } else if (movedBy > 50 && currentBannerSlide > 0) {
      // Swiped right
      prevSlide();
    } else {
      // Not enough movement - return to current slide
      updateBannerPosition();
    }
    
    // Restart the interval
    resetBannerInterval();
  }
  
  // Initialize the banner slider
  initBannerSlider();

  /******************************************************************************
   * BRAND SLIDER FUNCTIONALITY
   ******************************************************************************/
  let brandSliderInterval;
  function resetBrandSlider() {
    clearInterval(brandSliderInterval);
    startBrandSlider();
  }
  
  function startBrandSlider() {
    const brandSlider = document.querySelector('.brand-slider');
    const brandSlides = document.querySelectorAll('.brand-slide');
    let brandScrollPosition = 0;
    const brandSlideWidth = brandSlides[0]?.offsetWidth || 120;
    
    function autoScrollBrands() {
      brandScrollPosition += brandSlideWidth;
      
      // If we've scrolled all the way, reset to start
      if (brandScrollPosition >= brandSlider.scrollWidth - brandSlider.offsetWidth) {
        brandScrollPosition = 0;
        // Small delay before restarting to make the loop less obvious
        setTimeout(() => {
          brandSlider.scrollTo({
            left: brandScrollPosition,
            behavior: 'smooth'
          });
        }, 1000);
      } else {
        brandSlider.scrollTo({
          left: brandScrollPosition,
          behavior: 'smooth'
        });
      }
    }
    
    // Start auto-scrolling if there are brands
    if (brandSlides.length > 0) {
      brandSliderInterval = setInterval(autoScrollBrands, 3000);
    }
  }
  
  // Initialize brand slider
  startBrandSlider();
  
  /******************************************************************************
   * PRODUCT CLICK HANDLER - REDIRECT TO SINGLE PRODUCT PAGE
   ******************************************************************************/
  document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', (e) => {
      // Only proceed if the click wasn't on a button or link
      if (!e.target.closest('button') && !e.target.closest('a')) {
        const productId = card.querySelector('.cart-btn')?.dataset.id;
        if (productId) {
          window.location.href = `single_product.php?id=${productId}`;
        }
      }
    });
  });
});
</script>
</body>
</html>