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
        <nav class="side-nav">
            <ul>
                <li class="nav-section-title">
                    <span>Overview</span>
                </li>
                <li <?php echo (($postType == '') ? 'class="active"' : '') ?>><a href="/dashboard/"><i
                            class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="nav-section-title">
                    <span>Website Content</span>
                </li>
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
                <li <?php echo (($postType == 'post') ? 'class="active"' : '') ?>>
                    <a href="post/?type=list&status=all"><i class="fas fa-file-alt"></i> Post</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="post/?type=list&status=all"><i class="fas fa-list"></i> All post</a></li>
                            <li><a href="post/?type=add&editor=true"><i class="fas fa-plus"></i>Add new post</a></li>
                            <li><a href="post/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="post/?type=list&status=all"><i class="fas fa-list"></i> All post</a></li>
                            <li><a href="post/?type=add&editor=true"><i class="fas fa-plus"></i>Add new post</a></li>
                            <li><a href="post/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo (($postType == 'question') ? 'class="active"' : '') ?>>
                    <a href="question/?type=list&status=all"><i class="fas fa-question"></i> Questions (FAQ's)</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="question/?type=list&status=all"><i class="fas fa-list"></i> All Questionss</a>
                            </li>
                            <li><a href="question/?type=add&editor=true"><i
                                        class="fas fa-plus"></i>Add
                                    new
                                    Questions</a></li>
                            <li><a href="question/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="question/?type=list&status=all"><i class="fas fa-list"></i> All Questionss</a>
                            </li>
                            <li><a href="question/?type=add&editor=true"><i
                                        class="fas fa-plus"></i>Add
                                    new
                                    Questions</a></li>
                            <li><a href="question/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-section-title">
                    <span>Cataloge</span>
                </li>
                <li <?php echo (($postType == 'treatment') ? 'class="active"' : '') ?>>
                    <a href="treatment/?type=list&status=all"><i class="fas fa-medkit"></i> Treatments</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="treatment/?type=list&status=all"><i class="fas fa-list"></i> All
                                    Treatments</a>
                            </li>
                            <li><a href="treatment/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    Treatment</a></li>
                            <li><a href="treatment/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="treatment/?type=list&status=all"><i class="fas fa-list"></i> All
                                    Treatments</a>
                            </li>
                            <li><a href="treatment/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    Treatment</a></li>
                            <li><a href="treatment/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo (($postType == 'technology') ? 'class="active"' : '') ?>>
                    <a href="technology/?type=list&status=all"><i class="fas fa-robot"></i> Technology</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="technology/?type=list&status=all"><i class="fas fa-list"></i> All
                                    treatment</a>
                            </li>
                            <li><a href="technology/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    treatment</a></li>
                            <li><a href="technology/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="technology/?type=list&status=all"><i class="fas fa-list"></i> All
                                    treatment</a>
                            </li>
                            <li><a href="technology/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    treatment</a></li>
                            <li><a href="technology/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo (($postType == 'package') ? 'class="active"' : '') ?>>
                    <a href="package/?type=list&status=all"><i class="fas fa-cubes"></i> Packages</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="package/?type=list&status=all"><i class="fas fa-list"></i> All packages</a>
                            </li>
                            <li><a href="package/?type=add&editor=true"><i class="fas fa-plus"></i> Add New
                                    package</a>
                            </li>
                            <li><a href="package/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="package/?type=list&status=all"><i class="fas fa-list"></i> All packages</a>
                            </li>
                            <li><a href="package/?type=add&editor=true"><i class="fas fa-plus"></i> Add New
                                    package</a>
                            </li>
                            <li><a href="package/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo (($postType == 'offer') ? 'class="active"' : '') ?>>
                    <a href="offer/?type=list&status=all"><i class="fas fa-percent"></i> Latest Offers</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="offer/?type=list&status=all"><i class="fas fa-list"></i> All offers</a>
                            </li>
                            <li><a href="offer/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    offer</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="offer/?type=list&status=all"><i class="fas fa-list"></i> All offers</a>
                            </li>
                            <li><a href="offer/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    offer</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-section-title">
                    <span>Media</span>
                </li>
                <li <?php echo (($postType == 'media') ? 'class="active"' : '') ?>>
                    <a href="media/?type=list"><i class="fas fa-folder-open"></i> Media Library</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="media/?type=list"><i class="fas fa-list"></i> All Media</a></li>
                            <li><a href="media/?type=add"><i class="fas fa-plus"></i> Add New</a></li>
                            <li><a href="media/?type=list&media=image"><i class="fas fa-images"></i> Images</a></li>
                            <li><a href="media/?type=list&media=video"><i class="fas fa-video"></i> Videos</a></li>
                            <li><a href="media/?type=list&media=docs"><i class="fas fa-copy"></i> Documents</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="media/?type=list"><i class="fas fa-list"></i> All Media</a></li>
                            <li><a href="media/?type=add"><i class="fas fa-plus"></i> Add New</a></li>
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
                <li class="nav-section-title">
                    <span>Engagement</span>
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
                <li <?php echo (($postType == 'social_proof') ? 'class="active"' : '') ?>>
                    <a href="social_proof/?type=list&status=all">
                        <i class="fas fa-play-circle"></i> Social Video Hub
                    </a>

                    <div class="side-menu">
                        <ul>
                            <li>
                                <a href="social_proof/?type=list&status=all">
                                    <i class="fas fa-list"></i> All Videos
                                </a>
                            </li>
                            <li>
                                <a href="social_proof/?type=add&editor=true">
                                    <i class="fas fa-plus"></i> Add New Video
                                </a>
                            </li>
                            <li>
                                <a href="social_proof/?type=category">
                                    <i class="fas fa-tags"></i> Video Categories
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="bottom-menu">
                        <ul>
                            <li>
                                <a href="social_proof/?type=list&status=all">
                                    <i class="fas fa-list"></i> All Videos
                                </a>
                            </li>
                            <li>
                                <a href="social_proof/?type=add&editor=true">
                                    <i class="fas fa-plus"></i> Add New Video
                                </a>
                            </li>
                            <li>
                                <a href="social_proof/?type=category">
                                    <i class="fas fa-tags"></i> Video Categories
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-section-title">
                    <span>Organization</span>
                </li>
                <li <?php echo (($postType == 'team') ? 'class="active"' : '') ?>>
                    <a href="team/?type=list&status=all"><i class="fas fa-users"></i>
                        Teams</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="team/?type=list&status=all"><i class="fas fa-list"></i> All
                                    teams</a>
                            </li>
                            <li><a href="team/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    team</a></li>
                            <li><a href="team/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="team/?type=list&status=all"><i class="fas fa-list"></i> All
                                    teams</a>
                            </li>
                            <li><a href="team/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    team</a></li>
                            <li><a href="team/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo (($postType == 'career') ? 'class="active"' : '') ?>>
                    <a href="career/?type=list&status=all"><i class="fas fa-briefcase"></i> Careers</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="career/?type=list&status=all"><i class="fas fa-list"></i> All careers</a>
                            </li>
                            <li><a href="career/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    career</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="career/?type=list&status=all"><i class="fas fa-list"></i> All Careers</a>
                            </li>
                            <li><a href="career/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    career</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-section-title">
                    <span>System</span>
                </li>
                <li <?php echo ((ADMIN_BASE_URL == '/appearance/') ? 'class="active"' : '') ?>>
                    <a href="appearance/?type=themes"><i class="fas fa-paint-brush"></i> Appearance</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="appearance/?type=themes"><i class="fas fa-palette"></i> Themes</a></li>
                            <li><a href="appearance/?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li>
                            <li><a href="appearance/?type=sliders"><i class="fas fa-images"></i>
                                    Sliders</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="appearance/?type=themes"><i class="fas fa-palette"></i> Themes</a></li>
                            <li><a href="appearance/?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li>
                            <li><a href="appearance/?type=sliders"><i class="fas fa-images"></i>
                                    Sliders</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo (($postType == 'widget') ? 'class="active"' : '') ?>>
                    <a href="widget/?type=list&status=all"><i class="fas fa-puzzle-piece"></i> Widgets</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="widget/?type=list&status=all"><i class="fas fa-list"></i> All Widgets</a></li>
                            <li><a href="widget/?type=add&editor=true"><i class="fas fa-plus"></i>Add new Widgets</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="widget/?type=list&status=all"><i class="fas fa-list"></i> All Widgets</a></li>
                            <li><a href="widget/?type=add&editor=true"><i class="fas fa-plus"></i>Add new Widgets</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo (($postType == 'setting') ? 'class="active"' : '') ?>>
                    <a href="setting/?type=configuration"><i class="fas fa-cog"></i> Settings</a>
                    <div class="side-menu">
                        <ul>
                            <!-- <li><a href="setting/?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li> -->
                            <li><a href="setting/?type=configuration"><i class="fas fa-tools"></i> Configuration</a>
                            </li>
                            <li><a href="setting/?type=profile"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a href="setting/?type=redirection&status=all"><i class="fas fa-external-link-alt"></i>
                                    URL redirection</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <!-- <li><a href="setting/?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li> -->
                            <li><a href="setting/?type=configuration"><i class="fas fa-tools"></i> Configuration</a>
                            </li>
                            <li><a href="setting/?type=profile"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a href="setting/?type=redirection&status=all"><i class="fas fa-external-link-alt"></i>
                                    URL redirection</a></li>
                        </ul>
                    </div>
                </li>
                <?php

                if ($site_access_type = 'administrator') { ?>
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
                <?php
                }

                ?>
            </ul>
        </nav>
    </aside>

    <div class="mobile-nav-overlay" id="nav-overlay"></div>
    <main>

        <div class="container">