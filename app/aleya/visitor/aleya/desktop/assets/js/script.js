/**
 * ALEYA — Fresh Modern Website
 * Interactive JavaScript
 */

document.addEventListener("DOMContentLoaded", function () {
  initCursorGlow();
  initNavigation();
  initMobileMenu();
  initSmoothScroll();
  initPricingToggle();
  initComparisonToggle();
  initFAQ();
  initScrollReveal();
  initCookieBanner();
  initFormHandling();
});

/**
 * Cursor Glow Effect
 * Creates a subtle glow that follows the cursor
 */
function initCursorGlow() {
  const glow = document.getElementById("cursorGlow");
  if (!glow) return;

  // Only on desktop
  if (window.innerWidth < 1024) {
    glow.style.display = "none";
    return;
  }

  let mouseX = 0,
    mouseY = 0;
  let currentX = 0,
    currentY = 0;

  document.addEventListener("mousemove", (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
  });

  function animate() {
    // Smooth follow
    currentX += (mouseX - currentX) * 0.08;
    currentY += (mouseY - currentY) * 0.08;

    glow.style.left = currentX + "px";
    glow.style.top = currentY + "px";

    requestAnimationFrame(animate);
  }

  animate();
}

/**
 * Navigation Scroll Effect
 * Adds background blur on scroll
 */
function initNavigation() {
  const nav = document.getElementById("nav");
  if (!nav) return;

  let lastScroll = 0;

  window.addEventListener(
    "scroll",
    () => {
      const currentScroll = window.pageYOffset;

      if (currentScroll > 50) {
        nav.classList.add("scrolled");
      } else {
        nav.classList.remove("scrolled");
      }

      lastScroll = currentScroll;
    },
    { passive: true },
  );
}

/**
 * Mobile Menu Toggle
 */
function initMobileMenu() {
  const toggle = document.getElementById("navToggle");
  const menu = document.getElementById("navMenu");

  if (!toggle || !menu) return;

  toggle.addEventListener("click", () => {
    toggle.classList.toggle("active");
    menu.classList.toggle("open");
    document.body.classList.toggle("menu-open");
  });

  // Close menu when clicking a link
  menu.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      toggle.classList.remove("active");
      menu.classList.remove("open");
      document.body.classList.remove("menu-open");
    });
  });
}

/**
 * Smooth Scroll for Anchor Links
 */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const targetId = this.getAttribute("href");

      if (targetId === "#") return;

      const target = document.querySelector(targetId);
      if (!target) return;

      e.preventDefault();

      const navHeight = document.getElementById("nav")?.offsetHeight || 80;
      const targetPosition =
        target.getBoundingClientRect().top + window.pageYOffset - navHeight;

      window.scrollTo({
        top: targetPosition,
        behavior: "smooth",
      });
    });
  });
}

/**
 * Pricing Toggle (Monthly/Yearly)
 */
function initPricingToggle() {
  const toggle = document.getElementById("pricingToggle");
  const labels = document.querySelectorAll(".toggle-label");
  const amounts = document.querySelectorAll(".pricing-card .amount");
  const periods = document.querySelectorAll(".pricing-card .period");

  if (!toggle) return;

  // Keep monthly values aligned to yearly base (+25%) even if markup changes.
  amounts.forEach((amount) => {
    const yearlyNumber = parseInt(
      (amount.dataset.yearly || "0").replace(/,/g, ""),
      10,
    );
    if (!Number.isNaN(yearlyNumber) && yearlyNumber > 0) {
      const monthlyNumber = Math.round(yearlyNumber * 1.25);
      amount.dataset.monthly = String(monthlyNumber);
      amount.dataset.yearly = String(yearlyNumber);
    }
  });

  const updatePricingView = (isYearly) => {
    // Update label styles
    labels.forEach((label) => {
      const period = label.dataset.period;
      if (
        (isYearly && period === "yearly") ||
        (!isYearly && period === "monthly")
      ) {
        label.classList.add("active");
      } else {
        label.classList.remove("active");
      }
    });

    // Update prices (with formatted numbers)
    amounts.forEach((amount) => {
      const monthly = amount.dataset.monthly;
      const yearly = amount.dataset.yearly;
      amount.textContent = isYearly ? yearly : monthly;
    });

    // Update period labels (/mo or /yr)
    periods.forEach((period) => {
      const monthlyText = period.dataset.monthly || "/mo";
      const yearlyText = period.dataset.yearly || "/yr";
      period.textContent = isYearly ? yearlyText : monthlyText;
    });
  };

  toggle.addEventListener("change", () => {
    updatePricingView(toggle.checked);
  });

  // Apply correct state on first load (yearly is default via checked toggle).
  updatePricingView(toggle.checked);
}

/**
 * Feature Comparison Toggle
 */
