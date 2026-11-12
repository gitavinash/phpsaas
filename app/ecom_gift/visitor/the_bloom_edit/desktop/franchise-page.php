<?php

include_once 'header.php';

?>

<!-- Editorial Banner -->
<section class="tbe-banner">
    <img src="<?php echo safe_html(USER_IMAGES . 'about-us-banner.jpg'); ?>"
        alt="The &amp;Blooms Luxury Floral Boutique Franchise"
        fetchpriority="high" decoding="async" class="tbe-banner-media">
    <div class="tbe-banner-overlay"></div>
    <div class="tbe-banner-content">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <nav class="text-xs uppercase tracking-[0.16em] text-[#EFE8DC]/85 mb-3" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="/" class="hover:text-white transition-colors">Home</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-white" aria-current="page">Franchise Partnership</li>
                </ol>
            </nav>
            <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl text-white">Own an &amp;Blooms Atelier</h1>
            <p class="text-[#EFE8DC] text-sm sm:text-base md:text-lg max-w-2xl mt-2 sm:mt-3">
                Bring our celebrated floral artistry and luxury gifting experience to your city through a structured boutique partnership.
            </p>
        </div>
    </div>
</section>

<!-- Franchise Introduction -->
<section class="bg-[#FAF7F2] py-10 sm:py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-10 lg:gap-14 items-center">
            <div class="lg:col-span-6">
                <div class="rounded-2xl overflow-hidden border border-[#E6DDD3] shadow-sm aspect-[4/3] bg-white">
                    <img src="<?php echo safe_html(USER_IMAGES . 'andblooms-store.jpg'); ?>"
                        alt="Flagship &amp;Blooms floral boutique retail showcase"
                        loading="lazy" decoding="async"
                        class="w-full h-full object-cover object-center">
                </div>
            </div>
            <div class="lg:col-span-6 space-y-5 sm:space-y-6">
                <span class="tbe-eyebrow">Partnership Model</span>
                <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#1C1A18] leading-tight">
                    Why Partner with &amp;Blooms?
                </h2>
                <p class="text-[#5E5650] leading-relaxed">
                    Join a distinguished luxury floral brand with four thriving Delhi NCR boutiques and direct farm-to-atelier supply chains. We equip every partner with store design standards, florist training, and unified technology.
                </p>

                <ul class="space-y-3.5 text-sm text-[#1C1A18]">
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full bg-[#EFE8DC] text-[#183028] flex-shrink-0 mt-0.5">
                            <i class="ri-check-line text-sm" aria-hidden="true"></i>
                        </span>
                        <span>Established luxury floral brand established in 2018 across New Delhi &amp; Gurugram</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full bg-[#EFE8DC] text-[#183028] flex-shrink-0 mt-0.5">
                            <i class="ri-check-line text-sm" aria-hidden="true"></i>
                        </span>
                        <span>Comprehensive 4-week botanical artistry &amp; retail operations training</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full bg-[#EFE8DC] text-[#183028] flex-shrink-0 mt-0.5">
                            <i class="ri-check-line text-sm" aria-hidden="true"></i>
                        </span>
                        <span>Protected catchment territory and centralized luxury packaging supply</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full bg-[#EFE8DC] text-[#183028] flex-shrink-0 mt-0.5">
                            <i class="ri-check-line text-sm" aria-hidden="true"></i>
                        </span>
                        <span>Central e-commerce order routing and seasonal marketing campaigns</span>
                    </li>
                </ul>

                <div class="pt-2">
                    <a href="#franchise-application" class="tbe-btn-forest w-full sm:w-auto justify-center px-8 py-3.5">
                        Begin Your Application
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Grid -->
<section class="bg-[#EFE8DC]/55 border-y border-[#E6DDD3] py-10 sm:py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <span class="tbe-eyebrow">Partner Advantage</span>
            <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#1C1A18] mt-1 mb-3">Franchise Support Pillars</h2>
            <p class="text-sm sm:text-base text-[#5E5650]">Structured end-to-end launch and operational backing from day one.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <article class="bg-white border border-[#E6DDD3] rounded-xl p-5 sm:p-7 text-center shadow-sm">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center bg-[#EFE8DC] text-[#183028] rounded-full mx-auto mb-4 sm:mb-5">
                    <i class="ri-graduation-cap-line text-2xl" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#1C1A18] mb-2">Training &amp; Craft</h3>
                <p class="text-sm text-[#5E5650] leading-relaxed">
                    4-week immersive curriculum covering conditioning, signature bouquets, and boutique hospitality.
                </p>
            </article>

            <article class="bg-white border border-[#E6DDD3] rounded-xl p-5 sm:p-7 text-center shadow-sm">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center bg-[#F4E9E4] text-[#9B5637] rounded-full mx-auto mb-4 sm:mb-5">
                    <i class="ri-shield-check-line text-2xl" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#1C1A18] mb-2">Protected Territory</h3>
                <p class="text-sm text-[#5E5650] leading-relaxed">
                    Dedicated catchment exclusivity backed by established regional brand recall.
                </p>
            </article>

            <article class="bg-white border border-[#E6DDD3] rounded-xl p-5 sm:p-7 text-center shadow-sm">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center bg-[#EFE8DC] text-[#183028] rounded-full mx-auto mb-4 sm:mb-5">
                    <i class="ri-truck-line text-2xl" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#1C1A18] mb-2">Direct Cold Chain</h3>
                <p class="text-sm text-[#5E5650] leading-relaxed">
                    Direct grower relationships, preferred seasonal stem pricing, and bespoke packaging access.
                </p>
            </article>

            <article class="bg-white border border-[#E6DDD3] rounded-xl p-5 sm:p-7 text-center shadow-sm">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center bg-[#F4E9E4] text-[#9B5637] rounded-full mx-auto mb-4 sm:mb-5">
                    <i class="ri-line-chart-line text-2xl" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#1C1A18] mb-2">Strong Unit Economics</h3>
                <p class="text-sm text-[#5E5650] leading-relaxed">
                    Proven multi-channel revenue combining walk-in retail, corporate retainers, and digital orders.
                </p>
            </article>
        </div>
    </div>
</section>

<!-- Application Form -->
<section id="franchise-application" class="bg-[#FAF7F2] py-10 sm:py-16 md:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-10">
            <span class="tbe-eyebrow">Express Interest</span>
            <h2 id="franchiseFormHeading" class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#1C1A18] mt-1 mb-3">Franchise Application</h2>
            <p class="text-sm sm:text-base text-[#5E5650]">Submit your profile below and our partnerships director will schedule an introductory call.</p>
        </div>

        <div class="bg-white border border-[#E6DDD3] rounded-2xl p-5 sm:p-10 shadow-sm">
            <form id="franchiseForm" class="space-y-8" novalidate aria-labelledby="franchiseFormHeading">
                <fieldset class="space-y-5">
                    <legend class="font-serif text-xl text-[#1C1A18] pb-2 border-b border-[#EFE8DC] w-full">Personal Information</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="tbe-field">
                            <label for="firstName" class="text-sm font-medium text-[#1C1A18]">
                                First Name <span class="text-[#991B1B]" aria-hidden="true">*</span>
                            </label>
                            <input type="text" id="firstName" name="firstName" autocomplete="given-name"
                                enterkeyhint="next" class="tbe-input" data-error="fr-firstName-error" required>
                            <div id="fr-firstName-error" class="tbe-field-error">Error: Please enter your first name.</div>
                        </div>

                        <div class="tbe-field">
                            <label for="lastName" class="text-sm font-medium text-[#1C1A18]">
                                Last Name <span class="text-[#991B1B]" aria-hidden="true">*</span>
                            </label>
                            <input type="text" id="lastName" name="lastName" autocomplete="family-name"
                                enterkeyhint="next" class="tbe-input" data-error="fr-lastName-error" required>
                            <div id="fr-lastName-error" class="tbe-field-error">Error: Please enter your last name.</div>
                        </div>

                        <div class="tbe-field">
                            <label for="email" class="text-sm font-medium text-[#1C1A18]">
                                Email Address <span class="text-[#991B1B]" aria-hidden="true">*</span>
                            </label>
                            <input type="email" id="email" name="email" autocomplete="email" autocorrect="off"
                                autocapitalize="none" spellcheck="false" enterkeyhint="next"
                                class="tbe-input" data-error="fr-email-error" required>
                            <div id="fr-email-error" class="tbe-field-error">Error: Enter a valid email address in the format you@example.com.</div>
                        </div>

                        <div class="tbe-field">
                            <label for="phone" class="text-sm font-medium text-[#1C1A18]">
                                Phone Number <span class="text-[#991B1B]" aria-hidden="true">*</span>
                            </label>
                            <span id="fr-phone-hint" class="tbe-field-hint">7 to 15 digits including country code if applicable.</span>
                            <input type="tel" id="phone" name="phone" inputmode="tel" autocomplete="tel"
                                pattern="[\d\s+\-()]{7,15}" enterkeyhint="next"
                                class="tbe-input" aria-describedby="fr-phone-hint" data-error="fr-phone-error" required>
                            <div id="fr-phone-error" class="tbe-field-error">Error: Please enter a valid numeric mobile number.</div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="space-y-5">
                    <legend class="font-serif text-xl text-[#1C1A18] pb-2 border-b border-[#EFE8DC] w-full">Business Background</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="tbe-field">
                            <label for="businessExp" class="text-sm font-medium text-[#1C1A18]">Years of Business Experience</label>
                            <select id="businessExp" name="businessExp" class="tbe-input">
                                <option value="">Select experience level</option>
                                <option value="0-2">0–2 years</option>
                                <option value="3-5">3–5 years</option>
                                <option value="6-10">6–10 years</option>
                                <option value="10+">10+ years</option>
                            </select>
                        </div>

                        <div class="tbe-field">
                            <label for="industry" class="text-sm font-medium text-[#1C1A18]">Previous Industry Experience</label>
                            <select id="industry" name="industry" class="tbe-input">
                                <option value="">Select industry</option>
                                <option value="retail">Luxury &amp; Boutique Retail</option>
                                <option value="hospitality">Hospitality &amp; Events</option>
                                <option value="food-service">Food &amp; Patisserie</option>
                                <option value="floral">Floral Industry</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="space-y-5">
                    <legend class="font-serif text-xl text-[#1C1A18] pb-2 border-b border-[#EFE8DC] w-full">Financial Capacity &amp; Timeline</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="tbe-field">
                            <label for="investment" class="text-sm font-medium text-[#1C1A18]">
                                Available Investment Capital <span class="text-[#991B1B]" aria-hidden="true">*</span>
                            </label>
                            <select id="investment" name="investment" class="tbe-input" data-error="fr-investment-error" required>
                                <option value="">Select investment range</option>
                                <option value="150k-200k">₹15,00,000 – ₹20,00,000</option>
                                <option value="200k-250k">₹20,00,000 – ₹25,00,000</option>
                                <option value="250k+">₹25,00,000+</option>
                            </select>
                            <div id="fr-investment-error" class="tbe-field-error">Error: Please select an investment range.</div>
                        </div>

                        <div class="tbe-field">
                            <label for="timeline" class="text-sm font-medium text-[#1C1A18]">
                                Preferred Timeline to Open <span class="text-[#991B1B]" aria-hidden="true">*</span>
                            </label>
                            <select id="timeline" name="timeline" class="tbe-input" data-error="fr-timeline-error" required>
                                <option value="">Select timeline</option>
                                <option value="3-months">Within 3 months</option>
                                <option value="6-months">3–6 months</option>
                                <option value="12-months">6–12 months</option>
                                <option value="12-months+">12+ months</option>
                            </select>
                            <div id="fr-timeline-error" class="tbe-field-error">Error: Please select a launch timeline.</div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="space-y-5">
                    <legend class="font-serif text-xl text-[#1C1A18] pb-2 border-b border-[#EFE8DC] w-full">Location Preferences</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="tbe-field">
                            <label for="preferredCity" class="text-sm font-medium text-[#1C1A18]">
                                Preferred City / Neighbourhood <span class="text-[#991B1B]" aria-hidden="true">*</span>
                            </label>
                            <input type="text" id="preferredCity" name="preferredCity" autocomplete="address-level2"
                                enterkeyhint="next" class="tbe-input" data-error="fr-preferredCity-error" required>
                            <div id="fr-preferredCity-error" class="tbe-field-error">Error: Please specify your preferred city or area.</div>
                        </div>

                        <div class="tbe-field">
                            <label for="alternateCity" class="text-sm font-medium text-[#1C1A18]">Alternate Location</label>
                            <input type="text" id="alternateCity" name="alternateCity" enterkeyhint="next" class="tbe-input">
                        </div>
                    </div>
                </fieldset>

                <div class="tbe-field">
                    <label for="motivation" class="text-sm font-medium text-[#1C1A18]">
                        Why would you like to open an &amp;Blooms franchise? <span class="text-[#991B1B]" aria-hidden="true">*</span>
                    </label>
                    <textarea id="motivation" name="motivation" rows="4" class="tbe-input"
                        data-error="fr-motivation-error" required></textarea>
                    <div id="fr-motivation-error" class="tbe-field-error">Error: Please tell us about your vision and interest.</div>
                </div>

                <div class="tbe-field">
                    <label for="agreement" class="custom-checkbox gap-3 items-start">
                        <input type="checkbox" name="agreement" id="agreement" data-error="fr-agreement-error" required>
                        <span class="checkmark flex-shrink-0 mt-0.5"></span>
                        <span class="text-sm text-[#5E5650] leading-relaxed">
                            I confirm the information provided is accurate and authorize &amp;Blooms to contact me regarding boutique franchise opportunities.
                        </span>
                    </label>
                    <div id="fr-agreement-error" class="tbe-field-error">Error: Please accept the authorization checkbox before submitting.</div>
                </div>

                <p id="franchiseFormStatus" class="text-sm font-medium min-h-[1.25rem]" aria-live="polite"></p>

                <button type="submit" class="tbe-btn-forest w-full sm:w-auto px-8 py-3.5 text-sm">
                    Submit Partnership Application
                </button>
            </form>
        </div>
    </div>
</section>

<?php

include_once 'footer.php';

?>
