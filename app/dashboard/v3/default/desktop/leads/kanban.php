<?php
/**
 * V3 Lead Pipeline Kanban Workspace
 * File: app/dashboard/v3/default/desktop/leads/kanban.php
 * Workspace Compliant: External CSS/JS, unified package-header-bar, pipeline switcher (List/Kanban), Add Lead CTA.
 */
?>
<div class="package-page-wrapper leads-page-wrapper leads-kanban-page-wrapper" id="kanbanApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
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
                <input type="text" class="kanban-search-input" id="kanbanSearchInput" placeholder="Search leads by name, phone, destination...">
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

            <!-- Primary Add Lead CTA Button -->
            <a href="/dashboard/leads/add/" class="btn-add-pkg-v3" title="Add New Lead">
                <i class="fi fi-rr-plus"></i> <span>Add New Lead</span>
            </a>
        </div>
    </div>

    <!-- Kanban Workspace Grid -->
    <div class="kanban-widget-wrapper kanban-workspace-page" id="kanbanWidgetWrapper">
        <div class="kanban-columns-grid">
            <!-- Pending Column -->
            <div class="kanban-column col-fresh" data-status="0">
                <div class="kanban-column-header">
                    <span class="kanban-column-title"><i class="fi fi-rr-time-fast"></i> Pending</span>
                    <span class="kanban-count-pill" id="kanban-count-0">0</span>
                </div>
                <div class="kanban-cards-container" id="kanban-container-0" data-status="0">
                    <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
                </div>
            </div>

            <!-- Follow-Up Column -->
            <div class="kanban-column col-followup" data-status="3">
                <div class="kanban-column-header">
                    <span class="kanban-column-title"><i class="fi fi-rr-phone-call"></i> Follow Up</span>
                    <span class="kanban-count-pill" id="kanban-count-3">0</span>
                </div>
                <div class="kanban-cards-container" id="kanban-container-3" data-status="3">
                    <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
                </div>
            </div>

            <!-- Itinerary Sent Column -->
            <div class="kanban-column col-pending" data-status="2">
                <div class="kanban-column-header">
                    <span class="kanban-column-title"><i class="fi fi-rr-paper-plane"></i> Itinerary Sent</span>
                    <span class="kanban-count-pill" id="kanban-count-2">0</span>
                </div>
                <div class="kanban-cards-container" id="kanban-container-2" data-status="2">
                    <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
                </div>
            </div>

            <!-- Confirmed Column -->
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