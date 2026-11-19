<?php

/**
 * V3 Sidebar Navigation Menu
 * Component: app/dashboard/v3/default/desktop/dashboard/app/aside/aleya.php
 * Comprehensive travel management system navigation.
 */

$requestPath = $_SERVER['REQUEST_URI'] ?? '';
$adminBaseUrl = defined('ADMIN_BASE_URL') ? ADMIN_BASE_URL : '';
$activeUrl = defined('ACTIVE_URL') ? ACTIVE_URL : '';
$currentUrl = $adminBaseUrl !== '' ? $adminBaseUrl : ($activeUrl !== '' ? $activeUrl : $requestPath);
$isDashboardActive = ($currentUrl === '/dashboard/' || $currentUrl === '/dashboard' || $currentUrl === '' || $currentUrl === '/' || $requestPath === '/dashboard/' || $requestPath === '/dashboard');

if (!function_exists('isAsideUrlActive')) {
    function isAsideUrlActive($pattern, $currentUrl, $requestUri = '')
    {
        $pattern = trim($pattern);
        if ($pattern === '') return false;
        $clean = trim($pattern, '/');
        if (strpos($currentUrl, $pattern) !== false || strpos($currentUrl, '/' . $clean) !== false || ($clean !== '' && strpos($currentUrl, $clean) !== false)) {
            return true;
        }
        if ($requestUri !== '' && (strpos($requestUri, $pattern) !== false || strpos($requestUri, '/' . $clean) !== false || ($clean !== '' && strpos($requestUri, $clean) !== false))) {
            return true;
        }
        return false;
    }
}

$leadStatus = isset($_GET['leadStatus']) ? (string)$_GET['leadStatus'] : '';
$acctStatus = isset($_GET['status']) ? (string)$_GET['status'] : '';

$isLeadsActive = isAsideUrlActive('/leads', $currentUrl, $requestPath);
$isLeadAllActive = (isAsideUrlActive('/leads/list', $currentUrl, $requestPath) && $leadStatus === '') || ($isLeadsActive && !isAsideUrlActive('/leads/add', $currentUrl, $requestPath) && $leadStatus === '');
$isLeadAddActive = isAsideUrlActive('/leads/add', $currentUrl, $requestPath);
$isLeadConfirmedActive = $isLeadsActive && $leadStatus === '1';
$isLeadPendingActive = $isLeadsActive && $leadStatus === '2';
$isLeadFollowupActive = $isLeadsActive && $leadStatus === '3';
$isLeadCancelledActive = $isLeadsActive && $leadStatus === '16';

