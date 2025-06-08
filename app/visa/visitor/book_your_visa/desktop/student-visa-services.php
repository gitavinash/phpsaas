<?php

require_once 'header.php';

?>

<main>
    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 overflow-hidden">
        <div
            style="background-image: url('<?php echo USER_IMAGES ?>tourist-us-background.jpg'); background-size: cover; background-position: center; position: absolute; top: 0; right: 0; bottom: 0; left: 0; opacity: 0.15;">
        </div>
        <div class="container mx-auto px-4 relative">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Tourist Visa Services</h1>
                <p class="text-xl text-gray-700 mb-8">Explore the world with our hassle-free tourist visa services. We
                    provide expert assistance to ensure your travel dreams become reality with minimal paperwork and
                    maximum efficiency.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="/start-application/"
                        class="bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-8 py-3 cursor-pointer whitespace-nowrap">Apply
                        Now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Destinations Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Popular Destinations</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Explore our most requested tourist visa destinations with
                    competitive processing times and fees</p>
            </div>
            <div class="container mx-auto px-4">
                <div class="flex justify-end items-center mb-8">
                    <div class="flex space-x-2">
                        <button id="scroll-left"
                            class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">
                            <i class="ri-arrow-left-s-line text-gray-700"></i>
                        </button>
                        <button id="scroll-right"
                            class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">
                            <i class="ri-arrow-right-s-line text-gray-700"></i>
                        </button>
                    </div>
                </div>

                <div class="scroll-container overflow-x-auto pb-4" id="countryScroll">
                    <div class="flex space-x-6 min-w-max">
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
    AND JSON_CONTAINS(vc.visa_country_services, '\"student\"')
ORDER BY vc.visa_country_is_featured DESC, RAND()
LIMIT 10";

                        if ($popularCountry = mysqli_query($connection, $query)) {
                            if (mysqli_num_rows($popularCountry) > 0) {
                                while ($popularCountryData = mysqli_fetch_assoc($popularCountry)) {
                                    echo "
                                  <div class='visa-card bg-white rounded-lg shadow-md p-6 transition-all duration-300 w-72'>
                                        <div class='flex items-center mb-4'>
                                            <div class='w-10 h-10 flex items-center justify-center mr-3'>
                                                <img src='https://flagcdn.com/h80/" . strtolower($popularCountryData['visa_country_code']) . ".png' alt='{$popularCountryData['visa_country_title']} Flag'
                                                    class='w-10 h-10 object-cover rounded-full'>
                                            </div>
                                            <div>
                                                <h3 class='font-semibold text-gray-900'>{$popularCountryData['visa_country_title']}</h3>
                                            </div>
                                        </div>
                                        <div class='flex justify-between text-sm text-gray-600 mb-4'>
                                            <div>
                                                <p class='font-medium'>Processing Time</p>
                                                <p>03-14 days</p>
                                            </div>
                                            <div>
                                                <p class='font-medium'>Type</p>
                                                <p class='text-sm text-gray-500'>Tourist</p>
                                            </div>
                                        </div>
                                        <div class='bg-gray-50 rounded p-3 mb-4'>
                                            <p class='text-gray-900 font-medium'>Starting from</p>
                                            <p class='text-2xl font-bold text-primary'>₹ {$popularCountryData['visa_package_price']}</p>
                                        </div>
                                        <a href='/start-application/?country=" . strtolower(str_replace(' ', '_', $popularCountryData['visa_country_title'])) . "'
                                            class='w-full applyNowBtn bg-primary text-white py-2 !rounded-button whitespace-nowrap font-medium'>Apply
                                            Now</a>
                                    </div>
                                    ";
                                }
                            } else {
                                echo "<p class='text-gray-500'>No popular destinations found.</p>";
                            }
                        }

                        ?>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Application Process Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Application Process</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Follow these simple steps to apply for your tourist visa</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6 relative">
                    <div
                        class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">
                        1</div>
                    <h3 class="text-xl font-semibold mb-3">Complete Application</h3>
                    <p class="text-gray-600">Fill out our simple online application form with your personal and travel
                        details.</p>
                    <i class="ri-file-list-3-line text-gray-200 absolute bottom-4 right-4 text-5xl"></i>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 relative">
                    <div
                        class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">
                        2</div>
                    <h3 class="text-xl font-semibold mb-3">Upload Documents</h3>
                    <p class="text-gray-600">Submit all required documents according to your destination country's
                        requirements.</p>
                    <i class="ri-upload-cloud-line text-gray-200 absolute bottom-4 right-4 text-5xl"></i>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 relative">
                    <div
                        class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">
                        3</div>
                    <h3 class="text-xl font-semibold mb-3">Pay Fees</h3>
                    <p class="text-gray-600">Make a secure payment for your visa processing and service fees.</p>
                    <i class="ri-secure-payment-line text-gray-200 absolute bottom-4 right-4 text-5xl"></i>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 relative">
                    <div
                        class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">
                        4</div>
                    <h3 class="text-xl font-semibold mb-3">Receive Visa</h3>
                    <p class="text-gray-600">Get your approved visa delivered to your email or physical address.</p>
                    <i class="ri-mail-check-line text-gray-200 absolute bottom-4 right-4 text-5xl"></i>
                </div>
            </div>
        </div>
    </section>


    <!-- Application CTA Section -->
    <section id="apply" class="py-16 bg-primary/5">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Ready to Start Your Visa Application?</h2>
                <p class="text-xl text-gray-700 mb-8">Begin your journey today with our simple, secure application
                    process</p>
                <a href="/start-application/"
                    class="inline-block bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-8 py-3 cursor-pointer whitespace-nowrap text-lg">Start
                    Your Application</a>
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                            <i
                                class="ri-customer-service-2-line text-primary text-2xl w-8 h-8 flex items-center justify-center"></i>
                        </div>
                        <h3 class="text-lg font-medium mb-2">24/7 Support</h3>
                        <p class="text-gray-600 text-center">Our team is available around the clock to assist with your
                            application</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                            <i
                                class="ri-shield-check-line text-primary text-2xl w-8 h-8 flex items-center justify-center"></i>
                        </div>
                        <h3 class="text-lg font-medium mb-2">Secure Process</h3>
                        <p class="text-gray-600 text-center">Your information is protected with advanced encryption
                            technology</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                            <i class="ri-award-line text-primary text-2xl w-8 h-8 flex items-center justify-center"></i>
                        </div>
                        <h3 class="text-lg font-medium mb-2">High Success Rate</h3>
                        <p class="text-gray-600 text-center">Over 95% of applications processed through our service are
                            approved</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php

require_once 'footer.php';

?>