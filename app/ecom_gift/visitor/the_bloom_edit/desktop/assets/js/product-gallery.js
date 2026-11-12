/**
 * Luxury Product Image Gallery & Magnified Inspection
 * Supports:
 * - Data-attribute gallery JSON or inline script fallback + thumbnail DOM collection
 * - Desktop hover lens with high-magnification flyout zoom window
 * - Mobile touch swipe gestures & full-screen accessible lightbox with focus management
 */
(function () {
  'use strict';

  function initProductGallery() {
    var mainContainer = document.getElementById('productMainContainer');
    var mainImg = document.getElementById('mainProductImage');
    var ambientBg = document.getElementById('productAmbientBg');
    var zoomLens = document.getElementById('zoomLens');
    var zoomFlyout = document.getElementById('zoomFlyout');
    var zoomFlyoutImg = document.getElementById('zoomFlyoutImg');
    var counterBadge = document.getElementById('galleryCounter');
    var prevBtn = document.getElementById('galleryPrevBtn');
    var nextBtn = document.getElementById('galleryNextBtn');

    // Lightbox elements
    var lightbox = document.getElementById('productLightboxModal');
    var lightboxImg = document.getElementById('lightboxImage');
    var lightboxCounter = document.getElementById('lightboxCounter');
    var lightboxCloseBtn = document.getElementById('lightboxCloseBtn');
    var lightboxPrevBtn = document.getElementById('lightboxPrevBtn');
    var lightboxNextBtn = document.getElementById('lightboxNextBtn');
    var lightboxZoomIn = document.getElementById('lightboxZoomIn');
    var lightboxZoomOut = document.getElementById('lightboxZoomOut');
    var lightboxZoomReset = document.getElementById('lightboxZoomReset');
    var lightboxViewport = document.getElementById('lightboxViewport');
    var lightboxThumbnailsStrip = document.getElementById('lightboxThumbnailsStrip');

    if (!mainContainer || !mainImg) return;

    var galleryImages = [];
    if (Array.isArray(window.PRODUCT_GALLERY_IMAGES) && window.PRODUCT_GALLERY_IMAGES.length) {
      galleryImages = window.PRODUCT_GALLERY_IMAGES;
    } else if (mainContainer.getAttribute('data-gallery-images')) {
      try {
        var parsed = JSON.parse(mainContainer.getAttribute('data-gallery-images'));
        if (Array.isArray(parsed)) {
          galleryImages = parsed;
        }
      } catch (_) {
        galleryImages = [];
      }
    }

    if (!galleryImages.length) {
      var thumbElements = document.querySelectorAll('.thumbnail-item');
      if (thumbElements.length) {
        thumbElements.forEach(function (el) {
          var thumbImg = el.querySelector('img');
          galleryImages.push({
            path: el.getAttribute('data-src') || (thumbImg ? thumbImg.getAttribute('src') : ''),
            title: el.getAttribute('data-title') || mainImg.getAttribute('alt') || 'Product Image'
          });
        });
      } else {
        galleryImages.push({
          path: mainImg.getAttribute('src'),
          title: mainImg.getAttribute('alt') || 'Product Image'
        });
      }
    }

    var currentIndex = 0;
    var totalImages = galleryImages.length;
    var MAGNIFICATION = 2.5;
    var lastFocusedBeforeLightbox = null;

    // Preload gallery images gently
    galleryImages.forEach(function (item) {
      if (item && item.path) {
        var pre = new Image();
        pre.src = item.path;
      }
    });

    if (ambientBg && mainImg) {
      ambientBg.style.backgroundImage = 'url("' + (mainImg.getAttribute('src') || '') + '")';
    }

    function setActiveImage(index, skipScroll) {
      if (index < 0) index = totalImages - 1;
      if (index >= totalImages) index = 0;
      currentIndex = index;

      var currentItem = galleryImages[currentIndex];
      var newSrc = currentItem ? currentItem.path : mainImg.getAttribute('src');

      mainImg.setAttribute('src', newSrc);

      if (ambientBg) {
        ambientBg.style.backgroundImage = 'url("' + newSrc + '")';
      }

      if (zoomFlyoutImg) {
        zoomFlyoutImg.setAttribute('src', newSrc);
      }

      if (counterBadge) {
        counterBadge.textContent = (currentIndex + 1) + ' / ' + totalImages;
      }

      var thumbItems = document.querySelectorAll('.thumbnail-item');
      thumbItems.forEach(function (thumb, idx) {
        var active = idx === currentIndex;
        thumb.setAttribute('aria-pressed', active ? 'true' : 'false');
        if (active) {
          thumb.classList.remove('border-gray-200', 'border-[#E6DDD3]', 'hover:border-gray-300', 'hover:border-[#B48E54]');
          thumb.classList.add('border-[#183028]', 'ring-2', 'ring-[#183028]/25');
          if (!skipScroll && thumb.scrollIntoView) {
            thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
          }
        } else {
          thumb.classList.remove('border-forest', 'border-primary', 'border-[#183028]', 'ring-2', 'ring-primary/40', 'ring-[#183028]/25');
          thumb.classList.add('border-[#E6DDD3]', 'hover:border-[#B48E54]');
        }
      });

      if (lightbox && !lightbox.classList.contains('hidden')) {
        updateLightboxImage();
      }
    }

    // Desktop Hover Lens & Flyout Zoom
    var isHovering = false;
    var displayedW = 0;
    var displayedH = 0;
    var offsetX = 0;
    var offsetY = 0;

    function handleMouseEnter(e) {
      if (window.innerWidth < 768) return;
      isHovering = true;

      var currentSrc = galleryImages[currentIndex] ? galleryImages[currentIndex].path : mainImg.getAttribute('src');

      if (zoomFlyout && zoomLens) {
        var rect = mainContainer.getBoundingClientRect();
        zoomLens.classList.remove('hidden');
        zoomFlyout.classList.remove('hidden');
        zoomFlyout.classList.add('is-active');

        zoomFlyout.style.width = rect.width + 'px';
        zoomFlyout.style.height = rect.height + 'px';

        var imgNaturalW = mainImg.naturalWidth || rect.width;
        var imgNaturalH = mainImg.naturalHeight || rect.height;
        var imgRatio = imgNaturalW / imgNaturalH;
        var containerRatio = rect.width / rect.height;

        if (imgRatio > containerRatio) {
          displayedH = rect.height;
          displayedW = rect.height * imgRatio;
          offsetX = (displayedW - rect.width) / 2;
          offsetY = 0;
        } else {
          displayedW = rect.width;
          displayedH = rect.width / imgRatio;
          offsetX = 0;
          offsetY = (displayedH - rect.height) / 2;
        }

        if (zoomFlyoutImg) {
          zoomFlyoutImg.setAttribute('src', currentSrc);
          zoomFlyoutImg.style.width = (displayedW * MAGNIFICATION) + 'px';
          zoomFlyoutImg.style.height = (displayedH * MAGNIFICATION) + 'px';
        }
        updateLensPosition(e);
      }
    }

    function updateLensPosition(e) {
      if (!zoomLens || !zoomFlyout) return;

      var rect = mainContainer.getBoundingClientRect();
      var x = e.clientX - rect.left;
      var y = e.clientY - rect.top;

      var lensW = rect.width / MAGNIFICATION;
      var lensH = rect.height / MAGNIFICATION;

      zoomLens.style.width = lensW + 'px';
      zoomLens.style.height = lensH + 'px';

      var lensX = Math.max(0, Math.min(rect.width - lensW, x - lensW / 2));
      var lensY = Math.max(0, Math.min(rect.height - lensH, y - lensH / 2));

      zoomLens.style.left = lensX + 'px';
      zoomLens.style.top = lensY + 'px';

      if (zoomFlyoutImg) {
        var relX = (lensX + offsetX) * MAGNIFICATION;
        var relY = (lensY + offsetY) * MAGNIFICATION;
        zoomFlyoutImg.style.left = (-relX) + 'px';
        zoomFlyoutImg.style.top = (-relY) + 'px';
      }
    }

    function handleMouseMove(e) {
      if (!isHovering || window.innerWidth < 768) return;
      updateLensPosition(e);
    }

    function handleMouseLeave() {
      isHovering = false;
      if (zoomLens) zoomLens.classList.add('hidden');
      if (zoomFlyout) {
        zoomFlyout.classList.remove('is-active');
        zoomFlyout.classList.add('hidden');
      }
    }

    mainContainer.addEventListener('mouseenter', handleMouseEnter);
    mainContainer.addEventListener('mousemove', handleMouseMove);
    mainContainer.addEventListener('mouseleave', handleMouseLeave);

    if (zoomFlyout) {
      zoomFlyout.addEventListener('click', openLightbox);
    }

    // Thumbnails: Hover, Click & Keyboard
    var thumbElements = document.querySelectorAll('.thumbnail-item');
    var thumbHoverTimeout = null;

    thumbElements.forEach(function (thumb, index) {
      thumb.addEventListener('mouseenter', function () {
        if (window.innerWidth >= 768) {
          clearTimeout(thumbHoverTimeout);
          thumbHoverTimeout = setTimeout(function () {
            setActiveImage(index);
          }, 40);
        }
      });

      thumb.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        setActiveImage(index);
      });

      thumb.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          setActiveImage(index);
        }
      });
    });

    if (prevBtn) {
      prevBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        setActiveImage(currentIndex - 1);
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        setActiveImage(currentIndex + 1);
      });
    }

    // Mobile Touch Swipe
    var touchStartX = 0;
    var touchStartY = 0;
    var touchStartTime = 0;
    var isSwiping = false;
    var lastTouchHandledAt = 0;

    mainContainer.addEventListener('touchstart', function (e) {
      if (e.target.closest('.gallery-nav-btn')) {
        isSwiping = false;
        return;
      }
      if (e.touches.length === 1) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        touchStartTime = Date.now();
        isSwiping = true;
      }
    }, { passive: true });

    mainContainer.addEventListener('touchend', function (e) {
      if (!isSwiping || e.changedTouches.length !== 1) return;
      isSwiping = false;

      if (e.target.closest('.gallery-nav-btn')) return;

      var deltaX = e.changedTouches[0].clientX - touchStartX;
      var deltaY = e.changedTouches[0].clientY - touchStartY;
      var duration = Date.now() - touchStartTime;

      if (Math.abs(deltaX) > 35 && Math.abs(deltaX) > Math.abs(deltaY) && duration < 600) {
        lastTouchHandledAt = Date.now();
        setActiveImage(deltaX < 0 ? currentIndex + 1 : currentIndex - 1);
      } else if (Math.abs(deltaX) < 10 && Math.abs(deltaY) < 10 && duration < 300) {
        lastTouchHandledAt = Date.now();
        openLightbox();
      }
    });

    mainContainer.addEventListener('click', function (e) {
      if (e.target.closest('.gallery-nav-btn')) return;
      if (Date.now() - lastTouchHandledAt < 500) return;
      openLightbox();
    });

    // Fullscreen Lightbox Modal
    var lbZoom = 1;
    var lbPanX = 0;
    var lbPanY = 0;
    var isDragging = false;
    var dragStartX = 0;
    var dragStartY = 0;

    function openLightbox() {
      if (!lightbox) return;
      lastFocusedBeforeLightbox = document.activeElement;
      handleMouseLeave();
      lightbox.classList.remove('hidden');
      lightbox.classList.add('flex');
      document.body.style.overflow = 'hidden';
      resetLightboxZoom();
      updateLightboxImage();
      if (lightboxCloseBtn) {
        setTimeout(function () {
          lightboxCloseBtn.focus();
        }, 30);
      }
    }

    function closeLightbox() {
      if (!lightbox) return;
      lightbox.classList.add('hidden');
      lightbox.classList.remove('flex');
      document.body.style.overflow = '';
      resetLightboxZoom();
      if (lastFocusedBeforeLightbox && typeof lastFocusedBeforeLightbox.focus === 'function') {
        lastFocusedBeforeLightbox.focus();
      }
    }

    function resetLightboxZoom() {
      lbZoom = 1;
      lbPanX = 0;
      lbPanY = 0;
      applyLightboxTransform();
    }

    function setLightboxZoom(newZoom) {
      lbZoom = Math.max(1, Math.min(4, newZoom));
      if (lbZoom === 1) {
        lbPanX = 0;
        lbPanY = 0;
      }
      applyLightboxTransform();
    }

    function applyLightboxTransform() {
      if (!lightboxImg) return;
      lightboxImg.style.transform = 'translate(' + lbPanX + 'px, ' + lbPanY + 'px) scale(' + lbZoom + ')';
      if (lightboxViewport) {
        lightboxViewport.style.cursor = lbZoom > 1 ? (isDragging ? 'grabbing' : 'grab') : 'zoom-in';
      }
    }

    function updateLightboxImage() {
      if (!lightboxImg) return;
      var currentItem = galleryImages[currentIndex];
      var newSrc = currentItem ? currentItem.path : mainImg.getAttribute('src');

      lightboxImg.setAttribute('src', newSrc);

      if (lightboxCounter) {
        lightboxCounter.textContent = (currentIndex + 1) + ' of ' + totalImages;
      }

      if (lightboxThumbnailsStrip) {
        var thumbs = lightboxThumbnailsStrip.querySelectorAll('.lightbox-thumb-item');
        thumbs.forEach(function (thumb, idx) {
          if (idx === currentIndex) {
            thumb.classList.remove('border-white/20', 'opacity-60');
            thumb.classList.add('border-[#B48E54]', 'ring-2', 'ring-[#B48E54]', 'opacity-100');
            if (thumb.scrollIntoView) {
              thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
          } else {
            thumb.classList.remove('border-primary', 'ring-primary', 'border-[#B48E54]', 'ring-2', 'ring-[#B48E54]', 'opacity-100');
            thumb.classList.add('border-white/20', 'opacity-60');
          }
        });
      }
    }

    if (lightboxCloseBtn) {
      lightboxCloseBtn.addEventListener('click', closeLightbox);
    }

    if (lightboxPrevBtn) {
      lightboxPrevBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        resetLightboxZoom();
        setActiveImage(currentIndex - 1);
      });
    }

    if (lightboxNextBtn) {
      lightboxNextBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        resetLightboxZoom();
        setActiveImage(currentIndex + 1);
      });
    }

    if (lightboxZoomIn) {
      lightboxZoomIn.addEventListener('click', function (e) {
        e.stopPropagation();
        setLightboxZoom(lbZoom + 0.5);
      });
    }

    if (lightboxZoomOut) {
      lightboxZoomOut.addEventListener('click', function (e) {
        e.stopPropagation();
        setLightboxZoom(lbZoom - 0.5);
      });
    }

    if (lightboxZoomReset) {
      lightboxZoomReset.addEventListener('click', function (e) {
        e.stopPropagation();
        resetLightboxZoom();
      });
    }

    if (lightboxThumbnailsStrip) {
      lightboxThumbnailsStrip.addEventListener('click', function (e) {
        var thumb = e.target.closest('.lightbox-thumb-item');
        if (thumb) {
          var idx = parseInt(thumb.getAttribute('data-index'), 10);
          if (!isNaN(idx)) {
            resetLightboxZoom();
            setActiveImage(idx);
          }
        }
      });
    }

    var lastTap = 0;
    var lbTouchStartX = 0;
    var lbTouchStartY = 0;
    var lbTouchStartTime = 0;
    var isLbSwiping = false;

    if (lightboxViewport) {
      lightboxViewport.addEventListener('dblclick', function (e) {
        e.preventDefault();
        setLightboxZoom(lbZoom > 1 ? 1 : 2.5);
      });

      lightboxViewport.addEventListener('mousedown', function (e) {
        if (lbZoom <= 1) return;
        isDragging = true;
        dragStartX = e.clientX - lbPanX;
        dragStartY = e.clientY - lbPanY;
        applyLightboxTransform();
      });

      window.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        lbPanX = e.clientX - dragStartX;
        lbPanY = e.clientY - dragStartY;
        applyLightboxTransform();
      });

      window.addEventListener('mouseup', function () {
        if (isDragging) {
          isDragging = false;
          applyLightboxTransform();
        }
      });

      lightboxViewport.addEventListener('touchstart', function (e) {
        var now = Date.now();
        if (now - lastTap < 300) {
          e.preventDefault();
          isLbSwiping = false;
          setLightboxZoom(lbZoom > 1 ? 1 : 2.5);
        } else if (e.touches.length === 1 && lbZoom > 1) {
          isDragging = true;
          isLbSwiping = false;
          dragStartX = e.touches[0].clientX - lbPanX;
          dragStartY = e.touches[0].clientY - lbPanY;
        } else if (e.touches.length === 1 && lbZoom <= 1) {
          lbTouchStartX = e.touches[0].clientX;
          lbTouchStartY = e.touches[0].clientY;
          lbTouchStartTime = now;
          isLbSwiping = true;
        }
        lastTap = now;
      }, { passive: false });

      lightboxViewport.addEventListener('touchmove', function (e) {
        if (!isDragging || e.touches.length !== 1 || lbZoom <= 1) return;
        e.preventDefault();
        lbPanX = e.touches[0].clientX - dragStartX;
        lbPanY = e.touches[0].clientY - dragStartY;
        applyLightboxTransform();
      }, { passive: false });

      lightboxViewport.addEventListener('touchend', function (e) {
        isDragging = false;
        if (isLbSwiping && lbZoom <= 1 && e.changedTouches && e.changedTouches.length === 1) {
          isLbSwiping = false;
          var dx = e.changedTouches[0].clientX - lbTouchStartX;
          var dy = e.changedTouches[0].clientY - lbTouchStartY;
          var elapsed = Date.now() - lbTouchStartTime;
          if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy) && elapsed < 600) {
            resetLightboxZoom();
            setActiveImage(dx < 0 ? currentIndex + 1 : currentIndex - 1);
          }
        }
      });
    }

    window.addEventListener('keydown', function (e) {
      if (!lightbox || lightbox.classList.contains('hidden')) return;

      if (e.key === 'Escape') {
        closeLightbox();
      } else if (e.key === 'ArrowLeft') {
        resetLightboxZoom();
        setActiveImage(currentIndex - 1);
      } else if (e.key === 'ArrowRight') {
        resetLightboxZoom();
        setActiveImage(currentIndex + 1);
      } else if (e.key === '+' || e.key === '=') {
        setLightboxZoom(lbZoom + 0.5);
      } else if (e.key === '-') {
        setLightboxZoom(lbZoom - 0.5);
      }
    });

    if (lightbox) {
      lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox || e.target.id === 'lightboxStage' || e.target.id === 'lightboxViewport') {
          if (lbZoom <= 1) {
            closeLightbox();
          }
        }
      });
    }

    setActiveImage(0, true);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProductGallery);
  } else {
    initProductGallery();
  }
})();
