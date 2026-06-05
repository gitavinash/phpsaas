/* 
========================================================================
   LUXURY AESTHETIC CLINIC - CORE INTERACTIONS
   Author: Antigravity UI/UX Core Team
   Description: Standard ES6 implementation of luxury micro-interactions,
                drag-based before-after slider, carousels, and animations.
========================================================================
*/

document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
  initStickyHeader();
  initScrollReveals();
  initCounters();
  initBeforeAfterSlider();
  initSocialCarousel();
  initTestimonialCarousel();
  initJourneyProgress();
  initFAQAccordion();
  initExperienceLightbox();
  initConsultationForm();
});

/* 1. MOBILE MENU TOGGLE */
function initMobileMenu() {
  const toggleBtn = document.querySelector('.mobile-nav-toggle');
  const navMenu = document.querySelector('.nav-menu');
  const navLinks = document.querySelectorAll('.nav-link');

  if (!toggleBtn || !navMenu) return;

  toggleBtn.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    toggleBtn.classList.toggle('open');
  });

  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      navMenu.classList.remove('active');
      toggleBtn.classList.remove('open');
    });
  });
}

/* 2. STICKY HEADER SCROLL STATE */
function initStickyHeader() {
  const header = document.querySelector('.main-header');
  if (!header) return;

  const handleScroll = () => {
    if (window.scrollY > 50) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  };

  window.addEventListener('scroll', handleScroll);
  handleScroll(); // Initial run in case of reload mid-scroll
}

/* 3. SCROLL REVEAL (INTERSECTION OBSERVER) */
function initScrollReveals() {
  const reveals = document.querySelectorAll('.reveal');
  if (reveals.length === 0) return;

  const observerOptions = {
    root: null,
    threshold: 0.15,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('active');
        obs.unobserve(entry.target); // Animate once
      }
    });
  }, observerOptions);

  reveals.forEach((element, index) => {
    // Add staggered delay to child items if defined
    if (element.classList.contains('reveal-stagger')) {
      element.style.transitionDelay = `${(index % 4) * 0.15}s`;
    }
    observer.observe(element);
  });
}

/* 4. ANIMATED COUNTERS */
function initCounters() {
  const counters = document.querySelectorAll('.counter-number');
  if (counters.length === 0) return;

  const observerOptions = {
    root: null,
    threshold: 0.5
  };

  const countUp = (element) => {
    const target = parseInt(element.getAttribute('data-target'), 10);
    const suffix = element.getAttribute('data-suffix') || '';
    const duration = 2000; // 2 seconds
    const startTime = performance.now();

    const updateCount = (currentTime) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      
      // Ease out quad formula
      const easeProgress = progress * (2 - progress);
      const currentValue = Math.floor(easeProgress * target);

      element.textContent = currentValue.toLocaleString() + suffix;

      if (progress < 1) {
        requestAnimationFrame(updateCount);
      } else {
        element.textContent = target.toLocaleString() + suffix;
      }
    };

    requestAnimationFrame(updateCount);
  };

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        countUp(entry.target);
        obs.unobserve(entry.target);
      }
    });
  }, observerOptions);

  counters.forEach(counter => observer.observe(counter));
}

