// Main JavaScript File

document.addEventListener("DOMContentLoaded", () => {
  loadHeader();
  loadFooter();
  setupLightbox();
});

function loadHeader() {
  const headerHTML = `
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.html" class="brand-logo" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                <img src="assets/images/logo.png" alt="Dharam Prachar Jan Kalyan Samiti Logo" class="logo-img">
                <span style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">DPJKS</span>
            </a>
            
            <div class="nav-right">
                <ul class="nav-menu" id="nav-menu">
                    <li><a href="index.html" class="nav-link">Home</a></li>
                    <li><a href="about.html" class="nav-link">About</a></li>
                    <li><a href="services.html" class="nav-link">Services</a></li>
                    <li><a href="events.html" class="nav-link">Events</a></li>
                    <li><a href="gallery.html" class="nav-link">Gallery</a></li>
                    <li><a href="blog.html" class="nav-link">Blog</a></li>
                    <li><a href="achievements.html" class="nav-link">Achievements</a></li>
                    <li><a href="contact.html" class="nav-link">Contact</a></li>
                    <li class="mobile-only"><a href="donation.html" class="nav-link" style="color: var(--secondary-color);">Donate</a></li>
                </ul>
                <a href="donation.html" class="btn btn-secondary nav-btn">Donate Now</a>
                
                <div class="mobile-toggle" id="mobile-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </nav>
    `;

  document.getElementById("header-placeholder").innerHTML = headerHTML;
  setActiveLink();
  setupMobileMenu();

  // Inject Google Translate script
  if (!document.getElementById("google-translate-script")) {
    window.googleTranslateElementInit = function () {
      new google.translate.TranslateElement(
        { pageLanguage: "en" },
        "google_translate_element",
      );
    };
    const gtScript = document.createElement("script");
    gtScript.id = "google-translate-script";
    gtScript.type = "text/javascript";
    gtScript.src =
      "//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
    document.body.appendChild(gtScript);
  }
}

function loadFooter() {
  const footerHTML = `
    <footer class="footer-mega">
        <div class="container footer-grid">
            <!-- Col 1: About & Social -->
            <div class="footer-col">
                <a href="index.html" class="brand-logo" style="margin-bottom: 1.5rem;">
                    <img src="assets/images/logo.png" alt="Dharam Prachar Jan Kalyan Samiti Logo" class="logo-img-footer">
                </a>
                <p>Dharam Prachar Jan Kalyan Samiti is a non-profit organization established in 2012 working for social welfare, education support, health awareness and upliftment of underprivileged communities.</p>
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links-mega">
                    <li><a href="about.html">About DPJKS</a></li>
                    <li><a href="services.html">Our Causes</a></li>
                    <li><a href="donation.html">Donate Now</a></li>
                    <li><a href="events.html" class="nav-link">Upcoming Events</a></li>
                    <li><a href="privacy-policy.html">Privacy Policy</a></li>
                    <li><a href="terms-conditions.html">Terms & Conditions</a></li>
                    <li><a href="refund-policy.html">Refund Policy</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                </ul>
            </div>

            <!-- Col 3: Contact Info -->
            <div class="footer-col">
                <h3>Contact Us</h3>
                 <div class="footer-contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Gram Hindupur Sabalpur, Shivrajpur,<br>Kanpur Nagar, Uttar Pradesh, India</span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <span>+91 93051 47080</span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>gauravmishra2k22@gmail.com</span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-clock"></i>
                    <span>Mon - Sat: 9:00 AM - 6:00 PM</span>
                </div>
            </div>

            <!-- Col 4: Newsletter -->
            <div class="footer-col">
                <h3>Stay Connected</h3>
                <p style="margin-bottom: 1rem;">Join our newsletter to receive updates on our latest projects and events.</p>
                <form class="footer-newsletter" onsubmit="event.preventDefault(); alert('Thank you for subscribing!');">
                    <input type="email" placeholder="Your Email Address" style="width: 100%; padding: 10px; border: none; border-radius: 4px; margin-bottom: 10px; font-family: inherit;">
                    <button type="submit" class="btn btn-secondary" style="width: 100%; border: none; cursor: pointer;">Subscribe</button>
                </form>
                <div style="margin-top: 2rem; background-color: rgba(244, 211, 94, 0.15); border: 1px solid var(--secondary-color); border-radius: 8px; padding: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <h3 style="margin-bottom: 10px; font-size: 1.1rem; color: var(--secondary-color);">Choose Your Language</h3>
                    <div id="google_translate_element" style="border-radius: 4px; overflow: hidden;"></div>
                </div>
            </div>
        </div>

        <div class="container footer-bottom">
            <p>&copy; 2026 Dharam Prachar Jan Kalyan Samiti. All rights reserved. | Reg. No: K-46259</p>
            <p style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.8;">Designed and Developed by <a href="https://blumox.com" target="_blank" style="color: inherit; text-decoration: underline;">Blumox Technologies</a></p>
            <div class="footer-legal-links">
                <a href="privacy-policy.html">Privacy Policy</a>
                <a href="terms-conditions.html">Terms of Use</a>
                <a href="refund-policy.html">Refund Policy</a>
            </div>
        </div>
    </footer>
    `;

  document.getElementById("footer-placeholder").innerHTML = footerHTML;
}

