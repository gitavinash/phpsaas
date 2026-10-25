<?php

/**
 * Control Panel: Aside Navigation Menu (Role-Aware Dedicated Pages)
 * Path: /app/dashboard/v3/default/desktop/dashboard/app/aside/control_panel.php
 * Strict V3 Rule: External CSS and External JS only, no inline handlers
 */

$role = isset($site_access_type) ? strtolower(trim($site_access_type)) : 'administrator';
$isAdmin = ($role === 'administrator');
$isPartner = ($role === 'partner');
$isSales = ($role === 'sales');
$isSupport = ($role === 'support');
$isGroundSales = ($role === 'on_ground_sales');

$currentBase = defined('ADMIN_BASE_URL') ? ADMIN_BASE_URL : '';
$isAppsActive = (strpos($currentBase, '/apps') !== false);
$isThemesActive = (strpos($currentBase, '/themes') !== false);
$isSitesActive = (strpos($currentBase, '/sites') !== false);
$isUsersActive = (strpos($currentBase, '/users') !== false || strpos($currentBase, '/team') !== false || strpos($currentBase, '/user') !== false);
$isInvoicesActive = (strpos($currentBase, '/invoices') !== false);
$isCommissionsActive = (strpos($currentBase, '/commissions') !== false);
$isConfigActive = (strpos($currentBase, '/configurations') !== false);
$isSettingsActive = (strpos($currentBase, '/setting') !== false);

$isOverviewActive = (!$isAppsActive && !$isThemesActive && !$isSitesActive && !$isUsersActive && !$isInvoicesActive && !$isCommissionsActive && !$isConfigActive && !$isSettingsActive);

?>

<!-- Control Panel Main Navigation -->
<li class="header-section-name">
    <p>Control Panel<span>Blumox Cloud Infrastructure</span></p>
</li>

<li <?php echo ($isOverviewActive ? 'class="active"' : ''); ?>>
    <a href="/dashboard/"><i class="fi fi-rr-dashboard"></i> Overview</a>
</li>

<?php if ($isAdmin || $isSales || $isSupport || $isGroundSales) { ?>
<li class="header-section-name">
    <p>Infrastructure &amp; Tenancy<span>Apps, Themes &amp; Sites</span></p>
</li>

<?php if ($isAdmin) { ?>
<li <?php echo ($isAppsActive ? 'class="active"' : ''); ?>>
    <a href="/dashboard/apps/"><i class="fi fi-rr-apps"></i> Apps Hub</a>
</li>

<li <?php echo ($isThemesActive ? 'class="active"' : ''); ?>>
    <a href="/dashboard/themes/"><i class="fi fi-rr-palette"></i> Themes Matrix</a>
</li>
<?php } ?>

<li <?php echo ($isSitesActive ? 'class="active"' : ''); ?>>
    <a href="/dashboard/sites/"><i class="fi fi-rr-globe"></i> Client Sites</a>
</li>
<?php } ?>

<!-- Business & Team -->
<li class="header-section-name">
    <p>Business &amp; Operations<span>Users, Invoices &amp; Partners</span></p>
</li>

<?php if ($isAdmin || $isSupport) { ?>
<li <?php echo ($isUsersActive ? 'class="active"' : ''); ?>>
    <a href="/dashboard/users/"><i class="fi fi-rr-users-alt"></i> Team &amp; Roles</a>
</li>
<?php } ?>

<?php if ($isAdmin || $isSales || $isGroundSales) { ?>
<li <?php echo ($isInvoicesActive ? 'class="active"' : ''); ?>>
    <a href="/dashboard/invoices/"><i class="fi fi-rr-receipt"></i> Invoices &amp; Billing</a>
</li>
<?php } ?>

<?php if ($isAdmin || $isPartner) { ?>
<li <?php echo ($isCommissionsActive ? 'class="active"' : ''); ?>>
    <a href="/dashboard/commissions/"><i class="fi fi-rr-hand-holding-usd"></i> Partner Commissions</a>
</li>
<?php } ?>

<!-- System Configuration -->
<?php if ($isAdmin || $isSupport) { ?>
<li class="header-section-name">
    <p>Configuration<span>Global Settings &amp; Flags</span></p>
</li>

<li <?php echo ($isConfigActive ? 'class="active"' : ''); ?>>
    <a href="/dashboard/configurations/"><i class="fi fi-rr-settings-sliders"></i> Configurations</a>
</li>

<li <?php echo ($isSettingsActive ? 'class="active"' : ''); ?>>
    <a href="/dashboard/setting/"><i class="fi fi-rr-settings"></i> System Settings</a>
</li>
<?php } ?>