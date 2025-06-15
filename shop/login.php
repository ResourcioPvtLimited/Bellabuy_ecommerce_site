<?php
session_start();
 require "../config.php";

// Registration
if (isset($_POST['register'])) {
    $shop_name = $_POST['shop_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $vendor_name = $_POST['vendor_name'];

    $nid_front = $_FILES['nid_front']['name'];
    $nid_back = $_FILES['nid_back']['name'];
    move_uploaded_file($_FILES['nid_front']['tmp_name'], "uploads/$nid_front");
    move_uploaded_file($_FILES['nid_back']['tmp_name'], "uploads/$nid_back");

    $stmt = $conn->prepare("INSERT INTO shop (name, email, password, phone, address, vendor_name, nid_front, nid_back) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $shop_name, $email, $password, $phone, $address, $vendor_name, $nid_front, $nid_back);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Registration successful! You can now login.');</script>";
}

// Login
if (isset($_POST['login'])) {
    $email_or_phone = $_POST['email_or_phone'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM shop WHERE (email = ? OR phone = ?) AND password = ?");
    $stmt->bind_param("sss", $email_or_phone, $email_or_phone, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION['shop_id'] = $row['id'];
        echo "<script>location.href='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Invalid login credentials');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Vendor Login / Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to bottom right, #ffe6f0, #fff2e6);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .container {
      display: flex;
      width: 900px;
      background: #fff;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      border-radius: 12px;
      overflow: hidden;
    }
    .promo {
      flex: 1;
      background: url('https://assets.myntassets.com/assets/images/retaillabs/2021/1/29/5e7d57f5-dcb7-4d0a-896b-89cc26d72d7b1611912342884-Flat-300-Off.jpg') center center no-repeat;
      background-size: cover;
    }
    .form-section {
      flex: 1;
      padding: 40px;
    }
    .form-section h2 {
      margin-bottom: 20px;
    }
    form {
      display: none;
      animation: fade 0.3s ease-in-out;
    }
    form.active {
      display: block;
    }
    .form-group {
      margin-bottom: 15px;
      flex: 1 1 calc(50% - 10px);
    }
    input, textarea {
      width: 100%;
      padding: 12px;
      font-size: 14px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      width: 100%;
      background: #333;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }
    .toggle {
      text-align: center;
      margin-top: 15px;
      color: #7380ec;
      cursor: pointer;
    }
    .terms {
      font-size: 12px;
      margin-top: 10px;
    }
    .terms a {
      color: #ff3366;
      text-decoration: none;
    }
    @keyframes fade {
      from { opacity: 0; transform: scale(0.98); }
      to { opacity: 1; transform: scale(1); }
    }
  </style>
</head>
<body>

<div class="container">
  <div class="promo"></div>
  <div class="form-section">
    <h2 id="formTitle">Login or Signup</h2>

    <!-- Login Form -->
    <form method="POST" id="loginForm" class="active">
      <div class="form-group">
        <input type="text" name="email_or_phone" placeholder="Email or Mobile Number" required>
      </div>
      <div class="form-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <div class="terms">
        <input type="checkbox" required> By continuing, you agree to our 
        <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a>
      </div>
      <div class="terms">
        <input type="checkbox" required>Re Member Me
      </div>
      <br>
      <button type="submit" name="login">CONTINUE</button>
      <div class="toggle" onclick="toggleForm()">Don't have an account? Register</div>
    </form>

    <!-- Register Form -->
    <form method="POST" enctype="multipart/form-data" id="registerForm">
      <div class="form-group">
        <input type="text" name="shop_name" placeholder="Shop Name" required>
      </div>
      <div class="form-group">
        <input type="text" name="vendor_name" placeholder="Vendor Name" required>
      </div>
      <div class="form-group" style="flex: 1 1 100%;">
        <input type="email" name="email" placeholder="Email Address" required>
      </div>
      <div class="form-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <div class="form-group">
        <input type="text" name="phone" placeholder="Phone Number" required>
      </div>
      <div class="form-group">
        <textarea name="address" placeholder="Shop Address" required></textarea>
      </div>
      <div class="form-group">
        <label>Upload NID Front:</label>
        <input type="file" name="nid_front" required>
      </div>
      <div class="form-group">
        <label>Upload NID Back:</label>
        <input type="file" name="nid_back" required>
      </div>
      <button type="submit" name="register">REGISTER</button>
      <div class="toggle" onclick="toggleForm()">Already have an account? Login</div>
    </form>
  </div>
</div>

<script>
  function toggleForm() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const formTitle = document.getElementById('formTitle');

    loginForm.classList.toggle('active');
    registerForm.classList.toggle('active');

    formTitle.innerText = loginForm.classList.contains('active') ? 'Login or Signup' : 'Create your Shop';
  }
</script>

</body>
</html>
