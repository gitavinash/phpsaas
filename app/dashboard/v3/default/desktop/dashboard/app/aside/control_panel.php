<?php

if ($site_access_type == 'administrator' || $site_access_type == 'superwiser' || $site_access_type == 'both') { ?>
    <li class="header-section-name">
        <p>Inventory<span>Inventory management.</span></p>
    </li>

    <li <?php echo ((strpos(ADMIN_BASE_URL, '/inventory/') !== false) ? 'class="active"' : '') ?>>
        <a href="inventory/"><i class="fi fi-rr-inventory-alt"></i> Inventory</a>
        <div class="side-menu">
            <ul>
                <li><a href="inventory/list/"><i class="fi-rr-apps-sort"></i> All Inventory</a></li>
                <li><a href="inventory/add/"><i class="fi fi-rr-add"></i> Add new Product</a></li>
                <li><a href="inventory/category/"><i class="fi fi-rr-category-alt"></i> Product Category</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="inventory/list/"><i class="fi-rr-apps-sort"></i> All Inventory</a></li>
                <li><a href="inventory/add/"><i class="fi fi-rr-add"></i> Add new Product</a></li>
                <li><a href="inventory/category/"><i class="fi fi-rr-category-alt"></i> Product Category</a></li>
            </ul>
        </div>
    </li>

    <li <?php echo ((strpos(ADMIN_BASE_URL, '/restock/') !== false) ? 'class="active"' : '') ?>>
        <a href="restock/"><i class="fi fi-rr-undo"></i> Restock</a>
        <div class="side-menu">
            <ul>
                <li><a href="restock/list/"><i class="fi fi-rr-apps-sort"></i> All requests</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="restock/list/"><i class="fi fi-rr-apps-sort"></i> All requests</a></li>
            </ul>
        </div>
    </li>

<?php
}

?>

<?php

if ($site_access_type == 'administrator') { ?>

    <li class="header-section-name">
        <p>Business Hub<span>Control Your Business</span></p>
    </li>

    <li <?php echo ((strpos(ADMIN_BASE_URL, '/company/') !== false) ? 'class="active"' : '') ?>>
        <a href="company/"><i class="fi fi-rr-building"></i> Company</a>
        <div class="side-menu">
            <ul>
                <li><a href="company/list/"><i class="fi-rr-apps-sort"></i> All companies</a></li>
                <li><a href="company/add/"><i class="fi fi-rr-add"></i> Add new company</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="company/list/"><i class="fi-rr-apps-sort"></i> All companies</a></li>
                <li><a href="company/add/"><i class="fi fi-rr-add"></i> Add new company</a></li>
            </ul>
        </div>
    </li>

    <li <?php echo ((strpos(ADMIN_BASE_URL, '/warehouse/') !== false) ? 'class="active"' : '') ?>>
        <a href="warehouse/"><i class="fi fi-rr-warehouse-alt"></i> Warehouse</a>
        <div class="side-menu">
            <ul>
                <li><a href="warehouse/list/"><i class="fi-rr-apps-sort"></i> All warehouses</a></li>
                <li><a href="warehouse/add/"><i class="fi fi-rr-add"></i> Add new warehouse</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="warehouse/list/"><i class="fi-rr-apps-sort"></i> All warehouses</a></li>
                <li><a href="warehouse/add/"><i class="fi fi-rr-add"></i> Add new warehouse</a></li>
            </ul>
        </div>
    </li>

    <li <?php echo ((strpos(ADMIN_BASE_URL, '/supplier/') !== false) ? 'class="active"' : '') ?>>
        <a href="supplier/"><i class="fi fi-rr-shipping-fast"></i> Supplier</a>
        <div class="side-menu">
            <ul>
                <li><a href="supplier/list/"><i class="fi-rr-apps-sort"></i> All suppliers</a></li>
                <li><a href="supplier/add/"><i class="fi fi-rr-add"></i> Add new supplier</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="supplier/list/"><i class="fi-rr-apps-sort"></i> All suppliers</a></li>
                <li><a href="supplier/add/"><i class="fi fi-rr-add"></i> Add new supplier</a></li>
            </ul>
        </div>
    </li>

<?php
}

?>

