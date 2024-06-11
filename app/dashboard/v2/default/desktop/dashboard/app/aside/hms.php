<nav class="side-nav">
    <ul>
        <li class="header-section-name">
            <p>Dashboard<span>View performance reports.</span></p>
        </li>

        <li <?php echo ((ADMIN_BASE_URL == '/') ? 'class="active"' : '') ?>><a href="/dashboard/"><i class="fi-rr-home"></i> Dashboard</a></li>

        <li class="header-section-name">
            <p>Properties<span>Manage properties.</span></p>
        </li>

        <li <?php echo ((strpos(ADMIN_BASE_URL, '/vendors/property/') !== false) ? 'class="active"' : '') ?>>
            <a href="vendors/property/"><i class="fi fi-rr-city"></i> Property Vendors</a>
            <div class="side-menu">
                <ul>
                    <li><a href="vendors/property/list/"><i class="fi-rr-apps-sort"></i> All properties</a></li>
                    <li><a href="vendors/property/add/"><i class="fi fi-rr-add"></i> Add new property</a></li>
                    <li><a href="vendors/property/amenities/"><i class="fi fi-rr-star"></i> Manage amenities</a>
                    </li>
                    <li><a href="vendors/property/room/"><i class="fi fi-rr-door-open"></i> Manage room
                            types</a>
                    </li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="vendors/property/list/"><i class="fi-rr-apps-sort"></i> All properties</a></li>
                    <li><a href="vendors/property/add/"><i class="fi fi-rr-add"></i> Add new property</a></li>
                    <li><a href="vendors/property/amenities/"><i class="fi fi-rr-star"></i> Manage amenities</a>
                    </li>
                    <li><a href="vendors/property/room/"><i class="fi fi-rr-door-open"></i> Manage room
                            types</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="header-section-name">
            <p>Setting<span>Manage application.</span></p>
        </li>

        <li <?php echo ((strpos(ADMIN_BASE_URL, '/setting/') !== false) ? 'class="active"' : '') ?>>
            <a href="setting/"><i class="fi-rr-settings"></i> Setting</a>
            <div class="side-menu">
                <ul>
                    <?php
                    if ($site_access_type == 'administrator') { ?>

                        <li><a href="setting/configuration/"><i class="fi-rr-settings"></i> Configuration</a></li>
                    <?php } ?>
                    <li><a href="setting/password/"><i class="fi-rr-key"></i> Change password</a></li>
                    <li><a href="setting/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>

                    <?php

                    if ($site_access_type == 'administrator') { ?>

                        <li><a href="setting/configuration/"><i class="fi-rr-settings"></i> Configuration</a></li>
                    <?php } ?>
                    <li><a href="setting/password/"><i class="fi-rr-key"></i> Change Password</a></li>
                    <li><a href="setting/profile/"><i class="fi-rr-portrait"></i> Profile</a></li>
                </ul>
            </div>
        </li>

        <?php

        if ($site_access_type == 'administrator') { ?>


            <li <?php echo ((strpos(ADMIN_BASE_URL, '/users/') !== false) ? 'class="active"' : '') ?>>
                <a href="users/"><i class="fi fi-rr-user"></i> Users</a>
                <div class="side-menu">
                    <ul>
                        <li><a href="users/list/"><i class="fi-rr-apps-sort"></i> All users</a></li>
                        <li><a href="users/add/"><i class="fi fi-rr-user-add"></i> Add new user</a></li>
                    </ul>
                </div>
                <div class="bottom-menu">
                    <ul>
                        <li><a href="users/list/"><i class="fi-rr-apps-sort"></i> All users</a></li>
                        <li><a href="users/add/"><i class="fi fi-rr-user-add"></i> Add new user</a>
                    </ul>
                </div>
            </li>

        <?php }
        ?>

        <li class="appVersion">
            <p>App version : <?php echo ALEYA_APP_VERSION ?></p>
        </li>
    </ul>
</nav>