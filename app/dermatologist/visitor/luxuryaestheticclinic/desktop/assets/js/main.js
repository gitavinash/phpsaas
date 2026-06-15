/* 
========================================================================
   LUXURY AESTHETIC CLINIC - CORE INTERACTIONS
   Author: Antigravity UI/UX Core Team
   Description: Standard ES6 implementation of luxury micro-interactions,
                drag-based before-after slider, carousels, and animations.
========================================================================
*/

document.addEventListener("DOMContentLoaded", () => {
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
  const toggleBtn = document.querySelector(".mobile-nav-toggle");
  const navMenu = document.querySelector(".nav-drawer-mobile");
  const navLinks = document.querySelectorAll("a.nav-link-mobile");

  if (!toggleBtn || !navMenu) return;

  toggleBtn.addEventListener("click", () => {
    navMenu.classList.toggle("active");
    toggleBtn.classList.toggle("open");
  });

  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      navMenu.classList.remove("active");
      toggleBtn.classList.remove("open");
    });
  });
}

/* 2. STICKY HEADER SCROLL STATE */
function initStickyHeader() {
  const header = document.querySelector(".main-header");
  if (!header) return;

  const handleScroll = () => {
    if (window.scrollY > 50) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  };

  window.addEventListener("scroll", handleScroll);
  handleScroll(); // Initial run in case of reload mid-scroll
}

/* 3. SCROLL REVEAL (INTERSECTION OBSERVER) */
function initScrollReveals() {
  const reveals = document.querySelectorAll(".reveal");
  if (reveals.length === 0) return;

  const observerOptions = {
    root: null,
    threshold: 0.15,
    rootMargin: "0px 0px -50px 0px",
  };

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("active");
        obs.unobserve(entry.target); // Animate once
      }
    });
  }, observerOptions);

  reveals.forEach((element, index) => {
    // Add staggered delay to child items if defined
    if (element.classList.contains("reveal-stagger")) {
      element.style.transitionDelay = `${(index % 4) * 0.15}s`;
    }
    observer.observe(element);
  });
}

/* 4. ANIMATED COUNTERS */
function initCounters() {
  const counters = document.querySelectorAll(".counter-number");
  if (counters.length === 0) return;

  const observerOptions = {
    root: null,
    threshold: 0.5,
  };

  const countUp = (element) => {
    const target = parseInt(element.getAttribute("data-target"), 10);
    const suffix = element.getAttribute("data-suffix") || "";
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
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        countUp(entry.target);
        obs.unobserve(entry.target);
      }
    });
  }, observerOptions);

  counters.forEach((counter) => observer.observe(counter));
}

/* 5. DYNAMIC BEFORE & AFTER SLIDER & TAB SWITCHING */
function initBeforeAfterSlider() {
  const sliders = document.querySelectorAll(".before-after-wrapper");
  const filterBtns = document.querySelectorAll(".filter-btn");
  const panels = document.querySelectorAll(".transformation-panel");

  // Initialize dragging and clicking events for all sliders on the page
  sliders.forEach((slider) => {
    const beforeImg = slider.querySelector(".slider-img-before");
    const handle = slider.querySelector(".slider-handle");

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
    const startDrag = (e) => {
      isDragging = true;
      e.preventDefault(); // Prevent text selection
    };
    const stopDrag = () => {
      isDragging = false;
    };
    const onDrag = (e) => {
      if (!isDragging) return;
      setPosition(e.clientX);
    };

    // Event Handlers for Touch (Mobile)
    const onTouchDrag = (e) => {
      if (!isDragging || e.touches.length === 0) return;
      setPosition(e.touches[0].clientX);
    };

    // Register Events
    handle.addEventListener("mousedown", startDrag);
    window.addEventListener("mouseup", stopDrag);
    window.addEventListener("mousemove", onDrag);

    handle.addEventListener("touchstart", (e) => {
      isDragging = true;
    }, { passive: true });
    window.addEventListener("touchend", stopDrag);
    window.addEventListener("touchmove", onTouchDrag, { passive: true });

    // Allow clicking on slider to snap position
    slider.addEventListener("click", (e) => {
      if (e.target !== handle && !handle.contains(e.target)) {
        setPosition(e.clientX);
      }
    });
  });

  // Tabbing / filter switching functionality
  filterBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      filterBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      const targetCategory = btn.getAttribute("data-category");

      panels.forEach((panel) => {
        if (panel.getAttribute("data-category") === targetCategory) {
          panel.classList.add("active");
          // Re-trigger layout alignment inside the panel just in case
          const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                entry.target.classList.add("active");
                obs.unobserve(entry.target);
              }
            });
          });
          panel.querySelectorAll(".reveal").forEach((el) => observer.observe(el));
        } else {
          panel.classList.remove("active");
        }
      });
    });
  });
}

