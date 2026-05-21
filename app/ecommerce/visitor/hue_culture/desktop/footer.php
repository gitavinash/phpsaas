<!-- Redesigned Footer -->
<footer class="main-footer <?php echo ((BASE_URL == '/color-clinic/') ? 'light-pink-bg' : 'soft-green-bg') ?>">
    <div class="container-wide">
        <div class="footer-main">
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="<?php echo USER_IMAGES ?>logo.png" alt="Hue Culture Logo" />
                </div>
                <p class="brand-tagline">
                    A scientific approach to color, meeting the soul of design. Since
                    2022.
                </p>
                <div class="footer-social">
                    <a href="#" class="social-link-outline" aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link-outline" aria-label="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link-outline" aria-label="X">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <a href="#" class="social-link-outline" aria-label="LinkedIn">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <div class="footer-col wide">
                <h4>Color Clinic</h4>
                <ul>
                    <?php generateMenu('SITE_NAV_FOOTER_1', 'mobile', true); ?>
                </ul>
            </div>

            <div class="footer-col wide">
                <h4>Quick links</h4>
                <ul>
                    <?php generateMenu('SITE_NAV_FOOTER_2', 'mobile', true); ?>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-copyright" style="display: flex; flex-direction: column; gap: 8px">
                <p>&copy; 2022-<?php echo date('Y') ?> Hue Culture<sup>TM</sup>. All Rights Reserved.</p>
                <p>
                    Designed &amp; Developed by
                    <a href="https://blumox.com" target="_blank" rel="noopener"
                        title="Blumox Technologies - Web Design and Development"
                        style="color: inherit; text-decoration: none; font-weight: 500">Blumox Technologies</a>
                </p>
            </div>
            <div class="footer-legal">
                <?php generateMenu('SITE_NAV_FOOTER_3', 'mobile', true); ?>
            </div>
        </div>
    </div>
</footer>

<!-- Full Screen Mobile Navigation -->
<div class="mobile-nav" id="mobile-nav">
    <div class="menu-close" id="menu-close">&times;</div>
    <nav>
        <ul>
            <?php generateMenu('SITE_NAV_MAIN_HEADER', 'mobile', true); ?>
            <li>
                <?php
                generateMenu('SITE_NAV_HEADER_MENU_CTA', 'non_list', true, 'btn-primary mobile-cta green-bg');
                ?>
            </li>
        </ul>
    </nav>
</div>

<script>
// Header scroll effect
window.addEventListener("scroll", () => {
    const header = document.getElementById("main-header");
    if (window.scrollY > 50) {
        header.classList.add("scrolled");
    } else {
        header.classList.remove("scrolled");
    }
});

// Intersection Observer for reveal animations
const observerOptions = {
    threshold: 0.1,
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add("active");
        }
    });
}, observerOptions);

document.querySelectorAll(".reveal").forEach((el) => {
    observer.observe(el);
});

// Mobile Menu Toggle
const menuToggle = document.getElementById("menu-toggle");
const menuClose = document.getElementById("menu-close");
const mobileNav = document.getElementById("mobile-nav");
const mobileLinks = document.querySelectorAll(".mobile-link");
const body = document.body;

function toggleMenu() {
    menuToggle.classList.toggle("active");
    mobileNav.classList.toggle("active");
    body.classList.toggle("no-scroll");
}

menuToggle.addEventListener("click", toggleMenu);
menuClose.addEventListener("click", toggleMenu);

mobileLinks.forEach((link) => {
    link.addEventListener("click", () => {
        menuToggle.classList.remove("active");
        mobileNav.classList.remove("active");
        body.classList.remove("no-scroll");
    });
});

// ===== Testimonial Tab Switcher =====
const testiTabs = document.querySelectorAll(".testi-tab");
const testiPanels = document.querySelectorAll(".testimonial-panel");

testiTabs.forEach((tab) => {
    tab.addEventListener("click", () => {
        testiTabs.forEach((t) => t.classList.remove("active"));
        testiPanels.forEach((p) => p.classList.remove("active"));
        tab.classList.add("active");
        const targetId = tab.getAttribute("data-panel");
        document.getElementById(targetId).classList.add("active");
    });
});

// ===== Infinite 3-Up Testimonial Carousel =====
const testiSliderWrap = document.querySelector(".testi-slider-wrap");
const testiSlider = document.getElementById("testiSlider");
const sliderDots = document.querySelectorAll(".slider-dot");

