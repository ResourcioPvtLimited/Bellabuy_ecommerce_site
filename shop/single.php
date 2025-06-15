<?php
session_start();
require "inc/head.php";
require "inc/sidebar.php";
require "../config.php";

$product_id = intval($_GET['id'] ?? 0);
if (!$product_id) die("Invalid product ID");

$shop_id = $_SESSION['shop_id'] ?? 0;
$shop_row = $conn->query("SELECT name FROM shop WHERE id='$shop_id'")->fetch_assoc();
$shop_name = $shop_row['name'] ?? '';

$product = $conn->query("SELECT * FROM item WHERE id='$product_id' AND shop_id='$shop_id'")->fetch_assoc();
if (!$product) die("Product not found");

$notification = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['productName'];
    $brand = $_POST['brand'];
    $stock = $_POST['num'];
    $cat = $_POST['cat_'];
    $subcat = $_POST['subcategory'];
    $state = $_POST['state'];
    $sizes = implode(",", $_POST['size'] ?? []);
    $colors = implode(",", $_POST['colour'] ?? []);
    $description = $conn->real_escape_string($_POST['description']);
    $specs = $conn->real_escape_string($_POST['specs']);
    $price = $_POST['productPrice'];
    $discount = $_POST['productDiscount'];
    $max_price = $_POST['maxPrice'];
    $person = $_POST['person'];

    $imgFields = ['img1', 'img2', 'img3', 'img4'];
    $uploadDir = "../prod/";
    $updatedImgs = [];

    foreach ($imgFields as $i => $field) {
        if (!empty($_FILES['images']['tmp_name'][$i])) {
            $imgName = time() . rand(1000, 9999) . "_" . basename($_FILES['images']['name'][$i]);
            move_uploaded_file($_FILES['images']['tmp_name'][$i], $uploadDir . $imgName);
            $updatedImgs[$field] = $imgName;
        }
    }

    foreach ($imgFields as $f) {
        if (!isset($updatedImgs[$f])) {
            $updatedImgs[$f] = $product[$f];
        }
    }

    $update = $conn->query("
        UPDATE item SET
            name='$name', brand='$brand', num='$stock', cat='$cat', subcat='$subcat',
            state='$state', size='$sizes', colour='$colors',
            des_short='$description', specs='$specs',
            price='$price', discount='$discount', max_price='$max_price',
            shop='$shop_name', person='$person',
            img1='{$updatedImgs['img1']}', img2='{$updatedImgs['img2']}',
            img3='{$updatedImgs['img3']}', img4='{$updatedImgs['img4']}'
        WHERE id='$product_id' AND shop_id='$shop_id'
    ");

    if ($update) {
        $notification = "<div class='alert alert-success text-center'>✅ Product updated successfully!</div>";
        $product = $conn->query("SELECT * FROM item WHERE id='$product_id' AND shop_id='$shop_id'")->fetch_assoc();
    } else {
        $notification = "<div class='alert alert-danger text-center'>❌ Failed to update product.</div>";
    }
}

function explodeOptions($str) {
    return array_filter(array_map('trim', explode(',', $str)));
}

$selectedSizes = explodeOptions($product['size']);
$selectedColors = explodeOptions($product['colour']);
$allColorOptions = [
  "White" => "#FFFFFF", "Black" => "#000000", "Red" => "#FF0000", "Blue" => "#0000FF",
  "Green" => "#008000", "Yellow" => "#FFFF00", "Pink" => "#FFC0CB", "Purple" => "#800080",
  "Grey" => "#808080", "Orange" => "#FFA500", "Brown" => "#A52A2A", "Teal" => "#008080"
];
?>

<!DOCTYPE html>
<html>
<head>
  <title>Update Product</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
</head>
<body>

