document.addEventListener('DOMContentLoaded', function() {
    // Sample product data for demonstration - this would come from an API in a real app
    const products = [
        // Generate 20 products for robust demonstration
        ...Array(20).fill().map((_, index) => ({
            id: index + 1,
            name: `Women's Fashion Product ${index + 1}`,
            currentPrice: Math.floor(Math.random() * 50) + 50, // Random price between 50-100
            originalPrice: Math.floor(Math.random() * 50) + 100, // Random price between 100-150
            discount: `${Math.floor(Math.random() * 40) + 10}% OFF`, // Random discount between 10-50%
            image: index === 0 || index % 4 === 0 ? 
                    "../assets/images/female_model.jpg" : 
                    "../assets/images/Mask Group.png", // Use female model image every 4th product
            retailerLogo: "../assets/images/Apex.png",
            timeFrame: ["today", "thisWeek", "lastWeek", "thisMonth", "topRated"][Math.floor(Math.random() * 5)] // Random timeframe
        }))
    ];

    // DOM elements
    const productGrid = document.getElementById('productGrid');
    const tabs = document.querySelectorAll('.tab');
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const filterButton = document.getElementById('filterButton');
    
    // Get filter modal elements
    const modal = document.getElementById('filterModal');
    const openFilterBtn = document.getElementById('filterButton');
    const closeFilterBtn = document.getElementById('closeFilterBtn');
    const minSlider = document.getElementById('min-price');
    const maxSlider = document.getElementById('max-price');
    const sliderTrack = document.querySelector('.slider-track');
    const priceDisplay = document.querySelector('.price-display');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const applyFiltersBtn = document.getElementById('applyFilters');
    
    // Current active filter
    let currentFilter = 'today';
    let searchQuery = '';
    
    // Initialize the page
    renderProducts();
    
    // Setup event listeners
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            currentFilter = tab.getAttribute('data-tab');
            renderProducts();
        });
    });
    
    searchInput.addEventListener('input', function() {
        searchQuery = this.value.toLowerCase().trim();
        renderProducts();
    });
    
    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        searchQuery = '';
        renderProducts();
    });
    
    openFilterBtn.addEventListener('click', function(e) {
        e.preventDefault();
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    });
    
    closeFilterBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    });
    
    // Price range slider functionality
    function updateSlider() {
        if (!minSlider || !maxSlider || !sliderTrack) return;
        
        const min = parseInt(minSlider.value);
        const max = parseInt(maxSlider.value);
        
        // Make sure min doesn't exceed max
        if (min > max) {
            minSlider.value = max;
            return updateSlider();
        }
        
        // Update the colored track
        const minPercent = ((min - minSlider.min) / (minSlider.max - minSlider.min)) * 100;
        const maxPercent = ((max - minSlider.min) / (maxSlider.max - minSlider.min)) * 100;
        
        sliderTrack.style.left = minPercent + '%';
        sliderTrack.style.width = (maxPercent - minPercent) + '%';
        
        // Update price display
        priceDisplay.innerHTML = `
            <span>৳${min}</span>
            <span>—</span>
            <span>৳${max}</span>
        `;
    }
    
    // Initialize sliders
    if (minSlider && maxSlider) {
        minSlider.addEventListener('input', updateSlider);
        maxSlider.addEventListener('input', updateSlider);
        updateSlider(); // Initial update
    }
    
    // Setup filter interactions
    setupFilterInteractions();
    
    function setupFilterInteractions() {
        // Category selection
        const categoryCircles = document.querySelectorAll('.category-circle');
        categoryCircles.forEach(circle => {
            circle.addEventListener('click', function() {
                this.classList.toggle('selected');
                // Update the check icon visibility
                const checkIcon = this.querySelector('.check-icon');
                if (checkIcon) {
                    if (this.classList.contains('selected')) {
                        checkIcon.style.display = 'flex';
                    } else {
                        checkIcon.style.display = 'none';
                    }
                }
            });
            
            // Initialize check icon display
            const checkIcon = circle.querySelector('.check-icon');
            if (checkIcon) {
                checkIcon.style.display = circle.classList.contains('selected') ? 'flex' : 'none';
            }
        });
        
        // Size selection
        const sizeOptions = document.querySelectorAll('.size-option');
        sizeOptions.forEach(option => {
            option.addEventListener('click', function() {
                this.classList.toggle('selected');
            });
        });
        
        // Color selection
        const colorOptions = document.querySelectorAll('.color-option');
        colorOptions.forEach(option => {
            option.addEventListener('click', function() {
                this.classList.toggle('selected');
                // Toggle check icon for colors
                const checkIcon = this.querySelector('.check-icon');
                if (checkIcon) {
                    checkIcon.style.display = this.classList.contains('selected') ? 'flex' : 'none';
                }
            });
        });
        
        // Sort options
        const sortOptions = document.querySelectorAll('.sort-option');
        sortOptions.forEach(option => {
            option.addEventListener('click', function() {
                sortOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    }
    
    // Clear filters button
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            // Reset categories
            document.querySelectorAll('.category-circle').forEach(circle => {
                circle.classList.remove('selected');
                const checkIcon = circle.querySelector('.check-icon');
                if (checkIcon) checkIcon.style.display = 'none';
            });
            
            // Reset sizes
            document.querySelectorAll('.size-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Reset colors
            document.querySelectorAll('.color-option').forEach(option => {
                option.classList.remove('selected');
                const checkIcon = option.querySelector('.check-icon');
                if (checkIcon) checkIcon.style.display = 'none';
            });
            
            // Reset price range
            if (minSlider && maxSlider) {
                minSlider.value = minSlider.min;
                maxSlider.value = maxSlider.max;
                updateSlider();
            }
            
            // Reset sort options
            const sortOptions = document.querySelectorAll('.sort-option');
            sortOptions.forEach(opt => opt.classList.remove('selected'));
            if (sortOptions.length > 0) sortOptions[0].classList.add('selected');
        });
    }
    
    // Apply filters button
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            // Collect filter selections
            const filters = collectFilters();
            console.log("Applied filters:", filters);
            
            // Apply filters to products (example implementation)
            filterProducts(filters);
            
            // Close the modal
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
        });
    }
    
    function collectFilters() {
        const selectedCategories = [];
        document.querySelectorAll('.category-circle.selected').forEach(category => {
            const categoryName = category.closest('.category-item').querySelector('.category-name').textContent;
            selectedCategories.push(categoryName);
        });
        
        const selectedSizes = [];
        document.querySelectorAll('.size-option.selected').forEach(size => {
            selectedSizes.push(size.textContent);
        });
        
        const selectedColors = [];
        document.querySelectorAll('.color-option.selected').forEach(color => {
            // Get color from class name
            const colorClasses = Array.from(color.classList)
                .filter(cls => cls !== 'color-option' && cls !== 'selected');
            if (colorClasses.length > 0) {
                selectedColors.push(colorClasses[0]);
            }
        });
        
        const priceRange = {
            min: minSlider ? parseInt(minSlider.value) : 10,
            max: maxSlider ? parseInt(maxSlider.value) : 150
        };
        
        const sortBy = document.querySelector('.sort-option.selected span')?.textContent || 'Popular';
        
        return {
            categories: selectedCategories,
            sizes: selectedSizes,
            colors: selectedColors,
            priceRange,
            sortBy
        };
    }
    
    function filterProducts(filters) {
        // This is a placeholder function that would apply the filters
        // In a real implementation, this would filter the products based on the selected criteria
        // For now, just re-render all products
        renderProducts();
    }
    
    // Function to render products based on active filter and search query
    function renderProducts() {
        productGrid.innerHTML = '';
        
        const filteredProducts = products.filter(product => {
            // Apply time frame filter - now includes "all" option
            const timeFrameMatch = currentFilter === 'all' || currentFilter === product.timeFrame;
            
            // Apply search filter if there's a query
            const searchMatch = !searchQuery || 
                product.name.toLowerCase().includes(searchQuery);
            
            return timeFrameMatch && searchMatch;
        });
        
        if (filteredProducts.length === 0) {
            productGrid.innerHTML = '<div class="no-results">No products found</div>';
            return;
        }
        
        filteredProducts.forEach(product => {
            const productCard = createProductCard(product);
            productGrid.appendChild(productCard);
        });
        
        // Setup wishlist functionality after rendering products
        setupWishlistFunctionality();
    }
    
    function createProductCard(product) {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.setAttribute('data-id', product.id);
        
        card.innerHTML = `
            <div class="wishlist-icon">
                <i class="far fa-heart"></i>
            </div>
            <div class="product-image">
                <img src="${product.image}" alt="${product.name}">
            </div>
            <div class="product-info">
                <div class="product-description">${product.name}</div>
                <div class="price-row">
                    <div class="price-info">
                        <div class="current-price">BDT ${product.currentPrice}</div>
                        <div class="original-price">BDT ${product.originalPrice}</div>
                    </div>
                    <div class="discount-badge">${product.discount}</div>
                </div>
                <div class="action-row">
                    <div class="retailer-logo">
                        <img src="${product.retailerLogo}" alt="Retailer">
                    </div>
                    <button class="add-btn">Add to cart</button>
                </div>
            </div>
        `;
        
        return card;
    }
    
    // Setup wishlist functionality
    function setupWishlistFunctionality() {
        const wishlistIcons = document.querySelectorAll('.wishlist-icon');
        
        wishlistIcons.forEach(icon => {
            icon.addEventListener('click', function() {
                const heartIcon = this.querySelector('i');
                
                // Add clicked animation
                this.classList.add('clicked');
                
                // Remove animation class after animation completes
                setTimeout(() => {
                    this.classList.remove('clicked');
                }, 300);
                
                // Toggle heart icon
                if (heartIcon.classList.contains('far')) {
                    heartIcon.classList.remove('far');
                    heartIcon.classList.add('fas');
                } else {
                    heartIcon.classList.remove('fas');
                    heartIcon.classList.add('far');
                }
            });
        });
    }
    
    // Handle window resize event to adapt layout
    window.addEventListener('resize', function() {
        // You can add additional responsive behavior here if needed
    });
});
