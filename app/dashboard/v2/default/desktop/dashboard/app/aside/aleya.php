<?php

if ($site_access_type == 'administrator') { ?>

    <li <?php echo ((strpos(ADMIN_BASE_URL, '/report/') !== false) ? 'class="active"' : '') ?>>
        <a href="report/"><i class="fi fi-rr-chart-histogram"></i> Reports</a>
        <div class="side-menu">
            <ul>
                <li><a href="report/employees/"><i class="fi-rr-employee-man-alt"></i> Employees
                        performance</a>
                </li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="report/employees/"><i class="fi-rr-employee-man-alt"></i> Employees
                        performance</a>
            </ul>
        </div>
    </li>

<?php
}

if ($site_access_type == 'administrator' || $site_access_type == 'operations') { ?>

    <li class="header-section-name">
        <p>Vendors<span>Manage vendor.</span></p>
    </li>

    <li <?php echo ((strpos(ADMIN_BASE_URL, '/vendors/transporter/') !== false) ? 'class="active"' : '') ?>>
        <a href="vendors/transporter/"><i class="fi fi-rr-bus"></i> Transporter vendor</a>
        <div class="side-menu">
            <ul>
                <li><a href="vendors/transporter/list/"><i class="fi-rr-apps-sort"></i> All transporters</a>
                </li>
                <li><a href="vendors/transporter/add/"><i class="fi fi-rr-add"></i> Add new transporter</a>
                </li>
                <li><a href="vendors/transporter/vehicle/"><i class="fi fi-rr-car-bus"></i> Manage
                        vehicles</a>
                </li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="vendors/transporter/list/"><i class="fi-rr-apps-sort"></i> All transporters</a></li>
                <li><a href="vendors/transporter/add/"><i class="fi fi-rr-add"></i> Add new transporter</a>
                </li>
                <li><a href="vendors/transporter/vehicle/"><i class="fi fi-rr-car-bus"></i> Manage
                        vehicles</a>
            </ul>
        </div>
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
                <li><a href="vendors/property/upload/"><i class="fi fi-rr-file-upload"></i> Upload properties</a></li>
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
                <li><a href="vendors/property/upload/"><i class="fi fi-rr-file-upload"></i> Upload properties</a></li>
            </ul>
        </div>
    </li>
    <!--
                <li <?php echo ((strpos(ADMIN_BASE_URL, '/vendors/dmc/') !== false) ? 'class="active"' : '') ?>>
                    <a href="vendors/dmc/"><i class="fi fi-rr-shop"></i> DMC Vendor</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="vendors/dmc/list/"><i class="fi-rr-apps-sort"></i> All DMC's</a></li>
                            <li><a href="vendors/dmc/add/"><i class="fi fi-rr-add"></i> Add New DMC</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="vendors/dmc/list/"><i class="fi-rr-apps-sort"></i> All DMC's</a></li>
                            <li><a href="vendors/dmc/add/"><i class="fi fi-rr-add"></i> Add New DMC</a></li>
                        </ul>
                    </div>
                </li> -->

    <li class="header-section-name">
        <p>Packages<span>Manage all tour packages.</span></p>
    </li>

    <li <?php echo ((strpos(ADMIN_BASE_URL, '/packages/domestic/') !== false) ? 'class="active"' : '') ?>>
        <a href="packages/domestic/"><i class="fi fi-rr-suitcase-alt"></i> Domestic packages</a>
        <div class="side-menu">
            <ul>
                <li><a href="packages/domestic/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
                </li>
                <li><a href="packages/domestic/add/"><i class="fi fi-rr-add"></i> Add new package</a>
                </li>
                <li><a href="packages/domestic/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
                </li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="packages/domestic/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
                </li>
                <li><a href="packages/domestic/add/"><i class="fi fi-rr-add"></i> Add new package</a>
                </li>
                <li><a href="packages/domestic/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
                </li>
            </ul>
        </div>
    </li>

    <li <?php echo ((strpos(ADMIN_BASE_URL, '/international/') !== false) ? 'class="active"' : '') ?>>
        <a href="packages/international/"><i class="fi fi-rr-earth-asia"></i> International packages</a>
        <div class="side-menu">
            <ul>
                <li><a href="packages/international/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
                </li>
                <li><a href="packages/international/add/"><i class="fi fi-rr-add"></i> Add new package</a>
                </li>
                <li><a href="packages/international/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
                </li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="packages/international/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
                </li>
                <li><a href="packages/international/add/"><i class="fi fi-rr-add"></i> Add new package</a>
                </li>
                <li><a href="packages/international/location/"><i class="fi fi-rr-marker"></i> Destinations</a>
                </li>
            </ul>
        </div>
    </li>
<?php
}

?>


<li class="header-section-name">
    <p>Leads<span>Manage leads.</span></p>
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

<?php

if ($site_access_type == 'administrator') { ?>

    <!-- <li class="header-section-name">
                    <p>Clients<span>Manage clients.</span></p>
                </li>

                <li <?php echo ((strpos(ADMIN_BASE_URL, '/clients/') !== false) ? 'class="active"' : '') ?>>
                    <a href="clients/"><i class="fi fi-rr-hr-group"></i> Clients</a>
                    <div class="side-menu">
                        <ul>
                            <li><a href="clients/list/"><i class="fi-rr-apps-sort"></i> All clients</a></li>
                            <li><a href="clients/add/"><i class="fi fi-rr-user-add"></i> Add new client</a></li>
                        </ul>
                    </div>
                    <div class="bottom-menu">
                        <ul>
                            <li><a href="clients/list/"><i class="fi-rr-apps-sort"></i> All clients</a></li>
                            <li><a href="clients/add/"><i class="fi fi-rr-user-add"></i> Add new client</a>
                        </ul>
                    </div>
                </li> -->

    <li class="header-section-name">
        <p>Integration<span>Manage third party resources.</span></p>
    </li>

    <li <?php echo ((strpos(ADMIN_BASE_URL, '/integration/') !== false) ? 'class="active"' : '') ?>>
        <a href="integration/"><i class="fi fi-rr-plug-connection"></i> Plugins</a>
    </li>

    <li class="header-section-name">
        <p>Business Hub<span>Control Your Business</span></p>
    </li>


    <li <?php echo ((strpos(ADMIN_BASE_URL, '/policy/') !== false) ? 'class="active"' : '') ?>>
        <a href="policy/"><i class="fi fi-rr-memo"></i> Policies</a>
        <div class="side-menu">
            <ul>
                <li><a href="policy/list/"><i class="fi-rr-apps-sort"></i> All Policies</a></li>
                <li><a href="policy/add/"><i class="fi fi-rr-add"></i> Add new policy</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="policy/list/"><i class="fi-rr-apps-sort"></i> All Policies</a></li>
                <li><a href="policy/add/"><i class="fi fi-rr-add"></i> Add new policy</a></li>
            </ul>
        </div>
    </li>
    <li <?php echo ((strpos(ADMIN_BASE_URL, '/bank/') !== false) ? 'class="active"' : '') ?>>
        <a href="payment/"><i class="fi fi-rr-indian-rupee-sign"></i> Payments</a>
        <div class="side-menu">
            <ul>
                <li><a href="payment/list/"><i class="fi-rr-apps-sort"></i> All methods</a></li>
                <li><a href="payment/add/"><i class="fi fi-rr-add"></i> Add new method</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="payment/list/"><i class="fi-rr-apps-sort"></i> All methods</a></li>
                <li><a href="payment/add/"><i class="fi fi-rr-add"></i> Add new method</a></li>
            </ul>
        </div>
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

<?php
}

?>