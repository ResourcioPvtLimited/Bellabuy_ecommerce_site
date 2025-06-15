<style>
/* Navigation Tabs */
.nav-tabs {
  display: flex;
  overflow-x: auto;
  white-space: nowrap;
  padding-bottom: 5px;
  margin-bottom: 15px;
  -webkit-overflow-scrolling: touch;
  box-shadow: 0 2px 5px rgba(36, 35, 36, 0.143);
  padding: 0px;
  margin: 10px 10px;
  border-radius: 8px;
  scrollbar-width: none;
}

.nav-tabs::-webkit-scrollbar {
  display: none;
}

.nav-tab {
  padding: 8px 12px;
  background-color: #f5f5f5; 
  font-size: 14px;
  flex: 0 0 auto;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.nav-tab.active {
  background-color: #2a94fe;
  color: white;
  transform: scale(1.05);
}

/* Combined Category and Brand Box */
.category-brand-box {
  background-color: #13182e;
  color: white;
  padding: 15px;
  margin-bottom: 15px;
  position: sticky;
  top: 0;
  z-index: 90;
  transition: all 0.5s ease, background-color 0.5s ease;
  will-change: transform, opacity;
}

.category-brand-box.hide {
  transform: translateY(-100%);
  opacity: 0;
  pointer-events: none;
  background-color: #000000;
}

/* Category Section */
.category-slider {
  display: flex;
  overflow-x: auto;
  padding: 10px 0;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  scroll-behavior: smooth;
}

.category-slider::-webkit-scrollbar {
  display: none;
}

.category-item {
  flex: 0 0 auto;
  width: 60px;
  text-align: center;
  margin-right: 15px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.category-item img {
  width: 50px;
  height: 50px;
  object-fit: contain;
  margin-bottom: 5px;
  border-radius: 50%;
  background-color: #f5f5f7;
  padding: 5px;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.category-item p {
  font-size: 12px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: white;
  transition: all 0.3s ease;
}

.category-item.active img {
  background-color: #ff3f6c;
  transform: scale(1.1);
  box-shadow: 0 2px 8px rgba(255, 63, 108, 0.4);
}

.category-item.active p {
  color: #ffffff;
}

/* Brand Section */
.brand-section {
  margin-top: 15px;
}

.suggested-brand {
  margin: 0 auto;
  text-align: center;
  font-size: 15px;
  font-weight: 600;
  background-color: white;
  width: 50%;
  justify-content: center;
  padding: 5px 10px;
  border-radius: 10px;
  color: #000000;
  margin-bottom: 10px;
  animation: fadeIn 0.6s ease-out;
}

.brand-slider {
  display: flex;
  position: relative;
  height: 40px;
  align-items: center;
  display: flex;
  overflow-x: auto;
  overflow-y: hidden;
  padding: 10px 0;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  scroll-behavior: smooth;
}

.brand-slide {
  flex: 0 0 auto;
  width: 60px;
  height: 60px;
  margin-right: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: slide 2s linear infinite;
  will-change: transform;
  cursor: pointer;
  transition: all .1s ease;
}

.brand-slide.active {
  transform: scale(1.1);
}

.brand-slide img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  filter: brightness(0) invert(1);
  transition: transform 0.3s ease;
}

.brand-slide.active img {
  filter: brightness(0) invert(0.7) sepia(1) saturate(5) hue-rotate(330deg);
}

/* Sort Options */
.sort-options {
  display: none;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 15px;
  padding: 15px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  animation: slideUp 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.sort-options.show {
  display: flex;
}

.sort-option {
  padding: 8px 15px;
  background-color: #f5f5f5;
  border-radius: 20px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.sort-option:hover {
  transform: translateY(-2px);
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.sort-option.active {
  background-color: #ff3f6c;
  color: white;
  box-shadow: 0 2px 8px rgba(255, 63, 108, 0.3);
}

/* Filter Button */
.filter-button {
  position: fixed;
  bottom: 80px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #ff3f6c;
  color: white;
  padding: 12px 25px;
  border-radius: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 10px rgba(255, 63, 108, 0.3);
  cursor: pointer;
  z-index: 80;
  transition: all 0.3s ease;
}

.filter-button:hover {
  transform: translateX(-50%) scale(1.05);
  box-shadow: 0 6px 15px rgba(255, 63, 108, 0.4);
}

.filter-button i {
  margin-right: 8px;
}

/* Filter Modal */
.filter-modal {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 80%;
  background-color: white;
  border-radius: 20px 20px 0 0;
  box-shadow: 0 -5px 20px rgba(0,0,0,0.2);
  z-index: 1000;
  transform: translateY(100%);
  transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  display: flex;
  flex-direction: column;
}

.filter-modal.show {
  transform: translateY(0);
}

.filter-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border-bottom: 1px solid #eee;
}

.filter-modal-header .clear-all {
  position: absolute;
  right: 50px;
  color: #ff3f6c;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-modal-header .clear-all:hover {
  color: #e0355f;
  transform: translateY(-2px);
}

.filter-modal-title {
  font-weight: bold;
  font-size: 18px;
}

.filter-modal-close {
  font-size: 20px;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.filter-modal-close:hover {
  transform: rotate(90deg);
}

.filter-modal-content {
  flex: 1;
  overflow-y: auto;
  padding: 15px;
}

.filter-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
  z-index: 999;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

.filter-overlay.show {
  opacity: 1;
  visibility: visible;
}

/* Applied Filters */
.applied-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 15px;
  padding: 0 15px;
}

.applied-filter {
  background: #f5f5f5;
  padding: 5px 10px;
  border-radius: 15px;
  font-size: 12px;
  display: flex;
  align-items: center;
  animation: fadeIn 0.3s ease;
}

.remove-filter {
  margin-left: 5px;
  cursor: pointer;
  color: #999;
  transition: color 0.3s ease;
}

.remove-filter:hover {
  color: #ff3f6c;
}

/* Filter Sections */
.filter-section {
  margin-bottom: 20px;
  border-bottom: 1px solid #eee;
  padding-bottom: 15px;
}

.filter-section:last-child {
  border-bottom: none;
}

.filter-section-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
  cursor: pointer;
}

.filter-section-name {
  font-weight: bold;
  font-size: 16px;
}

.filter-section-arrow {
  transition: transform 0.3s ease;
}

.filter-section.open .filter-section-arrow {
  transform: rotate(180deg);
}

.filter-options {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
}

.filter-section.open .filter-options {
  max-height: 500px;
}

.filter-option {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
  padding: 5px 0;
}

.filter-option input {
  margin-right: 8px;
  accent-color: #ff3f6c;
  cursor: pointer;
}

.filter-option label {
  font-size: 14px;
  cursor: pointer;
  transition: color 0.3s ease;
}

.filter-option:hover label {
  color: #ff3f6c;
}

/* Price Range Slider */
.price-range {
  padding: 10px 0;
}

.price-input {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.price-input input {
  width: 45%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  outline: none;
}

.slider {
  height: 5px;
  background: #ddd;
  border-radius: 5px;
  position: relative;
  margin: 20px 0;
}

.slider .progress {
  height: 5px;
  background: #ff3f6c;
  border-radius: 5px;
  position: absolute;
  left: 25%;
  right: 25%;
}

.range-input {
  position: relative;
}

.range-input input {
  position: absolute;
  top: -5px;
  height: 5px;
  width: 100%;
  background: none;
  pointer-events: none;
  -webkit-appearance: none;
}

input[type="range"]::-webkit-slider-thumb {
  height: 15px;
  width: 15px;
  background: #ff3f6c;
  border-radius: 50%;
  pointer-events: auto;
  -webkit-appearance: none;
  cursor: pointer;
}

/* Ripple Effect */
.ripple {
  position: absolute;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.7);
  transform: scale(0);
  animation: ripple 0.6s linear;
  pointer-events: none;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { 
    opacity: 0;
    transform: translateY(20px);
  }
  to { 
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

@keyframes ripple {
  0% {
    transform: scale(0);
    opacity: 1;
  }
  100% {
    transform: scale(4);
    opacity: 0;
  }
}
</style>

<div class="nav-tabs">
    <div class="nav-tab active">This Week</div>
    <div class="nav-tab">Last Week</div>
    <div class="nav-tab">This Month</div>
    <div class="nav-tab">Top Rated</div>
    <div class="nav-tab">Best Offer</div>
</div>

<div class="category-brand-box" id="categoryBrandBox">
    <div class="category-section">
        <div class="category-slider">
            <div class="category-item active">
                <img src="https://img.icons8.com/color/48/t-shirt.png" alt="Shirts">
                <p>Shirts</p>
            </div>
            <div class="category-item">
               <img src="https://img.icons8.com/color/48/jeans.png" alt="Jeans">
               <p>Jeans</p>
            </div>
            <!-- More category items -->
        </div>
    </div>
    
    <div class="brand-section">
        <div class="suggested-brand">Suggested Brand</div>
        <div class="brand-slider">
            <div class="brand-slide">
                <img src="prod/shop.png" alt="XDEN">
            </div>
            <!-- More brand slides -->
        </div>
    </div>
</div>

<div class="sort-options" id="sortOptions">
    <div class="sort-option">Under 1000 BDT</div>
    <div class="sort-option">Under 2000 BDT</div>
    <div class="sort-option">Latest</div>
    <div class="sort-option">Popularity</div>
    <div class="sort-option">Discount</div>
</div>

<!-- Applied Filters -->
<div class="applied-filters" id="appliedFilters">
    <div class="applied-filter">
        T-Shirts <span class="remove-filter">×</span>
    </div>
    <div class="applied-filter">
        This Week <span class="remove-filter">×</span>
    </div>
</div>

<!-- Filter Button -->
<div class="filter-button" id="filterButton">
    <i class="fas fa-filter"></i>
    <span>FILTER</span>
</div>

<!-- Filter Modal -->
<div class="filter-overlay" id="filterOverlay"></div>
<div class="filter-modal" id="filterModal">
    <div class="filter-modal-header">
        <div class="filter-modal-title">FILTERS</div>
        <div class="clear-all" id="clearAllButton">Clear All</div>
        <div class="filter-modal-close" id="filterClose">
            <i class="fas fa-times"></i>
        </div>
    </div>

    <div class="filter-modal-content">
        <!-- Filter sections would go here -->
    </div>
</div>

<script>
// Filter Modal Functionality
const filterButton = document.getElementById('filterButton');
const filterModal = document.getElementById('filterModal');
const filterOverlay = document.getElementById('filterOverlay');
const filterClose = document.getElementById('filterClose');

function openFilterModal() {
    filterModal.classList.add('show');
    filterOverlay.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeFilterModal() {
    filterModal.classList.remove('show');
    filterOverlay.classList.remove('show');
    document.body.style.overflow = 'auto';
}

filterButton.addEventListener('click', openFilterModal);
filterClose.addEventListener('click', closeFilterModal);
filterOverlay.addEventListener('click', closeFilterModal);

// Scroll behavior for category-brand-box
const categoryBrandBox = document.getElementById('categoryBrandBox');
let lastScrollPosition = 0;

window.addEventListener('scroll', () => {
    const currentScrollPosition = window.pageYOffset;
    
    if (currentScrollPosition > lastScrollPosition && currentScrollPosition > 100) {
        // Scrolling down
        categoryBrandBox.classList.add('hide');
    } else {
        // Scrolling up
        categoryBrandBox.classList.remove('hide');
    }
    
    lastScrollPosition = currentScrollPosition;
});

// Navigation Tabs Filter
const navTabs = document.querySelectorAll('.nav-tab');
const appliedFiltersContainer = document.getElementById('appliedFilters');

navTabs.forEach(tab => {
    tab.addEventListener('click', () => {
        // Remove active class from all tabs
        navTabs.forEach(t => t.classList.remove('active'));
        
        // Add active class to clicked tab
        tab.classList.add('active');
        
        // Add ripple effect
        const ripple = document.createElement('span');
        ripple.classList.add('ripple');
        tab.appendChild(ripple);
        setTimeout(() => {
            ripple.remove();
        }, 600);
        
        // Update applied filters
        updateAppliedFilter('Time Period', tab.textContent);
    });
});

// Category Items Filter
const categoryItems = document.querySelectorAll('.category-item');

categoryItems.forEach(item => {
    item.addEventListener('click', () => {
        // Remove active class from all categories
        categoryItems.forEach(i => i.classList.remove('active'));
        
        // Add active class to clicked category
        item.classList.add('active');
        
        // Add bounce animation to the icon
        const img = item.querySelector('img');
        img.style.transform = 'scale(1.3)';
        setTimeout(() => {
            img.style.transform = 'scale(1.1)';
        }, 300);
        
        // Update applied filters
        updateAppliedFilter('Category', item.querySelector('p').textContent);
    });
});

// Brand Slides Filter
const brandSlides = document.querySelectorAll('.brand-slide');

brandSlides.forEach(slide => {
    slide.addEventListener('click', () => {
        // Toggle active class
        slide.classList.toggle('active');
        
        // Update applied filters
        const brandName = slide.querySelector('img').alt;
        if (slide.classList.contains('active')) {
            updateAppliedFilter('Brand', brandName);
        } else {
            removeAppliedFilter('Brand', brandName);
        }
    });
});

// Price Range Slider
const rangeInput = document.querySelectorAll(".range-input input");
const priceInput = document.querySelectorAll(".price-input input");
const progress = document.querySelector(".slider .progress");

let priceGap = 1000;

priceInput.forEach(input => {
    input.addEventListener("input", e => {
        let minVal = parseInt(priceInput[0].value);
        let maxVal = parseInt(priceInput[1].value);
        
        if((maxVal - minVal) >= priceGap && maxVal <= 10000) {
            if(e.target.className === "price-min") {
                rangeInput[0].value = minVal;
                progress.style.left = (minVal / rangeInput[0].max) * 100 + "%";
            } else {
                rangeInput[1].value = maxVal;
                progress.style.right = 100 - (maxVal / rangeInput[1].max) * 100 + "%";
            }
        }
    });
});

rangeInput.forEach(input => {
    input.addEventListener("input", e => {
        let minVal = parseInt(rangeInput[0].value);
        let maxVal = parseInt(rangeInput[1].value);
        
        if((maxVal - minVal) < priceGap) {
            if(e.target.className === "range-min") {
                rangeInput[0].value = maxVal - priceGap;
            } else {
                rangeInput[1].value = minVal + priceGap;
            }
        } else {
            priceInput[0].value = minVal;
            priceInput[1].value = maxVal;
            progress.style.left = (minVal / rangeInput[0].max) * 100 + "%";
            progress.style.right = 100 - (maxVal / rangeInput[1].max) * 100 + "%";
            
            // Update applied filters for price range
            updateAppliedFilter('Price', `BDT ${minVal}-${maxVal}`);
        }
    });
});

// Filter Sections Toggle
const filterSections = document.querySelectorAll('.filter-section');

filterSections.forEach(section => {
    const title = section.querySelector('.filter-section-title');
    title.addEventListener('click', () => {
        section.classList.toggle('open');
    });
});

// Filter Checkboxes
document.querySelectorAll('.filter-option input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', (e) => {
        const filterName = e.target.nextElementSibling.textContent;
        const filterType = e.target.closest('.filter-section').querySelector('.filter-section-name').textContent;
        
        if(e.target.checked) {
            updateAppliedFilter(filterType, filterName);
        } else {
            removeAppliedFilter(filterType, filterName);
        }
    });
});

// Remove Applied Filters
document.addEventListener('click', (e) => {
    if(e.target.classList.contains('remove-filter')) {
        const filterText = e.target.parentElement.textContent.trim().replace('×', '').trim();
        e.target.parentElement.remove();
        
        // Also uncheck corresponding checkbox if exists
        const checkboxes = document.querySelectorAll('.filter-option input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            if(checkbox.nextElementSibling.textContent === filterText) {
                checkbox.checked = false;
            }
        });
        
        // Also remove active class from nav tabs or categories
        navTabs.forEach(tab => {
            if(tab.textContent === filterText) {
                tab.classList.remove('active');
            }
        });
        
        categoryItems.forEach(item => {
            if(item.querySelector('p').textContent === filterText) {
                item.classList.remove('active');
            }
        });
        
        // Also remove active class from brand slides
        brandSlides.forEach(slide => {
            const brandName = slide.querySelector('img').alt;
            if(brandName === filterText) {
                slide.classList.remove('active');
            }
        });
    }
});

// Infinite brand slider
const brandSlider = document.querySelector('.brand-slider');
const brandSlideItems = document.querySelectorAll('.brand-slide');

// Clone slides for infinite loop
brandSlideItems.forEach(slide => {
    const clone = slide.cloneNode(true);
    brandSlider.appendChild(clone);
});

// Pause animation on hover
brandSlider.addEventListener('mouseenter', () => {
    brandSlider.style.animationPlayState = 'paused';
});

brandSlider.addEventListener('mouseleave', () => {
    brandSlider.style.animationPlayState = 'running';
});

// Clear all filters
const clearAllButton = document.getElementById('clearAllButton');

clearAllButton.addEventListener('click', () => {
    // 1. Clear all checkboxes in modal
    document.querySelectorAll('.filter-option input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // 2. Reset price range in modal
    const priceInputs = document.querySelectorAll(".price-input input");
    const rangeInputs = document.querySelectorAll(".range-input input");
    priceInputs[0].value = 500;
    priceInputs[1].value = 1000;
    rangeInputs[0].value = 500;
    rangeInputs[1].value = 1000;
    document.querySelector(".slider .progress").style.left = "25%";
    document.querySelector(".slider .progress").style.right = "25%";
    
    // 3. Clear applied filters from main view
    document.querySelectorAll('.applied-filter').forEach(filter => {
        filter.remove();
    });
    
    // 4. Reset active states
    document.querySelectorAll('.nav-tab.active, .category-item.active, .brand-slide.active').forEach(item => {
        item.classList.remove('active');
    });
    
    // 5. Set default active states (optional)
    document.querySelector('.nav-tab').classList.add('active');
    document.querySelector('.category-item').classList.add('active');
    
    // Visual feedback
    clearAllButton.textContent = 'Cleared!';
    clearAllButton.style.color = '#4CAF50';
    
    setTimeout(() => {
        clearAllButton.textContent = 'Clear All';
        clearAllButton.style.color = '#ff3f6c';
    }, 1000);
});

// Helper function to update applied filters
function updateAppliedFilter(type, value) {
    // First check if this filter type already exists
    const existingFilters = document.querySelectorAll('.applied-filter');
    let filterExists = false;
    
    existingFilters.forEach(filter => {
        if(filter.textContent.includes(value)) {
            filterExists = true;
        }
    });
    
    if(!filterExists) {
        const newFilter = document.createElement('div');
        newFilter.className = 'applied-filter';
        newFilter.innerHTML = `
            ${value} <span class="remove-filter">×</span>
        `;
        
        appliedFiltersContainer.appendChild(newFilter);
    }
}

// Helper function to remove applied filters
function removeAppliedFilter(type, value) {
    const filters = document.querySelectorAll('.applied-filter');
    filters.forEach(filter => {
        if(filter.textContent.includes(value)) {
            filter.remove();
        }
    });
}
</script>