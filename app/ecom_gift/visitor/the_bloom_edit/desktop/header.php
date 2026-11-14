<?php

require_once MENU_FUNCTIONS;
require_once DATA_FUNCTIONS;
require_once __DIR__ . '/partials/product-card.php';

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
                        primary: '#9B5637',
                        secondary: '#EFE8DC',
                        ivory: '#FAF7F2',
                        champagne: '#EFE8DC',
                        blush: '#F4E9E4',
                        forest: '#183028',
                        charcoal: '#1C1A18',
                        taupe: '#5E5650',
                        gold: '#B48E54'
                    },
                    borderRadius: {
                        'none': '0px',
                        'sm': '4px',
                        DEFAULT: '6px',
                        'md': '10px',
                        'lg': '12px',
                        'xl': '16px',
                        '2xl': '24px',
                        '3xl': '32px',
                        'full': '9999px',
                        'button': '6px'
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
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css?v=<?php echo assetVersion('css/style.min.css'); ?>">
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
    <noscript><img height="1" width="1" hidden alt=""
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

<body class="bg-[#FAF7F2] text-[#1C1A18] antialiased">
    <a href="#main-content" class="tbe-skip-link">Skip to content</a>

    <!-- Header -->
    <header id="site-header" class="site-header sticky top-0 z-[55] header-transparent">
        <div class="tbe-announcement-bar" role="region" aria-label="Atelier service highlights">
            <div class="tbe-announcement-inner">
                <span class="hidden md:inline">Chhatarpur &amp; Gurugram Floral Ateliers</span>
                <span class="tbe-announcement-sep hidden md:inline" aria-hidden="true">✦</span>
                <span>Same-Day Handcrafted Delivery across Delhi NCR</span>
                <span class="tbe-announcement-sep hidden sm:inline" aria-hidden="true">✦</span>
                <a href="tel:+919910744119" class="hidden sm:inline underline decoration-[#B48E54]/70 underline-offset-4 hover:text-[#EFE8DC] transition-colors">
                    Concierge: +91-9910744119
                </a>
            </div>
        </div>

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
                        <input type="search" name="q" placeholder="Search creations..." aria-label="Search products"
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
                $cartItems = 0;
                $cartCountQuery = "SELECT COALESCE(SUM(GREATEST(c.cart_qty, 1)), 0) AS total_items
                    FROM " . TABLE_PREFIX . "cart c
                    LEFT JOIN " . TABLE_PREFIX . "product p
                        ON (c.cart_relation_id = p.product_id AND (c.cart_relation_type = 'product' OR c.cart_relation_type = '' OR c.cart_relation_type IS NULL))
                    LEFT JOIN " . TABLE_PREFIX . "event e
                        ON (c.cart_relation_id = e.event_id AND c.cart_relation_type = 'event')
                    WHERE c.cart_session_id = '" . SESSION_ID . "'
                      AND c.cart_site_id = " . SITE_ID . "
                      AND (c.cart_status = 0 OR c.cart_status = 2)
                      AND (
                          ((c.cart_relation_type = 'product' OR c.cart_relation_type = '' OR c.cart_relation_type IS NULL) AND (p.product_status = 1 OR p.product_status IS NULL))
                          OR (c.cart_relation_type = 'event' AND (e.event_status = 1 OR e.event_status IS NULL))
                          OR (c.cart_relation_type NOT IN ('product', 'event') AND c.cart_relation_type != '')
                      )";
                if ($cartNumber = mysqli_query($connection, $cartCountQuery)) {
                    $cartCountRow = mysqli_fetch_assoc($cartNumber);
                    $cartItems = (int) ($cartCountRow['total_items'] ?? 0);
                }
                if (isset($totalBagUnits) && (int) $totalBagUnits > 0) {
                    $cartItems = max($cartItems, (int) $totalBagUnits);
                }
                ?>
                <div class="relative cart-dropdown-trigger group">
                    <a href="/checkout/" aria-label="Shopping bag, <?php echo (int) $cartItems; ?> item<?php echo $cartItems === 1 ? '' : 's'; ?>"
                        class="site-action-btn relative">
                        <i class="ri-shopping-bag-line text-xl" aria-hidden="true"></i>
                    </a>
                    <?php if ($cartItems > 0) { ?>
                        <span id="cartItems"
                            class="absolute -top-0.5 -right-0.5 bg-[#183028] text-white text-[11px] w-5 h-5 flex items-center justify-center rounded-full font-semibold pointer-events-none"><?php echo $cartItems ?></span>
                    <?php } else { ?>
                        <span id="cartItems"
                            class="absolute -top-0.5 -right-0.5 bg-[#EFE8DC] text-[#5E5650] border border-[#D4C8BA] text-[11px] w-5 h-5 flex items-center justify-center rounded-full font-semibold pointer-events-none">0</span>
                    <?php } ?>

                    <!-- Editorial Cart Preview (Hover & Keyboard Focus) -->
                    <div id="tbeCartDropdown" class="tbe-cart-dropdown" aria-label="Shopping bag summary">
                        <div class="px-4 py-3.5 border-b border-[#E6DDD3] bg-[#FAF7F2] flex items-center justify-between">
                            <p class="font-serif text-base font-medium text-[#1C1A18] flex items-center gap-2">
                                <i class="ri-shopping-bag-3-line text-[#9B5637]" aria-hidden="true"></i>
                                <span>Your Floral Bag</span>
                            </p>
                            <span class="tbe-cart-count-pill bg-[#183028] text-white text-xs px-2.5 py-0.5 rounded-full font-medium">
                                <?php echo $cartItems ?> item<?php echo $cartItems === 1 ? '' : 's' ?>
                            </span>
                        </div>

                        <div id="tbeCartLastAdded" class="hidden px-4 pt-3.5 pb-1 bg-white">
                            <div class="flex items-center gap-3 p-2.5 rounded-lg bg-[#FAF7F2] border border-[#E6DDD3]">
                                <img id="tbeCartLastImg" src="" alt="" class="w-11 h-11 rounded object-cover flex-shrink-0 bg-[#EFE8DC] hidden">
                                <div class="min-w-0 flex-1 text-left">
                                    <p class="text-[10px] uppercase tracking-wider font-semibold text-[#9B5637]">Just Added</p>
                                    <p id="tbeCartLastTitle" class="text-xs font-medium text-[#1C1A18] truncate"></p>
                                    <p id="tbeCartLastMeta" class="text-[11px] text-[#5E5650]"></p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 text-center bg-white">
                            <p id="tbeCartDropdownStatus" class="text-sm text-[#5E5650]">
                                <?php if ($cartItems > 0) { ?>
                                    <i class="ri-checkbox-circle-line text-[#183028] mr-1" aria-hidden="true"></i>
                                    You have <strong class="text-[#1C1A18] font-semibold"><?php echo $cartItems ?></strong>
                                    curated item<?php echo $cartItems === 1 ? '' : 's' ?> ready for checkout.
                                <?php } else { ?>
                                    Your bespoke floral bag is currently empty.
                                <?php } ?>
                            </p>
                        </div>

                        <div id="tbeCartDropdownActions" class="px-4 pb-4 space-y-2 bg-white">
                            <a href="/checkout/" data-cart-checkout-cta
                                class="tbe-btn-forest w-full text-center <?php echo $cartItems > 0 ? '' : 'hidden'; ?>">
                                <i class="ri-shopping-cart-2-line" aria-hidden="true"></i>
                                <span>View Bag &amp; Checkout</span>
                            </a>
                            <a href="/shop/" data-cart-shop-cta
                                class="<?php echo $cartItems > 0 ? 'tbe-btn-outline text-xs' : 'tbe-btn-forest'; ?> w-full text-center">
                                <span><?php echo $cartItems > 0 ? 'Continue Shopping' : 'Explore Creations'; ?></span>
                            </a>
                        </div>

                        <div class="px-4 py-2.5 bg-[#FAF7F2] border-t border-[#E6DDD3] text-center">
                            <p class="text-xs text-[#5E5650]">
                                <i class="ri-shield-check-line text-[#9B5637] mr-1" aria-hidden="true"></i>
                                Encrypted checkout • Same-day Delhi NCR delivery
                            </p>
                        </div>
                    </div>
                    <div id="tbeCartLiveRegion" class="visually-hidden" aria-live="polite" aria-atomic="true"></div>
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
    <main id="main-content" tabindex="-1" class="tbe-main-shell">
