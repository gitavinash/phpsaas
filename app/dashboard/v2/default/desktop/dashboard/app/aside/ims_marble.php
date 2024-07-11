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
            <li><a href="sales/add/"><i class="fi-rr-add"></i> Add new sale</a></li>
        </ul>
    </div>
</li>
<?php
}

?>

<?php

if ($site_access_type == 'administrator' || $site_access_type == 'accounts' || $site_access_type == 'both') { ?>

<li class="header-section-name">
    <p>Sales<span>Sales management.</span></p>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/sales_request/') !== false) ? 'class="active"' : '') ?>>
    <a href="sales_request/"><i class="fi fi-rr-memo-circle-check"></i> Sales requests</a>
    <div class="side-menu">
        <ul>
            <li><a href="sales_request/list/"><i class="fi fi-rr-apps-sort"></i> All requests</a></li>
            <li><a href="sales_request/add/"><i class="fi fi-rr-hourglass-end"></i> Pending requests</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="sales_request/list/"><i class="fi fi-rr-apps-sort"></i> All requests</a></li>
            <li><a href="sales_request/add/"><i class="fi fi-rr-hourglass-end"></i> Pending requests</a></li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/return_request/') !== false) ? 'class="active"' : '') ?>>
    <a href="return_request/"><i class="fi fi-rr-undo"></i> Return requests</a>
    <div class="side-menu">
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

if ($site_access_type == 'administrator' || $site_access_type == 'superwiser' || $site_access_type == 'both') { ?>
<li class="header-section-name">
    <p>Inventory<span>Inventory management.</span></p>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/inventory/') !== false) ? 'class="active"' : '') ?>>
    <a href="inventory/"><i class="fi fi-rr-inventory-alt"></i> Inventory</a>
    <div class="side-menu">
        <ul>
            <li><a href="inventory/list/"><i class="fi-rr-apps-sort"></i> All blocks</a></li>
            <li><a href="inventory/add/"><i class="fi fi-rr-add"></i> Add new block</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="inventory/list/"><i class="fi-rr-apps-sort"></i> All blocks</a></li>
            <li><a href="inventory/add/"><i class="fi fi-rr-add"></i> Add new block</a></li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/restock/') !== false) ? 'class="active"' : '') ?>>
    <a href="restock/"><i class="fi fi-rr-undo"></i> Restock</a>
    <div class="side-menu">
        <ul>
            <li><a href="restock/list/"><i class="fi fi-rr-apps-sort"></i> All requests</a></li>
            <li><a href="restock/add/"><i class="fi fi-rr-hourglass-end"></i> Pending requests</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="restock/list/"><i class="fi fl-rr-apps-sort"></i> All requests</a></li>
            <li><a href="restock/add/"><i class="fi fi-rr-hourglass-end"></i> Pending requests</a></li>
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