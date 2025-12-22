<?php

require_once MENU_FUNCTIONS;

if (defined('SETTING_COMPANY_PROFILE')) {
    $companyProfile = json_decode(SETTING_COMPANY_PROFILE, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($companyProfile)) {
        extract($companyProfile, EXTR_SKIP);
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '<?php echo SITE_THEME_COLOUR ?>',
                    secondary: '<?php echo SITE_BACKGROUND_COLOUR ?>'
                },
                borderRadius: {
                    'none': '0px',
                    'sm': '4px',
                    DEFAULT: '8px',
                    'md': '12px',
                    'lg': '16px',
                    'xl': '20px',
                    '2xl': '24px',
                    '3xl': '32px',
                    'full': '9999px',
                    'button': '8px'
                }
            }
        }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_ICON ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Montserrat:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>select2/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <?php require_once DEFAULT_META; ?>
    <meta name="google-site-verification" content="HT7OlsigSJmSLCG9PCz7JmCbyRGmzUeJapljsV4KRIg" />

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-69G2SPCWHX"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'G-69G2SPCWHX');
    </script>

    <!-- Global site tag (gtag.js) - Google Ads: 628904231 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-628904231"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'AW-628904231');
    </script>

    <script type="text/javascript">
    (function(c, l, a, r, i, t, y) {
        c[a] = c[a] || function() {
            (c[a].q = c[a].q || []).push(arguments)
        };
        t = l.createElement(r);
        t.async = 1;
        t.src = "https://www.clarity.ms/tag/" + i;
        y = l.getElementsByTagName(r)[0];
        y.parentNode.insertBefore(t, y);
    })(window, document, "clarity", "script", "dw3u1ggui2");
    </script>

    <?php

    if (BASE_URL == '/thank-you/') {

        if (isset($id)) {

            if ($id != '') {

    ?>

    <script>
    gtag('event', 'conversion', {
        'send_to': 'AW-628904231/OgMhCLfdtdIBEKei8asC',
        'value': 1,
        'currency': 'INR',
        'transaction_id': ''
    });
    </script>
    <?php

            }
        }
    }

    ?>

    <!-- Meta Pixel Code -->
    <script>
    ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '577779820444463');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=577779820444463&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->

    <!-- Google Tag Manager -->
    <script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-K46P78T');
    </script>
    <!-- End Google Tag Manager -->
    <?php require_once GOOGLE_RECAPTCHA_GENERATOR; ?>
</head>

<body class="bg-white">
    <!-- Header -->
    <header class="fixed w-full top-0 z-[55] bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center space-x-8">
                <a href="/" class="flex items-center">
                    <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?> Logo"
                        class="h-<?php echo ((SITE_ID == 376) ? 20 : 8) ?>">
                </a>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-6">
                    <?php
                    generateMenu('SITE_NAV_MAIN_HEADER', 'dropdown', true);
                    ?>
                </nav>
            </div>

            <!-- Right Icons -->
            <div class="flex items-center space-x-4">
                <!-- Desktop Search -->
                <div class="relative hidden md:block">
                    <form action="/search/" method="get">
                        <input type="text" name="q" placeholder="Search..."
                            class="pl-10 pr-4 py-2 rounded-full border border-gray-200 focus:outline-none focus:border-primary w-64 text-sm">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="ri-search-line"></i>
                        </div>
                    </form>
                </div>

                <!-- Cart -->
                <?php
                if ($cartNumber = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "product ON cart_relation_id=product_id WHERE cart_session_id='" . SESSION_ID . "' AND cart_status=0 AND product_status=1 AND cart_site_id=" . SITE_ID)) {
                    $cartItems = mysqli_num_rows($cartNumber);
                } else {
                    $cartItems = 0;
                }
                ?>
                <div class="relative">
                    <a href="/checkout/"
                        class="w-10 h-10 flex items-center justify-center text-gray-700 hover:text-primary">
                        <i class="ri-shopping-bag-line text-xl"></i>
                    </a>
                    <span id="cartItems"
                        class="absolute -top-1 -right-1 bg-primary text-white text-xs w-5 h-5 flex items-center justify-center rounded-full"><?php echo $cartItems ?></span>
                </div>

                <!-- Hamburger -->
                <button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700"
                    id="mobile-menu-button">
                    <i class="ri-menu-line text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden hidden bg-white w-full py-4 px-4 shadow-md" id="mobile-menu">
            <div class="mb-4">
                <div class="relative">
                    <form action="/search/" method="get">
                        <input type="text" name="q" placeholder="Search..."
                            class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-200 focus:outline-none focus:border-primary text-sm">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="ri-search-line"></i>
                        </div>
                    </form>
                </div>
            </div>
            <nav class="flex flex-col space-y-3">
                <?php generateMenu('SITE_NAV_MAIN_HEADER', 'mobile', true); ?>
            </nav>

        </div>
    </header>