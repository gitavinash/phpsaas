<?php

require_once 'header.php';

?>



<!-- Hero Section -->
<section class="brand-hero">
    <div class="container hero-flex">
        <div class="hero-content">
            <h1 class="hero-premium-headline">
                Brand Colours<br>
                <span style="font-size: 2.5rem;">That Build <span style="color: #ba6041;">Recognition,</span></span><br>
                <span style="color: #ba6041; font-size: 2.5rem;">Trust & Experience</span>
            </h1>
            <p class="hero-subtitle">
                Strategic colour systems that communicate your brand values, influence customer perception and create consistent experiences across every touchpoint.
            </p>
            <div class="hero-actions">
                <a href="#" class="btn-filled">BOOK A BRAND COLOUR CONSULTATION</a>
                <a href="#" class="btn-outline">DOWNLOAD SERVICE GUIDE <svg style="width: 14px; height: 14px; vertical-align: middle; margin-left: 5px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg></a>
            </div>
            <div class="hero-features-wrap">
                <div class="hero-features">
                    <div class="h-feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        30 MINUTES<br>DISCOVERY CALL
                    </div>
                    <div class="h-divider"></div>
                    <div class="h-feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        STRATEGIC &<br>RESEARCH DRIVEN
                    </div>
                    <div class="h-divider"></div>
                    <div class="h-feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        TAILORED TO YOUR<br>BRAND ESSENCE
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Matters -->
<section class="matters-section bg-white">
    <div class="container">
        <h2 class="section-title">Why Brand Colour Matters</h2>
        <div class="matters-grid">
            <div class="matter-card">
                <div class="matter-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </div>
                <div class="matter-text">Increase<br>Brand Recognition</div>
            </div>
            <div class="matter-card">
                <div class="matter-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <path d="M9 12l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="matter-text">Build Trust &<br>Emotional Connection</div>
            </div>
            <div class="matter-card">
                <div class="matter-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
                <div class="matter-text">Influence Buying<br>Behaviour</div>
            </div>
            <div class="matter-card">
                <div class="matter-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </div>
                <div class="matter-text">Create Memorable<br>Customer Experience</div>
            </div>
            <div class="matter-card">
                <div class="matter-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <circle cx="12" cy="12" r="6"></circle>
                        <circle cx="12" cy="12" r="2"></circle>
                    </svg>
                </div>
                <div class="matter-text">Maintain Consistency<br>Across All Platforms</div>
            </div>
        </div>
    </div>
</section>

<!-- What We Do -->
<section class="wwd-section bg-light-grey">
    <div class="container whatwedo-flex">
        <div class="whatwedo-content">
            <div class="wwd-subtitle">What We Do</div>
            <h2 class="wwd-title">Brand Colour<br>Strategy Includes</h2>
            <div class="wwd-list">
                <?php
                $features = [
                    "Brand personality analysis",
                    "Interior colour strategy",
                    "Colour psychology research",
                    "Retail & hospitality experience colours",
                    "Primary & secondary colour palette",
                    "Packaging colour direction",
                    "Accent colour recommendations",
                    "Digital & social media colour consistency",
                    "Material & finish colour selection",
                    "Brand colour guideline document"
                ];
                foreach ($features as $f) {
                    echo "<div class='wwd-item'><svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><path d='M22 11.08V12a10 10 0 1 1-5.93-9.14'></path><polyline points='22 4 12 14.01 9 11.01'></polyline></svg> {$f}</div>";
                }
                ?>
            </div>
            <a href="#" class="btn-outline" style="background: transparent;">EXPLORE IN DETAIL</a>
        </div>
    </div>
</section>

<!-- Who is it for -->
<section class="brand-section bg-white">
    <div class="container">
        <h2 class="section-title">Who Is It For?</h2>
        <div class="who-grid">
            <?php
            $who = [
                ["Restaurants<br>& Cafés", '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path>'],
                ["Retail<br>Stores", '<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path>'],
                ["Hotels &<br>Resorts", '<path d="M22 20v-5c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v5"></path><path d="M2 10v4"></path><path d="M22 10v4"></path><path d="M8 10h8"></path><path d="M12 10v4"></path><path d="M12 6v4"></path>'],
                ["Wellness &<br>Beauty Brands", '<path d="M12 22c5.52 0 10-4.48 10-10S17.52 2 12 2 2 7.52 2 13s4.48 10 10 10z"></path><path d="M12 8v8"></path><path d="M8 12h8"></path>'],
                ["Healthcare<br>Clinics", '<path d="M3 21h18"></path><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"></path><path d="M10 9h4"></path><path d="M12 7v4"></path>'],
                ["Corporate<br>Offices", '<rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path>'],
                ["Luxury<br>Residential", '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>'],
                ["Product &<br>Lifestyle", '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line>']
            ];
            foreach ($who as $w) {
                echo "<div class='who-card'><div class='who-icon'><svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='1.5'>{$w[1]}</svg></div><div class='who-text'>{$w[0]}</div></div>";
            }
            ?>
        </div>
    </div>
</section>

