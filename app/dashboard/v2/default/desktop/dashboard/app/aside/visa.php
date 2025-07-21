<?php

if ($site_access_type != 'customer') { ?>
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


<li class="header-section-name">
    <p>VISA Packages<span>Manage all visa packages.</span></p>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/visa_packages/') !== false) ? 'class="active"' : '') ?>>
    <a href="visa_packages/"><i class="fi fi-rr-passport"></i>Packages</a>
    <div class="side-menu">
        <ul>
            <li><a href="visa_packages/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
            </li>
            <li><a href="visa_packages/add/"><i class="fi fi-rr-add"></i> Add new package</a>
            </li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="visa_packages/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
            </li>
            <li><a href="visa_packages/add/"><i class="fi fi-rr-add"></i> Add new package</a>
            </li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/country/') !== false) ? 'class="active"' : '') ?>>
    <a href="country/"><i class="fi fi-rr-flag"></i>Country</a>
    <div class="side-menu">
        <ul>
            <li><a href="country/list/"><i class="fi-rr-apps-sort"></i> All countries</a>
            </li>
            <li><a href="country/add/"><i class="fi fi-rr-add"></i> Add new country</a>
            </li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <ul>
                <li><a href="country/list/"><i class="fi-rr-apps-sort"></i> All countries</a>
                </li>
                <li><a href="country/add/"><i class="fi fi-rr-add"></i> Add new country</a>
                </li>
            </ul>
        </ul>
    </div>
</li>

<li class="header-section-name">
    <p>Blog Center<span>Latest News & Updates</span></p>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/blog/') !== false) ? 'class="active"' : '') ?>>
    <a href="blog/"><i class="fi fi-rr-newspaper"></i> Blogs</a>
    <div class="side-menu">
        <ul>
            <li><a href="blog/list/"><i class="fi-rr-apps-sort"></i> All Blogs</a></li>
            <li><a href="blog/add/"><i class="fi fi-rr-add"></i> Add new blog</a></li>
            <li><a href="blog/category/"><i class="fi fi-rr-filter-list"></i> Categories</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="blog/list/"><i class="fi-rr-apps-sort"></i> All Blogs</a></li>
            <li><a href="blog/add/"><i class="fi fi-rr-add"></i> Add new blog</a></li>
            <li><a href="blog/category/"><i class="fi fi-rr-filter-list"></i> Categories</a></li>
        </ul>
    </div>
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

// For customer users

if ($site_access_type == 'customer') { ?>
<li class="header-section-name">
    <p>Application<span>Track your VISA Applications.</span></p>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/visa_applications/') !== false) ? 'class="active"' : '') ?>>
    <a href="visa_applications/"><i class="fi fi-rr-passport"></i>Applications</a>
    <div class="side-menu">
        <ul>
            <li><a href="visa_applications/list/"><i class="fi-rr-apps-sort"></i> All Applications</a>
            </li>
            <li><a href="/start-application/" target="_blank"><i class="fi fi-rr-add"></i> Apply new application</a>
            </li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="visa_applications/list/"><i class="fi-rr-apps-sort"></i> All Applications</a>
            </li>
            <li><a href="/start-application/" target="_blank"><i class="fi fi-rr-add"></i> Apply new application</a>
            </li>
        </ul>
    </div>
</li>
<?php
}

?>