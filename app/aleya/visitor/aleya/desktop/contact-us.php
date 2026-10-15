<?php
/**
 * Aleya SaaS — Contact Us Page
 * Handles inquiries, client & server-side validation, and CRM ingestion
 */

require_once __DIR__ . '/includes/seo.php';

$feedbackMessage = '';
$feedbackType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $industry = trim($_POST['industry'] ?? '');
    $inquiryType = trim($_POST['inquiry_type'] ?? 'Demo Request');
    $message = trim($_POST['message'] ?? '');

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if (empty($name) || empty($email) || empty($phone) || empty($industry)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields (Name, Email, Mobile, and Industry).']);
            exit();
        }
        $feedbackMessage = 'Please fill in all required fields.';
        $feedbackType = 'error';
    } else {
        // Safe database ingestion if database connection is available
        if (isset($connection) && $connection instanceof mysqli) {
            $escName = mysqli_real_escape_string($connection, $name);
            $escEmail = mysqli_real_escape_string($connection, $email);
            $escPhone = mysqli_real_escape_string($connection, $phone);
            $escCompany = mysqli_real_escape_string($connection, $company);
            $escIndustry = mysqli_real_escape_string($connection, $industry);
            $escType = mysqli_real_escape_string($connection, $inquiryType);
            $escMsg = mysqli_real_escape_string($connection, $message);
            $siteId = defined('SITE_ID') ? (int)SITE_ID : 52;

            $insertSql = "INSERT INTO " . TABLE_PREFIX . "enquiry 
                (enquiry_site_id, enquiry_name, enquiry_email, enquiry_mobile, enquiry_subject, enquiry_message, enquiry_status, enquiry_created) 
                VALUES 
                ({$siteId}, '{$escName}', '{$escEmail}', '{$escPhone}', '{$escType} [{$escIndustry}]', 'Company: {$escCompany}\nMessage: {$escMsg}', 1, NOW())";
            @mysqli_query($connection, $insertSql);
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Your inquiry has been received. Our team will contact you shortly.']);
            exit();
        }

        $feedbackMessage = 'Thank you! Your inquiry has been received. Our solution team will contact you shortly.';
        $feedbackType = 'success';
    }
}

$seoConfig = [
    'title' => 'Contact Aleya — Book a Demo or Talk to Sales Support',
    'description' => 'Contact the Aleya solution team for product demonstrations, pricing questions, onboarding support, or customized industry workflows.',
    'canonical' => 'https://aleya.in/contact-us/',
    'stylesheets' => [
        '/app/aleya/visitor/aleya_new/desktop/assets/css/pages.css'
    ],
    'schema' => [
        '@context' => 'https://schema.org',
        '@type' => 'ContactPage',
        'name' => 'Contact Aleya',
        'url' => 'https://aleya.in/contact-us/',
        'mainEntity' => [
            '@type' => 'Organization',
            'name' => 'Aleya',
            'telephone' => '+91-9990026008',
            'email' => 'support@aleya.in',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Kirti Nagar',
                'addressLocality' => 'New Delhi',
                'addressCountry' => 'IN'
            ]
        ]
    ]
];

