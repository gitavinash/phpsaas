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
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>all.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>ui/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>image-uploader/css/image-uploader.min.css">
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
                        <div class="profile-img"><img
                                src="<?php echo ((PROFILE_IMAGE != '') ? PROFILE_IMAGE : IMG_LIBRARY . 'default-profile.png'); ?>"
                                alt=""></div>
                    </a>
                    <ul>
                        <li><a href="setting/?type=profile"><i class="fas fa-user-cog"></i> Edit Profile</a></li>
                        <li><a href="setting/?type=password"><i class="fas fa-key"></i> Change Password</a></li>
                        <li><a href="/logout/"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </header>

    <aside>
        <nav class="side-nav">
            <ul>
                <li <?php echo (($postType == '') ? 'class="active"' : '') ?>><a href="/dashboard/"><i
                            class="fas fa-tachometer-alt"></i> Dashboard</a></li>

                <li <?php echo (($postType == 'page') ? 'class="active"' : '') ?>>
                    <a href="page/?type=list"><i class="fas fa-file"></i> Page</a>
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
                <li <?php echo ((ADMIN_BASE_URL == '/visa_packages/') ? 'class="active"' : '') ?>>
                    <a href="visa_packages/?type=list&status=all"><i class="fas fa-globe"></i> Visa Packages</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="visa_packages/?type=list&status=all"><i class="fas fa-list"></i> All
                                    packages</a>
                            </li>
                            <li><a href="visa_packages/?type=add&editor=true"><i class="fas fa-plus"></i>Add
                                    visa packages</a></li>
                            <li><a href="visa_packages/?type=category"><i class="fas fa-tag"></i>Categories</a></li>
                            <!-- <li><a href="visa_packages/?type=tags"><i class="fas fa-tags"></i>Tags</a></li> -->
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="visa_packages/?type=list&status=all"><i class="fas fa-list"></i> All
                                    packages</a>
                            </li>
                            <li><a href="visa_packages/?type=add&editor=true"><i class="fas fa-plus"></i>Add new
                                    packages</a></li>
                            <li><a href="visa_packages/?type=category"><i class="fas fa-tag"></i>Categories</a></li>
                            <!-- <li><a href="visa_packages/?type=tags"><i class="fas fa-tags"></i>Tags</a></li> -->
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

                <li <?php echo ((ADMIN_BASE_URL == '/appearance/') ? 'class="active"' : '') ?>>
                    <a href="appearance/?type=configuration"><i class="fas fa-paint-brush"></i> Appearance</a>
                    <div class="side-menu">
                        <ul>
                            <!-- <li><a href="appearance/?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li> -->
                            <li><a href="appearance/?type=sliders"><i class="fas fa-images"></i>
                                    Sliders</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <!-- <li><a href="appearance/?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li> -->
                            <li><a href="appearance/?type=sliders"><i class="fas fa-images"></i>
                                    Sliders</a></li>
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
                            <li><a href="setting/?type=password"><i class="fas fa-key"></i> Change Password</a></li>
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
                            <li><a href="setting/?type=password"><i class="fas fa-key"></i> Change Password</a></li>
                            <li><a href="setting/?type=profile"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a href="setting/?type=redirection&status=all"><i class="fas fa-external-link-alt"></i>
                                    URL redirection</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </aside>

    <main>

        <div class="container">