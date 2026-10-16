<?php
/**
 * Aleya SaaS — Complete Redesigned Homepage
 * High-Contrast Neo-Brutalist B2B Conversion Architecture
 */

require_once __DIR__ . '/includes/seo.php';

$seoConfig = [
    'title' => 'Aleya — Business Management, CRM, ERP & Operational Workflows Platform',
    'description' => 'Run your business with more clarity. Aleya centralizes inquiries, automated WhatsApp follow-ups, quotations, itineraries, and backend operations for service industries.',
    'canonical' => 'https://aleya.in/',
    'keywords' => 'business CRM, travel ERP, transporter management, DMS distribution, spa management software, WhatsApp CRM, lead management',
    'stylesheets' => [
        '/app/aleya/visitor/aleya_new/desktop/assets/css/homepage.css',
        '/app/aleya/visitor/aleya_new/desktop/assets/css/industry.css'
    ],
    'schema' => [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                'name' => 'Aleya',
                'url' => 'https://aleya.in',
                'logo' => 'https://aleya.in/app/aleya/visitor/aleya_new/desktop/assets/images/aleya_logo.png',
                'parentOrganization' => [
                    '@type' => 'Organization',
                    'name' => 'Blumox Technologies',
                    'url' => 'https://blumox.com'
                ],
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'telephone' => '+91-9990026008',
                    'contactType' => 'customer support',
                    'email' => 'support@aleya.in',
                    'areaServed' => 'IN'
                ]
            ],
            [
                '@type' => 'SoftwareApplication',
                'name' => 'Aleya Business Platform',
                'applicationCategory' => 'BusinessApplication',
                'operatingSystem' => 'Web',
                'description' => 'SaaS business management, CRM & operations platform for travel, transport, distribution, and service enterprises.',
                'url' => 'https://aleya.in',
                'offers' => [
                    [
                        '@type' => 'Offer',
                        'name' => 'CRM Starter',
                        'price' => '1499',
                        'priceCurrency' => 'INR'
                    ],
                    [
                        '@type' => 'Offer',
                        'name' => 'ERP Complete',
                        'price' => '3999',
                        'priceCurrency' => 'INR'
                    ]
                ]
            ]
        ]
    ]
];

