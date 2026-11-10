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
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_new.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>all.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>ui/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/datatables.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>image-uploader/css/image-uploader.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>select2/css/select2.min.css">
    <?php if (defined('ADMIN_BASE_URL') && ADMIN_BASE_URL === '/order/') { ?>
        <link rel="stylesheet" type="text/css" href="/lib/resource/ui/css/app/crm/orders-list.css?v=<?php echo @filemtime(ROOT . '/lib/resource/ui/css/app/crm/orders-list.css') ?: 1; ?>">
    <?php } ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap">
</head>

<body class="premium-admin">

    <header>
        <div class="left-side">
            <ul>
                <li>
                    <button type="button" class="nav-toggle" id="nav-toggle" aria-label="Open navigation">
                        <i class="fas fa-bars"></i>
                    </button>
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
        <div class="nav-search-wrapper">
            <label for="side-nav-search">Quick Navigation</label>
            <div class="nav-search-input">
                <i class="fas fa-search"></i>
                <input type="text" id="side-nav-search" placeholder="Search dashboard menu">
            </div>
        </div>
        <nav class="side-nav">
            <ul>
                <li <?php echo ((ADMIN_BASE_URL == '/') ? 'class="active"' : '') ?>><a href="/dashboard/"><i
                            class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                </li>
                <li <?php echo ((ADMIN_BASE_URL == '/order/') ? 'class="active"' : '') ?>>
                    <a href="order/?type=list&status=all"><i class="fas fa-funnel-dollar"></i> Manage order</a>
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
                <?php

                if (SITE_ID == 17) { ?>

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

                <?php
                }

                ?>
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
                <li <?php echo ((ADMIN_BASE_URL == '/user/') ? 'class="active"' : '') ?>>
                    <a href="user/?type=list&status=all"><i class="fas fa-users"></i> Manage Users</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="user/?type=list&status=all"><i class="fas fa-list"></i> All users</a>
                            </li>
                            <li><a href="user/?type=add"><i class="fas fa-plus"></i> Add new user</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="user/?type=list&status=all"><i class="fas fa-list"></i> All users</a>
                            </li>
                            <li><a href="user/?type=add"><i class="fas fa-plus"></i> Add new user</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="mobile-nav-overlay" id="nav-overlay"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var menuSearch = document.getElementById('side-nav-search');
            if (!menuSearch) {
                return;
            }

            var menuRoot = document.querySelector('nav.side-nav > ul');
            if (!menuRoot) {
                return;
            }

            var menuItems = menuRoot.children;

            menuSearch.addEventListener('input', function() {
                var searchTerm = menuSearch.value.toLowerCase().trim();

                for (var i = 0; i < menuItems.length; i++) {
                    var item = menuItems[i];
                    var topAnchor = item.firstElementChild;

                    if (!topAnchor || topAnchor.tagName !== 'A') {
                        continue;
                    }

                    var searchableText = topAnchor.textContent.toLowerCase();
                    var subLinks = item.querySelectorAll('.side-menu a, .bottom-menu a');

                    for (var j = 0; j < subLinks.length; j++) {
                        searchableText += ' ' + subLinks[j].textContent.toLowerCase();
                    }

                    item.style.display = (searchTerm === '' || searchableText.indexOf(searchTerm) !== -1) ?
                        '' : 'none';
                }
            });
        });
    </script>

    <main>

        <div class="container">