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

                <li <?php echo (($postType == 'event') ? 'class="active"' : '') ?>>
                    <a href="event/?type=list&status=all"><i class="fas fa-gopuram"></i> Events</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="event/?type=list&status=all"><i class="fas fa-list"></i> All events</a></li>
                            <li><a href="event/?type=add"><i class="fas fa-plus"></i> Add New event</a></li>
                            <li><a href="event/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="event/?type=list&status=all"><i class="fas fa-list"></i> All events</a></li>
                            <li><a href="event/?type=add"><i class="fas fa-plus"></i> Add New event</a></li>
                            <li><a href="event/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
                        </ul>
                    </div>
                </li>

                <li <?php echo (($postType == 'questions') ? 'class="active"' : '') ?>>
                    <a href="questions/?type=list&status=all"><i class="fas fa-question"></i> Questions</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="questions/?type=list&status=all"><i class="fas fa-list"></i> All Questionss</a>
                            </li>
                            <li><a href="/start-a-discussion/?ref=dashboard&postType=questions" target="_blank"><i
                                        class="fas fa-plus"></i>Add
                                    new
                                    Questions</a></li>
                            <li><a href="questions/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="questions/?type=list&status=all"><i class="fas fa-list"></i> All Questionss</a>
                            </li>
                            <li><a href="/start-a-discussion/?ref=dashboard&postType=questions" target="_blank"><i
                                        class="fas fa-plus"></i>Add
                                    new
                                    Questions</a></li>
                            <li><a href="questions/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                </li>

                <li <?php echo (($postType == 'discussion') ? 'class="active"' : '') ?>>
                    <a href="discussion/?type=list&status=all"><i class="fas fa-comments"></i> Discussion</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="discussion/?type=list&status=all"><i class="fas fa-list"></i> All
                                    Discussions</a></li>
                            <li><a href="/start-a-discussion/?ref=dashboard&postType=discussion" target="_blank">
                                    <i class="fas fa-plus"></i> Add New Discussion</a>
                            </li>
                            <li><a href="discussion/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="discussion/?type=list&status=all"><i class="fas fa-list"></i> All
                                    Discussions</a></li>
                            <li><a href="/start-a-discussion/?ref=dashboard&postType=discussion" target="_blank">
                                    <i class="fas fa-plus"></i> Add New Discussion</a>
                            </li>
                            <li><a href="discussion/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
                        </ul>
                    </div>
                </li>

                <!-- Success Store Menu -->
                <li <?php echo (($postType == 'success_story') ? 'class="active"' : '') ?>>
                    <a href="success_story/?type=list&status=all"><i class="fas fa-star"></i> Success Stories</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="success_story/?type=list&status=all"><i class="fas fa-list"></i> All Success
                                    Stories</a></li>
                            <li><a href="/start-a-discussion/?ref=dashboard&postType=success_story" target="_blank">
                                    <i class="fas fa-plus"></i> Add New Success Story</a>
                            </li>
                            <li><a href="success_story/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="success_story/?type=list&status=all"><i class="fas fa-list"></i> All Success
                                    Stories</a></li>
                            <li><a href="/start-a-discussion/?ref=dashboard&postType=success_story" target="_blank">
                                    <i class="fas fa-plus"></i> Add New Success Story</a>
                            </li>
                            <li><a href="success_story/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
                        </ul>
                    </div>
                </li>

                <!-- Clinic Menu -->
                <li <?php echo (($postType == 'clinic') ? 'class="active"' : '') ?>>
                    <a href="clinic/?type=list&status=all"><i class="fas fa-hospital"></i> Hospital</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="clinic/?type=list&status=all"><i class="fas fa-list"></i> All Clinics</a></li>
                            <li>
                                <a href="/add-new-listing/?ref=dashboard&postType=clinic" target="_blank">
                                    <i class="fas fa-plus"></i> Add New Clinic
                                </a>
                            </li>
                            <li><a href="clinic/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="clinic/?type=list&status=all"><i class="fas fa-list"></i> All Clinics</a></li>
                            <li>
                                <a href="/add-new-listing/?ref=dashboard&postType=clinic" target="_blank">
                                    <i class="fas fa-plus"></i> Add New Clinic
                                </a>
                            </li>
                            <li><a href="clinic/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
                        </ul>
                    </div>
                </li>

                <!-- Doctor Menu -->
                <li <?php echo (($postType == 'doctor') ? 'class="active"' : '') ?>>
                    <a href="doctor/?type=list&status=all"><i class="fas fa-user-md"></i> Doctor</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="doctor/?type=list&status=all"><i class="fas fa-list"></i> All Doctors</a></li>
                            <li>
                                <a href="/add-new-listing/?ref=dashboard&postType=doctor" target="_blank">
                                    <i class="fas fa-plus"></i> Add New Doctor
                                </a>
                            </li>
                            <li><a href="doctor/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="doctor/?type=list&status=all"><i class="fas fa-list"></i> All Doctors</a></li>
                            <li>
                                <a href="/add-new-listing/?ref=dashboard&postType=doctor" target="_blank">
                                    <i class="fas fa-plus"></i> Add New Doctor
                                </a>
                            </li>
                            <li><a href="doctor/?type=category"><i class="fas fa-tags"></i> Categories</a></li>
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

    <main>

        <div class="container">