function initSocialCarousel() {
  const container = document.querySelector(".carousel-container");
  const track = document.querySelector(".carousel-track");
  const btnPrev = document.querySelector(".social-proof-section .btn-prev");
  const btnNext = document.querySelector(".social-proof-section .btn-next");

  if (!container || !track || !btnPrev || !btnNext) return;

  const originalCards = Array.from(track.querySelectorAll(".social-card"));
  if (originalCards.length === 0) return;

  const visibleCardsCount = 4; // Max visible on desktop

  // Tag original cards with their original indices
  originalCards.forEach((card, idx) => {
    card.setAttribute("data-index", idx);
    const img = card.querySelector(".social-video-img");
    const thumbContainer = card.querySelector(".social-video-thumb");
    if (img) img.style.cursor = "pointer";
    if (thumbContainer) thumbContainer.style.cursor = "pointer";
  });

  // Clone cards for infinite loop
  const startClones = originalCards.slice(0, visibleCardsCount).map(el => el.cloneNode(true));
  const endClones = originalCards.slice(-visibleCardsCount).map(el => el.cloneNode(true));

  // Prepend and Append clones (reverse endClones so order is correct when inserting at firstChild)
  endClones.reverse().forEach(clone => {
    track.insertBefore(clone, track.firstChild);
  });
  startClones.forEach(clone => {
    track.appendChild(clone);
  });

  const getAllCards = () => track.querySelectorAll(".social-card");

  let isDown = false;
  let startX;
  let currentTranslate = 0;
  let prevTranslate = 0;

  // Video embed URL parser helper
  function getEmbedVideoUrl(url) {
    if (!url) return '';
    if (url.endsWith('.mp4') || url.includes('/upload/')) {
      return url;
    }
    // Extract ID or shortcode
    let id = '';
    if (url.includes('youtube.com') || url.includes('youtu.be')) {
      const match = url.match(/(?:shorts\/|v=|be\/|embed\/)([^?&#]+)/);
      if (match && match[1]) {
        id = match[1];
      }
    } else if (url.includes('instagram.com')) {
      const match = url.match(/(?:reel|reels|p)\/([^/?#&]+)/);
      if (match && match[1]) {
        id = match[1];
      }
    }
    if (id) {
      return `/upload/hb.local/social_proof/${id}.mp4`;
    }
    return url;
  }

  // Title case helper
  function ucwords(str) {
    if (!str) return '';
    return str.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
  }

  // Inject Popup CSS Styles
  const styleTag = document.createElement("style");
  styleTag.textContent = `
    /* Hide card badges, play buttons, and tags */
    .play-button-overlay {
      display: none !important;
    }
    .social-icon-badge {
      display: none !important;
    }
    .social-card-tag {
      display: none !important;
    }

    .social-video-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.50);
      align-items: center;
      justify-content: center;
      z-index: 9999;
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
    }
    .social-video-modal-content {
      position: relative;
      width: 90%;
      max-width: 1100px;
      height: 80vh;
      background: #111;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 30px 80px rgba(0,0,0,0.8);
      border: 1px solid rgba(255,255,255,0.1);
      display: flex;
      flex-direction: row-reverse; /* Video Left, Metadata Right */
    }
    .social-video-modal-sidebar {
      width: 40%;
      padding: 50px 40px;
      display: flex;
      flex-direction: column;
      background: #161616;
      overflow-y: auto;
      color: #fff;
      border-left: 1px solid rgba(255,255,255,0.08);
      box-sizing: border-box;
    }
    .social-video-modal-player-container {
      width: 60%;
      position: relative;
      background: #000;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .social-video-modal-body {
      width: 100%;
      height: 100%;
      position: relative;
      overflow: hidden;
    }
    .social-video-modal-body iframe {
      width: 100%;
      height: 100%;
      border: none;
    }
    
    /* Instagram Clip styling to hide header/follow/caption in Modal */
    .social-video-modal-body.instagram-video {
      overflow: hidden;
      position: relative;
      width: 100%;
      height: 100%;
    }
    .social-video-modal-body.instagram-video iframe {
      position: absolute;
      top: -95px;
      left: 0;
      width: 100%;
      height: calc(100% + 190px) !important;
    }

    /* YouTube Clip styling to hide title/avatar in Modal */
    .social-video-modal-body.youtube-video {
      overflow: hidden;
      position: relative;
      width: 100%;
      height: 100%;
    }
    .social-video-modal-body.youtube-video iframe {
      position: absolute;
      top: -80px;
      left: 0;
      width: 100%;
      height: calc(100% + 160px) !important;
    }

    .social-modal-nav-btn:hover, .social-video-modal-close:hover {
      background: var(--color-primary-gold) !important;
      border-color: var(--color-primary-gold) !important;
      color: #fff !important;
    }
    
    @media (max-width: 768px) {
      .social-video-modal-content {
        flex-direction: column; /* Video Top, Metadata Bottom */
        width: 95%;
        height: 90vh;
        border-radius: 12px;
      }
      .social-video-modal-player-container {
        width: 100%;
        height: 55%;
      }
      .social-video-modal-sidebar {
        width: 100%;
        height: 45%;
        padding: 25px 20px;
        border-left: none;
        border-top: 1px solid rgba(255,255,255,0.08);
      }
      .social-modal-nav-btn {
        width: 40px !important;
        height: 40px !important;
      }
      .social-video-modal-close {
        top: 10px !important;
        right: 10px !important;
        width: 36px !important;
        height: 36px !important;
      }
    }
  `;
  document.head.appendChild(styleTag);

  // Create Video Popup Modal structure
  const modal = document.createElement("div");
  modal.className = "social-video-modal";
  modal.innerHTML = `
    <div class="social-video-modal-content">
      <button class="social-video-modal-close" style="
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(0, 0, 0, 0.6);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
        transition: all 0.3s;
      ">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>

      <div class="social-video-modal-sidebar">
        <span class="social-modal-type-badge" style="
          align-self: flex-start;
          font-size: 1.1rem;
          text-transform: uppercase;
          letter-spacing: 0.15em;
          color: #C5A880;
          font-weight: 600;
          margin-bottom: 20px;
          padding-bottom: 5px;
          border-bottom: 2px solid #C5A880;
        "></span>
        <h3 class="social-modal-title" style="
          font-family: var(--font-serif);
          font-size: 2.2rem;
          font-weight: 600;
          line-height: 1.4;
          margin: 0 0 20px 0;
          color: #fff;
        "></h3>
        <p class="social-modal-description" style="
          font-size: 1.4rem;
          line-height: 1.7;
          color: #ccc;
          margin: 0 0 30px 0;
        "></p>
        
        <div class="social-modal-reference-box" style="
          margin-top: auto;
          padding: 20px;
          background: rgba(197, 168, 128, 0.08);
          border: 1px solid rgba(197, 168, 128, 0.2);
          border-radius: 8px;
          display: none;
          flex-direction: column;
          gap: 15px;
        ">
          <span style="font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.05em; color: #C5A880; display: block; margin-bottom: 2px;">Related Treatment</span>
          
          <div class="social-modal-ref-card" style="
            display: flex;
            gap: 15px;
            align-items: center;
          ">
            <img class="social-modal-ref-img" src="" alt="" style="
              width: 70px;
              height: 70px;
              object-fit: cover;
              border-radius: 6px;
              border: 1px solid rgba(255,255,255,0.1);
              display: none;
            " />
            
            <div style="flex-grow: 1;">
              <h4 class="social-modal-ref-title" style="
                font-family: var(--font-sans);
                font-size: 1.5rem;
                font-weight: 600;
                margin: 0 0 4px 0;
                color: #fff;
              "></h4>
              <p class="social-modal-ref-desc" style="
                font-size: 1.2rem;
                line-height: 1.5;
                color: #aaa;
                margin: 0 0 8px 0;
                display: none;
              "></p>
              
              <a class="social-modal-reference-link" href="#" style="
                font-size: 1.3rem;
                font-weight: 600;
                color: #C5A880;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                transition: color 0.3s;
              ">
                <span>View Details</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="social-video-modal-player-container">
        <button class="social-modal-nav-btn btn-prev" style="
          position: absolute;
          left: 20px;
          top: 50%;
          transform: translateY(-50%);
          background: rgba(0, 0, 0, 0.6);
          border: 1px solid rgba(255,255,255,0.15);
          color: #fff;
          width: 50px;
          height: 50px;
          border-radius: 50%;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 10;
          transition: all 0.3s ease;
        ">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>

        <button class="social-modal-nav-btn btn-next" style="
          position: absolute;
          right: 20px;
          top: 50%;
          transform: translateY(-50%);
          background: rgba(0, 0, 0, 0.6);
          border: 1px solid rgba(255,255,255,0.15);
          color: #fff;
          width: 50px;
          height: 50px;
          border-radius: 50%;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 10;
          transition: all 0.3s ease;
        ">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>

        <div class="social-video-modal-body"></div>
      </div>
    </div>
  `;
  document.body.appendChild(modal);

  let modalActiveIndex = 0;

  const openPopupAtIndex = (index) => {
    if (index < 0 || index >= originalCards.length) return;
    modalActiveIndex = index;

    const card = originalCards[index];
    const videoUrl = card.getAttribute("data-video-url");
    const title = card.getAttribute("data-title");
    const description = card.getAttribute("data-description");
    const refType = card.getAttribute("data-ref-type");
    const refUrl = card.getAttribute("data-ref-url");

    if (!videoUrl) return;

    const resolvedUrl = getEmbedVideoUrl(videoUrl);

    modal.querySelector(".social-modal-type-badge").textContent = "Video Insights";
    modal.querySelector(".social-modal-title").textContent = title || '';
    modal.querySelector(".social-modal-description").textContent = description || 'No description available for this post.';

    const refTitle = card.getAttribute("data-ref-title");
    const refImage = card.getAttribute("data-ref-image");
    const refDescription = card.getAttribute("data-ref-description");

    const refBox = modal.querySelector(".social-modal-reference-box");
    if (refUrl) {
      refBox.style.display = "flex";
      
      const refLink = refBox.querySelector(".social-modal-reference-link");
      refLink.href = refUrl;
      
      const refTitleEl = refBox.querySelector(".social-modal-ref-title");
      refTitleEl.textContent = refTitle || ucwords(refType || 'details');

      const refImgEl = refBox.querySelector(".social-modal-ref-img");
      if (refImage) {
        refImgEl.src = refImage;
        refImgEl.alt = refTitle || 'Treatment image';
        refImgEl.style.display = 'block';
      } else {
        refImgEl.style.display = 'none';
      }

      const refDescEl = refBox.querySelector(".social-modal-ref-desc");
      if (refDescription) {
        refDescEl.textContent = refDescription;
        refDescEl.style.display = 'block';
      } else {
        refDescEl.style.display = 'none';
      }
    } else {
      refBox.style.display = "none";
    }

    const modalBody = modal.querySelector(".social-video-modal-body");
    
    modalBody.className = "social-video-modal-body";
    modalBody.innerHTML = `
      <video src="${resolvedUrl}" 
             autoplay 
             loop 
             playsinline 
             style="width:100%; height:100%; object-fit:contain; border:none; cursor:pointer;"></video>
      
      <!-- Custom Modal Controls Overlay -->
      <div class="social-modal-controls" style="
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 15px;
        background: rgba(0, 0, 0, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 10px 20px;
        border-radius: 30px;
        z-index: 15;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
      ">
        <button class="modal-control-btn modal-play-pause-btn" aria-label="Pause" style="
          background: none;
          border: none;
          color: #fff;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: color 0.3s;
        ">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>
        </button>
        <button class="modal-control-btn modal-mute-unmute-btn" aria-label="Mute" style="
          background: none;
          border: none;
          color: #fff;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: color 0.3s;
        ">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><line x1="23" y1="9" x2="17" y2="15"></line><line x1="17" y1="9" x2="23" y2="15"></line></svg>
        </button>
      </div>
    `;

    const videoEl = modalBody.querySelector("video");
    const modalPlayPause = modalBody.querySelector(".modal-play-pause-btn");
    const modalMuteUnmute = modalBody.querySelector(".modal-mute-unmute-btn");

    if (videoEl) {
      // Sync mute state from original card
      const origVideo = card.querySelector("video");
      if (origVideo) {
        videoEl.muted = origVideo.muted;
      }

      if (videoEl.muted) {
        modalMuteUnmute.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><line x1="23" y1="9" x2="17" y2="15"></line><line x1="17" y1="9" x2="23" y2="15"></line></svg>`;
      } else {
        modalMuteUnmute.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>`;
      }

      videoEl.addEventListener("click", () => {
        toggleModalPlay();
      });

      modalPlayPause.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleModalPlay();
      });

      modalMuteUnmute.addEventListener("click", (e) => {
        e.stopPropagation();
        videoEl.muted = !videoEl.muted;
        if (videoEl.muted) {
          modalMuteUnmute.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><line x1="23" y1="9" x2="17" y2="15"></line><line x1="17" y1="9" x2="23" y2="15"></line></svg>`;
        } else {
          modalMuteUnmute.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>`;
        }
      });

      const toggleModalPlay = () => {
        if (videoEl.paused) {
          videoEl.play();
          modalPlayPause.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>`;
        } else {
          videoEl.pause();
          modalPlayPause.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>`;
        }
      };
    }

    modal.style.display = "flex";
    document.body.style.overflow = "hidden";
  };

  const closeModal = () => {
    modal.style.display = "none";
    modal.querySelector(".social-video-modal-body").innerHTML = "";
    document.body.style.overflow = "";
    playAllInlineVideos();
  };

  modal.querySelector(".social-video-modal-close").addEventListener("click", closeModal);
  modal.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
  });
  window.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.style.display === "flex") {
      closeModal();
    }
  });

  const modalBtnNext = modal.querySelector(".social-video-modal-player-container .btn-next");
  const modalBtnPrev = modal.querySelector(".social-video-modal-player-container .btn-prev");

  modalBtnNext.addEventListener("click", (e) => {
    e.stopPropagation();
    let nextIdx = modalActiveIndex + 1;
    if (nextIdx >= originalCards.length) nextIdx = 0;
    openPopupAtIndex(nextIdx);
  });

  modalBtnPrev.addEventListener("click", (e) => {
    e.stopPropagation();
    let prevIdx = modalActiveIndex - 1;
    if (prevIdx < 0) prevIdx = originalCards.length - 1;
    openPopupAtIndex(prevIdx);
  });

  const playAllInlineVideos = () => {
    if (modal.style.display === "flex") return;
    getAllCards().forEach(card => {
      const video = card.querySelector("video");
      if (video) {
        video.play().catch(err => {
          // Prevent standard autoplay warnings
        });
      }
    });
  };

  const stopAllInlineVideos = () => {
    getAllCards().forEach(card => {
      const video = card.querySelector("video");
      if (video) video.pause();
    });
  };

  // Update active highlighting for centered card
  const updateActiveVideo = () => {
    const containerRect = container.getBoundingClientRect();
    const containerCenter = containerRect.left + containerRect.width / 2;

    let closestCard = null;
    let minDistance = Infinity;
    let closestIdx = 0;
    const allCards = getAllCards();

    allCards.forEach((card, idx) => {
      const cardRect = card.getBoundingClientRect();
      const cardCenter = cardRect.left + cardRect.width / 2;
      const distance = Math.abs(cardCenter - containerCenter);
      if (distance < minDistance) {
        minDistance = distance;
        closestCard = card;
        closestIdx = idx;
      }
    });

    allCards.forEach((card, idx) => {
      if (idx === closestIdx) {
        card.classList.add("active");
      } else {
        card.classList.remove("active");
      }
    });
  };

  // Event delegation click handler to launch full screen popup
  track.addEventListener("click", (e) => {
    const card = e.target.closest(".social-card");
    if (!card) return;

    // Check if the click was on the thumbnail or image (which launches popup)
    const isThumb = e.target.closest(".social-video-thumb") || e.target.classList.contains("social-video-img");
    if (!isThumb) return;

    const indexStr = card.getAttribute("data-index");
    if (indexStr === null) return;

    e.preventDefault();
    e.stopPropagation();
    stopAllInlineVideos();
    openPopupAtIndex(parseInt(indexStr, 10));
  });

  // Dragging / swiping positioning adjustments
  const adjustPosition = () => {
    const cardWidth = getCardWidth();
    const minTranslate = -((visibleCardsCount + originalCards.length) * cardWidth);
    const maxTranslate = -(visibleCardsCount * cardWidth);

    if (currentTranslate > maxTranslate + 0.1 * cardWidth) {
      currentTranslate -= originalCards.length * cardWidth;
      track.style.transition = "none";
      track.style.transform = `translateX(${currentTranslate}px)`;
      prevTranslate = currentTranslate;
      track.offsetHeight; // force reflow
      track.style.transition = "";
    } else if (currentTranslate < minTranslate - 0.1 * cardWidth) {
      currentTranslate += originalCards.length * cardWidth;
      track.style.transition = "none";
      track.style.transform = `translateX(${currentTranslate}px)`;
      prevTranslate = currentTranslate;
      track.offsetHeight; // force reflow
      track.style.transition = "";
    }
  };

  // Track transitionend wrapper logic
  track.addEventListener("transitionend", () => {
    adjustPosition();
    updateActiveVideo();
  });

  // Track dragging logic
  container.addEventListener("mousedown", (e) => {
    isDown = true;
    startX = e.pageX - track.offsetLeft;
    container.style.cursor = "grabbing";
    track.style.transition = "none";
  });

  window.addEventListener("mouseup", () => {
    if (isDown) {
      isDown = false;
      container.style.cursor = "grab";
      track.style.transition = "";
      snapToNearestCard();
    }
  });

  window.addEventListener("mousemove", (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - track.offsetLeft;
    const walk = (x - startX) * 1.5;
    currentTranslate = prevTranslate + walk;

    const cardWidth = getCardWidth();
    const minTranslate = -((visibleCardsCount + originalCards.length) * cardWidth);
    const maxTranslate = -(visibleCardsCount * cardWidth);

    if (currentTranslate > maxTranslate + 0.1 * cardWidth) {
      currentTranslate -= originalCards.length * cardWidth;
      startX -= originalCards.length * cardWidth;
    } else if (currentTranslate < minTranslate - 0.1 * cardWidth) {
      currentTranslate += originalCards.length * cardWidth;
      startX += originalCards.length * cardWidth;
    }

    track.style.transform = `translateX(${currentTranslate}px)`;
  });

  // Touch Swiping (Mobile)
  container.addEventListener("touchstart", (e) => {
    isDown = true;
    startX = e.touches[0].pageX - track.offsetLeft;
    track.style.transition = "none";
  }, { passive: true });

  window.addEventListener("touchend", () => {
    if (isDown) {
      isDown = false;
      track.style.transition = "";
      snapToNearestCard();
    }
  });

  window.addEventListener("touchmove", (e) => {
    if (!isDown) return;
    const x = e.touches[0].pageX - track.offsetLeft;
    const walk = (x - startX) * 1.5;
    currentTranslate = prevTranslate + walk;

    const cardWidth = getCardWidth();
    const minTranslate = -((visibleCardsCount + originalCards.length) * cardWidth);
    const maxTranslate = -(visibleCardsCount * cardWidth);

    if (currentTranslate > maxTranslate + 0.1 * cardWidth) {
      currentTranslate -= originalCards.length * cardWidth;
      startX -= originalCards.length * cardWidth;
    } else if (currentTranslate < minTranslate - 0.1 * cardWidth) {
      currentTranslate += originalCards.length * cardWidth;
      startX += originalCards.length * cardWidth;
    }

    track.style.transform = `translateX(${currentTranslate}px)`;
  });

  // Snapping Carousel Card
  const getCardWidth = () => {
    const card = track.querySelector(".social-card");
    return card ? card.offsetWidth + 30 : 300;
  };

  const snapToNearestCard = () => {
    const cardWidth = getCardWidth();
    let targetTranslate = Math.round(currentTranslate / cardWidth) * cardWidth;

    if (targetTranslate === prevTranslate) {
      adjustPosition();
      updateActiveVideo();
    } else {
      track.style.transform = `translateX(${targetTranslate}px)`;
      prevTranslate = targetTranslate;
      currentTranslate = targetTranslate;
    }
  };

  // Buttons navigation
  btnNext.addEventListener("click", () => {
    const cardWidth = getCardWidth();
    let newTranslate = prevTranslate - cardWidth;
    track.style.transform = `translateX(${newTranslate}px)`;
    prevTranslate = newTranslate;
    currentTranslate = newTranslate;
  });

  btnPrev.addEventListener("click", () => {
    const cardWidth = getCardWidth();
    let newTranslate = prevTranslate + cardWidth;
    track.style.transform = `translateX(${newTranslate}px)`;
    prevTranslate = newTranslate;
    currentTranslate = newTranslate;
  });

  // Position setup & auto play
  const setInitialPosition = () => {
    const cardWidth = getCardWidth();
    currentTranslate = -visibleCardsCount * cardWidth;
    prevTranslate = currentTranslate;
    track.style.transition = "none";
    track.style.transform = `translateX(${currentTranslate}px)`;
    track.offsetHeight; // force reflow
    track.style.transition = "";
    updateActiveVideo();
  };

  // Run initial sizing
  window.addEventListener("resize", setInitialPosition);

  setTimeout(setInitialPosition, 100);

  // Autoplay all videos after page load
  window.addEventListener("load", () => {
    setInitialPosition();
    setTimeout(playAllInlineVideos, 800);
  });
  if (document.readyState === "complete") {
    setInitialPosition();
    setTimeout(playAllInlineVideos, 800);
  }
}

