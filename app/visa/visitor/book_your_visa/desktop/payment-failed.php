<?php

require_once 'header.php';

?>

<main class="flex-grow pt-32 pb-20">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="relative h-48 bg-cover bg-center">
                <div class="absolute inset-0 bg-gradient-to-r from-red-600/80 to-red-400/50"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <h1 class="text-3xl md:text-3xl font-bold text-white text-center">Payment Processing Failed</h1>
                </div>
            </div>

            <div class="p-8">
                <div class="max-w-2xl mx-auto">
                    <div class="text-center mb-12">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="ri-close-line text-red-600 ri-3x"></i>
                        </div>
                        <p class="text-gray-700 text-lg mb-4">We couldn’t process your request due to a payment
                            issue.<br />
                            Please ensure your payment method is valid and try again.</p>
                    </div>

                    <div class="text-center space-y-4">
                        <a href="/"
                            class="inline-flex items-center justify-center bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-8 py-3 cursor-pointer whitespace-nowrap">
                            <i class="ri-home-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                            Return to Homepage
                        </a>
                        <p class="text-gray-600">Need help? <a href="/contact-us/"
                                class="text-primary hover:underline">Contact our support team</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php

require_once 'footer.php';

?>