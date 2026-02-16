$(function () {
  const $slider = $(".testimonial-slider");
  const $track = $slider.find(".testimonial-track");
  const $slides = $track.find(".testimonial-slide");
  const $dotsWrap = $slider.find(".testimonial-dots");

  let index = 0;
  let timer;
  const INTERVAL = 4000;

  // Build dots
  $slides.each(function (i) {
    $("<button>")
      .on("click", function () {
        goTo(i, true);
      })
      .appendTo($dotsWrap);
  });
  const $dots = $dotsWrap.find("button");

  function updateUI() {
    $track.css("transform", "translateX(-" + index * 100 + "%)");
    $dots.removeClass("active").eq(index).addClass("active");
  }

  function goTo(i, manual) {
    index = (i + $slides.length) % $slides.length;
    updateUI();
    if (manual) restart();
  }

  function next() {
    goTo(index + 1);
  }
  function start() {
    if (!timer) timer = setInterval(next, INTERVAL);
  }
  function stop() {
    clearInterval(timer);
    timer = null;
  }
  function restart() {
    stop();
    start();
  }

  // Pause on hover/touch
  $slider.on("mouseenter", stop).on("mouseleave", start);
  $slider.on("touchstart", stop).on("touchend touchcancel", start);

  // Swipe
  let startX = 0;
  $slider.on("touchstart", (e) => {
    startX = e.originalEvent.touches[0].clientX;
  });
  $slider.on("touchend", (e) => {
    let deltaX = e.originalEvent.changedTouches[0].clientX - startX;
    if (deltaX > 50) goTo(index - 1, true);
    if (deltaX < -50) goTo(index + 1, true);
  });

  updateUI();
  start();

  const $fab = $("#openReviews");
  const $sheet = $("#reviewsSheet");
  const $backdrop = $("#closeReviewsBackdrop");
  const $close = $("#closeReviews");
  const $panel = $sheet.find(".reviews-sheet__panel");

  function openSheet() {
    $("body").addClass("no-scroll");
    $sheet.addClass("is-open").attr("aria-hidden", "false");
    $fab.attr("aria-expanded", "true");
    $panel.attr("tabindex", "-1").focus();
  }
  function closeSheet() {
    $("body").removeClass("no-scroll");
    $sheet.removeClass("is-open").attr("aria-hidden", "true");
    $fab.attr("aria-expanded", "false").focus();
  }

  $fab.on("click", openSheet);
  $close.on("click", closeSheet);
  $backdrop.on("click", closeSheet);

  // Escape key
  $(document).on("keydown", function (e) {
    if (e.key === "Escape" && $sheet.hasClass("is-open")) closeSheet();
  });

  // Optional: swipe down to close
  let startY = null,
    deltaY = 0;
  $panel.on("touchstart", function (e) {
    startY = e.originalEvent.touches[0].clientY;
    deltaY = 0;
  });
  $panel.on("touchmove", function (e) {
    if (startY === null) return;
    deltaY = e.originalEvent.touches[0].clientY - startY;
    // Only if at top and swiping down
    if ($panel.scrollTop() <= 0 && deltaY > 0) {
      e.preventDefault();
      // slight visual drag (optional)
      $panel.css("transform", "translateY(" + Math.min(deltaY, 80) + "px)");
    }
  });
  $panel.on("touchend touchcancel", function () {
    $panel.css("transform", "");
    if (deltaY > 70) closeSheet();
    startY = null;
    deltaY = 0;
  });
});