/* 7. TESTIMONIAL CAROUSEL */
function initTestimonialCarousel() {
  const track = document.querySelector(".testimonial-track");
  const slides = document.querySelectorAll(".testimonial-slide");
  const btnPrev = document.querySelector(".testimonial-carousel .btn-prev");
  const btnNext = document.querySelector(".testimonial-carousel .btn-next");

  if (!track || slides.length === 0 || !btnPrev || !btnNext) return;

  let currentIndex = 0;

  const updateSlidePosition = () => {
    track.style.transform = `translateX(-${currentIndex * 100}%)`;
  };

  btnNext.addEventListener("click", () => {
    currentIndex = (currentIndex + 1) % slides.length;
    updateSlidePosition();
  });

  btnPrev.addEventListener("click", () => {
    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
    updateSlidePosition();
  });
}

/* 8. TREATMENT JOURNEY PROGRESS LINE */
function initJourneyProgress() {
  const steps = document.querySelectorAll(".journey-step");
  const progressLine = document.querySelector(".journey-timeline-progress");

  if (steps.length === 0 || !progressLine) return;

  const observerOptions = {
    root: null,
    threshold: 0.6,
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        // Highlight up to the current step on scroll
        const activeStep = entry.target;
        let activeIdx = 0;

        steps.forEach((step, idx) => {
          if (step === activeStep || idx <= activeIdx) {
            step.classList.add("active");
            activeIdx = idx;
          }
        });

        // Set progress width percentage (0% to 100%)
        const percentage = (activeIdx / (steps.length - 1)) * 90; // scale nicely inside bounds
        progressLine.style.width = `${5 + percentage}%`;
      }
    });
  }, observerOptions);

  steps.forEach((step) => observer.observe(step));
}

