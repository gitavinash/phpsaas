<?php

/**
 * Universal WhatsApp Web CRM Aside Menu Section
 * Path: /app/dashboard/v3/default/desktop/dashboard/app/aside/whatsapp_section.php
 * Displays for all package users when WhatsApp Web API is activated.
 */

if (defined('ISW_WA_ASIDE_LOADED')) {
    return;
}
define('ISW_WA_ASIDE_LOADED', true);

// 1. Resolve active site ID
$siteId = 0;
if (defined('SITE_ID') && (int)SITE_ID > 0) {
    $siteId = (int)SITE_ID;
} elseif (defined('APP_SITE_ID') && (int)APP_SITE_ID > 0) {
    $siteId = (int)APP_SITE_ID;
} elseif (isset($site_id) && (int)$site_id > 0) {
    $siteId = (int)$site_id;
} elseif (isset($_SESSION['site_id']) && (int)$_SESSION['site_id'] > 0) {
    $siteId = (int)$_SESSION['site_id'];
}

// 2. Ensure database connection is active
global $connection;
if ((!isset($connection) || !$connection) && defined('DB_HOST')) {
    $connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
}

$prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

// 3. Condition A: WhatsApp Web plugin is enabled
$isWaWebActive = false;
if (defined('ENABLE_WHATSAPP_WEB_API')) {
    $isWaWebActive = (ENABLE_WHATSAPP_WEB_API == '1' || ENABLE_WHATSAPP_WEB_API === 1 || ENABLE_WHATSAPP_WEB_API === 'true');
}

if (!$isWaWebActive && isset($connection) && $connection && $siteId > 0) {
    $appSiteId = defined('APP_SITE_ID') ? (int)APP_SITE_ID : 0;
    $siteCfgCond = "site_configuration_site_id = {$siteId}";
    if ($appSiteId > 0 && $appSiteId !== $siteId) {
        $siteCfgCond = "(site_configuration_site_id = {$siteId} OR site_configuration_site_id = {$appSiteId})";
    }
    $cfgQ = @mysqli_query($connection, "SELECT site_configuration_value FROM {$prefix}site_configuration WHERE {$siteCfgCond} AND site_configuration_key = 'enable_whatsapp_web_api' AND site_configuration_status = 1 LIMIT 1");
    if ($cfgQ && $cfgR = mysqli_fetch_assoc($cfgQ)) {
        $isWaWebActive = ($cfgR['site_configuration_value'] === '1' || $cfgR['site_configuration_value'] === 1);
    }
}

// 4. Condition B: At least 1 device is connected / active
$hasConnectedDevice = false;
if ($isWaWebActive && isset($connection) && $connection && $siteId > 0) {
    $appSiteId = defined('APP_SITE_ID') ? (int)APP_SITE_ID : 0;
    $siteDevCond = "device_site_id = {$siteId}";
    if ($appSiteId > 0 && $appSiteId !== $siteId) {
        $siteDevCond = "(device_site_id = {$siteId} OR device_site_id = {$appSiteId})";
    }
    $devConnQ = @mysqli_query($connection, "SELECT device_id FROM {$prefix}whatsapp_device WHERE {$siteDevCond} AND (device_status = 'connected' OR device_status = 'active') LIMIT 1");
    if ($devConnQ && mysqli_num_rows($devConnQ) > 0) {
        $hasConnectedDevice = true;
    }
}

// 5. Show WhatsApp CRM menu ONLY when at least 1 device is active AND WhatsApp Web is enabled
$isWhatsAppCrmActive = ($isWaWebActive && $hasConnectedDevice);
if (!$isWhatsAppCrmActive) {
    return;
}
$waVisibilityClass = '';

$waStatusBadge = $hasConnectedDevice
    ? '<span class="aside-menu-pill badge-live">Live</span>'
    : '<span class="aside-menu-pill badge-sync">Sync</span>';

$requestPath = $_SERVER['REQUEST_URI'] ?? '';
$currentAsideUrl = defined('ADMIN_BASE_URL') ? ADMIN_BASE_URL : (defined('ACTIVE_URL') ? ACTIVE_URL : $requestPath);
$waFilter = isset($_GET['filter']) ? (string)$_GET['filter'] : '';
$waAction = isset($_GET['action']) ? (string)$_GET['action'] : '';
$waTab = isset($_GET['tab']) ? (string)$_GET['tab'] : '';

$isWaDevicePage = (
    strpos($currentAsideUrl, '/integration/whatsapp_web') !== false ||
    strpos($requestPath, '/integration/whatsapp_web') !== false ||
    (isset($currentUrl) && strpos($currentUrl, '/integration/whatsapp_web') !== false)
);

$isWaTemplatesPage = !$isWaDevicePage && (
    strpos($currentAsideUrl, '/whatsapp/templates') !== false ||
    strpos($currentAsideUrl, '/whatsapp-templates') !== false ||
    strpos($requestPath, '/whatsapp/templates') !== false ||
    (isset($currentUrl) && strpos($currentUrl, '/whatsapp/templates') !== false)
);

$isWaInboxPage = !$isWaDevicePage && !$isWaTemplatesPage && (
    strpos($currentAsideUrl, '/whatsapp/') !== false ||
    strpos($requestPath, '/whatsapp/') !== false ||
    (isset($currentUrl) && strpos($currentUrl, '/whatsapp/') !== false)
) && strpos($currentAsideUrl, '/integration/') === false && strpos($requestPath, '/integration/') === false;