function setActiveLink() {
  const currentPage = window.location.pathname.split("/").pop() || "index.html";
  const links = document.querySelectorAll(".nav-link");

  links.forEach((link) => {
    if (link.getAttribute("href") === currentPage) {
      link.classList.add("active");
    }
  });
}

function setupMobileMenu() {
  const toggle = document.getElementById("mobile-toggle");
  const menu = document.getElementById("nav-menu");

  if (toggle && menu) {
    toggle.addEventListener("click", () => {
      menu.classList.toggle("active");
      const icon = toggle.querySelector("i");
      if (menu.classList.contains("active")) {
        icon.classList.remove("fa-bars");
        icon.classList.add("fa-times");
      } else {
        icon.classList.remove("fa-times");
        icon.classList.add("fa-bars");
      }
    });
  }
}

// Lightbox Functionality
function setupLightbox() {
  const galleryImages = document.querySelectorAll(
    ".gallery-strip img, .achievements-grid .achievement-card img",
  );
  if (galleryImages.length === 0) return;

  // Create Lightbox HTML if not exists
  if (!document.getElementById("lightbox")) {
    const lightboxHTML = `
            <div id="lightbox" class="lightbox">
                <span class="lightbox-close" id="lb-close">&times;</span>
                <div class="lightbox-nav lb-prev" id="lb-prev"><i class="fas fa-chevron-left"></i></div>
                <div class="lightbox-content">
                    <img src="" alt="" class="lightbox-img" id="lb-img">
                    <div class="lightbox-caption" id="lb-caption"></div>
                </div>
                <div class="lightbox-nav lb-next" id="lb-next"><i class="fas fa-chevron-right"></i></div>
            </div>
        `;
    document.body.insertAdjacentHTML("beforeend", lightboxHTML);
  }

  const lightbox = document.getElementById("lightbox");
  const lbImg = document.getElementById("lb-img");
  const lbCaption = document.getElementById("lb-caption");
  const closeBtn = document.getElementById("lb-close");
  const prevBtn = document.getElementById("lb-prev");
  const nextBtn = document.getElementById("lb-next");

  let currentIndex = 0;

  // Open Lightbox
  galleryImages.forEach((img, index) => {
    img.addEventListener("click", () => {
      currentIndex = index;
      updateLightbox();
      lightbox.style.display = "flex";
      setTimeout(() => lightbox.classList.add("active"), 10);
      document.body.style.overflow = "hidden"; // Disable scroll
    });
  });

  // Update Image & Caption
  function updateLightbox() {
    const img = galleryImages[currentIndex];
    const highResSrc = img.getAttribute("src");
    const caption = img.getAttribute("data-caption") || img.getAttribute("alt");

    lbImg.src = highResSrc;
    lbCaption.textContent = caption;
  }

  // Navigation
  function showNext() {
    currentIndex = (currentIndex + 1) % galleryImages.length;
    updateLightbox();
  }

  function showPrev() {
    currentIndex =
      (currentIndex - 1 + galleryImages.length) % galleryImages.length;
    updateLightbox();
  }

  nextBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    showNext();
  });
  prevBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    showPrev();
  });

  // Close
  function closeLightbox() {
    lightbox.classList.remove("active");
    setTimeout(() => {
      lightbox.style.display = "none";
      document.body.style.overflow = "auto"; // Re-enable scroll
    }, 300);
  }

  closeBtn.addEventListener("click", closeLightbox);

  // Close on clicking outside image
  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox) {
      closeLightbox();
    }
  });

  // Keyboard Navigation
  document.addEventListener("keydown", (e) => {
    if (lightbox.style.display === "flex") {
      if (e.key === "ArrowRight") showNext();
      if (e.key === "ArrowLeft") showPrev();
      if (e.key === "Escape") closeLightbox();
    }
  });
}

