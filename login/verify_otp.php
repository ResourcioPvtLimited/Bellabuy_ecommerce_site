<?php
session_start();
$verified = false;
$expired = false;
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($submitted && isset($_POST['otp'])) {
  $enteredOtp = $_POST['otp'];
  $sessionOtp = $_SESSION['otp'] ?? null;
  $otpTimestamp = $_SESSION['otp_time'] ?? 0;

  if (time() - $otpTimestamp > 180) {
    $expired = true;
  } elseif ($enteredOtp == $sessionOtp) {
    $conn = new mysqli("localhost", "alokito2_sadi", "sadi9507@#", "alokito2_ecom");
    $name = $_SESSION['name'];
    $email = $_SESSION['email'] ?? null;
    $phone = $_SESSION['phone'] ?? null;
    $password = $_SESSION['password'];

    $stmt = $conn->prepare("INSERT INTO cust (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $password);

    if ($stmt->execute()) {
      unset($_SESSION['otp'], $_SESSION['otp_time'], $_SESSION['name'], $_SESSION['email'], $_SESSION['phone'], $_SESSION['password'], $_SESSION['register_mode']);
      header("Location: ../index.php");
      exit;
    } else {
      $error = "Database Error: " . $stmt->error;
    }
  } else {
    $error = "Invalid OTP. Please try again.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Verify OTP</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: url('prod/loginbac.png') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }

    .otp-container {
      background: #fff;
      padding: 25px 20px;
      border-radius: 15px;
      width: 100%;
      max-width: 400px;
      text-align: center;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .logo img {
      height: 50px;
      margin-bottom: 10px;
    }

    h2 {
      margin: 10px 0;
      color: #222;
      font-size: 22px;
    }

    .otp-box {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      margin: 30px 0;
    }

    .otp-input {
      width: 48px;
      height: 56px;
      font-size: 22px;
      text-align: center;
      border: 2px solid #ddd;
      border-radius: 12px;
      transition: 0.3s;
    }

    .otp-input:focus {
      border-color: #e44a2d;
      outline: none;
    }

    .btn {
      padding: 12px;
      background: #e44a2d;
      border: none;
      color: white;
      font-size: 16px;
      border-radius: 25px;
      cursor: pointer;
      width: 100%;
    }

    .msg-success {
      background: #e6ffed;
      color: #1a7f37;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    .msg-error {
      background: #ffe5e5;
      color: #d60000;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    .timer {
      font-size: 13px;
      margin-top: 10px;
      color: #555;
    }
  </style>
</head>
<body>

<div class="otp-container">
  <div class="logo"><img src="prod/logo.png" alt="Logo" /></div>

  <?php if ($verified): ?>
    <div class="msg-success">✅ OTP verified! Redirecting to home...</div>
    <script>
      setTimeout(() => window.location.href = "../index.php", 1500);
    </script>
  <?php elseif ($expired): ?>
    <div class="msg-error">⏰ OTP expired. Please re-register.</div>
  <?php else: ?>
    <h2>Enter the 6-digit OTP</h2>
    <form method="POST" onsubmit="return setOTPValue();">
      <div class="otp-box" id="otp-box">
        <input class="otp-input" type="text" maxlength="1" />
        <input class="otp-input" type="text" maxlength="1" />
        <input class="otp-input" type="text" maxlength="1" />
        <input class="otp-input" type="text" maxlength="1" />
        <input class="otp-input" type="text" maxlength="1" />
        <input class="otp-input" type="text" maxlength="1" />
      </div>
      <input type="hidden" name="otp" id="otp_hidden" />
      <button type="submit" class="btn">Verify</button>
      <div class="timer" id="timer">OTP valid for: 03:00</div>
    </form>
    <?php if (!empty($error)): ?>
      <div class="msg-error"><?= $error ?></div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<script>
const boxes = document.querySelectorAll(".otp-input");
const hiddenInput = document.getElementById("otp_hidden");

boxes.forEach((box, i) => {
  box.addEventListener("input", () => {
    if (box.value && i < boxes.length - 1) boxes[i + 1].focus();
  });

  box.addEventListener("keydown", e => {
    if (e.key === "Backspace" && !box.value && i > 0) {
      boxes[i - 1].focus();
    }
  });

  box.addEventListener("paste", e => {
    const paste = e.clipboardData.getData("text").trim();
    if (/^\d{6}$/.test(paste)) {
      paste.split('').forEach((digit, idx) => {
        if (boxes[idx]) boxes[idx].value = digit;
      });
    }
    e.preventDefault();
  });
});

function setOTPValue() {
  const otp = Array.from(boxes).map(b => b.value).join('');
  hiddenInput.value = otp;
  return otp.length === 6;
}

// Timer logic
let seconds = 180;
const timerBox = document.getElementById("timer");
const interval = setInterval(() => {
  const m = String(Math.floor(seconds / 60)).padStart(2, '0');
  const s = String(seconds % 60).padStart(2, '0');
  timerBox.textContent = `OTP valid for: ${m}:${s}`;
  seconds--;
  if (seconds < 0) {
    clearInterval(interval);
    timerBox.textContent = "⛔ OTP expired";
  }
}, 1000);
</script>

</body>
</html>