if (testiSlider && sliderDots.length) {
    let slides = Array.from(testiSlider.querySelectorAll(".testi-slide"));
    const slideCount = slides.length;
    let activeIdx = 0;
    let autoPlayInterval;
    let isTransitioning = false;

    // 1. Clone slides for infinite loop
    const clonesToPrepend = slides.slice(-3).map((s) => s.cloneNode(true));
    const clonesToAppend = slides.slice(0, 3).map((s) => s.cloneNode(true));

    clonesToPrepend
        .reverse()
        .forEach((clone) => testiSlider.prepend(clone));
    clonesToAppend.forEach((clone) => testiSlider.append(clone));

    function getLayoutInfo() {
        const wrapWidth = testiSliderWrap.offsetWidth;
        let visibleSlides = 3;
        if (window.innerWidth <= 600) visibleSlides = 1;
        else if (window.innerWidth <= 991) visibleSlides = 2;

        const gap = 24;
        const totalGapSpace = gap * (visibleSlides - 1);
        const slideWidth = (wrapWidth - totalGapSpace) / visibleSlides;

        return {
            slideWidth,
            gap,
            totalWidth: slideWidth + gap
        };
    }

    function updateSlider(animate = true) {
        const {
            slideWidth,
            totalWidth
        } = getLayoutInfo();

        // Apply width to all slides (including clones)
        const allSlides = testiSlider.querySelectorAll(".testi-slide");
        allSlides.forEach((s) => {
            s.style.flex = `0 0 ${slideWidth}px`;
        });

        const offset = (activeIdx + 3) * totalWidth;

        if (animate) {
            testiSlider.style.transition =
                "transform 0.6s cubic-bezier(0.23, 1, 0.32, 1)";
            isTransitioning = true;
        } else {
            testiSlider.style.transition = "none";
        }

        testiSlider.style.transform = `translateX(-${offset}px)`;

        const realIdx = (activeIdx + slideCount) % slideCount;
        sliderDots.forEach((d, i) => {
            d.classList.toggle("active", i === realIdx);
        });
    }

    testiSlider.addEventListener("transitionend", () => {
        isTransitioning = false;
        if (activeIdx >= slideCount) {
            activeIdx = 0;
            updateSlider(false);
        } else if (activeIdx < 0) {
            activeIdx = slideCount - 1;
            updateSlider(false);
        }
    });

    function goToSlide(idx) {
        if (isTransitioning) return;
        activeIdx = idx;
        updateSlider(true);
    }

    function startAutoPlay() {
        stopAutoPlay();
        autoPlayInterval = setInterval(() => {
            if (!isTransitioning) {
                activeIdx++;
                updateSlider(true);
            }
        }, 5000);
    }

    function stopAutoPlay() {
        if (autoPlayInterval) clearInterval(autoPlayInterval);
    }

    sliderDots.forEach((dot) => {
        dot.addEventListener("click", () => {
            const idx = parseInt(dot.getAttribute("data-idx"));
            goToSlide(idx);
            startAutoPlay();
        });
    });

    testiSlider.addEventListener("mouseenter", stopAutoPlay);
    testiSlider.addEventListener("mouseleave", () => {
        // Only resume if no video was clicked to unmute
        if (!testiSlider.classList.contains("video-playing")) {
            startAutoPlay();
        }
    });

    // Click to Unmute & Stop Slider (Via Overlay)
    testiSlider.addEventListener("click", (e) => {
        const overlay = e.target.closest(".testi-video-overlay");
        if (overlay) {
            const videoWrap = overlay.closest(".testi-video-wrap");
            const iframe = videoWrap.querySelector("iframe");
            if (iframe) {
                // Stop the carousel permanently
                stopAutoPlay();
                testiSlider.classList.add("video-playing");

                // 1. Mute ALL other videos in the slider track
                const allIframes = testiSlider.querySelectorAll("iframe");
                allIframes.forEach((f) => {
                    f.contentWindow.postMessage(
                        JSON.stringify({
                            event: "command",
                            func: "mute",
                            args: "",
                        }),
                        "*",
                    );
                });

                // 2. Reset ALL other overlays to visible (so they can be clicked to unmute later)
                const allOverlays = testiSlider.querySelectorAll(
                    ".testi-video-overlay",
                );
                allOverlays.forEach((o) => o.classList.remove("hidden"));

                // 3. Unmute and Play the target video
                overlay.classList.add("hidden"); // Hide the current one

                iframe.contentWindow.postMessage(
                    JSON.stringify({
                        event: "command",
                        func: "unMute",
                        args: "",
                    }),
                    "*",
                );
                iframe.contentWindow.postMessage(
                    JSON.stringify({
                        event: "command",
                        func: "playVideo",
                        args: "",
                    }),
                    "*",
                );
            }
        }
    });

    window.addEventListener("resize", () => {
        updateSlider(false);
    });

    // Initialize
    setTimeout(() => {
        updateSlider(false);
        startAutoPlay();
    }, 100);
}

// ===== Hero Quote Switcher (Style C) =====
const heroQuotes = [{
        quote: "The Hue Culture System completely changed how I look at my home. I finally understand why my 'off-white' walls looked yellow!",
        name: "Sarah Jenkins",
        role: "Homeowner",
        initials: "SJ",
    },
    {
        quote: "As a designer, this system is my secret weapon. It gives me the confidence to specify colors that I know will work perfectly every single time.",
        name: "Marcus Thorne",
        role: "Interior Architect",
        initials: "MT",
    },
    {
        quote: "No more second-guessing. The color wheel and guides are essential for anyone who wants to get their renovation right the first time.",
        name: "Elena Rodriguez",
        role: "Space Owner",
        initials: "ER",
    },
];

const stripAvatars = document.querySelectorAll(".testi-strip-avatar");
const heroQuoteEl = document.getElementById("heroQuote");
const heroAvatarEl = document.getElementById("heroAvatar");
const heroNameEl = document.getElementById("heroName");
const heroRoleEl = document.getElementById("heroRole");

if (stripAvatars.length) {
    stripAvatars.forEach((av) => {
        av.addEventListener("click", () => {
            const idx = parseInt(av.getAttribute("data-quote"));
            const data = heroQuotes[idx];
            heroQuoteEl.textContent = data.quote;
            heroAvatarEl.textContent = data.initials;
            heroNameEl.textContent = data.name;
            heroRoleEl.textContent = data.role;
            stripAvatars.forEach((a) => a.classList.remove("active"));
            av.classList.add("active");
        });
    });
}
</script>
<script src="<?php echo USER_JS ?>main.js"></script>
</body>

</html>