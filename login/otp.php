<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register | BellaBuy</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="overflow-x-hidden bg-gray-100">
  <div class="min-h-screen max-w-7xl mx-auto px-4 sm:py-10 py-20 flex flex-col items-center">
    <!-- Header Section -->
    <div class="text-center">
      <h1 class="text-4xl font-bold">You are Almost Done!</h1>
      <div class="mt-4 space-y-1">
        <p class="text-lg text-gray-600">An 4 digit code has been sent to your</p>
        <p class="text-lg text-gray-600 font-medium">E-mail: mypc***@gmail.com</p>
      </div>
    </div>

   
    <div class="mt-10 w-full max-w-xs">
      <h2 class="text-2xl font-semibold text-center">Enter OTP</h2>
      <div class="mt-4 flex justify-center space-x-4" id="otpContainer">
        <input type="text" class="w-16 h-16 border-2 border-gray-300 rounded-lg text-center text-2xl focus:border-red-500 transition-colors" maxlength="1" id="otp1" aria-label="OTP digit 1">
        <input type="text" class="w-16 h-16 border-2 border-gray-300 rounded-lg text-center text-2xl focus:border-red-500 transition-colors" maxlength="1" id="otp2" aria-label="OTP digit 2">
        <input type="text" class="w-16 h-16 border-2 border-gray-300 rounded-lg text-center text-2xl focus:border-red-500 transition-colors" maxlength="1" id="otp3" aria-label="OTP digit 3">
        <input type="text" class="w-16 h-16 border-2 border-gray-300 rounded-lg text-center text-2xl focus:border-red-500 transition-colors" maxlength="1" id="otp4" aria-label="OTP digit 4">
      </div>
    </div>

   
    <div class="mt-10 text-center">
      <h3 class="text-2xl font-semibold">04:49</h3>
      <img src="../img/otp.svg" alt="OTP Timer Icon" class="mt-4 mx-auto" />
      <p class="mt-6 text-lg text-gray-600">
        Didn’t receive the OTP? 
        <a href="#" class="text-red-500 font-medium hover:underline">Resend OTP</a>
      </p>
    </div>

   
    <div class="sm:mt-10 mt-15 w-full max-w-xs">
      <button class="w-full cursor-pointer flex justify-center items-center gap-2 px-6 py-3 bg-gray-800 text-white text-xl font-bold rounded-lg transition-colors" aria-label="Verify OTP">
        Verify
        <img src="../img/arrow.svg" alt="Arrow Icon" class="mt-1" />
      </button>
    </div>
  </div>

  <script>
    const inputs = document.querySelectorAll('#otpContainer input');

    inputs.forEach((input, index) => {
      input.addEventListener('focus', () => {
        inputs.forEach((inp) => inp.classList.remove('border-red-500'));
        input.classList.add('border-red-500');
      });

      input.addEventListener('input', (e) => {
        if (e.target.value.length === 1 && index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && index > 0) {
          inputs[index - 1].focus();
        }
      });
    });
  </script>
</body>
</html>