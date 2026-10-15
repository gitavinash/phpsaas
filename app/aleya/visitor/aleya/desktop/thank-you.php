<?php
/**
 * Aleya SaaS — Thank You / Order Confirmation Page
 * Confirmation state for demo bookings, inquiries, and subscription activations
 */

require_once __DIR__ . '/includes/seo.php';

$type = isset($_GET['type']) ? htmlspecialchars(trim($_GET['type'])) : 'inquiry';
$name = isset($_GET['name']) ? htmlspecialchars(trim($_GET['name'])) : '';

$seoConfig = [
    'title' => 'Thank You — Aleya SaaS Business Platform',
    'description' => 'Your submission has been received. Our team is preparing your workspace and will connect with you shortly.',
    'canonical' => 'https://aleya.in/thank-you/',
    'robots' => 'noindex, follow',
    'stylesheets' => [
        '/app/aleya/visitor/aleya_new/desktop/assets/css/pages.css'
    ]
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
    <section class="section">
        <div class="container">
            
            <div class="thank-you-card">
                <div class="thank-you-icon">
                    <i class="fas fa-check"></i>
                </div>

                <?php if ($type === 'order'): ?>
                    <span class="badge badge-brand">Order Received</span>
                    <h1>Workspace Activation In Progress!</h1>
                    <p>Thank you<?php echo !empty($name) ? ', ' . $name : ''; ?>. We have received your subscription request and our provisioning team is configuring your cloud environment.</p>

                    <div class="thank-you-steps-box">
                        <h4>What happens next?</h4>
                        <ol>
                            <li><strong>Confirmation Email:</strong> We have sent an acknowledgment and payment receipt to your registered email address.</li>
                            <li><strong>Database Provisioning:</strong> Your isolated cloud workspace instance is being provisioned on our high-speed server clusters.</li>
                            <li><strong>Onboarding Kickoff:</strong> A dedicated Aleya implementation manager will contact you within 2 business hours to schedule data migration and initial staff training.</li>
                        </ol>
                    </div>
                <?php else: ?>
                    <span class="badge badge-brand">Inquiry Received</span>
                    <h1>Thank You for Connecting!</h1>
                    <p>We have received your message<?php echo !empty($name) ? ', ' . $name : ''; ?>. An Aleya product specialist has been assigned to your request and will reach out shortly.</p>

                    <div class="thank-you-steps-box">
                        <h4>Next steps on our end:</h4>
                        <ol>
                            <li><strong>Requirements Review:</strong> We analyze your industry workflows (CRM, Invoicing, Dispatch, or Operations) to prepare relevant live demonstrations.</li>
                            <li><strong>Direct Call / WhatsApp:</strong> We will connect via phone or WhatsApp to coordinate a convenient time for your walk-through.</li>
                            <li><strong>Sandbox Access:</strong> You will receive sandbox credentials pre-loaded with sample data for your specific industry segment.</li>
                        </ol>
                    </div>
                <?php endif; ?>

                <div class="thank-you-cta-group mt-6 d-flex justify-center gap-4">
                    <a href="/" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Return to Homepage</span>
                    </a>
                    <a href="https://wa.me/919990026008?text=Hi%20Aleya%20Team,%20I%20just%20submitted%20a%20request%20on%20your%20website" class="btn btn-primary" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-whatsapp"></i>
                        <span>Need Immediate Assistance? Chat on WhatsApp</span>
                    </a>
                </div>

                <div class="thank-you-direct-info mt-6 text-muted fs-sm">
                    <p>Have an urgent operational question? Call our direct support desk at <a href="tel:+919990026008"><strong>+91-9990026008</strong></a> (Mon–Sat, 9:30 AM – 7:30 PM IST).</p>
                </div>
            </div>

        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
