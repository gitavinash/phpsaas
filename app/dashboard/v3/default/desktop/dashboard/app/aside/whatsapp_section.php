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
$waVisibilityClass = $isWaWebActive ? '' : ' is-hidden';
$currentAsideUrl = defined('ADMIN_BASE_URL') ? ADMIN_BASE_URL : (defined('ACTIVE_URL') ? ACTIVE_URL : '');
$isWaTemplatesPage = (strpos($currentAsideUrl, '/whatsapp/templates') !== false || strpos($currentAsideUrl, '/whatsapp-templates') !== false);
$isWaInboxPage = (strpos($currentAsideUrl, '/whatsapp/') !== false && !$isWaTemplatesPage && strpos($currentAsideUrl, '/integration/') === false);
$isWaDevicePage = (strpos($currentAsideUrl, '/integration/whatsapp_web') !== false);
?>

<li class="header-section-name<?php echo $waVisibilityClass; ?>" data-aside-plugin="whatsapp_web">
    <p>WhatsApp CRM<span>Live chats &amp; sync</span></p>
</li>

<li data-aside-plugin="whatsapp_web" class="<?php echo ($isWaInboxPage ? 'active' : '') . $waVisibilityClass; ?>">
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

<li data-aside-plugin="whatsapp_web" class="<?php echo ($isWaTemplatesPage ? 'active' : '') . $waVisibilityClass; ?>">
    <a href="/dashboard/whatsapp/templates/"><i class="fi fi-rr-document-signed"></i> WhatsApp Templates</a>
    <div class="side-menu">
        <ul>
            <li><a href="/dashboard/whatsapp/templates/"><i class="fi fi-rr-apps-sort"></i> All Templates</a></li>
            <li><a href="/dashboard/whatsapp/templates/?action=create"><i class="fi fi-rr-add"></i> Create Template</a></li>
            <li><a href="/dashboard/whatsapp/templates/?tab=actions"><i class="fi fi-rr-settings-sliders"></i> Action Defaults</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="/dashboard/whatsapp/templates/"><i class="fi fi-rr-apps-sort"></i> All Templates</a></li>
            <li><a href="/dashboard/whatsapp/templates/?action=create"><i class="fi fi-rr-add"></i> Create Template</a></li>
            <li><a href="/dashboard/whatsapp/templates/?tab=actions"><i class="fi fi-rr-settings-sliders"></i> Action Defaults</a></li>
        </ul>
    </div>
</li>

<li data-aside-plugin="whatsapp_web" class="<?php echo ($isWaDevicePage ? 'active' : '') . $waVisibilityClass; ?>">
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
