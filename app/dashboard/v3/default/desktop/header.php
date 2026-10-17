<?php

$packageId = isset($site_package_id) ? (int)$site_package_id : (defined('SITE_PACKAGE_ID') ? (int)SITE_PACKAGE_ID : 7);
if (!defined('SITE_PACKAGE_ID')) {
    define('SITE_PACKAGE_ID', $packageId);
}
$isDigitalMarketingPackage = ($packageId === 6 || (defined('SITE_PACKAGE_ID') && (int)SITE_PACKAGE_ID === 6));

require_once UTIL_FUNCTIONS;

$appDashboardDirectory = ADMIN_PATH . 'dashboard/app/';
$postType = rtrim(ltrim(ADMIN_BASE_URL, '/'), '/');
$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';
$siteAccessMeta = '';
if (isset($_COOKIE['user_meta'])) {
    if ($_COOKIE['user_meta'] != '') {
        $siteAccessMeta = (json_decode(base64_decode($_COOKIE['user_meta']), true));
    }
}

$loggedInUserMeta = (isset($_COOKIE['user_meta']) ? json_decode(base64_decode($_COOKIE['user_meta']), true) : '');

if (isset($daterange)) {
    $dates = explode(" - ", $daterange);
    $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
    $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
} else {
    $startDate = date("Y-m-d 00:00:00", strtotime("-29 days"));
    $endDate = date("Y-m-d 23:59:59");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/dashboard/">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="icon" href="<?php echo SITE_ICON ?>">
    <title><?php echo str_replace(['_', '-'], ' ', $dashboardPageTitle . ' | ' . SITE_NAME); ?></title>
    <style>
        :root {
            --brand-700: <?php echo SITE_THEME_COLOUR ?>;
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>ui/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/datatables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.9/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.0.0/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/responsive.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>select2/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>image-uploader/css/image-uploader.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>drag_and_drop/drag_and_drop.min.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>whatsapp.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>web_forms.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>transporter-quote.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>transporter-fleet.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>whatsapp_automation.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>lead_automation.css?v=<?php echo time(); ?>">
    <?php
    if (SITE_VERSION == 'mobile') {
        echo "<link rel='stylesheet' type='text/css' href='" . ADMIN_STYLESHEET . "admin_mobile.css'>";
    }
    ?>
    <?php require_once PWA . 'header.php'; ?>
    <?php

    if (DEVELOPMENT_MODE == false) { ?>
        <script type="text/javascript">
            var oldGalleryImages = '';
            (function(c, l, a, r, i, t, y) {
                c[a] = c[a] || function() {
                    (c[a].q = c[a].q || []).push(arguments)
                };
                t = l.createElement(r);
                t.async = 1;
                t.src = "https://www.clarity.ms/tag/" + i;
                y = l.getElementsByTagName(r)[0];
                y.parentNode.insertBefore(t, y);
            })(window, document, "clarity", "script", "lrfkm51aya");
        </script>
    <?php
    }

    $currentUrl = defined('ADMIN_BASE_URL') ? ADMIN_BASE_URL : (defined('ACTIVE_URL') ? ACTIVE_URL : '');
    $isDashboardActive = ($currentUrl === '/dashboard/' || $currentUrl === '/dashboard' || $currentUrl === '' || $currentUrl === '/');
    $userRole = strtolower(isset($site_access_type) ? $site_access_type : (defined('USER_TYPE') ? USER_TYPE : ''));
    ?>
</head>

<body>
    <div id="spa-loader-bar"></div>
    <header>
        <div class="v3-minimized-brand-logo">
            <a href="/dashboard/" title="<?php echo !empty($app_title) ? $app_title : 'Aleya'; ?>">
                <img src="<?php echo !empty($app_image) ? $app_image : (defined('SITE_APP_IMAGE') ? SITE_APP_IMAGE : '/app/cms/visitor/blumox/desktop/assets/images/logo.svg'); ?>" alt="<?php echo !empty($app_title) ? $app_title : 'Aleya'; ?>">
            </a>
        </div>

        <div class="right-side">
            <div class="mobileLogo"><a href="/dashboard/"><img src="<?php echo $app_image ?>" alt=""></a></div>
            <ul>
                <li>
                    <a>Welcome, <strong><?php echo PROFILE_DISPLAY_NAME ?></strong>
                        <div class="profile-img"><img
                                src="<?php echo ((PROFILE_IMAGE != '') ? PROFILE_IMAGE : IMG_LIBRARY . 'default-profile.png'); ?>"
                                alt=""></div>
                    </a>
                    <?php
                    if (SITE_VERSION != 'mobile') { ?>
                        <div class="profile-menu">
                            <ul>
                                <li><a href="/dashboard/setting/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
                                <li><a href="/logout/"><i class="fi-rr-exit"></i> Logout</a></li>
                            </ul>
                        </div>
                    <?php
                    }
                    ?>
                </li>
            </ul>
        </div>
    </header>

    <aside id="sidebar">
        <div class="logo"><a href="/dashboard/"><img src="<?php echo $app_image ?>" alt="<?php echo $app_title ?>"></a>
        </div>
        <nav class="side-nav">
            <ul>

                <?php
                $appAsideFile = $appDashboardDirectory . 'aside/' . strtolower(SITE_APP) . '.php';

                if (file_exists($appAsideFile)) {
                    require_once $appAsideFile;
                } else {
                    require_once $appDashboardDirectory . 'aside/default.php';
                }

                $waAsideSectionFile = $appDashboardDirectory . 'aside/whatsapp_section.php';
                if (file_exists($waAsideSectionFile) && !defined('ISW_WA_ASIDE_LOADED')) {
                    require_once $waAsideSectionFile;
                }
                ?>

                <li class="appVersion">
                    <p>App version 3.0</p>
                </li>
            </ul>
        </nav>

    </aside>

    <?php

    if (SITE_VERSION == 'mobile') {

        $mobileMenuFile = $appDashboardDirectory . 'mobile-menu/' . strtolower(SITE_APP) . '.php';
        $defaultMobileMenuFile = $appDashboardDirectory . 'mobile-menu/default.php';

        if (file_exists($mobileMenuFile)) {
            require_once $mobileMenuFile;
        } else {
            require_once $defaultMobileMenuFile;
        }
    }

    ?>

    <main>

        <div class="container">

            <?php

            if (ADMIN_BASE_URL != '/') { ?>

                <div class="i-m-here-pagination">
                    <ul>
                        <li><a href="/dashboard/"><i class="fi-rr-home"></i></a></li>
                        <?php

                        $paginationURL = '';

                        for ($i = 0; $i < sizeof($urlStructure); $i++) {

                            $paginationURL = $paginationURL . '/' . strtolower($urlStructure[$i]);
                            echo "<li><a href='{$paginationURL}/'>" . ucwords(str_replace(['_', '-'], ' ', $urlStructure[$i])) . "</a></li>";
                        }

                        ?>
                    </ul>
                </div>

            <?php
            }

            ?>