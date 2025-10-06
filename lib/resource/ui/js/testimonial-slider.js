$(document).ready(function () {
  if (!$(".testimonialslider").length) {
    const $track = $("#tTrack");
    if (!$track.length) return;

    const $slides = $track.children(".t-slide");
    const $dotsWrap = $("#tDots");
    const $prev = $("#tPrev");
    const $next = $("#tNext");

    let current = 0;
    let perView = 1;
    let slideW = 0;
    let autoplayTimer = null;
    const AUTOPLAY_MS = 5000;

    function computePerView() {
      const w = window.innerWidth || document.documentElement.clientWidth;
      if (w >= 1280) return 3; // xl and up
      if (w >= 768) return 2; // md and up
      return 1; // mobile
    }

    function layout() {
      perView = computePerView();
      const viewportW = $track.parent().innerWidth();
      slideW = viewportW / perView;

      // set width for each slide
      $slides.css("minWidth", slideW + "px");

      // clamp current so we don't overshoot
      const maxIndex = Math.max(0, $slides.length - perView);
      current = Math.min(current, maxIndex);

      update();
      buildDots();
    }

    function update() {
      const x = -(current * slideW);
      $track.css("transform", "translateX(" + x + "px)");
      highlightDots();
    }

    function next() {
      const maxIndex = Math.max(0, $slides.length - perView);
      current = current >= maxIndex ? 0 : current + 1;
      update();
    }

    function prev() {
      const maxIndex = Math.max(0, $slides.length - perView);
      current = current <= 0 ? maxIndex : current - 1;
      update();
    }

    function buildDots() {
      $dotsWrap.empty();
      const maxIndex = Math.max(0, $slides.length - perView);
      const total = maxIndex + 1; // number of positions
      for (let i = 0; i < total; i++) {
        const $d = $(
          '<button type="button" class="w-2.5 h-2.5 rounded-full bg-gray-300"></button>'
        );
        $d.on("click", function () {
          current = i;
          stopAutoplay();
          update();
          startAutoplay();
        });
        $dotsWrap.append($d);
      }
      highlightDots();
    }

    function highlightDots() {
      const $all = $dotsWrap.children("button");
      $all.removeClass("bg-primary").addClass("bg-gray-300");
      $all.eq(current).removeClass("bg-gray-300").addClass("bg-primary");
    }

    function startAutoplay() {
      stopAutoplay();
      autoplayTimer = setInterval(next, AUTOPLAY_MS);
    }

    function stopAutoplay() {
      if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
      }
    }

    // Controls
    $next.on("click", function () {
      stopAutoplay();
      next();
      startAutoplay();
    });

    $prev.on("click", function () {
      stopAutoplay();
      prev();
      startAutoplay();
    });

    // Pause on hover
    const $viewport = $track.parent();
    $viewport.on("mouseenter", stopAutoplay).on("mouseleave", startAutoplay);

    // Pause on touch-hold
    let touchHoldTimeout = null;
    $viewport.on("touchstart", function () {
      stopAutoplay();
      touchHoldTimeout = setTimeout(stopAutoplay, 200);
    });
    $viewport.on("touchend touchcancel", function () {
      if (touchHoldTimeout) clearTimeout(touchHoldTimeout);
      startAutoplay();
    });

    // Init
    layout();
    startAutoplay();

    // Re-layout on resize/orientation change
    $(window).on("resize", layout);
  }
});
