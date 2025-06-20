<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,500;0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" rel="stylesheet">
  <link rel="stylesheet" href="test.css">
  <style>
  :root {
    --clr-light: rgba(115, 128, 236, 0.1);
    --clr-primary: #7380ec;
  }

  .menu-item a {
    display: flex;
    align-items: center;
    color: inherit;
    text-decoration: none;
   
    
  }

  
  .menu-item a.active {
   
    color: var(--clr-primary);
    font-weight: 600;
    align-items: center;
     border-bottom: 1px var(--clr-primary) solid;
     max-width:100%;
  }

  .menu-item a.active span.material-symbols-sharp {
    color: var(--clr-primary);
    align-items: center;
  }

  .menu-item a span.material-symbols-sharp {
    font-size: 20px;
    transition: color 0.3s ease;
    align-items: center;
  }

  .mobile-menu-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    align-items: center;
    text-align:center;
    color: inherit;
    text-decoration: none;
    transition: background 0.3s ease, color 0.3s ease;
    border-radius: 10px;
  }

  .mobile-menu-item.active {
    background-color: var(--clr-light);
    color: var(--clr-primary);
    font-weight: 600;
  }

  .mobile-menu-item.active span.material-symbols-sharp {
    color: var(--clr-primary);
  }
</style>

</head>
<body>
  <div class="header">
    <div class="logo">Shop <span style="color: var(--clr-danger)">DashBoard</span></div>
    <div id="fullscreenIcon" title="Enter Fullscreen">
      <span class="material-symbols-sharp">fullscreen</span>
    </div>
    <div class="theme-toggler" id="themeToggler">
      <span class="material-symbols-sharp">light_mode</span>
      <span class="material-symbols-sharp">dark_mode</span>
    </div>
    <div class="menu-toggle" onclick="toggleSidebarMobile()">
      <span class="material-symbols-sharp">menu</span>
    </div>
  </div>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="sidebar" id="sidebar">
    <div>
      <div class="search-box">
        <span class="material-symbols-sharp">search</span>
        <input type="text" placeholder="Search menu..." id="menuSearch">
      </div>
      <div class="no-results">No menu items found</div>

      <ul class="menu">
        <li class="menu-item"><a href="index.php"><span class="material-symbols-sharp menu-icon">grid_view</span><span> Dashboard</span></a></li>
        <li class="menu-item"><a href="profile.php"><span class="material-symbols-sharp menu-icon">settings</span><span> Profile</span></a></li>
        <li class="menu-item"><a href="add_product.php"><span class="material-symbols-sharp menu-icon">add</span><span> Add Product</span></a></li>
        <li class="menu-item"><a href="products.php"><span class="material-symbols-sharp menu-icon">receipt_long</span><span> Products</span></a></li>
        <li class="menu-item"><a href="disable.php"><span class="material-symbols-sharp menu-icon">mail_outline</span><span> Disabled Products</span></a></li>
        <li class="menu-item"><a href="on_request.php"><span class="material-symbols-sharp menu-icon">pending_actions</span><span> On Request</span></a></li>
        <li class="menu-item"><a href="del_ord.php"><span class="material-symbols-sharp menu-icon">local_shipping</span><span> Delivered Orders</span></a></li>
        <li class="menu-item"><a href="pen_ord.php"><span class="material-symbols-sharp menu-icon">insights</span><span> Pending Orders</span></a></li>
        <li class="menu-item"><a href="reviews.php"><span class="material-symbols-sharp menu-icon">report</span><span> Reviews</span></a></li>
        <li class="menu-item"><a href="withdraw.php"><span class="material-symbols-sharp menu-icon">account_balance_wallet</span><span> Withdraw</span></a></li>
        <li class="menu-item"><a href="out.php"><span class="material-symbols-sharp menu-icon"> logout</span><span>Logout</span></a></li>
      </ul>
    </div>
    <div class="collapse-btn" id="collapseBtn">⇦</div>
    <br><br><br><br><br><br><br>
  </div>

  

  <!-- MOBILE BOTTOM MENU -->
  <div class="mobile-bottom-menu" id="mobileBottomMenu">
    <div class="mobile-menu-toggle" onclick="toggleMobileMenu()">
      <span class="material-symbols-sharp">expand_less</span>
    </div>
    <div class="mobile-menu-items" id="quickAccessMenu">
      <a class="mobile-menu-item" href="index.php">
        <span class="material-symbols-sharp">grid_view</span><span>Dashboard</span>
      </a>
      <a class="mobile-menu-item" href="products.php">
        <span class="material-symbols-sharp">receipt_long</span><span>Products</span>
      </a>
      <a class="mobile-menu-item" href="withdraw.php">
        <span class="material-symbols-sharp">account_balance_wallet</span><span>Withdraw</span>
      </a>
      <a class="mobile-menu-item" href="profile.php">
        <span class="material-symbols-sharp">settings</span><span>Profile</span>
      </a>
    </div>
    <div class="mobile-menu-items" id="fullMenu">
      
      
      <a class="mobile-menu-item" href="add_product.php"><span class="material-symbols-sharp">add</span><span>Add Product</span></a>
     
      <a class="mobile-menu-item" href="disable.php"><span class="material-symbols-sharp">mail_outline</span><span>Disabled</span></a>
      <a class="mobile-menu-item" href="del_ord.php"><span class="material-symbols-sharp">local_shipping</span><span>Delivered</span></a>
      <a class="mobile-menu-item" href="pen_ord.php"><span class="material-symbols-sharp">insights</span><span>Pending</span></a>
      <a class="mobile-menu-item" href="reviews.php"><span class="material-symbols-sharp">report</span><span>Reviews</span></a>
      <a class="mobile-menu-item" href="withdraw.php"><span class="material-symbols-sharp">account_balance_wallet</span><span>Withdraw</span></a>
      <a class="mobile-menu-item" href="out.php"><span class="material-symbols-sharp">logout</span><span>Logout</span></a>
    </div>
  </div>



  <script src="inc/script.js"></script>
</body>
</html>
