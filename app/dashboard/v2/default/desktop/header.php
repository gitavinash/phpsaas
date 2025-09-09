<?php

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
    $startDate = date("Y-m-d 00:00:00", strtotime("-1 month"));
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    if ($postType == '') {
        echo "<meta http-equiv='refresh' content='300'>";
    }
    ?>
    <link rel="icon" href="<?php echo SITE_ICON ?>">
    <title><?php echo str_replace('_', ' ', $dashboardPageTitle . ' | ' . SITE_NAME); ?></title>
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>uicons-regular-rounded.min.css"> -->
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
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
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>admin_v2.min.css">
    <link rel="manifest" href="mainfest.json">
    <?php
    if (SITE_VERSION == 'mobile') {
        echo "<link rel='stylesheet' type='text/css' href='" . ADMIN_STYLESHEET . "admin_v2_mobile.min.css'>";
    }
    ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300;400;600&display=swap"
        media="screen">
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
</head>

<body>
    <!-- Show this section when notification is disable -->
    <!-- <div class="enableNotification">

    </div> -->
    <!-- Show this section when page is loading -->
    <!-- <div class="pageLoader">
        <?php

        if (SITE_ID == 50) { ?>
        <img src="/app/hms/visitor/mgihotels/desktop/assets/images/logo.png" alt="<?php echo ucwords(SITE_APP) ?> logo">
        <?php
        } else { ?>
        <img src="<?php echo ((SITE_APP_IMAGE != '') ? SITE_APP_IMAGE : '/app/cms/visitor/blumox/desktop/assets/images/logo.svg') ?>"
            alt="<?php echo ucwords(SITE_APP) ?> logo">
        <?php
        }

        ?>
    </div> -->
    <header>
        <div class="right-side">
            <div class="mobileLogo"><a href="/dashboard/"><img src="<?php echo $app_image ?>" alt=""></a></div>
            <ul>
                <li <?php echo ((SITE_VERSION == 'mobile') ? "style='display:none'" : '') ?>><a
                        href="https://support.blumox.com/?url=<?php echo urlencode(SITE_URL . ACTIVE_URL . '&domain=' . SITE_DOMAIN) ?>"
                        target="_blank" title="Support"><i class="fi-rr-headset" style="color: #167347;"></i></a></li>
                <!-- <li <?php echo ((SITE_VERSION == 'mobile') ? "style='display:none'" : '') ?>><a href="#"
                        onclick="showNotification()"><i class="fi-rr-bell"></i></a></li> -->
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
                            <li><a href="/dashboard/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
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
                <li class="header-section-name">
                    <p>Dashboard<span>View performance reports.</span></p>
                </li>

                <li <?php echo ((ADMIN_BASE_URL == '/') ? 'class="active"' : '') ?>><a href="/dashboard/"><i
                            class="fi-rr-home"></i> Dashboard</a></li>

                <?php
                $appAsideFile = $appDashboardDirectory . 'aside/' . strtolower(SITE_APP) . '.php';

                if (file_exists($appAsideFile)) {
                    require_once $appAsideFile;
                } else {
                    require_once $appDashboardDirectory . 'aside/default.php';
                }
                ?>

                <li class="header-section-name">
                    <p>Setting<span>Manage profile setting.</span></p>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/setting/') !== false) ? 'class="active"' : '') ?>>
                    <a href="setting/"><i class="fi-rr-settings"></i> Setting</a>
                    <div class="side-menu">
                        <ul>
                            <?php
                            if ($site_access_type == 'administrator') { ?>

                            <li><a href="setting/configuration/"><i class="fi-rr-settings"></i> Configuration</a></li>
                            <?php } ?>
                            <li><a href="setting/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>

                            <?php

                            if ($site_access_type == 'administrator') { ?>

                            <li><a href="setting/configuration/"><i class="fi-rr-settings"></i> Configuration</a></li>
                            <?php } ?>
                            <li><a href="setting/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
                        </ul>
                    </div>
                </li>

                <?php

                if ($site_access_type == 'administrator') { ?>


                <li <?php echo ((strpos(ADMIN_BASE_URL, '/user/') !== false) ? 'class="active"' : '') ?>>
                    <a href="user/"><i class="fi fi-rr-user"></i> Users</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="user/list/"><i class="fi-rr-apps-sort"></i> All users</a></li>
                            <li><a href="user/add/"><i class="fi fi-rr-user-add"></i> Add new user</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="user/list/"><i class="fi-rr-apps-sort"></i> All users</a></li>
                            <li><a href="user/add/"><i class="fi fi-rr-user-add"></i> Add new user</a>
                        </ul>
                    </div>
                </li>

                <?php }
                ?>

                <li class="appVersion">
                    <p>App version : <?php echo ALEYA_APP_VERSION ?></p>
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
                            echo "<li><a href='{$paginationURL}/'>" . ucwords(str_replace('_', ' ', $urlStructure[$i])) . "</a></li>";
                        }

                        ?>
                </ul>
            </div>

            <?php
            }

            ?>