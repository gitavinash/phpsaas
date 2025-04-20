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
            <li><a href="package/domestic/list/"><i class="fi-rr-apps-sort"></i> All packages</a>
            </li>
            <li><a href="package/domestic/add/"><i class="fi fi-rr-add"></i> Add new package</a>
            </li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/package/domestic/') !== false) ? 'class="active"' : '') ?>>
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
        </ul>
    </div>
</li>