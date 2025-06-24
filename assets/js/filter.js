// Handle price range slider and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const minSlider = document.getElementById('min-price');
    const maxSlider = document.getElementById('max-price');
    const sliderTrack = document.querySelector('.slider-track');
    const priceDisplay = document.querySelector('.price-display');
    const modal = document.getElementById('filterModal');
    const openBtn = document.getElementById('openFilterBtn');
    const closeBtn = document.getElementById('closeFilterBtn');
    
    // Price range slider functionality
    function updateSlider() {
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
        
        // Initial update
        updateSlider();
    }
    
    // Modal functionality
    if (openBtn) {
        openBtn.onclick = function() {
            modal.style.display = 'block';
            document.body.classList.add('modal-open');
        }
    }
    
    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    }
    
    // Filter option interactions
    setupFilterInteractions();
    
    function setupFilterInteractions() {
        // Category selection - Fixed to properly toggle check icons
        const categoryCircles = document.querySelectorAll('.category-circle');
        categoryCircles.forEach(circle => {
            circle.addEventListener('click', function() {
                this.classList.toggle('selected');
                // Update the check icon visibility when toggling selection
                const checkIcon = this.querySelector('.check-icon');
                if (checkIcon) {
                    if (this.classList.contains('selected')) {
                        checkIcon.style.display = 'flex';
                    } else {
                        checkIcon.style.display = 'none';
                    }
                }
            });
            
            // Initialize the check icon display state
            const checkIcon = circle.querySelector('.check-icon');
            if (checkIcon) {
                if (circle.classList.contains('selected')) {
                    checkIcon.style.display = 'flex';
                } else {
                    checkIcon.style.display = 'none';
                }
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
            });
        });
        
        // Sort options selection (only one can be selected)
        const sortOptions = document.querySelectorAll('.sort-option');
        sortOptions.forEach(option => {
            option.addEventListener('click', function() {
                sortOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
        
        // Clear filters button
        const clearBtn = document.getElementById('clearFilters');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                categoryCircles.forEach(circle => circle.classList.remove('selected'));
                sizeOptions.forEach(option => option.classList.remove('selected'));
                colorOptions.forEach(option => option.classList.remove('selected'));
                
                // Reset price sliders
                if (minSlider && maxSlider) {
                    minSlider.value = minSlider.min;
                    maxSlider.value = maxSlider.max;
                    updateSlider();
                }
                
                // Reset sort options
                if (sortOptions.length > 0) {
                    sortOptions.forEach(opt => opt.classList.remove('selected'));
                    sortOptions[0].classList.add('selected');
                }
            });
        }
        
        // Apply filters button
        const applyBtn = document.getElementById('applyFilters');
        if (applyBtn) {
            applyBtn.addEventListener('click', function() {
                // Collect all selected filters
                const selectedFilters = collectSelectedFilters();
                console.log('Applying filters:', selectedFilters);
                
                // Close the modal
                if (modal) {
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                }
                
                // Additional filter application logic would go here
            });
        }
    }
    
    function collectSelectedFilters() {
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
            min: minSlider ? parseInt(minSlider.value) : 0,
            max: maxSlider ? parseInt(maxSlider.value) : 0
        };
        
        const selectedSortOption = document.querySelector('.sort-option.selected span')?.textContent || 'Popular';
        
        return {
            categories: selectedCategories,
            sizes: selectedSizes,
            colors: selectedColors,
            priceRange: priceRange,
            sortBy: selectedSortOption
        };
    }
    
    // Make updateSlider function globally available
    window.updateSlider = updateSlider;
});
