<?php
/**
 * V3 WhatsApp Chat Automation Plugin View
 * Path: /app/dashboard/v3/default/desktop/integration/whatsapp_automation/index.php
 */
?>
<div class="package-page-wrapper integration-plugin-wrapper" id="whatsappAutomationApp">

    <!-- Dashboard View Container -->
    <div id="waDashboardView" class="wa-dashboard-view">

        <!-- Header Banner -->
        <div class="package-header-bar">
            <div class="package-header-left">
                <div class="package-header-icon brand-whatsapp-bot wf-kpi-icon emerald">
                    <img src="/app/dashboard/v3/default/desktop/assets/images/integration/whatsapp.svg" alt="WhatsApp Automation">
                </div>
                <div class="package-header-titles">
                    <h2>WhatsApp Chat Automation</h2>
                    <p>Design visual chatbot workflows, keyword triggers, instant auto-replies, and human agent handoffs for incoming WhatsApp chats.</p>
                </div>
            </div>
            <div class="package-header-actions">
                <div class="wa-plugin-toggle-box">
                    <span class="wa-plugin-status-pill inactive" id="waPluginStatusPill">
                        <span class="status-pulse-dot"></span> Setup Mode
                    </span>
                    <label class="v3-toggle-switch" title="Enable or Disable WhatsApp Automation">
                        <input type="checkbox" id="togglePluginActiveWa">
                        <span class="v3-toggle-slider emerald"></span>
                    </label>
                </div>
                <a href="/dashboard/integration/" class="btn-v3-action" data-tooltip="Back to All Integrations">
                    <i class="fi fi-rr-arrow-left"></i> <span>All Integrations</span>
                </a>
                <button type="button" class="btn-v3-action btn-v3-primary btn-create-wa-wf" id="btnCreateWaWorkflow">
                    <i class="fi fi-rr-plus"></i> <span>Create Bot Workflow</span>
                </button>
            </div>
        </div>

        <!-- KPI Statistics Grid -->
        <div class="wf-kpi-grid">
            <div class="wf-kpi-card">
                <div class="wf-kpi-icon emerald">
                    <i class="fi fi-rr-comment-code"></i>
                </div>
                <div class="wf-kpi-info">
                    <span class="wf-kpi-value" id="statWaTotal">0</span>
                    <span class="wf-kpi-label">Total Workflows</span>
                </div>
            </div>
            <div class="wf-kpi-card">
                <div class="wf-kpi-icon indigo">
                    <i class="fi fi-rr-bolt"></i>
                </div>
                <div class="wf-kpi-info">
                    <span class="wf-kpi-value" id="statWaActive">0</span>
                    <span class="wf-kpi-label">Active Chat Bots</span>
                </div>
            </div>
            <div class="wf-kpi-card">
                <div class="wf-kpi-icon amber">
                    <i class="fi fi-rr-paper-plane"></i>
                </div>
                <div class="wf-kpi-info">
                    <span class="wf-kpi-value" id="statWaExecutions">0</span>
                    <span class="wf-kpi-label">Automated Replies</span>
                </div>
            </div>
            <div class="wf-kpi-card">
                <div class="wf-kpi-icon purple">
                    <i class="fi fi-rr-mobile-notch"></i>
                </div>
                <div class="wf-kpi-info">
                    <span class="wf-kpi-value">WhatsApp Web</span>
                    <span class="wf-kpi-label">Connected Gateway</span>
                </div>
            </div>
        </div>

        <!-- Navigation Category Tabs & Actions -->
        <div class="integration-toolbar-row">
            <div class="integration-category-tabs">
                <button type="button" class="integration-cat-tab wa-tab-btn active" data-tab="workflows">
                    <i class="fi fi-rr-network"></i> Bot Workflows
                </button>
                <button type="button" class="integration-cat-tab wa-tab-btn" data-tab="templates">
                    <i class="fi fi-rr-magic-wand"></i> 1-Click Templates
                </button>
                <button type="button" class="integration-cat-tab wa-tab-btn" data-tab="simulator">
                    <i class="fi fi-rr-play-alt"></i> Live Chat Simulator
                </button>
                <button type="button" class="integration-cat-tab wa-tab-btn" data-tab="logs">
                    <i class="fi fi-rr-time-past"></i> Execution Audit Logs
                </button>
            </div>
            <div class="integration-toolbar-right">
                <button type="button" class="btn-v3-action" id="btnRefreshWaWorkflows" title="Refresh Workflows">
                    <i class="fi fi-rr-refresh"></i> <span>Refresh</span>
                </button>
            </div>
        </div>

        <!-- Tab 1: Workflows List -->
        <div class="wa-tab-pane" id="waTab_workflows">
            <div class="wa-tab-content-card">
                <!-- Dedicated Workflow List Toolbar -->
                <div class="wa-list-toolbar" id="waWorkflowsToolbar">
                    <div class="wa-list-toolbar-left">
                        <div class="wa-search-box">
                            <i class="fi fi-rr-search"></i>
                            <input type="text" id="waWorkflowSearchInput" placeholder="Search by workflow name, trigger keyword, or step message..." autocomplete="off">
                            <button type="button" class="wa-search-clear is-hidden" id="waSearchClear" title="Clear search"><i class="fi fi-rr-cross-small"></i></button>
                        </div>
                        <div class="wa-filter-chips">
                            <button type="button" class="wa-filter-chip active" data-filter="all">All <span class="wa-filter-badge" id="waCountAll">0</span></button>
                            <button type="button" class="wa-filter-chip" data-filter="active">Active <span class="wa-filter-badge" id="waCountActive">0</span></button>
                            <button type="button" class="wa-filter-chip" data-filter="keyword">Keywords <span class="wa-filter-badge" id="waCountKeywords">0</span></button>
                            <button type="button" class="wa-filter-chip" data-filter="fallback">Fallback <span class="wa-filter-badge" id="waCountFallback">0</span></button>
                        </div>
                    </div>
                    <div class="wa-list-toolbar-right">
                        <button type="button" class="btn-v3-action btn-v3-secondary wa-test-sim-btn" id="btnListOpenSim">
                            <i class="fi fi-rr-play-alt"></i> Test Simulator
                        </button>
                        <button type="button" class="btn-v3-action btn-v3-primary btn-create-wa-wf" id="btnListCreateWf">
                            <i class="fi fi-rr-plus"></i> Create Workflow
                        </button>
                    </div>
                </div>

                <div id="waWorkflowsLoading" class="text-center py-4 text-muted">
                    <i class="fi fi-rr-spinner animate-spin"></i> Loading workflows...
                </div>
                <div id="waWorkflowsEmpty" class="text-center py-5">
                    <div class="mb-3 text-muted wa-empty-icon"><i class="fi fi-rr-comment-question"></i></div>
                    <h4 class="wa-empty-title">No Chatbot Workflows Created Yet</h4>
                    <p class="wa-empty-desc">
                        Create your first automated chat response rule or choose from our pre-built 1-click industry templates.
                    </p>
                    <button type="button" class="btn-v3-action btn-v3-primary btn-create-wa-wf" id="btnEmptyCreateWf">
                        <i class="fi fi-rr-plus"></i> Create Workflow
                    </button>
                </div>
                <div id="waWorkflowsNoMatch" class="text-center py-5 is-hidden">
                    <div class="mb-3 text-muted wa-empty-icon"><i class="fi fi-rr-search"></i></div>
                    <h4 class="wa-empty-title">No Workflows Matched Your Filter</h4>
                    <p class="wa-empty-desc">
                        Try searching with different terms or reset your active filters.
                    </p>
                    <button type="button" class="btn-v3-action btn-v3-secondary" id="btnResetWfFilters">
                        <i class="fi fi-rr-refresh"></i> Reset Filters
                    </button>
                </div>
                <div id="waWorkflowsTableWrapper" class="wa-workflows-table-container">
                    <table class="wa-workflows-table">
                        <thead>
                            <tr>
                                <th class="wa-col-name">Workflow &amp; Logic</th>
                                <th class="wa-col-trigger">Trigger Rules</th>
                                <th class="wa-col-preview">Response Preview &amp; Sequence</th>
                                <th class="wa-col-exec">Activity</th>
                                <th class="wa-col-status">Status</th>
                                <th class="wa-col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="waWorkflowsTbody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 2: 1-Click Templates -->
        <div class="wa-tab-pane" id="waTab_templates">
            <div class="wa-tab-content-card">
                <div class="mb-4">
                    <h3 class="wa-section-title">Ready-to-Use WhatsApp Bots</h3>
                    <p class="wa-section-subtitle">Instantly deploy pre-built conversational flows customized for travel agencies and service inquiries.</p>
                </div>
                <div class="row g-3 wa-templates-grid" id="waTemplatesGrid"></div>
            </div>
        </div>

        <!-- Tab 3: Interactive Chat Simulator -->
        <div class="wa-tab-pane" id="waTab_simulator">
            <div class="wa-tab-content-card">
                <div class="mb-4">
                    <h3 class="wa-section-title">Real-Time Interactive Chat Simulator</h3>
                    <p class="wa-section-subtitle">Test your automation rules by conversing with the simulated WhatsApp bot. Fast-track with quick test chips below.</p>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <span class="wa-sim-prompt-label">Quick Test Prompts:</span>
                        <button type="button" class="wa-keyword-tag wa-sim-chip">Hi</button>
                        <button type="button" class="wa-keyword-tag wa-sim-chip">Package prices</button>
                        <button type="button" class="wa-keyword-tag wa-sim-chip">Talk to agent</button>
                        <button type="button" class="wa-keyword-tag wa-sim-chip">Booking info</button>
                    </div>
                </div>

                <div class="wa-simulator-box">
                    <!-- Phone Mockup -->
                    <div class="wa-phone-mockup">
                        <div class="wa-phone-header">
                            <div class="wa-phone-avatar">
                                <i class="fi fi-rr-comment-alt-middle"></i>
                            </div>
                            <div>
                                <div class="wa-sim-header-name">Aleya Bot Assistant</div>
                                <div class="wa-sim-header-status">online &bull; automated</div>
                            </div>
                        </div>
                        <div class="wa-phone-chat-body" id="waChatMessagesBody">
                            <div class="wa-chat-bubble bot-reply">
                                Hello! 👋 I am your automated WhatsApp concierge. Type a message or click a test prompt above to test your workflows.
                            </div>
                        </div>
                        <div class="wa-phone-input-bar">
                            <input type="text" id="waSimInput" class="wa-phone-input" placeholder="Type a message (e.g. 'hi', 'price')...">
                            <button type="button" id="waSimSendBtn" class="wa-phone-send-btn">
                                <i class="fi fi-rr-paper-plane"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Simulation Trace Panel -->
                    <div class="wa-sim-trace-panel">
                        <div class="wa-sim-trace-title">
                            <i class="fi fi-rr-radar"></i> Step-by-Step Execution Trace
                        </div>
                        <p class="wa-sim-side-desc">
                            When you send a message in the phone emulator, the automation engine evaluates your active workflows and displays the execution breakdown here.
                        </p>
                        <div id="waSimTraceSteps">
                            <div class="wa-sim-empty-state">
                                Send a message to see the real-time rule evaluation path.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 4: Audit Logs -->
        <div class="wa-tab-pane" id="waTab_logs">
            <div class="wa-tab-content-card">
                <div class="wa-section-header-row">
                    <div>
                        <h3 class="wa-section-title">WhatsApp Execution Audit Trail</h3>
                        <p class="wa-section-subtitle">Review recent real-time triggers, processed phone numbers, and bot responses.</p>
                    </div>
                    <button type="button" class="btn-v3-action" id="btnRefreshWaLogs">
                        <i class="fi fi-rr-refresh"></i> Refresh Logs
                    </button>
                </div>
                <div class="wa-workflows-table-container">
                    <table class="wa-workflows-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Phone Number</th>
                                <th>Matched Workflow</th>
                                <th>Incoming Query</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="waLogsTbody"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div> <!-- /#waDashboardView -->

    <!-- Visual Workflow Builder View (Split into 2 Columns: 1. Inspector & Palette, 2. Drag & Drop Flow Canvas) -->
    <div id="waVisualBuilderView" class="wa-visual-builder-wrapper is-hidden">
        <!-- Builder Top Bar -->
        <div class="wa-builder-top-bar">
            <div class="wa-builder-top-left">
                <div class="wa-builder-brand-logo-wrap">
                    <a href="/dashboard/" class="wa-builder-brand-logo" title="Aleya Dashboard">
                        <img src="<?php echo !empty($app_image) ? $app_image : (defined('SITE_APP_IMAGE') ? SITE_APP_IMAGE : '/app/cms/visitor/blumox/desktop/assets/images/logo.svg'); ?>" alt="<?php echo !empty($app_title) ? $app_title : 'Aleya'; ?>">
                    </a>
                </div>
                <button type="button" class="wa-builder-icon-btn" id="btnToggleBuilderAside" title="Toggle Main Sidebar Navigation" aria-label="Toggle Sidebar Navigation">
                    <i class="fi fi-rr-menu-burger"></i>
                </button>
                <button type="button" class="wa-builder-icon-btn" id="btnExitVisualBuilder" title="Exit Visual Builder &amp; Return to Dashboard" aria-label="Back to Dashboard">
                    <i class="fi fi-rr-arrow-left"></i>
                </button>
                <div class="wa-builder-v-divider"></div>
                <div class="wa-builder-meta-indicator">
                    <span class="wa-builder-flow-badge"><i class="fi fi-rr-bolt"></i> Visual Flow</span>
                    <span class="wa-builder-active-name" id="builderTopWorkflowTitle">New Bot Flow</span>
                </div>
            </div>
            <div class="wa-builder-top-right">
                <div class="wa-builder-status-toggle-wrap">
                    <span class="wa-builder-toggle-label">Active:</span>
                    <label class="v3-toggle-switch" title="Activate or Deactivate Workflow">
                        <input type="checkbox" id="builderWorkflowActiveToggle" checked>
                        <span class="v3-toggle-slider emerald"></span>
                    </label>
                    <span class="wa-builder-status-badge is-active" id="builderStatusBadge">Active</span>
                </div>
                <button type="button" class="btn-v3-action" id="btnBuilderTestSim" title="Test this flow in the chat simulator">
                    <i class="fi fi-rr-play-alt"></i> <span>Simulator</span>
                </button>
                <button type="button" class="btn-v3-action btn-v3-primary" id="btnSaveVisualWorkflow">
                    <i class="fi fi-rr-check"></i> <span>Save Workflow</span>
                </button>
            </div>
        </div>

        <!-- 2-Column Split Layout -->
        <div class="wa-builder-layout">
            <!-- COLUMN 1: Workflow Details & Selected Element Inspector -->
            <div class="wa-builder-left-col">
                <div class="wa-builder-left-inner">
                    <!-- Workflow Metadata Section -->
                    <div class="wa-builder-panel-card">
                        <div class="wa-builder-panel-header">
                            <span class="wa-builder-panel-title"><i class="fi fi-rr-settings-sliders"></i> Workflow Details</span>
                            <span class="wa-builder-step-chip">Metadata</span>
                        </div>
                        <div class="v3-form-group">
                            <label for="builderWfTitleInput" class="v3-form-label">
                                Workflow Title <span class="required">*</span>
                            </label>
                            <input type="text" id="builderWfTitleInput" class="form-control form-control-sm" placeholder="e.g. Customer Greeting &amp; Tour Inquiries" required>
                        </div>
                        <div class="v3-form-group">
                            <label for="builderWfDescInput" class="v3-form-label">
                                Description <span class="lead-label-hint">Optional</span>
                            </label>
                            <input type="text" id="builderWfDescInput" class="form-control form-control-sm" placeholder="e.g. Greets incoming inquiries and provides tour assistance">
                        </div>
                    </div>

                    <!-- Selected Step Property Inspector -->
                    <div class="wa-builder-panel-card" id="builderInspectorPanel">
                        <div class="wa-builder-panel-header">
                            <span class="wa-builder-panel-title"><i class="fi fi-rr-cursor-finger"></i> Step Property Inspector</span>
                            <span class="wa-builder-step-chip" id="builderSelectedStepChip">Trigger Step</span>
                        </div>
                        <div id="builderInspectorContent">
                            <!-- Populated dynamically by JS for the currently selected node -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMN 2: Visual Drag and Drop Flow Canvas -->
            <div class="wa-builder-canvas-col" id="waBuilderCanvasCol">
                <!-- Non-Tech Quick Automation Recipes Strip -->
                <div class="wa-quick-templates-strip" id="waQuickTemplatesStrip">
                    <div class="wa-quick-strip-left">
                        <span class="wa-quick-strip-icon"><i class="fi fi-rr-magic-wand"></i></span>
                        <div class="wa-quick-strip-text">
                            <strong>Instant Automation Recipes</strong>
                            <span>1-click load pre-built workflows designed for non-tech users</span>
                        </div>
                    </div>
                    <div class="wa-quick-strip-chips">
                        <button type="button" class="wa-quick-recipe-btn" data-recipe="welcome_bot" title="Welcome new chats and present tour menu options">
                            <span class="recipe-emoji">⚡</span>
                            <span class="recipe-label">Welcome &amp; Tour Menu</span>
                        </button>
                        <button type="button" class="wa-quick-recipe-btn" data-recipe="lead_collector" title="Ask customer destination, travel dates and pax">
                            <span class="recipe-emoji">📋</span>
                            <span class="recipe-label">Collect Travel Dates</span>
                        </button>
                        <button type="button" class="wa-quick-recipe-btn" data-recipe="itinerary_bot" title="Automatically deliver PDF brochure with typing delay">
                            <span class="recipe-emoji">🏖️</span>
                            <span class="recipe-label">Holiday Brochure (PDF)</span>
                        </button>
                        <button type="button" class="wa-quick-recipe-btn" data-recipe="deposit_bot" title="Send booking deposit payment link on WhatsApp">
                            <span class="recipe-emoji">💳</span>
                            <span class="recipe-label">Booking Deposit Link</span>
                        </button>
                    </div>
                </div>

                <!-- Canvas Flow Nodes Sequence -->
                <div class="wa-flow-canvas-scroll" id="waFlowCanvasScroll">
                    <div class="wa-flow-nodes-container" id="waFlowNodesContainer">
                        <!-- Rendered dynamically by JS -->
                    </div>
                </div>

                <!-- Make.com Floating Bottom Dock Toolbar -->
                <div class="wa-make-bottom-dock" id="waMakeBottomDock">
                    <button type="button" class="wa-dock-btn is-run-once" id="btnMakeRunOnce" title="Run workflow test in simulator">
                        <i class="fi fi-rr-play"></i>
                        <span>Run once</span>
                    </button>
                    <div class="wa-dock-v-divider"></div>
                    <button type="button" class="wa-dock-btn" id="btnCanvasZoomOut" title="Zoom Out (Ctrl -)" aria-label="Zoom Out">
                        <i class="fi fi-rr-zoom-out"></i>
                    </button>
                    <span class="wa-dock-zoom-level" id="canvasZoomLevel">100%</span>
                    <button type="button" class="wa-dock-btn" id="btnCanvasZoomIn" title="Zoom In (Ctrl +)" aria-label="Zoom In">
                        <i class="fi fi-rr-zoom-in"></i>
                    </button>
                    <button type="button" class="wa-dock-btn" id="btnCanvasResetZoom" title="Reset View to 100%" aria-label="Reset View">
                        <i class="fi fi-rr-compress-alt"></i>
                    </button>
                    <div class="wa-dock-v-divider"></div>
                    <button type="button" class="wa-dock-btn is-auto-align" id="btnMakeAutoAlign" title="Auto-align canvas view">
                        <i class="fi fi-rr-magic-wand"></i>
                        <span>Align</span>
                    </button>
                    <div class="wa-dock-v-divider"></div>
                    <button type="button" class="wa-dock-btn is-save" id="btnMakeSaveWorkflow" title="Save Workflow">
                        <i class="fi fi-rr-disk"></i>
                        <span>Save</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Aside Menu Drawer Backdrop in Builder Mode -->
    <div class="wa-builder-sidebar-backdrop" id="waBuilderSidebarBackdrop"></div>

    <!-- Modal: Workflow Builder / Editor (Preserved as fallback) -->
    <div class="wa-modal-backdrop" id="waWorkflowModal">
        <div class="wa-modal-card">
            <div class="v3-modal-header">
                <div class="v3-modal-header-left">
                    <div class="v3-modal-header-icon-box icon-payment">
                        <i class="fi fi-rr-comment-code"></i>
                    </div>
                    <div class="v3-modal-title-wrap">
                        <h3 id="waModalTitle" class="v3-modal-title">Create WhatsApp Chat Workflow</h3>
                        <p class="v3-modal-subtitle">Configure keyword triggers, matching criteria, and automated bot replies.</p>
                    </div>
                </div>
                <button type="button" class="v3-modal-close-btn" id="btnCloseWaModal" aria-label="Close">
                    <i class="fi fi-rr-cross"></i>
                </button>
            </div>
            <form id="waWorkflowForm">
                <input type="hidden" id="waModalId" value="0">
                <div class="wa-modal-body">
                    <div class="v3-form-grid">
                        <div class="v3-form-group span-2">
                            <label for="waModalName" class="v3-form-label">
                                Workflow Title <span class="required">*</span>
                            </label>
                            <input type="text" id="waModalName" class="form-control form-control-sm" placeholder="e.g. Greeting &amp; Welcome Flow" required>
                        </div>

                        <div class="v3-form-group span-2">
                            <label for="waModalDesc" class="v3-form-label">
                                Description <span class="lead-label-hint">Optional</span>
                            </label>
                            <input type="text" id="waModalDesc" class="form-control form-control-sm" placeholder="e.g. Greets new leads and asks their preferred destination">
                        </div>

                        <div class="v3-form-group">
                            <label for="waModalTriggerType" class="v3-form-label">
                                Trigger Type
                            </label>
                            <select id="waModalTriggerType" class="form-select form-select-sm selectjs2 selectJS">
                                <option value="keyword">Incoming Keyword Match</option>
                                <option value="default_fallback">Default Fallback (Any Unmatched Message)</option>
                            </select>
                        </div>

                        <div class="v3-form-group">
                            <label for="waModalMatchType" class="v3-form-label">
                                Matching Logic
                            </label>
                            <select id="waModalMatchType" class="form-select form-select-sm selectjs2 selectJS">
                                <option value="contains">Message Contains Any Keyword</option>
                                <option value="exact">Exact Message Match</option>
                                <option value="starts_with">Message Starts With</option>
                            </select>
                        </div>

                        <div class="v3-form-group span-2" id="waKeywordsGroup">
                            <label for="waModalKeywords" class="v3-form-label">
                                Keywords (Comma Separated)
                            </label>
                            <input type="text" id="waModalKeywords" class="form-control form-control-sm" placeholder="e.g. hi, hello, price, package, quote">
                            <div class="form-text wa-form-hint">The bot triggers this workflow when a customer message matches any of these words.</div>
                        </div>

                        <div class="v3-form-group span-2">
                            <div class="wa-msg-header">
                                <label for="waModalReplyText" class="v3-form-label mb-0">
                                    Automated Reply Message <span class="required">*</span>
                                </label>
                                <div class="wa-token-group">
                                    <button type="button" class="btn-v3-action py-0 px-2 wa-token-btn lead-token-btn-sm" data-token="{name}">+ {name}</button>
                                    <button type="button" class="btn-v3-action py-0 px-2 wa-token-btn lead-token-btn-sm" data-token="{company}">+ {company}</button>
                                    <button type="button" class="btn-v3-action py-0 px-2 wa-token-btn lead-token-btn-sm" data-token="{phone}">+ {phone}</button>
                                </div>
                            </div>
                            <textarea id="waModalReplyText" class="form-control form-control-sm" rows="4" placeholder="Write your automated WhatsApp response message here..." required></textarea>
                        </div>

                        <div class="v3-form-group span-2">
                            <div class="wa-fallback-check">
                                <label class="v3-toggle-switch" title="Set as Default Fallback Bot">
                                    <input type="checkbox" id="waModalIsDefault">
                                    <span class="v3-toggle-slider emerald"></span>
                                </label>
                                <label class="wa-checkbox-label mb-0" for="waModalIsDefault">
                                    Set as Default Fallback Bot (triggers when incoming chat matches no specific keyword rules)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wa-modal-footer">
                    <div class="lead-modal-footer-hint">
                        <i class="fi fi-rr-info"></i>
                        <span>Changes apply immediately to incoming WhatsApp chats.</span>
                    </div>
                    <div class="lead-modal-footer-actions">
                        <button type="button" class="btn-v3-action" id="btnCancelWaModal">Cancel</button>
                        <button type="submit" class="btn-v3-action btn-v3-primary" id="btnSaveWaWorkflow">
                            <i class="fi fi-rr-check"></i> Save Workflow
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Action Suggestion Picker Modal (Triggered when clicking + button on flow canvas) -->
    <div class="wa-modal-backdrop" id="waActionPickerModal">
        <div class="wa-modal-card wa-action-picker-card">
            <div class="v3-modal-header">
                <div class="v3-modal-header-left">
                    <div class="v3-modal-header-icon-box icon-emerald">
                        <i class="fi fi-rr-apps-add"></i>
                    </div>
                    <div class="v3-modal-title-wrap">
                        <h3 class="v3-modal-title">Add Flow Step</h3>
                        <p class="v3-modal-subtitle">Choose a pre-configured controller action or predefined template to link into your workflow.</p>
                    </div>
                </div>
                <button type="button" class="v3-modal-close-btn" id="btnCloseActionPicker" aria-label="Close">
                    <i class="fi fi-rr-cross"></i>
                </button>
            </div>
            <div class="wa-modal-body wa-action-picker-body">
                <!-- Search & Category Filters -->
                <div class="wa-picker-search-bar">
                    <div class="wa-picker-search-box">
                        <i class="fi fi-rr-search"></i>
                        <input type="text" id="actionPickerSearchInput" placeholder="Search actions (e.g. itinerary, buttons, payment, handoff)...">
                    </div>
                    <div class="wa-picker-category-pills">
                        <button type="button" class="wa-picker-cat-btn active" data-cat="all">All Actions</button>
                        <button type="button" class="wa-picker-cat-btn" data-cat="presets">✨ Predefined Options</button>
                        <button type="button" class="wa-picker-cat-btn" data-cat="messaging">Messaging</button>
                        <button type="button" class="wa-picker-cat-btn" data-cat="travel">Travel &amp; Itinerary</button>
                        <button type="button" class="wa-picker-cat-btn" data-cat="routing">Routing &amp; CRM</button>
                    </div>
                </div>

                <!-- Action Cards Grid -->
                <div class="wa-action-cards-grid" id="waActionCardsGrid">
                    <!-- PREDEFINED OPTION 1: Destination Choice Menu -->
                    <div class="wa-action-card is-preset-card" data-action-type="collect_input" data-action-preset="dest_picker" data-cat="presets travel">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-teal"><i class="fi fi-rr-map-marker"></i></div>
                            <span class="wa-action-badge is-preset">✨ Predefined</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Destination Choice Menu</h4>
                            <p class="wa-action-card-desc">Asks customer to pick holiday destination with 1-tap buttons (Manali, Kerala, Dubai, Ladakh).</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- PREDEFINED OPTION 2: Travel Dates & Month -->
                    <div class="wa-action-card is-preset-card" data-action-type="collect_input" data-action-preset="dates_picker" data-cat="presets travel">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-indigo"><i class="fi fi-rr-calendar"></i></div>
                            <span class="wa-action-badge is-preset">✨ Predefined</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Travel Dates / Month Selector</h4>
                            <p class="wa-action-card-desc">Quick buttons for Next Month, Summer Vacation, Winter / Year-End, or Flexible Dates.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- PREDEFINED OPTION 3: Group Size / Pax -->
                    <div class="wa-action-card is-preset-card" data-action-type="collect_input" data-action-preset="pax_picker" data-cat="presets travel">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-cyan"><i class="fi fi-rr-users"></i></div>
                            <span class="wa-action-badge is-preset">✨ Predefined</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Group Size &amp; Pax Selector</h4>
                            <p class="wa-action-card-desc">Collect number of travelers: Couple (2 Pax), Family (3-4), Group (5-10), or Corporate.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- PREDEFINED OPTION 4: Budget Range -->
                    <div class="wa-action-card is-preset-card" data-action-type="collect_input" data-action-preset="budget_picker" data-cat="presets travel">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-emerald"><i class="fi fi-rr-money"></i></div>
                            <span class="wa-action-badge is-preset">✨ Predefined</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Budget Range Inquiry</h4>
                            <p class="wa-action-card-desc">Segment customer budget: Under ₹30k, ₹30k - ₹60k, ₹60k - ₹1.2L, or Ultra VIP Luxury.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- PREDEFINED OPTION 5: Welcome & Agency Menu -->
                    <div class="wa-action-card is-preset-card" data-action-type="interactive_menu" data-action-preset="welcome_menu" data-cat="presets messaging">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-blue"><i class="fi fi-rr-list-check"></i></div>
                            <span class="wa-action-badge is-preset">✨ Predefined</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Welcome &amp; Services Menu</h4>
                            <p class="wa-action-card-desc">Greeting + 1-tap options: 1. Popular Tour Packages, 2. Get Custom Quote, 3. Speak to Specialist.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- PREDEFINED OPTION 6: Dispatch Verified Tour Itinerary PDF -->
                    <div class="wa-action-card is-preset-card" data-action-type="send_itinerary" data-action-preset="tour_itinerary" data-cat="presets travel">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-rose"><i class="fi fi-rr-document-signed"></i></div>
                            <span class="wa-action-badge is-preset">✨ Predefined</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Send Tour Itinerary PDF</h4>
                            <p class="wa-action-card-desc">Dispatches verified tour itinerary PDF brochure with day-wise plan, hotel inclusions &amp; price.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- PREDEFINED OPTION 7: Advance Token Payment Link -->
                    <div class="wa-action-card is-preset-card" data-action-type="payment_link" data-action-preset="advance_token" data-cat="presets travel">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-emerald"><i class="fi fi-rr-credit-card"></i></div>
                            <span class="wa-action-badge is-preset">✨ Predefined</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Advance Token Deposit (₹5,000)</h4>
                            <p class="wa-action-card-desc">Instant booking link to lock in reservations and secure peak season slot discounts.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- PREDEFINED OPTION 8: Route to Destination Specialist -->
                    <div class="wa-action-card is-preset-card" data-action-type="handoff_agent" data-action-preset="specialist_handoff" data-cat="presets routing">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-purple"><i class="fi fi-rr-user-headset"></i></div>
                            <span class="wa-action-badge is-preset">✨ Predefined</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Destination Specialist Handoff</h4>
                            <p class="wa-action-card-desc">Transfers qualified customer to dedicated booking advisor with CRM notification.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- PREDEFINED OPTION 9: Hot Lead Qualifier -->
                    <div class="wa-action-card is-preset-card" data-action-type="tag_lead" data-action-preset="hot_qualifier" data-cat="presets routing">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-orange"><i class="fi fi-rr-tag"></i></div>
                            <span class="wa-action-badge is-preset">✨ Predefined</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Hot Lead Stage &amp; CRM Tags</h4>
                            <p class="wa-action-card-desc">Moves lead to 'Hot Lead - Requested Itinerary' and applies 'whatsapp_lead', 'high_intent'.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- 1. Send WhatsApp Reply -->
                    <div class="wa-action-card" data-action-type="reply_text" data-cat="messaging">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-blue"><i class="fi fi-rr-comment-alt"></i></div>
                            <span class="wa-action-badge is-popular">Standard</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Send WhatsApp Reply</h4>
                            <p class="wa-action-card-desc">Send automated text reply with dynamic tags ({name}, {company}, {destination}).</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- 2. Buttons / Quick Replies -->
                    <div class="wa-action-card" data-action-type="interactive_menu" data-cat="messaging">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-indigo"><i class="fi fi-rr-list-check"></i></div>
                            <span class="wa-action-badge is-interactive">Interactive</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Buttons / Quick Replies</h4>
                            <p class="wa-action-card-desc">Offer 1-tap WhatsApp response choices (e.g. Packages, Instant Quote, Specialist).</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- 3. Collect Customer Requirement -->
                    <div class="wa-action-card" data-action-type="collect_input" data-cat="travel">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-teal"><i class="fi fi-rr-edit"></i></div>
                            <span class="wa-action-badge is-crm">Lead Intake</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Collect Requirement</h4>
                            <p class="wa-action-card-desc">Ask for Destination, Travel Dates, Pax, or Budget; saves to CRM lead fields.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- 4. Send Tour Package Itinerary -->
                    <div class="wa-action-card" data-action-type="send_itinerary" data-cat="travel">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-rose"><i class="fi fi-rr-document-signed"></i></div>
                            <span class="wa-action-badge is-featured">Travel Special</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Send Itinerary Brochure PDF</h4>
                            <p class="wa-action-card-desc">Fetch and dispatch verified tour package PDF brochure with daily plans &amp; pricing.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- 5. If / Else Condition Branch -->
                    <div class="wa-action-card" data-action-type="condition_branch" data-cat="routing">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-cyan"><i class="fi fi-rr-code-branch"></i></div>
                            <span class="wa-action-badge is-logic">Smart Logic</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">If / Else Condition Branch</h4>
                            <p class="wa-action-card-desc">Route customer based on button clicked, destination, or budget range.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- 6. Payment Link / Advance Deposit -->
                    <div class="wa-action-card" data-action-type="payment_link" data-cat="travel">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-emerald"><i class="fi fi-rr-credit-card"></i></div>
                            <span class="wa-action-badge is-finance">Payments</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Payment Link / Advance Token</h4>
                            <p class="wa-action-card-desc">Generate instant payment gateway link to collect advance booking deposit.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- 7. Human Agent Handoff -->
                    <div class="wa-action-card" data-action-type="handoff_agent" data-cat="routing">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-purple"><i class="fi fi-rr-user-headset"></i></div>
                            <span class="wa-action-badge is-support">Live Agent</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Human Agent Handoff</h4>
                            <p class="wa-action-card-desc">Pause chatbot and route conversation to live sales agent with CRM inbox alert.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- 8. Cadence / Typing Delay -->
                    <div class="wa-action-card" data-action-type="delay" data-cat="messaging">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-amber"><i class="fi fi-rr-hourglass"></i></div>
                            <span class="wa-action-badge is-timing">Natural Feel</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Typing Cadence Delay</h4>
                            <p class="wa-action-card-desc">Simulate realistic human typing indicator for 1–15s before next message.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>

                    <!-- 9. Update Lead Stage & Tags -->
                    <div class="wa-action-card" data-action-type="tag_lead" data-cat="routing">
                        <div class="wa-action-card-top">
                            <div class="wa-action-card-icon icon-orange"><i class="fi fi-rr-tag"></i></div>
                            <span class="wa-action-badge is-crm">Pipeline</span>
                        </div>
                        <div class="wa-action-card-content">
                            <h4 class="wa-action-card-title">Update Lead Stage &amp; Tags</h4>
                            <p class="wa-action-card-desc">Tag contact in CRM (e.g. 'Hot Lead') and update lead status stage.</p>
                        </div>
                        <div class="wa-action-card-footer">
                            <span class="wa-action-btn-insert"><i class="fi fi-rr-plus"></i> Link this Action</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
