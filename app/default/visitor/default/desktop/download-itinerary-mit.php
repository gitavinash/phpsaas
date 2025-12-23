<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tour_package_title . ' | ' . SITE_NAME ?></title>
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
</head>

<body>

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
                    <p><strong><?php echo $numberOfDays ?> Nights / <?php echo $numberOfDays - 1 ?> Days</strong></p>
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

    <section class="page">
        <header class="hero">
            <img src="https://amanmishra1412.github.io/HTMLToPDF/image/hero 2.png" alt="Rajasthan Fort">
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

    <section class="page">
        <header class="hero">
            <img src="https://amanmishra1412.github.io/HTMLToPDF/image/hero 2.png" alt="Rajasthan Fort">
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
            <img src="https://amanmishra1412.github.io/HTMLToPDF/image/hero 6.png" alt="Rajasthan Fort">
        </header>

        <div class="container">
            <div class="climax-section">
                <div class="tour_container">
                    <h2 class="tour_title">Tour Pricing</h2>
                    <div class="tour_card">
                        <div class="tour_card_header">
                            <div>
                                <h3 class="tour_package_title">
                                    Manali Kasol Jibhi Volvo Tour Package 5N 6D - Quotation
                                </h3>
                                <p class="tour_package_meta">
                                    1 Adult | 3 Star Hotel Accommodation | 0 Nights, 1 Days
                                </p>
                            </div>
                            <span class="tour_badge">Best Value</span>
                        </div>

                        <hr class="tour_divider" />

                        <div class="tour_price_row">
                            <span><i class="ri-hotel-line"></i> Base Price ( 1 Adult )</span>
                            <span>₹0.00</span>
                        </div>

                        <div class="tour_price_row">
                            <span><i class="ri-percent-line text-primary"></i> GST ( 5% )</span>
                            <span>+ ₹0.00</span>
                        </div>

                        <div class="tour_price_row tour_discount">
                            <span><i class="ri-gift-line text-green-600"></i> Special Discount</span>
                            <span>- ₹13,125.00</span>
                        </div>

                        <div class="tour_total_section">
                            <div></div>
                            <div class="text-right">

                                <div class="tour_total_text">
                                    <p>Total Price</p>
                                    <h1>₹0.00</h1>
                                </div>

                                <div class="tour_save_box">
                                    <span class="tour_old_price">₹13,125.00</span>
                                    <span class="tour_save_badge">Save 100%</span>
                                </div>
                            </div>
                        </div>

                        <button class="tour_btn">Click here to Book Now</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    document.addEventListener("DOMContentLoaded", async () => {
        const pages = document.querySelectorAll(".page");
        const {
            jsPDF
        } = window.jspdf;
        const pdf = new jsPDF('p', 'pt', 'a4');
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();

        for (let i = 0; i < pages.length; i++) {
            const canvas = await html2canvas(pages[i], {
                scale: 1.5,
                useCORS: true
            });
            const imgData = canvas.toDataURL('image/jpeg', 0.8);

            const imgProps = pdf.getImageProperties(imgData);
            const pdfImgHeight = (imgProps.height * pageWidth) / imgProps.width;

            pdf.addImage(imgData, 'JPEG', 0, 0, pageWidth, pdfImgHeight);

            if (i < pages.length - 1) pdf.addPage();
        }

        pdf.save("<?php echo $pdfFileName ?>");
    });
    </script>
</body>

</html>