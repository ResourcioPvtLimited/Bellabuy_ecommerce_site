const form = document.getElementById("locationForm");
const modal = document.getElementById("successModal");
const mainContent = document.querySelector(".form-container");

form.addEventListener("submit", function (e) {
  e.preventDefault();

  // Collect all required inputs
  const country = document.getElementById("country").value.trim();
  const district = document.getElementById("district").value.trim();
  const town = document.getElementById("town").value.trim();
  const postal = document.getElementById("postal").value.trim();
  const address = document.getElementById("address").value.trim();
  const phone = document.getElementById("phone").value.trim();
  
  // Validate: Email is optional, others are required
  if (!country || !district || !town || !postal || !address || !phone) {
    alert("Please fill in all required fields before saving.");
    return;
  }

  // Show modal if all required fields are filled
  modal.style.display = "flex";
  mainContent.classList.add("blurred");
});

function closeModal() {
  modal.style.display = "none";
  mainContent.classList.remove("blurred");
}
