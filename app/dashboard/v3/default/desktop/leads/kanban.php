<div class="dashboard-top-bar">
    <div class="dashboard-title-box">
        <h2><i class="fi-rr-apps" style="color: var(--brand-600); margin-right: 8px;"></i> Lead Pipeline Kanban Workspace</h2>
        <p>Interactive real-time drag & drop lead management pipeline</p>
    </div>
    <div class="dashboard-datepicker" style="gap: 10px;">
        <div class="kanban-search-box" style="position: relative; display: flex; align-items: center;">
            <i class="fi-rr-search" style="position: absolute; left: 12px; color: var(--slate-400); font-size: 13px;"></i>
            <input type="text" id="kanbanSearchInput" placeholder="Filter leads by name, phone..." style="padding: 7px 12px 7px 34px; font-size: 12px; border: 1px solid var(--slate-200); border-radius: var(--radius-md); width: 220px; outline: none; background: #ffffff;">
        </div>
        <a href="<?php echo BASE_URL ?>leads/add/" class="btn btn-purple" style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 7px 14px; text-decoration: none;">
            <i class="fi-rr-plus"></i> Add New Lead
        </a>
    </div>
</div>

<div class="kanban-widget-wrapper kanban-workspace-page" id="kanbanWidgetWrapper" style="box-shadow: var(--shadow-sm); border-radius: var(--radius-lg); background: #ffffff; padding: 20px; border: 1px solid var(--border-subtle); margin-top: 16px;">
    <div class="kanban-columns-grid">
        <!-- Fresh Enquiries Column -->
        <div class="kanban-column col-fresh" data-status="0">
            <div class="kanban-column-header">
                <span class="kanban-column-title"><i class="fi-rr-inbox"></i> Fresh</span>
                <span class="kanban-count-pill" id="kanban-count-0">0</span>
            </div>
            <div class="kanban-cards-container" id="kanban-container-0" data-status="0" style="max-height: calc(100vh - 240px); min-height: 400px;">
                <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
            </div>
        </div>

        <!-- Pending Column -->
        <div class="kanban-column col-pending" data-status="2">
            <div class="kanban-column-header">
                <span class="kanban-column-title"><i class="fi-rr-time-fast"></i> Pending</span>
                <span class="kanban-count-pill" id="kanban-count-2">0</span>
            </div>
            <div class="kanban-cards-container" id="kanban-container-2" data-status="2" style="max-height: calc(100vh - 240px); min-height: 400px;">
                <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
            </div>
        </div>

        <!-- Follow-Up Column -->
        <div class="kanban-column col-followup" data-status="3">
            <div class="kanban-column-header">
                <span class="kanban-column-title"><i class="fi-rr-phone-call"></i> Follow Up</span>
                <span class="kanban-count-pill" id="kanban-count-3">0</span>
            </div>
            <div class="kanban-cards-container" id="kanban-container-3" data-status="3" style="max-height: calc(100vh - 240px); min-height: 400px;">
                <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
            </div>
        </div>

        <!-- Confirmed Column -->
        <div class="kanban-column col-confirmed" data-status="1">
            <div class="kanban-column-header">
                <span class="kanban-column-title"><i class="fi-rr-check-circle"></i> Confirmed</span>
                <span class="kanban-count-pill" id="kanban-count-1">0</span>
            </div>
            <div class="kanban-cards-container" id="kanban-container-1" data-status="1" style="max-height: calc(100vh - 240px); min-height: 400px;">
                <div class="kanban-empty-drop-zone"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="Loading..."></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Quick search filter for Kanban Workspace cards
    const searchInput = document.getElementById("kanbanSearchInput");
    if (searchInput) {
        searchInput.addEventListener("input", function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll(".kanban-card").forEach(function(card) {
                const text = card.textContent.toLowerCase();
                if (text.includes(query)) {
                    card.style.display = "flex";
                } else {
                    card.style.display = "none";
                }
            });
        });
    }
});
</script>
