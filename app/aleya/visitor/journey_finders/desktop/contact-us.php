<?php

require_once 'header.php';

?>
<!-- Hero Section -->
<section class="relative py-20 overflow-hidden">
    <div style="background-image: url('#api/search-image?query=professional%20travel%20agency%20customer%20service%20representatives%20in%20a%20modern%20office%20environment%20with%20soft%20lighting%2C%20people%20working%20at%20desks%20with%20headsets%2C%20travel%20posters%20on%20walls%2C%20clean%20and%20professional%20atmosphere%2C%20high%20quality%20professional%20photography&width=1920&height=600&seq=1&orientation=landscape'); background-size: cover; background-position: center;"
        class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-primary bg-opacity-70"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Contact Us</h1>
            <p class="text-xl text-white opacity-90 mb-8">We're here to help you plan your perfect journey. Reach
                out to our team of travel experts through multiple channels.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="tel:+919211287555" target="_blank"
                    class="bg-white text-primary px-6 py-3 !rounded-button font-medium hover:bg-gray-100 transition whitespace-nowrap">
                    <i class="ri-phone-line mr-2"></i>Call Now
                </a>
                <a href="https://api.whatsapp.com/send?phone=919211287555&text=Hello%20JourneyFinders" target="_blank"
                    class="bg-transparent border-2 border-white text-white px-6 py-3 !rounded-button font-medium hover:bg-white hover:bg-opacity-10 transition whitespace-nowrap">
                    <i class="ri-message-3-line mr-2"></i>Live Chat
                </a>
            </div>
        </div>
    </div>
