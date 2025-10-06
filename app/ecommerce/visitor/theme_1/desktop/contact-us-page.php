<?php

include_once 'header.php';

?>

<div class="relative h-[280px] w-full mb-16 bg-[#F9F5F2] mt-16">
    <div class="container mx-auto px-4 h-full flex flex-col justify-center items-center text-center relative z-10">
        <div class="flex items-center text-sm text-gray-500 mb-6">
            <a href="https://readdy.ai/home/75ca93db-7f39-4a6e-aecd-73a6bbf40a50/0552cf37-1ea1-4d63-9b0a-130ac1cc039d"
                data-readdy="true" class="hover:text-primary transition-colors">Home</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Contact Us</span>
        </div>
        <h1 class="font-serif text-4xl md:text-5xl text-gray-900 mb-4">Contact Us</h1>
        <p class="text-gray-600 text-lg max-w-2xl">We'd love to hear from you. Let us know how we can help.</p>
    </div>
</div>
<!-- Contact Information Section -->
<section class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 gap-12">
        <div>
            <h2 class="font-serif text-3xl mb-8">Our Branches</h2>
            <div class="grid grid-cols-1 gap-8">
                <div class="bg-white p-8 rounded-lg border border-gray-100 hover:border-primary/20 transition-colors">
                    <div class="flex items-start mb-6">
                        <div
                            class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-4 flex-shrink-0">
                            <i class="ri-map-pin-line text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-serif text-lg mb-2">Gurugram Branch</h3>
                            <p class="text-gray-600 mb-2">House no 51, Shop No 1, Banjara Market, Sector 58, Ghata,
                                Haryana 122011</p>
                            <p class="text-sm text-primary mb-4">Main Store</p>
                            <div class="flex flex-col gap-2">
                                <a href="tel:+919810494275"
                                    class="text-gray-600 hover:text-primary transition-colors flex items-center">
                                    <i class="ri-phone-line mr-2"></i>+91-9810494275
                                </a>
                                <a href="mailto:vinodhomedecor78@gmail.com"
                                    class="text-gray-600 hover:text-primary transition-colors flex items-center">
                                    <i class="ri-mail-line mr-2"></i>vinodhomedecor78@gmail.com
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="map-container w-full max-h-[500px] rounded-lg overflow-hidden">
                        <div class="relative w-full aspect-[16/9] rounded overflow-hidden shadow">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3508.975202387228!2d77.1174805!3d28.4200051!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d194bba8aead3%3A0x9cc58b63970bba87!2sVinod%20Home%20decor%20Banjara%20Market%20sector%2058!5e0!3m2!1sen!2sin!4v1755553758975!5m2!1sen!2sin"
                                class="absolute top-0 left-0 w-full h-full border-0" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="bg-gray-50 p-8 rounded-lg shadow-sm mt-8">
        <h2 class="font-serif text-3xl mb-6">Send Us a Message</h2>
        <form id="contactForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-gray-700 mb-2">Full Name *</label>
                    <input type="text" id="name" name="name" required
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label for="email" class="block text-gray-700 mb-2">Email Address *</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label for="phone" class="block text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone"
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label for="subject" class="block text-gray-700 mb-2">Subject *</label>
                    <div class="relative">
                        <select id="subject" name="subject" required
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded focus:outline-none focus:border-primary appearance-none pr-8">
                            <option value="">Select a subject</option>
                            <option value="order">Order Inquiry</option>
                            <option value="custom">Custom Arrangement</option>
                            <option value="wedding">Wedding Consultation</option>
                            <option value="corporate">Corporate Services</option>
                            <option value="feedback">Feedback</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i class="ri-arrow-down-s-line text-gray-500"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-6">
                <label for="message" class="block text-gray-700 mb-2">Your Message *</label>
                <textarea id="message" name="message" rows="5" required
                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded focus:outline-none focus:border-primary"></textarea>
            </div>
            <button type="submit"
                class="bg-primary text-white px-6 py-3 rounded-button whitespace-nowrap hover:bg-opacity-90 transition-colors">Send
                Message</button>
        </form>
    </div>
    </div>
</section>
<!-- Customer Service Options -->
<section class="container mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h2 class="font-serif text-3xl md:text-4xl mb-4">Ways to Reach Us</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">Choose the most convenient way to connect with our customer service
            team</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-8">
        <div
            class="contact-card bg-white p-8 rounded-lg border border-gray-100 hover:border-primary/20 text-center transition-colors">
            <div
                class="w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mx-auto mb-6">
                <i class="ri-whatsapp-line text-2xl"></i>
            </div>
            <h3 class="font-serif text-xl mb-3">WhatsApp</h3>
            <p class="text-gray-600 mb-6">Message us directly on WhatsApp for quick responses and updates.</p>
            <a href="https://wa.me/919810494275" target="_blank"
                class="inline-block bg-[#25D366] text-white px-6 py-3 rounded-button whitespace-nowrap hover:bg-opacity-90 transition-colors">WhatsApp
                Us</a>
        </div>
        <div
            class="contact-card bg-white p-8 rounded-lg border border-gray-100 hover:border-primary/20 text-center transition-colors">
            <div
                class="w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mx-auto mb-6">
                <i class="ri-mail-send-line text-2xl"></i>
            </div>
            <h3 class="font-serif text-xl mb-3">Email Support</h3>
            <p class="text-gray-600 mb-6">Send us an email and we'll get back to you within 24 hours.</p>
            <a href="mailto:vinodhomedecor78@gmail.com"
                class="inline-block border border-primary text-primary px-6 py-3 rounded-button whitespace-nowrap hover:bg-primary hover:text-white transition-colors">Email
                Us</a>
        </div>
        <div
            class="contact-card bg-white p-8 rounded-lg border border-gray-100 hover:border-primary/20 text-center transition-colors">
            <div
                class="w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mx-auto mb-6">
                <i class="ri-instagram-line text-2xl"></i>
            </div>
            <h3 class="font-serif text-xl mb-3">Social Media</h3>
            <p class="text-gray-600 mb-6">Connect with us on social media for updates and inspiration.</p>
            <div class="flex justify-center space-x-3">
                <a href="https://www.facebook.com/profile.php?id=61560513337525" target="_blank"
                    class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white transition-colors">
                    <i class="ri-facebook-fill"></i>
                </a>
                <a href="https://www.instagram.com/vinodhomedecor/" target="_blank"
                    class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white transition-colors">
                    <i class="ri-instagram-line"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php

include_once 'footer.php';

?>