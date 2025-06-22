function showSuccessModal(e) {
    e.preventDefault();
  
    // Get form values
    const firstName = document.getElementById("firstName").value.trim();
    const dob = document.getElementById("dob").value;
    const gender = document.getElementById("gender").value;
    const phone = document.getElementById("phone").value.trim();
  
    // Validation
    if (!firstName || !dob || gender === "Choose Your Gender" || !phone) {
      alert("Please fill all required fields.");
      return;
    }
  
    // Show modal if valid
    document.getElementById("successModal").style.display = "flex";
    document.getElementById("mainContent").classList.add("blurred");
  }
  
  function closeModal() {
    document.getElementById("successModal").style.display = "none";
    document.getElementById("mainContent").classList.remove("blurred");
  }
  