$pageScripts = [
    '/app/aleya/visitor/aleya_new/desktop/assets/js/industry.js',
    '/app/aleya/visitor/aleya_new/desktop/assets/js/pricing.js'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php render_seo_meta($seoConfig); ?>
</head>
<body>
<?php require __DIR__ . '/includes/header.php'; ?>

<main id="mainContent">
    <!-- =================================================================
         SECTION 2: HERO
         ================================================================= -->
    <section class="hero-section" id="hero">
        <div class="hero-grid-bg"></div>
        <div class="container hero-content">
            <div class="hero-pill">
                <span class="hero-pill-dot"></span>
                <span>The Unified SaaS Operations Platform</span>
            </div>

            <h1 class="hero-title">
                Run Your Business With More Clarity.<br />
                <span class="hero-title-highlight">Grow With Less Complexity.</span>
            </h1>

            <p class="hero-subtitle">
                Aleya centralizes multi-channel inquiries, customer follow-ups, quotations, booking vouchers, and backend operations into one reliable system. Eliminate dropped leads and scattered spreadsheets.
            </p>

            <div class="hero-actions">
                <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-lg" data-track="demo">
                    <i class="far fa-calendar-check"></i> Book a Personalized Demo
                </a>
                <a href="#industries" class="btn btn-secondary btn-lg">
                    Explore Industry Solutions <i class="fas fa-arrow-down"></i>
                </a>
                <a href="/pricing/" class="btn btn-outline btn-lg">
                    View Pricing
                </a>
            </div>

            <div class="hero-trust-row">
                <div class="trust-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Zero Data Leakage Guarantee</span>
                </div>
                <div class="trust-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Instant WhatsApp Gateway Included</span>
                </div>
                <div class="trust-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Setup in 1 Business Day</span>
                </div>
            </div>

            <!-- Dashboard Preview Frame -->
            <div class="hero-visual-wrapper">
                <div class="hero-mac-frame">
                    <div class="hero-mac-bar">
                        <div class="mac-dot mac-dot-red"></div>
                        <div class="mac-dot mac-dot-yellow"></div>
                        <div class="mac-dot mac-dot-green"></div>
                        <div class="mac-url">
                            <i class="fas fa-lock text-emerald icon-xs"></i>
                            <span>https://app.aleya.in/dashboard/overview/</span>
                        </div>
                    </div>
                    <div class="hero-mac-content">
                        <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/actual_dashboard_overview.png" alt="Aleya Executive SaaS CRM & Operations Dashboard" width="1440" height="900" loading="eager" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================
         SECTION 10 (PART A): TRUSTED BY INDUSTRY CLIENTS
         ================================================================= -->
    <section class="client-proof-section">
        <div class="container">
            <div class="client-proof-label">Trusted By Fast-Growing Service & Operations Leaders Across India</div>
            <div class="client-logos-grid">
                <div class="client-logo-item">
                    <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/featured_clients/musafir_india_trip.png" alt="Musafir India Trip" />
                </div>
                <div class="client-logo-item">
                    <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/featured_clients/la_skinnovita.png" alt="La Skinnovita Clinic & Spa" />
                </div>
                <div class="client-logo-item">
                    <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/featured_clients/sanjay_jai_marble.png" alt="Sanjay Jai Marble" />
                </div>
                <div class="client-logo-item">
                    <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/featured_clients/stoneval.png" alt="Stoneval Surfaces" />
                </div>
                <div class="client-logo-item">
                    <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/featured_clients/phyworld.png" alt="Phyworld Healthcare" />
                </div>
                <div class="client-logo-item">
                    <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/featured_clients/skin_qure.png" alt="Skin Qure Clinic" />
                </div>
                <div class="client-logo-item">
                    <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/featured_clients/and_blooms.png" alt="And Blooms Wellness" />
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================
         SECTION 3: INDUSTRY SELECTOR GRID
         ================================================================= -->
    <section class="section" id="industries">
        <div class="container">
            <div class="section-header center">
                <span class="section-kicker">Tailored Solutions</span>
                <h2 class="section-title">Engineered For Your Specific Industry Workflows</h2>
                <p class="section-subtitle">
                    Select your business segment below to discover how Aleya eliminates specific operational bottlenecks and replaces manual spreadsheets.
                </p>
            </div>

            <div class="industry-grid">
                <!-- Travel Agencies -->
                <a href="/industries/travel-agencies/" class="industry-card">
                    <div>
                        <div class="industry-card-icon"><i class="fas fa-plane-departure"></i></div>
                        <div class="industry-card-tag">Tour Operators & DMCs</div>
                        <h3 class="industry-card-title">Travel Agencies</h3>
                        <p class="industry-card-desc">
                            Automate Meta & Google ad lead capture, build day-wise custom itineraries in under 3 minutes, issue supplier vouchers, and track DMC payables.
                        </p>
                    </div>
                    <div class="industry-card-footer">
                        <span>Explore Travel Agency Platform</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <!-- Travel Agents -->
                <a href="/industries/travel-agents/" class="industry-card">
                    <div>
                        <div class="industry-card-icon"><i class="fas fa-user-tie"></i></div>
                        <div class="industry-card-tag">Independent Consultants</div>
                        <h3 class="industry-card-title">Travel Agents</h3>
                        <p class="industry-card-desc">
                            A fast, lightweight, and affordable travel CRM for solo agents and small teams. Keep client inquiries organized, share fast quotes, and never drop a lead.
                        </p>
                    </div>
                    <div class="industry-card-footer">
                        <span>Explore Travel Agent CRM</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <!-- Transporters -->
                <a href="/industries/transporters/" class="industry-card">
                    <div>
                        <div class="industry-card-icon"><i class="fas fa-truck-moving"></i></div>
                        <div class="industry-card-tag">Fleet Operators & Cabs</div>
                        <h3 class="industry-card-title">Transporters</h3>
                        <p class="industry-card-desc">
                            Manage vehicle rosters, driver allocations, and trip costing. Send verified vehicle photo quotes via WhatsApp and issue digital duty slips in seconds.
                        </p>
                    </div>
                    <div class="industry-card-footer">
                        <span>Explore Transporter Logistics</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <!-- DMS -->
                <a href="/industries/dms/" class="industry-card">
                    <div>
                        <div class="industry-card-icon"><i class="fas fa-warehouse"></i></div>
                        <div class="industry-card-tag">Wholesale & Manufacturers</div>
                        <h3 class="industry-card-title">DMS (Distribution)</h3>
                        <p class="industry-card-desc">
                            Centralize dealer reorders, monitor multi-warehouse inventory levels, approve purchase orders, track returns, and automate ledger collection statements.
                        </p>
                    </div>
                    <div class="industry-card-footer">
                        <span>Explore DMS Distribution</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <!-- Spa Management -->
                <a href="/industries/spa-management/" class="industry-card">
                    <div>
                        <div class="industry-card-icon"><i class="fas fa-spa"></i></div>
                        <div class="industry-card-tag">Salons & Wellness Centers</div>
                        <h3 class="industry-card-title">SPA Management</h3>
                        <p class="industry-card-desc">
                            Coordinate appointment bookings, therapist schedules, and therapy rooms. Cut no-shows by 85% with automated WhatsApp reminders and track prepaid memberships.
                        </p>
                    </div>
                    <div class="industry-card-footer">
                        <span>Explore Spa Management</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <!-- All Other Industries -->
                <a href="/industries/other-industries/" class="industry-card industry-card-other" aria-label="Commercial Sales CRM for All Other Industries">
                    <div>
                        <div class="industry-card-icon"><i class="fas fa-briefcase"></i></div>
                        <div class="industry-card-tag">All Other Industries</div>
                        <h3 class="industry-card-title">Commercial Sales CRM</h3>
                        <p class="industry-card-desc">
                            Pure sales and finance velocity: Lead capture, product catalog, proforma quotations, payment requests, and WhatsApp Web sync with zero ops bloat.
                        </p>
                    </div>
                    <div class="industry-card-footer">
                        <span class="industry-card-action">
                            Explore Commercial CRM <i class="fas fa-arrow-right"></i>
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- =================================================================
         SECTION 4: BUSINESS CHALLENGES (BEFORE VS AFTER)
         ================================================================= -->
    <section class="section section-muted" id="challenges">
        <div class="container">
            <div class="section-header center">
                <span class="section-kicker">The Cost of Friction</span>
                <h2 class="section-title">Why Growing Businesses Leave Spreadsheets Behind</h2>
                <p class="section-subtitle">
                    Operating on scattered tools and manual messaging drains valuable team hours and directly hurts your bottom line.
                </p>
            </div>

            <div class="challenges-grid">
                <!-- Fragmented Workflow -->
                <div class="challenge-column challenge-column-before">
                    <span class="challenge-badge-negative">The Fragmented Old Way</span>
                    <h3 class="pain-card-header">Scattered Apps & Silent Revenue Leaks</h3>
                    <p class="pain-card-sub">When operations rely on personal phones, spreadsheets, and manual memory:</p>

                    <div class="challenge-list">
                        <div class="challenge-list-item">
                            <i class="fas fa-times-circle"></i>
                            <span>Meta and Google ad leads wait hours on spreadsheets before receiving a callback.</span>
                        </div>
                        <div class="challenge-list-item">
                            <i class="fas fa-times-circle"></i>
                            <span>Staff spend 2-3 hours manually typing Word quotations or WhatsApp messages.</span>
                        </div>
                        <div class="challenge-list-item">
                            <i class="fas fa-times-circle"></i>
                            <span>Bookings, supplier vouchers, or duty slips are verified over frantic phone calls.</span>
                        </div>
                        <div class="challenge-list-item">
                            <i class="fas fa-times-circle"></i>
                            <span>Managers have zero visibility into daily team activities or pipeline conversion rates.</span>
                        </div>
                        <div class="challenge-list-item">
                            <i class="fas fa-times-circle"></i>
                            <span>Customer balances and advance deposits go uncollected due to missing reminders.</span>
                        </div>
                    </div>
                </div>

                <!-- Aleya Workflow -->
                <div class="challenge-column challenge-column-after">
                    <span class="challenge-badge-positive">The Aleya Operations Engine</span>
                    <h3 class="gain-card-header">Centralized Control & 0% Lead Leakage</h3>
                    <p class="gain-card-sub">When your entire team operates inside Aleya's unified architecture:</p>

                    <div class="challenge-list">
                        <div class="challenge-list-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Leads sync in 0 seconds with instant WhatsApp context and sales rep auto-assignment.</span>
                        </div>
                        <div class="challenge-list-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Branded day-wise itineraries, vehicle photo galleries, or quotes dispatched in 3 mins.</span>
                        </div>
                        <div class="challenge-list-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Vouchers, digital duty slips, and room allocations generate automatically upon payment.</span>
                        </div>
                        <div class="challenge-list-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Live executive dashboards show response speeds, conversion stages, and gross profit.</span>
                        </div>
                        <div class="challenge-list-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Automated UPI collection links and balance recovery reminders sent to client WhatsApp.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================
         SECTION 5: HOW ALEYA HELPS (CORE FEATURE PILLARS)
         ================================================================= -->
    <section class="section" id="features">
        <div class="container">
            <div class="section-header center">
                <span class="section-kicker">Core Capabilities</span>
                <h2 class="section-title">Designed For Practical B2B Excellence</h2>
                <p class="section-subtitle">
                    Six foundational modules engineered to standardize your operational rhythm and accelerate business conversion.
                </p>
            </div>

            <div class="capability-grid">
                <div class="capability-card">
                    <div class="capability-icon"><i class="fas fa-satellite-dish"></i></div>
                    <h3>Multi-Channel Lead Ingestion</h3>
                    <p>
                        Instant, zero-latency sync from Facebook Lead Ads, Instagram DMs, Google Ads, website forms, and walk-ins into a unified pipeline.
                    </p>
                </div>

                <div class="capability-card">
                    <div class="capability-icon"><i class="fas fa-funnel-dollar"></i></div>
                    <h3>Smart Pipeline Qualification</h3>
                    <p>
                        Organize prospects by qualification stage, destination interest, budget range, and follow-up urgency. Prevent leads from going cold.
                    </p>
                </div>

                <div class="capability-card">
                    <div class="capability-icon"><i class="fab fa-whatsapp"></i></div>
                    <h3>Native WhatsApp CRM</h3>
                    <p>
                        Send quotations, booking confirmations, itinerary PDFs, and balance payment reminders directly to client WhatsApp without leaving your dashboard.
                    </p>
                </div>

                <div class="capability-card">
                    <div class="capability-icon"><i class="fas fa-cogs"></i></div>
                    <h3>Industry Tailored Operations</h3>
                    <p>
                        Specific workflows for travel itineraries, vehicle photo dispatches, multi-warehouse dealer orders, and spa room/therapist scheduling.
                    </p>
                </div>

                <div class="capability-card">
                    <div class="capability-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    <h3>GST Invoicing & Fast UPI Links</h3>
                    <p>
                        Generate GST-compliant invoices with HSN/SAC codes, capture advance deposits via dynamic UPI QR codes, and issue instant digital receipts.
                    </p>
                </div>

                <div class="capability-card">
                    <div class="capability-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Live Performance Reports</h3>
                    <p>
                        Real-time executive oversight into staff response latency, quotation close rates, active proposal values, and net booking profitability.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================
         SECTION 6: PRODUCT EXPERIENCE (TABBED INTERFACE SHOWCASE)
         ================================================================= -->
    <section class="section section-dark" id="showcase">
        <div class="container">
            <div class="section-header center">
                <span class="section-kicker">Realistic Experience</span>
                <h2 class="section-title">Explore the Actual Aleya Platform Interface</h2>
                <p class="section-subtitle">
                    Switch between operational views to preview how Aleya streamlines real daily tasks across sales, itineraries, and logistics.
                </p>
            </div>

            <div class="showcase-tabs-container">
                <div class="showcase-tabs">
                    <button class="showcase-tab-btn active" data-target="showcaseOverview">
                        <i class="fas fa-chart-pie"></i>
                        <span>Executive Overview</span>
                        <span class="tab-chip">Analytics</span>
                    </button>
                    <button class="showcase-tab-btn" data-target="showcaseLeads">
                        <i class="fab fa-whatsapp"></i>
                        <span>Lead Pipeline & WhatsApp</span>
                        <span class="tab-chip">CRM</span>
                    </button>
                    <button class="showcase-tab-btn" data-target="showcaseItineraries">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>Tour Itinerary Engine</span>
                        <span class="tab-chip">CPQ</span>
                    </button>
                    <button class="showcase-tab-btn" data-target="showcaseFleet">
                        <i class="fas fa-truck"></i>
                        <span>Fleet Logistics & Transporters</span>
                        <span class="tab-chip">Fleet Ops</span>
                    </button>
                </div>
            </div>

            <!-- Tab 1: Executive Overview -->
            <div class="showcase-panel active" id="showcaseOverview">
                <div class="hero-mac-frame">
                    <div class="hero-mac-bar">
                        <div class="mac-bar-left">
                            <div class="mac-dots">
                                <div class="mac-dot mac-dot-red"></div>
                                <div class="mac-dot mac-dot-yellow"></div>
                                <div class="mac-dot mac-dot-green"></div>
                            </div>
                            <div class="mac-url">
                                <i class="fas fa-lock text-emerald icon-xs"></i>
                                <span>https://app.aleya.in/dashboard/overview/</span>
                            </div>
                        </div>
                        <div class="mac-status-badge">
                            <span class="status-pulse-dot"></span> Verified Live System
                        </div>
                    </div>
                    <div class="hero-mac-content">
                        <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/actual_dashboard_overview.png" alt="Executive CRM & Business Overview" width="1440" height="900" loading="lazy" />
                    </div>
                    <div class="showcase-feature-strip">
                        <div class="feature-strip-item">
                            <i class="fas fa-chart-line text-teal"></i>
                            <div>
                                <strong>Unified Revenue KPIs</strong>
                                <span>Live margin & order volume rollups across teams</span>
                            </div>
                        </div>
                        <div class="feature-strip-item">
                            <i class="fas fa-users-cog text-teal"></i>
                            <div>
                                <strong>Sales Velocity Tracking</strong>
                                <span>Real-time executive pipeline and conversion monitoring</span>
                            </div>
                        </div>
                        <div class="feature-strip-item">
                            <i class="fas fa-shield-alt text-teal"></i>
                            <div>
                                <strong>Role-Based Access</strong>
                                <span>Strict admin, manager, and sales rep data permissions</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Leads Pipeline -->
            <div class="showcase-panel" id="showcaseLeads">
                <div class="hero-mac-frame">
                    <div class="hero-mac-bar">
                        <div class="mac-bar-left">
                            <div class="mac-dots">
                                <div class="mac-dot mac-dot-red"></div>
                                <div class="mac-dot mac-dot-yellow"></div>
                                <div class="mac-dot mac-dot-green"></div>
                            </div>
                            <div class="mac-url">
                                <i class="fas fa-lock text-emerald icon-xs"></i>
                                <span>https://app.aleya.in/dashboard/leads/</span>
                            </div>
                        </div>
                        <div class="mac-status-badge">
                            <span class="status-pulse-dot"></span> WhatsApp API Connected
                        </div>
                    </div>
                    <div class="hero-mac-content">
                        <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/actual_dashboard_leads.png" alt="Leads Pipeline & WhatsApp Conversion Board" width="1440" height="900" loading="lazy" />
                    </div>
                    <div class="showcase-feature-strip">
                        <div class="feature-strip-item">
                            <i class="fas fa-bolt text-lime"></i>
                            <div>
                                <strong>Zero-Lag Lead Ingestion</strong>
                                <span>Instant webhook capture from Meta Ads & Google Forms</span>
                            </div>
                        </div>
                        <div class="feature-strip-item">
                            <i class="fab fa-whatsapp text-success"></i>
                            <div>
                                <strong>1-Click WhatsApp Dispatch</strong>
                                <span>Send custom quotations & payment links with read receipts</span>
                            </div>
                        </div>
                        <div class="feature-strip-item">
                            <i class="fas fa-columns text-teal"></i>
                            <div>
                                <strong>Visual Kanban Pipeline</strong>
                                <span>Drag-and-drop lead stage progression with automated follow-ups</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Tour Itinerary Engine -->
            <div class="showcase-panel" id="showcaseItineraries">
                <div class="hero-mac-frame">
                    <div class="hero-mac-bar">
                        <div class="mac-bar-left">
                            <div class="mac-dots">
                                <div class="mac-dot mac-dot-red"></div>
                                <div class="mac-dot mac-dot-yellow"></div>
                                <div class="mac-dot mac-dot-green"></div>
                            </div>
                            <div class="mac-url">
                                <i class="fas fa-lock text-emerald icon-xs"></i>
                                <span>https://app.aleya.in/dashboard/packages/</span>
                            </div>
                        </div>
                        <div class="mac-status-badge">
                            <span class="status-pulse-dot"></span> PDF Engine Active
                        </div>
                    </div>
                    <div class="hero-mac-content">
                        <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/actual_dashboard_packages.png" alt="Day-Wise Itinerary Builder & Vouchers" width="1440" height="900" loading="lazy" />
                    </div>
                    <div class="showcase-feature-strip">
                        <div class="feature-strip-item">
                            <i class="fas fa-magic text-teal"></i>
                            <div>
                                <strong>3-Minute Day-Wise Builder</strong>
                                <span>Pre-loaded hotel, flight, and sightseeing package templates</span>
                            </div>
                        </div>
                        <div class="feature-strip-item">
                            <i class="fas fa-file-pdf text-teal"></i>
                            <div>
                                <strong>Branded Client PDF Quotes</strong>
                                <span>White-labeled proposals with QR payment & hotel vouchers</span>
                            </div>
                        </div>
                        <div class="feature-strip-item">
                            <i class="fas fa-calculator text-teal"></i>
                            <div>
                                <strong>Margin & Cost Protection</strong>
                                <span>Real-time calculation of vendor rates, markup, and net profit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Fleet Logistics -->
            <div class="showcase-panel" id="showcaseFleet">
                <div class="hero-mac-frame">
                    <div class="hero-mac-bar">
                        <div class="mac-bar-left">
                            <div class="mac-dots">
                                <div class="mac-dot mac-dot-red"></div>
                                <div class="mac-dot mac-dot-yellow"></div>
                                <div class="mac-dot mac-dot-green"></div>
                            </div>
                            <div class="mac-url">
                                <i class="fas fa-lock text-emerald icon-xs"></i>
                                <span>https://app.aleya.in/dashboard/transporters/</span>
                            </div>
                        </div>
                        <div class="mac-status-badge">
                            <span class="status-pulse-dot"></span> Fleet Telematics Ready
                        </div>
                    </div>
                    <div class="hero-mac-content">
                        <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/actual_dashboard_transporters.png" alt="Vehicle Roster, Duty Slips & Trip Costing" width="1440" height="900" loading="lazy" />
                    </div>
                    <div class="showcase-feature-strip">
                        <div class="feature-strip-item">
                            <i class="fas fa-car-side text-teal"></i>
                            <div>
                                <strong>Live Vehicle Rostering</strong>
                                <span>Conflict-free driver dispatch and vehicle utilization tracking</span>
                            </div>
                        </div>
                        <div class="feature-strip-item">
                            <i class="fas fa-receipt text-teal"></i>
                            <div>
                                <strong>Digital Duty Slips</strong>
                                <span>Paperless guest signature, toll logs, and trip clearance</span>
                            </div>
                        </div>
                        <div class="feature-strip-item">
                            <i class="fas fa-gas-pump text-teal"></i>
                            <div>
                                <strong>Mileage & Fuel Auditing</strong>
                                <span>Garage-to-garage kilometer verification and expense reconciliation</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================
         SECTION 8: HOW IT WORKS (4-STEP OPERATING RHYTHM)
         ================================================================= -->
    <section class="section" id="workflow">
        <div class="container">
            <div class="section-header center">
                <span class="section-kicker">Operating Rhythm</span>
                <h2 class="section-title">From Inbound Lead to Verified Booking</h2>
                <p class="section-subtitle">
                    A disciplined, 4-step workflow that keeps your sales reps fast, your operational data accurate, and your customers delighted.
                </p>
            </div>

            <div class="rhythm-grid">
                <div class="rhythm-card">
                    <div class="rhythm-card-header">
                        <span class="rhythm-step-badge">STEP 01</span>
                        <div class="rhythm-icon"><i class="fas fa-bolt"></i></div>
                    </div>
                    <div class="rhythm-card-body">
                        <h3>Inquiry Arrives Instantly</h3>
                        <p>
                            Prospect submits a Meta ad, Google ad form, or website inquiry. The lead lands in your Aleya pipeline in 0 seconds.
                        </p>
                    </div>
                    <div class="rhythm-meta">
                        <span class="rhythm-meta-label">SPEED</span>
                        <span class="rhythm-meta-val"><i class="fas fa-bolt text-teal"></i> 0-Sec Sync</span>
                    </div>
                </div>

                <div class="rhythm-card">
                    <div class="rhythm-card-header">
                        <span class="rhythm-step-badge">STEP 02</span>
                        <div class="rhythm-icon"><i class="fab fa-whatsapp"></i></div>
                    </div>
                    <div class="rhythm-card-body">
                        <h3>Smart Routing & Alert</h3>
                        <p>
                            Assigned sales executive receives an immediate WhatsApp notification with lead budget, travel dates, and contact details.
                        </p>
                    </div>
                    <div class="rhythm-meta">
                        <span class="rhythm-meta-label">CHANNEL</span>
                        <span class="rhythm-meta-val"><i class="fab fa-whatsapp text-success"></i> WhatsApp Alert</span>
                    </div>
                </div>

                <div class="rhythm-card">
                    <div class="rhythm-card-header">
                        <span class="rhythm-step-badge">STEP 03</span>
                        <div class="rhythm-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    </div>
                    <div class="rhythm-card-body">
                        <h3>Create Itinerary or Quote</h3>
                        <p>
                            Select pre-loaded package templates or vehicle rosters to share a branded PDF quote with payment links in under 3 minutes.
                        </p>
                    </div>
                    <div class="rhythm-meta">
                        <span class="rhythm-meta-label">TIME</span>
                        <span class="rhythm-meta-val"><i class="fas fa-clock text-teal"></i> &lt; 3 Minutes</span>
                    </div>
                </div>

                <div class="rhythm-card">
                    <div class="rhythm-card-header">
                        <span class="rhythm-step-badge">STEP 04</span>
                        <div class="rhythm-icon"><i class="fas fa-calendar-check"></i></div>
                    </div>
                    <div class="rhythm-card-body">
                        <h3>Confirm Booking & Vouchers</h3>
                        <p>
                            Customer transfers deposit. System automatically generates hotel vouchers, digital duty slips, and receipt confirmations.
                        </p>
                    </div>
                    <div class="rhythm-meta">
                        <span class="rhythm-meta-label">OUTPUT</span>
                        <span class="rhythm-meta-val"><i class="fas fa-check-circle text-teal"></i> Auto Vouchers</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================
         SECTION 9: PRICING PREVIEW
         ================================================================= -->
    <section class="section section-muted" id="pricing">
        <div class="container">
            <div class="section-header center">
                <span class="section-kicker">Transparent Plans</span>
                <h2 class="section-title">Straightforward, Predictable Pricing</h2>
                <p class="section-subtitle">
                    Choose the plan that fits your business scale. No hidden onboarding fees or surprise charges.
                </p>

                <!-- Billing Frequency Toggle -->
                <div class="pricing-toggle-wrap mt-6">
                    <span class="pricing-toggle-label" id="labelMonthly">Monthly Billing</span>
                    <label class="toggle-switch" for="pricingBillingToggle" aria-label="Toggle annual or monthly billing">
                        <input type="checkbox" id="pricingBillingToggle" checked />
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="pricing-toggle-label active" id="labelYearly">Annual Billing</span>
                    <span class="pricing-discount-badge">Save up to 20% Annually</span>
                </div>
            </div>

            <div class="pricing-grid-preview">
                <!-- CRM Starter Plan -->
                <div class="pricing-card-preview">
                    <div>
                        <div class="card-header">
                            <h3 class="fs-2xl text-dark">CRM Starter</h3>
                            <p class="fs-sm text-muted">For service businesses focused on lead capture & sales follow-ups.</p>
                        </div>

                        <div class="price-row">
                            <span class="price-currency">₹</span>
                            <span class="price-number price-amount-dynamic" data-yearly="1,499" data-monthly="1,799">1,499</span>
                            <span class="price-period price-period-dynamic">/mo (billed annually)</span>
                            <span class="price-gst-tag">+ 18% GST</span>
                        </div>
                        <p class="billing-note-dynamic fs-sm text-muted mb-4">
                            Billed annually + 18% GST • <span class="badge-waived">₹0 Setup Fee</span>
                        </p>

                        <ul class="pricing-features-list">
                            <li><i class="fas fa-check"></i> Meta & Google Ads automated lead capture</li>
                            <li><i class="fas fa-check"></i> Structured visual sales qualification pipeline</li>
                            <li><i class="fas fa-check"></i> WhatsApp customer communication & reminders</li>
                            <li><i class="fas fa-check"></i> Up to 10 users included (+₹149/user after 10)</li>
                            <li><i class="fas fa-check"></i> Quotation sharing & customer profile history</li>
                            <li><i class="fas fa-check"></i> Fast email & chat support</li>
                        </ul>
                    </div>

                    <div class="mt-6">
                        <a href="/checkout/?plan=crm&cycle=yearly" class="btn btn-secondary btn-block pricing-buy-btn">
                            Get CRM Starter <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- ERP Complete Plan -->
                <div class="pricing-card-preview pricing-card-featured">
                    <span class="featured-pill">Most Popular for Agencies</span>
                    <div>
                        <div class="card-header">
                            <h3 class="fs-2xl text-dark">ERP Complete</h3>
                            <p class="fs-sm text-muted">Full-stack automation for travel, transporters & operations teams.</p>
                        </div>

                        <div class="price-row">
                            <span class="price-currency">₹</span>
                            <span class="price-number price-amount-dynamic" data-yearly="3,999" data-monthly="4,999">3,999</span>
                            <span class="price-period price-period-dynamic">/mo (billed annually)</span>
                            <span class="price-gst-tag">+ 18% GST</span>
                        </div>
                        <p class="billing-note-dynamic fs-sm text-muted mb-4">
                            Billed annually + 18% GST • <span class="badge-waived">₹0 Setup Fee</span>
                        </p>

                        <ul class="pricing-features-list">
                            <li><i class="fas fa-check"></i> Everything in CRM Starter</li>
                            <li><i class="fas fa-check"></i> Day-wise visual tour itinerary builder</li>
                            <li><i class="fas fa-check"></i> 1-click hotel, cab & activity voucher automation</li>
                            <li><i class="fas fa-check"></i> DMC & supplier payable ledger</li>
                            <li><i class="fas fa-check"></i> Vehicle photo quotes & digital duty slips</li>
                            <li><i class="fas fa-check"></i> Multi-warehouse inventory & dealer orders</li>
                            <li><i class="fas fa-check"></i> Up to 10 users included (+₹349/user after 10)</li>
                            <li><i class="fas fa-check"></i> Dedicated priority customer success manager</li>
                        </ul>
                    </div>

                    <div class="mt-6">
                        <a href="/checkout/?plan=erp&cycle=yearly" class="btn btn-primary btn-block pricing-buy-btn">
                            Get ERP Complete <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="/pricing/" class="btn btn-outline">
                    Compare All Features & Limits <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- =================================================================
         SECTION 10 (PART B): CUSTOMER TESTIMONIALS
         ================================================================= -->
    <section class="section" id="testimonials">
        <div class="container">
            <div class="section-header center">
                <span class="section-kicker">Verified Feedback</span>
                <h2 class="section-title">Loved by Business Operators</h2>
                <p class="section-subtitle">Real experiences from teams managing active daily customer inquiries and bookings with Aleya.</p>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div>
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">
                            "Before Aleya, our agents were spending half their afternoon formatting itineraries in Canva. Now they customize itineraries and share quotes on WhatsApp in 3 minutes. It completely changed our conversion rate."
                        </p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">MT</div>
                        <div class="author-info">
                            <h4>Rakesh Verma</h4>
                            <span>Director, Musafir India Trip</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div>
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">
                            "Automated WhatsApp reminders reduced our spa appointment no-shows from over 18% down to almost nothing. The room and therapist roster prevents double bookings on busy weekends."
                        </p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">LS</div>
                        <div class="author-info">
                            <h4>Dr. Preeti Sen</h4>
                            <span>Operations Head, La Skinnovita</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div>
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">
                            "Sending genuine vehicle photos directly from Aleya into WhatsApp closes taxi rentals twice as fast. Drivers get duty slips on their phones and we have clear profit records per trip."
                        </p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">SJ</div>
                        <div class="author-info">
                            <h4>Gurpreet Singh</h4>
                            <span>Fleet Manager, Golden Triangle Transports</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================
         SECTION 11: HOMEPAGE FAQS
         ================================================================= -->
    <section class="section section-muted" id="faq">
        <div class="container container-narrow">
            <div class="section-header center">
                <span class="section-kicker">Got Questions?</span>
                <h2 class="section-title">Frequently Asked Questions</h2>
                <p class="section-subtitle">Everything you need to know about getting started, team accounts, and implementation.</p>
            </div>

            <div class="accordion">
                <div class="accordion-item active">
                    <button class="accordion-header" aria-expanded="true">
                        <span>How does Aleya capture leads from Facebook and Google Ads?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>
                            Aleya connects directly via official webhook integrations with Meta Lead Ads and Google Ads. As soon as a user submits an ad form, the inquiry is created in your workspace in zero seconds with full lead parameters, trigger alerts, and auto-assignment.
                        </p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header" aria-expanded="false">
                        <span>Is there a long-term contract or setup fee?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>
                            No. You can subscribe on a flexible monthly basis or choose an annual subscription to save 25%. There are zero mandatory setup fees, hidden implementation charges, or termination penalties.
                        </p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header" aria-expanded="false">
                        <span>How many team members can use the platform?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>
                            Both CRM Starter and ERP Complete include up to 10 users out of the box. Additional users can be added at any time for ₹149/user/month (CRM) or ₹349/user/month (ERP).
                        </p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header" aria-expanded="false">
                        <span>How long does onboarding and workspace setup take?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>
                            Your workspace subdomain (e.g. youragency.aleya.in) is provisioned immediately upon order. Most businesses configure their master packages, lead sources, and team members within 1 business day with guidance from our support team.
                        </p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header" aria-expanded="false">
                        <span>Can we import our existing customer contacts from Excel?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>
                            Yes. Aleya provides straightforward CSV and Excel import utilities to bring your existing customer database, destination packages, or vendor registries into your new workspace.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================
         SECTION 12: FINAL CONVERSION BANNER
         ================================================================= -->
    <section class="section pt-0">
        <div class="container">
            <div class="cta-banner">
                <h2>Ready to Move Your Business to Autopilot?</h2>
                <p>Join hundreds of forward-thinking operators who save hours every week, eliminate missed leads, and deliver professional customer experiences with Aleya.</p>
                <div class="cta-banner-actions">
                    <a href="/checkout/" class="btn btn-secondary btn-lg">
                        Get Started Today <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" rel="noopener noreferrer" class="btn btn-dark btn-lg" data-track="demo">
                        <i class="far fa-calendar-check"></i> Book a 1-on-1 Product Demo
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
