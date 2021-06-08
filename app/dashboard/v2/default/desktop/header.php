<?php
	$postType=rtrim(ltrim(ADMIN_BASE_URL, '/'), '/');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/dashboard/">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo SITE_ICON ?>">
    <title><?php echo $dashboardPageTitle.' | '.SITE_NAME; ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_v2.min.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>all.min.css"> -->
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>uicons-regular-rounded.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY?>ui/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/datatables.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300;400;600&display=swap"
        media="screen">

</head>

<body>

    <header>
        <div class="left-side">
            <ul>
                <li>
                    <!-- <a href="#" class="close-nav" id="closeNav"><i class="fi-rr-settings-sliders"></i></a> -->
                </li>
            </ul>
        </div>
        <div class="right-side">
            <ul>
                <!-- <li><a href="#"><i class="fi-rr-bell"></i></a></li> -->
                <li>
                    <a>Welcome, <strong><?php echo PROFILE_DISPLAY_NAME ?></strong>
                        <div class="profile-img"><img
                                src="<?php echo ((PROFILE_IMAGE!='') ? PROFILE_IMAGE : IMG_LIBRARY.'default-profile.png' ); ?>"
                                alt=""></div>
                    </a>
                    <ul>
                        <li><a href="settings/profile/"><i class="fi-rr-portrait"></i> Edit Profile</a></li>
                        <li><a href="settings/password/"><i class="fi-rr-key"></i> Change Password</a></li>
                        <li><a href="/logout/"><i class="fi-rr-power"></i> Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </header>

    <aside>
        <div class="logo"><a href="/dashboard/"><img src="<?php echo SITE_LOGO ?>" alt=""></a></div>
        <nav class="side-nav">
            <ul>
                <li <?php echo ((ADMIN_BASE_URL=='/') ? 'class="active"' : '' ) ?>><a href="/dashboard/"><i
                            class="fi-rr-home"></i> Dashboard</a></li>
                <li <?php echo ((strpos(ADMIN_BASE_URL, '/orders/')!==false) ? 'class="active"' : '' ) ?>>
                    <a href="orders/"><i class="fi-rr-box-alt"></i> Manage Order</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="orders/list/"><i class="fi-rr-apps-sort"></i> All order</a></li>
                            <li><a href="orders/list/?status=1"><i class="fi-rr-check"></i>
                                    Confirm order</a>
                            </li>
                            <li><a href="orders/list/?status=2"><i class="fi-rr-hourglass-end"></i> Pending Order</a>
                            </li>
                            <li><a href="orders/list/?status=3"><i class="fi-rr-cross"></i> Cancel order</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="orders/list/"><i class="fi-rr-apps-sort"></i> All order</a></li>
                            <li><a href="orders/list/?status=1"><i class="fi-rr-check"></i>
                                    Confirm order</a>
                            </li>
                            <li><a href="orders/list/?status=2"><i class="fi-rr-hourglass-end"></i> Pending Order</a>
                            </li>
                            <li><a href="orders/list/?status=3"><i class="fi-rr-cross"></i> Cancel order</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo ((strpos(ADMIN_BASE_URL, '/product/')!==false) ? 'class="active"' : '' ) ?>>
                    <a href="product/list/"><i class="fi-rr-box"></i> Product</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="product/list/"><i class="fi-rr-apps-sort"></i> All
                                    product</a>
                            </li>
                            <li><a href="product/add/"><i class="fi-rr-add"></i>Add new
                                    product</a></li>
                            <li><a href="product/category/"><i class="fi-rr-bookmark"></i>Categories</a>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="product/list/"><i class="fi-rr-apps-sort"></i> All
                                    product</a>
                            </li>
                            <li><a href="product/add/"><i class="fi-rr-add"></i>Add new
                                    product</a></li>
                            <li><a href="product/category/"><i class="fi-rr-bookmark"></i>Categories</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li <?php echo ((strpos(ADMIN_BASE_URL, '/page/')!==false) ? 'class="active"' : '' ) ?>>
                    <a href="page/list/"><i class="fi-rr-file"></i> Page</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="page/list/"><i class="fi-rr-apps-sort"></i> All
                                    pages</a>
                            </li>
                            <li><a href="page/add/"><i class="fi-rr-add"></i>Add new page</a>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="page/list/"><i class="fi-rr-apps-sort"></i> All
                                    pages</a>
                            </li>
                            <li><a href="page/add/"><i class="fi-rr-add"></i>Add new page</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li <?php echo ((strpos(ADMIN_BASE_URL, '/media/')!==false) ? 'class="active"' : '' ) ?>>
                    <a href="media/list/"><i class="fi-rr-gallery"></i> Media</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="media/list/"><i class="fi-rr-apps-sort"></i> All
                                    media</a>
                            </li>
                            <li><a href="media/add/"><i class="fi-rr-add"></i> Add new media</a></li>
                            <li><a href="media/list/?type=image"><i class="fi-rr-picture"></i> Images</a>
                            </li>
                            <li><a href="media/list/?type=video"><i class="fi-rr-film"></i> Videos</a></li>
                            <li><a href="media/list/?type=docs"><i class="fi-rr-document"></i>
                                    Documents</a>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="media/list/"><i class="fi-rr-apps-sort"></i> All
                                    media</a>
                            </li>
                            <li><a href="media/add/"><i class="fi-rr-add"></i> Add new media</a></li>
                            <li><a href="media/list/?type=image"><i class="fi-rr-picture"></i> Images</a>
                            </li>
                            <li><a href="media/list/?type=video"><i class="fi-rr-film"></i> Videos</a></li>
                            <li><a href="media/list/?type=docs"><i class="fi-rr-document"></i>
                                    Documents</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <?php
					
					if(SITE_ID!=22) { ?>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/coupon/')!==false) ? 'class="active"' : '' ) ?>>
                    <a href="coupon/list/"><i class="fi-rr-ticket"></i> Coupon</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="coupon/list/"><i class="fi-rr-apps-sort"></i> All
                                    coupons</a>
                            </li>
                            <li><a href="coupon/add/"><i class="fi-rr-add"></i> Add new coupon</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="coupon/list/"><i class="fi-rr-apps-sort"></i> All
                                    coupons</a>
                            </li>
                            <li><a href="coupon/add/"><i class="fi-rr-add"></i> Add new coupon</a></li>
                        </ul>
                    </div>
                </li>

                <?php
					}

					?>
                <li <?php echo ((strpos(ADMIN_BASE_URL, '/import/')!==false) ? 'class="active"' : '' ) ?>>
                    <a href="import/"><i class="fi-rr-upload"></i> Import</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="import/product/"><i class="fi-rr-box"></i> Products</a></li>
                            <li><a href="import/post/"><i class="fi-rr-document"></i> Posts</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="import/product/"><i class="fi-rr-box"></i> Products</a></li>
                            <li><a href="import/post/"><i class="fi-rr-document"></i> Posts</a></li>
                        </ul>
                    </div>
                </li>
                <li <?php echo ((strpos(ADMIN_BASE_URL, '/settings/')!==false) ? 'class="active"' : '' ) ?>>
                    <a href="settings/"><i class="fi-rr-settings"></i> Settings</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="settings/menu/"><i class="fi-rr-menu-burger"></i> Menu</a></li>
                            <li><a href="settings/configuration/"><i class="fi-rr-settings-sliders"></i>
                                    Configuration</a>
                            </li>
                            <li><a href="settings/password/"><i class="fi-rr-key"></i> Change password</a></li>
                            <li><a href="settings/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
                            <li><a href="settings/redirection/"><i class="fi-rr-rotate-right"></i>
                                    URL
                                    redirection</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="settings/menu/"><i class="fi-rr-menu-burger"></i> Menu</a></li>
                            <li><a href="settings/configuration/"><i class="fi-rr-settings-sliders"></i>
                                    Configuration</a>
                            </li>
                            <li><a href="settings/password/"><i class="fi-rr-key"></i> Change Password</a></li>
                            <li><a href="settings/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
                            <li><a href="settings/redirection/"><i class="fi-rr-link"></i> URL
                                    redirection</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </aside>

    <main>

        <div class="container">

            <div class="i-m-here-pagination">
                <ul>
                    <li><a href="/dashboard/"><i class="fi-rr-home"></i></a></li>
                    <?php
                    
                        $paginationURL='';

                        for($i=0; $i<sizeof($urlStructure); $i++) {

                            $paginationURL=$paginationURL.'/'.strtolower($urlStructure[$i]);
                            echo "<li><a href='{$paginationURL}/'>".ucwords($urlStructure[$i])."</a></li>";

                        }

                    ?>
                </ul>
            </div>