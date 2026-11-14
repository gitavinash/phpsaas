<?php

require_once MENU_FUNCTIONS;
require_once DATA_FUNCTIONS;
require_once __DIR__ . '/partials/product-card.php';

if (!function_exists('safe_html')) {
    function safe_html(?string $str): string
    {
        return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Checks whether any descendant menu item matches the active route.
 */
function veloura_menu_branch_has_active(array $items): bool
{
    foreach ($items as $item) {
        if (!is_array($item) || empty($item['label'])) {
            continue;
        }
        $url = (string) ($item['url'] ?? '');
        if ($url !== '' && $url !== '#' && is_menu_active($url)) {
            return true;
        }
        if (!empty($item['children']) && is_array($item['children']) && veloura_menu_branch_has_active($item['children'])) {
            return true;
        }
    }
    return false;
}

/**
 * Renders the unified semantic navigation tree for Veloura.
 */
function render_veloura_nav_tree(array $items, int $depth = 0, string $idPrefix = 'site-nav'): void
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
                || veloura_menu_branch_has_active($children);

            if ($depth === 0) {
                $colsClass = count($children) > 7 ? ' site-dropdown-list--columns' : '';
                echo '<li class="site-nav-dropdown">';
                echo '<button type="button" class="site-nav-link site-dropdown-trigger" aria-expanded="false" aria-controls="' . $subId . '"' . ($isBranchActive ? ' data-active-branch="true"' : '') . '>';
                echo '<span>' . $label . '</span>';
                echo '<i class="ri-arrow-down-s-line site-nav-chevron text-xs opacity-60 ml-1" aria-hidden="true"></i>';
                echo '</button>';
                echo '<ul id="' . $subId . '" class="site-dropdown-list' . $colsClass . '">';
                render_veloura_nav_tree($children, $depth + 1, $subId);
                echo '</ul>';
                echo '</li>';
            } else {
                echo '<li class="site-subdropdown">';
                echo '<button type="button" class="site-dropdown-link site-subdropdown-trigger" aria-expanded="false" aria-controls="' . $subId . '"' . ($isBranchActive ? ' data-active-branch="true"' : '') . '>';
                echo '<span>' . $label . '</span>';
                echo '<i class="ri-arrow-down-s-line site-nav-chevron text-xs opacity-60 ml-1" aria-hidden="true"></i>';
                echo '</button>';
                echo '<ul id="' . $subId . '" class="site-subdropdown-list">';
                render_veloura_nav_tree($children, $depth + 1, $subId);
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
                        primary: '#C47C68',
                        terracotta: '#C47C68',
                        sage: '#3D5447',
                        porcelain: '#FAF7F5',
                        charcoal: '#242220'
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', '"Cormorant Garamond"', 'Georgia', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_ICON ?>">
    <!-- Google Fonts: Playfair Display, Cormorant Garamond, Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>select2/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>header-nav.css?v=<?php echo time(); ?>">
    <?php require_once DEFAULT_META; ?>

    <!-- Global site tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-69G2SPCWHX"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-69G2SPCWHX');
    </script>
    <?php require_once GOOGLE_RECAPTCHA_GENERATOR; ?>
</head>

<body class="bg-[#FAF7F5] text-[#242220] antialiased">
    <a href="#main-content" class="vl-skip-link">Skip to content</a>

    <!-- Top Announcement Bar (Fiorello Style) -->
    <div class="vl-announcement-bar" role="region" aria-label="Atelier services">
        <div class="vl-announcement-inner">
            <span>✦ Same-Day Handcrafted Delivery Across Delhi NCR</span>
            <span class="vl-announcement-sep hidden sm:inline" aria-hidden="true">✦</span>
            <span class="hidden sm:inline">Complimentary Calligraphy Card with Every Bouquet</span>
            <span class="vl-announcement-sep hidden md:inline" aria-hidden="true">✦</span>
            <a href="tel:+919910744119" class="hover:text-[#D9BA8F] transition-colors underline underline-offset-4">
                Atelier Concierge: +91-9910744119
            </a>
        </div>
    </div>

    <!-- Header -->
    <header id="site-header" class="site-header sticky top-0 z-[55] header-transparent bg-white/95 border-b border-[#E8DDD5] shadow-sm">
        <div id="site-header-inner" class="site-header-inner">
            <!-- Brand Logo -->
            <a href="/" class="site-logo group flex items-center gap-2">
                <span class="font-serif text-2xl md:text-3xl font-medium tracking-[0.08em] text-[#242220] group-hover:text-[#C47C68] transition-colors">
                    VELOURA
                </span>
                <span class="hidden lg:inline-block text-[9px] uppercase tracking-[0.25em] text-[#8E8780] border-l border-[#D5C4B7] pl-2 font-sans font-medium">
                    Haute Botanicals
                </span>
            </a>

            <!-- Navigation Tree -->
            <nav class="site-nav" aria-label="Primary">
                <ul id="site-menu" class="site-menu" popover="auto">
                    <?php
                    $menuItems = get_object_from_constant('SITE_NAV_MAIN_HEADER');
                    if (!empty($menuItems)) {
                        render_veloura_nav_tree($menuItems);
                    } else {
                        $isHomeActive = is_menu_active('/');
                        echo '<li><a href="/" class="site-nav-link"' . ($isHomeActive ? ' aria-current="page"' : '') . '>Home</a></li>';
                    }
                    ?>
                </ul>
            </nav>

            <!-- Right Header Actions (Search, Phone, Cart) -->
            <div class="site-actions">
                <!-- Search Button -->
                <button type="button" id="header-search-button" class="site-action-btn"
                    popovertarget="header-search-popover" popovertargetaction="toggle"
                    aria-controls="header-search-popover" aria-expanded="false" aria-label="Search creations">
                    <i class="ri-search-line text-xl" aria-hidden="true"></i>
                </button>

                <!-- Concierge / Contact Link -->
                <a href="/contact-us/" class="site-action-btn hidden sm:inline-flex" aria-label="Contact atelier">
                    <i class="ri-customer-service-2-line text-xl" aria-hidden="true"></i>
                </a>

                <!-- Cart Icon with Synchronized Count & Dropdown Preview -->
                <?php
                $cartCountSql = "
                    SELECT COALESCE(SUM(GREATEST(c.cart_qty, 1)), 0) AS total_items
                    FROM " . TABLE_PREFIX . "cart c
                    LEFT JOIN " . TABLE_PREFIX . "product p 
                        ON c.cart_relation_id = p.product_id AND (c.cart_relation_type = 'product' OR c.cart_relation_type = '' OR c.cart_relation_type IS NULL)
                    LEFT JOIN " . TABLE_PREFIX . "event e 
                        ON c.cart_relation_id = e.event_id AND c.cart_relation_type = 'event'
                    WHERE c.cart_session_id = '" . SESSION_ID . "'
                      AND c.cart_site_id = " . SITE_ID . "
                      AND (c.cart_status = 0 OR c.cart_status = 2)
                      AND (
                          ((c.cart_relation_type = 'product' OR c.cart_relation_type = '' OR c.cart_relation_type IS NULL) AND (p.product_status = 1 OR p.product_status IS NULL))
                          OR (c.cart_relation_type = 'event' AND (e.event_status = 1 OR e.event_status IS NULL))
                          OR (c.cart_relation_type NOT IN ('product', 'event') AND c.cart_relation_type != '')
                      )";
                $cartCountRes = mysqli_query($connection, $cartCountSql);
                $cartItems = 0;
                if ($cartCountRes && $cartRow = mysqli_fetch_assoc($cartCountRes)) {
                    $cartItems = (int) ($cartRow['total_items'] ?? 0);
                }
                if (isset($totalBagUnits) && (int) $totalBagUnits > 0) {
                    $cartItems = max($cartItems, (int) $totalBagUnits);
                }
                ?>
                <div class="relative cart-dropdown-trigger group">
                    <a href="/checkout/" aria-label="Floral bag, <?php echo $cartItems; ?> items"
                        class="site-action-btn relative">
                        <i class="ri-shopping-bag-3-line text-xl text-[#242220]" aria-hidden="true"></i>
                        <span id="cartItems"
                            class="absolute -top-1 -right-1 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full font-bold transition-all <?php echo $cartItems > 0 ? 'bg-[#C47C68]' : 'bg-[#D5C4B7] text-[#635E59]'; ?>">
                            <?php echo $cartItems; ?>
                        </span>
                    </a>

                    <!-- Cart Dropdown Menu -->
                    <div class="cart-dropdown-menu">
                        <div class="p-4 border-b border-[#E8DDD5] bg-[#FAF7F5] flex items-center justify-between">
                            <span class="font-serif font-medium text-base text-[#242220]">Your Floral Bag</span>
                            <span class="vl-badge vl-badge-terracotta"><?php echo $cartItems; ?> Item<?php echo $cartItems === 1 ? '' : 's'; ?></span>
                        </div>
                        <div class="p-5 text-center bg-white">
                            <?php if ($cartItems > 0): ?>
                                <p id="vlCartDropdownStatus" class="text-sm text-[#635E59] mb-4">
                                    <i class="ri-checkbox-circle-line text-[#3D5447] mr-1" aria-hidden="true"></i>
                                    You have <strong><?php echo $cartItems; ?></strong> curated items ready for atelier delivery.
                                </p>
                                <a href="/checkout/" class="vl-btn-primary w-full text-center mb-2">
                                    <span>Proceed to Checkout</span>
                                </a>
                                <a href="/shop/" class="vl-btn-outline w-full text-center text-xs py-2">
                                    <span>Continue Exploring</span>
                                </a>
                            <?php else: ?>
                                <p id="vlCartDropdownStatus" class="text-sm text-[#635E59] mb-4">
                                    Your bespoke floral bag is currently empty.
                                </p>
                                <a href="/shop/" class="vl-btn-primary w-full text-center">
                                    <span>Explore Creations</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" id="mobile-menu-button" class="site-action-btn lg:hidden"
                    popovertarget="site-menu" popovertargetaction="toggle"
                    aria-controls="site-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="ri-menu-line text-2xl text-[#242220]" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <!-- Search Popover -->
        <div id="header-search-popover" class="header-search-popover max-w-xl mx-auto p-4 bg-white border border-[#E8DDD5] rounded-xl shadow-lg mt-2" popover="auto">
            <form action="/search/" method="get" role="search" class="flex gap-2">
                <input type="search" name="q" placeholder="Search roses, orchids, hampers, planters..."
                    class="vl-input flex-1" aria-label="Search catalog">
                <button type="submit" class="vl-btn-primary">Search</button>
            </form>
        </div>
    </header>

    <main id="main-content" tabindex="-1">