// 1. CORE & SALES CRM / DIGITAL MARKETING
?>
<?php if ($site_access_type === 'digital_marketing' || $isDigitalMarketingPackage) { ?>
    <!-- 1. DIGITAL MARKETING SUITE (Package ID 6: Dashboard, Leads) -->
    <li class="header-section-name">
        <p>Digital Marketing<span>Campaigns &amp; Leads</span></p>
    </li>

    <li <?php echo ($isDashboardActive ? 'class="active"' : ''); ?> data-aside-nav="dashboard">
        <a href="/dashboard/"><i class="fi fi-rr-apps"></i> Dashboard</a>
        <div class="side-menu">
            <ul>
                <li><a href="/dashboard/"><i class="fi-rr-apps"></i> Overview</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/"><i class="fi-rr-apps"></i> Overview</a></li>
            </ul>
        </div>
    </li>

    <li <?php echo ($isLeadsActive ? 'class="active"' : ''); ?> data-aside-nav="leads">
        <a href="/dashboard/leads/"><i class="fi fi-rr-chart-network"></i> Leads &amp; Inquiries</a>
        <div class="side-menu">
            <ul>
                <li <?php echo ($isLeadAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/" <?php echo ($isLeadAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Leads</a></li>
                <li <?php echo ($isLeadAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/add/" <?php echo ($isLeadAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Lead</a></li>
                <li <?php echo ($isLeadConfirmedActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=1" <?php echo ($isLeadConfirmedActive ? 'class="active"' : ''); ?>><i class="fi-rr-check"></i> Confirmed Leads</a></li>
                <li <?php echo ($isLeadPendingActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=2" <?php echo ($isLeadPendingActive ? 'class="active"' : ''); ?>><i class="fi-rr-hourglass-end"></i> Pending / Quotations</a></li>
                <li <?php echo ($isLeadFollowupActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=3" <?php echo ($isLeadFollowupActive ? 'class="active"' : ''); ?>><i class="fi-rr-comment"></i> Follow-ups</a></li>
                <li <?php echo ($isLeadCancelledActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=16" <?php echo ($isLeadCancelledActive ? 'class="active"' : ''); ?>><i class="fi-rr-cross-circle"></i> Cancelled Leads</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li <?php echo ($isLeadAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/" <?php echo ($isLeadAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Leads</a></li>
                <li <?php echo ($isLeadAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/add/" <?php echo ($isLeadAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Lead</a></li>
                <li <?php echo ($isLeadConfirmedActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=1" <?php echo ($isLeadConfirmedActive ? 'class="active"' : ''); ?>><i class="fi-rr-check"></i> Confirmed Leads</a></li>
                <li <?php echo ($isLeadPendingActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=2" <?php echo ($isLeadPendingActive ? 'class="active"' : ''); ?>><i class="fi-rr-hourglass-end"></i> Pending / Quotations</a></li>
                <li <?php echo ($isLeadFollowupActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=3" <?php echo ($isLeadFollowupActive ? 'class="active"' : ''); ?>><i class="fi-rr-comment"></i> Follow-ups</a></li>
                <li <?php echo ($isLeadCancelledActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=16" <?php echo ($isLeadCancelledActive ? 'class="active"' : ''); ?>><i class="fi-rr-cross-circle"></i> Cancelled Leads</a></li>
            </ul>
        </div>
    </li>

<?php } else { ?>
    <!-- 1. SALES CRM (Standard ERP Package) -->
    <li class="header-section-name">
        <p>Sales CRM<span>Daily Operations &amp; Leads</span></p>
    </li>

    <li <?php echo ($isDashboardActive ? 'class="active"' : ''); ?> data-aside-nav="dashboard">
        <a href="/dashboard/"><i class="fi fi-rr-apps"></i> Dashboard</a>
    </li>

    <li <?php echo ($isLeadsActive ? 'class="active"' : ''); ?> data-aside-nav="leads">
        <a href="/dashboard/leads/"><i class="fi fi-rr-chart-network"></i> Leads &amp; Inquiries</a>
        <div class="side-menu">
            <ul>
                <li <?php echo ($isLeadAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/" <?php echo ($isLeadAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Leads</a></li>
                <li <?php echo ($isLeadAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/add/" <?php echo ($isLeadAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Lead</a></li>
                <li <?php echo ($isLeadConfirmedActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=1" <?php echo ($isLeadConfirmedActive ? 'class="active"' : ''); ?>><i class="fi-rr-check"></i> Confirmed Leads</a></li>
                <li <?php echo ($isLeadPendingActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=2" <?php echo ($isLeadPendingActive ? 'class="active"' : ''); ?>><i class="fi-rr-hourglass-end"></i> Pending / Quotations</a></li>
                <li <?php echo ($isLeadFollowupActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=3" <?php echo ($isLeadFollowupActive ? 'class="active"' : ''); ?>><i class="fi-rr-comment"></i> Follow-ups</a></li>
                <li <?php echo ($isLeadCancelledActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=16" <?php echo ($isLeadCancelledActive ? 'class="active"' : ''); ?>><i class="fi-rr-cross-circle"></i> Cancelled Leads</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li <?php echo ($isLeadAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/" <?php echo ($isLeadAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Leads</a></li>
                <li <?php echo ($isLeadAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/add/" <?php echo ($isLeadAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Lead</a></li>
                <li <?php echo ($isLeadConfirmedActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=1" <?php echo ($isLeadConfirmedActive ? 'class="active"' : ''); ?>><i class="fi-rr-check"></i> Confirmed Leads</a></li>
                <li <?php echo ($isLeadPendingActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=2" <?php echo ($isLeadPendingActive ? 'class="active"' : ''); ?>><i class="fi-rr-hourglass-end"></i> Pending / Quotations</a></li>
                <li <?php echo ($isLeadFollowupActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=3" <?php echo ($isLeadFollowupActive ? 'class="active"' : ''); ?>><i class="fi-rr-comment"></i> Follow-ups</a></li>
                <li <?php echo ($isLeadCancelledActive ? 'class="active"' : ''); ?>><a href="/dashboard/leads/list/?leadStatus=16" <?php echo ($isLeadCancelledActive ? 'class="active"' : ''); ?>><i class="fi-rr-cross-circle"></i> Cancelled Leads</a></li>
            </ul>
        </div>
    </li>

    <?php if ($site_access_type == 'administrator' || $site_access_type == 'operations' || $site_access_type == 'admin' || $site_access_type == 'superadmin' || $site_access_type == 'super_administrator') {
        $isOpsActive = !$isDashboardActive && isAsideUrlActive('/operations', $currentUrl, $requestPath);
        $isOpsListActive = $isOpsActive && (isAsideUrlActive('/operations/list', $currentUrl, $requestPath) || ($isOpsActive && !isAsideUrlActive('/operations/trips', $currentUrl, $requestPath) && !isAsideUrlActive('/operations/requests', $currentUrl, $requestPath) && !isAsideUrlActive('/operations/unassigned', $currentUrl, $requestPath) && !isAsideUrlActive('/operations/templates', $currentUrl, $requestPath)));
        $isOpsTripsActive = $isOpsActive && isAsideUrlActive('/operations/trips', $currentUrl, $requestPath);
        $isOpsRequestsActive = $isOpsActive && isAsideUrlActive('/operations/requests', $currentUrl, $requestPath);
        $isOpsUnassignedActive = $isOpsActive && isAsideUrlActive('/operations/unassigned', $currentUrl, $requestPath);
        $isOpsTemplatesActive = $isOpsActive && isAsideUrlActive('/operations/templates', $currentUrl, $requestPath);
    ?>
        <li class="ops-aside-item <?php echo ($isOpsActive ? 'active' : ''); ?>" data-aside-nav="operations">
            <a href="/dashboard/operations/"><i class="fi fi-rr-compass-alt"></i> Operations Hub</a>
            <div class="side-menu">
                <ul>
                    <li <?php echo ($isOpsListActive ? 'class="active"' : ''); ?>><a href="/dashboard/operations/list/" <?php echo ($isOpsListActive ? 'class="active"' : ''); ?>><i class="fi-rr-badge-check"></i> Confirmed Leads</a></li>
                    <li <?php echo ($isOpsTripsActive ? 'class="active"' : ''); ?>><a href="/dashboard/operations/trips/" <?php echo ($isOpsTripsActive ? 'class="active"' : ''); ?>><i class="fi-rr-plane-departure"></i> Trip Planner</a></li>
                    <li <?php echo ($isOpsRequestsActive ? 'class="active"' : ''); ?>><a href="/dashboard/operations/requests/" <?php echo ($isOpsRequestsActive ? 'class="active"' : ''); ?>><i class="fi-rr-paper-plane"></i> Booking Requests</a></li>
                    <li <?php echo ($isOpsUnassignedActive ? 'class="active"' : ''); ?>><a href="/dashboard/operations/unassigned/" <?php echo ($isOpsUnassignedActive ? 'class="active"' : ''); ?>><i class="fi-rr-user-add"></i> Assign Leads</a></li>
                    <li <?php echo ($isOpsTemplatesActive ? 'class="active"' : ''); ?>><a href="/dashboard/operations/templates/" <?php echo ($isOpsTemplatesActive ? 'class="active"' : ''); ?>><i class="fi-rr-file-edit"></i> Request Templates</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li <?php echo ($isOpsListActive ? 'class="active"' : ''); ?>><a href="/dashboard/operations/list/" <?php echo ($isOpsListActive ? 'class="active"' : ''); ?>><i class="fi-rr-badge-check"></i> Confirmed Leads</a></li>
                    <li <?php echo ($isOpsTripsActive ? 'class="active"' : ''); ?>><a href="/dashboard/operations/trips/" <?php echo ($isOpsTripsActive ? 'class="active"' : ''); ?>><i class="fi-rr-plane-departure"></i> Trip Planner</a></li>
                    <li <?php echo ($isOpsRequestsActive ? 'class="active"' : ''); ?>><a href="/dashboard/operations/requests/" <?php echo ($isOpsRequestsActive ? 'class="active"' : ''); ?>><i class="fi-rr-paper-plane"></i> Booking Requests</a></li>
                    <li <?php echo ($isOpsUnassignedActive ? 'class="active"' : ''); ?>><a href="/dashboard/operations/unassigned/" <?php echo ($isOpsUnassignedActive ? 'class="active"' : ''); ?>><i class="fi-rr-user-add"></i> Assign Leads</a></li>
                    <li <?php echo ($isOpsTemplatesActive ? 'class="active"' : ''); ?>><a href="/dashboard/operations/templates/" <?php echo ($isOpsTemplatesActive ? 'class="active"' : ''); ?>><i class="fi-rr-file-edit"></i> Request Templates</a></li>
                </ul>
            </div>
        </li>
    <?php } ?>
<?php } ?>

<?php
// 1.5 ACCOUNTS & FINANCE (Administrator & Accountant) - Hidden in Digital Marketing Package (ID 6)
if (!$isDigitalMarketingPackage && ($site_access_type == 'administrator' || $site_access_type == 'accountant')) {
    $isAcctActive = isAsideUrlActive('/accounts', $currentUrl, $requestPath);
    $isAcctAllActive = $isAcctActive && $acctStatus === '';
    $isAcctPendingActive = $isAcctActive && $acctStatus === 'pending';
    $isAcctVerifiedActive = $isAcctActive && $acctStatus === 'verified';
?>
    <li <?php echo ($isAcctActive ? 'class="active"' : ''); ?> data-aside-nav="accounts">
        <a href="/dashboard/accounts/"><i class="fi fi-rr-wallet"></i> Accounts &amp; Ledger</a>
        <div class="side-menu">
            <ul>
                <li <?php echo ($isAcctAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/accounts/" <?php echo ($isAcctAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Transactions</a></li>
                <li <?php echo ($isAcctPendingActive ? 'class="active"' : ''); ?>><a href="/dashboard/accounts/?status=pending" <?php echo ($isAcctPendingActive ? 'class="active"' : ''); ?>><i class="fi-rr-clock"></i> Pending Verification</a></li>
                <li <?php echo ($isAcctVerifiedActive ? 'class="active"' : ''); ?>><a href="/dashboard/accounts/?status=verified" <?php echo ($isAcctVerifiedActive ? 'class="active"' : ''); ?>><i class="fi-rr-badge-check"></i> Verified Collections</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li <?php echo ($isAcctAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/accounts/" <?php echo ($isAcctAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Transactions</a></li>
                <li <?php echo ($isAcctPendingActive ? 'class="active"' : ''); ?>><a href="/dashboard/accounts/?status=pending" <?php echo ($isAcctPendingActive ? 'class="active"' : ''); ?>><i class="fi-rr-clock"></i> Pending Verification</a></li>
                <li <?php echo ($isAcctVerifiedActive ? 'class="active"' : ''); ?>><a href="/dashboard/accounts/?status=verified" <?php echo ($isAcctVerifiedActive ? 'class="active"' : ''); ?>><i class="fi-rr-badge-check"></i> Verified Collections</a></li>
            </ul>
        </div>
    </li>
<?php } ?>

<?php
// 2. PACKAGES, VENDORS & CMS - Hidden in Digital Marketing Package (ID 6)
if (!$isDigitalMarketingPackage && ($site_access_type == 'administrator' || $site_access_type == 'operations')) {
    $asideNatureVal = defined('COMPANY_NATURE') ? strtolower(COMPANY_NATURE) : 'travel_agency';
    $isDefaultNaturePackagesVendors = in_array($asideNatureVal, ['travel_agency', 'dmc']);

    $showTourPackages = defined('ASIDE_MENU_SHOW_PACKAGES')
        ? (ASIDE_MENU_SHOW_PACKAGES == '1' || ASIDE_MENU_SHOW_PACKAGES === 1)
        : $isDefaultNaturePackagesVendors;

    $showSupplyVendors = defined('ASIDE_MENU_SHOW_VENDORS')
        ? (ASIDE_MENU_SHOW_VENDORS == '1' || ASIDE_MENU_SHOW_VENDORS === 1)
        : $isDefaultNaturePackagesVendors;

    $showMarketingCms = defined('ASIDE_MENU_SHOW_CMS')
        ? (ASIDE_MENU_SHOW_CMS == '1' || ASIDE_MENU_SHOW_CMS === 1)
        : false;
?>

    <?php if ($showTourPackages) {
        $isPkgDomActive = isAsideUrlActive('/packages/domestic', $currentUrl, $requestPath);
        $isPkgDomAllActive = isAsideUrlActive('/packages/domestic/list', $currentUrl, $requestPath) || ($isPkgDomActive && !isAsideUrlActive('/packages/domestic/add', $currentUrl, $requestPath) && !isAsideUrlActive('/packages/domestic/location', $currentUrl, $requestPath));
        $isPkgDomAddActive = isAsideUrlActive('/packages/domestic/add', $currentUrl, $requestPath);
        $isPkgDomLocActive = isAsideUrlActive('/packages/domestic/location', $currentUrl, $requestPath);

        $isPkgIntActive = isAsideUrlActive('/packages/international', $currentUrl, $requestPath) || isAsideUrlActive('/international', $currentUrl, $requestPath);
        $isPkgIntAllActive = isAsideUrlActive('/packages/international/list', $currentUrl, $requestPath) || ($isPkgIntActive && !isAsideUrlActive('/packages/international/add', $currentUrl, $requestPath) && !isAsideUrlActive('/packages/international/location', $currentUrl, $requestPath));
        $isPkgIntAddActive = isAsideUrlActive('/packages/international/add', $currentUrl, $requestPath);
        $isPkgIntLocActive = isAsideUrlActive('/packages/international/location', $currentUrl, $requestPath);
    ?>
        <li class="header-section-name" data-aside-group="packages">
            <p>Tour Packages<span>Smart Itineraries &amp; Catalog</span></p>
        </li>

        <li data-aside-group="packages" data-aside-nav="packages-domestic" <?php echo ($isPkgDomActive ? 'class="active"' : ''); ?>>
            <a href="/dashboard/packages/domestic/"><i class="fi fi-rr-suitcase-alt"></i> Domestic Packages</a>
            <div class="side-menu">
                <ul>
                    <li <?php echo ($isPkgDomAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/domestic/list/" <?php echo ($isPkgDomAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Packages</a></li>
                    <li <?php echo ($isPkgDomAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/domestic/add/" <?php echo ($isPkgDomAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Package</a></li>
                    <li <?php echo ($isPkgDomLocActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/domestic/location/" <?php echo ($isPkgDomLocActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-marker"></i> Destinations</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li <?php echo ($isPkgDomAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/domestic/list/" <?php echo ($isPkgDomAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Packages</a></li>
                    <li <?php echo ($isPkgDomAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/domestic/add/" <?php echo ($isPkgDomAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Package</a></li>
                    <li <?php echo ($isPkgDomLocActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/domestic/location/" <?php echo ($isPkgDomLocActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-marker"></i> Destinations</a></li>
                </ul>
            </div>
        </li>

        <li data-aside-group="packages" data-aside-nav="packages-international" <?php echo ($isPkgIntActive ? 'class="active"' : ''); ?>>
            <a href="/dashboard/packages/international/"><i class="fi fi-rr-globe"></i> International Packages</a>
            <div class="side-menu">
                <ul>
                    <li <?php echo ($isPkgIntAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/international/list/" <?php echo ($isPkgIntAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Packages</a></li>
                    <li <?php echo ($isPkgIntAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/international/add/" <?php echo ($isPkgIntAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Package</a></li>
                    <li <?php echo ($isPkgIntLocActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/international/location/" <?php echo ($isPkgIntLocActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-marker"></i> Destinations</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li <?php echo ($isPkgIntAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/international/list/" <?php echo ($isPkgIntAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Packages</a></li>
                    <li <?php echo ($isPkgIntAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/international/add/" <?php echo ($isPkgIntAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Package</a></li>
                    <li <?php echo ($isPkgIntLocActive ? 'class="active"' : ''); ?>><a href="/dashboard/packages/international/location/" <?php echo ($isPkgIntLocActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-marker"></i> Destinations</a></li>
                </ul>
            </div>
        </li>
    <?php } ?>

    <?php if ($showSupplyVendors) {
        $isVendPropActive = isAsideUrlActive('/vendors/property', $currentUrl, $requestPath);
        $isVendPropAllActive = isAsideUrlActive('/vendors/property/list', $currentUrl, $requestPath) || ($isVendPropActive && !isAsideUrlActive('/vendors/property/add', $currentUrl, $requestPath) && !isAsideUrlActive('/vendors/property/room', $currentUrl, $requestPath) && !isAsideUrlActive('/vendors/property/amenities', $currentUrl, $requestPath) && !isAsideUrlActive('/vendors/property/upload', $currentUrl, $requestPath));
        $isVendPropAddActive = isAsideUrlActive('/vendors/property/add', $currentUrl, $requestPath);
        $isVendPropRoomActive = isAsideUrlActive('/vendors/property/room', $currentUrl, $requestPath);
        $isVendPropAmenActive = isAsideUrlActive('/vendors/property/amenities', $currentUrl, $requestPath);
        $isVendPropUploadActive = isAsideUrlActive('/vendors/property/upload', $currentUrl, $requestPath);

        $isVendTransActive = isAsideUrlActive('/vendors/transporter', $currentUrl, $requestPath);
        $isVendTransAllActive = isAsideUrlActive('/vendors/transporter/list', $currentUrl, $requestPath) || ($isVendTransActive && !isAsideUrlActive('/vendors/transporter/add', $currentUrl, $requestPath) && !isAsideUrlActive('/vendors/transporter/vehicle', $currentUrl, $requestPath));
        $isVendTransAddActive = isAsideUrlActive('/vendors/transporter/add', $currentUrl, $requestPath);
        $isVendTransVehActive = isAsideUrlActive('/vendors/transporter/vehicle', $currentUrl, $requestPath);
    ?>
        <!-- 3. SUPPLY & VENDORS -->
        <li class="header-section-name" data-aside-group="vendors">
            <p>Supply &amp; Vendors<span>Inventory, Hotels &amp; Logistics</span></p>
        </li>

        <li data-aside-group="vendors" data-aside-nav="vendors-property" <?php echo ($isVendPropActive ? 'class="active"' : ''); ?>>
            <a href="/dashboard/vendors/property/"><i class="fi fi-rr-city"></i> Properties &amp; Hotels</a>
            <div class="side-menu">
                <ul>
                    <li <?php echo ($isVendPropAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/property/list/" <?php echo ($isVendPropAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Properties</a></li>
                    <li <?php echo ($isVendPropAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/property/add/" <?php echo ($isVendPropAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Property</a></li>
                    <li <?php echo ($isVendPropRoomActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/property/room/" <?php echo ($isVendPropRoomActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-door-open"></i> Manage Room Types</a></li>
                    <li <?php echo ($isVendPropAmenActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/property/amenities/" <?php echo ($isVendPropAmenActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-star"></i> Manage Amenities</a></li>
                    <li <?php echo ($isVendPropUploadActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/property/upload/" <?php echo ($isVendPropUploadActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-file-import"></i> Import Properties CSV</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li <?php echo ($isVendPropAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/property/list/" <?php echo ($isVendPropAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Properties</a></li>
                    <li <?php echo ($isVendPropAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/property/add/" <?php echo ($isVendPropAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Property</a></li>
                    <li <?php echo ($isVendPropRoomActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/property/room/" <?php echo ($isVendPropRoomActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-door-open"></i> Manage Room Types</a></li>
                    <li <?php echo ($isVendPropAmenActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/property/amenities/" <?php echo ($isVendPropAmenActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-star"></i> Manage Amenities</a></li>
                    <li <?php echo ($isVendPropUploadActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/property/upload/" <?php echo ($isVendPropUploadActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-file-import"></i> Import Properties CSV</a></li>
                </ul>
            </div>
        </li>

        <li data-aside-group="vendors" data-aside-nav="vendors-transporter" <?php echo ($isVendTransActive ? 'class="active"' : ''); ?>>
            <a href="/dashboard/vendors/transporter/"><i class="fi fi-rr-bus"></i> Transporters &amp; Fleet</a>
            <div class="side-menu">
                <ul>
                    <li <?php echo ($isVendTransAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/transporter/list/" <?php echo ($isVendTransAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Transporters</a></li>
                    <li <?php echo ($isVendTransAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/transporter/add/" <?php echo ($isVendTransAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Transporter</a></li>
                    <li <?php echo ($isVendTransVehActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/transporter/vehicle/" <?php echo ($isVendTransVehActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-car-bus"></i> Manage Fleet Vehicles</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li <?php echo ($isVendTransAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/transporter/list/" <?php echo ($isVendTransAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Transporters</a></li>
                    <li <?php echo ($isVendTransAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/transporter/add/" <?php echo ($isVendTransAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Transporter</a></li>
                    <li <?php echo ($isVendTransVehActive ? 'class="active"' : ''); ?>><a href="/dashboard/vendors/transporter/vehicle/" <?php echo ($isVendTransVehActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-car-bus"></i> Manage Fleet Vehicles</a></li>
                </ul>
            </div>
        </li>

        <li data-aside-group="vendors" class="aside-menu-disabled">
            <a href="javascript:void(0)"><i class="fi fi-rr-building"></i> DMC Partners <span class="aside-badge-soon">Soon</span></a>
        </li>
    <?php } ?>

    <?php if ($showMarketingCms) { ?>
        <!-- 4. MARKETING & CONTENT (CMS) -->
        <li class="header-section-name" data-aside-group="cms">
            <p>Marketing &amp; CMS<span>Blogs, Reviews &amp; Website</span></p>
        </li>

        <li data-aside-group="cms" class="aside-menu-disabled">
            <a href="javascript:void(0)"><i class="fi fi-rr-document-signed"></i> Travel Blogs <span class="aside-badge-soon">Soon</span></a>
        </li>

        <li data-aside-group="cms" class="aside-menu-disabled">
            <a href="javascript:void(0)"><i class="fi fi-rr-comment-quote"></i> Client Testimonials <span class="aside-badge-soon">Soon</span></a>
        </li>

        <li data-aside-group="cms" class="aside-menu-disabled">
            <a href="javascript:void(0)"><i class="fi fi-rr-picture"></i> Hero Sliders <span class="aside-badge-soon">Soon</span></a>
        </li>

        <li data-aside-group="cms" class="aside-menu-disabled">
            <a href="javascript:void(0)"><i class="fi fi-rr-interrogation"></i> FAQs <span class="aside-badge-soon">Soon</span></a>
        </li>
    <?php } ?>

<?php
}

// 5. ANALYTICS & REPORTS (Administrator Only)
if ($site_access_type == 'administrator') {
    $isReportActive = isAsideUrlActive('/report', $currentUrl, $requestPath);
    $isReportEmpActive = isAsideUrlActive('/report/employees', $currentUrl, $requestPath) || $isReportActive;
?>

    <li class="header-section-name">
        <p>Analytics<span>Performance &amp; Reports</span></p>
    </li>

    <li <?php echo ($isReportActive ? 'class="active"' : ''); ?> data-aside-nav="report">
        <a href="/dashboard/report/"><i class="fi fi-rr-chart-histogram"></i> Performance Reports</a>
        <div class="side-menu">
            <ul>
                <li <?php echo ($isReportEmpActive ? 'class="active"' : ''); ?>><a href="/dashboard/report/employees/" <?php echo ($isReportEmpActive ? 'class="active"' : ''); ?>><i class="fi-rr-employee-man-alt"></i> Employees Performance</a></li>
            </ul>
        </div>
        <div class="bottom-menu">
            <ul>
                <li <?php echo ($isReportEmpActive ? 'class="active"' : ''); ?>><a href="/dashboard/report/employees/" <?php echo ($isReportEmpActive ? 'class="active"' : ''); ?>><i class="fi-rr-employee-man-alt"></i> Employees Performance</a></li>
            </ul>
        </div>
    </li>
<?php } ?>

<?php
// 7. PLUGINS & INTEGRATIONS (Dedicated Section with Active/Available Indicators)
if ($site_access_type == 'administrator' || $site_access_type == 'digital_marketing') {
    $activeAsidePg = 'razorpay';
    if (defined('PG_NAME') && !empty(PG_NAME)) {
        $activeAsidePg = strtolower(PG_NAME);
    } elseif (defined('ENABLE_INSTAMOJO') && ENABLE_INSTAMOJO == '1') {
        $activeAsidePg = 'instamojo';
    } elseif (defined('ENABLE_SABPAISA') && ENABLE_SABPAISA == '1') {
        $activeAsidePg = 'sabpaisa';
    } elseif (defined('ENABLE_PHONEPE') && ENABLE_PHONEPE == '1') {
        $activeAsidePg = 'phonepe';
    } elseif (defined('ENABLE_WORLDLINE') && ENABLE_WORLDLINE == '1') {
        $activeAsidePg = 'worldline';
    } elseif (defined('ENABLE_PAYU') && ENABLE_PAYU == '1') {
        $activeAsidePg = 'payu';
    } elseif (defined('ENABLE_PHI_COMMERCE') && ENABLE_PHI_COMMERCE == '1') {
        $activeAsidePg = 'phi_commerce';
    } elseif (defined('ENABLE_CASHFREE') && ENABLE_CASHFREE == '1') {
        $activeAsidePg = 'cashfree';
    } elseif (defined('ENABLE_RAZORPAY') && ENABLE_RAZORPAY == '1') {
        $activeAsidePg = 'razorpay';
    }

    $isFbActive    = ($isDigitalMarketingPackage || !defined('ENABLE_FACEBOOK_LEAD_ADS') || ENABLE_FACEBOOK_LEAD_ADS == '1' || ENABLE_FACEBOOK_LEAD_ADS === 1);
    $isGAdsActive  = ($isDigitalMarketingPackage || (defined('ENABLE_GOOGLE_ADS') && (ENABLE_GOOGLE_ADS == '1' || ENABLE_GOOGLE_ADS === 1)));
    $isWfActive    = ($isDigitalMarketingPackage || !defined('ENABLE_WEB_FORMS') || ENABLE_WEB_FORMS == '1' || ENABLE_WEB_FORMS === 1);
    $isWaApiActive = (!defined('ENABLE_WHATSAPP_BUSINESS_API') || ENABLE_WHATSAPP_BUSINESS_API == '1' || ENABLE_WHATSAPP_BUSINESS_API === 1);
    $isSmtpActive  = (!defined('ENABLE_SMTP_EMAIL') || ENABLE_SMTP_EMAIL == '1' || ENABLE_SMTP_EMAIL === 1);
    $isUpiActive   = (!defined('ENABLE_UPI') || ENABLE_UPI == '1' || ENABLE_UPI === 1);
    $isWhActive    = (!defined('ENABLE_WEBHOOKS') || ENABLE_WEBHOOKS == '1' || ENABLE_WEBHOOKS === 1);
    $isWorkflowsActive = (!defined('ENABLE_WORKFLOWS') || ENABLE_WORKFLOWS == '1' || ENABLE_WORKFLOWS === 1);

    $activePluginCount = 0;
    if ($isWorkflowsActive) $activePluginCount++;
    if ($isFbActive) $activePluginCount++;
    if ($isGAdsActive) $activePluginCount++;
    if ($isWfActive) $activePluginCount++;
    if ($isWaApiActive) $activePluginCount++;
    if ($isSmtpActive) $activePluginCount++;
    if ($isWhActive) $activePluginCount++;
    if (!$isDigitalMarketingPackage && $site_access_type !== 'digital_marketing') {
        if (!empty($activeAsidePg)) $activePluginCount++;
        if ($isUpiActive) $activePluginCount++;
    }
?>
    <!-- 7. PLUGINS & INTEGRATIONS -->
    <li class="header-section-name">
        <p>Plugins &amp; Integrations<span>Active &amp; Available Addons</span></p>
    </li>

    <li class="aside-item-plugins<?php echo ((isAsideUrlActive('/integration', $currentUrl, $requestPath) && !isAsideUrlActive('/integration/whatsapp_web', $currentUrl, $requestPath)) ? ' active' : ''); ?>" data-aside-nav="integration">
        <a href="/dashboard/integration/" class="aside-plugins-main-link">
            <i class="fi fi-rr-plug-connection"></i>
            <span class="aside-menu-text">Plugins &amp; Integrations</span>
            <span class="aside-badge-plugin-count" id="asideActivePluginBadge"><?php echo $activePluginCount; ?> Active</span>
        </a>
        <div class="side-menu aside-menu-plugins">
            <!-- Header with Quick Directory Shortcut -->
            <div class="aside-plugin-drawer-header">
                <div class="aside-plugin-drawer-title">
                    <i class="fi fi-rr-apps-sort"></i>
                    <span>Plugin Center</span>
                    <span class="aside-plugin-active-pill" id="asideActivePluginCountPill"><?php echo $activePluginCount; ?> Active</span>
                </div>
                <a href="/dashboard/integration/" class="aside-plugin-directory-btn" title="Open Full Plugins Directory">
                    Directory <i class="fi fi-rr-arrow-up-right-from-square"></i>
                </a>
            </div>

            <!-- Instant Live Search Bar -->
            <div class="aside-plugin-search-box">
                <i class="fi fi-rr-search"></i>
                <input type="text" id="asidePluginSearchInput" class="aside-plugin-search-input" placeholder="Search active plugins..." autocomplete="off">
                <button type="button" id="asidePluginSearchClear" class="aside-plugin-search-clear is-hidden" aria-label="Clear Search"><i class="fi fi-rr-cross-small"></i></button>
            </div>

            <!-- Panel 1: Connected & Active Plugins (Direct Settings Access) -->
            <div class="aside-plugin-panel is-active" id="asidePluginsActivePanel">
                <div class="aside-plugin-list-scroll">
                    <ul class="aside-plugin-items-list">
                        <li data-aside-plugin="google_ads" class="aside-plugin-item <?php echo ($isGAdsActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="google ads adwords sem search marketing">
                            <a href="/dashboard/integration/google_ads/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-google"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/google_ads.svg" alt="Google Ads"></span>
                                <span class="aside-plugin-name">Google Ads</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <li data-aside-plugin="facebook" class="aside-plugin-item <?php echo ($isFbActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="facebook lead ads meta instagram advertising">
                            <a href="/dashboard/integration/facebook/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-facebook"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/facebook.svg" alt="Facebook"></span>
                                <span class="aside-plugin-name">Facebook Ads</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <li data-aside-plugin="web_forms" class="aside-plugin-item <?php echo ($isWfActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="web forms lead capture landing page builder embed">
                            <a href="/dashboard/integration/web_forms/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-web-forms"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/web_forms.svg" alt="Web Forms"></span>
                                <span class="aside-plugin-name">Web Forms</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <li data-aside-plugin="whatsapp" class="aside-plugin-item <?php echo ($isWaApiActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="whatsapp api business meta official cloud">
                            <a href="/dashboard/integration/whatsapp/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-whatsapp"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/whatsapp.svg" alt="WhatsApp"></span>
                                <span class="aside-plugin-name">WhatsApp API</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <li data-aside-plugin="smtp" class="aside-plugin-item <?php echo ($isSmtpActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="smtp email mail server notifications transactional">
                            <a href="/dashboard/integration/smtp/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-smtp"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/smtp.svg" alt="SMTP Email"></span>
                                <span class="aside-plugin-name">SMTP Email</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <?php if (!$isDigitalMarketingPackage && $site_access_type !== 'digital_marketing') { ?>
                            <li data-aside-plugin="razorpay" class="aside-plugin-item <?php echo ($activeAsidePg === 'razorpay' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="razorpay payment gateway online payments">
                                <a href="/dashboard/integration/razorpay/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-razorpay"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/razorpay.svg" alt="Razorpay"></span>
                                    <span class="aside-plugin-name">Razorpay</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="phonepe" class="aside-plugin-item <?php echo ($activeAsidePg === 'phonepe' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="phonepe payment gateway upi qr">
                                <a href="/dashboard/integration/phonepe/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-phonepe"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/phonepe.svg" alt="PhonePe"></span>
                                    <span class="aside-plugin-name">PhonePe</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="cashfree" class="aside-plugin-item <?php echo ($activeAsidePg === 'cashfree' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="cashfree payment gateway auto collection">
                                <a href="/dashboard/integration/cashfree/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-cashfree"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/cashfree.svg" alt="Cashfree" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">Cashfree</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="payu" class="aside-plugin-item <?php echo ($activeAsidePg === 'payu' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="payu payment gateway">
                                <a href="/dashboard/integration/payu/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-payu"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/payu.svg" alt="PayU" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">PayU</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="instamojo" class="aside-plugin-item <?php echo ($activeAsidePg === 'instamojo' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="instamojo payment gateway">
                                <a href="/dashboard/integration/instamojo/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-instamojo"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/instamojo.png" alt="Instamojo" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">Instamojo</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="sabpaisa" class="aside-plugin-item <?php echo ($activeAsidePg === 'sabpaisa' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="sabpaisa payment gateway">
                                <a href="/dashboard/integration/sabpaisa/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-sabpaisa"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/sabpaisa.png" alt="SabPaisa" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">SabPaisa</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="worldline" class="aside-plugin-item <?php echo ($activeAsidePg === 'worldline' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="worldline payment gateway">
                                <a href="/dashboard/integration/worldline/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-worldline"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/worldline.svg" alt="Worldline" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">Worldline</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="phi_commerce" class="aside-plugin-item <?php echo ($activeAsidePg === 'phi_commerce' ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="phi commerce payment gateway">
                                <a href="/dashboard/integration/phi_commerce/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-phicommerce"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/phicommerce.png" alt="Phi Commerce" class="brand-logo-wide"></span>
                                    <span class="aside-plugin-name">Phi Commerce</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                            <li data-aside-plugin="upi" class="aside-plugin-item <?php echo ($isUpiActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="upi qr static dynamic scan code payment">
                                <a href="/dashboard/integration/upi/" class="aside-plugin-link">
                                    <span class="aside-plugin-icon-wrap brand-upi"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/upi.svg" alt="UPI"></span>
                                    <span class="aside-plugin-name">UPI &amp; QR</span>
                                    <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                                </a>
                            </li>
                        <?php } ?>
                        <li data-aside-plugin="webhooks" class="aside-plugin-item <?php echo ($isWhActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="webhooks developer rest api callbacks events">
                            <a href="/dashboard/integration/webhooks/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-webhooks"><img src="/app/dashboard/v3/default/desktop/assets/images/integration/webhooks.svg" alt="Webhooks"></span>
                                <span class="aside-plugin-name">Webhooks</span>
                                <span class="aside-plugin-setting-pill" title="Configure Settings"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                        <li data-aside-plugin="workflows" class="aside-plugin-item <?php echo ($isWorkflowsActive ? 'plugin-item-active' : 'plugin-item-disabled is-hidden'); ?>" data-plugin-name="automation workflows flowchart make visual studio pipelines lead distribution">
                            <a href="/dashboard/integration/workflows/" class="aside-plugin-link">
                                <span class="aside-plugin-icon-wrap brand-workflows"><i class="fi fi-rr-workflow"></i></span>
                                <span class="aside-plugin-name">Workflows</span>
                                <span class="aside-plugin-setting-pill" title="Configure Flowcharts"><i class="fi fi-rr-settings"></i></span>
                            </a>
                        </li>
                    </ul>
                    <div class="aside-plugin-empty-state is-hidden" id="asideActiveEmptyState">
                        <i class="fi fi-rr-search"></i>
                        <span>No matching active plugin found</span>
                    </div>
                </div>
                <div class="aside-plugin-drawer-footer">
                    <a href="/dashboard/integration/" class="aside-plugin-explore-link">
                        <span><i class="fi fi-rr-apps"></i> All Plugins Directory</span>
                        <i class="fi fi-rr-angle-small-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="bottom-menu">
            <ul>
                <li><a href="/dashboard/integration/"><i class="fi fi-rr-apps-sort"></i> All Plugins</a></li>
            </ul>
        </div>
    </li>
<?php } ?>

<?php
// 8. WHATSAPP CRM (Displayed above Setting section)
$waAsideSectionFile = __DIR__ . '/whatsapp_section.php';
if (file_exists($waAsideSectionFile)) {
    require_once $waAsideSectionFile;
}
?>

<!-- 9. SYSTEM & SETTINGS (Always at end of menus) -->
<?php if ($site_access_type == 'administrator' || $site_access_type == 'digital_marketing') { ?>
    <li class="header-section-name">
        <p>System &amp; Settings<span>Team, Policies &amp; Configuration</span></p>
    </li>

    <?php if ($site_access_type == 'administrator') {
        $isUserActive = isAsideUrlActive('/user', $currentUrl, $requestPath);
        $isUserListActive = isAsideUrlActive('/user/list', $currentUrl, $requestPath) || ($isUserActive && !isAsideUrlActive('/user/add', $currentUrl, $requestPath));
        $isUserAddActive = isAsideUrlActive('/user/add', $currentUrl, $requestPath);

        $isCompActive = isAsideUrlActive('/company', $currentUrl, $requestPath);
        $isCompListActive = isAsideUrlActive('/company/list', $currentUrl, $requestPath) || ($isCompActive && !isAsideUrlActive('/company/add', $currentUrl, $requestPath));
        $isCompAddActive = isAsideUrlActive('/company/add', $currentUrl, $requestPath);

        $isPolicyActive = isAsideUrlActive('/policy', $currentUrl, $requestPath);
        $isPolicyListActive = isAsideUrlActive('/policy/list', $currentUrl, $requestPath) || ($isPolicyActive && !isAsideUrlActive('/policy/add', $currentUrl, $requestPath));
        $isPolicyAddActive = isAsideUrlActive('/policy/add', $currentUrl, $requestPath);
    ?>
        <li <?php echo ($isUserActive ? 'class="active"' : ''); ?> data-aside-nav="user">
            <a href="/dashboard/user/list/"><i class="fi fi-rr-users-alt"></i> Team &amp; Users</a>
            <div class="side-menu">
                <ul>
                    <li <?php echo ($isUserListActive ? 'class="active"' : ''); ?>><a href="/dashboard/user/list/" <?php echo ($isUserListActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Team Members</a></li>
                    <li <?php echo ($isUserAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/user/add/" <?php echo ($isUserAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-user-add"></i> Add Team Member</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li <?php echo ($isUserListActive ? 'class="active"' : ''); ?>><a href="/dashboard/user/list/" <?php echo ($isUserListActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Team Members</a></li>
                    <li <?php echo ($isUserAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/user/add/" <?php echo ($isUserAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-user-add"></i> Add Team Member</a></li>
                </ul>
            </div>
        </li>

        <li <?php echo ($isCompActive ? 'class="active"' : ''); ?> data-aside-nav="company">
            <a href="/dashboard/company/list/"><i class="fi fi-rr-shop"></i> Companies &amp; Branches</a>
            <div class="side-menu">
                <ul>
                    <li <?php echo ($isCompListActive ? 'class="active"' : ''); ?>><a href="/dashboard/company/list/" <?php echo ($isCompListActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Companies</a></li>
                    <li <?php echo ($isCompAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/company/add/" <?php echo ($isCompAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Company</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li <?php echo ($isCompListActive ? 'class="active"' : ''); ?>><a href="/dashboard/company/list/" <?php echo ($isCompListActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Companies</a></li>
                    <li <?php echo ($isCompAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/company/add/" <?php echo ($isCompAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Company</a></li>
                </ul>
            </div>
        </li>

        <li <?php echo ($isPolicyActive ? 'class="active"' : ''); ?> data-aside-nav="policy">
            <a href="/dashboard/policy/"><i class="fi fi-rr-memo"></i> Company Policies</a>
            <div class="side-menu">
                <ul>
                    <li <?php echo ($isPolicyListActive ? 'class="active"' : ''); ?>><a href="/dashboard/policy/list/" <?php echo ($isPolicyListActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Policies</a></li>
                    <li <?php echo ($isPolicyAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/policy/add/" <?php echo ($isPolicyAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Policy</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li <?php echo ($isPolicyListActive ? 'class="active"' : ''); ?>><a href="/dashboard/policy/list/" <?php echo ($isPolicyListActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Policies</a></li>
                    <li <?php echo ($isPolicyAddActive ? 'class="active"' : ''); ?>><a href="/dashboard/policy/add/" <?php echo ($isPolicyAddActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Add New Policy</a></li>
                </ul>
            </div>
        </li>
    <?php } ?>

    <?php if (USER_TYPE == 'administrator') {
        $isSettingActive = isAsideUrlActive('/setting', $currentUrl, $requestPath);
        $isSettingAppActive = isAsideUrlActive('/setting/app', $currentUrl, $requestPath);
        $isSettingConfigActive = isAsideUrlActive('/setting/configuration', $currentUrl, $requestPath);
        $isSettingContactActive = isAsideUrlActive('/setting/contact', $currentUrl, $requestPath);
        $isSettingNotifActive = isAsideUrlActive('/setting/notifications', $currentUrl, $requestPath);
        $isSettingBankActive = isAsideUrlActive('/setting/bank', $currentUrl, $requestPath);
        $isSettingSeoActive = isAsideUrlActive('/setting/seo', $currentUrl, $requestPath);
        $isSettingProfileActive = isAsideUrlActive('/setting/profile', $currentUrl, $requestPath);
        $isSettingAllActive = $isSettingActive && !$isSettingAppActive && !$isSettingConfigActive && !$isSettingContactActive && !$isSettingNotifActive && !$isSettingBankActive && !$isSettingSeoActive && !$isSettingProfileActive;
    ?>
        <li <?php echo ($isSettingActive ? 'class="active"' : ''); ?> data-aside-nav="setting">
            <a href="/dashboard/setting/"><i class="fi fi-rr-settings"></i> Settings</a>
            <div class="side-menu">
                <ul>
                    <li <?php echo ($isSettingAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/" <?php echo ($isSettingAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Settings</a></li>
                    <li <?php echo ($isSettingAppActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/app/" <?php echo ($isSettingAppActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-toggle-on"></i> Features &amp; Modules</a></li>
                    <li <?php echo ($isSettingConfigActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/configuration/" <?php echo ($isSettingConfigActive ? 'class="active"' : ''); ?>><i class="fi-rr-settings-sliders"></i> Configuration</a></li>
                    <li <?php echo ($isSettingContactActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/contact/" <?php echo ($isSettingContactActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-phone-call"></i> Contact Details</a></li>
                    <li <?php echo ($isSettingNotifActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/notifications/" <?php echo ($isSettingNotifActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-bell"></i> Notifications</a></li>
                    <li <?php echo ($isSettingBankActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/bank/" <?php echo ($isSettingBankActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-bank"></i> Bank &amp; Payments</a></li>
                    <li <?php echo ($isSettingSeoActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/seo/" <?php echo ($isSettingSeoActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-search-alt"></i> SEO Settings</a></li>
                    <li <?php echo ($isSettingProfileActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/profile/" <?php echo ($isSettingProfileActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-user"></i> My Profile</a></li>
                </ul>
            </div>
            <div class="bottom-menu">
                <ul>
                    <li <?php echo ($isSettingAllActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/" <?php echo ($isSettingAllActive ? 'class="active"' : ''); ?>><i class="fi-rr-apps-sort"></i> All Settings</a></li>
                    <li <?php echo ($isSettingAppActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/app/" <?php echo ($isSettingAppActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-toggle-on"></i> Features &amp; Modules</a></li>
                    <li <?php echo ($isSettingConfigActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/configuration/" <?php echo ($isSettingConfigActive ? 'class="active"' : ''); ?>><i class="fi-rr-settings-sliders"></i> Configuration</a></li>
                    <li <?php echo ($isSettingContactActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/contact/" <?php echo ($isSettingContactActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-phone-call"></i> Contact Details</a></li>
                    <li <?php echo ($isSettingNotifActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/notifications/" <?php echo ($isSettingNotifActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-bell"></i> Notifications</a></li>
                    <li <?php echo ($isSettingBankActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/bank/" <?php echo ($isSettingBankActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-bank"></i> Bank &amp; Payments</a></li>
                    <li <?php echo ($isSettingSeoActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/seo/" <?php echo ($isSettingSeoActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-search-alt"></i> SEO Settings</a></li>
                    <li <?php echo ($isSettingProfileActive ? 'class="active"' : ''); ?>><a href="/dashboard/setting/profile/" <?php echo ($isSettingProfileActive ? 'class="active"' : ''); ?>><i class="fi fi-rr-user"></i> My Profile</a></li>
                </ul>
            </div>
        </li>
    <?php } ?>
<?php } ?>