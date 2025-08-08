<?php

require_once 'header.php';
require_once UTIL_FUNCTIONS . 'getInitials.php';
?>

<main class="flex-grow container mx-auto py-[10em]">
    <div class="mx-auto applicationBox" id="selectCountry">

        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-12">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">Track Your Visa Application</h1>
                    <p class="text-gray-600">Enter your application details below to check the current status</p>
                </div>
                <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                    <form id="trackingForm" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Application Reference
                                Number</label>
                            <input type="text" id="referenceNumber"
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                placeholder="Enter your reference number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone
                                Number</label>
                            <input type="number" id="contactInfo"
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                placeholder="Enter email or phone number">
                        </div>
                        <button type="submit" id="getApplicationStatus"
                            class="w-full bg-primary text-white font-medium py-3 !rounded-button hover:bg-primary/90 cursor-pointer">Track
                            Application</button>
                    </form>
                </div>
                <div id="applicationStatus" class="hidden">
                    <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Application Status</h2>
                            <span id="visaStatus"
                                class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Reference Number</div>
                                <div class="font-medium">
                                    <?php echo getInitials(SITE_NAME) . '-00' ?><span id="visaApplicationId"></span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Application Date</div>
                                <div class="font-medium" id="visaAppliedDate"></div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Visa Type</div>
                                <div class="font-medium" id="visaType"></div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Destination Country</div>
                                <div class="font-medium" id="visaCountry"></div>
                            </div>
                        </div>
                        <div id="lastUpdateBox"
                            class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start">
                            <i
                                class="ri-information-line text-blue-500 mr-3 w-5 h-5 flex items-center justify-center"></i>
                            <div class="text-sm text-blue-800">Last update on <span id="lastUpdateText"></span></div>
                        </div>
                    </div>
                    <!-- <div class="bg-white rounded-lg shadow-lg p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Application Timeline</h2>
                        <div class="relative pl-8">
                            <div class="timeline-line"></div>
                            <div class="space-y-8">
                                <div class="relative">
                                    <div
                                        class="timeline-dot completed w-8 h-8 rounded-full border-2 border-white shadow flex items-center justify-center absolute -left-8">
                                        <i class="ri-check-line text-white"></i>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-medium">Application Submitted</div>
                                            <div class="text-sm text-gray-500">March 25, 2025</div>
                                        </div>
                                        <p class="text-sm text-gray-600">Your application has been successfully
                                            submitted and payment confirmed.</p>
                                    </div>
                                </div>
                                <div class="relative">
                                    <div
                                        class="timeline-dot completed w-8 h-8 rounded-full border-2 border-white shadow flex items-center justify-center absolute -left-8">
                                        <i class="ri-check-line text-white"></i>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-medium">Document Verification</div>
                                            <div class="text-sm text-gray-500">March 26, 2025</div>
                                        </div>
                                        <p class="text-sm text-gray-600">All submitted documents have been verified and
                                            approved.</p>
                                    </div>
                                </div>
                                <div class="relative">
                                    <div
                                        class="timeline-dot active w-8 h-8 rounded-full border-2 border-white shadow flex items-center justify-center absolute -left-8">
                                        <i class="ri-time-line text-white"></i>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-medium">Application Processing</div>
                                            <div class="text-sm text-gray-500">In Progress</div>
                                        </div>
                                        <p class="text-sm text-gray-600">Your application is being processed by the
                                            embassy.</p>
                                    </div>
                                </div>
                                <div class="relative">
                                    <div
                                        class="timeline-dot pending w-8 h-8 rounded-full border-2 border-white shadow flex items-center justify-center absolute -left-8">
                                        <i class="ri-checkbox-blank-circle-line text-gray-400"></i>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-medium">Decision</div>
                                            <div class="text-sm text-gray-500">Pending</div>
                                        </div>
                                        <p class="text-sm text-gray-600">Awaiting final decision from the embassy.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</main>
<?php
require_once 'footer.php';

?>