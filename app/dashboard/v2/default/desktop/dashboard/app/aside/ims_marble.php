<nav class="side-nav">
    <ul>
        <li class="header-section-name">
            <p>Dashboard<span>View performance reports.</span></p>
        </li>

        <li <?php echo ((ADMIN_BASE_URL == '/') ? 'class="active"' : '') ?>><a href="/dashboard/"><i
                    class="fi-rr-home"></i> Dashboard</a></li>

        <?php

        if ($site_access_type == 'administrator' || $site_access_type == 'sales_executive') { ?>
        <li class="header-section-name">
            <p>Leads<span>Lead management.</span></p>
        </li>


        <li <?php echo ((strpos(ADMIN_BASE_URL, '/leads/') !== false) ? 'class="active"' : '') ?>>
            <a href="leads/"><i class="fi fi-rr-chart-network"></i> Leads</a>
            <div class="side-menu">
                <ul>
                    <li><a href="leads/list/"><i class="fi-rr-apps-sort"></i> All leads</a></li>
                    <li><a href="leads/add/"><i class="fi fi-rr-add"></i> Add new lead</a></li>
                    <li><a href="leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed leads</a>
                    </li>
                    <li><a href="leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending leads</a>
                    </li>
                    <li><a href="leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow ups leads</a>
                    </li>
                    <li><a href="leads/list/?leadStatus=4"><i class="fi-rr-cross-circle"></i> Cancelled
                            leads</a>
                    </li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="leads/list/"><i class="fi-rr-apps-sort"></i> All leads</a></li>
                    <li><a href="leads/add/"><i class="fi fi-rr-add"></i> Add new lead</a></li>
                    <li><a href="leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed leads</a>
                    </li>
                    <li><a href="leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending leads</a>
                    </li>
                    <li><a href="leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow ups leads</a>
                    </li>
                    <li><a href="leads/list/?leadStatus=4"><i class="fi-rr-cross-circle"></i> Cancelled
                            leads</a>
                    </li>
                </ul>
            </div>
        </li>

        <li <?php echo ((strpos(ADMIN_BASE_URL, '/sales/') !== false) ? 'class="active"' : '') ?>>
            <a href="sales/"><i class="fi fi-rr-calculator-bill"></i> Sales</a>
            <div class="side-menu">
                <ul>
                    <li><a href="sales/list/"><i class="fi-rr-apps-sort"></i> All sales</a></li>
                    <li><a href="sales/add/"><i class="fi fi-rr-add"></i> Add new sale</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="sales/list/"><i class="fi-rr-apps-sort"></i> All sales</a></li>
                    <li><a href="sales/add/"><i class="ADMfi-rr-add"></i> Add new sale</a></li>
                </ul>
            </div>
        </li>
        <?php
        }

        ?>

        <?php

        if ($site_access_type == 'administrator' || $site_access_type == 'accounts') { ?>

        <li class="header-section-name">
            <p>Sales<span>Sales management.</span></p>
        </li>

        <li <?php echo ((strpos(ADMIN_BASE_URL, '/sales_request/') !== false) ? 'class="active"' : '') ?>>
            <a href="sales_request/"><i class="fi fi-rr-memo-circle-check"></i> Sales requests</a>
            <div class=" side-menu">
                <ul>
                    <li><a href="sales_request/list/"><i class="fi-rr-apps-sort"></i> All requests</a></li>
                    <li><a href="sales_request/add/"><i class="fi fi-rr-hourglass-end"></i> Pending requests</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="sales_request/list/"><i class="fi-rr-apps-sort"></i> All requests</a></li>
                    <li><a href="sales_request/add/"><i class="fi fi-rr-hourglass-end"></i> Pending requests</a></li>
                </ul>
            </div>
        </li>

        <li <?php echo ((strpos(ADMIN_BASE_URL, '/return_request/') !== false) ? 'class="active"' : '') ?>>
            <a href="return_request/"><i class="fi fi-rr-undo"></i> Return requests</a>
            <div class=" side-menu">
                <ul>
                    <li><a href="return_request/list/"><i class="fi-rr-apps-sort"></i> All requests</a></li>
                    <li><a href="return_request/add/"><i class="fi fi-rr-hourglass-end"></i> Pending requests</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="return_request/list/"><i class="fi-rr-apps-sort"></i> All requests</a></li>
                    <li><a href="return_request/add/"><i class="fi fi-rr-hourglass-end"></i> Pending requests</a></li>
                </ul>
            </div>
        </li>

        <?php
        }

        ?>

        <?php

        if ($site_access_type == 'administrator' || $site_access_type == 'superwiser') { ?>
        <li class="header-section-name">
            <p>Inventory<span>Inventory management.</span></p>
        </li>

        <li <?php echo ((strpos(ADMIN_BASE_URL, '/book_keeping/') !== false) ? 'class="active"' : '') ?>>
            <a href="book_keeping/"><i class="fi fi-rr-warehouse-alt"></i> Book keeping</a>
            <div class=" side-menu">
                <ul>
                    <li><a href="book_keeping/list/"><i class="fi-rr-apps-sort"></i> All blocks</a></li>
                    <li><a href="book_keeping/add/"><i class="fi fi-rr-add"></i> Add new block</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="book_keeping/list/"><i class="fi-rr-apps-sort"></i> All blocks</a></li>
                    <li><a href="book_keeping/add/"><i class="fi fi-rr-add"></i> Add new block</a></li>
                </ul>
            </div>
        </li>

        <li <?php echo ((strpos(ADMIN_BASE_URL, '/restock/') !== false) ? 'class="active"' : '') ?>>
            <a href="restock/"><i class="fi fi-rr-undo"></i> Restock</a>
            <div class=" side-menu">
                <ul>
                    <li><a href="restock/list/"><i class="fi-rr-apps-sort"></i> All requests</a></li>
                    <li><a href="restock/add/"><i class="fi fi-rr-hourglass-end"></i> Pending requests</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="restock/list/"><i class="fi-rr-apps-sort"></i> All requests</a></li>
                    <li><a href="restock/add/"><i class="fi fi-rr-hourglass-end"></i> Pending requests</a></li>
                </ul>
            </div>
        </li>
        <?php
        }

        ?>

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