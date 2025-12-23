<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download PDF of <?php echo $tour_package_title . ' Quotation | ' . SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
    :root {
        --primary-color: <?php echo SITE_THEME_COLOUR ?>;
        --secondary-color: <?php echo SITE_BACKGROUND_COLOUR ?>;
        --text-color: #333;
    }
    </style>
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>itinerary-2.css">
    <style>
    #pdf-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        color: white;
    }

    .loader-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .loader-text {
        margin-top: 24px;
        font-size: 18px;
        font-weight: 600;
        text-align: center;
    }

    .loader-progress {
        margin-top: 12px;
        font-size: 14px;
        opacity: 0.9;
    }

    .loader-powered-by {
        margin-top: 30px;
        font-size: 12px;
        opacity: 0.8;
    }

    .loader-powered-by a {
        color: white;
        text-decoration: none;
        font-weight: 600;
    }

    .loader-powered-by a:hover {
        text-decoration: underline;
    }

    #content-wrapper {
        opacity: 0;
        transition: opacity 0.5s ease;
    }

    #content-wrapper.show {
        opacity: 1;
    }
    </style>
</head>

<body>
    <div id="pdf-loader">
        <div class="loader-spinner"></div>
        <div class="loader-text">Generating Your Itinerary PDF</div>
        <div class="loader-progress" id="loader-progress">Please wait...</div>
        <div class="loader-powered-by">Powered by <a href='http://aleya.in'>Aleya</a></div>
    </div>

    <div id="content-wrapper">

        <section class="page">
            <header class="hero">
                <img src="<?php echo $tour_package_image ?>" alt="<?php echo $tour_package_title ?>">
                <div class="refid">
                    Ref. id: <?php echo $quoteId ?>
                </div>
            </header>

            <div class="container">
                <div class="title-section">
                    <span class="line-left">|</span>
                    <h1> <?php echo $tour_package_title ?> </h1>
                    <span class="line-right">|</span>
                </div>

                <!-- <h2 class="route">
                | Manali Kasol | Fall in love with Himachal Pradesh
            </h2> -->

                <div class="stats">
                    <?php

                    if ($travelDate != '') { ?>
                    <div class="box">
                        <p>Travel date</p>
                        <p><strong><?php echo date('d M Y', strtotime($travelDate)) ?></strong></p>
                    </div>
                    <?php
                    }

                    if ($numberOfDays != 0) { ?>
                    <div class="box">
                        <p>Travel Duration</p>
                        <p><strong><?php echo $numberOfDays ?> Nights / <?php echo $numberOfDays - 1 ?> Days</strong>
                        </p>
                    </div>
                    <?php
                    }

                    if ($tour_package_city != '') { ?>
                    <div class="box">
                        <p>Travel Location</p>
                        <p><strong><?php echo $tour_package_city ?></strong></p>
                    </div>
                    <?php
                    }
                    ?>
                </div>

                <div class="top-section">

                    <div class="top-heading">
                        <h2>Tour Overview</h2>
                    </div>

                    <p class="paragraph"><?php echo $tour_package_overview ?></p>

                </div>
            </div>

        </section>

        <?php
        $j = 0;
        $dayCount = 01;

        foreach ($itineraries as $day) {
            // Extract title and remove "Day X - " prefix
            $titleParts = explode(' - ', $day['day'], 2);
            $title = htmlspecialchars(isset($titleParts[1]) ? $titleParts[1] : $titleParts[0]);
            $featureImage = isset($day['feature_image']) ? $day['feature_image'] : '';

            $dayOverview = isset($day['overview']) ? str_replace(array("\r", "\n", "u003C",  "/pu003E", "pu003E",), '<br>', $day['overview']) : '';

            echo "<section class='page days-page'><header class='hero'>";

            if ($featureImage != '') {
                echo "<img src='{$featureImage}' alt='" . $title . "'>";
            } else {
                echo "<div class='default-day-image'>
                <h2>Day " . $dayCount . "</h2>
            </div>";
            }

            echo "</header>
            <div class='container'>
            <div class='day-header'>
                <div class='day-number-badge'>Day {$dayCount}</div>
                <h2 class='day-title'>{$title}</h2>
            </div>
            <div class='day-content'>
                <p class='day-description'>{$dayOverview}</p>
            </div>
            </div>";

            $dayCount++;
            $j++;
            echo '</section>';
        }
        ?>

        <section class="page days-page">
            <header class="hero">
                <div class='default-day-image greenGradient'>
                    <h2>Inclusions</h2>
                </div>
            </header>

            <div class="container">

                <div class="climax-section">

                    <div class="tour_info_wrapper">

                        <!-- What's Included -->
                        <div class="tour_info_card tour_included">
                            <h3 class="tour_info_title">What's Included:</h3>

                            <div class="tour_info_grid">
                                <ul class="tour_info_list">
                                    <?php foreach ($inclusions as $inclusion) { ?>
                                    <?php $inclusion = trim($inclusion); ?>
                                    <?php if (!empty($inclusion)) { ?>
                                    <li class="flex items-center">
                                        <div class="tour_check">
                                            <i class="ri-check-line"></i>
                                        </div>
                                        <span><?php echo htmlspecialchars(ucfirst($inclusion)); ?></span>
                                    </li>
                                    <?php } ?>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>



                    </div>



                </div>

            </div>

        </section>

        <section class="page days-page">
            <header class="hero">
                <div class='default-day-image redGradient'>
                    <h2>Exclusions</h2>
                </div>
            </header>

            <div class="container">

                <div class="climax-section">

                    <div class="tour_info_wrapper">


                        <div class="tour_info_card tour_excluded">
                            <h3 class="tour_info_title">Not Included in Package:</h3>

                            <div class="tour_info_grid">
                                <ul class="tour_info_list">
                                    <?php foreach ($exclusions as $exclusion) { ?>
                                    <?php $exclusion = trim($exclusion); ?>
                                    <?php if (!empty($exclusion)) { ?>
                                    <li class="flex items-center">
                                        <div class="tour_cross">
                                            <i class="ri-close-line"></i>
                                        </div>
                                        <span><?php echo htmlspecialchars(ucfirst($exclusion)); ?></span>
                                    </li>
                                    <?php } ?>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>

                    </div>



                </div>

            </div>

        </section>

        <!-- payment section -->

        <section class="page days-page">
            <header class="hero">
                <div class='default-day-image'>
                    <h2>Pricing</h2>
                </div>
            </header>

            <div class="container">
                <div class="climax-section">
                    <div class="tour_container">
                        <h2 class="tour_title">Tour Pricing</h2>
                        <div class="tour_card">
                            <div class="tour_card_header">
                                <div>
                                    <h3 class="tour_package_title">
                                        <?php echo $tour_package_title ?> - Quotation
                                    </h3>
                                    <p class="tour_package_meta">
                                        <?php $passangerInfo = ($numberOfAdults == 1 ? '1 Adult' : $numberOfAdults . ' Adults') . (($numberOfChilds != 0) ? ', ' . ($numberOfChilds == 1 ? '1 Child' : $numberOfChilds . ' Children') : '') . (($numberOfInfants != 0) ? ', ' . ($numberOfInfants == 1 ? '1 Infant' : $numberOfInfants . ' Infants') : '');
                                        echo $passangerInfo; ?>
                                        |
                                        <?php echo ucwords(str_replace('_', ' ', $tour_package_hotel)) ?> Accommodation
                                        |
                                        <?php echo $numberOfDays; ?> Nights, <?php echo $days; ?> Days
                                    </p>
                                </div>
                                <span class="tour_badge">Best Value</span>
                            </div>

                            <hr class="tour_divider" />

                            <div class="tour_price_row">
                                <span><i class="ri-hotel-line"></i> Base Price ( <?php echo $passangerInfo ?> )</span>
                                <span>₹ <?php echo number_format($total_base_price, 2); ?></span>
                            </div>

                            <div class="tour_price_row">
                                <span><i class="ri-percent-line text-primary"></i> GST ( <?php echo $gst_percent; ?>%
                                    )</span>
                                <span>+ ₹ <?php echo number_format($gst_amount, 2); ?></span>
                            </div>

                            <?php

                            if ($total_discount != 0) { ?>
                            <div class="tour_price_row tour_discount">
                                <span><i class="ri-gift-line text-green-600"></i> Special Discount</span>
                                <span>- ₹ <?php echo number_format(str_replace('-', '', $total_discount), 2); ?></span>
                            </div>
                            <?php
                            } ?>

                            <div class="tour_total_section">
                                <div></div>
                                <div class="text-right">

                                    <div class="tour_total_text">
                                        <p>Total Price</p>
                                        <h1>₹ <?php echo number_format($final_price, 2); ?></h1>
                                    </div>

                                    <?php

                                    if ($total_discount != 0) { ?>
                                    <div class="tour_save_box">
                                        <span class="tour_old_price">₹
                                            <?php echo number_format($mrp_gst_total, 2); ?></span>
                                        <span class="tour_save_badge">Save <?php echo $discount_percent; ?> %</span>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <a href="https://<?php echo SITE_DOMAIN ?>/book-now/?id=<?php echo base64_encode($quotationId) ?>"
                                class="tour_btn">Click here to Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const pages = document.querySelectorAll('.page');
        const totalPages = pages.length;

        pages.forEach((page, index) => {
            const footer = document.createElement('div');
            const pageNumber = "Page " + (index + 1) + " of " + totalPages;
            footer.innerHTML =
                "<footer style=\"position: relative; width: 100%;\">" +
                "<div class='bottom-decor' style=\"display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px;\">" +
                "<img src='<?php echo SITE_LOGO; ?>' class='center-logo'>" +
                "<span style=\"font-size: 12px; color: #666; letter-spacing: 1px; white-space: nowrap;\"><?php echo COMPANY_SITE_DOMAIN ?></span>" +
                "</div>" +
                "<div style=\"display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:nowrap; margin: 0px 50px\">" +
                "<span style=\"font-size: 12px; color: #666; white-space: nowrap;\">Powered by Aleya.in</span>" +
                "<span style=\"font-size: 12px; color: #666; white-space: nowrap;\">" + pageNumber +
                "</span>" +
                "</div>" +
                "</footer>";
            footer.style.position = 'absolute';
            footer.style.bottom = '20px';
            footer.style.width = '100%';
            page.appendChild(footer);
        });

        // Generate SVG with random smooth mountain-like shapes
        function generateWavePath(heroIndex = 0) {
            const steps = 25 + Math.floor(Math.random() * 15); // Fewer points for ultra smooth curves
            const points = [];

            // Generate random height points with varied patterns
            for (let i = 0; i <= steps; i++) {
                const progress = i / steps;
                const x = progress * 100;

                // Create mountain peaks with varied heights
                const peakFreq1 = 1.5 + Math.random() * 2.5;
                const peakFreq2 = 2 + Math.random() * 3;
                const peakFreq3 = 0.5 + Math.random() * 1.5;

                const amplitude1 = 8 + Math.random() * 18;
                const amplitude2 = 5 + Math.random() * 12;
                const amplitude3 = 3 + Math.random() * 8;

                // Multiple wave layers for organic randomness
                const wave1 = Math.sin(progress * Math.PI * peakFreq1) * amplitude1;
                const wave2 = Math.cos(progress * Math.PI * peakFreq2 + Math.random() * 0.3) * amplitude2;
                const wave3 = Math.sin(progress * Math.PI * peakFreq3 + Math.random() * 0.5) * amplitude3;

                // Add occasional dramatic peaks
                const dramaticPeak = (Math.random() > 0.75) ? (Math.random() * 15) : 0;

                // Smooth noise
                const noise = (Math.random() - 0.5) * 5;

                const baseHeight = 20 + Math.random() * 8;
                const y = Math.max(3, Math.min(55, baseHeight + wave1 + wave2 + wave3 + dramaticPeak + noise));
                points.push({
                    x,
                    y
                });
            }

            // Start from bottom left
            let pathData = 'M 0,100 L 0,100';

            // Create ultra-smooth curves using cubic bezier (C command)
            for (let i = 0; i < points.length; i++) {
                if (i === 0) {
                    pathData += ` L ${points[i].x.toFixed(2)},${points[i].y.toFixed(2)}`;
                } else {
                    const prevPoint = points[i - 1];
                    const currPoint = points[i];

                    // Calculate smooth control points for cubic bezier
                    const tension = 0.3 + Math.random() * 0.2; // Randomize smoothness
                    const dx = currPoint.x - prevPoint.x;

                    const cp1x = prevPoint.x + dx * tension;
                    const cp1y = prevPoint.y;
                    const cp2x = currPoint.x - dx * tension;
                    const cp2y = currPoint.y;

                    pathData +=
                        ` C ${cp1x.toFixed(2)},${cp1y.toFixed(2)} ${cp2x.toFixed(2)},${cp2y.toFixed(2)} ${currPoint.x.toFixed(2)},${currPoint.y.toFixed(2)}`;
                }
            }

            // Complete the path back to start
            pathData += ' L 100,100 Z';

            return pathData;
        }

        // Apply SVG wave overlays to ALL hero sections
        const heroElements = document.querySelectorAll('.hero');

        heroElements.forEach((hero, index) => {
            // Create white div overlay at bottom of hero
            const waveContainer = document.createElement('div');
            waveContainer.className = 'hero-wave-container';
            waveContainer.style.position = 'absolute';
            waveContainer.style.bottom = '0';
            waveContainer.style.left = '0';
            waveContainer.style.width = '100%';
            waveContainer.style.height = '40px'; // Increased for taller spikes
            waveContainer.style.zIndex = '5';
            waveContainer.style.overflow = 'hidden';

            // Create SVG for wave shape
            const wavePath = generateWavePath(index);
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', '0 0 100 100');
            svg.setAttribute('preserveAspectRatio', 'none');
            svg.style.width = '100%';
            svg.style.height = '100%';
            svg.style.display = 'block';

            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', wavePath);
            path.setAttribute('fill', '#ffffff');
            path.setAttribute('stroke', 'none');

            svg.appendChild(path);
            waveContainer.appendChild(svg);
            hero.appendChild(waveContainer);
        });

        // Randomize wave overlay variables per default day image for unique gradients
        const fallbackBlocks = document.querySelectorAll('.default-day-image');
        fallbackBlocks.forEach(block => {
            const angle = Math.round(Math.random() * 90); // 0-90deg
            const strength = (8 + Math.random() * 8).toFixed(2) + '%'; // 8-16%
            const shift = (55 + Math.random() * 10).toFixed(2) + '%'; // 55-65%
            block.style.setProperty('--wave-angle', `${angle}deg`);
            block.style.setProperty('--wave-strength', strength);
            block.style.setProperty('--wave-shift', shift);
        });
    });

    // Auto-generate PDF on page load
    document.addEventListener("DOMContentLoaded", async () => {
        const loaderProgress = document.getElementById('loader-progress');
        const loaderOverlay = document.getElementById('pdf-loader');
        const contentWrapper = document.getElementById('content-wrapper');

        try {
            // Wait for all effects to render
            loaderProgress.textContent = 'Preparing pages...';
            await new Promise(resolve => setTimeout(resolve, 500));

            const pages = document.querySelectorAll(".page");
            const {
                jsPDF
            } = window.jspdf;
            const pdf = new jsPDF('p', 'pt', 'a4');
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            for (let i = 0; i < pages.length; i++) {
                loaderProgress.textContent = `Processing page ${i + 1} of ${pages.length}...`;

                const canvas = await html2canvas(pages[i], {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    windowWidth: pages[i].scrollWidth,
                    windowHeight: pages[i].scrollHeight
                });
                const imgData = canvas.toDataURL('image/jpeg', 0.95);

                const imgProps = pdf.getImageProperties(imgData);
                const pdfImgHeight = (imgProps.height * pageWidth) / imgProps.width;

                pdf.addImage(imgData, 'JPEG', 0, 0, pageWidth, pdfImgHeight);

                if (i < pages.length - 1) pdf.addPage();
            }

            loaderProgress.textContent = 'Saving PDF...';
            pdf.save("<?php echo $pdfFileName ?>");

            // Wait a moment then show the webpage
            await new Promise(resolve => setTimeout(resolve, 800));
            loaderOverlay.style.display = 'none';
            contentWrapper.classList.add('show');
        } catch (error) {
            console.error('PDF generation error:', error);
            loaderProgress.textContent = 'Error generating PDF. Showing webpage...';
            await new Promise(resolve => setTimeout(resolve, 1500));
            loaderOverlay.style.display = 'none';
            contentWrapper.classList.add('show');
        }
    });
    </script>
</body>

</html>