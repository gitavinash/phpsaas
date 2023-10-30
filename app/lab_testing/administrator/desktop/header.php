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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/buttons.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>select2/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>image-uploader/css/image-uploader.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>domenu/css/domenu.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
</head>

<body>

    <header>
        <div class="left-side">
            <ul>
                <li>
                    <div class="logo"><a href="/" target="_blank"><img src="<?php echo SITE_LOGO ?>" alt=""></a></div>
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
                <li <?php echo ((ADMIN_BASE_URL == '/') ? 'class="active"' : '') ?>><a href="/dashboard/"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li <?php echo ((ADMIN_BASE_URL == '/brands/') ? 'class="active"' : '') ?>><a href="brand/?type=category&status=all"><i class="fas fa-asterisk"></i> Brands</a></li>
                <li <?php echo ((ADMIN_BASE_URL == '/diagnostic/') ? 'class="active"' : '') ?>>
                    <a href="diagnostic/?type=list&status=all"><i class="fas fa-flask"></i> Diagnostic Tests</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="diagnostic/?type=list&status=all"><i class="fas fa-list"></i> All tests</a>
                            </li>
                            <li><a href="diagnostic/?type=add&editor=true"><i class="fas fa-plus"></i>Add new test</a>
                            </li>
                            <li><a href="diagnostic/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="diagnostic/?type=list&status=all"><i class="fas fa-list"></i> All tests</a>
                            </li>
                            <li><a href="diagnostic/?type=add&editor=true"><i class="fas fa-plus"></i>Add new test</a>
                            </li>
                            <li><a href="diagnostic/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                </li>

                <!-- <li <?php echo ((ADMIN_BASE_URL == '/packages/') ? 'class="active"' : '') ?>>
                    <a href="packages/?type=list&status=all"><i class="fas fa-cubes"></i> Packages</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="diagnostic/?type=list&status=all"><i class="fas fa-list"></i> All tests</a>
                            </li>
                            <li><a href="diagnostic/?type=add&editor=true"><i class="fas fa-plus"></i>Add new test</a>
                            </li>
                            <li><a href="diagnostic/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="diagnostic/?type=list&status=all"><i class="fas fa-list"></i> All tests</a>
                            </li>
                            <li><a href="diagnostic/?type=add&editor=true"><i class="fas fa-plus"></i>Add new test</a>
                            </li>
                            <li><a href="diagnostic/?type=category"><i class="fas fa-tags"></i>Categories</a></li>
                        </ul>
                    </div>
                </li> -->
                <li <?php echo ((ADMIN_BASE_URL == '/page/') ? 'class="active"' : '') ?>>
                    <a href="page/?type=list&status=all"><i class="fas fa-file"></i> Page</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="page/?type=list&status=all"><i class="fas fa-list"></i> All pages</a></li>
                            <li><a href="page/?type=add&editor=true"><i class="fas fa-plus"></i>Add new page</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="page/?type=list&status=all"><i class="fas fa-list"></i> All pages</a></li>
                            <li><a href="page/?type=add&editor=true"><i class="fas fa-plus"></i>Add new page</a></li>
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
                <li <?php echo ((ADMIN_BASE_URL == '/import/') ? 'class="active"' : '') ?>>
                    <a href="import/?type=product"><i class="fas fa-upload"></i> Import</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="import/?type=lab_tests"><i class="fas fa-flask"></i>
                                    Lab Tests</a></li>
                            <li><a href="import/?type=product&productType=package"><i class="fas fa-medkit"></i>
                                    Test Packages</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="import/?type=lab_tests"><i class="fas fa-flask"></i>
                                    Lab Tests</a></li>
                            <li><a href="import/?type=product&productType=package"><i class="fas fa-medkit"></i>
                                    Test Packages</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo ((ADMIN_BASE_URL == '/setting/') ? 'class="active"' : '') ?>>
                    <a href="setting/?type=configuration"><i class="fas fa-cog"></i> Settings</a>
                    <div class="side-menu">
                        <ul>
                            <!-- <li><a href="setting/?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li> -->
                            <li><a href="setting/?type=configuration"><i class="fas fa-tools"></i> Configuration</a>
                            </li>
                            <li><a href="setting/?type=password"><i class="fas fa-key"></i> Change password</a></li>
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