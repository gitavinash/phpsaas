<?php
/**
 * Aleya SaaS — Centralized SEO & Structured Data Helper
 * Generates unique meta tags, social cards, and Schema.org JSON-LD
 */

function render_seo_meta(array $config): void {
    $siteName = 'Aleya';
    $baseUrl = 'https://aleya.in';
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $canonicalUrl = $config['canonical'] ?? ($baseUrl . $currentPath);

    $title = $config['title'] ?? 'Aleya — Business Management, CRM & Operational SaaS Platform';
    $description = $config['description'] ?? 'Automate inquiries, customer follow-ups, quotes, and backend operations with Aleya.';
    $keywords = $config['keywords'] ?? 'business CRM, travel ERP, operations software, lead management, WhatsApp CRM';
    $ogImage = $config['og_image'] ?? ($baseUrl . '/app/aleya/visitor/aleya_new/desktop/assets/images/aleya_push_notification.png');
    $pageType = $config['type'] ?? 'website';
    $stylesheets = $config['stylesheets'] ?? [];

    $schemaData = $config['schema'] ?? null;
    ?>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="keywords" content="<?php echo htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8'); ?>" />
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="robots" content="<?php echo htmlspecialchars($config['robots'] ?? 'index, follow', ENT_QUOTES, 'UTF-8'); ?>" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo htmlspecialchars($pageType, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta property="og:title" content="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="twitter:description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>" />

    <!-- Favicon -->
    <link rel="icon" href="/app/aleya/visitor/aleya_new/desktop/assets/images/aleya_favicon.png" type="image/png" />
    <link rel="shortcut icon" href="/app/aleya/visitor/aleya_new/desktop/assets/images/aleya_favicon.png" type="image/png" />

    <!-- Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700;800&display=swap" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Design System Stylesheets -->
    <link rel="stylesheet" href="/app/aleya/visitor/aleya_new/desktop/assets/css/variables.css" />
    <link rel="stylesheet" href="/app/aleya/visitor/aleya_new/desktop/assets/css/global.css" />
    <?php foreach ($stylesheets as $sheet): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($sheet, ENT_QUOTES, 'UTF-8'); ?>" />
    <?php endforeach; ?>

    <!-- Schema.org JSON-LD Structured Data -->
    <?php if ($schemaData): ?>
        <script type="application/ld+json">
            <?php echo json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
        </script>
    <?php endif; ?>
    <?php
}
