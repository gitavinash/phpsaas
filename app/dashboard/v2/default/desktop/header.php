<?php
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300;400;600&display=swap"
        media="screen">
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
                        <div class="profile-img"><img
                                src="<?php echo ((PROFILE_IMAGE != '') ? PROFILE_IMAGE : IMG_LIBRARY . 'default-profile.png'); ?>"
                                alt=""></div>
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
        <nav class="side-nav">
            <ul>
                <li class="header-section-name">
                    <p>Dashboard<span>View performance reports.</span></p>
                </li>

                <li <?php echo ((ADMIN_BASE_URL == '/') ? 'class="active"' : '') ?>><a href="/dashboard/"><i
                            class="fi-rr-home"></i> Dashboard</a></li>

                <?php

                if ($site_access_type == 'administrator') { ?>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/report/') !== false) ? 'class="active"' : '') ?>>
                    <a href="report/"><i class="fi fi-rr-chart-histogram"></i> Reports</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="report/employees/"><i class="fi-rr-employee-man-alt"></i> Employees
                                    performance</a>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="report/employees/"><i class="fi-rr-employee-man-alt"></i> Employees
                                    performance</a>
                        </ul>
                    </div>
                </li>

                <li class="header-section-name">
                    <p>Leads<span>Manage leads.</span></p>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/leads/') !== false) ? 'class="active"' : '') ?>>
                    <a href="leads/"><i class="fi fi-rr-chart-network"></i> Leads</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="leads/list/"><i class="fi-rr-apps-sort"></i> All leads</a></li>
                            <li><a href="leads/add/"><i class="fi fi-rr-add"></i> Add new lead</a></li>
                            <li><a href="leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed leads</a>
                            </li>
                            <li><a href="leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending leads</a>
                            </li>
                            <li><a href="leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow ups leads</a>
                            </li>
                            <li><a href="leads/list/?leadStatus=4"><i class="fi-rr-cross-circle"></i> Cancelled
                                    leads</a>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="leads/list/"><i class="fi-rr-apps-sort"></i> All leads</a></li>
                            <li><a href="leads/add/"><i class="fi fi-rr-add"></i> Add new lead</a></li>
                            <li><a href="leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed leads</a>
                            </li>
                            <li><a href="leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending leads</a>
                            </li>
                            <li><a href="leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow ups leads</a>
                            </li>
                            <li><a href="leads/list/?leadStatus=4"><i class="fi-rr-cross-circle"></i> Cancelled
                                    leads</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <?php

                    if (SITE_ID == 57) { ?>
                <li class="header-section-name">
                    <p>Vendors<span>Manage vendor.</span></p>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/vendors/transporter/') !== false) ? 'class="active"' : '') ?>>
                    <a href="vendors/transporter/"><i class="fi fi-rr-bus"></i> Transporter vendor</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="vendors/transporter/list/"><i class="fi-rr-apps-sort"></i> All transporters</a>
                            </li>
                            <li><a href="vendors/transporter/add/"><i class="fi fi-rr-add"></i> Add new transporter</a>
                            </li>
                            <li><a href="vendors/transporter/vehicle/"><i class="fi fi-rr-car-bus"></i> Manage
                                    vehicles</a>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="vendors/leads/list/"><i class="fi-rr-apps-sort"></i> All transporters</a></li>
                            <li><a href="vendors/transporter/add/"><i class="fi fi-rr-add"></i> Add new transporter</a>
                            </li>
                            <li><a href="vendors/transporter/vehicle/"><i class="fi fi-rr-car-bus"></i> Manage
                                    vehicles</a>
                        </ul>
                    </div>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/vendors/hotel/') !== false) ? 'class="active"' : '') ?>>
                    <a href="vendors/hotel/"><i class="fi fi-rr-hotel"></i> Hotel Vendors</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="vendors/hotel/list/"><i class="fi-rr-apps-sort"></i> All hotels</a></li>
                            <li><a href="vendors/hotel/add/"><i class="fi fi-rr-add"></i> Add new hotel</a></li>
                            <li><a href="vendors/hotel/amenities/"><i class="fi fi-rr-star"></i> Manage amenities</a>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="vendors/hotel/list/"><i class="fi-rr-apps-sort"></i> All hotels</a></li>
                            <li><a href="vendors/hotel/add/"><i class="fi fi-rr-add"></i> Add new hotel</a></li>
                            <li><a href="vendors/hotel/amenities/"><i class="fi fi-rr-star"></i> Manage amenities</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- 
                <li <?php echo ((strpos(ADMIN_BASE_URL, '/vendors/dmc/') !== false) ? 'class="active"' : '') ?>>
                    <a href="vendors/dmc/"><i class="fi fi-rr-shop"></i> DMC Vendor</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="vendors/dmc/list/"><i class="fi-rr-apps-sort"></i> All DMC's</a></li>
                            <li><a href="vendors/dmc/add/"><i class="fi fi-rr-add"></i> Add New DMC</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="vendors/dmc/list/"><i class="fi-rr-apps-sort"></i> All DMC's</a></li>
                            <li><a href="vendors/dmc/add/"><i class="fi fi-rr-add"></i> Add New DMC</a></li>
                        </ul>
                    </div>
                </li> -->

                <li class="header-section-name">
                    <p>Packages<span>Manage all tour packages.</span></p>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/packages/domestic/') !== false) ? 'class="active"' : '') ?>>
                    <a href="packages/domestic/"><i class="fi fi-rr-suitcase-alt"></i> Domestic packages</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="packages/domestic/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
                            </li>
                            <li><a href="packages/domestic/add/"><i class="fi fi-rr-add"></i> Add new package</a>
                            </li>
                            <li><a href="packages/domestic/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="packages/domestic/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
                            </li>
                            <li><a href="packages/domestic/add/"><i class="fi fi-rr-add"></i> Add new package</a>
                            </li>
                            <li><a href="packages/domestic/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/international/') !== false) ? 'class="active"' : '') ?>>
                    <a href="packages/international/"><i class="fi fi-rr-earth-asia"></i> International packages</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="packages/domestic/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
                            </li>
                            <li><a href="packages/domestic/add/"><i class="fi fi-rr-add"></i> Add new package</a>
                            </li>
                            <li><a href="packages/domestic/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="packages/domestic/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
                            </li>
                            <li><a href="packages/domestic/add/"><i class="fi fi-rr-add"></i> Add new package</a>
                            </li>
                            <li><a href="packages/domestic/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <?php
                    }

                    ?>

                <?php
                }

                ?>


                <?php

                if ($site_access_type == 'administrator') { ?>

                <!-- <li class="header-section-name">
                    <p>Clients<span>Manage clients.</span></p>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/clients/') !== false) ? 'class="active"' : '') ?>>
                    <a href="clients/"><i class="fi fi-rr-hr-group"></i> Clients</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="clients/list/"><i class="fi-rr-apps-sort"></i> All clients</a></li>
                            <li><a href="clients/add/"><i class="fi fi-rr-user-add"></i> Add new client</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="clients/list/"><i class="fi-rr-apps-sort"></i> All clients</a></li>
                            <li><a href="clients/add/"><i class="fi fi-rr-user-add"></i> Add new client</a>
                        </ul>
                    </div>
                </li> -->

                <li class="header-section-name">
                    <p>Integration<span>Manage third party resources.</span></p>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/integration/') !== false) ? 'class="active"' : '') ?>>
                    <a href="integration/"><i class="fi fi-rr-plug-connection"></i> Plugins</a>
                </li>

                <?php
                }


                ?>

                <li class="header-section-name">
                    <p>Setting<span>Manage application.</span></p>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/setting/') !== false) ? 'class="active"' : '') ?>>
                    <a href="setting/"><i class="fi-rr-settings"></i> Setting</a>
                    <div class="side-menu">
                        <ul>
                            <?php
                            if ($site_access_type == 'administrator') { ?>

                            <li><a href="setting/configuration/"><i class="fi-rr-settings"></i> Configuration</a></li>
                            <?php } ?>
                            <li><a href="setting/password/"><i class="fi-rr-key"></i> Change password</a></li>
                            <li><a href="setting/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>

                            <?php

                            if ($site_access_type == 'administrator') { ?>

                            <li><a href="setting/configuration/"><i class="fi-rr-settings"></i> Configuration</a></li>
                            <?php } ?>
                            <li><a href="setting/password/"><i class="fi-rr-key"></i> Change Password</a></li>
                            <li><a href="setting/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
                        </ul>
                    </div>
                </li>

                <?php

                if ($site_access_type == 'administrator') { ?>


                <li <?php echo ((strpos(ADMIN_BASE_URL, '/users/') !== false) ? 'class="active"' : '') ?>>
                    <a href="users/"><i class="fi fi-rr-user"></i> Users</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="users/list/"><i class="fi-rr-apps-sort"></i> All users</a></li>
                            <li><a href="users/add/"><i class="fi fi-rr-user-add"></i> Add new user</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="users/list/"><i class="fi-rr-apps-sort"></i> All users</a></li>
                            <li><a href="users/add/"><i class="fi fi-rr-user-add"></i> Add new user</a>
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