<?php
$postType = rtrim(ltrim(ADMIN_BASE_URL, '/'), '/');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/dashboard/">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo SITE_ICON ?>">
    <title>
        <?php echo str_replace('_', ' ', ucwords(((ADMIN_BASE_URL == '/') ? 'Dashboard' : str_replace('/', ' ', ADMIN_BASE_URL . ' ' . @$_GET['type'])) . ' | ' . SITE_NAME)); ?>
    </title>
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>all.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>ui/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/datatables.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
</head>

<body>

    <header>
        <div class="left-side">
            <ul>
                <li>
                    <div class="logo"><a href="/dashboard/"><img src="<?php echo SITE_LOGO ?>" alt=""></a></div>
                </li>
            </ul>
        </div>
        <div class="right-side">
            <ul>
                <li>
                    <a href="">Welcome, <strong><?php echo PROFILE_DISPLAY_NAME ?></strong>
                        <div class="profile-img"><img src="<?php echo ((PROFILE_IMAGE != '') ? PROFILE_IMAGE : IMG_LIBRARY . 'default-profile.png'); ?>" alt=""></div>
                    </a>
                    <ul>
                        <li><a href="/logout/"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </header>

    <aside>
        <nav class="side-nav">
            <ul>
                <li <?php echo ((ADMIN_BASE_URL == '/') ? 'class="active"' : '') ?>><a href="/dashboard/"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li <?php echo ((ADMIN_BASE_URL == '/order/') ? 'class="active"' : '') ?>>
                    <a href="order/?type=list&status=all"><i class="fas fa-funnel-dollar"></i> Manage order</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="order/?type=list&status=all"><i class="fas fa-list"></i> All Order</a></li>
                            <li><a href="order/?type=list&status=1"><i class="fas fa-check"></i> Confirm Order</a>
                            </li>
                            <li><a href="order/?type=list&status=0"><i class="fas fa-exclamation"></i> Pending
                                    Order</a></li>
                            <li><a href="order/?type=list&status=2"><i class="fas fa-times"></i> Cancel Order</a>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="order/?type=list&status=all"><i class="fas fa-list"></i> All Order</a></li>
                            <li><a href="order/?type=list&status=1"><i class="fas fa-check"></i> Confirm Order</a>
                            </li>
                            <li><a href="order/?type=list&status=0"><i class="fas fa-exclamation"></i> Pending
                                    Order</a></li>
                            <li><a href="order/?type=list&status=2"><i class="fas fa-times"></i> Cancel Order</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li <?php echo ((ADMIN_BASE_URL == '/subscription/') ? 'class="active"' : '') ?>>
                    <a href="subscription/?type=list&status=all"><i class="fas fa-crown"></i> Manage
                        Subscription</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="subscription/?type=list&status=all"><i class="fas fa-list"></i> All
                                    Subscription</a></li>
                            <li><a href="subscription/?type=list&status=1"><i class="fas fa-check"></i> Confirm
                                    Subscription</a></li>
                            <li><a href="subscription/?type=list&status=0"><i class="fas fa-exclamation"></i> Pending
                                    Subscription</a></li>
                            <li><a href="subscription/?type=list&status=2"><i class="fas fa-times"></i> Cancel
                                    Subscription</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="subscription/?type=list&status=all"><i class="fas fa-list"></i> All
                                    Subscription</a></li>
                            <li><a href="subscription/?type=list&status=1"><i class="fas fa-check"></i> Confirm
                                    Subscription</a></li>
                            <li><a href="subscription/?type=list&status=0"><i class="fas fa-exclamation"></i> Pending
                                    Subscription</a></li>
                            <li><a href="subscription/?type=list&status=2"><i class="fas fa-times"></i> Cancel
                                    Subscription</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo ((ADMIN_BASE_URL == '/customer/') ? 'class="active"' : '') ?>>
                    <a href="customer/?type=list&status=all"><i class="fas fa-users"></i> Manage Customer</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="customer/?type=list&status=all"><i class="fas fa-list"></i> All customer</a>
                            </li>
                            <!-- <li><a href="customer/?type=add"><i class="fas fa-plus"></i>  Add new customer</a></li> -->
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="customer/?type=list&status=all"><i class="fas fa-list"></i> All customer</a>
                            </li>
                            <!-- <li><a href="customer/?type=add"><i class="fas fa-plus"></i>  Add new customer</a></li> -->
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </aside>

    <main>

        <div class="container">