<!-- Our Process -->
<section class="brand-section bg-light-grey" style="background-color: #f7f5f2;">
    <div class="container">
        <h2 class="section-title">Our Process</h2>
        <div class="process-grid">
            <?php
            $process = [
                ["1. DISCOVER", "We understand your brand, audience, vision & goals.", '<circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>'],
                ["2. RESEARCH", "In-depth study of your industry, competitors & colour psychology.", '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>'],
                ["3. STRATEGY", "We craft a strategic colour direction aligned with your brand essence.", '<circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle>'],
                ["4. PALETTE DEVELOPMENT", "Curated palette options with materials & finish recommendations.", '<circle cx="13.5" cy="5.5" r="2.5"></circle><circle cx="20.5" cy="10.5" r="2.5"></circle><circle cx="18.5" cy="17.5" r="2.5"></circle><circle cx="11.5" cy="20.5" r="2.5"></circle><circle cx="5.5" cy="15.5" r="2.5"></circle><circle cx="6.5" cy="8.5" r="2.5"></circle>'],
                ["5. APPLICATION GUIDELINES", "How to apply the colours consistently across all touchpoints.", '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>'],
                ["6. FINAL SYSTEM", "A complete brand colour system & guideline document.", '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>']
            ];
            foreach ($process as $p) {
                echo "<div class='process-step'>
                    <div class='process-icon'><svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='1.5'>{$p[2]}</svg></div>
                    <div class='process-title'>{$p[0]}</div>
                    <div class='process-desc'>{$p[1]}</div>
                </div>";
            }
            ?>
        </div>
    </div>
</section>

<!-- Featured Projects -->
<section class="brand-section bg-white">
    <div class="container">
        <div class="fp-flex">
            <div class="fp-left">
                <div class="fp-header">
                    <h2 class="fp-title" style="margin: 0;">Featured Projects</h2>
                    <a href="#" class="fp-link">VIEW ALL PROJECTS &rarr;</a>
                </div>
                <div class="project-grid">
                    <div class="project-card">
                        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80" alt="Singh Sahib" style="height: 200px; object-fit: cover;">
                        <div class="project-title">SINGH SAHIB</div>
                        <div class="project-cat">Restaurant</div>
                        <div class="project-colors">
                            <div class="p-color" style="background: #6a7457;"></div>
                            <div class="p-color" style="background: #a96a4f;"></div>
                            <div class="p-color" style="background: #3a3a3a;"></div>
                        </div>
                    </div>
                    <div class="project-card">
                        <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=800&q=80" alt="Dry Fitt Gym" style="height: 200px; object-fit: cover;">
                        <div class="project-title">DRY FITT GYM</div>
                        <div class="project-cat">Fitness Studio</div>
                        <div class="project-colors">
                            <div class="p-color" style="background: #2a2a2a;"></div>
                            <div class="p-color" style="background: #6a7457;"></div>
                            <div class="p-color" style="background: #b29983;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fp-right">
                <h2 class="fp-title-test">CLIENTS LOVE OUR COLOUR STRATEGY</h2>
                <div class="test-icon">&ldquo;</div>
                <div class="test-text">Hue Culture understood our brand soul beautifully and created a colour system that completely transformed our space and brand experience.</div>
                <div class="test-author">&mdash; RAGHAV SACHDEV</div>
                <div class="test-role">Founder, Raghav Sachdev Design</div>
                <div class="test-dots">
                    <div class="test-dot active"></div>
                    <div class="test-dot"></div>
                    <div class="test-dot"></div>
                    <div class="test-dot"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Green Banner -->
<section class="green-banner">
    <div class="container gb-flex">
        <div class="gb-content">
            <h2 class="gb-title">Your brand deserves more than a beautiful logo&mdash;<br>it deserves a colour strategy people remember.</h2>
            <p class="gb-desc">Whether you're launching a new brand or refreshing an existing one, Hue Culture helps create colour systems that strengthen identity, shape perception and leave a lasting impression.</p>
            <div class="gb-actions">
                <a href="#" class="btn-filled">BOOK A CONSULTATION</a>
                <a href="#" class="btn-gb-outline">REQUEST A PROPOSAL</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form -->
<section class="contact-section">
    <div class="container contact-flex">
        <div class="contact-left">
            <h2 class="contact-title">Let's Create Your<br>Brand Identity In Colour</h2>
            <p class="contact-desc">Fill in your details and we'll get back to you for a discovery call.</p>
        </div>
        <div class="contact-right">
            <form class="c-form">
                <input type="text" class="c-input" placeholder="Full Name">
                <input type="text" class="c-input" placeholder="Company / Brand Name">
                <select class="c-input">
                    <option>Industry</option>
                </select>
                <select class="c-input">
                    <option>Project Type</option>
                </select>
                <input type="text" class="c-input" placeholder="Phone Number">
                <input type="email" class="c-input" placeholder="Email Address">
                <textarea class="c-input c-full" placeholder="Tell us about your project" rows="4"></textarea>
                <button type="button" class="c-submit">SEND ENQUIRY</button>
            </form>
        </div>
    </div>
</section>

<?php
require_once 'footer.php';
?>