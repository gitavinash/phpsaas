<?php
$postType = rtrim(ltrim(ADMIN_BASE_URL, '/'), '/');
$egAdminCssFile = ROOT . ADMIN_STYLESHEET . 'ecom-admin.css';
$egAdminCssVer = file_exists($egAdminCssFile) ? filemtime($egAdminCssFile) : time();

$egNavSections = array(
    array(
        'id' => 'dashboard',
        'label' => 'Dashboard',
        'href' => '/dashboard/',
        'icon' => 'fas fa-tachometer-alt',
        'active' => (ADMIN_BASE_URL == '/'),
        'children' => array(),
    ),
    array(
        'id' => 'orders',
        'label' => 'Orders',
        'href' => 'order/?type=list&status=all',
        'icon' => 'fas fa-shopping-bag',
        'active' => ($postType == 'order'),
        'children' => array(
            array('label' => 'All Orders', 'href' => 'order/?type=list&status=all', 'icon' => 'fas fa-list'),
            array('label' => 'Pending Orders', 'href' => 'order/?type=list&status=0', 'icon' => 'fas fa-clock'),
            array('label' => 'Delivered', 'href' => 'order/?type=list&status=6', 'icon' => 'fas fa-check-circle'),
        ),
    ),
    array(
        'id' => 'product',
        'label' => 'Product',
        'href' => 'product/?type=list&status=all',
        'icon' => 'fas fa-box-open',
        'active' => (ADMIN_BASE_URL == '/product/'),
        'children' => array(
            array('label' => 'All Products', 'href' => 'product/?type=list&status=all', 'icon' => 'fas fa-list'),
            array('label' => 'Add New Product', 'href' => 'product/?type=add&editor=true', 'icon' => 'fas fa-plus'),
            array('label' => 'Categories', 'href' => 'product/?type=category', 'icon' => 'fas fa-tag'),
            array('label' => 'Tags', 'href' => 'product/?type=tags', 'icon' => 'fas fa-tags'),
        ),
    ),
    array(
        'id' => 'subscription',
        'label' => 'Subscription',
        'href' => 'subscription/?type=list&status=all',
        'icon' => 'fas fa-crown',
        'active' => (ADMIN_BASE_URL == '/subscription/'),
        'children' => array(
            array('label' => 'All Subscriptions', 'href' => 'subscription/?type=list&status=all', 'icon' => 'fas fa-list'),
            array('label' => 'Add New Subscription', 'href' => 'subscription/?type=add&editor=true', 'icon' => 'fas fa-plus'),
            array('label' => 'Categories', 'href' => 'subscription/?type=category', 'icon' => 'fas fa-tag'),
        ),
    ),
    array(
        'id' => 'event',
        'label' => 'Events / Workshop',
        'href' => 'event/?type=list&status=all',
        'icon' => 'fas fa-gopuram',
        'active' => ($postType == 'event'),
        'children' => array(
            array('label' => 'All Events', 'href' => 'event/?type=list&status=all', 'icon' => 'fas fa-list'),
            array('label' => 'Add New Event', 'href' => 'event/?type=add', 'icon' => 'fas fa-plus'),
            array('label' => 'Categories', 'href' => 'event/?type=category', 'icon' => 'fas fa-tags'),
        ),
    ),
    array(
        'id' => 'page',
        'label' => 'Page',
        'href' => 'page/?type=list&status=all',
        'icon' => 'fas fa-file',
        'active' => (ADMIN_BASE_URL == '/page/'),
        'children' => array(
            array('label' => 'All Pages', 'href' => 'page/?type=list&status=all', 'icon' => 'fas fa-list'),
            array('label' => 'Add New Page', 'href' => 'page/?type=add&editor=true', 'icon' => 'fas fa-plus'),
        ),
    ),
    array(
        'id' => 'post',
        'label' => 'Blog',
        'href' => 'post/?type=list&status=all',
        'icon' => 'fas fa-blog',
        'active' => ($postType == 'post'),
        'children' => array(
            array('label' => 'All Posts', 'href' => 'post/?type=list&status=all', 'icon' => 'fas fa-list'),
            array('label' => 'Add New Post', 'href' => 'post/?type=add&editor=true', 'icon' => 'fas fa-plus'),
            array('label' => 'Categories', 'href' => 'post/?type=category', 'icon' => 'fas fa-tags'),
        ),
    ),
    array(
        'id' => 'media',
        'label' => 'Media',
        'href' => 'media/?type=list&status=all',
        'icon' => 'fas fa-folder-open',
        'active' => (ADMIN_BASE_URL == '/media/'),
        'children' => array(
            array('label' => 'All Media', 'href' => 'media/?type=list&status=all', 'icon' => 'fas fa-list'),
            array('label' => 'Add New Media', 'href' => 'media/?type=add', 'icon' => 'fas fa-plus'),
            array('label' => 'Images', 'href' => 'media/?type=list&media=image', 'icon' => 'fas fa-images'),
            array('label' => 'Videos', 'href' => 'media/?type=list&media=video', 'icon' => 'fas fa-video'),
            array('label' => 'Documents', 'href' => 'media/?type=list&media=docs', 'icon' => 'fas fa-copy'),
        ),
    ),
    array(
        'id' => 'social_proof',
        'label' => 'Social Video Hub',
        'href' => 'social_proof/?type=list&status=all',
        'icon' => 'fas fa-play-circle',
        'active' => ($postType == 'social_proof'),
        'children' => array(
            array('label' => 'All Videos', 'href' => 'social_proof/?type=list&status=all', 'icon' => 'fas fa-list'),
            array('label' => 'Add New Video', 'href' => 'social_proof/?type=add&editor=true', 'icon' => 'fas fa-plus'),
            array('label' => 'Video Categories', 'href' => 'social_proof/?type=category', 'icon' => 'fas fa-tags'),
        ),
    ),
    array(
        'id' => 'coupon',
        'label' => 'Coupon',
        'href' => 'coupon/?type=list&status=all',
        'icon' => 'fas fa-tags',
        'active' => (ADMIN_BASE_URL == '/coupon/'),
        'children' => array(
            array('label' => 'All Coupons', 'href' => 'coupon/?type=list&status=all', 'icon' => 'fas fa-list'),
            array('label' => 'Add New Coupon', 'href' => 'coupon/?type=add', 'icon' => 'fas fa-ticket-alt'),
        ),
    ),
    array(
        'id' => 'import',
        'label' => 'Import',
        'href' => 'import/?type=product',
        'icon' => 'fas fa-upload',
        'active' => (ADMIN_BASE_URL == '/import/'),
        'children' => array(
            array('label' => 'Products', 'href' => 'import/?type=product', 'icon' => 'fas fa-cubes'),
        ),
    ),
    array(
        'id' => 'appearance',
        'label' => 'Appearance',
        'href' => 'appearance/?type=themes',
        'icon' => 'fas fa-paint-brush',
        'active' => (ADMIN_BASE_URL == '/appearance/'),
        'children' => array(
            array('label' => 'Themes', 'href' => 'appearance/?type=themes', 'icon' => 'fas fa-palette'),
            array('label' => 'Menu', 'href' => 'appearance/?type=menu', 'icon' => 'fas fa-ellipsis-v'),
            array('label' => 'Sliders', 'href' => 'appearance/?type=sliders', 'icon' => 'fas fa-images'),
        ),
    ),
    array(
        'id' => 'setting',
        'label' => 'Settings',
        'href' => 'setting/?type=configuration',
        'icon' => 'fas fa-cog',
        'active' => (ADMIN_BASE_URL == '/setting/'),
        'children' => array(
            array('label' => 'Configuration', 'href' => 'setting/?type=configuration', 'icon' => 'fas fa-tools'),
            array('label' => 'URL redirection', 'href' => 'setting/?type=redirection&status=all', 'icon' => 'fas fa-external-link-alt'),
            array('label' => 'Profile', 'href' => 'setting/?type=profile', 'icon' => 'fas fa-user'),
        ),
    ),
);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/dashboard/">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="icon" href="<?php echo htmlspecialchars(SITE_ICON, ENT_QUOTES, 'UTF-8'); ?>">
    <title>
        <?php echo htmlspecialchars(str_replace('_', ' ', ucwords(((ADMIN_BASE_URL == '/') ? 'Dashboard' : str_replace('/', ' ', ADMIN_BASE_URL . ' ' . @$_GET['type'])) . ' | ' . SITE_NAME)), ENT_QUOTES, 'UTF-8'); ?>
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
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>ecom-admin.css?v=<?php echo $egAdminCssVer; ?>">
</head>

