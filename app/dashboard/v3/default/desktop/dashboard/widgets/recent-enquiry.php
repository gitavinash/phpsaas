<div class="kanban-widget-wrapper" id="kanbanWidgetWrapper">
    <div class="kanban-header-bar">
        <div class="kanban-header-title">
            <i class="fi-rr-apps"></i>
            <div>
                <h3>Lead Pipeline Kanban Board</h3>
                <p>Drag and drop leads to update status in real time</p>
            </div>
        </div>
        <div class="kanban-header-actions">
            <a href="<?php echo BASE_URL ?>leads/kanban/" class="btn-kanban-fullscreen" id="openKanbanPageBtn" style="text-decoration: none;">
                <i class="fi-rr-expand"></i> <span class="btn-fs-label">Open Full Kanban Page</span>
            </a>
        </div>
    </div>

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