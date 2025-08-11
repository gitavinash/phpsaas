<?php

require_once UTIL_FUNCTIONS . 'getInitials.php';

if (!empty($id)) {
    // Sanitize $id as integer to avoid injection risks (very important!)
    $id = (int)base64_decode($id);

    $checkApplicationQuery = "
        SELECT 
            visa_application_id, visa_application_order_id, visa_application_data, visa_application_time, visa_application_update, visa_package_title, visa_package_visa_type, visa_country_title, visa_country_code,
            transaction_method, transaction_gateway, transaction_reference_id, transaction_amount,
            transaction_coupon, transaction_coupon_amount, visa_package_entry_type,
            CASE visa_application_status
                WHEN 0 THEN 'Initiated'
                WHEN 1 THEN 'Success'
                WHEN 2 THEN 'Pending'
                WHEN 3 THEN 'Followup'
                WHEN 4 THEN 'Failed'
                WHEN 5 THEN 'Duplicate'
                WHEN 6 THEN 'Payment'
                WHEN 7 THEN 'Requested'
                WHEN 8 THEN 'Completed'
                ELSE 'Unknown'
            END as visa_application_status
        FROM {$tableNamePrefix}application
        INNER JOIN {$tableNamePrefix}package 
            ON visa_package_id = visa_application_package_id 
            AND visa_package_site_id = " . SITE_ID . "
        INNER JOIN {$tableNamePrefix}country 
            ON visa_country_id = visa_package_country_id
        LEFT JOIN " . TABLE_PREFIX . "order 
            ON order_id = visa_application_order_id 
            AND order_site_id = " . SITE_ID . "
        LEFT JOIN " . TABLE_PREFIX . "transaction 
            ON transaction_order_id = order_id 
            AND transaction_type = 'visa_application' 
            AND transaction_site_id = " . SITE_ID . "
        WHERE visa_application_id = {$id} 
          AND visa_application_site_id = " . SITE_ID;

    $checkApplication = mysqli_query($connection, $checkApplicationQuery);

    if ($checkApplication === false) {
        echo "<p class='text-red-600'>Database query error: " . mysqli_error($connection) . "</p>";
        exit();
    }

    if (mysqli_num_rows($checkApplication) === 0) {
        // No data found - show full screen no-data message
        echo '
<div class="min-h-screen bg-gray-100 flex flex-col items-center justify-center px-6 py-12 relative text-center">
    <!-- Icon -->
    <div class="w-24 h-24 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-8 shadow-lg">
        <i class="ri-error-warning-line text-6xl"></i>
    </div>

    <!-- Heading -->
    <h1 class="text-8xl font-extrabold text-gray-900 mb-4">404</h1>
    <h2 class="text-3xl font-semibold text-gray-700 mb-6">Oops! No Data Found</h2>
    <p class="text-gray-600 max-w-lg mx-auto mb-10">
        We couldn’t find any visa applications for this request.<br>
        If you haven’t applied yet, get started now!
    </p>

    <!-- Actions -->
    <div class="flex space-x-6 justify-center">
        <a href="/services/" target="_blank"
            class="bg-red-600 text-white px-7 py-3 rounded-lg shadow hover:bg-red-700 transition duration-300 flex items-center font-semibold">
            <i class="ri-file-add-line mr-3 text-xl"></i> Start new Visa Application
        </a>
        <a href="/contact-us/" target="_blank"
            class="bg-gray-200 text-gray-800 px-7 py-3 rounded-lg shadow hover:bg-gray-300 transition duration-300 flex items-center font-semibold">
            <i class="ri-question-line mr-3 text-xl"></i> Contact Support
        </a>
    </div>

    <!-- Decorative Animation -->
    <div class="absolute top-10 left-10 w-36 h-36 bg-red-100 rounded-full blur-3xl opacity-30"></div>
    <div class="absolute bottom-10 right-10 w-48 h-48 bg-red-200 rounded-full blur-3xl opacity-30"></div>
</div>';
        exit();
    }

    // Fetch data
    extract(mysqli_fetch_assoc($checkApplication));

    $passangerDetails = json_decode($visa_application_data, TRUE);
    $personsDataJSON = json_decode($passangerDetails['personsData'], TRUE);
    $personsData = json_decode($personsDataJSON, TRUE);
} else {
    echo '
<div class="min-h-screen bg-gray-100 flex flex-col items-center justify-center px-6 py-12 relative text-center">
    <!-- Icon -->
    <div class="w-24 h-24 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-8 shadow-lg">
        <i class="ri-error-warning-line text-6xl"></i>
    </div>

    <!-- Heading -->
    <h1 class="text-8xl font-extrabold text-gray-900 mb-4">Missing ID</h1>
    <h2 class="text-3xl font-semibold text-gray-700 mb-6">Application ID Not Found</h2>
    <p class="text-gray-600 max-w-lg mx-auto mb-10">
        The requested visa application cannot be displayed because a valid Application ID was not provided.<br>
        Please verify the URL or contact support for further assistance.
    </p>

    <!-- Actions -->
    <div class="flex space-x-6 justify-center">
        <a href="/services/" target="_blank"
            class="bg-red-600 text-white px-7 py-3 rounded-lg shadow hover:bg-red-700 transition duration-300 flex items-center font-semibold">
            <i class="ri-file-add-line mr-3 text-xl"></i> Start new Visa Application
        </a>
        <a href="/contact-us/" target="_blank"
            class="bg-gray-200 text-gray-800 px-7 py-3 rounded-lg shadow hover:bg-gray-300 transition duration-300 flex items-center font-semibold">
            <i class="ri-question-line mr-3 text-xl"></i> Contact Support
        </a>
    </div>

    <!-- Decorative Animation -->
    <div class="absolute top-10 left-10 w-36 h-36 bg-red-100 rounded-full blur-3xl opacity-30"></div>
    <div class="absolute bottom-10 right-10 w-48 h-48 bg-red-200 rounded-full blur-3xl opacity-30"></div>
</div>';
    exit();
}

