<?php
session_start();
require "inc/head.php";
require "inc/sidebar.php";
require "../config.php";

// Fetch shop name
$shop_id = $_SESSION['shop_id'] ?? 0;
$shop_row = $conn->query("SELECT name FROM shop WHERE id='$shop_id'")->fetch_assoc();
$shop_name = $shop_row['name'] ?? '';

$msg = '';
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

    // Upload images
    $uploadDir = "../prod/";
    $imgNames = [];
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if (!empty($tmp)) {
            $imgName = time() . rand(1000, 9999) . "_" . basename($_FILES['images']['name'][$i]);
            move_uploaded_file($tmp, $uploadDir . $imgName);
            $imgNames[] = $imgName;
        }
    }
    [$img1, $img2, $img3, $img4] = array_pad($imgNames, 4, '');

   $sql = "INSERT INTO item 
(name, brand, num, cat, subcat, state, size, colour, des_short, specs, price, discount, max_price, shop, shop_id, img1, img2, img3, img4, person)
VALUES 
('$name', '$brand', '$stock', '$cat', '$subcat', '$state', '$sizes', '$colors', '$description', '$specs', '$price', '$discount', '$max_price', '$shop_name', '$shop_id', '$img1', '$img2', '$img3', '$img4', '$person')";

    if ($conn->query($sql)) {
        header("Location: add_product.php?success=1");
        exit;
    } else {
        header("Location: add_product.php?error=1");
        exit;
    }
}