$pageScripts = [
    '/app/aleya/visitor/aleya_new/desktop/assets/js/contact.js'
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
            <span class="section-kicker">Get in Touch</span>
            <h1>Talk to the Aleya Solution Team</h1>
            <p>Have questions about onboarding, pricing, or industry workflows? We are here to help you get started with zero friction.</p>
        </div>
    </section>

    <section class="section pt-12">
        <div class="container">
            <div class="contact-grid-layout">
                <!-- Contact Form Card -->
                <div class="contact-form-card">
                    <h2 class="fs-2xl mb-2">Send Us a Message</h2>
                    <p class="fs-sm mb-6">Fill out the form below and an Aleya product specialist will connect with you within 2 business hours.</p>

                    <div id="contactAlert" class="neo-card <?php echo empty($feedbackMessage) ? 'd-none' : 'd-block'; ?> <?php echo $feedbackType === 'success' ? 'badge-green' : 'badge-crimson'; ?>">
                        <?php echo htmlspecialchars($feedbackMessage, ENT_QUOTES, 'UTF-8'); ?>
                    </div>

                    <form id="contactForm" method="POST" action="/contact-us/" novalidate>
                        <div class="grid-2">
                            <div class="form-group">
                                <label for="contactName" class="form-label">Full Name *</label>
                                <input type="text" id="contactName" name="name" class="form-input" placeholder="e.g. Ramesh Kumar" required />
                                <span class="form-error" id="nameError"></span>
                            </div>

                            <div class="form-group">
                                <label for="contactEmail" class="form-label">Business Email *</label>
                                <input type="email" id="contactEmail" name="email" class="form-input" placeholder="name@yourcompany.com" required />
                                <span class="form-error" id="emailError"></span>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="form-group">
                                <label for="contactPhone" class="form-label">Mobile Number *</label>
                                <input type="tel" id="contactPhone" name="phone" class="form-input" placeholder="+91 98765 43210" required />
                                <span class="form-error" id="phoneError"></span>
                            </div>

                            <div class="form-group">
                                <label for="contactCompany" class="form-label">Company / Agency Name</label>
                                <input type="text" id="contactCompany" name="company" class="form-input" placeholder="e.g. Skyline Holidays" />
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="form-group">
                                <label for="contactIndustry" class="form-label">Your Industry *</label>
                                <select id="contactIndustry" name="industry" class="form-select" required>
                                    <option value="" disabled selected>Select Industry</option>
                                    <option value="Travel Agency">Travel Agency & Tour Operator</option>
                                    <option value="Travel Agent">Independent Travel Agent</option>
                                    <option value="Transporter">Transporter & Fleet Logistics</option>
                                    <option value="DMS">DMS (Distribution / Wholesale)</option>
                                    <option value="Spa Management">Spa, Salon & Wellness Clinic</option>
                                    <option value="Other">Other Service Business</option>
                                </select>
                                <span class="form-error" id="industryError"></span>
                            </div>

                            <div class="form-group">
                                <label for="contactInquiryType" class="form-label">Inquiry Type</label>
                                <select id="contactInquiryType" name="inquiry_type" class="form-select">
                                    <option value="Product Demo">Request 1-on-1 Product Demo</option>
                                    <option value="Pricing Question">Pricing & Plan Inquiries</option>
                                    <option value="Onboarding Support">Onboarding & Setup Guidance</option>
                                    <option value="Custom Integration">Custom Workflow / API Integration</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contactMessage" class="form-label">How can we help you?</label>
                            <textarea id="contactMessage" name="message" class="form-textarea" placeholder="Describe your daily operations, team size, or what software you are currently using..."></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" id="contactSubmitBtn" class="btn btn-primary btn-lg btn-block">
                                Send Message <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                        <p class="fs-xs text-muted mt-4 text-center">
                            We respect your privacy. No spam. Your contact information is never shared with third parties.
                        </p>
                    </form>
                </div>

                <!-- Contact Info & Office Card -->
                <div class="contact-info-card">
                    <h3 class="fs-xl text-dark">Direct Communication</h3>
                    <p class="fs-sm text-muted">
                        Prefer to talk right now? Reach out through direct call, WhatsApp, or email and we will be delighted to answer your questions.
                    </p>

                    <div class="contact-detail-row">
                        <div class="contact-detail-icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="contact-detail-info">
                            <h4>Phone Support</h4>
                            <a href="tel:+919990026008">+91-9990026008</a>
                            <p class="fs-xs text-muted">Mon – Sat, 10:00 AM – 7:00 PM IST</p>
                        </div>
                    </div>

                    <div class="contact-detail-row">
                        <div class="contact-detail-icon"><i class="fab fa-whatsapp"></i></div>
                        <div class="contact-detail-info">
                            <h4>Instant WhatsApp</h4>
                            <a href="https://wa.me/919990026008?text=Hi,%20I'm%20interested%20in%20Aleya" target="_blank" rel="noopener noreferrer">Chat on WhatsApp</a>
                            <p class="fs-xs text-muted">Instant response for quick questions</p>
                        </div>
                    </div>

                    <div class="contact-detail-row">
                        <div class="contact-detail-icon"><i class="fas fa-envelope"></i></div>
                        <div class="contact-detail-info">
                            <h4>Email Inquiries</h4>
                            <a href="mailto:support@aleya.in">support@aleya.in</a>
                            <p class="fs-xs text-muted">Responses within 2 business hours</p>
                        </div>
                    </div>

                    <div class="contact-detail-row">
                        <div class="contact-detail-icon"><i class="fas fa-location-dot"></i></div>
                        <div class="contact-detail-info">
                            <h4>Corporate Office</h4>
                            <p>Kirti Nagar, New Delhi, India</p>
                            <p class="fs-xs text-muted">A Product of Blumox Technologies</p>
                        </div>
                    </div>

                    <!-- Map Frame -->
                    <div class="contact-map-frame">
                        <iframe src="https://www.google.com/maps?q=Kirti%20Nagar%20New%20Delhi&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Aleya office location in Kirti Nagar, New Delhi"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