/* 9. LUXURY FAQ ACCORDION */
function initFAQAccordion() {
  const faqHeaders = document.querySelectorAll(".faq-header");

  faqHeaders.forEach((header) => {
    header.addEventListener("click", () => {
      const item = header.parentElement;
      const body = item.querySelector(".faq-body");

      // Close other FAQs if active
      document.querySelectorAll(".faq-item").forEach((otherItem) => {
        if (otherItem !== item && otherItem.classList.contains("active")) {
          otherItem.classList.remove("active");
          otherItem.querySelector(".faq-body").style.maxHeight = null;
        }
      });

      // Toggle current FAQ
      item.classList.toggle("active");
      if (item.classList.contains("active")) {
        body.style.maxHeight = `${body.scrollHeight}px`;
      } else {
        body.style.maxHeight = null;
      }
    });
  });
}

/* 10. CLINIC EXPERIENCE LIGHTBOX & CINEMATIC VIDEO */
function initExperienceLightbox() {
  const lightbox = document.createElement("div");
  lightbox.classList.add("lightbox-modal");
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

  const closeBtn = lightbox.querySelector(".lightbox-close-btn");
  const lightboxImg = lightbox.querySelector(".lightbox-img");
  const lightboxCaption = lightbox.querySelector(".lightbox-caption");

  // Open Lightbox for Images
  const galleryItems = document.querySelectorAll(
    ".experience-item:not(.item-video)",
  );
  galleryItems.forEach((item) => {
    item.addEventListener("click", () => {
      const img = item.querySelector("img");
      const captionText = item.querySelector(
        ".experience-hover-lbl",
      ).textContent;

      lightboxImg.src = img.src;
      lightboxCaption.textContent = captionText;

      lightbox.classList.add("active");
      document.body.style.overflow = "hidden"; // Lock scrolling
    });
  });

  // Open video placeholder for cinematic room
  const videoPlayBtn = document.querySelector(".cinematic-play-btn");
  if (videoPlayBtn) {
    videoPlayBtn.addEventListener("click", (e) => {
      e.stopPropagation();

      lightboxImg.src = "assets/images/hero_clinic.png"; // Mock video static shot
      lightboxCaption.textContent =
        "Luxury Aesthetic Clinic - Cinematic Tour (Playing Demo)";

      lightbox.classList.add("active");
      document.body.style.overflow = "hidden";
    });
  }

  // Close Lightbox
  const closeLightbox = () => {
    lightbox.classList.remove("active");
    document.body.style.overflow = "";
  };

  closeBtn.addEventListener("click", closeLightbox);
  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox) closeLightbox();
  });

  window.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeLightbox();
  });
}