<!-- SYSTEM & SETTINGS (Always at end of menus) -->
<?php if ($site_access_type == 'administrator' || $site_access_type == 'digital_marketing') { ?>
    <li class="header-section-name">
        <p>System &amp; Settings<span>Team, Policies &amp; Configuration</span></p>
    </li>

    <?php if ($site_access_type == 'administrator') { ?>
        <li <?php echo (strpos($currentUrl, '/user/') !== false ? 'class="active"' : ''); ?>>
            <a href="/dashboard/user/list/"><i class="fi fi-rr-users-alt"></i> Team &amp; Users</a>
            <div class="side-menu">
                <ul>
                    <li><a href="/dashboard/user/list/"><i class="fi-rr-apps-sort"></i> All Team Members</a></li>
                    <li><a href="/dashboard/user/add/"><i class="fi fi-rr-user-add"></i> Add Team Member</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="/dashboard/user/list/"><i class="fi-rr-apps-sort"></i> All Team Members</a></li>
                    <li><a href="/dashboard/user/add/"><i class="fi fi-rr-user-add"></i> Add Team Member</a></li>
                </ul>
            </div>
        </li>

        <li <?php echo (strpos($currentUrl, '/company/') !== false ? 'class="active"' : ''); ?>>
            <a href="/dashboard/company/list/"><i class="fi fi-rr-shop"></i> Companies &amp; Branches</a>
            <div class="side-menu">
                <ul>
                    <li><a href="/dashboard/company/list/"><i class="fi-rr-apps-sort"></i> All Companies</a></li>
                    <li><a href="/dashboard/company/add/"><i class="fi fi-rr-add"></i> Add New Company</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="/dashboard/company/list/"><i class="fi-rr-apps-sort"></i> All Companies</a></li>
                    <li><a href="/dashboard/company/add/"><i class="fi fi-rr-add"></i> Add New Company</a></li>
                </ul>
            </div>
        </li>

        <li <?php echo (strpos($currentUrl, '/policy/') !== false ? 'class="active"' : ''); ?>>
            <a href="/dashboard/policy/"><i class="fi fi-rr-memo"></i> Company Policies</a>
            <div class="side-menu">
                <ul>
                    <li><a href="/dashboard/policy/list/"><i class="fi-rr-apps-sort"></i> All Policies</a></li>
                    <li><a href="/dashboard/policy/add/"><i class="fi fi-rr-add"></i> Add New Policy</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="/dashboard/policy/list/"><i class="fi-rr-apps-sort"></i> All Policies</a></li>
                    <li><a href="/dashboard/policy/add/"><i class="fi fi-rr-add"></i> Add New Policy</a></li>
                </ul>
            </div>
        </li>
    <?php } ?>

    <?php if (USER_TYPE == 'administrator') { ?>
        <li <?php echo (strpos($currentUrl, '/setting/') !== false ? 'class="active"' : ''); ?>>
            <a href="/dashboard/setting/"><i class="fi fi-rr-settings"></i> Settings</a>
            <div class="side-menu">
                <ul>
                    <li><a href="/dashboard/setting/"><i class="fi-rr-apps-sort"></i> All Settings</a></li>
                    <li><a href="/dashboard/setting/app/"><i class="fi fi-rr-toggle-on"></i> Features &amp; Modules</a></li>
                    <li><a href="/dashboard/setting/configuration/"><i class="fi fi-rr-settings-sliders"></i> Configuration</a></li>
                    <li><a href="/dashboard/setting/contact/"><i class="fi fi-rr-phone-call"></i> Contact Details</a></li>
                    <li><a href="/dashboard/setting/notifications/"><i class="fi fi-rr-bell"></i> Notifications</a></li>
                    <li><a href="/dashboard/setting/bank/"><i class="fi fi-rr-bank"></i> Bank &amp; Payments</a></li>
                    <li><a href="/dashboard/setting/seo/"><i class="fi fi-rr-search-alt"></i> SEO Settings</a></li>
                    <li><a href="/dashboard/setting/profile/"><i class="fi fi-rr-user"></i> My Profile</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li><a href="/dashboard/setting/"><i class="fi-rr-apps-sort"></i> All Settings</a></li>
                    <li><a href="/dashboard/setting/app/"><i class="fi fi-rr-toggle-on"></i> Features &amp; Modules</a></li>
                    <li><a href="/dashboard/setting/configuration/"><i class="fi fi-rr-settings-sliders"></i> Configuration</a></li>
                </ul>
            </div>
        </li>
    <?php } ?>
<?php } ?>