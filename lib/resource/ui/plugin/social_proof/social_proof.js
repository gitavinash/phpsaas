/* 
========================================================================
   SOCIAL PROOF PLUGIN JAVASCRIPT
   Description: Infinite loop carousel slider, custom controls,
                and details reference drawer popup.
========================================================================
*/

document.addEventListener("DOMContentLoaded", () => {
  initSocialCarousel();
});

function initSocialCarousel() {
  const container = document.querySelector(".carousel-container");
  const track = document.querySelector(".carousel-track");
  const btnPrev = document.querySelector(".social-proof-section .btn-prev");
  const btnNext = document.querySelector(".social-proof-section .btn-next");

  if (!container || !track || !btnPrev || !btnNext) return;

  const originalCards = Array.from(track.querySelectorAll(".social-card"));
  if (originalCards.length === 0) return;

  const visibleCardsCount = 4; // Max visible on desktop
  const isSingleCard = originalCards.length <= 1;
  const clonedCount = isSingleCard ? 0 : Math.min(originalCards.length, visibleCardsCount);

  // Tag original cards with their original indices
  originalCards.forEach((card, idx) => {
    card.setAttribute("data-index", idx);
    const img = card.querySelector(".social-video-img");
    const thumbContainer = card.querySelector(".social-video-thumb");
    if (img) img.style.cursor = "pointer";
    if (thumbContainer) thumbContainer.style.cursor = "pointer";
  });

  if (isSingleCard) {
    if (btnPrev) btnPrev.style.display = "none";
    if (btnNext) btnNext.style.display = "none";
    track.style.justifyContent = "center";
  } else {
    // Clone cards for infinite loop
    const startClones = originalCards.slice(0, clonedCount).map(el => el.cloneNode(true));
    const endClones = originalCards.slice(-clonedCount).map(el => el.cloneNode(true));

    // Prepend and Append clones (reverse endClones so order is correct when inserting at firstChild)
    endClones.reverse().forEach(clone => {
      track.insertBefore(clone, track.firstChild);
    });
    startClones.forEach(clone => {
      track.appendChild(clone);
    });
  }

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
      return `/upload/${window.location.hostname}/social_proof/${id}.mp4`;
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
      background: var(--color-primary-gold, #C5A880) !important;
      border-color: var(--color-primary-gold, #C5A880) !important;
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
          font-family: var(--font-serif, Georgia, serif);
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

  let observerInitialized = false;
  const setupVideoObserver = () => {
    if (observerInitialized) return;
    observerInitialized = true;

    if (typeof IntersectionObserver === "undefined") {
      getAllCards().forEach(card => {
        const video = card.querySelector("video");
        if (video) {
          if (video.getAttribute("data-src") && !video.src) {
            video.src = video.getAttribute("data-src");
          }
          video.dataset.isIntersecting = "true";
          video.play().catch(() => {});
        }
      });
      return;
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        const card = entry.target;
        const video = card.querySelector("video");
        const poster = card.querySelector(".social-video-poster");

        if (video) {
          if (entry.isIntersecting) {
            video.dataset.isIntersecting = "true";
            if (video.getAttribute("data-src") && !video.src) {
              video.src = video.getAttribute("data-src");
              video.load();
            }

            const handlePlaying = () => {
              if (poster) poster.style.opacity = "0";
              video.style.opacity = "1";
            };

            video.addEventListener("playing", handlePlaying);
            if (!video.paused) {
              handlePlaying();
            }

            if (modal.style.display !== "flex") {
              video.play().catch(() => {});
            }
          } else {
            video.dataset.isIntersecting = "false";
            video.pause();
          }
        }
      });
    }, {
      threshold: 0.15
    });

    getAllCards().forEach(card => observer.observe(card));
  };

  const playAllInlineVideos = () => {
    if (modal.style.display === "flex") return;
    getAllCards().forEach(card => {
      const video = card.querySelector("video");
      if (video && video.dataset.isIntersecting === "true") {
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
    if (isSingleCard) return;
    const cardWidth = getCardWidth();
    const minTranslate = -((clonedCount + originalCards.length) * cardWidth);
    const maxTranslate = -(clonedCount * cardWidth);

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
    if (isSingleCard) return;
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
    if (!isDown || isSingleCard) return;
    e.preventDefault();
    const x = e.pageX - track.offsetLeft;
    const walk = (x - startX) * 1.5;
    currentTranslate = prevTranslate + walk;

    const cardWidth = getCardWidth();
    const minTranslate = -((clonedCount + originalCards.length) * cardWidth);
    const maxTranslate = -(clonedCount * cardWidth);

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
  let touchStartX = 0;
  let touchStartY = 0;
  let isScrolling = false;
  let isDragging = false;

  container.addEventListener("touchstart", (e) => {
    if (isSingleCard) return;
    isDown = true;
    touchStartX = e.touches[0].pageX;
    touchStartY = e.touches[0].pageY;
    isScrolling = false;
    isDragging = false;
    startX = e.touches[0].pageX - track.offsetLeft;
    track.style.transition = "none";
  }, { passive: false });

  window.addEventListener("touchend", () => {
    if (isDown) {
      isDown = false;
      track.style.transition = "";
      snapToNearestCard();
    }
  });

  window.addEventListener("touchmove", (e) => {
    if (!isDown || isSingleCard) return;

    const currentX = e.touches[0].pageX;
    const currentY = e.touches[0].pageY;
    const deltaX = Math.abs(currentX - touchStartX);
    const deltaY = Math.abs(currentY - touchStartY);

    if (!isScrolling && !isDragging) {
      if (deltaX > deltaY && deltaX > 8) {
        isDragging = true;
      } else if (deltaY > deltaX && deltaY > 8) {
        isScrolling = true;
      }
    }

    if (isScrolling) {
      isDown = false;
      return;
    }

    if (isDragging) {
      if (e.cancelable) e.preventDefault();

      const x = currentX - track.offsetLeft;
      const walk = (x - startX) * 1.5;
      currentTranslate = prevTranslate + walk;

      const cardWidth = getCardWidth();
      const minTranslate = -((clonedCount + originalCards.length) * cardWidth);
      const maxTranslate = -(clonedCount * cardWidth);

      if (currentTranslate > maxTranslate + 0.1 * cardWidth) {
        currentTranslate -= originalCards.length * cardWidth;
        startX -= originalCards.length * cardWidth;
      } else if (currentTranslate < minTranslate - 0.1 * cardWidth) {
        currentTranslate += originalCards.length * cardWidth;
        startX += originalCards.length * cardWidth;
      }

      track.style.transform = `translateX(${currentTranslate}px)`;
    }
  }, { passive: false });

  // Snapping Carousel Card
  const getCardWidth = () => {
    const card = track.querySelector(".social-card");
    return card ? card.offsetWidth + 30 : 300;
  };

  const snapToNearestCard = () => {
    if (isSingleCard) return;
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
    if (isSingleCard) return;
    const cardWidth = getCardWidth();
    let newTranslate = prevTranslate - cardWidth;
    track.style.transform = `translateX(${newTranslate}px)`;
    prevTranslate = newTranslate;
    currentTranslate = newTranslate;
  });

  btnPrev.addEventListener("click", () => {
    if (isSingleCard) return;
    const cardWidth = getCardWidth();
    let newTranslate = prevTranslate + cardWidth;
    track.style.transform = `translateX(${newTranslate}px)`;
    prevTranslate = newTranslate;
    currentTranslate = newTranslate;
  });

  // Position setup & auto play
  const setInitialPosition = () => {
    if (isSingleCard) {
      currentTranslate = 0;
      prevTranslate = 0;
      track.style.transition = "none";
      track.style.transform = `translateX(0px)`;
      track.offsetHeight; // force reflow
      track.style.transition = "";
      updateActiveVideo();
      return;
    }
    const cardWidth = getCardWidth();
    currentTranslate = -clonedCount * cardWidth;
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

  // Set up position & observer after page load
  window.addEventListener("load", () => {
    setInitialPosition();
    setupVideoObserver();
  });
  if (document.readyState === "complete") {
    setInitialPosition();
    setupVideoObserver();
  }
}