/* 11. CONSULTATION FORM SUBMIT & INPUT LABEL EFFECT */
function initConsultationForm() {
  const form = document.getElementById("consultationForm");
  if (!form) return;

  const inputs = form.querySelectorAll(
    ".form-input, .form-textarea, .form-select",
  );

  // Input label interaction helpers
  inputs.forEach((input) => {
    const checkValue = () => {
      if (input.value !== "") {
        input.classList.add("has-value");
      } else {
        input.classList.remove("has-value");
      }
    };

    input.addEventListener("blur", checkValue);
    input.addEventListener("change", checkValue);

    // Check initial values (autofill, etc.)
    setTimeout(checkValue, 100);
  });

  // Submission handler
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const name = document.getElementById("formName").value;
    const phone = document.getElementById("formPhone").value;
    const concern = document.getElementById("formConcern").value;

    if (!name || !phone) {
      alert("Please fill out all mandatory fields.");
      return;
    }

    // Success styling and popup simulation
    const btn = form.querySelector(".form-btn-submit");
    const originalText = btn.textContent;

    btn.textContent = "Booking Confirmed...";
    btn.style.backgroundColor = "#128C7E";
    btn.style.borderColor = "#128C7E";

    alert(
      `Thank you, ${name}! Your consultation request for ${concern || "aesthetic treatment"} has been securely submitted. Dr. E. Reed's team will contact you shortly.`,
    );

    setTimeout(() => {
      form.reset();
      btn.textContent = originalText;
      btn.style.backgroundColor = "";
      btn.style.borderColor = "";

      // Reset inputs state
      inputs.forEach((input) => input.classList.remove("has-value"));
    }, 2000);
  });
}

