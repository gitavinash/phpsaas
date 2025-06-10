<?php

require_once 'header.php';

?>
<main class="flex-grow pt-32 pb-20">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="relative h-48 bg-cover bg-center">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/80 to-primary/50"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-white text-center">Registration Successful!</h1>
                </div>
            </div>

            <div class="p-8">
                <div class="max-w-2xl mx-auto">
                    <div class="text-center mb-12">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="ri-check-line text-green-600 ri-3x"></i>
                        </div>
                        <p class="text-gray-700 text-lg mb-4">Thank you for registering as a travel agent partner
                            with <?php echo SITE_NAME ?>. Your application has been submitted successfully and is
                            currently under
                            review.</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6 mb-8">
                        <h3 class="font-semibold text-gray-800 mb-4">Application Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Submission Date</span>
                                <span class="font-medium text-gray-800" id="submissionDate"><?php echo date('F j, Y h:i A');
                                                                                            ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Status</span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <i class="ri-time-line mr-1 w-4 h-4 flex items-center justify-center"></i>
                                    Under Review
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-xl p-6 mb-8">
                        <h3 class="font-semibold text-gray-800 mb-4">Next Steps</h3>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="ri-mail-line text-primary"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-800 mb-1">Check Your Email</h4>
                                    <p class="text-gray-600">We've sent a confirmation email with important details
                                        about your application.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="ri-time-line text-primary"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-800 mb-1">Application Review</h4>
                                    <p class="text-gray-600">Our team will review your application within 1-2
                                        business days.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="ri-shield-check-line text-primary"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-800 mb-1">Verification Process</h4>
                                    <p class="text-gray-600">We may contact you for additional documentation or
                                        verification if needed.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center space-y-4">
                        <a href="/"
                            class="inline-flex items-center justify-center bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-8 py-3 cursor-pointer whitespace-nowrap">
                            <i class="ri-home-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                            Return to Homepage
                        </a>
                        <p class="text-gray-600">Need help? <a href="/contact-us/"
                                class="text-primary hover:underline">Contact
                                our support team</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php

require_once 'footer.php';

?>