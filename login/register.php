<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register | BellaBuy</title>
  <style>
    :root {
      --primary: #e44a2d;
      --bg: #fff9f9;
      --light: #fff;
      --gray: #aaa;
      --dark: #1a1a1a;
    }

    body {
      background: url('prod/loginbac.png') no-repeat center center fixed;
      background-size: cover;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .container {
      background: var(--light);
      padding: 30px 20px;
      border-radius: 20px;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
      animation: fadeIn 0.4s ease;
      position: relative;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .logo {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }

    .logo img {
      height: 60px;
    }

    .tabs {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }

    .tabs div {
      margin: 0 15px;
      font-weight: bold;
      cursor: pointer;
      position: relative;
      color: var(--gray);
      transition: 0.3s;
    }

    .tabs div.active {
      color: var(--primary);
    }

    .tabs div.active::after {
      content: "";
      width: 100%;
      height: 2px;
      background: var(--primary);
      position: absolute;
      bottom: -5px;
      left: 0;
    }

    form {
      display: none;
      flex-direction: column;
      gap: 12px;
    }

    form.active {
      display: flex;
    }

    input {
      padding: 12px;
      font-size: 16px;
      border-radius: 10px;
      border: 1.5px solid #ccc;
      outline: none;
      transition: border 0.3s;
    }

    input:focus {
      border-color: var(--primary);
    }

    .error {
      background: #ffe5e5;
      color: #d60000;
      font-size: 13px;
      padding: 5px 10px;
      border-radius: 5px;
      display: none;
    }

    button {
      background: var(--primary);
      border: none;
      padding: 12px;
      color: white;
      border-radius: 25px;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    .timer {
      font-size: 13px;
      text-align: center;
      color: var(--dark);
      margin-top: 5px;
    }

    @media (max-width: 480px) {
      .container { padding: 25px 15px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">
      <img src="prod/logo.png" alt="BellaBuy" />
    </div>

    <div class="tabs">
      <div class="tab active" onclick="switchTab('phone')">By phone</div>
      <div class="tab" onclick="switchTab('email')">By email</div>
    </div>

    <form id="phoneForm" class="active" onsubmit="return sendOtp(event, 'phone')">
      <input type="text" name="name" placeholder="Name" required />
      <input type="text" name="phone" placeholder="Phone Number" maxlength="11" required onblur="checkExist('phone', this.value)" />
      <div class="error" id="phoneError">Phone already registered!</div>
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Register</button>
      <div class="timer" id="timerPhone"></div>
    </form>

    <form id="emailForm" onsubmit="return sendOtp(event, 'email')">
      <input type="text" name="name" placeholder="Name" required />
      <input type="email" name="email" placeholder="Email Address" required onblur="checkExist('email', this.value)" />
      <div class="error" id="emailError">Email already registered!</div>
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Register</button>
      <div class="timer" id="timerEmail"></div>
    </form>
  </div>

  <script>
    const tabs = document.querySelectorAll(".tab");
    const forms = document.querySelectorAll("form");

    function switchTab(type) {
      tabs.forEach(t => t.classList.remove("active"));
      forms.forEach(f => f.classList.remove("active"));
      document.querySelector(`#${type}Form`).classList.add("active");
      tabs[type === 'phone' ? 0 : 1].classList.add("active");
    }

    function checkExist(type, value) {
      const errorBox = document.getElementById(type + "Error");
      fetch('check_exist.php?type=' + type + '&value=' + value)
        .then(res => res.text())
        .then(data => {
          if (data === "exists") {
            errorBox.style.display = 'block';
          } else {
            errorBox.style.display = 'none';
          }
        });
    }

    function sendOtp(event, type) {
      event.preventDefault();
      const form = event.target;
      const formData = new FormData(form);
      const errorBox = document.getElementById(type + "Error");
      const timerBox = document.getElementById("timer" + (type === "phone" ? "Phone" : "Email"));
      if (errorBox.style.display === 'block') return false;
      const endpoint = type === 'phone' ? 'register_phone.php' : 'register_email.php';
      fetch(endpoint, { method: 'POST', body: formData })
        .then(res => res.text())
        .then(data => {
          if (data.includes("sent")) {
            startOtpTimer(timerBox);
            window.location.href = "verify_otp.php";
          } else {
            alert("Error sending OTP!");
          }
        });
      return false;
    }

    function startOtpTimer(box) {
      let seconds = 180;
      const interval = setInterval(() => {
        const m = String(Math.floor(seconds / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        box.textContent = `OTP valid for: ${m}:${s}`;
        seconds--;
        if (seconds < 0) {
          clearInterval(interval);
          box.textContent = "OTP expired. Please resend.";
        }
      }, 1000);
    }
  </script>
</body>
</html>
