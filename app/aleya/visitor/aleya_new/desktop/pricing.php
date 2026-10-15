<?php
/**
 * Aleya SaaS — Dedicated Pricing Page
 * Transparent pricing plans, feature comparison matrix, and billing FAQs
 */

require_once __DIR__ . '/includes/seo.php';

$seoConfig = [
    'title' => 'Aleya Pricing — Transparent SaaS Plans for CRM & Operations',
    'description' => 'Compare Aleya CRM Starter (₹1,499/mo) and ERP Complete (₹3,999/mo) plans. 10 users included out of the box with zero hidden onboarding fees.',
    'canonical' => 'https://aleya.in/pricing/',
    'keywords' => 'travel CRM pricing, ERP software cost, travel agency software pricing, fleet management cost, spa software pricing',
    'stylesheets' => [
        '/app/aleya/visitor/aleya_new/desktop/assets/css/homepage.css',
        '/app/aleya/visitor/aleya_new/desktop/assets/css/pages.css'
    ],
    'schema' => [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => 'Aleya SaaS Business Platform',
        'description' => 'Comprehensive CRM and ERP software for travel, transport, distribution, and service enterprises.',
        'brand' => [
            '@type' => 'Brand',
            'name' => 'Aleya'
        ],
        'offers' => [
            [
                '@type' => 'Offer',
                'name' => 'CRM Starter',
                'price' => '1499',
                'priceCurrency' => 'INR',
                'priceValidUntil' => '2027-12-31'
            ],
            [
                '@type' => 'Offer',
                'name' => 'ERP Complete',
                'price' => '3999',
                'priceCurrency' => 'INR',
                'priceValidUntil' => '2027-12-31'
            ]
        ]
    ]
];