$toast = '';
if (isset($_GET['success'])) {
  $toast = "<script>window.addEventListener('DOMContentLoaded', () => showToast('Product Request successfully Send!', true));</script>";
}
if (isset($_GET['error'])) {
  $toast = "<script>window.addEventListener('DOMContentLoaded', () => showToast('Failed to Send Request!', false));</script>";
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Add Product</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
  <style>
    .image-box { position: relative; width: 100%; max-width: 180px; }
    .image-box img { width: 100%; border-radius: 8px; margin-top: 5px; }
    .remove-btn {
        position: absolute; top: -10px; right: -10px;
        background: red; color: white; border: none;
        border-radius: 50%; width: 24px; height: 24px;
        font-size: 14px; cursor: pointer;
    }
    .select2-container--default .select2-selection--multiple {
        padding: 6px; min-height: 38px;
    }
    
    .custom-toast {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  min-width: 260px;
  padding: 12px 20px;
  border-radius: 8px;
  font-weight: 500;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  gap: 12px;
  animation: fadeInRight 0.5s ease;
}
.toast-success {
  background-color: #28a745;
  color: white;
}
.toast-error {
  background-color: #dc3545;
  color: white;
}
.toast-icon {
  font-size: 18px;
}
@keyframes fadeInRight {
  from {
    opacity: 0;
    transform: translateX(100px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
  </style>
</head>
<body>
<div class="content">
  <div class="container mt-4">
    <div class="card p-4">
      <h4 class="mb-4">Add Product</h4>
      <?= $msg ?>
      <form method="POST" enctype="multipart/form-data">
        <label class="mb-2 fw-bold">Upload Product Images (Max 4)</label>
        <div class="d-flex flex-wrap gap-3 mb-3" id="imageGrid">
          <div class="image-box">
            <input type="file" name="images[]" class="form-control image-input" onchange="previewImage(this)" required>
            <div class="preview-box"></div>
          </div>
        </div>
        <button type="button" class="btn btn-outline-primary mb-4" onclick="addImageBox()">Add Image</button>

        <div class="row mb-3">
          <div class="col-md-6">
            <label>Name</label>
            <input type="text" name="productName" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label>Brand</label>
            <input type="text" name="brand" class="form-control" required>
          </div>
        </div>

        <div class="row mb-3">
  <div class="col-md-4">
    <label>Price (Without Discount)</label>
    <input type="number" id="basePrice" class="form-control" required>
    <input type="hidden" name="maxPrice" id="maxPrice">
  </div>
  <div class="col-md-4">
    <label>Discount (%)</label>
    <input type="number" id="discountPercent" class="form-control" value="0" required>
  </div>
  <div class="col-md-4">
    <label>Final Price</label>
    <input type="number" id="finalPrice" name="productPrice" class="form-control" readonly>
    <input type="hidden" name="productDiscount" id="hiddenDiscount">
  </div>
</div>


        <div class="mb-3">
          <label>Stock</label>
          <input type="number" name="num" class="form-control" required>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label>Category</label>
            <select name="cat_" id="category" class="form-control" onchange="getSubcategories()" required>
              <option>--SELECT CATEGORY--</option>
              <?php include '../back/category.php';
              foreach ($_cat_ as $__c) echo '<option value="'.$__c[0].'">'.$__c[2].'</option>'; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label>Subcategory</label>
            <select name="subcategory" id="subcategory" class="form-control">
              <option>--SELECT SUB CATEGORY--</option>
            </select>
          </div>
        </div>
         <div class="row mb-3">
          <div class="col-md-6">
            <label>Select Person</label>
            <select name="person" id="person" class="form-control">
              <option>--SELECT Person--</option>
              <option>Men</option>
              <option>Women</option>
              <option>both</option>
            </select>
          </div>
        <div class="col-md-6">
          <label>District</label>
          <select name="state" class="form-control" required>
            <option>--SELECT DISTRICT--</option>
            <?php foreach ($_state_ as $state) echo "<option value='$state'>$state</option>"; ?>
          </select>
        </div>
        </div>

        <div class="mb-3">
          <label>Sizes (Multi-select)</label>
          <select name="size[]" class="form-control select2" multiple>
            <?php foreach (['XS','S','M','L','XL','XXL','3XL','4XL'] as $s) echo "<option value='$s'>$s</option>"; ?>
          </select>
        </div>

        <div class="mb-3">
          <label>Colors (Multi-select)</label>
          <select name="colour[]" id="colorSelect" class="form-control select2" multiple>
            <?php
            $colors = [
              "White" => "#FFFFFF", "Black" => "#000000", "Red" => "#FF0000",
              "Blue" => "#0000FF", "Sky Blue" => "#87CEEB", "Pink" => "#FFC0CB",
              "Green" => "#008000", "Olive" => "#808000", "Navy" => "#000080",
              "Purple" => "#800080", "Grey" => "#808080", "Brown" => "#A52A2A",
              "Orange" => "#FFA500", "Yellow" => "#FFFF00", "Beige" => "#F5F5DC",
              "Maroon" => "#800000", "Teal" => "#008080", "Mint" => "#98FF98",
              "Lavender" => "#E6E6FA", "Mixed Color" => "#CCCCCC"
            ];
            foreach ($colors as $name => $hex) echo "<option value='{$name}:{$hex}'>{$name}</option>";
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label>Pick Custom Color</label><br>
          <input type="color" id="colorPicker" />
          <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="addCustomColor()">Add Color</button>
        </div>

        <div class="mb-3">
          <label>Description</label>
          <textarea name="description" id="descriptionEditor" class="form-control"></textarea>
        </div>

        <div class="mb-3">
          <label>Specifications</label>
          <textarea name="specs" id="specsEditor" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit Product</button>
      </form>
    </div>
  </div>
  <br><br><br><br>
</div>

<?= $toast ?>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
let imageCount = 1;

function addImageBox() {
  if (imageCount >= 4) return alert("Maximum 4 images allowed.");
  imageCount++;
  const box = document.createElement('div');
  box.className = "image-box";
  box.innerHTML = `
    <button type="button" class="remove-btn" onclick="this.parentElement.remove(); imageCount--;">×</button>
    <input type="file" name="images[]" class="form-control image-input" onchange="previewImage(this)" required>
    <div class="preview-box"></div>`;
  document.getElementById('imageGrid').appendChild(box);
}

function previewImage(input) {
  const reader = new FileReader();
  reader.onload = e => {
    input.nextElementSibling.innerHTML = `<img src="${e.target.result}" />`;
  };
  reader.readAsDataURL(input.files[0]);
}

function updateFinalPrice() {
  const base = parseFloat(document.getElementById('basePrice').value) || 0;
  const discount = parseFloat(document.getElementById('discountPercent').value) || 0;
  const final = base - (base * discount / 100);
  document.getElementById('finalPrice').value = final.toFixed(2);
  document.getElementById('hiddenDiscount').value = discount;
  document.getElementById('maxPrice').value = base;
}

document.getElementById('basePrice').addEventListener('input', updateFinalPrice);
document.getElementById('discountPercent').addEventListener('input', updateFinalPrice);

function addCustomColor() {
  const hex = document.getElementById('colorPicker').value;
  fetch(`https://www.thecolorapi.com/id?hex=${hex.replace('#','')}`)
    .then(res => res.json())
    .then(data => {
      const colorName = data.name.value;
      const value = `${colorName}:${hex}`;
      const select = $('#colorSelect');
      if (!select.find("option[value='" + value + "']").length) {
        const option = new Option(colorName, value, true, true);
        select.append(option).trigger('change');
      }
    });
}

function getSubcategories() {
  const categoryId = $('#category').val();
  $.post('../back/category.php', { category_id: categoryId, action: "cat" }, function (response) {
    $('#subcategory').html('<option>--SELECT SUB CATEGORY--</option>');
    response.forEach((subcat, index) => {
      $('#subcategory').append(`<option value="${index}">${subcat}</option>`);
    });
  }, 'json');
}

// Select2 Initialization
$('.select2').select2();

// TinyMCE Initialization
tinymce.init({
  selector:'#descriptionEditor,#specsEditor',
  height: 250,
  menubar: false,
  plugins: 'lists link image table code',
  toolbar: 'undo redo | styles | bold italic underline | fontselect fontsizeselect forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | removeformat'
});
</script>
<script>
function showToast(message, isSuccess = true) {
  const icon = isSuccess
    ? '<span class="toast-icon">✅</span>'
    : '<span class="toast-icon">❌</span>';

  const toast = $(`
    <div class="custom-toast ${isSuccess ? 'toast-success' : 'toast-error'}">
      ${icon}<span>${message}</span>
    </div>
  `);

  $("body").append(toast);
  setTimeout(() => {
    toast.fadeOut(500, () => toast.remove());
  }, 3000);
}
</script>

</body>
</html>
