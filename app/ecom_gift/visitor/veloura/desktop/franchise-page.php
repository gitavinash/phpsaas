<?php
include_once 'header.php';
?>

<!-- Editorial Header -->
<section class="bg-[#FAF7F5] py-16 md:py-24 border-b border-[#E8DDD5]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <!-- Breadcrumb -->
        <nav class="text-xs sm:text-sm mb-5 text-[#6E675F] flex justify-center" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li><a href="/" class="text-[#C47C68] hover:text-[#242220] transition-colors">Home</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-[#242220] font-medium" aria-current="page">Franchise &amp; Partnerships</li>
            </ol>
        </nav>
        <span class="vl-eyebrow">Atelier Expansion</span>
        <h1 class="font-serif text-3xl sm:text-5xl text-[#242220] mt-1 mb-4 font-normal">
            Boutique Atelier Franchise Partnerships
        </h1>
        <p class="text-base sm:text-lg text-[#6E675F] max-w-2xl mx-auto leading-relaxed">
            Partner with one of India's fastest-growing luxury floral design houses. Bring Veloura's European botanical aesthetic and high-margin gifting atelier to your city.
        </p>
    </div>
</section>

<!-- Why Partner With Veloura -->
<section class="py-16 md:py-24 bg-white border-b border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 relative">
                <div class="relative rounded-2xl overflow-hidden shadow-lg border border-[#E8DDD5] aspect-[4/3] max-w-lg mx-auto">
                    <img src="<?php echo htmlspecialchars(USER_IMAGES . 'andblooms-store.jpg', ENT_QUOTES, 'UTF-8'); ?>"
                        alt="Veloura Retail Atelier"
                        class="w-full h-full object-cover">
                </div>
            </div>

            <div class="lg:col-span-6 space-y-6">
                <span class="vl-eyebrow">Proven Business Model</span>
                <h2 class="font-serif text-3xl sm:text-4xl text-[#242220] leading-tight font-normal">
                    Turn Your Passion for Floristry into a Thriving Luxury Business
                </h2>
                <p class="text-sm sm:text-base text-[#6E675F] leading-relaxed">
                    Veloura offers a complete, turnkey retail and e-commerce model designed for entrepreneurs who value design excellence, high average order values, and loyal clientele.
                </p>
                <ul class="space-y-3.5 text-sm text-[#242220]">
                    <li class="flex items-start gap-3">
                        <i class="ri-check-line text-[#C47C68] text-lg mt-0.5" aria-hidden="true"></i>
                        <span>Established luxury brand presence with over 75,000 orders fulfilled</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="ri-check-line text-[#C47C68] text-lg mt-0.5" aria-hidden="true"></i>
                        <span>Direct supply chain with fresh stems dispatched daily from auction partners</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="ri-check-line text-[#C47C68] text-lg mt-0.5" aria-hidden="true"></i>
                        <span>Full staff training on floral conditioning, modern European arrangement, and POS operations</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="ri-check-line text-[#C47C68] text-lg mt-0.5" aria-hidden="true"></i>
                        <span>Protected geographical territory for exclusive local and online deliveries</span>
                    </li>
                </ul>
                <div class="pt-2">
                    <a href="#franchise-application" class="vl-btn-primary">
                        <span>Apply for Franchise</span>
                        <i class="ri-arrow-down-line" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Franchise Benefits Grid -->
<section class="py-16 md:py-24 bg-[#FAF7F5] border-b border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="vl-eyebrow">Partnership Advantages</span>
            <h2 class="font-serif text-3xl sm:text-4xl text-[#242220] mt-2 mb-4 font-normal">
                Everything You Need to Succeed
            </h2>
            <p class="text-sm sm:text-base text-[#6E675F]">
                Our comprehensive support model guides you from store architecture to day-to-day profitability.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            <div class="bg-white border border-[#E8DDD5] rounded-2xl p-6 sm:p-8 shadow-sm text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-[#FAF7F5] text-[#C47C68] flex items-center justify-center mb-5 text-xl">
                    <i class="ri-graduation-cap-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-lg text-[#242220] mb-2">Master Florist Training</h3>
                <p class="text-xs sm:text-sm text-[#6E675F] leading-relaxed">
                    Four-week immersive training program covering floral preservation, signature bouquet wrapping, and guest etiquette.
                </p>
            </div>

            <div class="bg-white border border-[#E8DDD5] rounded-2xl p-6 sm:p-8 shadow-sm text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-[#FAF7F5] text-[#3D5447] flex items-center justify-center mb-5 text-xl">
                    <i class="ri-shield-star-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-lg text-[#242220] mb-2">Exclusive Territory</h3>
                <p class="text-xs sm:text-sm text-[#6E675F] leading-relaxed">
                    Clear demarcation of territory ensuring exclusive rights to walk-in boutique sales, corporate contracts, and app deliveries.
                </p>
            </div>

            <div class="bg-white border border-[#E8DDD5] rounded-2xl p-6 sm:p-8 shadow-sm text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-[#FAF7F5] text-[#C47C68] flex items-center justify-center mb-5 text-xl">
                    <i class="ri-truck-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-lg text-[#242220] mb-2">Cold-Chain Supply</h3>
                <p class="text-xs sm:text-sm text-[#6E675F] leading-relaxed">
                    Access to our cold-chain distribution network delivering Dutch imports, Ecuadorian roses, and regional fresh flowers.
                </p>
            </div>

            <div class="bg-white border border-[#E8DDD5] rounded-2xl p-6 sm:p-8 shadow-sm text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-[#FAF7F5] text-[#3D5447] flex items-center justify-center mb-5 text-xl">
                    <i class="ri-line-chart-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-lg text-[#242220] mb-2">Attractive Returns</h3>
                <p class="text-xs sm:text-sm text-[#6E675F] leading-relaxed">
                    High average order values and corporate recurring subscriptions projected to achieve breakeven within 12–18 months.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Application Form -->
<section id="franchise-application" class="py-16 md:py-24 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-[#FAF7F5] border border-[#E8DDD5] rounded-2xl p-6 sm:p-10 shadow-sm">
            <div class="text-center mb-10">
                <span class="vl-eyebrow">Confidential Inquiry</span>
                <h2 class="font-serif text-2xl sm:text-3xl text-[#242220] mt-1 mb-2 font-normal">
                    Franchise Application Form
                </h2>
                <p class="text-sm text-[#6E675F]">
                    Complete the application below and our development director will get in touch.
                </p>
            </div>

            <form id="franchiseForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="vl-field">
                        <label for="firstName" class="text-sm font-medium text-[#242220]">First Name <span class="text-[#C47C68]">*</span></label>
                        <input type="text" id="firstName" name="firstName" required class="vl-input" placeholder="e.g. Rahul">
                    </div>
                    <div class="vl-field">
                        <label for="lastName" class="text-sm font-medium text-[#242220]">Last Name <span class="text-[#C47C68]">*</span></label>
                        <input type="text" id="lastName" name="lastName" required class="vl-input" placeholder="e.g. Mehra">
                    </div>
                    <div class="vl-field">
                        <label for="email" class="text-sm font-medium text-[#242220]">Email Address <span class="text-[#C47C68]">*</span></label>
                        <input type="email" id="email" name="email" required class="vl-input" placeholder="rahul@example.com">
                    </div>
                    <div class="vl-field">
                        <label for="phone" class="text-sm font-medium text-[#242220]">Phone Number <span class="text-[#C47C68]">*</span></label>
                        <input type="tel" id="phone" name="phone" required class="vl-input" placeholder="+91 98100 00000">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="vl-field">
                        <label for="investment" class="text-sm font-medium text-[#242220]">Available Investment Capital <span class="text-[#C47C68]">*</span></label>
                        <select id="investment" name="investment" required class="vl-input">
                            <option value="">Select capital range</option>
                            <option value="15-20L">₹15,00,000 – ₹20,00,000</option>
                            <option value="20-30L">₹20,00,000 – ₹30,00,000</option>
                            <option value="30L+">₹30,00,000+</option>
                        </select>
                    </div>
                    <div class="vl-field">
                        <label for="timeline" class="text-sm font-medium text-[#242220]">Target Timeline to Launch <span class="text-[#C47C68]">*</span></label>
                        <select id="timeline" name="timeline" required class="vl-input">
                            <option value="">Select timeline</option>
                            <option value="immediate">Within 3 Months</option>
                            <option value="3-6months">3 to 6 Months</option>
                            <option value="6months+">6+ Months</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="vl-field">
                        <label for="preferredCity" class="text-sm font-medium text-[#242220]">Preferred City / Location <span class="text-[#C47C68]">*</span></label>
                        <input type="text" id="preferredCity" name="preferredCity" required class="vl-input" placeholder="e.g. Chandigarh, Mumbai, Jaipur">
                    </div>
                    <div class="vl-field">
                        <label for="businessExp" class="text-sm font-medium text-[#242220]">Business / Retail Background</label>
                        <select id="businessExp" name="businessExp" class="vl-input">
                            <option value="">Select experience</option>
                            <option value="retail">Luxury / Fashion Retail</option>
                            <option value="hospitality">Hospitality &amp; F&amp;B</option>
                            <option value="corporate">Corporate Management</option>
                            <option value="first-time">First-time Entrepreneur</option>
                        </select>
                    </div>
                </div>

                <div class="vl-field">
                    <label for="motivation" class="text-sm font-medium text-[#242220]">Why are you interested in a Veloura Atelier franchise? <span class="text-[#C47C68]">*</span></label>
                    <textarea id="motivation" name="motivation" rows="4" required class="vl-input" placeholder="Tell us about your proposed space, commercial background, and vision for your city..."></textarea>
                </div>

                <div class="flex items-start gap-3">
                    <input type="checkbox" id="agreement" name="agreement" required class="mt-1 accent-[#C47C68]">
                    <label for="agreement" class="text-xs text-[#6E675F] leading-normal">
                        I confirm that the details provided are accurate and authorize Veloura's franchise team to contact me regarding expansion opportunities.
                    </label>
                </div>

                <div class="pt-2 text-center">
                    <button type="submit" class="vl-btn-primary py-3.5 px-8 text-sm">
                        <span>Submit Application</span>
                        <i class="ri-check-line" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
include_once 'footer.php';
?>