// Gallery Data
const galleryData = {
  education: {
    title: "Education Drives",
    description:
      "Empowering the future generation through education and literacy programs.",
    images: [
      "1000071261.jpg",
      "1000071263.jpg",
      "1000071269.jpg",
      "1000071271.jpg",
      "1000071287.jpg",
      "1000071289.jpg",
      "1000071291.jpg",
    ],
  },
  healthcare: {
    title: "Healthcare Camps",
    description: "Providing essential medical services to remote villages.",
    images: [
      "1000071301.jpg",
      "1000071302.jpg",
      "1000071303.jpg",
      "1000071304.jpg",
      "1000071305.jpg",
      "1000071306.jpg",
    ],
  },
  events: {
    title: "Community Events",
    description: "Bringing people together for social causes and celebrations.",
    images: [
      "1000071328.jpg",
      "1000071330.jpg",
      "1000071334.jpg",
      "1000071336.jpg",
      "1000071337.jpg",
      "1000071341.jpg",
      "1000071342.jpg",
      "1000071343.jpg",
      "1000071344.jpg",
      "1000071345.jpg",
      "1000071346.jpg",
      "1000071347.jpg",
      "1000071348.jpg",
      "1000071349.jpg",
      "1000071350.jpg",
      "1000071351.jpg",
      "1000071352.jpg",
      "1000071353.jpg",
    ],
  },
};

function loadGalleryAlbum() {
  const urlParams = new URLSearchParams(window.location.search);
  const albumKey = urlParams.get("album");
  const galleryStrip = document.querySelector(".gallery-strip");

  if (albumKey && galleryData[albumKey] && galleryStrip) {
    const album = galleryData[albumKey];

    // Update Titles
    document.title = `${album.title} - Photo Gallery`;
    const heroTitle = document.querySelector(".detail-hero-content h1");
    const heroDesc = document.querySelector(".detail-hero-content p");
    const contentDesc = document.querySelector(".container p"); // The description paragraph

    if (heroTitle) heroTitle.textContent = album.title;
    if (heroDesc) heroDesc.textContent = album.description;
    if (contentDesc)
      contentDesc.textContent = `Viewing ${album.images.length} photos from our ${album.title} album.`;

    // Update Images
    let imagesHTML = "";
    album.images.forEach((img) => {
      imagesHTML += `<img src="assets/images/gallery/${albumKey}/${img}" alt="${album.title} Image" onclick="setupLightbox()">`;
    });
    galleryStrip.innerHTML = imagesHTML;

    // Setup lightbox for new images
    setTimeout(setupLightbox, 100);
  }
}

// Check if we need to load an album
if (window.location.pathname.includes("gallery-detail.html")) {
  document.addEventListener("DOMContentLoaded", loadGalleryAlbum);
}
