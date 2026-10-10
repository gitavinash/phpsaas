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

$isWaWebActive = (defined('ENABLE_WHATSAPP_WEB_API') ? (ENABLE_WHATSAPP_WEB_API == '1' || ENABLE_WHATSAPP_WEB_API === 1) : true);
$waDisplayAttr = $isWaWebActive ? '' : 'style="display:none;"';
$currentAsideUrl = defined('ADMIN_BASE_URL') ? ADMIN_BASE_URL : (defined('ACTIVE_URL') ? ACTIVE_URL : '');
$isWaInboxPage = (strpos($currentAsideUrl, '/whatsapp/') !== false && strpos($currentAsideUrl, '/integration/') === false);
$isWaDevicePage = (strpos($currentAsideUrl, '/integration/whatsapp_web') !== false);
?>

<li class="header-section-name" data-aside-plugin="whatsapp_web" <?php echo $waDisplayAttr; ?>>
    <p>WhatsApp CRM<span>Live chats &amp; sync</span></p>
</li>

<li data-aside-plugin="whatsapp_web" <?php echo $waDisplayAttr; ?> <?php echo $isWaInboxPage ? 'class="active"' : ''; ?>>
    <a href="/dashboard/whatsapp/"><i class="fi fi-rr-comment-alt-middle"></i> WhatsApp Inbox</a>
    <div class="side-menu">
        <ul>
            <li><a href="/dashboard/whatsapp/"><i class="fi fi-rr-inbox-in"></i> All Conversations</a></li>
            <li><a href="/dashboard/whatsapp/?filter=unread"><i class="fi fi-rr-envelope-dot"></i> Unread Chats</a></li>
            <li><a href="/dashboard/whatsapp/?filter=leads"><i class="fi fi-rr-bullseye-arrow"></i> CRM Leads</a></li>
            <li><a href="/dashboard/integration/whatsapp_web/"><i class="fi fi-rr-qr-scan"></i> Linked Device (QR)</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="/dashboard/whatsapp/"><i class="fi fi-rr-inbox-in"></i> All Conversations</a></li>
            <li><a href="/dashboard/whatsapp/?filter=unread"><i class="fi fi-rr-envelope-dot"></i> Unread Chats</a></li>
            <li><a href="/dashboard/whatsapp/?filter=leads"><i class="fi fi-rr-bullseye-arrow"></i> CRM Leads</a></li>
            <li><a href="/dashboard/integration/whatsapp_web/"><i class="fi fi-rr-qr-scan"></i> Linked Device (QR)</a></li>
        </ul>
    </div>
</li>

<li data-aside-plugin="whatsapp_web" <?php echo $waDisplayAttr; ?> <?php echo $isWaDevicePage ? 'class="active"' : ''; ?>>
    <a href="/dashboard/integration/whatsapp_web/"><i class="fi fi-rr-qr-scan"></i> WhatsApp Device</a>
    <div class="side-menu">
        <ul>
            <li><a href="/dashboard/integration/whatsapp_web/"><i class="fi fi-rr-mobile-notch"></i> Pair Device (QR)</a></li>
            <li><a href="/dashboard/whatsapp/"><i class="fi fi-rr-comment-alt-middle"></i> Live Chat Inbox</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="/dashboard/integration/whatsapp_web/"><i class="fi fi-rr-mobile-notch"></i> Pair Device (QR)</a></li>
            <li><a href="/dashboard/whatsapp/"><i class="fi fi-rr-comment-alt-middle"></i> Live Chat Inbox</a></li>
        </ul>
    </div>
</li>
