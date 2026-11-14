<?php
include_once 'header.php';
?>

<!-- Editorial Header -->
<section class="bg-[#FAF7F5] py-16 md:py-20 border-b border-[#E8DDD5]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <!-- Breadcrumb -->
        <nav class="text-xs sm:text-sm mb-5 text-[#6E675F] flex justify-center" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li><a href="/" class="text-[#C47C68] hover:text-[#242220] transition-colors">Home</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-[#242220] font-medium" aria-current="page">Contact &amp; Ateliers</li>
            </ol>
        </nav>
        <span class="vl-eyebrow">Atelier Concierge</span>
        <h1 class="font-serif text-3xl sm:text-5xl text-[#242220] mt-1 mb-4 font-normal">
            Connect with Our Floral Concierge
        </h1>
        <p class="text-base sm:text-lg text-[#6E675F] max-w-xl mx-auto leading-relaxed">
            Whether inquiring about bespoke commissions, same-day delivery coordination, private workshops, or wedding florals, we welcome your conversation.
        </p>
    </div>
</section>

<!-- Atelier Locations Grid -->
<section class="py-16 md:py-20 bg-white border-b border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="vl-eyebrow">Flagship Studios</span>
            <h2 class="font-serif text-3xl sm:text-4xl text-[#242220] mt-2 mb-3 font-normal">
                Visit Our Delhi NCR Ateliers
            </h2>
            <p class="text-sm text-[#6E675F]">
                Open daily from 9:00 AM to 9:00 PM for private consultations and floral pickups.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Studio 1: Chhatarpur -->
            <div class="bg-[#FAF7F5] border border-[#E8DDD5] rounded-2xl p-6 sm:p-8 flex flex-col justify-between shadow-sm">
                <div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <span class="vl-badge vl-badge-terracotta mb-2">Main Flagship Atelier</span>
                            <h3 class="font-serif text-2xl text-[#242220]">Chhatarpur Studio</h3>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-white border border-[#E8DDD5] text-[#C47C68] flex items-center justify-center text-lg flex-shrink-0">
                            <i class="ri-map-pin-2-line" aria-hidden="true"></i>
                        </div>
                    </div>
                    <p class="text-sm text-[#6E675F] leading-relaxed mb-4">
                        Shop No. A179/2, Mehrauli-Gurgaon Road, Andheria Mor Village, Chhatarpur, New Delhi, Delhi 110030
                    </p>
                    <div class="space-y-2 text-sm text-[#242220] mb-6">
                        <div class="flex items-center gap-2.5">
                            <i class="ri-phone-line text-[#C47C68]" aria-hidden="true"></i>
                            <a href="tel:+919910744119" class="hover:text-[#C47C68] transition-colors">+91 99107 44119</a>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="ri-mail-line text-[#C47C68]" aria-hidden="true"></i>
                            <a href="mailto:sd@andblooms.com" class="hover:text-[#C47C68] transition-colors">sd@andblooms.com</a>
                        </div>
                    </div>
                </div>
                <div class="aspect-[16/9] w-full rounded-xl overflow-hidden border border-[#E8DDD5]">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d261800.09945971734!2d76.87707860663976!3d28.448943365668505!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d1fa00bcfb3e1%3A0xaee164484f4aea1f!2s%26%20BLOOMS!5e0!3m2!1sen!2sin!4v1752140847448!5m2!1sen!2sin"
                        class="w-full h-full border-0" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" title="Chhatarpur Atelier Map">
                    </iframe>
                </div>
            </div>

            <!-- Studio 2: Gurugram -->
            <div class="bg-[#FAF7F5] border border-[#E8DDD5] rounded-2xl p-6 sm:p-8 flex flex-col justify-between shadow-sm">
                <div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <span class="vl-badge vl-badge-sage mb-2">Corporate &amp; Boutique Studio</span>
                            <h3 class="font-serif text-2xl text-[#242220]">Gurugram Studio</h3>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-white border border-[#E8DDD5] text-[#3D5447] flex items-center justify-center text-lg flex-shrink-0">
                            <i class="ri-map-pin-2-line" aria-hidden="true"></i>
                        </div>
                    </div>
                    <p class="text-sm text-[#6E675F] leading-relaxed mb-4">
                        LG-K3, South Point Mall, Golf Course Road, Sector 53, Gurugram, Haryana 122002
                    </p>
                    <div class="space-y-2 text-sm text-[#242220] mb-6">
                        <div class="flex items-center gap-2.5">
                            <i class="ri-phone-line text-[#3D5447]" aria-hidden="true"></i>
                            <a href="tel:+919910744119" class="hover:text-[#3D5447] transition-colors">+91 99107 44119</a>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="ri-mail-line text-[#3D5447]" aria-hidden="true"></i>
                            <a href="mailto:sd@andblooms.com" class="hover:text-[#3D5447] transition-colors">sd@andblooms.com</a>
                        </div>
                    </div>
                </div>
                <div class="aspect-[16/9] w-full rounded-xl overflow-hidden border border-[#E8DDD5]">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d261800.09945971734!2d76.87707860663976!3d28.448943365668505!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d19fdeea8bb45%3A0xef63eb649801b6f6!2s%26%20BLOOMS!5e0!3m2!1sen!2sin!4v1752140990991!5m2!1sen!2sin"
                        class="w-full h-full border-0" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" title="Gurugram Atelier Map">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Direct Consultation Form Section -->
<section class="py-16 md:py-24 bg-[#FAF7F5]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-[#E8DDD5] rounded-2xl p-6 sm:p-10 shadow-sm">
            <div class="text-center mb-8">
                <span class="vl-eyebrow">Bespoke Inquiries</span>
                <h2 class="font-serif text-2xl sm:text-3xl text-[#242220] mt-1 mb-2 font-normal">
                    Send Our Florists a Message
                </h2>
                <p class="text-sm text-[#6E675F]">
                    We respond to all bespoke requests within four business hours.
                </p>
            </div>

            <form id="contactForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="vl-field">
                        <label for="name" class="text-sm font-medium text-[#242220]">Your Name <span class="text-[#C47C68]">*</span></label>
                        <input type="text" id="name" name="name" required class="vl-input" placeholder="e.g. Tara Sharma">
                    </div>
                    <div class="vl-field">
                        <label for="email" class="text-sm font-medium text-[#242220]">Email Address <span class="text-[#C47C68]">*</span></label>
                        <input type="email" id="email" name="email" required class="vl-input" placeholder="tara@example.com">
                    </div>
                    <div class="vl-field">
                        <label for="phone" class="text-sm font-medium text-[#242220]">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="vl-input" placeholder="+91 98100 00000">
                    </div>
                    <div class="vl-field">
                        <label for="subject" class="text-sm font-medium text-[#242220]">Inquiry Subject <span class="text-[#C47C68]">*</span></label>
                        <select id="subject" name="subject" required class="vl-input">
                            <option value="">Select a subject</option>
                            <option value="order">Order Coordination &amp; Tracking</option>
                            <option value="custom">Bespoke Floral Commission</option>
                            <option value="wedding">Weddings &amp; Social Galas</option>
                            <option value="corporate">Corporate Gifting &amp; Retainers</option>
                            <option value="workshop">Private Masterclasses</option>
                            <option value="other">Other Inquiry</option>
                        </select>
                    </div>
                </div>

                <div class="vl-field">
                    <label for="message" class="text-sm font-medium text-[#242220]">Your Message <span class="text-[#C47C68]">*</span></label>
                    <textarea id="message" name="message" rows="5" required class="vl-input" placeholder="Tell us about your event, preferred dates, floral inspirations, or delivery requests..."></textarea>
                </div>

                <div class="pt-2 text-center">
                    <button type="submit" class="vl-btn-primary py-3.5 px-8 text-sm">
                        <span>Send Message</span>
                        <i class="ri-send-plane-line" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Direct Channels & WhatsApp -->
<section class="py-14 bg-white border-t border-[#E8DDD5]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div class="p-6 rounded-xl border border-[#E8DDD5] bg-[#FAF7F5]">
                <div class="w-12 h-12 rounded-full bg-white border border-[#E8DDD5] text-[#25D366] flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="ri-whatsapp-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-lg text-[#242220] mb-2">WhatsApp Concierge</h3>
                <p class="text-xs text-[#6E675F] mb-4">Instant floral consultations &amp; live bouquet previews.</p>
                <a href="https://wa.me/919910744119" target="_blank" rel="noopener noreferrer"
                    class="text-xs uppercase tracking-wider font-semibold text-[#242220] hover:text-[#C47C68] transition-colors inline-flex items-center gap-1">
                    <span>Chat on WhatsApp</span>
                    <i class="ri-arrow-right-line" aria-hidden="true"></i>
                </a>
            </div>

            <div class="p-6 rounded-xl border border-[#E8DDD5] bg-[#FAF7F5]">
                <div class="w-12 h-12 rounded-full bg-white border border-[#E8DDD5] text-[#C47C68] flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="ri-phone-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-lg text-[#242220] mb-2">Telephone Concierge</h3>
                <p class="text-xs text-[#6E675F] mb-4">Speak directly with our senior florists daily.</p>
                <a href="tel:+919910744119"
                    class="text-xs uppercase tracking-wider font-semibold text-[#242220] hover:text-[#C47C68] transition-colors inline-flex items-center gap-1">
                    <span>+91 99107 44119</span>
                    <i class="ri-arrow-right-line" aria-hidden="true"></i>
                </a>
            </div>

            <div class="p-6 rounded-xl border border-[#E8DDD5] bg-[#FAF7F5]">
                <div class="w-12 h-12 rounded-full bg-white border border-[#E8DDD5] text-[#3D5447] flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="ri-mail-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-lg text-[#242220] mb-2">Email Desk</h3>
                <p class="text-xs text-[#6E675F] mb-4">Detailed proposals for weddings and luxury retainers.</p>
                <a href="mailto:sd@andblooms.com"
                    class="text-xs uppercase tracking-wider font-semibold text-[#242220] hover:text-[#3D5447] transition-colors inline-flex items-center gap-1">
                    <span>sd@andblooms.com</span>
                    <i class="ri-arrow-right-line" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php
include_once 'footer.php';
?>
