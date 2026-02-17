<?php

$assetVersion = static function ($relativePath) {
    $filePath = USER_PATH . 'assets/' . ltrim($relativePath, '/');
    return (string) (@filemtime($filePath) ?: time());
};

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css?v=<?php echo $assetVersion('css/style.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>about.min.css?v=<?php echo $assetVersion('css/about.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>manage.min.css?v=<?php echo $assetVersion('css/manage.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>subscribe.min.css?v=<?php echo $assetVersion('css/subscribe.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>footer.min.css?v=<?php echo $assetVersion('css/footer.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>details.min.css?v=<?php echo $assetVersion('css/details.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>thankyou.css?v=<?php echo $assetVersion('css/thankyou.css'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <?php require_once DEFAULT_META; ?>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <?php

    if (BASE_URL == '/thank-you/') {

        /** Send Email */

        $email = PROFILE_EMAIL;
        require_once APP_EMAIL_TEMPLATE . 'newSubscription.php';

        echo "<meta http-equiv='refresh' content='10; URL=/manage-subscription/'>";
    }

    ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KN6V4VKJN4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-KN6V4VKJN4');
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
        })(window, document, "clarity", "script", "qicb9eq9ym");
    </script>
</head>

<body oncontextmenu="return false;">
    <div class="no-print" style="display: none;">Print is not allowed</div>
    </div>
    <header>
        <div class="myHeader <?php echo (($link_type == 'page') ? 'about_bg' : '') ?>">
            <div class="container">
                <div class="header_flex">
                    <div class="company-logo">
                        <a href="/"><img src="<?php echo SITE_LOGO ?>" class="img-fluid" alt="logo"></a>
                    </div>

                    <!-- Desktop Search Bar (Center) -->
                    <div class="header-search">
                        <input type="text" placeholder="Search articles..." aria-label="Search">
                        <button type="submit" aria-label="Submit search">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" />
                                <path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>

                    <div class="mobile-actions">
                        <button class="mobile-menu-btn" id="mobileMenuButton" aria-controls="siteNav"
                            aria-expanded="false" aria-label="Open menu">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
                                <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                        </button>
                        <div class="search-container">
                            <button class="search-btn" id="searchButton" aria-label="Search">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
                                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6" />
                                    <path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.6"
                                        stroke-linecap="round" />
                                </svg>
                                <span class="btn-text">Search</span>
                            </button>
                            <input type="text" class="search-input" id="searchInput" placeholder="Type to search...">
                            <button class="close-btn" id="closeButton">
                                <i class="icon-close">&times;</i>
                            </button>
                        </div>
                        <?php if (isset($_COOKIE['user_data'])) { ?>
                            <button class="mobile-user-btn user--name" type="button" aria-label="Account">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.6" />
                                    <path d="M4 20c1.8-3.5 5-5 8-5s6.2 1.5 8 5" stroke="currentColor" stroke-width="1.6"
                                        stroke-linecap="round" />
                                </svg>
                            </button>
                            <?php if (isset($showLogoutBtn) && $showLogoutBtn == 1) { ?>
                                <a href="/logout/" class="mobile-auth-link">Logout</a>
                            <?php } ?>
                        <?php } else { ?>
                            <a href="/subscribe/<?php echo ((ACTIVE_URL != '/') ? '?redirect_url=' . ACTIVE_URL : '') ?>"
                                class="mobile-auth-link">Subscribe</a>
                            <a href="/login/<?php echo ((ACTIVE_URL != '/') ? '?redirect_url=' . ACTIVE_URL : '') ?>"><i
                                    class='bx bx-user-circle'></i></a>
                        <?php } ?>
                    </div>
                    <?php

                    if (isset($_COOKIE['user_data'])) { ?>

                        <div class="header_right">
                            <p class="user--name"><i class='bx bx-user-circle'></i><?php echo PROFILE_DISPLAY_NAME ?>
                            </p>
                        </div>

                        <!-- profile side--bar -->
                        <div class="profile--sideBar">
                            <div class="profile_container">
                                <div class="profile_cancel">
                                    <i class='bx bx-x'></i>
                                </div>
                                <div class="profile_header">
                                    <!-- <div class="profile--img">
                                        <img src="<?php echo PROFILE_IMAGE ?>" class="img-fluid" alt="user_img">
                                    </div> -->
                                    <div class="user--content">
                                        <h6><?php echo PROFILE_DISPLAY_NAME ?></h6>
                                        <a href="#"><?php echo PROFILE_EMAIL ?></a>
                                    </div>
                                </div>

                                <?php

                                if ($activeSubscription == true) {

                                    if ($subscription_mode == 'month') {
                                        $increseDuration = "+1 month";
                                    } else {
                                        $increseDuration = "+1 year";
                                    }

                                    $expiryDate = date('d M Y', strtotime($subscription_time . $increseDuration));;

                                    echo "<div class='manage_link'>
                                            <a href='/manage-subscription/'>MANAGE MY SUBSCRIPTION</a>
                                        </div>

                                        <div class='plan_card'>
                                            <h6>YOUR CURRENT PLAN</h6>
                                            <h3>{${$subscription_type . '_title'}}</h3>
                                            <p>Next billing date : {$expiryDate}</p>
                                        </div>";
                                } else {
                                    echo "<div class='manage_link'>
                                            <a href='/subscribe/'>BUY SUBSCRIPTION</a>
                                        </div>";
                                }

                                ?>

                            </div>
                        </div>

                    <?php
                    } else { ?>


                        <div class="header_right">
                            <!-- <button class="subscribe">subscribe</button> -->
                            <a href="/subscribe/" class="subscribe">subscribe</a>
                            <!-- <button class="sign_in"></button> -->
                            <a href="/login/<?php echo ((ACTIVE_URL != '/') ? '?redirect_url=' . ACTIVE_URL : '') ?>"
                                class="sign_in">sign in</a>
                        </div>

                    <?php
                    }

                    ?>
                </div>
            </div>
        </div>
        <?php

        switch (BASE_URL) {
            case '/partner-insights-guidelines/':
                $changeBackground = 'pinkBackground';
                break;
            case '/about-us/':
                $changeBackground = 'pinkBackground';
                break;
            case '/terms-and-conditions/':
                $changeBackground = 'pinkBackground';
                break;
            case '/privacy-policy/':
                $changeBackground = 'pinkBackground';
                break;
            case '/refund-policy/':
                $changeBackground = 'pinkBackground';
                break;
            default:
                $changeBackground = '';
                break;
        }

        ?>
        <div class="mobile-nav-backdrop" id="mobileNavBackdrop" aria-hidden="true"></div>
        <nav id="siteNav" class="site-nav <?php echo $changeBackground ?>">
            <div class="mobile-nav-header">
                <span class="mobile-nav-title">Menu</span>
                <button class="mobile-menu-close" id="mobileMenuClose" type="button" aria-label="Close menu">
                    <span>&times;</span>
                </button>
            </div>
            <?php if (isset($_COOKIE['user_data'])) { ?>
                <div class="mobile-user">
                    <div class="mobile-user__name"><?php echo PROFILE_DISPLAY_NAME ?></div>
                    <div class="mobile-user__email"><?php echo PROFILE_EMAIL ?></div>
                    <div class="mobile-user__links">
                        <?php
                        if (isset($activeSubscription) && $activeSubscription == true) {
                            echo "<a href='/manage-subscription/'>Manage Subscription</a>";
                        } else {
                            echo "<a href='/subscribe/'>Buy Subscription</a>";
                        }
                        ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="mobile-auth">
                    <a href="/subscribe/" class="mobile-auth__primary">Subscribe</a>
                    <a href="/login/<?php echo ((ACTIVE_URL != '/') ? '?redirect_url=' . ACTIVE_URL : '') ?>"
                        class="mobile-auth__secondary">Sign in</a>
                </div>
            <?php } ?>
            <ul>
                <?php

                if (SITE_ID == 49) { ?>
                    <li><a href="/deep-tales/" <?php echo ((BASE_URL == '/deep-tales/') ? 'class="active"' : '') ?>
                            title="Deepdives">Deepdives</a></li>
                    <li><a href="/explainer/" <?php echo ((BASE_URL == '/explainer/') ? 'class="active"' : '') ?>
                            title="Explainer">Explainer</a></li>
                    <li><a href="/fintech-news/" <?php echo ((BASE_URL == '/fintech-news/') ? 'class="active"' : '') ?>
                            title="FIntech News">News</a></li>
                    <li><a href="/ai-emerging-tech/" title="Report"
                            <?php echo ((BASE_URL == '/ai-emerging-tech/') ? 'class="active"' : '') ?>>AI/Emerging Tech</a>
                    </li>
                    <li><a href="/money-trivia/" <?php echo ((BASE_URL == '/money-trivia/') ? 'class="active"' : '') ?>
                            title="Money Trivia">Money Trivia</a></li>
                    <li><a href="/money-and-machines/"
                            <?php echo ((BASE_URL == '/money-and-machines/') ? 'class="active"' : '') ?>
                            title="Money & Machines">Money & Machines</a></li>
                    <li><a href="https://stack.theheadandtale.com/" target="_blank" title="Report"
                            <?php echo ((BASE_URL == '/report/') ? 'class="active"' : '') ?>>Report<sup
                                class="sup">NEW</sup></a></li>
                <?php
                }

                ?>
            </ul>
            <div class="mobile-nav-search">
                <input type="text" class="mobile-nav-search__input" id="mobileNavSearchInput"
                    placeholder="Search news...">
                <button class="mobile-nav-search__btn" id="mobileNavSearchButton" type="button" aria-label="Search">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6" />
                        <path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </nav>
        <div class="mobile-search-overlay" id="mobileSearchOverlay" aria-hidden="true">
            <div class="mobile-search-panel" role="dialog" aria-modal="true">
                <div class="mobile-search-header">
                    <span>Search</span>
                    <button class="mobile-search-close" id="mobileSearchClose" type="button"
                        aria-label="Close search">&times;</button>
                </div>
                <input type="text" class="mobile-search-input" id="mobileSearchInput" placeholder="Search news...">
                <p class="mobile-search-hint">Press Enter to search</p>
            </div>
        </div>
    </header>
    <main <?php echo (($link_type == 'page') ? 'style="border-top: 0px"' : '') ?>>