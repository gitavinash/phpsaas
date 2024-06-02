<?php

$appDashboardDirectory = ADMIN_PATH . 'dashboard/app/';

$postType = rtrim(ltrim(ADMIN_BASE_URL, '/'), '/');

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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    if ($postType == '') {
        echo "<meta http-equiv='refresh' content='300'>";
    }
    ?>
    <link rel="icon" href="<?php echo SITE_ICON ?>">
    <title><?php echo $dashboardPageTitle . ' | ' . SITE_NAME; ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>uicons-regular-rounded.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>ui/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/datatables.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>select2/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>admin_v2.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300;400;600&display=swap" media="screen">
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
        })(window, document, "clarity", "script", "lrfkm51aya");
    </script>
</head>

<body>
    <!-- Show this section when notification is disable -->
    <!-- <div class="enableNotification">

    </div> -->
    <!-- Show this section when page is loading -->
    <div class="pageLoader">
        <img src="/upload/others/aleya-logo.png" alt="Aleya logo">
    </div>
    <header>
        <div class="right-side">
            <div class="search-box">
                <form action="#">
                    <div class="form-group">
                        <input type="text" placeholder="Search for all customers">
                    </div>
                </form>
            </div>
            <ul>
                <li><a href="#" onclick="showNotification()"><i class="fi-rr-bell"></i></a></li>
                <li>
                    <a>Welcome, <strong><?php echo PROFILE_DISPLAY_NAME ?></strong>
                        <div class="profile-img"><img src="<?php echo ((PROFILE_IMAGE != '') ? PROFILE_IMAGE : IMG_LIBRARY . 'default-profile.png'); ?>" alt=""></div>
                    </a>
                    <ul>
                        <li><a href="setting/profile/"><i class="fi-rr-portrait"></i> Edit Profile</a></li>
                        <li><a href="setting/password/"><i class="fi-rr-key"></i> Change Password</a></li>
                        <li><a href="/logout/?redirect_url=<?php echo ACTIVE_URL ?>"><i class="fi-rr-power"></i> Log
                                Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </header>

    <aside>
        <div class="logo"><a href="/dashboard/"><img src="<?php echo SITE_LOGO ?>" alt=""></a></div>

        <?php
        $appAsideFile = $appDashboardDirectory . 'aside/' . strtolower(SITE_APP) . '.php';

        if (file_exists($appAsideFile)) {
            require_once $appAsideFile;
        } else {
            require_once $appDashboardDirectory . 'aside/default.php';
        }
        ?>

    </aside>

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
                            echo "<li><a href='{$paginationURL}/'>" . ucwords($urlStructure[$i]) . "</a></li>";
                        }

                        ?>
                    </ul>
                </div>

            <?php
            }

            ?>