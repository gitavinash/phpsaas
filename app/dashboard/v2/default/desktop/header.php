<?php
	$postType=rtrim(ltrim(ADMIN_BASE_URL, '/'), '/');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/dashboard/">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo SITE_ICON ?>">
    <title><?php echo $dashboardPageTitle.' | '.SITE_NAME; ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>uicons-regular-rounded.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY?>ui/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/datatables.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>admin_v2.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300;400;600&display=swap"
        media="screen">

</head>

<body>

    <header>
        <div class="left-side">
            <ul>
                <li>
                    <!-- <a href="#" class="close-nav" id="closeNav"><i class="fi-rr-settings-sliders"></i></a> -->
                </li>
            </ul>
        </div>
        <div class="right-side">
            <ul>
                <!-- <li><a href="#"><i class="fi-rr-bell"></i></a></li> -->
                <li>
                    <a>Welcome, <strong><?php echo PROFILE_DISPLAY_NAME ?></strong>
                        <div class="profile-img"><img
                                src="<?php echo ((PROFILE_IMAGE!='') ? PROFILE_IMAGE : IMG_LIBRARY.'default-profile.png' ); ?>"
                                alt=""></div>
                    </a>
                    <ul>
                        <li><a href="settings/profile/"><i class="fi-rr-portrait"></i> Edit Profile</a></li>
                        <li><a href="settings/password/"><i class="fi-rr-key"></i> Change Password</a></li>
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
                <li <?php echo ((ADMIN_BASE_URL=='/') ? 'class="active"' : '' ) ?>><a href="/dashboard/"><i
                            class="fi-rr-home"></i> Dashboard</a></li>
                <li class="header-section-name">
                    <p>CRM<span>Customer relationship Management</span></p>
                </li>
                <li <?php echo ((strpos(ADMIN_BASE_URL, '/orders/')!==false) ? 'class="active"' : '' ) ?>>
                    <a href="orders/"><i class="fi-rr-box-alt"></i> Manage Order</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="orders/list/"><i class="fi-rr-apps-sort"></i> All Orders</a></li>
                            <li><a href="orders/list/?status=1"><i class="fi-rr-check"></i>
                                    Confirm Orders</a>
                            </li>
                            <li><a href="orders/list/?status=2"><i class="fi-rr-hourglass-end"></i> Pending Orders</a>
                            </li>
                            <li><a href="orders/list/?status=3"><i class="fi-rr-cross"></i> Cancelled Orders</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="orders/list/"><i class="fi-rr-apps-sort"></i> All Orders</a></li>
                            <li><a href="orders/list/?status=1"><i class="fi-rr-check"></i>
                                    Confirm Orders</a>
                            </li>
                            <li><a href="orders/list/?status=2"><i class="fi-rr-hourglass-end"></i> Pending Orders</a>
                            </li>
                            <li><a href="orders/list/?status=3"><i class="fi-rr-cross"></i> Cancelled Orders</a></li>
                        </ul>
                    </div>
                </li>

                <li class="header-section-name">
                    <p>Subscriptions<span>Manage subscriptions and members.</span></p>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/subscriptions/')!==false) ? 'class="active"' : '' ) ?>>
                    <a href="subscriptions/"><i class="fi-rr-crown"></i> Manage Subscriptions</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="subscriptions/list/"><i class="fi-rr-apps-sort"></i> All subscriptions</a></li>
                            <li><a href="subscriptions/add/"><i class="fi-rr-add"></i>Add New Subscription</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="subscriptions/list/"><i class="fi-rr-apps-sort"></i> All subscriptions</a></li>
                            <li><a href="subscriptions/add/"><i class="fi-rr-add"></i>Add New Subscription</a></li>
                        </ul>
                    </div>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/subscribers/')!==false) ? 'class="active"' : '' ) ?>>
                    <a href="subscribers/"><i class="fi-rr-following"></i> Manage Subscribers</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="subscribers/list/"><i class="fi-rr-apps-sort"></i> All Subscribers</a></li>
                            <li><a href="subscribers/list/?status=1"><i class="fi-rr-check"></i>
                                    Confirm Subscribers</a>
                            </li>
                            <li><a href="subscribers/list/?status=2"><i class="fi-rr-hourglass-end"></i> Pending
                                    Subscribers</a>
                            </li>
                            <li><a href="subscribers/list/?status=3"><i class="fi-rr-cross"></i> Cancelled
                                    Subscribers</a></li>
                            <li><a href="subscribers/list/?status=4"><i class="fi-rr-time-delete"></i> Expired
                                    Subscribers</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="subscribers/list/"><i class="fi-rr-apps-sort"></i> All Subscribers</a></li>
                            <li><a href="subscribers/list/?status=1"><i class="fi-rr-check"></i>
                                    Confirm Subscribers</a>
                            </li>
                            <li><a href="subscribers/list/?status=2"><i class="fi-rr-hourglass-end"></i> Pending
                                    Subscribers</a>
                            </li>
                            <li><a href="subscribers/list/?status=3"><i class="fi-rr-cross"></i> Cancelled
                                    Subscribers</a>
                            </li>
                            <li><a href="subscribers/list/?status=4"><i class="fi-rr-time-delete"></i> Expired
                                    Subscribers</a></li>
                        </ul>
                    </div>
                </li>
                <!-- 
                <li class="header-section-name">
                    <p>General<span>Comman options like: Setting etc.</span></p>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/settings/')!==false) ? 'class="active"' : '' ) ?>>
                    <a href="settings/"><i class="fi-rr-settings"></i> Settings</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="settings/menu/"><i class="fi-rr-menu-burger"></i> Menu</a></li>
                            <li><a href="settings/configuration/"><i class="fi-rr-settings-sliders"></i>
                                    Configuration</a>
                            </li>
                            <li><a href="settings/password/"><i class="fi-rr-key"></i> Change password</a></li>
                            <li><a href="settings/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
                            <li><a href="settings/redirection/"><i class="fi-rr-link"></i>
                                    URL Redirection</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="settings/menu/"><i class="fi-rr-menu-burger"></i> Menu</a></li>
                            <li><a href="settings/configuration/"><i class="fi-rr-settings-sliders"></i>
                                    Configuration</a>
                            </li>
                            <li><a href="settings/password/"><i class="fi-rr-key"></i> Change Password</a></li>
                            <li><a href="settings/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
                            <li><a href="settings/redirection/"><i class="fi-rr-link"></i> URL Redirection</a></li>
                        </ul>
                    </div>
                </li> -->

            </ul>
        </nav>
    </aside>

    <main>

        <div class="container">

            <?php
            
            if(ADMIN_BASE_URL!='/') { ?>

            <div class="i-m-here-pagination">
                <ul>
                    <li><a href="/dashboard/"><i class="fi-rr-home"></i></a></li>
                    <?php
                        
                            $paginationURL='';

                            for($i=0; $i<sizeof($urlStructure); $i++) {

                                $paginationURL=$paginationURL.'/'.strtolower($urlStructure[$i]);
                                echo "<li><a href='{$paginationURL}/'>".ucwords($urlStructure[$i])."</a></li>";

                            }

                        ?>
                </ul>
            </div>

            <?php
            }

            ?>