<body class="premium-admin ecom-gift-admin">
    <a class="eg-skip-link" href="#main-content">Skip to main content</a>

    <header class="eg-topbar">
        <div class="left-side">
            <button
                type="button"
                class="nav-toggle"
                id="nav-toggle"
                aria-label="Open navigation menu"
                aria-expanded="false"
                aria-controls="admin-nav-drawer">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="logo">
                <a href="/dashboard/" aria-label="<?php echo htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8'); ?> dashboard home">
                    <img src="<?php echo htmlspecialchars(SITE_LOGO, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8'); ?>">
                </a>
            </div>
        </div>

        <div class="right-side eg-profile-menu" id="eg-profile-menu">
            <button
                type="button"
                class="eg-profile-trigger"
                id="profile-menu-trigger"
                aria-expanded="false"
                aria-haspopup="menu"
                aria-controls="profile-menu-list">
                <span class="eg-profile-welcome">
                    <span class="eg-profile-greeting">Welcome, </span>
                    <strong><?php echo htmlspecialchars(PROFILE_DISPLAY_NAME, ENT_QUOTES, 'UTF-8'); ?></strong>
                </span>
                <span class="profile-img">
                    <img
                        src="<?php echo htmlspecialchars((PROFILE_IMAGE != '') ? PROFILE_IMAGE : IMG_LIBRARY . 'default-profile.png', ENT_QUOTES, 'UTF-8'); ?>"
                        alt=""
                        width="30"
                        height="30">
                </span>
                <i class="fas fa-chevron-down eg-profile-caret" aria-hidden="true"></i>
            </button>
            <div id="eg-profile-backdrop" class="eg-profile-backdrop" hidden></div>
            <ul id="profile-menu-list" class="eg-profile-dropdown" role="menu" aria-labelledby="profile-menu-trigger" hidden>
                <li class="eg-profile-sheet-header" role="presentation">
                    <span class="eg-sheet-handle" aria-hidden="true"></span>
                    <strong><?php echo htmlspecialchars(PROFILE_DISPLAY_NAME, ENT_QUOTES, 'UTF-8'); ?></strong>
                    <small>Administrator</small>
                </li>
                <li role="none"><a role="menuitem" href="setting/?type=profile"><i class="fas fa-user" aria-hidden="true"></i> My Profile</a></li>
                <li role="none"><a role="menuitem" href="setting/?type=configuration"><i class="fas fa-tools" aria-hidden="true"></i> Configuration</a></li>
                <li role="none"><a role="menuitem" href="/logout/"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Log Out</a></li>
            </ul>
        </div>
    </header>

    <aside id="admin-nav-drawer" class="Drawer eg-drawer" popover="manual" role="dialog" aria-label="Dashboard navigation">
        <div class="Drawer-scroller">
            <div class="Drawer-sheet">
                <div class="eg-drawer-header">
                    <span class="eg-drawer-title">Menu</span>
                    <button
                        type="button"
                        id="drawer-close"
                        class="eg-drawer-close"
                        aria-label="Close navigation menu"
                        autofocus>
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="nav-search-wrapper">
                    <label for="side-nav-search">Quick Navigation</label>
                    <div class="nav-search-input">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <input type="search" id="side-nav-search" placeholder="Search menu (/)" autocomplete="off">
                        <button
                            type="button"
                            id="side-nav-search-clear"
                            class="eg-search-clear"
                            aria-label="Clear menu filter"
                            hidden>
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div id="nav-search-status" class="visually-hidden" aria-live="polite"></div>
                </div>

                <nav class="side-nav" aria-label="Primary dashboard sections">
                    <ul>
                        <?php foreach ($egNavSections as $egSec):
                            $egHasChildren = !empty($egSec['children']);
                            $egSubId = 'eg-sub-' . $egSec['id'];
                            $egIsActive = !empty($egSec['active']);
                        ?>
                            <li<?php echo $egIsActive ? ' class="active"' : ''; ?>>
                                <?php if ($egHasChildren): ?>
                                    <div class="eg-nav-row">
                                        <a class="eg-nav-link" href="<?php echo htmlspecialchars($egSec['href'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($egIsActive && empty($_GET['type'])) ? ' aria-current="page"' : ''; ?>>
                                            <i class="<?php echo htmlspecialchars($egSec['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i>
                                            <span class="eg-nav-label"><?php echo htmlspecialchars($egSec['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </a>
                                        <button
                                            type="button"
                                            class="eg-nav-sub-btn"
                                            aria-expanded="<?php echo $egIsActive ? 'true' : 'false'; ?>"
                                            aria-controls="<?php echo htmlspecialchars($egSubId, ENT_QUOTES, 'UTF-8'); ?>"
                                            aria-label="Toggle <?php echo htmlspecialchars($egSec['label'], ENT_QUOTES, 'UTF-8'); ?> submenu">
                                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <ul id="<?php echo htmlspecialchars($egSubId, ENT_QUOTES, 'UTF-8'); ?>" class="eg-submenu" <?php echo $egIsActive ? '' : ' hidden'; ?>>
                                        <?php foreach ($egSec['children'] as $egChild): ?>
                                            <li>
                                                <a href="<?php echo htmlspecialchars($egChild['href'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <i class="<?php echo htmlspecialchars($egChild['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i>
                                                    <span><?php echo htmlspecialchars($egChild['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="eg-nav-row">
                                        <a class="eg-nav-link" href="<?php echo htmlspecialchars($egSec['href'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $egIsActive ? ' aria-current="page"' : ''; ?>>
                                            <i class="<?php echo htmlspecialchars($egSec['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i>
                                            <span class="eg-nav-label"><?php echo htmlspecialchars($egSec['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                    </ul>
                    <div id="nav-search-empty" class="eg-nav-empty" hidden>No matching menu items.</div>
                </nav>
            </div>
        </div>
    </aside>

    <main id="main-content" tabindex="-1">
        <div class="container">