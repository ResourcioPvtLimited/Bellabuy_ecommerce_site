// Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/11.9.1/firebase-app.js";
import { getAuth,createUserWithEmailAndPassword, GoogleAuthProvider, FacebookAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/11.9.1/firebase-auth.js";

   const firebaseConfig = {
    apiKey: "AIzaSyAkNErW4m1pvEjbm5pxjYn3fyiWb1CdU8w",
    authDomain: "bellabuy-dev.firebaseapp.com",
    projectId: "bellabuy-dev",
    storageBucket: "bellabuy-dev.firebasestorage.app",
    messagingSenderId: "138623330709",
    appId: "1:138623330709:web:0d179d625c6f367fdbee9c"
  };


  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const auth = getAuth(app); // Create a single auth instance
  
  // Wait for DOMContentLoaded before accessing DOM elements
  window.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('register-form');
    if (form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const email = emailInput ? emailInput.value : '';
        const password = passwordInput ? passwordInput.value : '';
        createUserWithEmailAndPassword(auth, email, password)
          .then((userCredential) => {
            // Signed up 
            const user = userCredential.user;
            user.getIdToken().then((idToken) => {
              console.log('Firebase ID Token:', idToken);
              alert("User registered successfully!\nCheck console for Firebase ID Token.");
              window.location.href = "./index.html";
            });
          })
          .catch((error) => {
            const errorMessage = error.message;
            alert("Error: " + errorMessage);
          });
      });
    }
  
    // Google Sign-In
    const googleBtn = document.querySelector('.google');
    if (googleBtn) {
      googleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const provider = new GoogleAuthProvider();
        signInWithPopup(auth, provider)
          .then((result) => {
            const user = result.user;
            user.getIdToken().then((idToken) => {
              console.log('Google Firebase ID Token:', idToken);
              alert('Signed in with Google!\nCheck console for Firebase ID Token.');
              window.location.href = './otp.html';
            });
          })
          .catch((error) => {
            alert('Google Sign-In Error: ' + error.message);
          });
        });
    }

    // Facebook Sign-In
    const facebookBtn = document.querySelector('.facebook');
    if (facebookBtn) {
      facebookBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const provider = new FacebookAuthProvider();
        signInWithPopup(auth, provider)
          .then((result) => {
            const user = result.user;
            user.getIdToken().then((idToken) => {
              console.log('Facebook Firebase ID Token:', idToken);
              alert('Signed in with Facebook!\nCheck console for Firebase ID Token.');
              window.location.href = './index.html';
            });
          })
          .catch((error) => {
            alert('Facebook Sign-In Error: ' + error.message);
          });
      });
    }

    // Apple Sign-In (not natively supported by Firebase Web SDK, but you can use OAuth provider)
    // This is a placeholder for Apple sign-in. You need to set up Apple as a custom OAuth provider in Firebase console.
    const appleBtn = document.querySelector('.github'); // Your Apple button uses class 'github'
    if (appleBtn) {
      appleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        alert('Apple Sign-In is not natively supported in Firebase Web SDK. Please implement using custom OAuth provider.');
      });
    }

    // --- PHONE AUTHENTICATION ---
    // (Phone authentication code removed as requested)
  });
