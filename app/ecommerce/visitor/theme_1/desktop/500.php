<?php

include_once 'header.php';

?>

<section class="error-bg relative min-h-screen flex items-center justify-center mt-32 mb-16">
    <div class="absolute inset-0 bg-white bg-opacity-80"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-2xl mx-auto text-center fade-in">
            <div class="floating-animation mb-8">
                <div class="w-32 h-32 mx-auto mb-6 flex items-center justify-center text-primary">
                    <i class="ri-sofa-line ri-10x"></i>
                </div>
            </div>
            <h1 class="text-8xl md:text-9xl font-bold text-primary mb-4">500</h1>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Something Went Wrong</h2>
            <p class="text-lg text-gray-600 mb-8 max-w-lg mx-auto">
                Oh no! A petal fell off our system bouquet. <br>
                We're working to fix this bloom-tiful mess as quickly as possible.
                <br class="hidden sm:block">
                Please try again shortly or head back to the homepage.
            </p>
            <form action="/search/" method="GET"
                class="flex flex-col gap-4 justify-center items-center max-w-2xl mx-auto">
                <!-- Search Input -->
                <div class="relative w-full">
                    <input type="text" name="q" placeholder="What are you looking for?"
                        class="w-full bg-white text-gray-800 border-2 border-primary px-6 py-4 !rounded-button text-lg font-medium transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50"
                        required>
                    <div
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 w-6 h-6 flex items-center justify-center text-gray-400 pointer-events-none">
                        <i class="ri-search-line ri-xl"></i>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center w-full">
                    <button type="submit"
                        class="bg-primary text-white px-12 py-3 !rounded-button font-medium hover:bg-opacity-90 transition-all duration-300 text-center whitespace-nowrap flex items-center justify-center">
                        <div class="w-5 h-5 flex items-center justify-center mr-2">
                            <i class="ri-search-line"></i>
                        </div>
                        <span>Search</span>
                    </button>
                </div>
            </form>

            <div class="mt-8">
                <p class="text-gray-500 text-sm">
                    Need help? Contact our customer service at
                    <a href="tel:+919910744119" class="text-primary hover:underline">+91-9910744119</a>
                </p>
            </div>
        </div>
    </div>
</section>

<?php

include_once 'footer.php';

?>