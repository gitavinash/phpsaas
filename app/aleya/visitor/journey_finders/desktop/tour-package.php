<?php

function renderItems($htmlString, $iconClass = 'ri-check-line', $iconColor = 'text-primary')
{
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $htmlString);
$xpath = new DOMXPath($doc);

$items = $xpath->query('//p');

if ($items->length === 0 || trim($items[0]->textContent) === '') {
$items = $xpath->query('//ol/li');
}

echo '<ul class="space-y-2">';
    foreach ($items as $item) {
    $text = trim($item->textContent);
    if (!empty($text)) {
    echo '
    <li class="flex items-start">
        <div class="w-5 h-5 flex items-center justify-center ' . $iconColor . ' mr-2 mt-0.5">
            <i class="' . $iconClass . '"></i>
        </div>
        <span class="text-gray-700">' . htmlspecialchars($text) . '</span>
    </li>';
    }
    }
    echo '
</ul>';
}

require_once 'header.php';

?>
<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="#" class="hover:text-primary">Home</a>
            <span class="mx-2">/</span>
            <a href="#" class="hover:text-primary">Holiday</a>
            <span class="mx-2">/</span>
            <a href="#" class="hover:text-primary"><?php echo $tour_package_country ?></a>
            <span class="mx-2">/</span>
            <a href="#" class="hover:text-primary"><?php echo $tour_package_city ?></a>
            <span class="mx-2">/</span>
            <span class="text-gray-700 font-medium"><?php echo $tour_package_title ?></span>
        </div>
    </div>
    <!-- Hero Section -->
    <div class="relative rounded-xl overflow-hidden mb-8">
        <img src="<?php echo $gallery_image ?>" alt="<?php echo $tour_package_title ?>"
            class="w-full h-80 object-cover object-center" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-6 md:p-8">
            <div class="flex items-center mb-2">
                <span
                    class="bg-primary text-white text-xs font-semibold px-2 py-1 rounded-full mr-2"><?php echo ucwords($tour_package_theme) ?></span>
                <span
                    class="bg-white/90 backdrop-blur-sm text-gray-800 text-xs font-semibold px-2 py-1 rounded-full"><?php echo $tour_package_duration + 1 ?>
                    Days</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                <?php echo $tour_package_title ?>
            </h1>
            <div class="flex items-center text-white/90 mb-4">
                <div class="flex items-center mr-4">
                    <div class="w-5 h-5 flex items-center justify-center mr-1">
                        <i class="ri-map-pin-line"></i>
                    </div>
                    <span><?php echo $tour_package_city . ', ' . $tour_package_country ?></span>
                </div>
                <div class="flex items-center mr-4">
                    <div class="w-5 h-5 flex items-center justify-center mr-1">
                        <i class="ri-group-line"></i>
                    </div>
                    <span>Min 2 people</span>
                </div>
            </div>
            <div class="flex items-center">
                <div class="text-white mr-4">
                    <span class="text-lg font-bold">₹ <?php echo $tour_package_price_selling_adult ?></span>
                    <span class="text-white/80 text-sm">per person</span>
                </div>
                <a href="#enquiry-form"
                    class="bg-primary text-white px-6 py-2 !rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition">Enquire
                    Now</a>
            </div>
        </div>
    </div>
    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Tour Details -->
        <div class="lg:col-span-2">
            <!-- Overview Section -->
            <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-2xl font-bold mb-4">Tour Overview</h2>
                <p class="text-gray-700 mb-6">
                    <?php echo $tour_package_overview ?>
                </p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="w-10 h-10 flex items-center justify-center text-primary mb-2">
                            <i class="ri-calendar-line ri-xl"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Duration</h3>
                        <p class="text-gray-600"><?php echo $tour_package_duration + 1 ?> Days /
                            <?php echo $tour_package_duration ?> Nights</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="w-10 h-10 flex items-center justify-center text-primary mb-2">
                            <i class="ri-group-line ri-xl"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Group Size</h3>
                        <p class="text-gray-600">2-100+ People</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="w-10 h-10 flex items-center justify-center text-primary mb-2">
                            <i class="ri-hotel-line ri-xl"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Accommodation</h3>
                        <p class="text-gray-600">
                            <?php echo ucwords(str_replace('_', ' ', $tour_package_hotel)) ?></p>
                    </div>
                </div>
                <!-- <div class="flex flex-wrap gap-2">
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded">Cultural</span>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded">Adventure</span>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded">Nature</span>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded">Beach</span>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded">Wellness</span>
                </div> -->
            </section>

            <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-2xl font-bold mb-6"><?php echo ucwords($tour_package_city) ?> Day-by-Day
                    Itinerary</h2>
                <div class="space-y-4">
                    <?php
                    $tour_package_itinerary = json_decode($tour_package_itinerary, true);

                    foreach ($tour_package_itinerary as $index => $item) {
                        $dayNumber = $index + 1;
                        echo "
    <div class=\"border border-gray-200 rounded-lg overflow-hidden mb-4\">
        <div class=\"itinerary-accordion flex items-center justify-between bg-gray-50 p-4 cursor-pointer\">
            <div class=\"flex items-center\">
                <div class=\"w-8 h-8 flex items-center justify-center bg-primary text-white rounded-full mr-3 flex-shrink-0\">
                    <span class=\"text-sm font-medium\">$dayNumber</span>
                </div>
                <h3 class=\"font-medium\">{$item['day']}</h3>
            </div>
            <div class=\"w-6 h-6 flex items-center justify-center text-gray-500\">
                <i class=\"ri-arrow-down-s-line ri-lg accordion-icon\"></i>
            </div>
        </div>
        <div class=\"itinerary-accordion-content p-4 border-t border-gray-200\">
            <p class=\"text-gray-700\">{$item['overview']}</p>
        </div>
    </div>";
                    }
                    ?>
                    <!-- Show More Button -->
                    <button id="show-more-itinerary"
                        class="w-full py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition flex items-center justify-center">
                        <span>Show Full Itinerary</span>
                        <i class="ri-arrow-down-s-line ml-1"></i>
                    </button>
                </div>
            </section>
            <!-- Photo Gallery -->
            <!-- <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-2xl font-bold mb-4">Photo Gallery</h2>
                <div class="overflow-x-auto gallery-scrollbar pb-4">
                    <div class="flex space-x-4 min-w-max">
                        <div class="w-80 h-60 rounded-lg overflow-hidden">
                            <img src="#api/search-image?query=beautiful%2520Bali%2520Ubud%2520rice%2520terraces%2520with%2520traditional%2520irrigation%2520system%252C%2520lush%2520green%2520landscape%252C%2520perfect%2520sunny%2520day%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=400&height=300&seq=1002&orientation=landscape"
                                alt="Tegalalang Rice Terraces"
                                class="w-full h-full object-cover object-top hover:scale-105 transition duration-500" />
                        </div>
                        <div class="w-80 h-60 rounded-lg overflow-hidden">
                            <img src="#api/search-image?query=beautiful%2520Bali%2520Uluwatu%2520temple%2520on%2520cliff%2520edge%2520with%2520ocean%2520view%2520at%2520sunset%252C%2520traditional%2520architecture%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=400&height=300&seq=1003&orientation=landscape"
                                alt="Uluwatu Temple"
                                class="w-full h-full object-cover object-top hover:scale-105 transition duration-500" />
                        </div>
                        <div class="w-80 h-60 rounded-lg overflow-hidden">
                            <img src="#api/search-image?query=beautiful%2520Bali%2520traditional%2520dance%2520performance%2520with%2520colorful%2520costumes%252C%2520ornate%2520headdresses%252C%2520expressive%2520movements%252C%2520professional%2520cultural%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=400&height=300&seq=1004&orientation=landscape"
                                alt="Traditional Dance"
                                class="w-full h-full object-cover object-top hover:scale-105 transition duration-500" />
                        </div>
                        <div class="w-80 h-60 rounded-lg overflow-hidden">
                            <img src="#api/search-image?query=beautiful%2520Bali%2520Mount%2520Batur%2520volcano%2520sunrise%2520trek%2520with%2520panoramic%2520views%252C%2520misty%2520mountains%252C%2520golden%2520sunlight%252C%2520professional%2520landscape%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=400&height=300&seq=1005&orientation=landscape"
                                alt="Mount Batur Sunrise"
                                class="w-full h-full object-cover object-top hover:scale-105 transition duration-500" />
                        </div>
                        <div class="w-80 h-60 rounded-lg overflow-hidden">
                            <img src="#api/search-image?query=beautiful%2520Bali%2520beach%2520in%2520Nusa%2520Dua%2520with%2520white%2520sand%252C%2520turquoise%2520water%252C%2520palm%2520trees%252C%2520luxury%2520beach%2520umbrellas%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=400&height=300&seq=1006&orientation=landscape"
                                alt="Nusa Dua Beach"
                                class="w-full h-full object-cover object-top hover:scale-105 transition duration-500" />
                        </div>
                    </div>
                </div>
            </section> -->
            <!-- Included/Not Included -->
            <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-2xl font-bold mb-4">
                    What's Included/Not Included
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-3 flex items-center text-gray-900">
                            <div class="w-6 h-6 flex items-center justify-center text-green-500 mr-2">
                                <i class="ri-check-line"></i>
                            </div>
                            Included
                        </h3>
                        <?php

                        renderItems($tour_package_inclusions, 'ri-check-line', 'text-green-500');

                        ?>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-3 flex items-center text-gray-900">
                            <div class="w-6 h-6 flex items-center justify-center text-red-500 mr-2">
                                <i class="ri-close-line"></i>
                            </div>
                            Not Included
                        </h3>
                        <?php

                        renderItems($tour_package_exclusions, 'ri-close-line', 'text-red-500');

                        ?>
                    </div>
                </div>
            </section>
        </div>
        <!-- Right Column - Enquiry Form and Additional Info -->
        <div>
            <!-- Enquiry Form -->
            <section id="enquiry-form" class="bg-white rounded-xl shadow-sm p-6 mb-8 sticky top-24">
                <h2 class="text-2xl font-bold mb-4">Enquire About This Tour</h2>
                <p class="text-gray-600 mb-6">
                    Fill out the form below and our travel experts will get back to
                    you within 24 hours.
                </p>
                <form class="space-y-4" method="POST" action="/enquiry/">
                    <div>
                        <label for="full-name" class="block text-sm font-medium text-gray-700 mb-1">Full
                            Name *</label>
                        <input type="text" name="name" id="full-name"
                            class="form-input w-full px-4 py-2 border border-gray-300 rounded text-gray-700 text-sm"
                            placeholder="Enter your full name" required />
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email
                            Address *</label>
                        <input type="email" name="email" id="email"
                            class="form-input w-full px-4 py-2 border border-gray-300 rounded text-gray-700 text-sm"
                            placeholder="Enter your email address" required />
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number
                            *</label>
                        <input type="tel" id="phone" name='mobile'
                            class="form-input w-full px-4 py-2 border border-gray-300 rounded text-gray-700 text-sm"
                            placeholder="Enter your phone number" required />
                    </div>
                    <div>
                        <label for="travel-date" class="block text-sm font-medium text-gray-700 mb-1">Preferred Travel
                            Date</label>
                        <input type="date" id="travel-date" name="travel_date" min="<?php echo date('Y-m-d') ?>"
                            class="form-input custom-date-input w-full px-4 py-2 border border-gray-300 rounded text-gray-700 text-sm" />
                    </div>
                    <div class="flex items-start">
                        <input type="checkbox" id="privacy-policy" required class="mt-1 w-[35px] h-[30px]" required>
                        <label for="privacy-policy" class="ml-2 text-sm text-gray-600">
                            I agree to the
                            <a href="/privacy-policy/" class="text-primary hover:underline">Privacy Policy</a>
                            and consent to being contacted regarding my enquiry.
                        </label>
                    </div>
                    <input type="hidden" id="recaptcha_token" name="recaptcha_token">
                    <input type="hidden" name="type" value="enquiry">
                    <input type="hidden" name="action" value="new">
                    <input type="hidden" name="subject" value="<?php echo $tour_package_title ?>">
                    <input type="hidden" name="sender" value="<?php echo SITE_EMAIL_ALERT ?>">
                    <input type="hidden" name="redirect_url" value="https://journeyfinders.com/thank-you/">
                    <input type="submit" id="submit-enquiry"
                        class="w-full bg-primary text-white px-6 py-3 !rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition cursor-pointer"
                        value="Submit Enquiry">
                </form>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-4">
                        Prefer to speak with our travel experts?
                    </p>
                    <a href="tel:+9192112-87555"
                        class="flex items-center text-gray-700 font-medium hover:text-primary mb-2">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                            <i class="ri-phone-line"></i>
                        </div>
                        <span>+91 92112 87555</span>
                    </a>
                    <a href="mailto:info@journeyfinders.com"
                        class="flex items-center text-gray-700 font-medium hover:text-primary">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                            <i class="ri-mail-line"></i>
                        </div>
                        <span>info@journeyfinders.com</span>
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>
<div class="container mx-auto px-4 py-8">
    <?php

    require_once 'quick_links.php';

    ?>
</div>
<script>
// Itinerary accordions
const accordions = document.querySelectorAll('.itinerary-accordion');
accordions.forEach(accordion => {
    accordion.addEventListener('click', function() {
        const content = this.nextElementSibling;
        content.classList.toggle('active');
        const icon = this.querySelector('.accordion-icon');
        if (content.classList.contains('active')) {
            icon.classList.remove('ri-arrow-down-s-line');
            icon.classList.add('ri-arrow-up-s-line');
        } else {
            icon.classList.remove('ri-arrow-up-s-line');
            icon.classList.add('ri-arrow-down-s-line');
        }
    });
});
// Show first itinerary item by default
const firstAccordion = document.querySelector('.itinerary-accordion');
if (firstAccordion) {
    firstAccordion.click();
}
// Show More Itinerary button
const showMoreButton = document.getElementById('show-more-itinerary');
if (showMoreButton) {
    showMoreButton.addEventListener('click', function() {
        const hiddenAccordions = document.querySelectorAll('.itinerary-accordion:not(.active)');
        hiddenAccordions.forEach(accordion => {
            accordion.click();
        });
        this.style.display = 'none';
    });
}
</script>
<?php

require_once 'footer.php';

?>