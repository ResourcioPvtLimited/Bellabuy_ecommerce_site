<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $identifier = trim($_POST['identifier']);
  $password = $_POST['password'];

  $conn = new mysqli("localhost", "root", "", "bellabuy");//change with the config file 

  // Determine if input is email or phone
  $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

  $stmt = $conn->prepare("SELECT id, name, password FROM cust WHERE $field = ?");
  $stmt->bind_param("s", $identifier);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $name, $hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
      $_SESSION['user_id'] = $id;
      $_SESSION['user_name'] = $name;
      header("Location: ../index.php");
      exit;
    } else {
      $error = "Incorrect password!";
    }
  } else {
    $error = "User not found!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | BellaBuy</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: url('prod/loginbac.png') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .login-container {
      background: #fff;
      padding: 25px 20px;
      border-radius: 15px;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 20px 30px rgba(0, 0, 0, 0.15);
      text-align: center;
    }

    .login-container img {
      height: 60px;
      margin-bottom: 10px;
    }

    h2 {
      color: #222;
      margin-bottom: 20px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1.5px solid #ccc;
      border-radius: 10px;
      font-size: 15px;
      outline: none;
      transition: border-color 0.3s;
    }

    input:focus {
      border-color: #e44a2d;
    }

    button {
      background: #e44a2d;
      color: #fff;
      border: none;
      width: 100%;
      padding: 12px;
      font-size: 16px;
      border-radius: 25px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #d43b22;
    }

    .error {
      background: #ffe5e5;
      color: #d60000;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 25px 15px;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <img src="prod/logo.png" alt="BellaBuy Logo" />
    <h2>Login to BellaBuy</h2>

    <?php if (!empty($error)): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <input type="text" name="identifier" placeholder="Email or Phone" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
  </div>

</body>
</html>
