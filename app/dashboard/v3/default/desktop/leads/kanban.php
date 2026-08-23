<div class="dashboard-top-bar">
    <div class="dashboard-title-box">
        <h2><i class="fi-rr-apps"></i> Lead Pipeline Kanban Workspace</h2>
        <p>Interactive real-time drag & drop lead management pipeline</p>
    </div>
    <div class="dashboard-datepicker">
        <div class="kanban-search-box">
            <span class="kanban-search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
            <input type="text" class="kanban-search-input" id="kanbanSearchInput" placeholder="Search leads by name, phone, destination...">
            <button type="button" class="kanban-search-clear-btn" id="kanbanSearchClearBtn" title="Clear search">&times;</button>
        </div>
        <a href="<?php echo BASE_URL ?>leads/add/" class="btn btn-purple">
            <i class="fi fi-rr-plus"></i> Add New Lead
        </a>
    </div>
</div>

<div class="kanban-widget-wrapper kanban-workspace-page" id="kanbanWidgetWrapper">
    <div class="kanban-columns-grid">
        <!-- Fresh Enquiries Column -->
        <div class="kanban-column col-fresh" data-status="0">
            <div class="kanban-column-header">
                <span class="kanban-column-title"><i class="fi-rr-inbox"></i> Fresh</span>
                <span class="kanban-count-pill" id="kanban-count-0">0</span>
            </div>
            <div class="kanban-cards-container" id="kanban-container-0" data-status="0">
                <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
            </div>
        </div>

        <!-- Pending Column -->
        <div class="kanban-column col-pending" data-status="2">
            <div class="kanban-column-header">
                <span class="kanban-column-title"><i class="fi-rr-time-fast"></i> Pending</span>
                <span class="kanban-count-pill" id="kanban-count-2">0</span>
            </div>
            <div class="kanban-cards-container" id="kanban-container-2" data-status="2">
                <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
            </div>
        </div>

        <!-- Follow-Up Column -->
        <div class="kanban-column col-followup" data-status="3">
            <div class="kanban-column-header">
                <span class="kanban-column-title"><i class="fi-rr-phone-call"></i> Follow Up</span>
                <span class="kanban-count-pill" id="kanban-count-3">0</span>
            </div>
            <div class="kanban-cards-container" id="kanban-container-3" data-status="3">
                <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
            </div>
        </div>

        <!-- Confirmed Column -->
        <div class="kanban-column col-confirmed" data-status="1">
            <div class="kanban-column-header">
                <span class="kanban-column-title"><i class="fi-rr-check-circle"></i> Confirmed</span>
                <span class="kanban-count-pill" id="kanban-count-1">0</span>
            </div>
            <div class="kanban-cards-container" id="kanban-container-1" data-status="1">
                <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
            </div>
        </div>
    </div>
</div>
