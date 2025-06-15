<?php
$conn = new mysqli("localhost", "alokito2_sadi", "sadi9507@#", "alokito2_ecom");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search | SION</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body { margin: 0; font-family: sans-serif; background: #f7f7f7; }
    header {
      background: white; padding: 10px 15px;
      display: flex; justify-content: space-between; align-items: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 999;
    }
    .logo { font-weight: bold; font-size: 20px; color: #7A3FFC; }
    .search-bar { flex: 1; margin: 0 15px; display: flex; align-items: center; background: #eee; border-radius: 20px; padding: 5px 10px; }
    .search-bar i { margin-right: 10px; color: #999; }
    .search-bar input { border: none; background: none; outline: none; width: 100%; padding: 6px; }
    .header-icons { display: flex; gap: 15px; }
    .header-icons i { font-size: 18px; color: #333; position: relative; }
    .header-icons span { background: #7A3FFC; color: white; border-radius: 10px; font-size: 10px; padding: 1px 5px; position: absolute; top: -8px; right: -10px; }
    .min-title { display: flex; align-items: center; justify-content: center; height: 50px; background-color: #fff; font-weight: bold; font-size: 16px; position: relative; }
    .arroeicon { position: absolute; left: 10px; font-size: 18px; cursor: pointer; }
    .products { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding: 15px; }
    .product-card { border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.05); transition: transform 0.3s; }
    .product-card:hover { transform: translateY(-5px); }
    .product-image { position: relative; background: #ddd; display: flex; justify-content: center; align-items: center; height: 250px; }
    .product-image img { width: 300px; height: 300px; object-fit: cover; }
    .label-launch { position: absolute; top: 5px; left: 5px; background: #7A3FFC; color: white; font-size: 10px; padding: 3px 6px; border-radius: 4px; }
    .wishlist-icon { position: absolute; top: 10px; right: 10px; font-size: 22px; cursor: pointer; color: #333; }
    .rating { position: absolute; bottom: 10px; left: 10px; background: white; padding: 3px 8px; font-size: 13px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 3px; }
    .product-info { padding: 12px; }
    .product-name { font-weight: bold; font-size: 14px; margin-bottom: 5px; }
    .product-price { font-weight: bold; color: #000; font-size: 15px; }
    .product-original { font-size: 13px; text-decoration: line-through; color: #888; margin-left: 5px; }
    .product-discount { font-size: 12px; color: #C25400; margin-left: 5px; }
    .brand { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; }
    .brand img { height: 20px; }
    .add-to-cart button { padding: 5px 10px; border: 2px solid #000; background: #fff; border-radius: 20px; cursor: pointer; font-size: 11px; font-weight: bold; transition: .3s; }
    .add-to-cart button:hover { background: #000; color: #fff; }
    footer { position: fixed; bottom: 0; left: 0; right: 0; background: white; display: flex; justify-content: space-around; padding: 10px 0; box-shadow: 0 -2px 5px rgba(0,0,0,0.1); }
    .footer-btn { display: flex; flex-direction: column; align-items: center; font-size: 12px; color: #999; text-decoration: none; }
    .footer-btn i { font-size: 18px; margin-bottom: 2px; }
    .footer-btn.active { color: #7A3FFC; }
  </style>
</head>
<body>

<header>
  <div class="logo">SION</div>
  <div class="search-bar">
    <form action="search.php" method="GET">
      <i class="fas fa-search"></i>
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search for products..." required>
    </form>
  </div>
  <div class="header-icons">
    <div class="wishlist-icon-container"><i class="fas fa-heart"></i><span class="wishlist-count">0</span></div>
    <div class="cart-icon-container"><i class="fas fa-shopping-bag"></i><span class="cart-count">0</span></div>
  </div>
</header>

<div class="min-title">
  <i class="fas fa-arrow-left arroeicon" onclick="history.back()"></i>
  <div class="page-title">Search Results</div>
</div>

<div class="products">
<?php
if ($search !== '') {
    $keywords = explode(" ", $search);
    $where = [];
    foreach ($keywords as $word) {
        $w = "%$word%";
        $where[] = "(item.name LIKE '$w' OR item.des_short LIKE '$w' OR item.brand LIKE '$w' OR item.colour LIKE '$w')";
    }
    $whereClause = implode(" AND ", $where);
    $query = "SELECT item.*, brands.logo AS brand_logo 
              FROM item 
              LEFT JOIN brands ON item.brand = brands.name 
              WHERE $whereClause AND item.disable = '0'";
    $res = $conn->query($query);

    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $discount = $row['discount'];
            $final = $row['price'] - ($row['price'] * ($discount / 100));
            echo '
            <div class="product-card">
              <div class="product-image">
                <img src="prod/'.$row['img1'].'" alt="'.$row['name'].'">
                <div class="label-launch">Just Launched</div>
                <div class="wishlist-icon">♡</div>
                <div class="rating"><i class="fas fa-star"></i><p>4.9</p></div>
              </div>
              <div class="product-info">
                <div class="product-name">'.htmlspecialchars($row['name']).'</div>
                <span class="product-price">BDT '.number_format($final).'</span>
                <span class="product-original">BDT '.$row['price'].'</span>
                <span class="product-discount">'.$discount.'% OFF</span>
                <div class="brand">
                  <img src="'.$row['brand_logo'].'" alt="">
                  <div class="add-to-cart"><button>Add To Cart</button></div>
                </div>
              </div>
            </div>';
        }
    } else {
        echo "<p style='padding:20px;'>No products found for '<strong>".htmlspecialchars($search)."</strong>'</p>";
    }
} else {
    echo "<p style='padding:20px;'>Please enter a search term.</p>";
}
?>
</div>

<footer>
  <a href="#" class="footer-btn"><i class="fas fa-tag"></i><span>Offer</span></a>
  <a href="#" class="footer-btn"><i class="fas fa-chart-line"></i><span>Trending</span></a>
  <a href="#" class="footer-btn active"><i class="fas fa-home"></i><span>Home</span></a>
  <a href="#" class="footer-btn"><i class="fas fa-comment"></i><span>Message</span></a>
  <a href="#" class="footer-btn"><i class="fas fa-user"></i><span>Profile</span></a>
</footer>

<script>
document.querySelectorAll('.add-to-cart button').forEach(button => {
  button.addEventListener('click', () => {
    let cart = document.querySelector('.cart-count');
    cart.textContent = parseInt(cart.textContent) + 1;
    button.textContent = "Added!";
    setTimeout(() => { button.textContent = "Add To Cart"; }, 1000);
  });
});
document.querySelectorAll('.wishlist-icon').forEach(icon => {
  icon.addEventListener('click', () => {
    icon.classList.toggle('active');
    icon.textContent = icon.classList.contains('active') ? '♥' : '♡';
    let w = document.querySelector('.wishlist-count');
    let val = parseInt(w.textContent);
    w.textContent = icon.classList.contains('active') ? val + 1 : val - 1;
  });
});
</script>

</body>
</html>
