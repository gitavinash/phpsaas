// Main JavaScript File

document.addEventListener("DOMContentLoaded", () => {
  loadHeader();
  loadFooter();
  setupLightbox();
});

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