if (isset($visa_application_status)) {
    // Status color mapping
    $statusStyles = [
        'completed'     => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'dot' => 'bg-green-500', 'label' => 'Confirmed', 'icon' => 'ri-check-line'],
        'in process'  => ['bg' => 'bg-blue-100',  'text' => 'text-blue-800',  'dot' => 'bg-blue-500',  'label' => 'In Process', 'icon' => 'ri-time-line'],
        'pending'     => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'dot' => 'bg-yellow-500', 'label' => 'Pending',    'icon' => 'ri-time-line'],
        'cancel'      => ['bg' => 'bg-red-100',   'text' => 'text-red-800',   'dot' => 'bg-red-500',   'label' => 'Cancelled',  'icon' => 'ri-close-line'],
        'unknown'     => ['bg' => 'bg-gray-100',  'text' => 'text-gray-800',  'dot' => 'bg-gray-500',  'label' => 'Unknown',    'icon' => 'ri-question-line'],
    ];
}

$statusKey = strtolower($visa_application_status);
$style = $statusStyles[$statusKey] ?? $statusStyles['unknown'];

?>
<div class="px-8 relative max-w-7xl mx-auto min-h-screen">
    <div class="max-w-7xl mx-auto py-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">VISA Application Details</h1>
                <p class="text-gray-600">Application ID:
                    <?php echo getInitials(SITE_NAME) . "-00" . (($visa_application_order_id != 0) ? $visa_application_order_id : 'REF' . $visa_application_id) ?>
                </p>
            </div>
            <span
                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium <?php echo $style['bg'] . ' ' . $style['text']; ?>">
                <div class="w-2 h-2 <?php echo $style['dot']; ?> rounded-full mr-2"></div>
                <i class="<?php echo $style['icon']; ?> mr-1 text-lg"></i>
                <?php echo htmlspecialchars($style['label']); ?>
            </span>
        </div>
    </div>
    <!-- Application Overview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center">
                <div class="w-16 h-12 border border-gray-200 bg-gray-50 p-2 flex items-center justify-center mr-6">
                    <img src="https://flagcdn.com/<?php echo strtolower($visa_country_code); ?>.svg"
                        alt="<?php echo ucwords($visa_country_title) ?> flag" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">
                        <?php echo ucwords($visa_package_title) ?>
                    </h2>
                    <p class="text-gray-600">
                        <?php echo ucwords($visa_country_title . ' • ' . $visa_package_visa_type) . ' VISA' ?> •
                        <?php echo ucwords($visa_package_entry_type) ?> Entry</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 mb-1">Last Updated</p>
                <p class="text-lg font-semibold text-gray-900">
                    <?php echo date('F d, Y h:i A', strtotime($visa_application_update)) ?></p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Applicant Name</p>
                <p class="text-lg font-semibold text-gray-900">
                    <?php echo ((isset($personsData[0])) ? $personsData[0]['first_name'] . ' ' . $personsData[0]['middle_name'] . ' ' . $personsData[0]['last_name'] : '') ?>
                </p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Passport Number</p>
                <p class="text-lg font-semibold text-gray-900">
                    <?php echo ((isset($personsData[0])) ? strtoupper($personsData[0]['passport_number']) : '') ?></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Application Date</p>
                <p class="text-lg font-semibold text-gray-900">
                    <?php echo date('F d, Y', strtotime($visa_application_time)) ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-6">Application Members & Documents</h3>
        <div class="space-y-8">
            <?php
            for ($i = 0; $i < sizeof($personsData); $i++) {
                extract($personsData[$i]);
                echo "<div class='border border-gray-200 rounded-xl p-6'>
                <div class='flex items-center justify-between mb-6'>
                    <div class='flex items-center'>
                        <img src='https://readdy.ai/api/search-image?query=professional%20business%20person%20headshot%20portrait%20with%20clean%20background%2C%20corporate%20style%2C%20modern%20professional%20appearance%2C%20neutral%20lighting&width=48&height=48&seq=person1&orientation=squarish'
                            alt='John Anderson' class='w-12 h-12 rounded-full object-cover mr-4'>
                        <div>
                            <p class='font-medium text-gray-900'>John Anderson</p>
                            <p class='text-sm text-gray-500'>Primary Applicant</p>
                        </div>
                    </div>
                    <div class='flex items-center space-x-6'>
                        <div>
                            <p class='text-sm text-gray-500'>Passport Number</p>
                            <p class='font-medium text-gray-900'>" . strtoupper($passport_number) . "</p>
                        </div>
                        <div>
                            <p class='text-sm text-gray-500'>Document Status</p>
                            <span
                                class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800'>
                                <i class='ri-check-line text-xs mr-1'></i>
                                All Verified
                            </span>
                        </div>
                    </div>
                </div>
                <div class='grid grid-cols-1 md:grid-cols-2 gap-4'>
                    <div class='flex items-center p-4 bg-gray-50 rounded-lg'>
                        <div class='w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4'>
                            <i class='ri-file-text-line text-lg text-blue-600'></i>
                        </div>
                        <div class='flex-1'>
                            <p class='font-medium text-gray-900'>DS-160 Form</p>
                            <p class='text-sm text-gray-500'>Primary Application Form</p>
                        </div>
                        <span
                            class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800'>
                            <i class='ri-check-line text-xs mr-1'></i>
                            Verified
                        </span>
                    </div>
                    <div class='flex items-center p-4 bg-gray-50 rounded-lg'>
                        <div class='w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4'>
                            <i class='ri-passport-line text-lg text-green-600'></i>
                        </div>
                        <div class='flex-1'>
                            <p class='font-medium text-gray-900'>Passport Copy</p>
                            <p class='text-sm text-gray-500'>Valid for 10 years</p>
                        </div>
                        <span
                            class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800'>
                            <i class='ri-check-line text-xs mr-1'></i>
                            Verified
                        </span>
                    </div>
                    <div class='flex items-center p-4 bg-gray-50 rounded-lg'>
                        <div class='w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4'>
                            <i class='ri-image-line text-lg text-purple-600'></i>
                        </div>
                        <div class='flex-1'>
                            <p class='font-medium text-gray-900'>Passport Photos</p>
                            <p class='text-sm text-gray-500'>2x2 inches, white background</p>
                        </div>
                        <span
                            class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800'>
                            <i class='ri-check-line text-xs mr-1'></i>
                            Verified
                        </span>
                    </div>
                    <div class='flex items-center p-4 bg-gray-50 rounded-lg'>
                        <div class='w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4'>
                            <i class='ri-bank-card-line text-lg text-orange-600'></i>
                        </div>
                        <div class='flex-1'>
                            <p class='font-medium text-gray-900'>Financial Documents</p>
                            <p class='text-sm text-gray-500'>Bank statements & tax returns</p>
                        </div>
                        <span
                            class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800'>
                            <i class='ri-check-line text-xs mr-1'></i>
                            Verified
                        </span>
                    </div>
                    <div class='flex items-center p-4 bg-gray-50 rounded-lg'>
                        <div class='w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-4'>
                            <i class='ri-briefcase-line text-lg text-red-600'></i>
                        </div>
                        <div class='flex-1'>
                            <p class='font-medium text-gray-900'>Employment Letter</p>
                            <p class='text-sm text-gray-500'>Current employment proof</p>
                        </div>
                        <span
                            class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800'>
                            <i class='ri-check-line text-xs mr-1'></i>
                            Verified
                        </span>
                    </div>
                    <div class='flex items-center p-4 bg-gray-50 rounded-lg'>
                        <div class='w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4'>
                            <i class='ri-file-list-3-line text-lg text-yellow-600'></i>
                        </div>
                        <div class='flex-1'>
                            <p class='font-medium text-gray-900'>Marriage Certificate</p>
                            <p class='text-sm text-gray-500'>Family relationship proof</p>
                        </div>
                        <span
                            class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800'>
                            <i class='ri-check-line text-xs mr-1'></i>
                            Verified
                        </span>
                    </div>
                </div>
            </div>";
            }
            ?>
        </div>
    </div>
    <!-- Timeline -->
    <!-- <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-6">Application Timeline</h3>
        <div class="relative">
            <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            <div class="space-y-8">
                <div class="relative flex items-start">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center relative z-10">
                        <i class="ri-check-line text-lg text-green-600"></i>
                    </div>
                    <div class="ml-6 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900">VISA Approved</h4>
                            <span class="text-sm text-gray-500">Jan 08, 2025 • 2:30 PM</span>
                        </div>
                        <p class="text-gray-600 mt-1">Your VISA application has been approved. You can now download
                            your approved documents.</p>
                    </div>
                </div>
                <div class="relative flex items-start">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center relative z-10">
                        <i class="ri-user-line text-lg text-blue-600"></i>
                    </div>
                    <div class="ml-6 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900">Interview Completed</h4>
                            <span class="text-sm text-gray-500">Jan 05, 2025 • 10:00 AM</span>
                        </div>
                        <p class="text-gray-600 mt-1">Successfully completed consular interview at US Embassy.</p>
                    </div>
                </div>
                <div class="relative flex items-start">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center relative z-10">
                        <i class="ri-calendar-check-line text-lg text-blue-600"></i>
                    </div>
                    <div class="ml-6 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900">Interview Scheduled</h4>
                            <span class="text-sm text-gray-500">Dec 28, 2024 • 3:45 PM</span>
                        </div>
                        <p class="text-gray-600 mt-1">Interview appointment scheduled for January 5, 2025 at US
                            Embassy.</p>
                    </div>
                </div>
                <div class="relative flex items-start">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center relative z-10">
                        <i class="ri-money-dollar-circle-line text-lg text-blue-600"></i>
                    </div>
                    <div class="ml-6 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900">Payment Confirmed</h4>
                            <span class="text-sm text-gray-500">Dec 20, 2024 • 11:20 AM</span>
                        </div>
                        <p class="text-gray-600 mt-1">Application fee payment of $185 has been successfully
                            processed.</p>
                    </div>
                </div>
                <div class="relative flex items-start">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center relative z-10">
                        <i class="ri-file-check-line text-lg text-blue-600"></i>
                    </div>
                    <div class="ml-6 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900">Documents Verified</h4>
                            <span class="text-sm text-gray-500">Dec 18, 2024 • 4:15 PM</span>
                        </div>
                        <p class="text-gray-600 mt-1">All submitted documents have been reviewed and verified.</p>
                    </div>
                </div>
                <div class="relative flex items-start">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center relative z-10">
                        <i class="ri-send-plane-line text-lg text-blue-600"></i>
                    </div>
                    <div class="ml-6 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900">Application Submitted</h4>
                            <span class="text-sm text-gray-500">Dec 15, 2024 • 9:30 AM</span>
                        </div>
                        <p class="text-gray-600 mt-1">VISA application form DS-160 submitted successfully with all
                            required documents.</p>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="grid grid-cols-1 gap-8 mb-8">
        <!-- Payment History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Payment History</h3>
            <div class="space-y-6">
                <div class="border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">VISA Application Fee</h4>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="ri-check-line text-sm mr-1"></i>
                            Paid
                        </span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Amount:</span>
                            <span class="font-medium text-gray-900">$185.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Payment Method:</span>
                            <span class="text-gray-700">Credit Card (**** 4567)</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Transaction ID:</span>
                            <span class="text-gray-700">TXN-USA2024001-001</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Payment Date:</span>
                            <span class="text-gray-700">December 20, 2024</span>
                        </div>
                    </div>
                    <!-- <div class="pt-4 border-t border-gray-200">
                        <button class="flex items-center text-primary hover:text-blue-700 text-sm font-medium">
                            <i class="ri-download-line text-sm mr-2"></i>
                            Download Receipt
                        </button>
                    </div> -->
                </div>
                <!-- <div class="border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">SEVIS Fee</h4>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            Not Required
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">SEVIS fee is not applicable for B-2 tourist visa applications.
                    </p>
                </div> -->
            </div>
        </div>
    </div>
    <!-- Approval VISA Details -->
    <!-- <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-6">Approved VISA Details</h3>
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-16 h-12 rounded-lg bg-white p-2 flex items-center justify-center mr-4 shadow-sm">
                        <img src="https://readdy.ai/api/search-image?query=United%20States%20flag%20icon%20clean%20simple%20design%20with%20white%20background%2C%20official%20colors%20red%20white%20blue%20stripes%20and%20stars&width=48&height=36&seq=us-flag-visa&orientation=landscape"
                            alt="USA" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-900 mb-1">B-2 Tourist VISA</h4>
                        <p class="text-blue-700 font-medium">Single Entry Permitted</p>
                    </div>
                </div>
                <div class="text-right">
                    <span
                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="ri-shield-check-line text-sm mr-2"></i>
                        Valid VISA
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">VISA Number</p>
                    <p class="text-lg font-semibold text-gray-900">US2025B2001234567</p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Issue Date</p>
                    <p class="text-lg font-semibold text-gray-900">January 08, 2025</p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Expiry Date</p>
                    <p class="text-lg font-semibold text-gray-900">January 08, 2035</p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Maximum Stay</p>
                    <p class="text-lg font-semibold text-gray-900">6 Months per Entry</p>
                </div>
            </div>
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h5 class="font-semibold text-gray-900 mb-2">Important Notes:</h5>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• This VISA is valid for tourism and visiting friends/family only</li>
                    <li>• Employment is strictly prohibited on this VISA</li>
                    <li>• Maximum stay of 6 months per entry as determined by CBP officer</li>
                    <li>• VISA validity does not guarantee entry - final decision rests with CBP</li>
                </ul>
            </div>
        </div>
    </div> -->
    <!-- Action Buttons -->
    <!-- <div class="bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-6 shadow-lg z-[999] mb-8">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button
                    class="flex items-center px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-blue-700 transition-colors duration-300 !rounded-button whitespace-nowrap">
                    <i class="ri-download-line text-lg mr-2"></i>
                    Download VISA
                </button>
                <button
                    class="flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-300 !rounded-button whitespace-nowrap">
                    <i class="ri-file-download-line text-lg mr-2"></i>
                    Download All Documents
                </button>
                <button
                    class="flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-300 !rounded-button whitespace-nowrap">
                    <i class="ri-printer-line text-lg mr-2"></i>
                    Print Summary
                </button>
            </div>
            <div class="flex items-center space-x-4">
                <button
                    class="flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-300 !rounded-button whitespace-nowrap">
                    <i class="ri-share-line text-lg mr-2"></i>
                    Share Details
                </button>
                <button
                    class="flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-300 !rounded-button whitespace-nowrap">
                    <i class="ri-customer-service-line text-lg mr-2"></i>
                    Contact Support
                </button>
            </div>
        </div>
    </div> -->
</div>