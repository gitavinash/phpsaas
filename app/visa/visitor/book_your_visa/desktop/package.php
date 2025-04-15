<?php

    require_once 'header.php';

?>
<main>
    <div class="min-h-screen flex flex-col justify-between">
        <div class="flex flex-col lg:flex-row">
            <!-- Sidebar Navigation -->
            <div class="hidden lg:block w-full lg:w-72 border-b lg:border-b-0 lg:border-r border-gray-100 bg-white p-6">
                <h1 class="text-2xl font-bold mb-1">Switzerland Visa Application</h1>
                <p class="text-gray-500 mb-8">Your Plan Summary is Here</p>
                <nav class="space-y-4">
                    <div class="flex items-center text-primary border-l-4 border-primary pl-3 py-1">
                        <span class="font-medium">Plan Details</span>
                    </div>
                    <div class="flex items-center text-gray-700 hover:text-primary pl-4 py-1">
                        <span>Plan Disclaimer</span>
                    </div>
                    <div class="flex items-center text-gray-700 hover:text-primary pl-4 py-1">
                        <span>Visa Schedule</span>
                    </div>
                    <div class="flex items-center text-gray-700 hover:text-primary pl-4 py-1">
                        <span>Timings & Holidays</span>
                    </div>
                    <div class="flex items-center text-gray-700 hover:text-primary pl-4 py-1">
                        <span>Documents Required</span>
                    </div>
                    <div class="flex items-center text-gray-700 hover:text-primary pl-4 py-1">
                        <span>Important Information</span>
                    </div>
                    <div class="flex items-center text-gray-700 hover:text-primary pl-4 py-1">
                        <span>Frequently Asked Questions</span>
                    </div>
                </nav>
            </div>
            <!-- Main Content -->
            <div class="flex-1 flex flex-col h-full lg:h-screen">
                <div class="flex-1 p-4 lg:p-6 overflow-y-auto" id="visaMainContent">
                    <!-- Banner Image -->
                    <div class="rounded-lg overflow-hidden mb-5">
                        <img src="https://readdy.ai/api/search-image?query=Beautiful%2520panoramic%2520view%2520of%2520Swiss%2520Alps%2520mountains%2520with%2520green%2520meadows%252C%2520blue%2520sky%2520and%2520white%2520clouds.%2520Majestic%2520landscape%2520of%2520Switzerland%2520countryside%2520in%2520summer.%2520Scenic%2520nature%2520background%2520with%2520high%2520peaks&width=1200&height=300&seq=123&orientation=landscape"
                            alt="Switzerland Mountains" class="w-full h-48 object-cover object-top">
                    </div>
                    <!-- Notification Banner -->
                    <div class="bg-amber-50 border border-amber-100 rounded-lg p-4 mb-6 flex items-center">
                        <div class="w-8 h-8 flex items-center justify-center text-amber-600 mr-3">
                            <i class="ri-calendar-check-line ri-lg"></i>
                        </div>
                        <p class="text-amber-800">Get your visa appointment date picked within 3 working days or get a
                            full refund.</p>
                    </div>
                    <!-- Appointment Options -->
                    <div
                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 space-y-4 sm:space-y-0">
                        <div
                            class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                            <button
                                class="bg-blue-50 text-blue-700 px-6 py-3 rounded-lg flex items-center !rounded-button whitespace-nowrap w-full sm:w-auto justify-center">
                                <span>Get your appointment in <strong>72 hours</strong></span>
                            </button>
                            <div class="flex space-x-3 w-full sm:w-auto">
                                <button
                                    class="bg-white border border-gray-200 px-5 py-2 rounded-lg font-medium text-gray-800 shadow-sm !rounded-button whitespace-nowrap">Business</button>
                                <button
                                    class="bg-gray-100 border border-gray-200 px-5 py-2 rounded-lg font-medium text-gray-500 !rounded-button whitespace-nowrap">Standard</button>
                            </div>
                        </div>
                        <div class="text-left sm:text-right w-full sm:w-auto mt-4 sm:mt-0">
                            <div class="text-2xl font-bold flex items-center justify-start sm:justify-end">
                                ₹ 5,350
                                <div class="w-6 h-6 flex items-center justify-center ml-1">
                                    <i class="ri-arrow-down-s-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Visa Details Grid -->
                    <div class="border border-gray-100 rounded-lg p-6 mb-8">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-gray-500 mb-1">Visa Type</p>
                                <p class="font-semibold">Business</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-gray-500 mb-1">Stay</p>
                                <p class="font-semibold">Upto 30 Days</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-gray-500 mb-1">Validity</p>
                                <p class="font-semibold">Upto 90 Days</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-gray-500 mb-1">Entry Type</p>
                                <p class="font-semibold">Single Entry</p>
                            </div>
                        </div>
                    </div>
                    <!-- Plan Disclaimer -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Plan Disclaimer</h2>
                        <div class="bg-white border border-gray-100 rounded-lg p-6">
                            <ul class="space-y-3 text-gray-700">
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </div>
                                    <span>The visa fee mentioned is inclusive of all service charges and taxes.</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </div>
                                    <span>Embassy/Consulate reserves the right to request additional documents or
                                        personal appearance.</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </div>
                                    <span>Visa approval is at the sole discretion of the Embassy/Consulate and cannot be
                                        guaranteed.</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </div>
                                    <span>Processing time may vary based on nationality and current application
                                        volume.</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </div>
                                    <span>Rescheduling of appointments is subject to availability and may incur
                                        additional charges.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Visa Schedule -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Visa Schedule</h2>
                        <div class="bg-white border border-gray-100 rounded-lg p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="font-medium mb-3">Processing Time</h3>
                                    <ul class="space-y-2 text-gray-700">
                                        <li class="flex items-center">
                                            <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                                                <i class="ri-time-line"></i>
                                            </div>
                                            <span>Standard Processing: 15 working days</span>
                                        </li>
                                        <li class="flex items-center">
                                            <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                                                <i class="ri-time-line"></i>
                                            </div>
                                            <span>Express Processing: 5 working days</span>
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <h3 class="font-medium mb-3">Application Centers</h3>
                                    <ul class="space-y-2 text-gray-700">
                                        <li class="flex items-center">
                                            <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                                                <i class="ri-map-pin-line"></i>
                                            </div>
                                            <span>Mumbai, Delhi, Bangalore</span>
                                        </li>
                                        <li class="flex items-center">
                                            <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                                                <i class="ri-map-pin-line"></i>
                                            </div>
                                            <span>Chennai, Kolkata, Hyderabad</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Timings & Holidays -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Timings & Holidays</h2>
                        <div class="bg-white border border-gray-100 rounded-lg p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="font-medium mb-3">Working Hours</h3>
                                    <ul class="space-y-2 text-gray-700">
                                        <li class="flex items-center">
                                            <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                                                <i class="ri-calendar-line"></i>
                                            </div>
                                            <span>Monday to Friday: 9:00 AM - 4:00 PM</span>
                                        </li>
                                        <li class="flex items-center">
                                            <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                                                <i class="ri-calendar-line"></i>
                                            </div>
                                            <span>Saturday & Sunday: Closed</span>
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <h3 class="font-medium mb-3">Holiday Calendar 2025</h3>
                                    <ul class="space-y-2 text-gray-700">
                                        <li class="flex items-center">
                                            <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                                                <i class="ri-calendar-event-line"></i>
                                            </div>
                                            <span>New Year's Day: January 1</span>
                                        </li>
                                        <li class="flex items-center">
                                            <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                                                <i class="ri-calendar-event-line"></i>
                                            </div>
                                            <span>Good Friday: April 18</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Documents Required -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Documents Required</h2>
                        <div class="bg-white border border-gray-100 rounded-lg p-6">
                            <ul class="space-y-3 text-gray-700">
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-file-list-line"></i>
                                    </div>
                                    <span>Valid passport with minimum 6 months validity and 2 blank pages</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-file-list-line"></i>
                                    </div>
                                    <span>Completed visa application form with recent photograph</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-file-list-line"></i>
                                    </div>
                                    <span>Travel insurance covering the entire stay period</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-file-list-line"></i>
                                    </div>
                                    <span>Flight itinerary and hotel reservations</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Important Information -->
                    <div class="mb-8 w-full">
                        <h2 class="text-xl font-semibold mb-4">Important Information</h2>
                        <div class="bg-white border border-gray-100 rounded-lg p-6 w-full">
                            <ul class="space-y-3 text-gray-700">
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-information-line"></i>
                                    </div>
                                    <span>Biometric data collection is mandatory for all applicants</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-information-line"></i>
                                    </div>
                                    <span>Personal appearance may be required during the application process</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5 mr-2">
                                        <i class="ri-information-line"></i>
                                    </div>
                                    <span>Keep copies of all submitted documents for your reference</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Frequently Asked Questions -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Frequently Asked Questions</h2>
                        <div class="bg-white border border-gray-100 rounded-lg p-6">
                            <div class="space-y-4">
                                <div class="border-b border-gray-100 pb-4">
                                    <h3 class="font-medium mb-2">What is the processing time for a Swiss visa?</h3>
                                    <p class="text-gray-700">Standard processing takes about 15 working days, while
                                        express processing can be completed in 5 working days.</p>
                                </div>
                                <div class="border-b border-gray-100 pb-4">
                                    <h3 class="font-medium mb-2">Can I track my visa application status?</h3>
                                    <p class="text-gray-700">Yes, you can track your application status online using
                                        your application reference number.</p>
                                </div>
                                <div class="pb-4">
                                    <h3 class="font-medium mb-2">What happens if my visa is rejected?</h3>
                                    <p class="text-gray-700">In case of rejection, you will receive a detailed
                                        explanation and can appeal within 30 days of the decision.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Action Buttons -->
                    <div id="actionButtons"
                        class="fixed lg:sticky bottom-0 left-0 right-0 lg:left-auto lg:right-auto bg-white border-t border-gray-100 p-6 mt-auto z-50">
                        <div class="flex flex-row justify-between max-w-full space-x-4">
                            <button
                                class="border border-gray-300 text-gray-700 px-10 py-3 rounded-lg font-medium !rounded-button whitespace-nowrap flex-1 sm:flex-none">Back</button>
                            <button
                                class="bg-primary text-white px-10 py-3 rounded-lg font-medium !rounded-button whitespace-nowrap flex-1 sm:flex-none">Continue</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php

    require_once 'footer.php';

?>