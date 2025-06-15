document.addEventListener("DOMContentLoaded", () => {
  // ------------------- Banner Slider (Auto Loop Smoothly) -------------------
  const bannerSlider = document.querySelector(".banner-slider");
  const bannerSlides = document.querySelectorAll(".banner-slide");
  const dots = document.querySelectorAll(".slider-dot");
  const slideCount = bannerSlides.length;
  let currentSlide = 0;
  let bannerInterval;

  function showSlide(index) {
    bannerSlider.style.transition = "transform 0.5s ease-in-out";
    bannerSlider.style.transform = `translateX(-${index * 100}%)`;
    dots.forEach(dot => dot.classList.remove("active"));
    dots[index % slideCount].classList.add("active");
    currentSlide = index;
  }

  function nextSlide() {
    currentSlide++;
    showSlide(currentSlide);

    if (currentSlide >= slideCount) {
      setTimeout(() => {
        bannerSlider.style.transition = "none";
        bannerSlider.style.transform = `translateX(0%)`;
        currentSlide = 0;
        dots.forEach(dot => dot.classList.remove("active"));
        dots[0].classList.add("active");
      }, 500);
    }
  }

  function startBannerInterval() {
    bannerInterval = setInterval(nextSlide, 3000);
  }

  bannerSlider.addEventListener("mouseenter", () => clearInterval(bannerInterval));
  bannerSlider.addEventListener("mouseleave", startBannerInterval);

  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      clearInterval(bannerInterval);
      showSlide(index);
      startBannerInterval();
    });
  });

  startBannerInterval();
  showSlide(0);


  // ------------------- Touch Slider Setup -------------------
  function setupTouchSlider(slider, options = { autoSlide: false }) {
    let isDragging = false;
    let startX = 0;
    let currentX = 0;
    let translateX = 0;
    let currentOffset = 0;
    const slideWidth = 115;
    const slides = slider.children;
    const totalSlides = slides.length;
    const totalWidth = totalSlides * slideWidth;

    slider.innerHTML += slider.innerHTML; // duplicate content for infinite loop
    slider.style.width = `${slider.children.length * slideWidth}px`;

    slider.addEventListener("touchstart", (e) => {
      isDragging = true;
      startX = e.touches[0].clientX;
      slider.style.transition = "none";
    });

    slider.addEventListener("touchmove", (e) => {
      if (!isDragging) return;
      currentX = e.touches[0].clientX;
      const delta = currentX - startX;
      slider.style.transform = `translateX(${currentOffset + delta}px)`;
      translateX = delta;
    });

    slider.addEventListener("touchend", () => {
      isDragging = false;
      currentOffset += translateX;

      if (currentOffset < -totalWidth) currentOffset = 0;
      if (currentOffset > 0) currentOffset = -totalWidth;

      slider.style.transition = "transform 0.3s ease";
      slider.style.transform = `translateX(${currentOffset}px)`;
    });

    // Optional auto-slide
    if (options.autoSlide) {
      setInterval(() => {
        currentOffset -= slideWidth;
        if (Math.abs(currentOffset) >= totalWidth) {
          currentOffset = 0;
        }
        slider.style.transition = "transform 0.3s ease";
        slider.style.transform = `translateX(${currentOffset}px)`;
      }, 3000);
    }
  }

  // ------------------- Sliders -------------------
  const categorySlider = document.querySelector(".category-slider");
  const brandSlider = document.querySelector(".brand-slider");

  setupTouchSlider(categorySlider); // touch only
  setupTouchSlider(brandSlider, { autoSlide: true }); // auto slide
});