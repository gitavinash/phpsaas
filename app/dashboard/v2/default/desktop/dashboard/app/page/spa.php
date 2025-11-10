<?php if (SITE_VERSION == 'mobile') { ?>

<?php if ($site_access_type === 'administrator') { ?>
<!-- Admin: Create membership • Check-in • Reports • Active rooms -->
<div class="widget box-2 box-mb-5">
    <a href="membership/sell/" class="actionBtn new-item-btn spa-link" data-route="/membership/sell/"
        aria-label="Create membership">
        <div class="widget-info">
            <i class="fi fi-rr-user-add plus-badge badge-blue" aria-hidden="true"></i>
            Create membership
        </div>
    </a>
</div>

<div class="widget box-2 box-mb-5">
    <a href="membership/check-in/" class="actionBtn new-item-btn spa-link" data-route="/membership/check-in/"
        aria-label="Deduct a membership visit">
        <div class="widget-info">
            <i class="fi fi-rr-entrance plus-badge badge-amber" aria-hidden="true"></i>
            Check-in
        </div>
    </a>
</div>

<div class="widget box-2 box-mb-5">
    <a href="membership/reports/" class="actionBtn new-item-btn spa-link" data-route="/membership/reports/"
        aria-label="Membership reports">
        <div class="widget-info">
            <i class="fi fi-rr-chart-histogram plus-badge" aria-hidden="true"></i>
            Reports
        </div>
    </a>
</div>

<div class="widget box-2 box-mb-5">
    <a href="rooms/active/" class="actionBtn new-item-btn spa-link" data-route="/rooms/active/"
        aria-label="Manage active rooms">
        <div class="widget-info">
            <i class="fi fi-rr-door-open plus-badge badge-green" aria-hidden="true"></i>
            Active rooms
        </div>
    </a>
</div>
<?php } ?>

<?php if ($site_access_type === 'reception' || $site_access_type === '') { ?>
<!-- Reception: Check-in • Active rooms -->

<div class="widget box-2 box-mb-5">
    <a href="membership/sell/" class="actionBtn new-item-btn spa-link" data-route="/membership/sell/"
        aria-label="Create membership">
        <div class="widget-info">
            <i class="fi fi-rr-user-add plus-badge badge-blue" aria-hidden="true"></i>
            Create membership
        </div>
    </a>
</div>

<div class="widget box-2 box-mb-5">
    <a href="membership/check-in/" class="actionBtn new-item-btn spa-link" data-route="/membership/check-in/"
        aria-label="Deduct a membership visit">
        <div class="widget-info">
            <i class="fi fi-rr-entrance plus-badge badge-amber" aria-hidden="true"></i>
            Check-in
        </div>
    </a>
</div>

<div class="widget box-2 box-mb-5">
    <a href="rooms/active/" class="actionBtn new-item-btn spa-link" data-route="/rooms/active/"
        aria-label="Manage active rooms">
        <div class="widget-info">
            <i class="fi fi-rr-door-open plus-badge badge-green" aria-hidden="true"></i>
            Active rooms
        </div>
    </a>
</div>
<?php } ?>

<?php } ?>


<div class="widget box-1">
    <?php

    require_once ADMIN_PATH . 'dashboard/widgets/sales-chart.php';

    ?>
</div>

<div class="widget box-1">
    <?php

    require_once ADMIN_PATH . 'dashboard/widgets/visits-chart.php';

    ?>
</div>