/* 5. DYNAMIC BEFORE & AFTER SLIDER */
function initBeforeAfterSlider() {
  const slider = document.querySelector('.before-after-wrapper');
  if (!slider) return;

  const beforeImg = slider.querySelector('.slider-img-before');
  const handle = slider.querySelector('.slider-handle');
  
  if (!beforeImg || !handle) return;

  let isDragging = false;

  const setPosition = (x) => {
    const rect = slider.getBoundingClientRect();
    let positionX = x - rect.left;

    // Boundary constraints
    if (positionX < 0) positionX = 0;
    if (positionX > rect.width) positionX = rect.width;

    const percentage = (positionX / rect.width) * 100;
    
    beforeImg.style.width = `${percentage}%`;
    handle.style.left = `${percentage}%`;
  };

  // Event Handlers for Mouse
  const startDrag = () => { isDragging = true; };
  const stopDrag = () => { isDragging = false; };
  
  const onDrag = (e) => {
    if (!isDragging) return;
    setPosition(e.clientX);
  };

  // Event Handlers for Touch
  const onTouchDrag = (e) => {
    if (e.touches.length === 0) return;
    setPosition(e.touches[0].clientX);
  };

  // Register Events
  handle.addEventListener('mousedown', startDrag);
  window.addEventListener('mouseup', stopDrag);
  window.addEventListener('mousemove', onDrag);

  handle.addEventListener('touchstart', startDrag);
  window.addEventListener('touchend', stopDrag);
  window.addEventListener('touchmove', onTouchDrag);

  // Allow clicking on slider to snap position
  slider.addEventListener('click', (e) => {
    if (e.target !== handle && !handle.contains(e.target)) {
      setPosition(e.clientX);
    }
  });

  // BEFORE & AFTER CATEGORY FILTER FUNCTIONALITY
  const filterBtns = document.querySelectorAll('.filter-btn');
  const sampleData = {
    skin: {
      before: 'assets/images/before_skin.png',
      after: 'assets/images/after_skin.png',
      title: 'Advanced Pigmentation & Melasma Therapy',
      metricVal1: '3 Sessions',
      metricVal2: 'FDA Approved Laser',
      desc: 'Targeted medical-grade laser treatment specifically breaking down deep dermal melanin. Yields smooth, even-toned skin with zero scarring and minimal down-time.'
    },
    hair: {
      before: 'assets/images/before_skin.png', // Fallback skin textures but styled beautifully
      after: 'assets/images/after_skin.png',
      title: 'PRP & GFC Hair Restoration Treatment',
      metricVal1: '6 Sessions',
      metricVal2: '94% Hair Density Increase',
      desc: 'Autologous Growth Factor Concentrate therapy targeting active hair follicles. Delivers massive nutrient density directly to the scalp for clinical hair thickness regrowth.'
    },
    body: {
      before: 'assets/images/before_skin.png',
      after: 'assets/images/after_skin.png',
      title: 'CoolSculpting & Fat Reduction',
      metricVal1: '2 Sessions',
      metricVal2: '24% Fat Layer Reduction',
      desc: 'Cryolipolysis technology precision freezing targeted fat cells in stubborn body regions. Safely processed out by the immune system for structural contouring.'
    }
  };

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const category = btn.getAttribute('data-category');
      const data = sampleData[category];

      if (data) {
        // Update images
        slider.querySelector('.slider-img-after img').src = data.after;
        beforeImg.querySelector('img').src = data.before;
        
        // Reset slider center
        beforeImg.style.width = '50%';
        handle.style.left = '50%';

        // Update Text
        const detailCard = document.querySelector('.transform-details-card');
        if (detailCard) {
          detailCard.querySelector('.transform-title').textContent = data.title;
          detailCard.querySelector('.transform-details-desc').textContent = data.desc;
          
          const metrics = detailCard.querySelectorAll('.metric-item-num');
          if (metrics.length >= 2) {
            metrics[0].textContent = data.metricVal1;
            metrics[1].textContent = data.metricVal2;
          }
        }
      }
    });
  });
}

