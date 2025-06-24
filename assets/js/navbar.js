// assets/js/navbar.js
function setupNavbarDropdown() {
  const toggleBtn = document.getElementById('profileDropdownToggle');
  const dropdown = document.getElementById('profileDropdown');
  if (!toggleBtn || !dropdown) return;

  toggleBtn.onclick = function(e) {
    e.stopPropagation();
    dropdown.classList.toggle('show');
  };

  document.addEventListener('click', function(e) {
    if (!toggleBtn.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.classList.remove('show');
    }
  });

  dropdown.onclick = function(e) {
    e.stopPropagation();
  };

  // Hamburger menu
  const hamburger = document.getElementById('hamburger');
  const navLinks = document.getElementById('navLinks');
  if (hamburger && navLinks) {
    hamburger.onclick = () => {
      navLinks.classList.toggle('active');
    };
  }
}