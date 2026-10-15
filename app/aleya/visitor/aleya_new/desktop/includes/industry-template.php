<?php
/**
 * Aleya SaaS — Reusable Industry Landing Page Template
 * Implements all 10 required sections with high-contrast Neo-Brutalist architecture
 */

function render_industry_landing_page(array $ind): void {
    require_once __DIR__ . '/seo.php';

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'SoftwareApplication',
                'name' => 'Aleya for ' . $ind['badge'],
                'applicationCategory' => 'BusinessApplication',
                'operatingSystem' => 'Web',
                'description' => $ind['meta_desc'],
                'url' => 'https://aleya.in/industries/' . $ind['slug'] . '/',
                'offers' => [
                    '@type' => 'Offer',
                    'name' => $ind['recommended_plan']['name'],
                    'price' => str_replace(',', '', $ind['recommended_plan']['price_monthly_billed_annually']),
                    'priceCurrency' => 'INR'
                ]
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => 'https://aleya.in/'
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => 'Industries',
                        'item' => 'https://aleya.in/#industries'
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $ind['badge'],
                        'item' => 'https://aleya.in/industries/' . $ind['slug'] . '/'
                    ]
                ]
            ]
        ]
    ];

    if (!empty($ind['faqs'])) {
        $faqEntities = [];
        foreach ($ind['faqs'] as $faq) {
            $faqEntities[] = [
                '@type' => 'Question',
                'name' => $faq['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['a']
                ]
            ];
        }
        $schema['@graph'][] = [
            '@type' => 'FAQPage',
            'mainEntity' => $faqEntities
        ];
    }

    $seoConfig = [
        'title' => $ind['meta_title'],
        'description' => $ind['meta_desc'],
        'canonical' => 'https://aleya.in/industries/' . $ind['canonical_slug'] . '/',
        'og_image' => 'https://aleya.in' . $ind['hero_mockup'],
        'stylesheets' => [
            '/app/aleya/visitor/aleya_new/desktop/assets/css/industry.css'
        ],
        'schema' => $schema
    ];

    $pageScripts = [
        '/app/aleya/visitor/aleya_new/desktop/assets/js/industry.js'
    ];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <?php render_seo_meta($seoConfig); ?>
    </head>
    <body>
    <?php require __DIR__ . '/header.php'; ?>

    <main id="mainContent">
        <!-- =================================================================
             SECTION 1: INDUSTRY SPECIFIC HERO
             ================================================================= -->
        <section class="industry-hero">
            <div class="container industry-hero-grid">
                <div class="industry-hero-content">
                    <span class="industry-hero-tag">
                        <i class="fas fa-check-circle text-brand-teal"></i>
                        <?php echo htmlspecialchars($ind['badge'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <h1 class="industry-hero-title"><?php echo htmlspecialchars($ind['h1'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="industry-hero-desc"><?php echo htmlspecialchars($ind['hero_desc'], ENT_QUOTES, 'UTF-8'); ?></p>

                    <div class="industry-hero-actions">
                        <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-lg" data-track="demo">
                            <i class="far fa-calendar-check"></i> <?php echo htmlspecialchars($ind['hero_cta_primary'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <a href="#pricing" class="btn btn-secondary btn-lg">
                            <?php echo htmlspecialchars($ind['hero_cta_secondary'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </div>

                    <div class="industry-hero-metrics">
                        <?php foreach ($ind['metrics'] as $metric): ?>
                            <div class="hero-metric-item">
                                <span class="hero-metric-value"><?php echo htmlspecialchars($metric['value'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="hero-metric-label"><?php echo htmlspecialchars($metric['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="industry-hero-visual">
                    <div class="hero-mac-frame">
                        <div class="hero-mac-bar">
                            <div class="mac-dot mac-dot-red"></div>
                            <div class="mac-dot mac-dot-yellow"></div>
                            <div class="mac-dot mac-dot-green"></div>
                            <div class="mac-url">
                                <i class="fas fa-lock text-emerald icon-xs"></i>
                                <span>https://app.aleya.in/dashboard/<?php echo htmlspecialchars($ind['slug'], ENT_QUOTES, 'UTF-8'); ?>/</span>
                            </div>
                        </div>
                        <div class="hero-mac-content">
                            <img src="<?php echo htmlspecialchars($ind['hero_mockup'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($ind['badge'], ENT_QUOTES, 'UTF-8'); ?> Dashboard Preview" width="1440" height="900" loading="eager" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =================================================================
             SECTION 2: THE PROBLEM — WHY DO YOU NEED ALEYA?
             ================================================================= -->
        <section class="section section-muted" id="problems">
            <div class="container">
                <div class="section-header center">
                    <span class="section-kicker">Operational Bottlenecks</span>
                    <h2 class="section-title">The Real Cost of Fragmented Workflows</h2>
                    <p class="section-subtitle">
                        Before adopting Aleya, businesses in this segment struggle with manual data entry, missed inquiries, and unorganized coordination that directly impacts revenue.
                    </p>
                </div>

                <div class="problem-cards-grid">
                    <?php foreach ($ind['problems'] as $prob): ?>
                        <div class="problem-card">
                            <div>
                                <span class="problem-badge"><?php echo htmlspecialchars($prob['badge'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <h3><?php echo htmlspecialchars($prob['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p><?php echo htmlspecialchars($prob['desc'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <div class="problem-consequence">
                                <strong>Impact:</strong> <?php echo htmlspecialchars($prob['consequence'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- =================================================================
             SECTION 3: HOW ALEYA HELPS (PROBLEM TO SOLUTION MAPPING)
             ================================================================= -->
        <section class="section">
            <div class="container">
                <div class="section-header center">
                    <span class="section-kicker">Direct Resolution</span>
                    <h2 class="section-title">How Aleya Solves Each Bottleneck</h2>
                    <p class="section-subtitle">Every feature in Aleya maps directly to a verified operational headache, transforming chaotic manual tasks into reliable workflows.</p>
                </div>

                <div class="mapping-grid">
                    <?php foreach ($ind['mappings'] as $map): ?>
                        <div class="mapping-row">
                            <div class="mapping-problem">
                                <i class="fas fa-times-circle"></i>
                                <div>
                                    <h4 class="fs-base text-danger mb-2">The Pain Point</h4>
                                    <p class="mb-0 fs-sm"><?php echo htmlspecialchars($map['problem'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <div class="mapping-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="mapping-solution">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <h4 class="fs-base text-brand mb-2"><?php echo htmlspecialchars($map['capability'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <p class="mb-0 fs-sm"><?php echo htmlspecialchars($map['benefit'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- =================================================================
             SECTION 4: INDUSTRY SPECIFIC FEATURES
             ================================================================= -->
        <section class="section section-dark" id="features">
            <div class="container">
                <div class="section-header center">
                    <span class="section-kicker">Core Modules</span>
                    <h2 class="section-title">Built Specially For Your Daily Operations</h2>
                    <p class="section-subtitle">No generic cards. Only the verified capabilities and modules required by businesses in your vertical.</p>
                </div>

                <div class="industry-features-grid">
                    <?php foreach ($ind['features'] as $feat): ?>
                        <div class="industry-feature-card">
                            <div class="industry-feature-icon">
                                <i class="fas <?php echo htmlspecialchars($feat['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($feat['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p><?php echo htmlspecialchars($feat['desc'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- =================================================================
             SECTION 5: HOW MUCH CAN YOU SAVE WITH ALEYA? (CALCULATOR)
             ================================================================= -->
        <section class="section" id="savingsCalculator" data-plan-cost="<?php echo (int)$ind['calc_defaults']['plan_cost']; ?>">
            <div class="container">
                <div class="section-header center">
                    <span class="section-kicker">Transparent Value Model</span>
                    <h2 class="section-title">Estimate Your Administrative Time & Cost Savings</h2>
                    <p class="section-subtitle">Adjust your team size and weekly manual administration hours to calculate estimated recovered hours and net financial return.</p>
                </div>

                <div class="calculator-card">
                    <div class="calculator-layout">
                        <div class="calculator-inputs">
                            <div>
                                <div class="calc-field-label">
                                    <span>Team Members Using Software</span>
                                    <span id="valTeamSize" class="calc-field-value"><?php echo $ind['calc_defaults']['team_size']; ?> Members</span>
                                </div>
                                <input type="range" id="calcTeamSize" class="calc-slider" min="1" max="30" value="<?php echo $ind['calc_defaults']['team_size']; ?>" />
                            </div>

                            <div>
                                <div class="calc-field-label">
                                    <span>Hours Spent on Repetitive Admin (Per Person / Week)</span>
                                    <span id="valWeeklyHours" class="calc-field-value"><?php echo $ind['calc_defaults']['admin_hours']; ?> hrs / week</span>
                                </div>
                                <input type="range" id="calcWeeklyHours" class="calc-slider" min="2" max="25" value="<?php echo $ind['calc_defaults']['admin_hours']; ?>" />
                            </div>

                            <div>
                                <div class="calc-field-label">
                                    <span>Estimated Hourly Staff Cost (₹)</span>
                                    <span id="valHourlyCost" class="calc-field-value">₹<?php echo $ind['calc_defaults']['hourly_cost']; ?> / hr</span>
                                </div>
                                <input type="range" id="calcHourlyCost" class="calc-slider" min="150" max="1000" step="25" value="<?php echo $ind['calc_defaults']['hourly_cost']; ?>" />
                            </div>

                            <div>
                                <div class="calc-field-label">
                                    <span>Current Monthly Expenses on Multiple Tools (₹)</span>
                                    <span id="valSoftwareCost" class="calc-field-value">₹<?php echo $ind['calc_defaults']['software_cost']; ?> / mo</span>
                                </div>
                                <input type="range" id="calcSoftwareCost" class="calc-slider" min="0" max="15000" step="500" value="<?php echo $ind['calc_defaults']['software_cost']; ?>" />
                            </div>
                        </div>

                        <div class="calculator-results">
                            <div class="calc-result-row">
                                <span>Current Monthly Admin Hours</span>
                                <strong id="outAdminHours">--</strong>
                            </div>
                            <div class="calc-result-row">
                                <span>Estimated Hours Recovered Monthly (60%)</span>
                                <strong id="outHoursSaved" class="text-brand-teal">--</strong>
                            </div>
                            <div class="calc-result-row">
                                <span>Monetary Value of Recovered Time</span>
                                <strong id="outTimeValue">--</strong>
                            </div>
                            <div class="calc-result-row">
                                <span>Aleya Monthly Plan Cost</span>
                                <strong id="outAleyaPlan" class="text-rose">--</strong>
                            </div>

                            <div class="calc-highlight-box">
                                <div class="calc-highlight-label">Estimated Net Monthly Benefit</div>
                                <div id="outNetMonthly" class="calc-highlight-amount">--</div>
                                <div class="fs-xs text-muted mt-2">
                                    Projected Annual Return: <strong id="outAnnualSavings" class="text-mint">--</strong>
                                </div>
                            </div>

                            <p class="calc-disclaimer">
                                Methodology: Monthly Time Value = Hours Saved Per Month × Hourly Staff Cost. Estimated Net Monthly Benefit = Monthly Time Value + Displaced Software Costs − Aleya Monthly Subscription. These are projections based on disclosed assumptions, not guarantees.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =================================================================
             SECTION 6: BEFORE AND AFTER MATRIX
             ================================================================= -->
        <section class="section section-muted">
            <div class="container">
                <div class="section-header center">
                    <span class="section-kicker">Operating Comparison</span>
                    <h2 class="section-title">Fragmented Operations vs Aleya Workflow</h2>
                    <p class="section-subtitle">A direct side-by-side view of how day-to-day operations change after standardizing on Aleya.</p>
                </div>

                <div class="before-after-matrix">
                    <div class="matrix-row matrix-header">
                        <div class="matrix-cell">Operational Dimension</div>
                        <div class="matrix-cell">Traditional Fragmented Way</div>
                        <div class="matrix-cell">Organized Aleya Way</div>
                    </div>
                    <?php foreach ($ind['matrix'] as $row): ?>
                        <div class="matrix-row">
                            <div class="matrix-cell font-bold"><?php echo htmlspecialchars($row['dimension'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="matrix-cell matrix-dim"><i class="fas fa-times mr-2"></i> <?php echo htmlspecialchars($row['before'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="matrix-cell matrix-bright"><i class="fas fa-check mr-2"></i> <?php echo htmlspecialchars($row['after'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- =================================================================
             SECTION 7: INDUSTRY-SPECIFIC WORKFLOW
             ================================================================= -->
        <section class="section">
            <div class="container">
                <div class="section-header center">
                    <span class="section-kicker">Day in the Life</span>
                    <h2 class="section-title">How Your Team Uses Aleya During a Typical Day</h2>
                    <p class="section-subtitle">Step-by-step walk-through of realistic daily interactions powered by supported platform modules.</p>
                </div>

                <div class="workflow-schedule-grid">
                    <?php foreach ($ind['workflow'] as $slot): ?>
                        <div class="workflow-slot-card">
                            <span class="workflow-time-tag"><?php echo htmlspecialchars($slot['time'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <h3><?php echo htmlspecialchars($slot['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p><?php echo htmlspecialchars($slot['desc'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- =================================================================
             SECTION 8: PRICING & SUBSCRIPTION
             ================================================================= -->
        <section class="section section-dark" id="pricing">
            <div class="container">
                <div class="section-header center">
                    <span class="section-kicker">Transparent Pricing</span>
                    <h2 class="section-title">Recommended Plan for <?php echo htmlspecialchars($ind['badge'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p class="section-subtitle">No hidden implementation fees. Standard usage limits and full capability disclosures.</p>
                </div>

                <div class="pricing-card-preview pricing-card-featured max-w-card">
                    <div class="featured-badge">Recommended Edition</div>
                    <div>
                        <div class="card-header">
                            <h3 class="fs-3xl text-dark"><?php echo htmlspecialchars($ind['recommended_plan']['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="text-muted"><?php echo htmlspecialchars($ind['recommended_plan']['users'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>

                        <div class="price-row">
                            <span class="price-currency">₹</span>
                            <span class="price-number"><?php echo htmlspecialchars($ind['recommended_plan']['price_monthly_billed_annually'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="price-period">/month (billed annually)</span>
                            <span class="price-gst-tag">+ 18% GST</span>
                        </div>
                        <p class="fs-sm text-muted mb-4">
                            Or ₹<?php echo htmlspecialchars($ind['recommended_plan']['price_monthly'], ENT_QUOTES, 'UTF-8'); ?>/mo when billed monthly.
                        </p>

                        <ul class="pricing-features-list">
                            <?php foreach ($ind['recommended_plan']['key_points'] as $pt): ?>
                                <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($pt, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="mt-6 d-flex flex-column gap-3">
                        <a href="/checkout/?plan=<?php echo $ind['recommended_plan']['name'] === 'ERP Complete' ? 'erp' : 'crm'; ?>" class="btn btn-primary btn-block btn-lg">
                            Get Started Now <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-block">
                            Schedule a Personalized Demo
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- =================================================================
             SECTION 9: INDUSTRY SPECIFIC FAQS
             ================================================================= -->
        <section class="section" id="faqs">
            <div class="container container-narrow">
                <div class="section-header center">
                    <span class="section-kicker">Buying Answers</span>
                    <h2 class="section-title">Frequently Asked Questions</h2>
                    <p class="section-subtitle">Real questions concerning suitability, setup, team onboarding, and operational limits.</p>
                </div>

                <div class="accordion">
                    <?php foreach ($ind['faqs'] as $idx => $faq): ?>
                        <div class="accordion-item <?php echo $idx === 0 ? 'active' : ''; ?>">
                            <button class="accordion-header" aria-expanded="<?php echo $idx === 0 ? 'true' : 'false'; ?>">
                                <span><?php echo htmlspecialchars($faq['q'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <i class="fas fa-chevron-down accordion-icon"></i>
                            </button>
                            <div class="accordion-content">
                                <p><?php echo htmlspecialchars($faq['a'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- =================================================================
             SECTION 10: FINAL CONVERSION BANNER
             ================================================================= -->
        <section class="section pt-0">
            <div class="container">
                <div class="cta-banner">
                    <h2>Ready to Bring Clarity to Your <?php echo htmlspecialchars($ind['badge'], ENT_QUOTES, 'UTF-8'); ?>?</h2>
                    <p>Join hundreds of business operators who save hours every day with Aleya. Set up your workspace in minutes or book a personalized demonstration with our solution team.</p>
                    <div class="cta-banner-actions">
                        <a href="/checkout/?plan=<?php echo $ind['recommended_plan']['name'] === 'ERP Complete' ? 'erp' : 'crm'; ?>" class="btn btn-secondary btn-lg">
                            Start With <?php echo htmlspecialchars($ind['recommended_plan']['name'], ENT_QUOTES, 'UTF-8'); ?> <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" rel="noopener noreferrer" class="btn btn-dark btn-lg" data-track="demo">
                            <i class="far fa-calendar-check"></i> Book a 1-on-1 Walkthrough
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/footer.php'; ?>
    <?php
}