function initComparisonToggle() {
  const toggle = document.getElementById("comparisonToggle");
  const table = document.getElementById("comparisonTable");

  if (!toggle || !table) return;

  toggle.addEventListener("click", () => {
    toggle.classList.toggle("active");
    table.classList.toggle("active");

    // Update button text
    const span = toggle.querySelector("span");
    if (table.classList.contains("active")) {
      span.textContent = "Hide comparison";
    } else {
      span.textContent = "Compare all features";
    }
  });
}

/**
 * Animate number value
 */
function animateValue(element, start, end, duration) {
  const range = end - start;
  const startTime = performance.now();

  function update(currentTime) {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);

    // Ease out cubic
    const easeProgress = 1 - Math.pow(1 - progress, 3);
    const current = Math.round(start + range * easeProgress);

    element.textContent = current;

    if (progress < 1) {
      requestAnimationFrame(update);
    }
  }

  requestAnimationFrame(update);
}

/**
 * FAQ Accordion
 */
function initFAQ() {
  const items = document.querySelectorAll(".faq-item");

  items.forEach((item) => {
    const question = item.querySelector(".faq-q");

    question.addEventListener("click", () => {
      const isOpen = item.classList.contains("open");

      // Close all
      items.forEach((i) => i.classList.remove("open"));

      // Open clicked if it was closed
      if (!isOpen) {
        item.classList.add("open");
      }
    });
  });
}

/**
 * Scroll Reveal Animations
 */
function initScrollReveal() {
  const reveals = document.querySelectorAll(
    ".section-header, .problem-text, .problem-visual, .feature-card, " +
      ".integration-card, .timeline-item, .pricing-card, .testimonial-card, .faq-item, .cta-content",
  );

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("revealed");
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    },
  );

  reveals.forEach((el) => {
    el.classList.add("reveal");
    observer.observe(el);
  });

  // Add reveal styles dynamically
  const style = document.createElement("style");
  style.textContent = `
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }
        .feature-card.reveal { transition-delay: calc(var(--i, 0) * 0.1s); }
    `;
  document.head.appendChild(style);

  // Add stagger delay to feature cards
  document.querySelectorAll(".feature-card").forEach((card, i) => {
    card.style.setProperty("--i", i);
  });
}

/**
 * Cookie Consent Banner
 */
function initCookieBanner() {
  const banner = document.getElementById("cookieBanner");
  if (!banner) return;

  // Check if already accepted
  if (localStorage.getItem("cookiesAccepted")) {
    banner.remove();
    return;
  }

  // Show banner after delay
  setTimeout(() => {
    banner.classList.add("show");
  }, 2000);
}

/**
 * Accept Cookies (Global function for inline onclick)
 */
window.acceptCookies = function () {
  const banner = document.getElementById("cookieBanner");
  if (banner) {
    banner.classList.remove("show");
    localStorage.setItem("cookiesAccepted", "true");
    setTimeout(() => banner.remove(), 500);
  }
};

/**
 * Form Handling
 */
function initFormHandling() {
  const ctaForm = document.getElementById("ctaForm");

  if (ctaForm) {
    ctaForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const email = this.querySelector('input[type="email"]').value;

      if (validateEmail(email)) {
        // Track conversion
        trackConversion("cta_signup");

        // Redirect to registration
        window.location.href = `https://aleya.in/register/?email=${encodeURIComponent(email)}`;
      }
    });
  }
}

/**
 * Email Validation
 */
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

/**
 * Google Ads Conversion Tracking
 */
function trackConversion(label) {
  // Google Ads gtag
  if (typeof gtag !== "undefined") {
    gtag("event", "conversion", {
      send_to: "AW-CONVERSION_ID/" + label,
    });
  }

  // Console log for debugging
  console.log("Conversion tracked:", label);
}

/**
 * Horizontal scroll for features on mobile
 */
document.addEventListener("DOMContentLoaded", () => {
  const featuresScroll = document.querySelector(".features-scroll");

  if (featuresScroll) {
    // Add scroll indicators
    let isDown = false;
    let startX;
    let scrollLeft;

    featuresScroll.addEventListener("mousedown", (e) => {
      isDown = true;
      startX = e.pageX - featuresScroll.offsetLeft;
      scrollLeft = featuresScroll.scrollLeft;
    });

    featuresScroll.addEventListener("mouseleave", () => {
      isDown = false;
    });

    featuresScroll.addEventListener("mouseup", () => {
      isDown = false;
    });

    featuresScroll.addEventListener("mousemove", (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - featuresScroll.offsetLeft;
      const walk = (x - startX) * 2;
      featuresScroll.scrollLeft = scrollLeft - walk;
    });
  }
});

/**
 * Magnetic button effect for CTAs
 */
document.querySelectorAll(".magnetic").forEach((btn) => {
  btn.addEventListener("mousemove", (e) => {
    const rect = btn.getBoundingClientRect();
    const x = e.clientX - rect.left - rect.width / 2;
    const y = e.clientY - rect.top - rect.height / 2;

    btn.style.transform = `translate(${x * 0.1}px, ${y * 0.1}px)`;
  });

  btn.addEventListener("mouseleave", () => {
    btn.style.transform = "translate(0, 0)";
  });
});
