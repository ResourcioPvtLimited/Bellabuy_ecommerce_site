// ================== ELEMENT REFERENCES ==================
const body = document.body;
const themeToggler = document.getElementById('themeToggler');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const collapseBtn = document.getElementById('collapseBtn');
const mobileMenu = document.getElementById('mobileBottomMenu');
const menuSearch = document.getElementById('menuSearch');
const menuItems = document.querySelectorAll('.menu-item');
const noResults = document.querySelector('.no-results');
const mobileMenuItems = document.querySelectorAll('.mobile-menu-item');
const fullscreenIcon = document.getElementById('fullscreenIcon');
const fullscreenSpan = fullscreenIcon?.querySelector('span');

// ================== THEME TOGGLER ==================
themeToggler?.addEventListener('click', () => {
  body.classList.toggle('dark-theme-variables');
});

// ================== SIDEBAR TOGGLE (DESKTOP) ==================
collapseBtn?.addEventListener('click', () => {
  sidebar.classList.toggle('collapsed');
});

// ================== SIDEBAR TOGGLE (MOBILE) ==================
function toggleSidebarMobile() {
  sidebar.classList.toggle('mobile-show');
  sidebarOverlay.classList.toggle('active');
}

// ================== SIDEBAR OVERLAY CLICK (MOBILE CLOSE) ==================
sidebarOverlay?.addEventListener('click', () => {
  sidebar.classList.remove('mobile-show');
  sidebarOverlay.classList.remove('active');
});

// ================== MENU SEARCH FUNCTIONALITY ==================
menuSearch?.addEventListener('input', (e) => {
  const searchTerm = e.target.value.toLowerCase();
  let hasResults = false;

  menuItems.forEach(item => {
    const text = item.textContent.toLowerCase();
    if (text.includes(searchTerm)) {
      item.classList.remove('hidden');
      hasResults = true;
    } else {
      item.classList.add('hidden');
    }
  });

  if (noResults) {
    noResults.style.display = hasResults ? 'none' : 'block';
  }
});

// ================== MOBILE BOTTOM MENU FUNCTIONALITY ==================
function toggleMobileMenu() {
  mobileMenu.classList.toggle('expanded');

  // Disable scroll when menu is open
  if (mobileMenu.classList.contains('expanded')) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
}

// ================== MOBILE MENU ITEM CLICK ==================
mobileMenuItems.forEach(item => {
  item.addEventListener('click', function () {
    // Remove active from all
    mobileMenuItems.forEach(i => i.classList.remove('active'));

    // Add active to clicked item
    this.classList.add('active');

    // Log or navigate
    const target = this.getAttribute('data-target');
    console.log(`Navigating to: ${target}`);

    // Close menu
    mobileMenu.classList.remove('expanded');
    document.body.style.overflow = '';
  });
});

// ================== ACTIVE LINK HIGHLIGHTING ==================
function setActiveLinks() {
  const currentPage = window.location.pathname.split("/").pop();

  // Sidebar links
  document.querySelectorAll(".menu-item a").forEach(link => {
    if (link.getAttribute("href") === currentPage) {
      link.classList.add("active");
    }
  });

  // Mobile menu links
  document.querySelectorAll(".mobile-menu-item").forEach(link => {
    if (link.getAttribute("href") === currentPage) {
      link.classList.add("active");
    }
  });
}
document.addEventListener("DOMContentLoaded", setActiveLinks);

// ================== FULLSCREEN TOGGLE ==================
fullscreenIcon?.addEventListener('click', () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen().then(() => {
      if (fullscreenSpan) fullscreenSpan.textContent = 'fullscreen_exit';
    }).catch(err => {
      console.error(`Error attempting to enable full-screen mode: ${err.message}`);
    });
  } else {
    document.exitFullscreen().then(() => {
      if (fullscreenSpan) fullscreenSpan.textContent = 'fullscreen';
    });
  }
});
