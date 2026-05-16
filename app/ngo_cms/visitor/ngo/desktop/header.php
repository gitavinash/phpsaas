<?php
require_once MENU_FUNCTIONS;

if (defined('SETTING_COMPANY_PROFILE')) {
    $companyProfile = json_decode(SETTING_COMPANY_PROFILE, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($companyProfile)) {
        extract($companyProfile, EXTR_SKIP);
    }
}
?>
<!doctype html>
<html lang="<?php echo SITE_LANG ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap"
        rel="stylesheet" />
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css" />
    <?php require_once DEFAULT_META; ?>
</head>

<body>
    <div id="header-placeholder">
        <nav class="navbar">
            <div class="container nav-container">
                <a href="/" class="brand-logo"
                    style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                    <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>" class="logo-img">
                </a>

                <div class="nav-right">
                    <ul class="nav-menu" id="nav-menu">
                        <?php
                        generateMenu('SITE_NAV_MAIN_HEADER', 'dropdown', true, 'nav-link');
                        ?>
                    </ul>
                    <?php
                    generateMenu('SITE_NAV_HEADER_MENU_CTA', 'non_list', true, 'btn btn-secondary nav-btn');
                    ?>

                    <div class="mobile-toggle" id="mobile-toggle">
                        <i class="fas fa-bars"></i>
                    </div>
                </div>
            </div>
        </nav>
    </div>