<?php

    require_once 'header.php';

?>
<main class="pt-32 pb-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-8 md:mb-12 px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3 md:mb-4">Contact Us</h1>
            <p class="text-gray-600 max-w-2xl mx-auto text-sm md:text-base">We're here to help with any questions
                about visa applications. Reach out to our team through any of the methods below.</p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 md:gap-8 max-w-6xl mx-auto px-4 lg:px-0">
            <div class="lg:col-span-3">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Send Us a Message</h2>
                    <form id="contactForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="name">Full
                                    Name*</label>
                                <input type="text" id="name" name="name"
                                    class="form-input w-full px-4 py-2 border border-gray-300 !rounded-button"
                                    placeholder="Enter your full name" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="email">Email
                                    Address*</label>
                                <input type="email" id="email" name="email"
                                    class="form-input w-full px-4 py-2 border border-gray-300 !rounded-button"
                                    placeholder="Enter your email address" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="phone">Phone
                                Number</label>
                            <input type="tel" id="phone" name="phone"
                                class="form-input w-full px-4 py-2 border border-gray-300 !rounded-button"
                                placeholder="Enter your phone number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="subject">Subject*</label>
                            <div class="relative">
                                <select id="subject" name="subject"
                                    class="form-input w-full px-4 py-2 border border-gray-300 !rounded-button appearance-none bg-white pr-8"
                                    required>
                                    <option value="" disabled selected>Select a subject</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="application">Visa Application</option>
                                    <option value="status">Application Status</option>
                                    <option value="documents">Document Requirements</option>
                                    <option value="payment">Payment Issues</option>
                                    <option value="feedback">Feedback</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i
                                        class="ri-arrow-down-s-line text-gray-500 w-5 h-5 flex items-center justify-center"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="message">Message*</label>
                            <textarea id="message" name="message" rows="5"
                                class="form-input w-full px-4 py-2 border border-gray-300 !rounded-button"
                                placeholder="Enter your message here" required></textarea>
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="terms" name="terms" type="checkbox"
                                    class="h-4 w-4 text-primary border-gray-300 !rounded-button" required>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="text-gray-600">I agree to the <a href="#"
                                        class="text-primary hover:underline">Terms and Conditions</a> and <a href="#"
                                        class="text-primary hover:underline">Privacy Policy</a></label>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full bg-primary text-white font-medium py-3 !rounded-button hover:bg-primary/90 cursor-pointer whitespace-nowrap">Send
                            Message</button>
                    </form>
                </div>
            </div>
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden  mb-6">
                    <div class="h-64 map-container relative">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3499.8753580337525!2d77.14701267547494!3d28.69337488131522!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d023326bfe819%3A0x1239ba9b904a5e90!2sPleco%20Migration%20Private%20Limited!5e0!3m2!1sen!2sin!4v1744657094104!5m2!1sen!2sin"
                            width="100%" height="415" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Contact Information</h2>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-primary/10 rounded-full p-3 mr-4">
                                <i class="ri-map-pin-line text-primary w-6 h-6 flex items-center justify-center"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 mb-1">Office Address</h3>
                                <p class="text-gray-600">1234 Visa Avenue, Suite 500<br>New York, NY 10001<br>United
                                    States</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-primary/10 rounded-full p-3 mr-4">
                                <i class="ri-time-line text-primary w-6 h-6 flex items-center justify-center"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 mb-1">Business Hours</h3>
                                <p class="text-gray-600">Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM -
                                    2:00 PM<br>Sunday: Closed</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-primary/10 rounded-full p-3 mr-4">
                                <i class="ri-mail-line text-primary w-6 h-6 flex items-center justify-center"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 mb-1">Email Us</h3>
                                <p class="text-gray-600">
                                    <a href="mailto:info@Book Your VISA.com"
                                        class="hover:text-primary">info@Book Your VISA.com</a><br>
                                    <a href="mailto:support@Book Your VISA.com"
                                        class="hover:text-primary">support@Book Your VISA.com</a>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-primary/10 rounded-full p-3 mr-4">
                                <i class="ri-phone-line text-primary w-6 h-6 flex items-center justify-center"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 mb-1">Call Us</h3>
                                <p class="text-gray-600">
                                    <a href="tel:+18001234567" class="hover:text-primary">+1 (800) 123-4567</a>
                                    (Toll Free)<br>
                                    <a href="tel:+12125551234" class="hover:text-primary">+1 (212) 555-1234</a>
                                    (Direct)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="max-w-6xl mx-auto mt-16">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">How long does the visa application process take?
                        </h3>
                        <p class="text-gray-600">Processing times vary depending on the visa type and destination
                            country. Tourist visas typically take 5-10 business days, while work visas may take 2-4
                            weeks.</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">What documents do I need for a visa application?
                        </h3>
                        <p class="text-gray-600">Required documents generally include a valid passport, application
                            form, photos, proof of travel insurance, flight itinerary, and financial statements.</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Can I expedite my visa application?</h3>
                        <p class="text-gray-600">Yes, we offer expedited processing for an additional fee. This can
                            reduce processing time by up to 50% depending on the visa type.</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">What if my visa application is rejected?</h3>
                        <p class="text-gray-600">We provide a complimentary consultation to understand the reasons
                            for rejection and assist with reapplication with improved documentation.</p>
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <a href="#" class="inline-flex items-center text-primary hover:text-primary/80 font-medium">
                        View all FAQs
                        <i class="ri-arrow-right-line ml-1 w-4 h-4 flex items-center justify-center"></i>
                    </a>
                </div>
            </div>
        </div> -->
        <!-- <div
            class="max-w-6xl mx-auto mt-8 md:mt-16 flex flex-col md:flex-row items-center justify-between bg-gradient-to-r from-primary/10 to-primary/5 rounded-lg shadow-lg p-6 md:p-8 mx-4 lg:mx-auto">
            <div class="text-center md:text-left">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">Need immediate assistance?</h2>
                <p class="text-gray-600 max-w-xl text-sm md:text-base">Our visa experts are available for quick
                    consultations to address your urgent queries.</p>
            </div>
            <div class="mt-6 md:mt-0 flex flex-col sm:flex-row gap-4 sm:space-x-4 w-full md:w-auto">
                <button
                    class="bg-white border border-primary text-primary hover:bg-gray-50 font-medium !rounded-button px-4 md:px-6 py-3 flex items-center justify-center whitespace-nowrap">
                    <i class="ri-calendar-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                    Schedule a Call
                </button>
                <button
                    class="bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-4 md:px-6 py-3 flex items-center justify-center whitespace-nowrap">
                    <i class="ri-chat-1-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                    Start Live Chat
                </button>
            </div>
        </div> -->
    </div>
</main>
<?php

    require_once 'footer.php'

?>