<div class="content">
  <?= $notification ?>
  <div class="card p-4">
    <h4 class="mb-4">Update Product</h4>
    <form method="POST" enctype="multipart/form-data">
      <div class="d-flex flex-wrap gap-3 mb-3" id="imageGrid">
        <?php foreach (["img1", "img2", "img3", "img4"] as $img): ?>
          <div class="image-box">
            <?php if (!empty($product[$img])): ?>
              <img src="../prod/<?= $product[$img] ?>" class="preview-box" style="width:100px;height:100px;object-fit:cover;border-radius:6px;margin-bottom:5px;">
            <?php endif; ?>
            <input type="file" name="images[]" class="form-control">
          </div>
        <?php endforeach; ?>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label>Name</label>
          <input type="text" name="productName" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="col-md-6">
          <label>Brand</label>
          <input type="text" name="brand" class="form-control" value="<?= htmlspecialchars($product['brand']) ?>" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4">
          <label>Price (Without Discount)</label>
          <input type="number" id="basePrice" class="form-control" value="<?= $product['max_price'] ?>" required>
          <input type="hidden" name="maxPrice" id="maxPrice" value="<?= $product['max_price'] ?>">
        </div>
        <div class="col-md-4">
          <label>Discount (%)</label>
          <input type="number" id="discountPercent" class="form-control" value="<?= $product['discount'] ?>" required>
        </div>
        <div class="col-md-4">
          <label>Final Price</label>
          <input type="number" id="finalPrice" name="productPrice" class="form-control" value="<?= $product['price'] ?>" readonly>
          <input type="hidden" name="productDiscount" id="hiddenDiscount" value="<?= $product['discount'] ?>">
        </div>
      </div>

      <div class="mb-3">
        <label>Stock</label>
        <input type="number" name="num" class="form-control" value="<?= $product['num'] ?>" required>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label>Sizes</label>
          <select name="size[]" class="form-control select2" multiple>
            <?php foreach (["XS","S","M","L","XL","XXL","3XL","4XL"] as $size): ?>
              <option value="<?= $size ?>" <?= in_array($size, $selectedSizes) ? 'selected' : '' ?>><?= $size ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label>Colors</label>
          <select name="colour[]" id="colorSelect" class="form-control select2" multiple>
            <?php foreach ($allColorOptions as $name => $hex): 
              $val = "$name:$hex";
              $selected = in_array($val, $selectedColors) ? 'selected' : '';
            ?>
              <option value="<?= $val ?>" <?= $selected ?>><?= $name ?></option>
            <?php endforeach; ?>
          </select>
          <div class="mt-2">
            <label>Pick Custom Color</label>
            <input type="color" id="colorPicker">
            <button type="button" onclick="addCustomColor()" class="btn btn-sm btn-outline-secondary ms-2">Add Color</button>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['des_short']) ?></textarea>
      </div>

      <div class="mb-3">
        <label>Specifications</label>
        <textarea name="specs" class="form-control" rows="4"><?= htmlspecialchars($product['specs']) ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary w-100">Update Product</button>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function updateFinalPrice() {
  const base = parseFloat(document.getElementById('basePrice').value) || 0;
  const discount = parseFloat(document.getElementById('discountPercent').value) || 0;
  const final = base - (base * discount / 100);
  document.getElementById('finalPrice').value = final.toFixed(2);
  document.getElementById('hiddenDiscount').value = discount;
  document.getElementById('maxPrice').value = base;
}

function addCustomColor() {
  const hex = document.getElementById('colorPicker').value;
  fetch(`https://www.thecolorapi.com/id?hex=${hex.replace('#','')}`)
    .then(res => res.json())
    .then(data => {
      const name = data.name.value;
      const value = `${name}:${hex}`;
      const select = $('#colorSelect');
      if (!select.find("option[value='" + value + "']").length) {
        const option = new Option(name, value, true, true);
        select.append(option).trigger('change');
      } else {
        const currentVals = select.val() || [];
        if (!currentVals.includes(value)) {
          currentVals.push(value);
          select.val(currentVals).trigger('change');
        }
      }
    });
}

document.getElementById('basePrice').addEventListener('input', updateFinalPrice);
document.getElementById('discountPercent').addEventListener('input', updateFinalPrice);
updateFinalPrice();
$('.select2').select2();
</script>
</body>
</html>
