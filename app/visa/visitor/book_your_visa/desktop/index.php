<?php

require_once 'header.php';

?>
<main class="pt-20">
    <section class="relative min-h-[600px] flex items-center bg-cover bg-center"
        style="background-image: url('<?php echo USER_IMAGES ?>background.jpg')">
        <div class="absolute inset-0 bg-gradient-to-r from-white/90 to-white/30"></div>
        <div class="container mx-auto px-4 relative">
            <div class="max-w-2xl">
                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6">Simple Visa Application Process</h1>
                <p class="text-lg md:text-xl text-gray-700 mb-8">Get your visa hassle-free with our streamlined
                    online process. Expert support at every step.</p>

                <div class="bg-white p-6 rounded-lg shadow-lg max-w-xl">
                    <div class="flex space-x-4 mb-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Country</label>
                            <div class="relative">
                                <select id="selectedCountry"
                                    class="selectJS selectDestination w-full border border-gray-300 rounded-button px-4 py-2.5 appearance-none bg-white pr-10">
                                    <option></option>
                                    <?php

                                    if ($checkCountry = mysqli_query($connection, "SELECT visa_country_code, visa_country_title, visa_country_services FROM {$tableNamePrefix}country WHERE visa_country_status=1 AND visa_country_site_id=" . SITE_ID . " ORDER BY visa_country_title ASC")) {
                                        while ($checkCountryData = mysqli_fetch_assoc($checkCountry)) {
                                            echo "<option value='{$checkCountryData['visa_country_title']}' data-service='{$checkCountryData['visa_country_services']}' data-flag='https://flagcdn.com/w80/" . strtolower($checkCountryData['visa_country_code']) . ".png'>{$checkCountryData['visa_country_title']}</option>";
                                        }
                                    }

                                    ?>
                                </select>
                                <i
                                    class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 flex items-center justify-center"></i>
                            </div>
                        </div>
                        <!-- <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Visa Type</label>
                            <div class="relative">
                                <select
                                    class="w-full border border-gray-300 rounded-button px-4 py-2.5 appearance-none bg-white pr-10">
                                    <option value="">Select type</option>
                                    <option value="tourist">Tourist</option>
                                    <option value="business">Business</option>
                                    <option value="student">Student</option>
                                </select>
                                <i
                                    class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 flex items-center justify-center"></i>
                            </div>
                        </div> -->
                    </div>
                    <a href="#" id="startApplication" data-readdy="true"
                        class="w-full bg-primary text-white font-medium py-3 !rounded-button hover:bg-primary/90 cursor-pointer block text-center">
                        Apply for Visa Now
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">How It Works</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-earth-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="font-medium mb-2">Select Country</h3>
                    <p class="text-sm text-gray-600">Choose your destination and visa type</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-file-list-3-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="font-medium mb-2">Fill Details</h3>
                    <p class="text-sm text-gray-600">Complete your application form</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-upload-cloud-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="font-medium mb-2">Upload Documents</h3>
                    <p class="text-sm text-gray-600">Submit required documents</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-radar-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="font-medium mb-2">Track Status</h3>
                    <p class="text-sm text-gray-600">Monitor your application</p>
                </div>
            </div>
        </div>
    </section>
    <section class="py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Popular Destinations</h2>
            <div class="relative">
                <div class="overflow-hidden">
                    <div id="destinationSlider" class="flex transition-transform duration-300 ease-in-out mb-12"
                        style="transform: translateX(0px);">
                        <?php
                        $query = "
                SELECT 
                    vc.visa_country_code,
                    vc.visa_country_image,
                    vc.visa_country_title,
                    vc.visa_country_services,
                    vp.visa_package_mrp,
                    vp.visa_package_price
                FROM {$tableNamePrefix}country AS vc
                JOIN (
                    SELECT 
                        visa_package_country_id,
                        MIN(visa_package_price) AS min_price
                    FROM {$tableNamePrefix}package
                    GROUP BY visa_package_country_id
                ) AS vp_min
                    ON vp_min.visa_package_country_id = vc.visa_country_id
                JOIN {$tableNamePrefix}package AS vp
                    ON vp.visa_package_country_id = vc.visa_country_id
                    AND vp.visa_package_price = vp_min.min_price
                WHERE 
                    vc.visa_country_status = 1
                    AND vc.visa_country_site_id = " . SITE_ID . "
                ORDER BY vc.visa_country_is_featured DESC, RAND()
                LIMIT 10";

                        if ($featuredCountry = mysqli_query($connection, $query)) {
                            while ($row = mysqli_fetch_assoc($featuredCountry)) {
                                $countryTitle = ucwords($row['visa_country_title']);
                                $countrySlug = strtolower(str_replace(' ', '_', $row['visa_country_title']));
                                $countryImage = $row['visa_country_image'] ?: '';
                                $price = number_format(str_replace(',', '', $row['visa_package_price']));

                                echo "
                    <div class='country-card relative bg-white rounded-2xl shadow-md overflow-hidden group min-w-[calc(100%-1.2rem)] md:min-w-[calc(25%-1.2rem)] mx-3'>
                        <img src='{$countryImage}' alt='{$countryTitle}' class='w-full h-48 object-cover'>
                        <div class='p-4'>
                            <h3 class='font-semibold text-lg mb-2'>{$countryTitle}</h3>
                            <div class='flex justify-between text-sm text-gray-600 mb-4'>
                                <span></span>
                                <span>From ₹{$price}</span>
                            </div>
                            <a href='/start-application/?country={$countrySlug}'
                               class='block w-full text-center bg-primary text-white font-medium py-2 rounded-button hover:bg-primary/90 transition'>
                               Apply Now
                            </a>
                        </div>
                    </div>
                    ";
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Slider Buttons -->
                <button id="prevBtn"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-2 md:-translate-x-6 w-10 h-10 md:w-12 md:h-12 bg-white rounded-full shadow-lg flex items-center justify-center text-primary hover:bg-gray-50 z-10">
                    <i class="ri-arrow-left-s-line ri-xl"></i>
                </button>
                <button id="nextBtn"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-2 md:translate-x-6 w-10 h-10 md:w-12 md:h-12 bg-white rounded-full shadow-lg flex items-center justify-center text-primary hover:bg-gray-50 z-10">
                    <i class="ri-arrow-right-s-line ri-xl"></i>
                </button>
            </div>

        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('destinationSlider');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            let currentPosition = 0;
            const cardWidth = slider.children[0].offsetWidth + 24; // Including margin
            const totalCards = slider.children.length;
            const visibleCards = window.innerWidth < 768 ? 1 : 4;
            const maxPosition = -(totalCards - visibleCards) * cardWidth;
            // Update on window resize
            window.addEventListener('resize', () => {
                const newVisibleCards = window.innerWidth < 768 ? 1 : 4;
                if (newVisibleCards !== visibleCards) {
                    currentPosition = 0;
                    updateSliderPosition();
                }
            });

            function updateSliderPosition() {
                slider.style.transform = `translateX(${currentPosition}px)`;
            }
            prevBtn.addEventListener('click', () => {
                if (currentPosition < 0) {
                    currentPosition += cardWidth;
                    updateSliderPosition();
                }
            });
            nextBtn.addEventListener('click', () => {
                if (currentPosition > maxPosition) {
                    currentPosition -= cardWidth;
                    updateSliderPosition();
                }
            });
        });
        </script>
    </section>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <title>Schengen Countries – Minimal</title>
        <script src="https://cdn.tailwindcss.com/3.4.16"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    </head>

    <body class="bg-white text-gray-900">
        <!-- Schengen Countries • Minimal -->
        <section id="schengen-minimal" class="py-16">
            <div class="container mx-auto px-4">
                <header class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold tracking-tight">Schengen Countries</h2>
                        <p class="text-gray-600 mt-1">Quick apply for popular Schengen destinations.</p>
                    </div>
                    <!-- Arrows (mobile only, live inside slider width) -->
                    <div class="md:hidden">
                        <div class="flex items-center gap-2">
                            <button id="schPrev"
                                class="w-9 h-9 rounded-full bg-white ring-1 ring-gray-300 text-gray-700 flex items-center justify-center disabled:opacity-40 disabled:cursor-not-allowed"
                                aria-label="Previous">
                                <i class="ri-arrow-left-s-line"></i>
                            </button>
                            <button id="schNext"
                                class="w-9 h-9 rounded-full bg-white ring-1 ring-gray-300 text-gray-700 flex items-center justify-center disabled:opacity-40 disabled:cursor-not-allowed"
                                aria-label="Next">
                                <i class="ri-arrow-right-s-line"></i>
                            </button>
                        </div>
                    </div>
                </header>

                <!-- Mobile slider: single row, exactly 2 cards per view -->
                <div class="md:hidden">
                    <div id="schTrack"
                        class="flex flex-nowrap gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-1"
                        role="list" aria-label="Schengen country slider">
                        <!-- Cards injected by JS -->
                    </div>
                </div>

                <!-- Desktop grid: 4 per row x 2 rows (8 max) -->
                <div id="schGrid" class="hidden md:grid grid-cols-4 gap-6">
                    <!-- Cards injected by JS -->
                </div>
            </div>
        </section>

        <script>
        // --- Data (max 8) ---
        const SCHENGEN = [{
                name: "Germany",
                code: "de"
            },
            {
                name: "France",
                code: "fr"
            },
            {
                name: "Italy",
                code: "it"
            },
            {
                name: "Spain",
                code: "es"
            },
            {
                name: "Netherlands",
                code: "nl"
            },
            {
                name: "Switzerland",
                code: "ch"
            },
            {
                name: "Sweden",
                code: "se"
            },
            {
                name: "Greece",
                code: "gr"
            },
        ].slice(0, 8);

        // --- Helpers ---
        const toSlug = s => s.toLowerCase().replace(/\&/g, "and").replace(/[^a-z0-9]+/g, "_").replace(/^_+|_+$/g, "");
        const flagURL = c => `https://flagcdn.com/w320/${c}.png`;
        const isMobile = () => window.innerWidth < 768;
        const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

        // Minimal, crisp card (no shared CSS needed)
        function cardHTML(c, extra = "") {
            const slug = toSlug(c.name);
            return `
        <div class="group bg-white rounded-xl ring-1 ring-gray-200 hover:ring-gray-300 transition ${extra}">
          <img src="${flagURL(c.code)}" loading="lazy" alt="${c.name} flag" class="w-full h-28 object-cover">
          <div class="p-4">
            <h3 class="font-semibold text-base md:text-lg mb-1">${c.name}</h3>
            <p class="text-xs md:text-sm text-gray-500 mb-3">Schengen Visa</p>
            <a href="/start-application/?country=${slug}"
               class="w-full inline-flex items-center justify-center rounded-button ring-1 ring-blue-600 text-blue-700 hover:bg-blue-600 hover:text-white font-medium py-2 transition">
               Apply Now
            </a>
          </div>
        </div>
      `;
        }

        // --- Desktop render (grid) ---
        const schGrid = document.getElementById("schGrid");
        schGrid.innerHTML = SCHENGEN.map(c => cardHTML(c)).join("");

        // --- Mobile slider (single row, 2 per view) ---
        const schTrack = document.getElementById("schTrack");
        // Key trick: each card is exactly half the visible width minus half the gap,
        // so two cards + one gap fill the viewport width perfectly.
        // gap = 1rem -> each card basis calc(50% - 0.5rem)
        schTrack.innerHTML = SCHENGEN.map(c =>
            cardHTML(c, "shrink-0 basis-[calc(50%-0.5rem)] snap-start")
        ).join("");

        // --- Slider controls (mobile only) ---
        const schPrev = document.getElementById("schPrev");
        const schNext = document.getElementById("schNext");

        function step() {
            // One "page" is exactly the visible width (2 cards)
            return schTrack.clientWidth;
        }

        function currentIndex() {
            return Math.round(schTrack.scrollLeft / step());
        }

        function totalPages() {
            const visible = 2;
            return Math.max(1, Math.ceil(SCHENGEN.length / visible));
        }

        function goTo(i, behavior = "smooth") {
            const max = totalPages() - 1;
            const idx = Math.max(0, Math.min(i, max));
            schTrack.scrollTo({
                left: idx * step(),
                behavior
            });
        }

        function updateArrows() {
            const idx = currentIndex();
            if (schPrev) schPrev.disabled = (idx <= 0);
            if (schNext) schNext.disabled = (idx >= totalPages() - 1);
        }

        schPrev?.addEventListener("click", () => goTo(currentIndex() - 1));
        schNext?.addEventListener("click", () => goTo(currentIndex() + 1));

        // --- Autoplay (mobile only) ---
        let autoplayTimer = null;
        const AUTOPLAY_MS = 2500;

        function startAutoplay() {
            if (!isMobile() || reducedMotion || totalPages() <= 1) return;
            stopAutoplay();
            autoplayTimer = setInterval(() => {
                const idx = currentIndex();
                goTo(idx >= totalPages() - 1 ? 0 : idx + 1);
            }, AUTOPLAY_MS);
        }

        function stopAutoplay() {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        }

        // Pause on interaction; resume after a moment
        ["touchstart", "pointerdown", "mousedown", "keydown"].forEach(evt =>
            schTrack.addEventListener(evt, () => stopAutoplay(), {
                passive: true
            })
        );
        ["touchend", "pointerup", "mouseup", "keyup"].forEach(evt =>
            schTrack.addEventListener(evt, () => setTimeout(startAutoplay, 400), {
                passive: true
            })
        );

        // Keep UI in sync
        let rAF;
        schTrack.addEventListener("scroll", () => {
            if (rAF) return;
            rAF = requestAnimationFrame(() => {
                updateArrows();
                rAF = null;
            });
        }, {
            passive: true
        });

        let resizeTimer;
        window.addEventListener("resize", () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                goTo(currentIndex(), "auto"); // snap after layout change
                stopAutoplay();
                startAutoplay();
            }, 120);
        });

        // Pause when tab hidden; resume when visible
        document.addEventListener("visibilitychange", () => {
            if (document.hidden) stopAutoplay();
            else startAutoplay();
        });

        // Init
        updateArrows();
        startAutoplay();
        </script>
    </body>

    </html>


    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Our Services</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-file-list-3-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="text-xl font-medium mb-4">Document Assistance</h3>
                    <p class="text-gray-600 mb-6">Expert guidance on documentation requirements and verification
                        services</p>
                    <!-- <a class="text-primary hover:text-primary/80 font-medium cursor-pointer">Learn More</a> -->
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-timer-flash-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="text-xl font-medium mb-4">Fast Processing</h3>
                    <p class="text-gray-600 mb-6">Quick turnaround times with priority processing options available
                    </p>
                    <!-- <a class="text-primary hover:text-primary/80 font-medium cursor-pointer">Learn More</a> -->
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-customer-service-2-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="text-xl font-medium mb-4">Expert Support</h3>
                    <p class="text-gray-600 mb-6">24/7 customer support and personalized assistance throughout the
                        process</p>
                    <!-- <a class="text-primary hover:text-primary/80 font-medium cursor-pointer">Learn More</a> -->
                </div>
            </div>
        </div>
    </section>
    <?php

    if ($partnerList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "company WHERE company_type='partnerships' AND company_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($partnerList) != 0) {
            echo "
    <section class='py-20 bg-gradient-to-b from-gray-50 to-white'>
        <div class='container mx-auto px-4'>
            <h2 class='text-3xl font-bold text-center mb-4'>Trusted By Leading Brands</h2>
            <p class='text-gray-600 text-center mb-12 max-w-2xl mx-auto'>Partner with industry leaders to provide
                seamless visa and travel solutions worldwide</p>
            <div class='grid grid-cols-2 md:grid-cols-4 gap-8 lg:gap-12 items-center partnerSlider'>";
            while ($partnerListData = mysqli_fetch_assoc($partnerList)) {
                extract($partnerListData);
                echo "
                                <div class='group hover:-translate-y-1 transition-transform duration-300'>
                                    <div
                                        class='w-full h-38 bg-white rounded-lg shadow-lg flex flex-col items-center justify-center p-4 hover:shadow-xl transition-shadow duration-300'>
                                        <img src='{$company_logo}' alt='{$company_title} logo' class='pt-4 pb-4'>
                                        <span class='text-sm text-gray-500 group-hover:text-primary font-medium'>{$company_title}</span>
                                    </div>
                                </div>
                            ";
            }
            echo "
    </div>
    </div>
    </section>";
        }
    }
    ?>
    <?php

    if ($blogPost = mysqli_query($connection, "SELECT link_url, post_title, post_thumbnail, post_image, meta_description, category_title, link_published FROM " . TABLE_PREFIX . "post INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=post_id INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id INNER JOIN " . TABLE_PREFIX . "category ON category_id=post_category AND category_site_id=" . SITE_ID . " WHERE link_type='post' AND post_type='blog' AND link_status=200 AND link_site_id=" . SITE_ID . " AND post_site_id=" . SITE_ID . " AND meta_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 3")) {
        if (mysqli_num_rows($blogPost) != 0) {
            echo "
    <section class='py-20'>
        <div class='container mx-auto px-4'>
            <div class='flex flex-col md:flex-row justify-between items-center mb-12'>
                <h2 class='text-3xl font-bold'>Latest Travel Updates</h2>
                <a href='/blogs/' class='text-primary hover:text-primary/80 font-medium'>View All Posts</a>
            </div>
            <div class='grid grid-cols-1 md:grid-cols-3 gap-8'>";
            while ($blogPostData = mysqli_fetch_assoc($blogPost)) {
                extract($blogPostData);
                echo "
                                <article class='bg-white rounded-lg shadow-lg overflow-hidden group'>
                                    <a href='{$link_url}' title='{$post_title}'>
                                        <img src='{$post_image}'
                                        alt='{$post_title}'
                                        class='w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300'>
                                    <div class='p-6'>
                                        <div class='flex items-center justify-between mb-3'>
                                            <span class='px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full'>{$category_title}</span>
                                            <div class='flex items-center text-sm text-gray-500'>
                                                <i class='ri-calendar-line mr-2 w-4 h-4 flex items-center justify-center'></i>
                                                " . date('d M Y h:i:A', strtotime($link_published)) . "
                                            </div>
                                        </div>
                                        <h3 class='text-xl font-medium mb-3'>{$post_title}</h3>
                                        <a href='{$link_url}' title='{$post_title}' class='text-primary hover:text-primary/80 font-medium inline-flex items-center'>
                                            Read More
                                            <i class='ri-arrow-right-line ml-1'></i>
                                        </a>
                                    </div>
                                    </a>
                                </article>";
            }
            echo "
    </div>
    </div>
    </section>";
        }
    }

    ?>


    <section class="w-full">
        <!-- Why Choose Us -->
        <div class="bg-white py-20">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-extrabold tracking-tight text-gray-900">
                        Why Choose Us
                    </h2>
                    <p class="mt-3 text-lg text-gray-600 max-w-2xl mx-auto">
                        Trusted by thousands worldwide for seamless visa solutions with expert support at every step.
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div
                        class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-8 shadow-sm hover:shadow-md transition">
                        <div class="text-4xl font-bold text-primary">50K+</div>
                        <p class="mt-2 text-gray-600 text-sm">Visas Processed</p>
                    </div>
                    <div
                        class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-8 shadow-sm hover:shadow-md transition">
                        <div class="text-4xl font-bold text-primary">100+</div>
                        <p class="mt-2 text-gray-600 text-sm">Countries Served</p>
                    </div>
                    <div
                        class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-8 shadow-sm hover:shadow-md transition">
                        <div class="text-4xl font-bold text-primary">98%</div>
                        <p class="mt-2 text-gray-600 text-sm">Success Rate</p>
                    </div>
                    <div
                        class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-8 shadow-sm hover:shadow-md transition">
                        <div class="text-4xl font-bold text-primary">24/7</div>
                        <p class="mt-2 text-gray-600 text-sm">Support</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testimonials -->
        <div class="bg-gray-50 py-20">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10">
                    <h2 class="text-4xl font-extrabold text-gray-900 text-center md:text-left">
                        What Our Customers Say
                    </h2>
                    <p class="mt-4 md:mt-0 text-gray-600 text-center md:text-left max-w-md">
                        Hear from our satisfied clients about their experience using our visa services.
                    </p>
                </div>

                <!-- Slider Container -->
                <div class="relative overflow-hidden">
                    <div id="testimonial-track"
                        class="flex gap-6 overflow-x-hidden snap-x snap-mandatory scroll-smooth pb-4" role="list"
                        aria-label="Customer Testimonials">
                        <!-- Testimonial Card -->
                        <div class="snap-start shrink-0 w-80 bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition"
                            role="listitem">
                            <p class="text-gray-700 italic mb-4">
                                “Excellent service! The process was smooth and the team was supportive throughout.”
                            </p>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gray-200 rounded-full mr-3"></div>
                                <div>
                                    <div class="font-medium">Sarah Thompson</div>
                                    <div class="text-sm text-gray-500">United States Visa</div>
                                </div>
                            </div>
                        </div>

                        <div class="snap-start shrink-0 w-80 bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition"
                            role="listitem">
                            <p class="text-gray-700 italic mb-4">
                                “Fast and reliable service. Got my visa approved without any hassle. Highly
                                recommended!”
                            </p>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gray-200 rounded-full mr-3"></div>
                                <div>
                                    <div class="font-medium">Michael Chen</div>
                                    <div class="text-sm text-gray-500">Australia Visa</div>
                                </div>
                            </div>
                        </div>

                        <div class="snap-start shrink-0 w-80 bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition"
                            role="listitem">
                            <p class="text-gray-700 italic mb-4">
                                “Clear guidance at each step. Their checklist saved me days. Fantastic service!”
                            </p>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gray-200 rounded-full mr-3"></div>
                                <div>
                                    <div class="font-medium">Aarav Khanna</div>
                                    <div class="text-sm text-gray-500">Canada Visa</div>
                                </div>
                            </div>
                        </div>

                        <div class="snap-start shrink-0 w-80 bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition"
                            role="listitem">
                            <p class="text-gray-700 italic mb-4">
                                “24/7 support actually means 24/7. My late-night queries were answered immediately.”
                            </p>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gray-200 rounded-full mr-3"></div>
                                <div>
                                    <div class="font-medium">Priya Mehta</div>
                                    <div class="text-sm text-gray-500">UK Visa</div>
                                </div>
                            </div>
                        </div>

                        <div class="snap-start shrink-0 w-80 bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition"
                            role="listitem">
                            <p class="text-gray-700 italic mb-4">
                                “They caught a mistake in my application that would have delayed my visa. Total
                                lifesaver.”
                            </p>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gray-200 rounded-full mr-3"></div>
                                <div>
                                    <div class="font-medium">Daniel Ruiz</div>
                                    <div class="text-sm text-gray-500">Schengen Visa</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gradient Scroll Hints -->
                    <div
                        class="absolute inset-y-0 left-0 w-24 bg-gradient-to-r from-gray-50 to-transparent pointer-events-none">
                    </div>
                    <div
                        class="absolute inset-y-0 right-0 w-24 bg-gradient-to-l from-gray-50 to-transparent pointer-events-none">
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
<?php

require_once 'footer.php';

?>