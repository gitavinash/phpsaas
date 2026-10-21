<?php

/**
 * V3 Lead Pipeline Kanban Workspace
 * File: app/dashboard/v3/default/desktop/leads/kanban.php
 * Workspace Compliant: External CSS/JS, unified package-header-bar, pipeline switcher (List/Kanban), Add Lead CTA.
 */

if (isset($site_access_type) && $site_access_type === 'digital_marketing') {
    header("Location: /dashboard/integration/facebook/");
    exit;
}

$userRole       = strtolower(isset($site_access_type) ? $site_access_type : (defined('USER_TYPE') ? USER_TYPE : ''));
$isCallingAgent = ($userRole === 'agent' || $userRole === 'calling_agent' || $userRole === 'sales_executive');

$loggedInUsername = '';
if (!empty($_COOKIE['user_name'])) {
    $loggedInUsername = trim((string)base64_decode($_COOKIE['user_name']));
}
if (empty($loggedInUsername) && !empty($_SESSION['user_name'])) {
    $loggedInUsername = trim((string)$_SESSION['user_name']);
}
if (empty($loggedInUsername) && !empty($_SESSION['user_username'])) {
    $loggedInUsername = trim((string)$_SESSION['user_username']);
}
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Our Team';
?>
<div class="package-page-wrapper leads-page-wrapper leads-kanban-page-wrapper<?php echo $isCallingAgent ? ' kanban-calling-agent' : ''; ?>" id="kanbanApp" data-user-role="<?php echo htmlspecialchars($userRole, ENT_QUOTES); ?>" data-user-name="<?php echo htmlspecialchars($loggedInUsername ?: 'Our Team', ENT_QUOTES); ?>" data-site-name="<?php echo htmlspecialchars($siteName, ENT_QUOTES); ?>">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon sky">
                <i class="fi fi-rr-apps"></i>
            </div>
            <div class="package-header-titles">
                <h2>Lead Pipeline Kanban Workspace</h2>
                <p>Interactive real-time drag &amp; drop lead management pipeline</p>
            </div>
        </div>
        <div class="package-header-actions">
            <!-- Real-time Kanban Search Box -->
            <div class="kanban-search-box">
                <span class="kanban-search-icon">
                    <i class="fi fi-rr-search"></i>
                </span>
                <input type="text" class="kanban-search-input" id="kanbanSearchInput" placeholder="<?php echo (isset($site_access_type) && $site_access_type === 'digital_marketing') ? 'Search leads by name, destination...' : 'Search leads by name, phone, destination...'; ?>">
                <button type="button" class="kanban-search-clear-btn" id="kanbanSearchClearBtn" title="Clear search">&times;</button>
            </div>

            <!-- View Switcher (List vs Kanban) -->
            <div class="leads-pipeline-toggle">
                <a href="/dashboard/leads/list/" class="leads-view-tab" title="Switch to Table List View">
                    <i class="fi fi-rr-list"></i> <span>List</span>
                </a>
                <a href="/dashboard/leads/kanban/" class="leads-view-tab active" title="Kanban Board View">
                    <i class="fi fi-rr-apps"></i> <span>Kanban</span>
                </a>
            </div>

            <?php if (!isset($site_access_type) || $site_access_type !== 'digital_marketing'): ?>
                <!-- Primary Add Lead CTA Button -->
                <a href="/dashboard/leads/add/" class="btn-add-pkg-v3" title="Add New Lead">
                    <i class="fi fi-rr-plus"></i> <span>Add New Lead</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Kanban Workspace Grid -->
    <div class="kanban-widget-wrapper kanban-workspace-page<?php echo $isCallingAgent ? ' kanban-calling-agent' : ''; ?>" id="kanbanWidgetWrapper">
        <div class="kanban-columns-grid">
            <?php if (!$isCallingAgent): ?>
            <!-- 1. New Leads Column -->
            <div class="kanban-column col-fresh" data-status="0">
                <div class="kanban-column-header">
                    <span class="kanban-column-title"><i class="fi fi-rr-sparkles"></i> New Leads</span>
                    <span class="kanban-count-pill" id="kanban-count-0">0</span>
                </div>
                <div class="kanban-cards-container" id="kanban-container-0" data-status="0">
                    <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 2. Assigned Column -->
            <div class="kanban-column col-assigned" data-status="2">
                <div class="kanban-column-header">
                    <span class="kanban-column-title"><i class="fi fi-rr-user-time"></i> Assigned</span>
                    <span class="kanban-count-pill" id="kanban-count-2">0</span>
                </div>
                <div class="kanban-cards-container" id="kanban-container-2" data-status="2">
                    <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
                </div>
            </div>

            <!-- 3. Follow Up Column -->
            <div class="kanban-column col-followup" data-status="3">
                <div class="kanban-column-header">
                    <span class="kanban-column-title"><i class="fi fi-rr-phone-call"></i> Follow Up</span>
                    <span class="kanban-count-pill" id="kanban-count-3">0</span>
                </div>
                <div class="kanban-cards-container" id="kanban-container-3" data-status="3">
                    <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
                </div>
            </div>

            <!-- 4. Itinerary / Quotation Sent Column -->
            <div class="kanban-column col-itinerary" data-status="9">
                <div class="kanban-column-header">
                    <span class="kanban-column-title"><i class="fi fi-rr-paper-plane"></i> Itinerary Sent</span>
                    <span class="kanban-count-pill" id="kanban-count-9">0</span>
                </div>
                <div class="kanban-cards-container" id="kanban-container-9" data-status="9">
                    <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
                </div>
            </div>

            <!-- 5. Payment Requested Column -->
            <div class="kanban-column col-payment" data-status="6">
                <div class="kanban-column-header">
                    <span class="kanban-column-title"><i class="fi fi-rr-credit-card"></i> Payment Req.</span>
                    <span class="kanban-count-pill" id="kanban-count-6">0</span>
                </div>
                <div class="kanban-cards-container" id="kanban-container-6" data-status="6">
                    <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
                </div>
            </div>

            <!-- 6. Confirmed Column -->
            <div class="kanban-column col-confirmed" data-status="1">
                <div class="kanban-column-header">
                    <span class="kanban-column-title"><i class="fi fi-rr-check-circle"></i> Confirmed</span>
                    <span class="kanban-count-pill" id="kanban-count-1">0</span>
                </div>
                <div class="kanban-cards-container" id="kanban-container-1" data-status="1">
                    <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once dirname(__DIR__) . '/templates/whatsapp/device_connect_modal.php'; ?>