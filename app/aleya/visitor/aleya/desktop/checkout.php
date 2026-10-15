<?php
/**
 * Aleya SaaS — Checkout & Order Page
 * Dedicated conversion architecture with verified pricing and real-time calculation
 */

require_once __DIR__ . '/includes/seo.php';

$seoConfig = [
    'title' => 'Launch Your Workspace — Aleya SaaS Order & Checkout',
    'description' => 'Configure your Aleya CRM Starter or ERP Complete subscription, verify your custom domain, and initiate immediate workspace setup.',
    'canonical' => 'https://aleya.in/checkout/',
    'keywords' => 'aleya checkout, subscribe crm, order erp, travel agency software order, business workspace setup',
    'stylesheets' => [
        '/app/aleya/visitor/aleya_new/desktop/assets/css/pages.css'
    ]
];

$pageScripts = [
    '/app/aleya/visitor/aleya_new/desktop/assets/js/checkout.js'
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
    <!-- Hero Header -->
    <header class="page-hero">
        <div class="container">
            <span class="badge badge-brand">Secure Checkout</span>
            <h1>Launch Your Aleya Workspace</h1>
            <p>Configure your subscription, verify your custom domain, and initiate immediate workspace setup.</p>
        </div>
    </header>

    <!-- Checkout Main Content -->
    <section class="section">
        <div class="container">
            <div class="checkout-grid">
                
                <!-- Left Column: Form -->
                <div class="checkout-form-container">
                    <form id="checkoutForm" class="contact-form-card" novalidate>
                        
                        <!-- Section 1: Plan & Billing Cycle -->
                        <h3 class="section-title">1. Subscription Configuration</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="checkoutPlan" class="form-label">Select Software Plan *</label>
                                <select id="checkoutPlan" name="plan" class="form-select" required>
                                    <option value="crm">CRM Starter — Pipeline, Leads & Team (₹1,499/mo)</option>
                                    <option value="erp" selected>ERP Complete — Full Operations, Invoices & Approvals (₹3,999/mo)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Billing Cycle *</label>
                                <div class="billing-cycle-options">
                                    <label class="radio-label">
                                        <input type="radio" name="billing_cycle" value="yearly" checked>
                                        <span>Annual Billing (Save 20%)</span>
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="billing_cycle" value="monthly">
                                        <span>Monthly Billing</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="checkoutUsers" class="form-label">Included Team Users *</label>
                                <input type="number" id="checkoutUsers" name="users" class="form-input" value="10" min="1" max="100" required>
                                <span class="form-hint">10 users included by default. Additional seats are automatically calculated.</span>
                            </div>

                            <div class="form-group">
                                <label for="checkoutSubdomain" class="form-label">Choose Workspace Subdomain *</label>
                                <div class="subdomain-input-group">
                                    <input type="text" id="checkoutSubdomain" name="subdomain" class="form-input" placeholder="myagency" required autocomplete="off">
                                    <div class="subdomain-addon">.aleya.in</div>
                                </div>
                                <div id="subdomainStatus" class="domain-status-badge">
                                    <span>Enter your preferred workspace prefix</span>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Account & Company Details -->
                        <h3 class="section-title mt-8">2. Organization & Billing Contact</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="orderName" class="form-label">Full Name *</label>
                                <input type="text" id="orderName" name="name" class="form-input" placeholder="e.g. Rajesh Sharma" required>
                            </div>

                            <div class="form-group">
                                <label for="orderEmail" class="form-label">Work Email *</label>
                                <input type="email" id="orderEmail" name="email" class="form-input" placeholder="rajesh@company.com" required>
                            </div>

                            <div class="form-group">
                                <label for="orderPhone" class="form-label">Mobile Number *</label>
                                <input type="tel" id="orderPhone" name="phone" class="form-input" placeholder="+91 98765 43210" required>
                            </div>

                            <div class="form-group">
                                <label for="orderBusiness" class="form-label">Company / Agency Name *</label>
                                <input type="text" id="orderBusiness" name="business_name" class="form-input" placeholder="Skyline Holidays Pvt Ltd" required>
                            </div>

                            <div class="form-group full-width">
                                <label for="orderGst" class="form-label">GSTIN (Optional, for Tax Credit)</label>
                                <input type="text" id="orderGst" name="gstin" class="form-input" placeholder="e.g. 07AAAAA0000A1Z5">
                                <span class="form-hint">Provide your GST number if you wish to claim 18% Input Tax Credit on your invoice.</span>
                            </div>
                        </div>

                        <!-- Section 3: Terms & Confirmation -->
                        <div class="checkout-terms-wrap mt-6">
                            <label class="checkbox-label">
                                <input type="checkbox" name="terms" required checked>
                                <span>I agree to Aleya's <a href="/terms-and-conditions/" target="_blank">Terms of Service</a> and <a href="/privacy-policy/" target="_blank">Privacy Policy</a>.</span>
                            </label>
                        </div>

                        <div class="checkout-submit-wrap mt-6">
                            <button type="submit" id="checkoutSubmitBtn" class="btn btn-primary btn-lg">
                                <span>Continue to Workspace Setup</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="checkout-summary-container">
                    <aside class="order-summary-card">
                        <h3 class="order-summary-title">Order Summary</h3>
                        
                        <div class="order-breakdown-row">
                            <span>Selected Plan</span>
                            <strong id="summaryPlanName">ERP Complete</strong>
                        </div>

                        <div class="order-breakdown-row">
                            <span>Billing Frequency</span>
                            <strong id="summaryCycle">Annual (12 Months)</strong>
                        </div>

                        <div class="order-breakdown-row">
                            <span>Base Subscription</span>
                            <strong id="summaryBasePrice">₹47,988</strong>
                        </div>

                        <div class="order-breakdown-row">
                            <span>Additional Users</span>
                            <strong id="summaryExtraUsers">Included (10 Users)</strong>
                        </div>

                        <div class="order-breakdown-row">
                            <span>Applicable GST (18%)</span>
                            <strong id="summaryGst">₹8,638</strong>
                        </div>

                        <div class="order-total-row">
                            <div>
                                <strong>Total Payable</strong>
                                <div class="form-hint">Inclusive of 18% GST</div>
                            </div>
                            <div class="order-total-amount" id="summaryTotal">₹56,626</div>
                        </div>

                        <!-- Security & Guarantee Badges -->
                        <div class="checkout-badges mt-6">
                            <div class="trust-badge-item">
                                <i class="fas fa-shield-alt text-brand"></i>
                                <span>256-bit TLS Encrypted Checkout</span>
                            </div>
                            <div class="trust-badge-item">
                                <i class="fas fa-file-invoice text-brand"></i>
                                <span>Valid GST Invoice Issued Immediately</span>
                            </div>
                            <div class="trust-badge-item">
                                <i class="fas fa-headset text-brand"></i>
                                <span>Dedicated Onboarding & Human Support</span>
                            </div>
                        </div>

                        <!-- Need Assistance Callout -->
                        <div class="checkout-help-box mt-6">
                            <p><strong>Need help with purchase?</strong></p>
                            <p>Call our corporate desk directly at <a href="tel:+919990026008">+91-9990026008</a> or email <a href="mailto:support@aleya.in">support@aleya.in</a>.</p>
                        </div>
                    </aside>
                </div>

            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section section-gray">
        <div class="container">
            <div class="section-header text-center">
                <span class="badge badge-brand">Checkout FAQs</span>
                <h2>Frequently Asked Billing Questions</h2>
            </div>

            <div class="faq-accordion-wrap">
                <div class="accordion-item">
                    <button class="accordion-header" type="button" aria-expanded="false">
                        <span>What payment methods are supported?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>We support corporate Net Banking (NEFT/RTGS/IMPS), UPI, all major credit/debit cards (Visa, MasterCard, RuPay), and direct invoice settlement for annual enterprise subscriptions.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header" type="button" aria-expanded="false">
                        <span>When does workspace provisioning happen?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>Immediately after completing the setup form and confirming the subscription, your isolated cloud database and custom subdomain are provisioned automatically. You receive administrative credentials on your registered work email.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header" type="button" aria-expanded="false">
                        <span>Can I add extra users later?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>Yes. You can add or remove users at any time directly from your Aleya admin dashboard under Settings &gt; Team Management. Additional seats are pro-rated automatically for the remainder of your billing period.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header" type="button" aria-expanded="false">
                        <span>Will I receive a GST-compliant tax invoice?</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div class="accordion-content">
                        <p>Yes. Blumox Technologies generates and emails a GST-compliant tax invoice immediately upon payment verification, enabling your finance team to claim 18% Input Tax Credit.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
