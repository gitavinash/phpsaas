<?php

function getInitials($name)
{
    $words = preg_split("/\s+/", trim($name));
    $initials = "";
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper($word[0]);
        }
    }
    return $initials;
}

$quotationId = base64_decode(end($urlStructure));

$query =
    "SELECT * FROM " .
    TABLE_PREFIX .
    "quotation 
    INNER JOIN " .
    TABLE_PREFIX .
    "tour_package ON quotation_type_id = tour_package_id
    INNER JOIN " .
    TABLE_PREFIX .
    "enquiry ON enquiry_id = quotation_ref_id
    LEFT JOIN " . TABLE_PREFIX . "gallery ON gallery_category = tour_package_id AND gallery_site_id = " . SITE_ID . "
    LEFT JOIN " . TABLE_PREFIX . "enquiry_profile ON enquiry_profile_ref_id = enquiry_id AND status=1
    WHERE quotation_id = '{$quotationId}' 
    AND quotation_type = 'tour_package' 
    AND quotation_status IN (0,1) 
    AND quotation_site_id = " .
    SITE_ID;

if ($checkQuote = mysqli_query($connection, $query)) {
    if (mysqli_num_rows($checkQuote) > 0) {
        extract(mysqli_fetch_assoc($checkQuote));
        extract(json_decode($enquiry_message, true));
    } else {
        die("Quotation not found or inactive.");
    }
} else {
    die("Database query failed: " . mysqli_error($connection));
}

$quoteId = '#' . getInitials(SITE_NAME) . '/' . base64_encode($quotation_ref_id) . '/' . $quotationId;
$pdfFileName = strtolower(str_replace(' ', '_', $enquiry_first_name . ' ' . $enquiry_last_name . ' ' . $tour_package_title) . '_itinerary_' . time() . '.pdf');
$inclusions = explode(',', $tour_package_inclusions);
$exclusions = explode(',', $tour_package_exclusions);
$itineraries = json_decode($tour_package_itinerary, true);
$dayCount = 1;
$totalAdultPerson = isset($number_of_adults) ? $number_of_adults : 1;
$totalChildPerson = isset($number_of_childrens) ? $number_of_childrens : 0;
$numberOfDays = isset($number_of_days) ? $number_of_days : 4;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tour_package_title . ' | ' . SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
    :where([class^="ri-"])::before {
        content: "\f3c2";
    }

    body {
        font-family: 'Inter', sans-serif;
    }

    .itinerary-day:not(:last-child) {
        border-bottom: 1px solid #e5e7eb;
    }

    .pdf-page {
        page-break-before: always;
        break-before: page;
    }

    .avoid-break {
        page-break-inside: avoid;
        break-inside: avoid;
    }
    </style>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#E76317',
                    secondary: '#FB923C'
                },
                borderRadius: {
                    'none': '0px',
                    'sm': '4px',
                    DEFAULT: '8px',
                    'md': '12px',
                    'lg': '16px',
                    'xl': '20px',
                    '2xl': '24px',
                    '3xl': '32px',
                    'full': '9999px',
                    'button': '8px'
                }
            }
        }
    }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>

