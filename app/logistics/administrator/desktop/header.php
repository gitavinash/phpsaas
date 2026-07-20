<?php
$postType = rtrim(ltrim(ADMIN_BASE_URL, '/'), '/');
$adminType = isset($_GET['type']) ? strtolower(trim((string)$_GET['type'])) : '';
$adminStatus = isset($_GET['status']) ? (string)$_GET['status'] : '';
$adminRole = isset($_GET['role']) ? strtolower(trim((string)$_GET['role'])) : '';

$isActiveBase = function ($base) {
    return (ADMIN_BASE_URL == $base) ? 'class="active"' : '';
};

$isSubActive = function ($base, $type = '', $status = null) use ($adminType, $adminStatus) {
    if (ADMIN_BASE_URL != $base) {
        return '';
    }
    if ($type !== '' && $adminType !== strtolower($type)) {
        return '';
    }
    if ($status !== null && $adminStatus !== (string)$status) {
        return '';
    }
    return 'class="active-sub"';
};

$isUserRoleMenuActive = function ($expectedRole = '') use ($adminRole) {
    if (ADMIN_BASE_URL != '/user/') {
        return '';
    }

    if ($expectedRole === '') {
        return ($adminRole === '' || $adminRole === 'staff' || $adminRole === 'admin') ? 'class="active"' : '';
    }

    return ($adminRole === strtolower($expectedRole)) ? 'class="active"' : '';
};
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
            <label for="side-nav-search">Quick Navigation <span class="nav-kbd">/</span></label>
            <div class="nav-search-input">
                <i class="fas fa-search"></i>
                <input type="text" id="side-nav-search" placeholder="Search dashboard menu">
            </div>
        </div>

        <nav class="side-nav">
            <ul>
                <li <?php echo $isActiveBase('/') ?>><a href="/dashboard/"><i
                            class="fas fa-tachometer-alt"></i> Dashboard</a></li>

                <li class="nav-section-title"><span>Operations</span></li>
                <li <?php echo $isActiveBase('/order/') ?>>
                    <a href="order/?type=list&status=all"><i class="fas fa-shipping-fast"></i> Order Management</a>
                    <div class="side-menu">
                        <ul>
                            <li><a <?php echo $isSubActive('/order/', 'list', 'all') ?> href="order/?type=list&status=all"><i class="fas fa-list"></i> All Orders</a></li>
                            <li><a <?php echo $isSubActive('/order/', 'list', '1') ?> href="order/?type=list&status=1"><i class="fas fa-check"></i> Confirmed Orders</a>
                            </li>
                            <li><a <?php echo $isSubActive('/order/', 'list', '0') ?> href="order/?type=list&status=0"><i class="fas fa-exclamation"></i> Pending Orders</a></li>
                            <li><a <?php echo $isSubActive('/order/', 'list', '6') ?> href="order/?type=list&status=6"><i class="fas fa-truck"></i> Delivered Orders</a></li>
                            <li><a <?php echo $isSubActive('/order/', 'list', '5') ?> href="order/?type=list&status=5"><i class="fas fa-undo"></i> RTO Orders</a></li>
                            <li><a <?php echo $isSubActive('/order/', 'list', '2') ?> href="order/?type=list&status=2"><i class="fas fa-times"></i> Cancelled Orders</a>
                            </li>
                            <li><a <?php echo $isSubActive('/order/', 'import') ?> href="order/?type=import"><i class="fas fa-file-import"></i> Import Orders</a>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a <?php echo $isSubActive('/order/', 'list', 'all') ?> href="order/?type=list&status=all"><i class="fas fa-list"></i> All Orders</a></li>
                            <li><a <?php echo $isSubActive('/order/', 'list', '1') ?> href="order/?type=list&status=1"><i class="fas fa-check"></i> Confirmed Orders</a>
                            </li>
                            <li><a <?php echo $isSubActive('/order/', 'list', '0') ?> href="order/?type=list&status=0"><i class="fas fa-exclamation"></i> Pending Orders</a></li>
                            <li><a <?php echo $isSubActive('/order/', 'list', '6') ?> href="order/?type=list&status=6"><i class="fas fa-truck"></i> Delivered Orders</a></li>
                            <li><a <?php echo $isSubActive('/order/', 'list', '5') ?> href="order/?type=list&status=5"><i class="fas fa-undo"></i> RTO Orders</a></li>
                            <li><a <?php echo $isSubActive('/order/', 'list', '2') ?> href="order/?type=list&status=2"><i class="fas fa-times"></i> Cancelled Orders</a>
                            </li>
                            <li><a <?php echo $isSubActive('/order/', 'import') ?> href="order/?type=import"><i class="fas fa-file-import"></i> Import Orders</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-section-title"><span>People</span></li>
                <li <?php echo $isActiveBase('/company/') ?>>
                    <a href="company/?type=list&status=all"><i class="fas fa-building"></i> Company Directory</a>
                    <div class="side-menu">
                        <ul>
                            <li><a <?php echo $isSubActive('/company/', 'list', 'all') ?> href="company/?type=list&status=all"><i class="fas fa-list"></i> All Companies</a>
                            </li>
                            <li><a href="company/?type=add"><i class="fas fa-plus"></i> Add new company</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a <?php echo $isSubActive('/company/', 'list', 'all') ?> href="company/?type=list&status=all"><i class="fas fa-list"></i> All Companies</a>
                            </li>
                            <li><a href="company/?type=add"><i class="fas fa-plus"></i> Add new company</a></li>
                        </ul>
                    </div>
                </li>

                <li <?php echo $isUserRoleMenuActive('vendor') ?>>
                    <a href="vendor/?type=list&status=all"><i class="fas fa-store"></i> Vendor Management</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="vendor/?type=list&status=all"><i class="fas fa-list"></i> All Vendors</a></li>
                            <li><a href="vendor/?type=add"><i class="fas fa-plus"></i> Add New Vendor</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="vendor/?type=list&status=all"><i class="fas fa-list"></i> All Vendors</a></li>
                            <li><a href="vendor/?type=add"><i class="fas fa-plus"></i> Add New Vendor</a></li>
                        </ul>
                    </div>
                </li>

                <li <?php echo $isUserRoleMenuActive() ?>>
                    <a href="user/?type=list&status=all"><i class="fas fa-user-cog"></i> Team &amp; Staff</a>
                    <div class="side-menu">
                        <ul>
                            <li><a <?php echo $isSubActive('/user/', 'list', 'all') ?> href="user/?type=list&status=all"><i class="fas fa-list"></i> All Users</a>
                            </li>
                            <li><a <?php echo $isSubActive('/user/', 'add') ?> href="user/?type=add"><i class="fas fa-plus"></i> Add New User</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a <?php echo $isSubActive('/user/', 'list', 'all') ?> href="user/?type=list&status=all"><i class="fas fa-list"></i> All Users</a>
                            </li>
                            <li><a <?php echo $isSubActive('/user/', 'add') ?> href="user/?type=add"><i class="fas fa-plus"></i> Add New User</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-section-title"><span>Settings</span></li>
                <li <?php echo (($postType == 'setting') ? 'class="active"' : '') ?>>
                    <a href="setting/?type=configuration"><i class="fas fa-cog"></i> Settings</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="setting/?type=configuration"><i class="fas fa-tools"></i> Configuration</a>
                            </li>
                            <li><a href="setting/?type=profile"><i class="fas fa-user"></i> Profile</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="setting/?type=configuration"><i class="fas fa-tools"></i> Configuration</a>
                            </li>
                            <li><a href="setting/?type=profile"><i class="fas fa-user"></i> Profile</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="mobile-nav-overlay" id="nav-overlay"></div>
    <main>

        <div class="container">