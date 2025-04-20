<?php

require_once 'header.php';

?>
<main class="flex-grow container mx-auto pt-[5em] pb-0 sm:py-[10em]">

    <div class="mx-auto applicationBox" id="selectCountry">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-8 sm:mb-12">
                <h1 class="text-2xl sm:text-4xl font-bold text-gray-900 mb-4 sm:mb-6">Select Your Destination Country
                </h1>
                <p class="text-gray-600 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">Choose the country you
                    plan to visit. We'll guide you through the visa requirements and application process specific to
                    your destination.</p>
            </div>
            <div class="relative mb-8">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ri-search-line text-gray-400 w-5 h-5 flex items-center justify-center"></i>
                </div>
                <select id="startApplicationDropDown"
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
            </div>
            <div class="mb-10">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Popular Destinations</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <?php

                    if ($checkCountry = mysqli_query($connection, "SELECT visa_country_code, visa_country_title, visa_country_services FROM {$tableNamePrefix}country WHERE visa_country_status=1 AND visa_country_site_id=" . SITE_ID . " ORDER BY visa_country_is_featured DESC")) {
                        while ($checkCountryData = mysqli_fetch_assoc($checkCountry)) {
                            echo "
                                <div class='country-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm'>
                                    <a href='/start-application/?country=" . strtolower(str_replace(' ', '_', $checkCountryData['visa_country_title'])) . "'>
                                        <div class='flex items-center mb-3'>
                                            <img src='https://flagcdn.com/w40/" . strtolower($checkCountryData['visa_country_code']) . ".png'  alt='{$checkCountryData['visa_country_title']} flag'
                                                class='w-6 h-6 rounded-full mr-3 object-cover border border-gray-200'>
                                            <h3 class='text-lg font-medium text-gray-900 startApplicationCountry' data-service='{$checkCountryData['visa_country_services']}'>{$checkCountryData['visa_country_title']}</h3>
                                        </div>
                                        <p class='text-gray-600 text-sm'><strong>VISA in </strong>2-3 weeks</p>
                                    </a>
                                </div>";
                        }
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="mx-auto applicationBox" id="purposeOfVisa">
        <div class="text-center mb-8 sm:mb-16">
            <h1 class="text-2xl sm:text-4xl font-bold text-gray-900 mb-4 sm:mb-6">Select Purpose of Visit to
                <?php echo ucwords(str_replace('_', ' ', $country)) ?></h1>
            <p class="text-gray-600 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">Please select the
                primary purpose of your visit to proceed with the visa application. We'll guide you through
                every step of the process.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 auto-rows-fr" id="visaPurposeListBox">

        </div>
    </div>
    <div class="mx-auto applicationBox" id="visaPackages">
        <div class="text-center mb-8 sm:mb-16">
            <h1 class="text-2xl sm:text-4xl font-bold text-gray-900 mb-4 sm:mb-6">Select Purpose of Visit to
                Switzerland</h1>
            <p class="text-gray-600 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">Please select the
                primary purpose of your visit to proceed with the visa application. We'll guide you through
                every step of the process.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 auto-rows-fr">
            <!-- Tourist Card -->
            <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl">
                <div class="flex items-center mb-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-blue-600/5">
                        <i class="ri-plane-line text-blue-600 ri-xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900">Tourist</h3>
                </div>
                <p class="text-gray-600 mb-4">For leisure travel, sightseeing, visiting friends and family, or medical
                    treatment.</p>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-time-line mr-2 w-4 h-4 flex items-center justify-center"></i>
                    <span>Typical processing: 5-15 days</span>
                </div>
            </div>
            <!-- Business Card -->
            <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl">
                <div class="flex items-center mb-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-amber-50 to-amber-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-amber-600/5">
                        <i class="ri-briefcase-line text-amber-600 ri-xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900">Business</h3>
                </div>
                <p class="text-gray-600 mb-4">For business meetings, conferences, trade fairs, or short-term work
                    assignments.</p>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-time-line mr-2 w-4 h-4 flex items-center justify-center"></i>
                    <span>Typical processing: 7-20 days</span>
                </div>
            </div>
            <!-- Student Card -->
            <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl">
                <div class="flex items-center mb-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-green-600/5">
                        <i class="ri-book-open-line text-green-600 ri-xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900">Student</h3>
                </div>
                <p class="text-gray-600 mb-4">For pursuing education, academic research, or attending educational
                    institutions.</p>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-time-line mr-2 w-4 h-4 flex items-center justify-center"></i>
                    <span>Typical processing: 15-30 days</span>
                </div>
            </div>
            <!-- Work Card -->
            <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl">
                <div class="flex items-center mb-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-purple-600/5">
                        <i class="ri-tools-line text-purple-600 ri-xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900">Work</h3>
                </div>
                <p class="text-gray-600 mb-4">For employment purposes, including temporary and permanent work positions.
                </p>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-time-line mr-2 w-4 h-4 flex items-center justify-center"></i>
                    <span>Typical processing: 30-90 days</span>
                </div>
            </div>
            <!-- Family Reunion Card -->
            <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl">
                <div class="flex items-center mb-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-red-600/5">
                        <i class="ri-heart-line text-red-600 ri-xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900">Family Reunion</h3>
                </div>
                <p class="text-gray-600 mb-4">For joining family members who are citizens or residents of the
                    destination country.</p>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-time-line mr-2 w-4 h-4 flex items-center justify-center"></i>
                    <span>Typical processing: 20-60 days</span>
                </div>
            </div>
            <!-- Other Card -->
            <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl">
                <div class="flex items-center mb-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-gray-600/5">
                        <i class="ri-more-line text-gray-600 ri-xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900">Other</h3>
                </div>
                <p class="text-gray-600 mb-4">For transit, diplomatic, official, or other special purpose visas not
                    listed above.</p>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-time-line mr-2 w-4 h-4 flex items-center justify-center"></i>
                    <span>Processing varies by type</span>
                </div>
            </div>
        </div>
    </div>
</main>
<?php

require_once 'footer.php';

?>