/* 6. SOCIAL CAROUSEL (DRAG AND CONTROL) */
function initSocialCarousel() {
  const container = document.querySelector('.carousel-container');
  const track = document.querySelector('.carousel-track');
  const btnPrev = document.querySelector('.social-proof-section .btn-prev');
  const btnNext = document.querySelector('.social-proof-section .btn-next');

  if (!container || !track || !btnPrev || !btnNext) return;

  let isDown = false;
  let startX;
  let scrollLeft = 0;
  let currentTranslate = 0;
  let prevTranslate = 0;

  // Track dragging calculations
  container.addEventListener('mousedown', (e) => {
    isDown = true;
    startX = e.pageX - track.offsetLeft;
    container.style.cursor = 'grabbing';
  });

  window.addEventListener('mouseup', () => {
    isDown = false;
    container.style.cursor = 'grab';
  });

  window.addEventListener('mousemove', (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - track.offsetLeft;
    const walk = (x - startX) * 1.5;
    track.style.transform = `translateX(${prevTranslate + walk}px)`;
    currentTranslate = prevTranslate + walk;
  });

  // Buttons navigation
  const getCardWidth = () => {
    const card = track.querySelector('.social-card');
    return card ? card.offsetWidth + 30 : 300; // card width + gap
  };

  btnNext.addEventListener('click', () => {
    const cardWidth = getCardWidth();
    const maxScroll = -(track.scrollWidth - container.offsetWidth);
    let newTranslate = prevTranslate - cardWidth;
    
    if (newTranslate < maxScroll) newTranslate = maxScroll;
    
    track.style.transform = `translateX(${newTranslate}px)`;
    prevTranslate = newTranslate;
  });

  btnPrev.addEventListener('click', () => {
    const cardWidth = getCardWidth();
    let newTranslate = prevTranslate + cardWidth;
    
    if (newTranslate > 0) newTranslate = 0;
    
    track.style.transform = `translateX(${newTranslate}px)`;
    prevTranslate = newTranslate;
  });
}

/* 7. TESTIMONIAL CAROUSEL */
function initTestimonialCarousel() {
  const track = document.querySelector('.testimonial-track');
  const slides = document.querySelectorAll('.testimonial-slide');
  const btnPrev = document.querySelector('.testimonial-carousel .btn-prev');
  const btnNext = document.querySelector('.testimonial-carousel .btn-next');

  if (!track || slides.length === 0 || !btnPrev || !btnNext) return;

  let currentIndex = 0;

  const updateSlidePosition = () => {
    track.style.transform = `translateX(-${currentIndex * 100}%)`;
  };

  btnNext.addEventListener('click', () => {
    currentIndex = (currentIndex + 1) % slides.length;
    updateSlidePosition();
  });

  btnPrev.addEventListener('click', () => {
    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
    updateSlidePosition();
  });
}

/* 8. TREATMENT JOURNEY PROGRESS LINE */
function initJourneyProgress() {
  const steps = document.querySelectorAll('.journey-step');
  const progressLine = document.querySelector('.journey-timeline-progress');

  if (steps.length === 0 || !progressLine) return;

  const observerOptions = {
    root: null,
    threshold: 0.6
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // Highlight up to the current step on scroll
        const activeStep = entry.target;
        let activeIdx = 0;
        
        steps.forEach((step, idx) => {
          if (step === activeStep || idx <= activeIdx) {
            step.classList.add('active');
            activeIdx = idx;
          }
        });

        // Set progress width percentage (0% to 100%)
        const percentage = (activeIdx / (steps.length - 1)) * 90; // scale nicely inside bounds
        progressLine.style.width = `${5 + percentage}%`;
      }
    });
  }, observerOptions);

  steps.forEach(step => observer.observe(step));
}

/* 9. LUXURY FAQ ACCORDION */
function initFAQAccordion() {
  const faqHeaders = document.querySelectorAll('.faq-header');

  faqHeaders.forEach(header => {
    header.addEventListener('click', () => {
      const item = header.parentElement;
      const body = item.querySelector('.faq-body');
      
      // Close other FAQs if active
      document.querySelectorAll('.faq-item').forEach(otherItem => {
        if (otherItem !== item && otherItem.classList.contains('active')) {
          otherItem.classList.remove('active');
          otherItem.querySelector('.faq-body').style.maxHeight = null;
        }
      });

      // Toggle current FAQ
      item.classList.toggle('active');
      if (item.classList.contains('active')) {
        body.style.maxHeight = `${body.scrollHeight}px`;
      } else {
        body.style.maxHeight = null;
      }
    });
  });
}