<body class="bg-gray-50">
    <div id="pdf-content"
        class="max-w-4xl mx-auto bg-white shadow-sm rounded-lg overflow-hidden max-w-[760px] mx-auto bg-white">
        <!-- Header Section -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="<?php echo SITE_LOGO ?>" style="height: 50px;" alt="<?php echo SITE_NAME ?>">
                </div>
                <div class="flex items-center gap-6 text-gray-600 text-sm">
                    <a href="mailto:info@logotravel.com" class="flex items-center gap-1 hover:text-primary">
                        <i class="ri-mail-line"></i>
                        <span><?php echo SITE_EMAIL ?></span>
                    </a>
                    <a href="tel:+919876543210" class="flex items-center gap-1 hover:text-primary">
                        <i class="ri-phone-line"></i>
                        <span><?php echo SITE_MOBILE ?></span>
                    </a>
                    <a href="https://www.logotravel.com" class="flex items-center gap-1 hover:text-primary">
                        <i class="ri-global-line"></i>
                        <span><?php echo SITE_DOMAIN ?></span>
                    </a>
                </div>
            </div>
        </div>
        <!-- Hero Section -->
        <div class="relative h-[400px] overflow-hidden">
            <img src="<?php echo $gallery_image ?>" alt="<?php echo $tour_package_title ?>"
                class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                <div class="max-w-4xl mx-auto">
                    <div class="flex justify-between items-end">
                        <div>
                            <h1 class="text-4xl font-bold mb-3 drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)]">
                                <?php echo $tour_package_title ?></h1>
                            <div class="flex items-center gap-6 text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.8)]">
                                <div class="flex items-center gap-2">
                                    <i class="ri-map-pin-line"></i>
                                    <span><?php echo $tour_package_city ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="ri-calendar-line"></i>
                                    <span><?php echo date('d F, Y', strtotime($travel_date)) ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="ri-time-line"></i>
                                    <span><?php echo $numberOfDays + 1 ?> Days</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <div class="text-3xl font-bold whitespace-nowrap">INR <?php echo $quotation_price ?></div>
                            <div class="text-sm text-white/75 whitespace-nowrap mb-2">per person</div>
                            <div class="text-sm text-white/75 whitespace-nowrap">Ref. id: <?php echo $quoteId ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tour Overview -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold mb-4">Tour Overview</h2>
            <p class="mb-8"><?php echo $tour_package_overview ?></p>
            <!-- Hotel List -->
            <!-- <div class="mt-8">
                <h3 class="text-lg font-semibold mb-4">Accommodation Details</h3>
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-start">
                            <img src="https://readdy.ai/api/search-image?query=Luxury%20hotel%20room%20interior%20with%20modern%20design%2C%20king%20size%20bed%2C%20city%20view%2C%20warm%20lighting%2C%20professional%20hotel%20photography&width=300&height=200&seq=10&orientation=landscape"
                                alt="Melia Vinpearl Da Nang" class="w-32 h-24 object-cover rounded mr-4">
                            <div class="flex-1">
                                <h4 class="font-medium">Melia Vinpearl Da Nang</h4>
                                <div class="flex items-center text-sm text-gray-600 mt-1">
                                    <div class="flex items-center">
                                        <i class="ri-star-fill text-yellow-400"></i>
                                        <i class="ri-star-fill text-yellow-400"></i>
                                        <i class="ri-star-fill text-yellow-400"></i>
                                        <i class="ri-star-fill text-yellow-400"></i>
                                        <i class="ri-star-line text-yellow-400"></i>
                                    </div>
                                    <span class="mx-2">•</span>
                                    <span>4 Nights</span>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    <div class="flex items-center gap-4">
                                        <span><i class="ri-wifi-line mr-1"></i> Free WiFi</span>
                                        <span><i class="ri-restaurant-line mr-1"></i> Restaurant</span>
                                        <span><i class="ri-parking-line mr-1"></i> Parking</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="bg-blue-50 p-4 rounded-lg mb-4 mt-8">
                <h3 class="font-medium mb-2">What's Included:</h3>
                <ul class="grid grid-cols-2 gap-2 text-sm">
                    <?php foreach ($inclusions as $inclusion) { ?>
                    <?php $inclusion = trim($inclusion); ?>
                    <?php if (!empty($inclusion)) { ?>
                    <li class="flex items-center">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                            <i class="ri-check-line"></i>
                        </div>
                        <span><?php echo htmlspecialchars(ucfirst($inclusion)); ?></span>
                    </li>
                    <?php } ?>
                    <?php } ?>
                </ul>
            </div>
            <!-- <div class="bg-amber-50 p-4 rounded-lg">
                <h3 class="font-medium mb-2">Important Notes:</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <div class="w-5 h-5 flex items-center justify-center text-amber-600 mr-2 mt-0.5">
                            <i class="ri-information-line"></i>
                        </div>
                        <span>Comfortable walking shoes recommended for temple visits</span>
                    </li>
                    <li class="flex items-start">
                        <div class="w-5 h-5 flex items-center justify-center text-amber-600 mr-2 mt-0.5">
                            <i class="ri-information-line"></i>
                        </div>
                        <span>Tour itinerary may change due to weather conditions</span>
                    </li>
                    <li class="flex items-start">
                        <div class="w-5 h-5 flex items-center justify-center text-amber-600 mr-2 mt-0.5">
                            <i class="ri-information-line"></i>
                        </div>
                        <span>Modest clothing required for temple visits</span>
                    </li>
                </ul>
            </div> -->
        </div>
        <section class="pdf-page">
            <!-- Itinerary Details -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold mb-4">Detailed Itinerary</h2>
                <?php
                echo '<div class="space-y-6">';
                foreach ($itineraries as $day) {
                    // Extract title and remove "Day X - " prefix
                    $titleParts = explode(' - ', $day['day'], 2);
                    $title = isset($titleParts[1]) ? $titleParts[1] : $titleParts[0];

                    echo '
    <div class="itinerary-day pb-6">
        <div class="flex items-center mb-3">
            <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                <span class="font-medium">' . $dayCount . '</span>
            </div>
            <h3 class="text-lg font-medium">' . htmlspecialchars($title) . '</h3>
        </div>
        <div class="pl-14">
            <p class="mb-3">' . nl2br(htmlspecialchars($day['overview'])) . '</p>
            <div class="text-sm text-gray-500 mb-3">
                <div class="flex items-center">
                    <div class="w-5 h-5 flex items-center justify-center mr-1">
                        <i class="ri-hotel-line"></i>
                    </div>
                    <span>Accommodation: Included</span>
                </div>
                <div class="flex items-center">
                    <div class="w-5 h-5 flex items-center justify-center mr-1">
                        <i class="ri-restaurant-line"></i>
                    </div>
                    <span>Meals: Included</span>
                </div>
            </div>
        </div>
    </div>';
                    $dayCount++;
                }
                echo '</div>';
                ?>

            </div>
        </section>


        <?php
        // Pre-calculation
        $days = $numberOfDays + 1;
        $persons = $totalAdultPerson;
        $base_price_per_person = $quotation_price;
        $mrp_per_person = $quotation_mrp;

        $total_base_price = $base_price_per_person * $persons;
        $total_mrp_price = $mrp_per_person * $persons;

        $gst_percent = 5;
        $gst_amount = $total_base_price * ($gst_percent / 100);
        $mrp_gst_total = $total_mrp_price + ($total_mrp_price * $gst_percent / 100);

        $final_price = $total_base_price + $gst_amount;
        $single_person_total = $base_price_per_person + ($base_price_per_person * $gst_percent / 100);

        $total_discount = $mrp_gst_total - $final_price;
        $discount_percent = round(($total_discount / $mrp_gst_total) * 100);
        ?>

        <section class="pdf-page">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold mb-4">Tour Pricing</h2>

                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-5">
                    <div class="bg-primary/10 p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-800">Standard Package</h3>
                            <div class="px-3 py-1 bg-primary text-white rounded-full text-sm font-medium">Best Value
                            </div>
                        </div>
                        <p class="text-gray-600 mt-1">
                            <?php echo ($persons == 1 ? '1 Adult' : $persons . ' Adults'); ?> |
                            4-Star Accommodation |
                            <?php echo $days; ?> Days, <?php echo $numberOfDays; ?> Nights
                        </p>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="space-y-1">
                                <div class="text-3xl font-bold text-gray-900">
                                    ₹<?php echo number_format($final_price, 2); ?>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-lg text-gray-500 line-through">
                                        ₹<?php echo number_format($mrp_gst_total, 2); ?>
                                    </span>
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-sm font-medium">
                                        Save <?php echo $discount_percent; ?>%
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Per Package</div>
                                <div class="text-sm text-gray-500">
                                    (₹<?php echo number_format($single_person_total, 2); ?> per person)
                                </div>
                            </div>
                        </div>

                        <!-- Breakdown -->
                        <div class="space-y-3 border-t border-b border-gray-200 py-4 mb-6">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <i class="ri-hotel-line text-primary"></i>
                                    <span>Base Price
                                        (<?php echo ($persons == 1 ? '1 Adult' : $persons . ' Adults'); ?>)</span>
                                </div>
                                <span class="font-medium">₹<?php echo number_format($total_base_price, 2); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <i class="ri-percent-line text-primary"></i>
                                    <span>GST (<?php echo $gst_percent; ?>%)</span>
                                </div>
                                <span class="font-medium">+ ₹<?php echo number_format($gst_amount, 2); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <i class="ri-gift-line text-green-600"></i>
                                    <span>Special Discount</span>
                                </div>
                                <span class="font-medium text-green-600">-
                                    ₹<?php echo number_format($total_discount, 2); ?></span>
                            </div>
                        </div>

                        <!-- Offer -->
                        <div class="bg-orange-50 p-4 rounded-lg mb-6">
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white shrink-0">
                                    <i class="ri-gift-line"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">Limited Time Offer!</p>
                                    <p class="text-gray-600">Free airport transfers worth ₹5,000</p>
                                </div>
                            </div>
                        </div>

                        <button
                            class="w-full bg-primary hover:bg-primary/90 text-white py-4 px-6 rounded-button font-medium text-lg whitespace-nowrap">
                            Book Now & Save <?php echo $discount_percent; ?>%
                        </button>
                    </div>
                </div>

                <!-- Notes -->
                <div class="space-y-2">
                    <div class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="ri-calendar-line mt-0.5"></i>
                        <span>Prices valid until December 31, 2025</span>
                    </div>
                    <div class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="ri-user-star-line mt-0.5"></i>
                        <span>Children aged 2–11 receive 25% discount when sharing room with adults</span>
                    </div>
                    <div class="flex items-start gap-2 text-sm text-gray-600">
                        <i class="ri-group-line mt-0.5"></i>
                        <span>Minimum 2 participants required for guaranteed departure</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Powered By Section -->
        <div class="p-4 bg-gray-100 text-center text-sm text-gray-600">
            Powered by <span class="font-medium">Aleya</span> - A Smart Travel ERP Software
        </div>

    </div>
    <!-- Download Section -->
    <!-- <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Download Itinerary</h2>
        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white mr-4">
                    <div class="w-6 h-6 flex items-center justify-center">
                        <i class="ri-file-pdf-line ri-xl"></i>
                    </div>
                </div>
                <div>
                    <h3 class="font-medium">Da Nang Highlights Tour - Detailed Itinerary</h3>
                    <p class="text-sm text-gray-600">PDF document (1.2 MB)</p>
                </div>
            </div>
            <button id="downloadItinerary"
                class="bg-primary hover:bg-primary/90 text-white py-2 px-4 rounded-button font-medium whitespace-nowrap flex items-center">
                <div class="w-5 h-5 flex items-center justify-center mr-1">
                    <i class="ri-download-line"></i>
                </div>
                Download PDF
            </button>
        </div>
    </div> -->
    <!-- App Promotion -->
    <!-- <div class="p-6 bg-blue-800 text-white">
        <div class="flex items-center">
            <div class="flex-1">
                <h2 class="text-xl font-bold mb-2">Your Personal Travel Assistant</h2>
                <p class="mb-3">Download our app for exclusive deals, offline itineraries, and real-time updates.
                </p>
                <div class="flex space-x-3">
                    <button
                        class="bg-black hover:bg-black/90 text-white py-2 px-4 rounded-button font-medium whitespace-nowrap flex items-center">
                        <div class="w-5 h-5 flex items-center justify-center mr-1">
                            <i class="ri-apple-fill"></i>
                        </div>
                        App Store
                    </button>
                    <button
                        class="bg-black hover:bg-black/90 text-white py-2 px-4 rounded-button font-medium whitespace-nowrap flex items-center">
                        <div class="w-5 h-5 flex items-center justify-center mr-1">
                            <i class="ri-google-play-fill"></i>
                        </div>
                        Google Play
                    </button>
                </div>
            </div>
            <div class="hidden md:block">
                <img src="https://readdy.ai/api/search-image?query=Smartphone%20showing%20travel%20app%20interface%20with%20itinerary%20and%20map%2C%20clean%20modern%20UI%20design%2C%20blue%20theme%2C%20professional%20product%20photography%20on%20white%20background&width=200&height=400&seq=15&orientation=portrait"
                    alt="Travel App" class="h-40 object-contain">
            </div>
        </div>
    </div>
    </div> -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const element = document.getElementById("pdf-content");

        if (!element) return;

        const opt = {
            filename: '<?php echo $pdfFileName ?>',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                useCORS: true,
                allowTaint: false,
                scrollY: 0,
            },
            jsPDF: {
                unit: 'in',
                format: 'a4', // A4 portrait
                orientation: 'portrait'
            },
            pagebreak: {
                mode: ['css', 'legacy'],
                before: '.pdf-page',
                avoid: ['.avoid-break']
            }
        };

        const cloned = element.cloneNode(true);
        cloned.style.background = "white";

        const wrapper = document.createElement("div");
        wrapper.style.width = "100%";
        wrapper.style.maxWidth = "1000px";
        wrapper.style.margin = "0 auto";
        wrapper.style.padding = "0";
        wrapper.appendChild(cloned);
        document.body.appendChild(wrapper);

        html2pdf().set(opt).from(cloned).save().then(() => {
            document.body.removeChild(wrapper);
        });
    });
    </script>

</body>

</html>