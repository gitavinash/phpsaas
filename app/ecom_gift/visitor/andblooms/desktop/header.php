<?php

require_once MENU_FUNCTIONS;
require_once DATA_FUNCTIONS;

/**
 * Checks whether any descendant menu item matches the active route.
 */
function andblooms_menu_branch_has_active(array $items): bool
{
    foreach ($items as $item) {
        if (!is_array($item) || empty($item['label'])) {
            continue;
        }
        $url = (string) ($item['url'] ?? '');
        if ($url !== '' && $url !== '#' && is_menu_active($url)) {
            return true;
        }
        if (!empty($item['children']) && is_array($item['children']) && andblooms_menu_branch_has_active($item['children'])) {
            return true;
        }
    }
    return false;
}

/**
 * Renders the unified semantic navigation tree used across both wide and collapsed viewports.
 */
function render_andblooms_nav_tree(array $items, int $depth = 0, string $idPrefix = 'site-nav'): void
{
    static $uidCounter = 0;

    foreach ($items as $item) {
        if (!is_array($item) || empty($item['label'])) {
            continue;
        }

        $rawLabel = (string) $item['label'];
        $rawUrl   = (string) ($item['url'] ?? '#');
        $label    = safe_html($rawLabel);
        $url      = safe_html($rawUrl !== '' ? $rawUrl : '#');

        $children = (isset($item['children']) && is_array($item['children'])) ? $item['children'] : [];
        $children = array_values(array_filter($children, function ($child) {
            return is_array($child) && !empty($child['label']);
        }));

        if (!empty($children)) {
            if ($rawUrl !== '' && $rawUrl !== '#') {
                $normalizedParent = trim((string) parse_url($rawUrl, PHP_URL_PATH), '/');
                $hasParentTarget = false;
                foreach ($children as $child) {
                    $childUrl = (string) ($child['url'] ?? '');
                    if ($childUrl === $rawUrl || ($normalizedParent !== '' && trim((string) parse_url($childUrl, PHP_URL_PATH), '/') === $normalizedParent)) {
                        $hasParentTarget = true;
                        break;
                    }
                }
                if (!$hasParentTarget) {
                    array_unshift($children, [
                        'label'    => 'All ' . $rawLabel,
                        'url'      => $rawUrl,
                        'children' => [],
                    ]);
                }
            }

            $uidCounter++;
            $subId = $idPrefix . '-sub-' . $uidCounter;
            $isBranchActive = ($rawUrl !== '' && $rawUrl !== '#' && is_menu_active($rawUrl))
                || andblooms_menu_branch_has_active($children);

            if ($depth === 0) {
                $colsClass = count($children) > 7 ? ' site-dropdown-list--columns' : '';
                echo '<li class="site-nav-dropdown">';
                echo '<button type="button" class="site-nav-link site-dropdown-trigger" aria-expanded="false" aria-controls="' . $subId . '" popovertarget="' . $subId . '"' . ($isBranchActive ? ' data-active-branch="true"' : '') . '>';
                echo '<span>' . $label . '</span>';
                echo '<i class="ri-arrow-down-s-line site-nav-chevron" aria-hidden="true"></i>';
                echo '</button>';
                echo '<ul id="' . $subId . '" class="site-dropdown-list' . $colsClass . '">';
                render_andblooms_nav_tree($children, $depth + 1, $subId);
                echo '</ul>';
                echo '</li>';
            } else {
                echo '<li class="site-subdropdown">';
                echo '<button type="button" class="site-dropdown-link site-subdropdown-trigger" aria-expanded="false" aria-controls="' . $subId . '"' . ($isBranchActive ? ' data-active-branch="true"' : '') . '>';
                echo '<span>' . $label . '</span>';
                echo '<i class="ri-arrow-down-s-line site-nav-chevron" aria-hidden="true"></i>';
                echo '</button>';
                echo '<ul id="' . $subId . '" class="site-subdropdown-list">';
                render_andblooms_nav_tree($children, $depth + 1, $subId);
                echo '</ul>';
                echo '</li>';
            }
        } else {
            $isActive = ($rawUrl !== '' && $rawUrl !== '#' && is_menu_active($rawUrl));
            $linkClass = ($depth === 0) ? 'site-nav-link' : 'site-dropdown-link';

            echo '<li>';
            echo '<a href="' . $url . '" class="' . $linkClass . '"' . ($isActive ? ' aria-current="page"' : '') . '>';
            echo $label;
            echo '</a>';
            echo '</li>';
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
    <!-- Social Proof Plugin -->
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>social_proof/social_proof.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>header-nav.css?v=<?php echo assetVersion('css/header-nav.css'); ?>">
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
                <!-- Event snippet for Purchase conversion page -->
<script>
  gtag('event', 'conversion', {
      'send_to': 'AW-628904231/OgMhCLfdtdIBEKei8asC',
      'value': 2000.0,
      'currency': 'INR',
      'transaction_id': ''
      // 'new_customer': true /* calculate dynamically, populate with true/false */,
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
    <header id="site-header" class="site-header fixed inset-x-0 top-0 z-[55] bg-white shadow-sm">
        <div id="site-header-inner" class="site-header-inner">
            <!-- Logo -->
            <a href="/" class="site-logo">
                <img src="<?php echo SITE_LOGO ?>" alt="<?php echo safe_html(SITE_NAME) ?>" class="h-8 w-auto">
            </a>

            <!-- Unified Responsive Navigation (inline on wide viewports, popover menu when collapsed) -->
            <nav class="site-nav" aria-label="Primary">
                <ul id="site-menu" class="site-menu" popover="auto">
                    <?php
                    $menuItems = get_object_from_constant('SITE_NAV_MAIN_HEADER');
                    if (!empty($menuItems)) {
                        render_andblooms_nav_tree($menuItems);
                    } else {
                        $isHomeActive = is_menu_active('/');
                        echo '<li><a href="/" class="site-nav-link"' . ($isHomeActive ? ' aria-current="page"' : '') . '>Home</a></li>';
                    }
                    ?>
                </ul>
            </nav>

            <!-- Right Actions -->
            <div class="site-actions">
                <!-- Wide Desktop Search Input -->
                <div class="site-search-desktop">
                    <form action="/search/" method="get" role="search" class="site-search-form">
                        <button type="submit" class="site-search-icon-btn" aria-label="Search">
                            <i class="ri-search-line" aria-hidden="true"></i>
                        </button>
                        <input type="search" name="q" placeholder="Search..." aria-label="Search products"
                            class="site-search-input">
                    </form>
                </div>

                <!-- Compact / Mobile Search Icon Toggle (opens search bar popover on click) -->
                <button type="button" id="header-search-button" class="site-action-btn header-search-button"
                    popovertarget="header-search-popover" popovertargetaction="toggle"
                    aria-controls="header-search-popover" aria-expanded="false" aria-label="Search products">
                    <i class="ri-search-line text-xl" aria-hidden="true"></i>
                </button>

                <!-- Cart -->
                <?php
                if ($cartNumber = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "product ON cart_relation_id=product_id WHERE cart_session_id='" . SESSION_ID . "' AND cart_status=0 AND product_status=1 AND cart_site_id=" . SITE_ID)) {
                    $cartItems = mysqli_num_rows($cartNumber);
                } else {
                    $cartItems = 0;
                }
                ?>
                <div class="relative cart-dropdown-trigger group">
                    <a href="/checkout/" aria-label="Shopping cart"
                        class="site-action-btn relative">
                        <i class="ri-shopping-bag-line text-xl" aria-hidden="true"></i>
                    </a>
                    <?php if ($cartItems > 0) { ?>
                        <span id="cartItems"
                            class="absolute -top-0.5 -right-0.5 bg-primary text-white text-xs w-5 h-5 flex items-center justify-center rounded-full font-medium pointer-events-none"><?php echo $cartItems ?></span>

                        <!-- Cart Dropdown Preview -->
                        <div
                            class="absolute right-0 top-full mt-3 w-80 max-w-[calc(100dvw-1.5rem)] bg-white rounded-xl shadow-2xl border border-gray-100 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-y-0 translate-y-2 z-50">
                            <!-- Header -->
                            <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-primary/5 to-transparent">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-800 flex items-center">
                                        <i class="ri-shopping-bag-3-line text-primary mr-2" aria-hidden="true"></i>
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
                                    <i class="ri-checkbox-circle-line text-green-500 mr-1" aria-hidden="true"></i>
                                    You have <strong class="text-primary"><?php echo $cartItems ?></strong>
                                    item<?php echo $cartItems > 1 ? 's' : '' ?> in your cart
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="p-4 space-y-2">
                                <a href="/checkout/"
                                    class="block w-full bg-primary text-white text-center py-3 rounded-lg font-medium hover:bg-opacity-90 transition shadow-sm">
                                    <i class="ri-shopping-cart-2-line mr-2" aria-hidden="true"></i>
                                    View Cart &amp; Checkout
                                </a>
                                <button type="button" data-cart-continue
                                    class="block w-full bg-gray-100 text-gray-700 text-center py-2.5 rounded-lg font-medium hover:bg-gray-200 transition text-sm">
                                    Continue Shopping
                                </button>
                            </div>

                            <!-- Footer Note -->
                            <div class="px-4 pb-4 text-center">
                                <p class="text-xs text-gray-500">
                                    <i class="ri-secure-payment-line text-primary" aria-hidden="true"></i>
                                    Secure checkout • Same day delivery
                                </p>
                            </div>
                        </div>
                    <?php } else { ?>
                        <span id="cartItems"
                            class="absolute -top-0.5 -right-0.5 bg-gray-300 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full pointer-events-none">0</span>
                    <?php } ?>
                </div>

                <!-- Menu Trigger (visible whenever inline row does not fit) -->
                <button type="button" id="mobile-menu-button" class="site-action-btn menu-button"
                    popovertarget="site-menu" popovertargetaction="toggle"
                    aria-controls="site-menu" aria-expanded="false" aria-label="Toggle navigation menu">
                    <i class="ri-menu-line text-xl" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <!-- Search Popover (mobile & compact-search widths) -->
        <div id="header-search-popover" class="header-search-popover" popover="auto">
            <form action="/search/" method="get" role="search" class="site-search-form">
                <button type="submit" class="site-search-icon-btn" aria-label="Search">
                    <i class="ri-search-line" aria-hidden="true"></i>
                </button>
                <input type="search" name="q" id="header-search-popover-input" placeholder="Search for luxury flowers..."
                    aria-label="Search products" class="site-search-input">
                <button type="submit" class="site-search-submit">Search</button>
            </form>
        </div>
    </header>
    <script src="<?php echo USER_JS ?>header-nav.js?v=<?php echo assetVersion('js/header-nav.js'); ?>"></script>