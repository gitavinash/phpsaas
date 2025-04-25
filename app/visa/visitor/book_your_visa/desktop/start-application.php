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
            <h1 class="text-2xl sm:text-4xl font-bold text-gray-900 mb-4 sm:mb-6">Select a Visa Package for
                <?php echo ucwords(str_replace('_', ' ', $country)) ?></h1>
            <p class="text-gray-600 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">Please choose the visa
                package that best suits your purpose of travel. We’ll help you through each step of the application
                process.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 auto-rows-fr"
            id="visaPackagesListBox">
            <!-- Purpose of visit -->
        </div>
    </div>
</main>
<?php

require_once 'footer.php';

?>