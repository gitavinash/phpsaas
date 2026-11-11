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
    <?php if (defined('ADMIN_BASE_URL') && ADMIN_BASE_URL === '/order/') { ?>
        <link rel="stylesheet" type="text/css" href="/lib/resource/ui/css/app/crm/orders-list.css?v=<?php echo @filemtime(ROOT . '/lib/resource/ui/css/app/crm/orders-list.css') ?: 1; ?>">
    <?php } ?>
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
                        <div class="profile-img"><img
                                src="<?php echo ((PROFILE_IMAGE != '') ? PROFILE_IMAGE : IMG_LIBRARY . 'default-profile.png'); ?>"
                                alt=""></div>
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
                <li <?php echo ((ADMIN_BASE_URL == '/') ? 'class="active"' : '') ?>><a href="/dashboard/"><i
                            class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                </li>

                <li <?php echo ((ADMIN_BASE_URL == '/order/') ? 'class="active"' : '') ?>>
                    <a href="order/?type=list&status=all"><i class="fas fa-rupee-sign" aria-hidden="true"></i> Manage order</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="order/?type=list&status=all"><i class="fas fa-list"></i> All Order</a></li>
                            <li><a href="order/?type=list&status=1"><i class="fas fa-check"></i> Confirm Order</a></li>
                            <li><a href="order/?type=list&status=0"><i class="fas fa-exclamation"></i> Pending Order</a></li>
                            <li><a href="order/?type=list&status=4"><i class="fas fa-clock"></i> Waiting Order</a></li>
                            <li><a href="order/?type=list&status=6"><i class="fas fa-box-open"></i> Delivered Order</a></li>
                            <li><a href="order/?type=list&status=3"><i class="fas fa-times"></i> Cancel Order</a></li>
                            <li><a href="order/?type=list&status=2"><i class="fas fa-ban"></i> Reject Order</a></li>
                            <li><a href="order/?type=list&status=5"><i class="fas fa-undo"></i> RTO Order</a></li>
                            <li><a href="order/?type=import"><i class="fas fa-file-import"></i> Import Orders</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="order/?type=list&status=all"><i class="fas fa-list"></i> All Order</a></li>
                            <li><a href="order/?type=list&status=1"><i class="fas fa-check"></i> Confirm Order</a></li>
                            <li><a href="order/?type=list&status=0"><i class="fas fa-exclamation"></i> Pending Order</a></li>
                            <li><a href="order/?type=list&status=4"><i class="fas fa-clock"></i> Waiting Order</a></li>
                            <li><a href="order/?type=list&status=6"><i class="fas fa-box-open"></i> Delivered Order</a></li>
                            <li><a href="order/?type=list&status=3"><i class="fas fa-times"></i> Cancel Order</a></li>
                            <li><a href="order/?type=list&status=2"><i class="fas fa-ban"></i> Reject Order</a></li>
                            <li><a href="order/?type=list&status=5"><i class="fas fa-undo"></i> RTO Order</a></li>
                            <li><a href="order/?type=import"><i class="fas fa-file-import"></i> Import Orders</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </aside>

    <main>

        <div class="container">