$pageScripts = [
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
    <section class="page-hero">
        <div class="container">
            <span class="section-kicker">Predictable Investment</span>
            <h1>Simple, Transparent SaaS Pricing</h1>
            <p>Every plan includes 10 team users, native WhatsApp capabilities, and verified database isolation. Scale as your sales volume grows.</p>

            <!-- Billing Frequency Toggle -->
            <div class="pricing-toggle-wrap mt-8">
                <span class="pricing-toggle-label active" id="labelYearly">Annual Billing</span>
                <label class="toggle-switch">
                    <input type="checkbox" id="pricingBillingToggle" checked />
                    <span class="toggle-slider"></span>
                </label>
                <span class="pricing-toggle-label" id="labelMonthly">Monthly Billing</span>
                <span class="pricing-discount-badge">Save 25% Annually</span>
            </div>
        </div>
    </section>

    <!-- Pricing Cards Grid -->
    <section class="section pt-8">
        <div class="container">
            <div class="pricing-grid-preview">
                <!-- CRM Starter Card -->
                <div class="pricing-card-preview">
                    <div>
                        <div class="card-header">
                            <h3 class="fs-2xl text-dark">CRM Starter</h3>
                            <p class="fs-sm text-muted">For businesses of any vertical getting inquiries and team sales organized.</p>
                        </div>

                        <div class="price-row">
                            <span class="price-currency">₹</span>
                            <span class="price-number price-amount-dynamic" data-yearly="1,499" data-monthly="1,874">1,499</span>
                            <span class="price-period price-period-dynamic">/mo (billed annually)</span>
                            <span class="price-gst-tag">+ 18% GST</span>
                        </div>
                        <p class="billing-note-dynamic fs-sm text-muted mb-4">
                            Billed annually + 18% GST
                        </p>

                        <ul class="pricing-features-list">
                            <li><i class="fas fa-check"></i> Meta & Google Ads automated lead capture</li>
                            <li><i class="fas fa-check"></i> Visual drag-and-drop lead qualification pipeline</li>
                            <li><i class="fas fa-check"></i> Native WhatsApp messaging & reminder alerts</li>
                            <li><i class="fas fa-check"></i> <strong>Up to 10 users included</strong> out of the box</li>
                            <li><i class="fas fa-check"></i> +₹149/user/month for additional users after 10</li>
                            <li><i class="fas fa-check"></i> Quotation builder & customer history records</li>
                            <li><i class="fas fa-check"></i> Standard email and WhatsApp customer support</li>
                        </ul>
                    </div>

                    <div class="mt-6">
                        <a href="/checkout/?plan=crm&cycle=yearly" class="btn btn-secondary btn-block btn-lg pricing-buy-btn">
                            Get CRM Starter <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- ERP Complete Card -->
                <div class="pricing-card-preview pricing-card-featured">
                    <span class="featured-pill">Recommended For Full Operations</span>
                    <div>
                        <div class="card-header">
                            <h3 class="fs-2xl text-dark">ERP Complete</h3>
                            <p class="fs-sm text-muted">Full-stack automation for travel agencies, transport fleets, and multi-warehouse operations.</p>
                        </div>

                        <div class="price-row">
                            <span class="price-currency">₹</span>
                            <span class="price-number price-amount-dynamic" data-yearly="3,999" data-monthly="4,999">3,999</span>
                            <span class="price-period price-period-dynamic">/mo (billed annually)</span>
                            <span class="price-gst-tag">+ 18% GST</span>
                        </div>
                        <p class="billing-note-dynamic fs-sm text-muted mb-4">
                            Billed annually + 18% GST
                        </p>

                        <ul class="pricing-features-list">
                            <li><i class="fas fa-check"></i> <strong>Everything in CRM Starter</strong></li>
                            <li><i class="fas fa-check"></i> Day-wise visual tour itinerary builder & sharing</li>
                            <li><i class="fas fa-check"></i> 1-click hotel, cab & activity voucher automation</li>
                            <li><i class="fas fa-check"></i> DMC & supplier payable ledger tracking</li>
                            <li><i class="fas fa-check"></i> Vehicle photo quotes & digital duty slips</li>
                            <li><i class="fas fa-check"></i> Multi-warehouse inventory & dealer orders</li>
                            <li><i class="fas fa-check"></i> <strong>Up to 10 users included</strong> (+₹349/extra user)</li>
                            <li><i class="fas fa-check"></i> Dedicated priority customer success manager</li>
                        </ul>
                    </div>

                    <div class="mt-6">
                        <a href="/checkout/?plan=erp&cycle=yearly" class="btn btn-primary btn-block btn-lg pricing-buy-btn">
                            Get ERP Complete <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Detailed Comparison Table -->
            <div class="comparison-section">
                <div class="section-header center">
                    <span class="section-kicker">Deep Dive</span>
                    <h2 class="section-title">Full Feature Comparison Matrix</h2>
                    <p class="section-subtitle">Examine exact capability coverage side-by-side.</p>
                </div>

                <div class="comparison-table-wrapper">
                    <table class="comparison-table">
                        <thead>
                            <tr>
                                <th>Platform Feature</th>
                                <th class="text-center col-quarter">CRM Starter</th>
                                <th class="text-center col-quarter">ERP Complete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Lead Ingestion & CRM -->
                            <tr class="comparison-category-row">
                                <td colspan="3">1. Lead Capture & Sales Qualification</td>
                            </tr>
                            <tr>
                                <td>Meta Lead Ads (Facebook & Instagram) Direct Webhook Sync</td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>Google Ads & Website Form Webhook Ingestion</td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>Visual Kanban Board (Qualification Stages)</td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>Smart Follow-up Date Reminders & Alerts</td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>Native WhatsApp Client Chat & Quotation Nudges</td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>

                            <!-- Travel Agency Modules -->
                            <tr class="comparison-category-row">
                                <td colspan="3">2. Travel Agency & Tour Operations</td>
                            </tr>
                            <tr>
                                <td>Day-Wise Itinerary Builder with Sightseeing & Inclusions</td>
                                <td class="text-center"><i class="fas fa-times"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>Branded Hotel, Cab & Activity Voucher Automation</td>
                                <td class="text-center"><i class="fas fa-times"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>DMC & Supplier Payables / Net Cost Tracking</td>
                                <td class="text-center"><i class="fas fa-times"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>Visa Application Stage Management</td>
                                <td class="text-center"><i class="fas fa-times"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>

                            <!-- Transporters & Fleet -->
                            <tr class="comparison-category-row">
                                <td colspan="3">3. Transporters & Fleet Logistics</td>
                            </tr>
                            <tr>
                                <td>Vehicle Roster & Driver Allocation Registry</td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>1-Click WhatsApp Vehicle Photo Gallery Dispatch</td>
                                <td class="text-center"><i class="fas fa-times"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>Outstation & Local Trip Costing Calculator</td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>Digital Duty Slips Dispatched via WhatsApp</td>
                                <td class="text-center"><i class="fas fa-times"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>

                            <!-- DMS & Distribution -->
                            <tr class="comparison-category-row">
                                <td colspan="3">4. Distribution Management (DMS)</td>
                            </tr>
                            <tr>
                                <td>Multi-Warehouse Stock Levels & Transfers</td>
                                <td class="text-center"><i class="fas fa-times"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>Dealer Sales Request & Approval Hierarchy</td>
                                <td class="text-center"><i class="fas fa-times"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>Stock Restock Triggers & Supplier PO Issuance</td>
                                <td class="text-center"><i class="fas fa-times"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>

                            <!-- Team & Limits -->
                            <tr class="comparison-category-row">
                                <td colspan="3">5. Team Limits, Access & Support</td>
                            </tr>
                            <tr>
                                <td>Users Included</td>
                                <td class="text-center"><strong>Up to 10 Users</strong></td>
                                <td class="text-center"><strong>Up to 10 Users</strong></td>
                            </tr>
                            <tr>
                                <td>Additional User Fee</td>
                                <td class="text-center">₹149 / user / month</td>
                                <td class="text-center">₹349 / user / month</td>
                            </tr>
                            <tr>
                                <td>Dedicated Isolated Subdomain</td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                                <td class="text-center"><i class="fas fa-check"></i></td>
                            </tr>
                            <tr>
                                <td>Customer Support Channel</td>
                                <td class="text-center">Email & WhatsApp Helpdesk</td>
                                <td class="text-center">Priority Phone, WhatsApp & Dedicated Onboarding</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing FAQs -->
    <section class="section section-muted">
        <div class="container container-narrow">
            <div class="section-header center">
                <span class="section-kicker">Billing Clarifications</span>
                <h2 class="section-title">Frequently Asked Pricing Questions</h2>
            </div>

            <div class="accordion">
                <div class="accordion-item active">
                    <button class="accordion-header" aria-expanded="true">
                        <span>Are taxes included in the displayed pricing?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>Prices are exclusive of 18% GST. A valid tax invoice with your company’s GSTIN number is generated automatically upon payment for full input tax credit.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header" aria-expanded="false">
                        <span>Can we upgrade from CRM Starter to ERP Complete later?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>Yes. You can upgrade at any time directly inside your account settings. All customer inquiries, contacts, and staff records remain completely intact.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header" aria-expanded="false">
                        <span>What payment modes are accepted?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>We support all Indian payment modes including UPI, Net Banking, Credit/Debit cards, and NEFT/RTGS bank transfers for annual corporate accounts.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
