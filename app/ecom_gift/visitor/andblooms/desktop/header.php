<?php

require_once MENU_FUNCTIONS;

/**
 * Renders the mobile menu tree recursively with a beautiful luxury design.
 */
function render_andblooms_mobile_menu_tree(array $items, int $depth = 0): void
{
    foreach ($items as $item) {
        $label = safe_html($item['label'] ?? '');
        $url   = safe_html($item['url'] ?? '#');
        $children = (isset($item['children']) && is_array($item['children'])) ? $item['children'] : [];
        $children = array_values(array_filter($children, function ($child) {
            return is_array($child) && !empty($child['label']);
        }));

        $isActive = is_menu_active($url);
        $hasChildren = !empty($children);

        if ($hasChildren) {
            echo '<div class="border-b border-gray-100/60 py-0.5">';
            echo '<details class="group">';
            echo '<summary class="flex items-center justify-between px-6 py-3 font-medium text-gray-800 hover:text-primary transition font-sans text-base cursor-pointer list-none">';
            echo '<span>' . $label . '</span>';
            echo '<i class="ri-arrow-down-s-line text-xl transition-transform duration-300 group-open:rotate-180 text-gray-400"></i>';
            echo '</summary>';
            echo '<ul class="pl-6 pr-6 pb-3 space-y-2 border-l border-primary/20 ml-8 my-1">';
            render_andblooms_mobile_menu_tree($children, $depth + 1);
            echo '</ul>';
            echo '</details>';
            echo '</div>';
        } else {
            echo '<div class="border-b border-gray-100/60 py-0.5">';
            echo '<a href="' . $url . '" class="block px-6 py-3 font-medium text-gray-700 hover:text-primary transition font-sans text-base' . ($isActive ? ' text-primary font-semibold bg-primary/5 pl-5 border-l-4 border-primary' : '') . '">';
            echo $label;
            echo '</a>';
            echo '</div>';
        }
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
                    primary: '#D4946D',
                    secondary: '#F8F5F2'
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
                    <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?> Logo" class="h-8">
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
                            class="pl-10 pr-4 py-2 rounded-full border border-gray-200 focus:outline-none focus:border-primary w-64 text-sm"
                            style="width: 100%">
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
                <div class="relative cart-dropdown-trigger group">
                    <a href="/checkout/"
                        class="w-10 h-10 flex items-center justify-center text-gray-700 hover:text-primary transition">
                        <i class="ri-shopping-bag-line text-xl"></i>
                    </a>
                    <?php if ($cartItems > 0) { ?>
                    <span id="cartItems"
                        class="absolute -top-1 -right-1 bg-primary text-white text-xs w-5 h-5 flex items-center justify-center rounded-full font-medium"><?php echo $cartItems ?></span>

                    <!-- Cart Dropdown Preview -->
                    <div
                        class="absolute right-0 top-full mt-3 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-y-0 translate-y-2 z-50">
                        <!-- Header -->
                        <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-primary/5 to-transparent">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-800 flex items-center">
                                    <i class="ri-shopping-bag-3-line text-primary mr-2"></i>
                                    Your Cart
                                </h3>
                                <span
                                    class="bg-primary text-white text-xs px-2 py-1 rounded-full"><?php echo $cartItems ?>
                                    item<?php echo $cartItems > 1 ? 's' : '' ?></span>
                            </div>
                        </div>

                        <!-- Quick Message -->
                        <div class="p-4 text-center bg-secondary/50">
                            <p class="text-sm text-gray-600">
                                <i class="ri-checkbox-circle-line text-green-500 mr-1"></i>
                                You have <strong class="text-primary"><?php echo $cartItems ?></strong>
                                item<?php echo $cartItems > 1 ? 's' : '' ?> in your cart
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="p-4 space-y-2">
                            <a href="/checkout/"
                                class="block w-full bg-primary text-white text-center py-3 rounded-lg font-medium hover:bg-opacity-90 transition shadow-sm">
                                <i class="ri-shopping-cart-2-line mr-2"></i>
                                View Cart & Checkout
                            </a>
                            <button onclick="this.closest('.cart-dropdown-trigger').querySelector('a').click()"
                                class="block w-full bg-gray-100 text-gray-700 text-center py-2.5 rounded-lg font-medium hover:bg-gray-200 transition text-sm">
                                Continue Shopping
                            </button>
                        </div>

                        <!-- Footer Note -->
                        <div class="px-4 pb-4 text-center">
                            <p class="text-xs text-gray-500">
                                <i class="ri-secure-payment-line text-primary"></i>
                                Secure checkout • Same day delivery
                            </p>
                        </div>
                    </div>
                    <?php } else { ?>
                    <span id="cartItems"
                        class="absolute -top-1 -right-1 bg-gray-300 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">0</span>
                    <?php } ?>
                </div>

                <!-- Hamburger -->
                <button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700"
                    id="mobile-menu-button">
                    <i class="ri-menu-line text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu full screen drawer -->
        <div class="md:hidden fixed inset-0 bg-white z-[60] transition-transform duration-300 ease-in-out flex flex-col"
            id="mobile-menu" style="transform: translateX(-100%);">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div class="flex flex-col">
                    <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?> Logo" class="h-6">
                </div>
                <div class="flex items-center space-x-2">
                    <button id="close-mobile-menu"
                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-primary hover:bg-primary/5 rounded-full transition-all duration-300 focus:outline-none">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Search Bar under Header -->
            <div class="px-6 py-4 bg-secondary/30 border-b border-gray-100">
                <form action="/search/" method="get" class="relative">
                    <input type="text" name="q" id="mobile-search-input" placeholder="Search for luxury flowers..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-full border border-gray-200 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm transition bg-white font-sans text-gray-800 placeholder-gray-400">
                    <button type="submit"
                        class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary transition focus:outline-none">
                        <i class="ri-search-line text-base"></i>
                    </button>
                </form>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto flex-grow flex flex-col justify-between">
                <!-- Dynamic Navigation Links -->
                <nav class="flex flex-col py-2">
                    <?php
                    $menuItems = get_object_from_constant('SITE_NAV_MAIN_HEADER');
                    if (!empty($menuItems)) {
                        render_andblooms_mobile_menu_tree($menuItems);
                    } else {
                        echo '<a href="/" class="block px-6 py-3 text-gray-700 hover:text-primary transition font-sans text-base">Home</a>';
                    }
                    ?>
                </nav>

            </div>
        </div>

        <!-- Mobile Menu Overlay Backdrop -->
        <div class="md:hidden fixed inset-0 z-50 transition-all duration-300 ease-in-out pointer-events-none bg-black/0"
            id="mobile-menu-overlay"></div>
    </header>

    <style>
    #mobile-menu-overlay {
        transition: background-color 300ms ease-in-out, backdrop-filter 300ms ease-in-out, -webkit-backdrop-filter 300ms ease-in-out;
    }

    summary::-webkit-details-marker {
        display: none;
    }

    summary {
        list-style: none;
    }
    </style>

    <script>
    // Initialize mobile menu
    (function() {
        let menuOpen = false;

        function initMobileMenu() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeMobileMenuButton = document.getElementById('close-mobile-menu');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

            if (!mobileMenuButton || !mobileMenu || !mobileMenuOverlay) {
                console.error('Menu elements not found');
                return;
            }

            function openMenu(e) {
                if (e) e.preventDefault();
                if (menuOpen) return;

                console.log('Opening menu');
                menuOpen = true;

                // Force reflow to ensure transition works
                mobileMenu.offsetHeight;

                mobileMenu.style.transform = 'translateX(0)';

                // Overlay enhancements
                mobileMenuOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.4)';
                mobileMenuOverlay.style.backdropFilter = 'blur(4px)';
                mobileMenuOverlay.style.webkitBackdropFilter = 'blur(4px)';
                mobileMenuOverlay.style.pointerEvents = 'auto';
                document.body.style.overflow = 'hidden';
            }

            function closeMenu(e) {
                if (e) e.preventDefault();
                if (!menuOpen) return;

                console.log('Closing menu');
                menuOpen = false;

                mobileMenu.style.transform = 'translateX(-100%)';

                mobileMenuOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0)';
                mobileMenuOverlay.style.backdropFilter = 'none';
                mobileMenuOverlay.style.webkitBackdropFilter = 'none';
                mobileMenuOverlay.style.pointerEvents = 'none';
                document.body.style.overflow = '';

                // Close all dynamic details accordions when closing menu
                document.querySelectorAll('#mobile-menu details').forEach(details => {
                    details.removeAttribute('open');
                });
            }

            // Attach event listeners
            mobileMenuButton.addEventListener('click', openMenu);
            closeMobileMenuButton.addEventListener('click', closeMenu);
            mobileMenuOverlay.addEventListener('click', closeMenu);

            // Close menu on link click (only direct navigation links, not accordion summaries)
            document.querySelectorAll('#mobile-menu a').forEach(link => {
                link.addEventListener('click', function() {
                    closeMenu();
                });
            });

            // Handle search input focus - scroll into view when keyboard appears
            const mobileSearchInput = document.getElementById('mobile-search-input');
            if (mobileSearchInput) {
                mobileSearchInput.addEventListener('focus', function() {
                    setTimeout(() => {
                        this.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 300); // Delay to allow keyboard to appear
                });
            }
        }

        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMobileMenu);
        } else {
            initMobileMenu();
        }
    })();
    </script>