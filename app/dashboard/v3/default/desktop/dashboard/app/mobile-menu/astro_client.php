<?php
/* =========================================
   MOBILE MENU: REPORTS + INTEGRATION + LEADS
   ========================================= */

/* Admin-only: Reports + Integrations */
if ($site_access_type == 'administrator') { ?>
<!-- Reports -->
<div class="more-section is-reports">
    <h4>Reports <small>Analytics & performance</small></h4>
    <div class="more-grid">
        <a href="report/" class="more-item more-reports">
            <i class="fi fi-rr-chart-histogram"></i>
            <span>Reports</span>
        </a>
        <a href="report/employees/" class="more-item more-reports">
            <i class="fi-rr-employee-man-alt"></i>
            <span>Employees performance</span>
        </a>
    </div>
</div>

<!-- Integration -->
<div class="more-section is-integration">
    <h4>Integration <small>Manage third party resources</small></h4>
    <div class="more-grid">
        <a href="integration/" class="more-item more-integration">
            <i class="fi fi-rr-plug-connection"></i>
            <span>Plugins</span>
        </a>
    </div>
</div>
<?php } ?>

<!-- Leads (visible as in your original) -->
<div class="more-section is-leads">
    <h4>Leads <small>Manage leads</small></h4>
    <div class="more-grid">
        <a href="leads/" class="more-item more-leads">
            <i class="fi fi-rr-chart-network"></i>
            <span>Leads</span>
        </a>
        <a href="leads/list/" class="more-item more-leads">
            <i class="fi-rr-apps-sort"></i>
            <span>All leads</span>
        </a>
        <a href="leads/add/" class="more-item more-leads">
            <i class="fi fi-rr-add"></i>
            <span>Add new lead</span>
        </a>
        <a href="leads/list/?leadStatus=1" class="more-item more-leads">
            <i class="fi-rr-check"></i>
            <span>Confirmed leads</span>
        </a>
        <a href="leads/list/?leadStatus=2" class="more-item more-leads">
            <i class="fi-rr-hourglass-end"></i>
            <span>Pending leads</span>
        </a>
        <a href="leads/list/?leadStatus=3" class="more-item more-leads">
            <i class="fi-rr-comment"></i>
            <span>Follow ups leads</span>
        </a>
        <a href="leads/list/?leadStatus=4" class="more-item more-leads">
            <i class="fi-rr-cross-circle"></i>
            <span>Cancelled leads</span>
        </a>
    </div>
</div>