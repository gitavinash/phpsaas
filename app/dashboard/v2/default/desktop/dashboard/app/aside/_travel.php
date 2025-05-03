<?php

if ($site_access_type == 'administrator' || $site_access_type == 'operations') { ?>

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


<?php

if ($site_access_type == 'administrator') { ?>

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