<?php

require_once UTIL_FUNCTIONS;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>about.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>manage.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>subscribe.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>footer.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>details.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>thankyou.css">
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
    <script>
        (function() {
            if (!('serviceWorker' in navigator)) {
                return;
            }

            const swVersion = '<?php echo assetVersion('service-worker.php', ROOT . '/router'); ?>';

            window.addEventListener('load', async function() {
                let pageNeedsReload = false;

                try {
                    const regs = await navigator.serviceWorker.getRegistrations();
                    for (const reg of regs) {
                        const swUrl = (reg.active && reg.active.scriptURL) ||
                            (reg.waiting && reg.waiting.scriptURL) ||
                            (reg.installing && reg.installing.scriptURL) || '';

                        if (swUrl.includes('/sw.js') && !swUrl.includes('/dashboard/sw.js')) {
                            const removed = await reg.unregister();
                            if (removed) {
                                pageNeedsReload = true;
                            }
                        }
                    }
                } catch (e) {}

                try {
                    if ('caches' in window) {
                        const cacheKeys = await caches.keys();
                        const visitorCacheKeys = cacheKeys.filter(function(key) {
                            return key.indexOf('-cache-') !== -1;
                        });

                        if (visitorCacheKeys.length) {
                            await Promise.all(visitorCacheKeys.map(function(key) {
                                return caches.delete(key);
                            }));
                            pageNeedsReload = true;
                        }
                    }
                } catch (e) {}

                if (pageNeedsReload && !sessionStorage.getItem('visitorSwCacheCleaned')) {
                    sessionStorage.setItem('visitorSwCacheCleaned', '1');
                    window.location.reload();
                    return;
                }

                try {
                    await navigator.serviceWorker.register('/sw.js?v=' + swVersion);
                } catch (e) {}
            });
        })();
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
                    <div class="search-container">
                        <button class="search-btn" id="searchButton">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path fill="#<?php echo ((SITE_VERSION == 'mobile') ? '5B99D5' : 'FFFFFF') ?>"
                                    d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
                            </svg>
                            <span class="btn-text">Search</span>
                        </button>
                        <input type="text" class="search-input" id="searchInput" placeholder="Type to search...">
                        <button class="close-btn" id="closeButton">
                            <i class="icon-close">&times;</i>
                        </button>
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
                $changeBackground = "class='pinkBackground'";
                break;
            case '/about-us/':
                $changeBackground = "class='pinkBackground'";
                break;
            case '/terms-and-conditions/':
                $changeBackground = "class='pinkBackground'";
                break;
            case '/privacy-policy/':
                $changeBackground = "class='pinkBackground'";
                break;
            case '/refund-policy/':
                $changeBackground = "class='pinkBackground'";
                break;
            default:
                $changeBackground = "";
                break;
        }

        ?>
        <nav <?php echo $changeBackground ?>>
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
        </nav>
    </header>
    <main <?php echo (($link_type == 'page') ? 'style="border-top: 0px"' : '') ?>>