</section>
<!-- Contact Methods Grid -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">How Can We Help You?</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Choose the most convenient way to connect with our
                travel experts who are ready to assist you 24/7.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Phone Support -->
            <div
                class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition duration-300 border border-gray-100">
                <div
                    class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ri-phone-line text-primary text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Phone Support</h3>
                <p class="text-gray-600 mb-4">Speak directly with our travel consultants for immediate assistance.
                </p>
                <div class="font-medium text-lg text-primary mb-2">+91 92112 87555</div>
                <p class="text-sm text-gray-500">Available 24/7</p>
                <div class="mt-6">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        Available Now
                    </span>
                </div>
            </div>
            <!-- Email Support -->
            <div
                class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition duration-300 border border-gray-100">
                <div
                    class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ri-mail-line text-primary text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Email Support</h3>
                <p class="text-gray-600 mb-4">Send us your inquiries and we'll respond within 24 hours.</p>
                <div class="font-medium text-lg text-primary mb-2">care@journeyfinders.com</div>
                <p class="text-sm text-gray-500">Response time: 24 hours</p>
                <div class="mt-6">
                    <a href="mailto:care@journeyfinders.com"
                        class="bg-primary text-white px-4 py-2 !rounded-button text-sm font-medium hover:bg-indigo-600 transition whitespace-nowrap">
                        <i class="ri-mail-send-line mr-1"></i>Send Email
                    </a>
                </div>
            </div>
            <!-- Live Chat -->
            <div
                class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition duration-300 border border-gray-100">
                <div
                    class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ri-chat-1-line text-primary text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Live Chat</h3>
                <p class="text-gray-600 mb-4">Chat with our travel experts in real-time for instant help.</p>
                <div class="font-medium text-lg text-primary mb-2">Live Chat Support</div>
                <p class="text-sm text-gray-500">Average wait time: 2 minutes</p>
                <div class="mt-6">
                    <a href="https://api.whatsapp.com/send?phone=919211287555&text=Hello%20JourneyFinders"
                        target="_blank"
                        class="bg-white border border-primary text-primary px-4 py-2 !rounded-button text-sm font-medium hover:bg-primary hover:text-white transition whitespace-nowrap">
                        <i class="ri-chat-1-line mr-1"></i>Start Chat
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Contact Form Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex flex-col md:flex-row">
                <!-- Form Left Side -->
                <div class="md:w-7/12 p-8 md:p-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Send Us a Message</h2>
                    <p class="text-gray-600 mb-8">Fill out the form below and our team will get back to you as soon
                        as possible.</p>
                    <form method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name
                                    *</label>
                                <input type="text" id="name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded text-gray-900 text-sm"
                                    placeholder="Avinash Kumar" required>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                    Address *</label>
                                <input type="email" id="email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded text-gray-900 text-sm"
                                    placeholder="avinash@gmail.com" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone
                                    Number</label>
                                <input type="tel" id="phone"
                                    class="w-full px-4 py-3 border border-gray-300 rounded text-gray-900 text-sm"
                                    placeholder="9211287555" pattern="[0-9]{10}">
                            </div>
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject
                                    *</label>
                                <div class="relative">
                                    <select id="subject"
                                        class="w-full px-4 py-3 border border-gray-300 rounded text-gray-900 text-sm appearance-none pr-8"
                                        required>
                                        <option value="" disabled selected>Select a subject</option>
                                        <option value="general">General Inquiry</option>
                                        <option value="booking">Booking Question</option>
                                        <option value="support">Customer Support</option>
                                        <option value="feedback">Feedback</option>
                                        <option value="partnership">Partnership Opportunity</option>
                                    </select>
                                    <div
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                        <i class="ri-arrow-down-s-line text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message
                                *</label>
                            <textarea id="message" rows="5"
                                class="w-full px-4 py-3 border border-gray-300 rounded text-gray-900 text-sm"
                                placeholder="Tell us how we can help you..." required></textarea>
                        </div>
                        <div class="mb-6">
                            <div class="flex items-start">
                                <div class="custom-checkbox" id="privacy-checkbox"></div>
                                <label for="privacy-checkbox" class="ml-3 text-sm text-gray-600">
                                    I agree to the <a href="#" class="text-primary hover:underline">Privacy
                                        Policy</a> and consent to having my data processed.
                                </label>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full bg-primary text-white py-3 !rounded-button font-medium hover:bg-indigo-600 transition whitespace-nowrap">
                            <i class="ri-send-plane-fill mr-2"></i>Send Message
                        </button>
                    </form>
                </div>
                <!-- Form Right Side -->
                <div class="md:w-5/12 bg-indigo-50 p-8 md:p-12 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">Contact Information</h3>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div
                                    class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-primary mr-4">
                                    <i class="ri-map-pin-line"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-1">Main Office</h4>
                                    <p class="text-gray-600">Roshanara road<br>New Delhi, 110007, India
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-primary mr-4">
                                    <i class="ri-time-line"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-1">Business Hours</h4>
                                    <p class="text-gray-600">
                                        Monday - Friday: 09:00 AM - 07:00 PM<br>
                                        Saturday: 10:00 AM - 6:00 PM<br>
                                        Sunday: Closed
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-primary mr-4">
                                    <i class="ri-customer-service-2-line"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-1">Customer Support</h4>
                                    <p class="text-gray-600">24/7 Emergency Travel Assistance<br>+91 92112 87555
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8">
                        <h4 class="font-medium text-gray-900 mb-4">Connect With Us</h4>
                        <div class="flex space-x-4">
                            <a href="#"
                                class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-gray-600 hover:text-primary transition">
                                <i class="ri-facebook-fill"></i>
                            </a>
                            <a href="#"
                                class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-gray-600 hover:text-primary transition">
                                <i class="ri-twitter-fill"></i>
                            </a>
                            <a href="#"
                                class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-gray-600 hover:text-primary transition">
                                <i class="ri-instagram-line"></i>
                            </a>
                            <a href="#"
                                class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-gray-600 hover:text-primary transition">
                                <i class="ri-linkedin-fill"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- FAQ Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Find answers to common questions about our services,
                booking process, and travel policies.</p>
        </div>
        <div class="max-w-3xl mx-auto">
            <div class="space-y-4" id="faq-container">
                <!-- FAQ Item 1 -->
                <div class="faq-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
                    <div class="faq-question p-5 cursor-pointer flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">How do I book a travel package?</h3>
                        <div class="faq-icon w-6 h-6 flex items-center justify-center text-primary">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </div>
                    <div class="faq-answer px-5 text-gray-600">
                        <p>You can book a travel package through our website by browsing available options,
                            selecting your preferred package, and following the booking steps. Alternatively, you
                            can contact our travel consultants via phone, email, or live chat for personalized
                            assistance with your booking. We recommend booking at least 3 months in advance for
                            international trips to secure the best rates and availability.</p>
                    </div>
                </div>
                <!-- FAQ Item 2 -->
                <div class="faq-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
                    <div class="faq-question p-5 cursor-pointer flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">What is your cancellation policy?</h3>
                        <div class="faq-icon w-6 h-6 flex items-center justify-center text-primary">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </div>
                    <div class="faq-answer px-5 text-gray-600">
                        <p>Our standard cancellation policy allows for a full refund if canceled 30 days or more
                            before departure. Cancellations made 15-29 days before departure receive a 50% refund.
                            Cancellations less than 15 days before departure are non-refundable. However, we offer
                            travel insurance options that can provide coverage for unexpected cancellations. Each
                            package may have specific terms, so please check the details at the time of booking.</p>
                    </div>
                </div>
                <!-- FAQ Item 3 -->
                <div class="faq-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
                    <div class="faq-question p-5 cursor-pointer flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Do you offer travel insurance?</h3>
                        <div class="faq-icon w-6 h-6 flex items-center justify-center text-primary">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </div>
                    <div class="faq-answer px-5 text-gray-600">
                        <p>Yes, we offer comprehensive travel insurance options through our trusted partners. Our
                            insurance packages can cover trip cancellation, medical emergencies, lost luggage,
                            travel delays, and more. We highly recommend purchasing travel insurance for all
                            international trips. Our consultants can help you select the right coverage based on
                            your destination, trip duration, and specific needs.</p>
                    </div>
                </div>
                <!-- FAQ Item 4 -->
                <div class="faq-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
                    <div class="faq-question p-5 cursor-pointer flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Can I customize a travel package?</h3>
                        <div class="faq-icon w-6 h-6 flex items-center justify-center text-primary">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </div>
                    <div class="faq-answer px-5 text-gray-600">
                        <p>Absolutely! We specialize in creating customized travel experiences tailored to your
                            preferences and needs. Our travel consultants can help you design a personalized
                            itinerary, including accommodations, activities, transportation, and special requests.
                            Contact us through phone, email, or schedule a consultation to start planning your
                            custom travel package.</p>
                    </div>
                </div>
                <!-- FAQ Item 5 -->
                <div class="faq-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
                    <div class="faq-question p-5 cursor-pointer flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">What payment methods do you accept?</h3>
                        <div class="faq-icon w-6 h-6 flex items-center justify-center text-primary">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </div>
                    <div class="faq-answer px-5 text-gray-600">
                        <p>We accept various payment methods including major credit cards (Visa, MasterCard,
                            American Express), PayPal, bank transfers, and in some locations, cash payments. For
                            certain destinations and packages, we also offer payment plans allowing you to pay in
                            installments. All online payments are secured with industry-standard encryption to
                            protect your financial information.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>