$isWaInboxAll = $isWaInboxPage && $waFilter === '';
$isWaInboxUnread = $isWaInboxPage && $waFilter === 'unread';
$isWaInboxLeads = $isWaInboxPage && $waFilter === 'leads';

$isWaTplAll = $isWaTemplatesPage && $waAction === '' && $waTab === '';
$isWaTplCreate = $isWaTemplatesPage && $waAction === 'create';
$isWaTplActions = $isWaTemplatesPage && $waTab === 'actions';
?>

<li class="header-section-name<?php echo $waVisibilityClass; ?>" data-aside-plugin="whatsapp_web">
    <p>WhatsApp CRM<span>Live chats &amp; sync</span></p>
</li>

<li data-aside-plugin="whatsapp_web" data-aside-nav="whatsapp" class="<?php echo ($isWaInboxPage ? 'active' : '') . $waVisibilityClass; ?>">
    <a href="/dashboard/whatsapp/"><i class="ri-whatsapp-line"></i> WhatsApp Inbox <?php echo $waStatusBadge; ?></a>
    <div class="side-menu">
        <ul>
            <li <?php echo ($isWaInboxAll ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/" <?php echo ($isWaInboxAll ? 'class="active"' : ''); ?>><i class="fi fi-rr-inbox-in"></i> All Conversations</a></li>
            <li <?php echo ($isWaInboxUnread ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/?filter=unread" <?php echo ($isWaInboxUnread ? 'class="active"' : ''); ?>><i class="fi fi-rr-envelope-dot"></i> Unread Chats</a></li>
            <li <?php echo ($isWaInboxLeads ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/?filter=leads" <?php echo ($isWaInboxLeads ? 'class="active"' : ''); ?>><i class="fi fi-rr-bullseye-arrow"></i> CRM Leads</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li <?php echo ($isWaInboxAll ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/" <?php echo ($isWaInboxAll ? 'class="active"' : ''); ?>><i class="fi fi-rr-inbox-in"></i> All Conversations</a></li>
            <li <?php echo ($isWaInboxUnread ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/?filter=unread" <?php echo ($isWaInboxUnread ? 'class="active"' : ''); ?>><i class="fi fi-rr-envelope-dot"></i> Unread Chats</a></li>
            <li <?php echo ($isWaInboxLeads ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/?filter=leads" <?php echo ($isWaInboxLeads ? 'class="active"' : ''); ?>><i class="fi fi-rr-bullseye-arrow"></i> CRM Leads</a></li>
        </ul>
    </div>
</li>

<li data-aside-plugin="whatsapp_web" data-aside-nav="whatsapp-templates" class="<?php echo ($isWaTemplatesPage ? 'active' : '') . $waVisibilityClass; ?>">
    <a href="/dashboard/whatsapp/templates/"><i class="fi fi-rr-document-signed"></i> WhatsApp Templates</a>
    <div class="side-menu">
        <ul>
            <li <?php echo ($isWaTplAll ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/templates/" <?php echo ($isWaTplAll ? 'class="active"' : ''); ?>><i class="fi fi-rr-apps-sort"></i> All Templates</a></li>
            <li <?php echo ($isWaTplCreate ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/templates/?action=create" <?php echo ($isWaTplCreate ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Create Template</a></li>
            <li <?php echo ($isWaTplActions ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/templates/?tab=actions" <?php echo ($isWaTplActions ? 'class="active"' : ''); ?>><i class="fi fi-rr-settings-sliders"></i> Action Defaults</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li <?php echo ($isWaTplAll ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/templates/" <?php echo ($isWaTplAll ? 'class="active"' : ''); ?>><i class="fi fi-rr-apps-sort"></i> All Templates</a></li>
            <li <?php echo ($isWaTplCreate ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/templates/?action=create" <?php echo ($isWaTplCreate ? 'class="active"' : ''); ?>><i class="fi fi-rr-add"></i> Create Template</a></li>
            <li <?php echo ($isWaTplActions ? 'class="active"' : ''); ?>><a href="/dashboard/whatsapp/templates/?tab=actions" <?php echo ($isWaTplActions ? 'class="active"' : ''); ?>><i class="fi fi-rr-settings-sliders"></i> Action Defaults</a></li>
        </ul>
    </div>
</li>

<li data-aside-plugin="whatsapp_web" data-aside-nav="integration/whatsapp_web" class="<?php echo ($isWaDevicePage ? 'active' : '') . $waVisibilityClass; ?>">
    <a href="/dashboard/integration/whatsapp_web/"><i class="fi fi-rr-qr-scan"></i> WhatsApp Device</a>
    <div class="side-menu">
        <ul>
            <li <?php echo ($isWaDevicePage ? 'class="active"' : ''); ?>><a href="/dashboard/integration/whatsapp_web/" <?php echo ($isWaDevicePage ? 'class="active"' : ''); ?>><i class="fi fi-rr-mobile-notch"></i> Pair Device (QR)</a></li>
            <li><a href="/dashboard/whatsapp/"><i class="ri-whatsapp-line"></i> Live Chat Inbox</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li <?php echo ($isWaDevicePage ? 'class="active"' : ''); ?>><a href="/dashboard/integration/whatsapp_web/" <?php echo ($isWaDevicePage ? 'class="active"' : ''); ?>><i class="fi fi-rr-mobile-notch"></i> Pair Device (QR)</a></li>
            <li><a href="/dashboard/whatsapp/"><i class="ri-whatsapp-line"></i> Live Chat Inbox</a></li>
        </ul>
    </div>
</li>