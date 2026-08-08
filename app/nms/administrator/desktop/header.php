<?php
$postType = mysqli_real_escape_string($connection, rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'));
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
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>image-uploader/css/image-uploader.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/datatables.min.css">
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
            <label for="side-nav-search">Quick Navigation</label>
            <div class="nav-search-input">
                <i class="fas fa-search"></i>
                <input type="text" id="side-nav-search" placeholder="Search dashboard menu">
            </div>
        </div>
        <nav class="side-nav">
            <ul>
                <li <?php echo (($postType == '') ? 'class="active"' : '') ?>><a href="/dashboard/"><i
                            class="fas fa-tachometer-alt"></i> Dashboard</a></li>

                <li <?php echo (($postType == 'page') ? 'class="active"' : '') ?>>
                    <a href="page/?type=list&status=all"><i class="fas fa-file"></i> Page</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="page/?type=list&status=all"><i class="fas fa-list"></i> All Pages</a></li>
                            <li><a href="page/?type=add&editor=true"><i class="fas fa-plus"></i>Add New Page</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="page/?type=list&status=all"><i class="fas fa-list"></i> All Pages</a></li>
                            <li><a href="page/?type=add&editor=true"><i class="fas fa-plus"></i>Add New Page</a></li>
                        </ul>
                    </div>
                </li>

                <li <?php echo (($postType == 'news') ? 'class="active"' : '') ?>>
                    <a href="news/?type=list&status=all"><i class="fas fa-file-alt"></i> News</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="news/?type=list&status=all"><i class="fas fa-list"></i> All news</a></li>
                            <li><a href="news/?type=add&editor=true"><i class="fas fa-plus"></i>Add new news</a></li>
                            <li><a href="news/?type=add&editor=true&theme=gallery"><i class="fas fa-images"></i>Add new
                                    gallery news</a></li>
                            <li><a href="news/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="news/?type=list&status=all"><i class="fas fa-list"></i> All news</a></li>
                            <li><a href="news/?type=add&editor=true"><i class="fas fa-plus"></i>Add new news</a></li>
                            <li><a href="news/?type=add&editor=true&theme=gallery"><i class="fas fa-images"></i>Add new
                                    gallery news</a></li>
                            <li><a href="news/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                </li>

                <li <?php echo (($postType == 'testimonial') ? 'class="active"' : '') ?>>
                    <a href="testimonial/?type=list&status=all"><i class="fas fa-comments"></i>
                        Testimonials</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="testimonial/?type=list&status=all"><i class="fas fa-list"></i> All
                                    testimonials</a>
                            </li>
                            <li><a href="testimonial/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    testimonial</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="testimonial/?type=list&status=all"><i class="fas fa-list"></i> All
                                    testimonials</a>
                            </li>
                            <li><a href="testimonial/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    testimonial</a></li>
                        </ul>
                    </div>
                </li>

                <li <?php echo ((ADMIN_BASE_URL == '/newsletter/') ? 'class="active"' : '') ?>>
                    <a href="newsletter/?type=list&status=all"><i class="fas fa-rss"></i> Newsletters</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="newsletter/?type=list&status=all"><i class="fas fa-list"></i> All
                                    Subscribers</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="newsletter/?type=list&status=all"><i class="fas fa-list"></i> All
                                    Subscribers</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo ((ADMIN_BASE_URL == '/subscription/') ? 'class="active"' : '') ?>>
                    <a href="subscription/?type=list&status=all"><i class="fas fa-crown"></i> Subscription</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="subscription/?type=list&status=all"><i class="fas fa-list"></i> All
                                    Subscriptions</a></li>
                            <li><a href="subscription/?type=add&editor=true"><i class="fas fa-plus"></i>Add New
                                    Subscription</a></li>
                            <li><a href="subscription/?type=category"><i class="fas fa-tag"></i>Categories</a></li>
                            <li><a href="subscription/?type=subscriber"><i class="fas fa-users"></i>Manage
                                    Subscribers</a></li>
                            <li><a href="subscription/?type=all-subscriber"><i class="fas fa-users"></i>Manage
                                    All Subscribers</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="subscription/?type=list&status=all"><i class="fas fa-list"></i> All
                                    Subscriptions</a></li>
                            <li><a href="subscription/?type=add&editor=true"><i class="fas fa-plus"></i>Add New
                                    Subscription</a></li>
                            <li><a href="subscription/?type=category"><i class="fas fa-tag"></i>Categories</a></li>
                            <li><a href="subscription/?type=subscriber"><i class="fas fa-users"></i>Manage
                                    Subscribers</a></li>
                            <li><a href="subscription/?type=all-subscriber"><i class="fas fa-users"></i>Manage
                                    All Subscribers</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo ((ADMIN_BASE_URL == '/media/') ? 'class="active"' : '') ?>>
                    <a href="media/?type=list"><i class="fas fa-folder-open"></i> Media</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="media/?type=list&status=all"><i class="fas fa-list"></i> All media</a></li>
                            <li><a href="media/?type=add"><i class="fas fa-plus"></i> Add new media</a></li>
                            <li><a href="media/?type=list&media=image"><i class="fas fa-images"></i> Images</a></li>
                            <li><a href="media/?type=list&media=video"><i class="fas fa-video"></i> Videos</a></li>
                            <li><a href="media/?type=list&media=docs"><i class="fas fa-copy"></i> Documents</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="media/?type=list&status=all"><i class="fas fa-list"></i> All media</a></li>
                            <li><a href="media/?type=add"><i class="fas fa-plus"></i> Add new media</a></li>
                            <li><a href="media/?type=list&media=image"><i class="fas fa-images"></i> Images</a></li>
                            <li><a href="media/?type=list&media=video"><i class="fas fa-video"></i> Videos</a></li>
                            <li><a href="media/?type=list&media=docs"><i class="fas fa-copy"></i> Documents</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo (($postType == 'gallery') ? 'class="active"' : '') ?>>
                    <a href="gallery/?type=list&status=all"><i class="fas fa-images"></i> Gallery</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="gallery/?type=list&status=all"><i class="fas fa-list"></i> All galleries</a>
                            </li>
                            <li><a href="gallery/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    gallery</a>
                            </li>
                            <li><a href="gallery/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="gallery/?type=list&status=all"><i class="fas fa-list"></i> All galleries</a>
                            </li>
                            <li><a href="gallery/?type=add&editor=true"><i class="fas fa-plus"></i>Add
                                    new gallery</a></li>
                            <li><a href="gallery/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo (($postType == 'media') ? 'class="active"' : '') ?>>
                    <a href="media/?type=list"><i class="fas fa-folder-open"></i> Media</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="media/?type=list"><i class="fas fa-list"></i> All Media</a></li>
                            <li><a href="media/?type=list&media=image"><i class="fas fa-images"></i> Images</a></li>
                            <li><a href="media/?type=list&media=video"><i class="fas fa-video"></i> Videos</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="media/?type=list"><i class="fas fa-list"></i> All Media</a></li>
                            <li><a href="media/?type=list&media=image"><i class="fas fa-images"></i> Images</a></li>
                            <li><a href="media/?type=list&media=video"><i class="fas fa-video"></i> Videos</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo (($postType == 'setting') ? 'class="active"' : '') ?>>
                    <a href="setting/?type=configuration"><i class="fas fa-cog"></i> Settings</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="setting/?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li>
                            <li><a href="setting/?type=configuration"><i class="fas fa-tools"></i> Configuration</a>
                            </li>
                            <li><a href="setting/?type=profile"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a href="setting/?type=redirection&status=all"><i class="fas fa-external-link-alt"></i>
                                    URL redirection</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="setting/?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li>
                            <li><a href="setting/?type=configuration"><i class="fas fa-tools"></i> Configuration</a>
                            </li>
                            <li><a href="setting/?type=profile"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a href="setting/?type=redirection&status=all"><i class="fas fa-external-link-alt"></i>
                                    URL redirection</a></li>
                        </ul>
                    </div>
                </li>
                <?php

                if ($site_access_type == 'administrator') { ?>

                    <li <?php echo ((ADMIN_BASE_URL == '/user/') ? 'class="active"' : '') ?>>
                        <a href="user/?type=list&status=all"><i class="fas fa-users"></i> Manage Users</a>
                        <div class="side-menu">
                            <ul>
                                <li><a href="user/?type=list&status=all"><i class="fas fa-list"></i> All users</a>
                                </li>
                                <li><a href="user/?type=add"><i class="fas fa-plus"></i> Add new user</a></li>
                                <li><a href="user/?type=sessions"><i class="fas fa-bolt"></i> Manage sessions</a></li>
                            </ul>
                        </div>
                        <div class="bottom-menu">
                            <ul>
                                <li><a href="user/?type=list&status=all"><i class="fas fa-list"></i> All users</a>
                                </li>
                                <li><a href="user/?type=add"><i class="fas fa-plus"></i> Add new user</a></li>
                                <li><a href="user/?type=sessions"><i class="fas fa-bolt"></i> Manage sessions</a></li>
                            </ul>
                        </div>
                    </li>
                <?php
                }

                ?>
            </ul>
        </nav>
    </aside>

    <div class="mobile-nav-overlay" id="nav-overlay"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var menuSearch = document.getElementById('side-nav-search');
            var menuRoot = document.querySelector('nav.side-nav > ul');

            if (menuSearch && menuRoot) {
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

                        item.style.display = (searchTerm === '' || searchableText.indexOf(searchTerm) !== -
                                1) ? '' :
                            'none';
                    }
                });
            }
        });
    </script>

    <main>

        <div class="container">