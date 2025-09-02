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