/* 10. CLINIC EXPERIENCE LIGHTBOX & CINEMATIC VIDEO */
function initExperienceLightbox() {
  const lightbox = document.createElement('div');
  lightbox.classList.add('lightbox-modal');
  lightbox.innerHTML = `
    <div class="lightbox-content">
      <button class="lightbox-close-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
      <img src="" alt="" class="lightbox-img">
      <div class="lightbox-caption"></div>
    </div>
  `;
  document.body.appendChild(lightbox);

  const closeBtn = lightbox.querySelector('.lightbox-close-btn');
  const lightboxImg = lightbox.querySelector('.lightbox-img');
  const lightboxCaption = lightbox.querySelector('.lightbox-caption');

  // Open Lightbox for Images
  const galleryItems = document.querySelectorAll('.experience-item:not(.item-video)');
  galleryItems.forEach(item => {
    item.addEventListener('click', () => {
      const img = item.querySelector('img');
      const captionText = item.querySelector('.experience-hover-lbl').textContent;
      
      lightboxImg.src = img.src;
      lightboxCaption.textContent = captionText;
      
      lightbox.classList.add('active');
      document.body.style.overflow = 'hidden'; // Lock scrolling
    });
  });

  // Open video placeholder for cinematic room
  const videoPlayBtn = document.querySelector('.cinematic-play-btn');
  if (videoPlayBtn) {
    videoPlayBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      
      lightboxImg.src = 'assets/images/hero_clinic.png'; // Mock video static shot
      lightboxCaption.textContent = 'Luxury Aesthetic Clinic - Cinematic Tour (Playing Demo)';
      
      lightbox.classList.add('active');
      document.body.style.overflow = 'hidden';
    });
  }

  // Close Lightbox
  const closeLightbox = () => {
    lightbox.classList.remove('active');
    document.body.style.overflow = '';
  };

  closeBtn.addEventListener('click', closeLightbox);
  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) closeLightbox();
  });

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLightbox();
  });
}

/* 11. CONSULTATION FORM SUBMIT & INPUT LABEL EFFECT */
function initConsultationForm() {
  const form = document.getElementById('consultationForm');
  if (!form) return;

  const inputs = form.querySelectorAll('.form-input, .form-textarea, .form-select');

  // Input label interaction helpers
  inputs.forEach(input => {
    const checkValue = () => {
      if (input.value !== '') {
        input.classList.add('has-value');
      } else {
        input.classList.remove('has-value');
      }
    };

    input.addEventListener('blur', checkValue);
    input.addEventListener('change', checkValue);
    
    // Check initial values (autofill, etc.)
    setTimeout(checkValue, 100);
  });

  // Submission handler
  form.addEventListener('submit', (e) => {
    e.preventDefault();

    const name = document.getElementById('formName').value;
    const phone = document.getElementById('formPhone').value;
    const concern = document.getElementById('formConcern').value;

    if (!name || !phone) {
      alert('Please fill out all mandatory fields.');
      return;
    }

    // Success styling and popup simulation
    const btn = form.querySelector('.form-btn-submit');
    const originalText = btn.textContent;
    
    btn.textContent = 'Booking Confirmed...';
    btn.style.backgroundColor = '#128C7E';
    btn.style.borderColor = '#128C7E';
    
    alert(`Thank you, ${name}! Your consultation request for ${concern || 'aesthetic treatment'} has been securely submitted. Dr. E. Reed's team will contact you shortly.`);
    
    setTimeout(() => {
      form.reset();
      btn.textContent = originalText;
      btn.style.backgroundColor = '';
      btn.style.borderColor = '';
      
      // Reset inputs state
      inputs.forEach(input => input.classList.remove('has-value'));
    }, 2000);
  });
}
