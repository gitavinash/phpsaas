<?php
$appLogo = !empty($app_image) ? $app_image : (defined('SITE_APP_IMAGE') ? SITE_APP_IMAGE : '/app/aleya/visitor/aleya/desktop/assets/images/aleya_logo.png');
$appName = !empty($app_title) ? $app_title : (defined('SITE_APP_NAME') ? SITE_APP_NAME : 'Aleya');
?>
<div class="package-page-wrapper workflows-page-wrapper" id="workflowsPluginApp">

    <!-- ========================================== -->
    <!-- VIEW 1: WORKFLOWS LIST & DASHBOARD VIEW    -->
    <!-- ========================================== -->
    <div class="workflows-view-container is-visible" id="workflowsListView">
        
        <!-- Header Banner -->
        <div class="package-header-bar workflows-header-bar">
            <div class="package-header-left">
                <div class="package-header-icon workflows-brand-icon">
                    <i class="fi fi-rr-workflow"></i>
                </div>
                <div class="package-header-titles">
                    <h2>Automation Workflows Studio</h2>
                    <p>Design visual drag-and-drop automation pipelines, lead routing, smart WhatsApp bots, and customer journeys.</p>
                </div>
            </div>
            <div class="package-header-actions">
                <a href="/dashboard/integration/" class="btn-v3-action" data-tooltip="Back to All Integrations">
                    <i class="fi fi-rr-apps"></i> <span>All Integrations</span>
                </a>
                <button type="button" class="btn-v3-primary" id="btnOpenCreateChoiceModal">
                    <i class="fi fi-rr-plus"></i> <span>Create Workflow</span>
                </button>
            </div>
        </div>

        <!-- Workflow Stats Row -->
        <div class="workflows-stats-grid">
            <div class="workflow-stat-card">
                <div class="stat-card-icon violet">
                    <i class="fi fi-rr-network"></i>
                </div>
                <div class="stat-card-info">
                    <span class="stat-card-number" id="statTotalWorkflows">0</span>
                    <span class="stat-card-label">Total Workflows</span>
                </div>
            </div>
            <div class="workflow-stat-card">
                <div class="stat-card-icon emerald">
                    <i class="fi fi-rr-bolt"></i>
                </div>
                <div class="stat-card-info">
                    <span class="stat-card-number" id="statActiveWorkflows">0</span>
                    <span class="stat-card-label">Active &amp; Live</span>
                </div>
            </div>
            <div class="workflow-stat-card">
                <div class="stat-card-icon blue">
                    <i class="fi fi-rr-refresh"></i>
                </div>
                <div class="stat-card-info">
                    <span class="stat-card-number" id="statTotalExecutions">0</span>
                    <span class="stat-card-label">Automations Executed</span>
                </div>
            </div>
            <div class="workflow-stat-card">
                <div class="stat-card-icon amber">
                    <i class="fi fi-rr-check-circle"></i>
                </div>
                <div class="stat-card-info">
                    <span class="stat-card-number">99.8%</span>
                    <span class="stat-card-label">Automation Success Rate</span>
                </div>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="workflows-toolbar-row">
            <div class="workflows-category-tabs" id="workflowCategoryTabs">
                <button type="button" class="workflow-cat-tab active" data-category="all">
                    <i class="fi fi-rr-apps"></i> All Flows
                </button>
                <button type="button" class="workflow-cat-tab" data-category="crm">
                    <i class="fi fi-rr-users-alt"></i> Lead Routing
                </button>
                <button type="button" class="workflow-cat-tab" data-category="whatsapp">
                    <i class="fi fi-rr-comment-alt"></i> WhatsApp Bots
                </button>
                <button type="button" class="workflow-cat-tab" data-category="payments">
                    <i class="fi fi-rr-credit-card"></i> Payments
                </button>
            </div>
            <div class="workflows-search-box">
                <i class="fi fi-rr-search"></i>
                <input type="text" id="workflowSearchInput" placeholder="Search workflows by name or trigger..." class="workflows-search-input">
            </div>
        </div>

        <!-- Workflows Grid / Cards -->
        <div class="workflows-cards-grid" id="workflowsCardsContainer">
            <!-- Dynamically populated via JS -->
            <div class="workflows-loading-state" id="workflowsLoadingState">
                <div class="workflows-spinner"></div>
                <p>Loading automation workflows...</p>
            </div>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- VIEW 2: MAKE.COM STYLE VISUAL FLOWCHART EDITOR (FULL CANVAS)   -->
    <!-- ============================================================== -->
    <div class="workflows-view-container is-hidden" id="workflowsEditorView">
        
        <!-- Editor Sticky Top Header -->
        <div class="workflow-editor-topbar">
            <div class="editor-topbar-left">
                <a href="/dashboard/" class="editor-header-logo" title="<?php echo htmlspecialchars($appName, ENT_QUOTES); ?>">
                    <img src="<?php echo htmlspecialchars($appLogo, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($appName, ENT_QUOTES); ?>">
                </a>
                <span class="editor-header-divider"></span>
                <button type="button" class="btn-editor-back" id="btnBackToWorkflowList" title="Back to Workflow List">
                    <i class="fi fi-rr-arrow-left"></i>
                </button>
                <div class="editor-workflow-title-wrap">
                    <div class="editor-workflow-title-row">
                        <input type="text" id="editorWorkflowName" class="editor-workflow-name-input" placeholder="Untitled Workflow..." value="New Automation Workflow" title="Click to rename workflow">
                        <i class="fi fi-rr-pencil editor-title-edit-icon" id="btnFocusWorkflowName" title="Rename workflow"></i>
                    </div>
                    <div class="editor-workflow-meta">
                        <span class="editor-category-badge" id="editorCategoryBadge">CRM Flow</span>
                        <span class="editor-save-state" id="editorSaveState"><i class="fi fi-rr-check"></i> All changes saved</span>
                    </div>
                </div>
            </div>

            <!-- Canvas Controls Center -->
            <div class="editor-topbar-center">
                <div class="editor-canvas-toolset">
                    <button type="button" class="canvas-tool-btn" id="btnAutoAlignFlowchart" title="Auto-Organize Flowchart (Tidy Layout)">
                        <i class="fi fi-rr-align-center"></i> <span>Auto Align</span>
                    </button>
                    <span class="tool-divider"></span>
                    <button type="button" class="canvas-tool-btn" id="btnZoomOut" title="Zoom Out">
                        <i class="fi fi-rr-minus-small"></i>
                    </button>
                    <span class="canvas-zoom-level" id="canvasZoomLevel">100%</span>
                    <button type="button" class="canvas-tool-btn" id="btnZoomIn" title="Zoom In">
                        <i class="fi fi-rr-plus-small"></i>
                    </button>
                    <button type="button" class="canvas-tool-btn" id="btnZoomReset" title="Reset View (100%)">
                        <i class="fi fi-rr-expand"></i>
                    </button>
                </div>
            </div>

            <!-- Actions Right -->
            <div class="editor-topbar-right">
                <div class="editor-status-toggle-wrap">
                    <span class="toggle-status-label" id="editorStatusText">Active</span>
                    <label class="v3-toggle-switch" title="Toggle Workflow Active / Inactive">
                        <input type="checkbox" id="editorStatusToggle" checked>
                        <span class="v3-toggle-slider violet"></span>
                    </label>
                </div>
                <button type="button" class="btn-editor-test" id="btnRunTestSimulation" title="Run Once (Simulate Flowchart Steps)">
                    <i class="fi fi-rr-play"></i> <span>Run Once</span>
                </button>
                <button type="button" class="btn-editor-save" id="btnSaveWorkflowFlowchart">
                    <i class="fi fi-rr-disk"></i> <span>Save Workflow</span>
                </button>
            </div>
        </div>

        <!-- Editor Workspace Area -->
        <div class="workflow-editor-workspace">
            
            <!-- Left Modules Palette (Collapsible Drag-and-Drop) -->
            <div class="workflow-editor-palette" id="workflowPalette">
                <div class="palette-header">
                    <div class="palette-title">
                        <i class="fi fi-rr-apps-add"></i>
                        <span>Flowchart Modules</span>
                    </div>
                    <button type="button" class="palette-collapse-btn" id="btnTogglePalette" title="Toggle Palette">
                        <i class="fi fi-rr-angle-left"></i>
                    </button>
                </div>
                
                <div class="palette-search-wrap">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" id="paletteSearchInput" class="palette-search-input" placeholder="Search modules..." autocomplete="off">
                </div>

                <div class="palette-scrollable-content">
                    
                    <!-- Group 1: Triggers -->
                    <div class="palette-group" data-group="triggers">
                        <div class="palette-group-title">
                            <i class="fi fi-rr-bolt"></i> <span>1. Triggers (When this happens)</span>
                        </div>
                        <div class="palette-items-list">
                            <div class="palette-item module-trigger" draggable="true" data-type="trigger" data-event="lead_created" data-title="New Lead Ingested" data-subtitle="Facebook Ads, Web Forms &amp; Website" data-icon="fi-rr-bullseye-arrow" data-color="#8b5cf6">
                                <span class="palette-item-icon violet"><i class="fi fi-rr-bullseye-arrow"></i></span>
                                <div class="palette-item-info">
                                    <strong>New Lead Ingested</strong>
                                    <span>From FB, Web Forms or Web</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>

                            <div class="palette-item module-trigger" draggable="true" data-type="trigger" data-event="whatsapp_incoming" data-title="WhatsApp Chat Received" data-subtitle="Keyword match or new message" data-icon="fi-rr-comment-dots" data-color="#8b5cf6">
                                <span class="palette-item-icon violet"><i class="fi fi-rr-comment-dots"></i></span>
                                <div class="palette-item-info">
                                    <strong>WhatsApp Chat Inbound</strong>
                                    <span>Keyword match or message</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>

                            <div class="palette-item module-trigger" draggable="true" data-type="trigger" data-event="payment_received" data-title="Payment Captured" data-subtitle="Razorpay, PhonePe, UPI" data-icon="fi-rr-badge-percent" data-color="#8b5cf6">
                                <span class="palette-item-icon violet"><i class="fi fi-rr-badge-percent"></i></span>
                                <div class="palette-item-info">
                                    <strong>Payment Captured</strong>
                                    <span>Advance deposit or full fee</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>

                            <div class="palette-item module-trigger" draggable="true" data-type="trigger" data-event="lead_stage_changed" data-title="Lead Stage Changed" data-subtitle="When pipeline milestone updates" data-icon="fi-rr-refresh" data-color="#8b5cf6">
                                <span class="palette-item-icon violet"><i class="fi fi-rr-refresh"></i></span>
                                <div class="palette-item-info">
                                    <strong>Pipeline Stage Update</strong>
                                    <span>Lead status changed</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Group 2: Actions -->
                    <div class="palette-group" data-group="actions">
                        <div class="palette-group-title">
                            <i class="fi fi-rr-play-alt"></i> <span>2. Actions (Do this)</span>
                        </div>
                        <div class="palette-items-list">
                            <div class="palette-item module-action" draggable="true" data-type="action" data-action="send_whatsapp" data-title="Send WhatsApp Message" data-subtitle="Personalized greeting with variables" data-icon="fi-rr-comment-alt" data-color="#10b981">
                                <span class="palette-item-icon emerald"><i class="fi fi-rr-comment-alt"></i></span>
                                <div class="palette-item-info">
                                    <strong>Send WhatsApp Message</strong>
                                    <span>Welcome, template, text</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>

                            <div class="palette-item module-action" draggable="true" data-type="action" data-action="send_itinerary" data-title="Dispatch Tour Itinerary" data-subtitle="Destination PDF brochure &amp; quotes" data-icon="fi-rr-document-signed" data-color="#10b981">
                                <span class="palette-item-icon emerald"><i class="fi fi-rr-document-signed"></i></span>
                                <div class="palette-item-info">
                                    <strong>Dispatch Tour Itinerary</strong>
                                    <span>Verified package PDF brochure</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>

                            <div class="palette-item module-action" draggable="true" data-type="action" data-action="assign_agent" data-title="Distribute Lead (Round-Robin)" data-subtitle="Auto-assign to active sales agents" data-icon="fi-rr-users-alt" data-color="#0ea5e9">
                                <span class="palette-item-icon blue"><i class="fi fi-rr-users-alt"></i></span>
                                <div class="palette-item-info">
                                    <strong>Assign Agent (Round-Robin)</strong>
                                    <span>Equal lead allocation</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>

                            <div class="palette-item module-action" draggable="true" data-type="action" data-action="add_tags" data-title="Add / Remove Tags" data-subtitle="Apply CRM tags to traveler" data-icon="fi-rr-tags" data-color="#f59e0b">
                                <span class="palette-item-icon amber"><i class="fi fi-rr-tags"></i></span>
                                <div class="palette-item-info">
                                    <strong>Tag Lead</strong>
                                    <span>Label high intent, source, VIP</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>

                            <div class="palette-item module-action" draggable="true" data-type="action" data-action="create_task" data-title="Create Follow-up Task" data-subtitle="Schedule call or meeting in CRM" data-icon="fi-rr-calendar-clock" data-color="#ec4899">
                                <span class="palette-item-icon pink"><i class="fi fi-rr-calendar-clock"></i></span>
                                <div class="palette-item-info">
                                    <strong>Create Follow-up Task</strong>
                                    <span>Call reminder in CRM</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>

                            <div class="palette-item module-action" draggable="true" data-type="action" data-action="update_lead_stage" data-title="Update CRM Stage" data-subtitle="Advance lead in sales pipeline" data-icon="fi-rr-refresh" data-color="#3b82f6">
                                <span class="palette-item-icon blue"><i class="fi fi-rr-refresh"></i></span>
                                <div class="palette-item-info">
                                    <strong>Update CRM Stage</strong>
                                    <span>Move lead in sales pipeline</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>

                            <div class="palette-item module-action" draggable="true" data-type="action" data-action="payment_link" data-title="Generate Payment Link" data-subtitle="Token booking deposit link" data-icon="fi-rr-credit-card" data-color="#0ea5e9">
                                <span class="palette-item-icon blue"><i class="fi fi-rr-credit-card"></i></span>
                                <div class="palette-item-info">
                                    <strong>Generate Payment Link</strong>
                                    <span>Token deposit or package fee</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>

                            <div class="palette-item module-action" draggable="true" data-type="action" data-action="webhook_call" data-title="Outbound Webhook / API" data-subtitle="Stream to Zapier, Make, n8n" data-icon="fi-rr-plug" data-color="#6366f1">
                                <span class="palette-item-icon indigo"><i class="fi fi-rr-plug"></i></span>
                                <div class="palette-item-info">
                                    <strong>Outbound Webhook</strong>
                                    <span>POST data to external API</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Group 3: Logic & Flow Control -->
                    <div class="palette-group" data-group="logic">
                        <div class="palette-group-title">
                            <i class="fi fi-rr-code-branch"></i> <span>3. Logic &amp; Flow Control</span>
                        </div>
                        <div class="palette-items-list">
                            <div class="palette-item module-logic" draggable="true" data-type="logic" data-action="condition_branch" data-title="If / Else Condition Branch" data-subtitle="Filter by destination, source, text" data-icon="fi-rr-code-fork" data-color="#f59e0b">
                                <span class="palette-item-icon amber"><i class="fi fi-rr-code-fork"></i></span>
                                <div class="palette-item-info">
                                    <strong>If / Else Condition</strong>
                                    <span>Route travelers by preference</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>

                            <div class="palette-item module-logic" draggable="true" data-type="action" data-action="delay" data-title="Delay / Wait Cadence" data-subtitle="Wait X minutes or seconds" data-icon="fi-rr-hourglass-end" data-color="#64748b">
                                <span class="palette-item-icon slate"><i class="fi fi-rr-hourglass-end"></i></span>
                                <div class="palette-item-info">
                                    <strong>Delay / Wait Cadence</strong>
                                    <span>Simulate typing or wait 24h</span>
                                </div>
                                <button type="button" class="btn-add-module" title="Add to Canvas"><i class="fi fi-rr-plus"></i></button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Center Infinite Dotted Canvas -->
            <div class="workflow-editor-canvas-container" id="canvasContainer">
                
                <!-- Dot Grid Background & Viewport -->
                <div class="workflow-canvas-viewport" id="canvasViewport">
                    
                    <!-- Dynamic SVG Connector Curves Layer -->
                    <svg class="workflow-canvas-svg" id="canvasSvgLayer" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <!-- Animated gradient for live connections -->
                            <linearGradient id="flowPulseGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#8b5cf6" />
                                <stop offset="50%" stop-color="#10b981" />
                                <stop offset="100%" stop-color="#0ea5e9" />
                            </linearGradient>
                            
                            <!-- Arrowhead Marker -->
                            <marker id="flowArrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                <path d="M 0 1.5 L 9 5 L 0 8.5 z" fill="#94a3b8" />
                            </marker>
                            <marker id="flowArrowActive" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                <path d="M 0 1.5 L 9 5 L 0 8.5 z" fill="#10b981" />
                            </marker>
                        </defs>
                        <g id="svgConnectionsGroup"></g>
                        <path id="svgDraftConnectionPath" class="draft-connection-path" d="" />
                    </svg>

                    <!-- Draggable Flowchart Nodes Layer -->
                    <div class="workflow-canvas-nodes" id="canvasNodesLayer">
                        <!-- Populated by JS -->
                    </div>

                    <!-- Empty Canvas Helper -->
                    <div class="canvas-empty-helper is-hidden" id="canvasEmptyHelper">
                        <div class="empty-helper-content">
                            <i class="fi fi-rr-sparkles"></i>
                            <h3>Your Flowchart Canvas is Ready</h3>
                            <p>Drag any trigger from the left palette onto this canvas, or click below to start with a sample trigger.</p>
                            <button type="button" class="btn-v3-primary" id="btnQuickAddTrigger">
                                <i class="fi fi-rr-plus"></i> <span>Add New Lead Trigger</span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Floating Canvas Minimap / Guide -->
                <div class="canvas-floating-tips">
                    <span class="tip-pill"><i class="fi fi-rr-mouse"></i> Drag to pan canvas</span>
                    <span class="tip-pill"><i class="fi fi-rr-arrows"></i> Drag node header to move</span>
                    <span class="tip-pill"><i class="fi fi-rr-add"></i> Click ports to connect</span>
                </div>
            </div>

            <!-- Right Node Configuration Inspector Drawer -->
            <div class="workflow-editor-drawer is-closed" id="nodeConfigDrawer">
                <div class="drawer-header">
                    <div class="drawer-header-icon" id="drawerNodeIcon">
                        <i class="fi fi-rr-settings"></i>
                    </div>
                    <div class="drawer-header-titles">
                        <h4 id="drawerNodeTitle">Configure Step</h4>
                        <span id="drawerNodeTypeBadge">Action Module</span>
                    </div>
                    <button type="button" class="drawer-close-btn" id="btnCloseNodeDrawer" title="Close Panel">
                        <i class="fi fi-rr-cross"></i>
                    </button>
                </div>

                <!-- Drawer Scrollable Form -->
                <div class="drawer-body">
                    
                    <!-- Common Fields: Step Name & Summary -->
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="drawerInputTitle">Step Label</label>
                        <input type="text" id="drawerInputTitle" class="drawer-input" placeholder="e.g. Send Welcome Greeting">
                    </div>

                    <div class="drawer-form-group">
                        <label class="drawer-label" for="drawerInputSubtitle">Description / Subtitle</label>
                        <input type="text" id="drawerInputSubtitle" class="drawer-input" placeholder="e.g. Ingest from Facebook Form">
                    </div>

                    <!-- Dynamic Fields Container (Populated based on node type) -->
                    <div class="drawer-dynamic-fields" id="drawerDynamicFields">
                        <!-- Custom controls generated dynamically by JS -->
                    </div>

                </div>

                <!-- Drawer Footer Actions -->
                <div class="drawer-footer">
                    <button type="button" class="btn-drawer-delete" id="btnDeleteCurrentNode">
                        <i class="fi fi-rr-trash"></i> <span>Delete Step</span>
                    </button>
                    <button type="button" class="btn-drawer-apply" id="btnApplyNodeChanges">
                        <i class="fi fi-rr-check"></i> <span>Apply Changes</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Simulation / Test Run Console (Slide-up footer drawer) -->
        <div class="workflow-test-console is-closed" id="testRunConsole">
            <div class="test-console-header">
                <div class="console-title">
                    <span class="console-status-dot is-running" id="consoleStatusDot"></span>
                    <strong id="consoleHeaderTitle">Flowchart Simulation Running...</strong>
                    <span class="console-timer-badge" id="consoleTimerBadge">0ms</span>
                </div>
                <div class="console-actions">
                    <button type="button" class="btn-console-preview" id="btnConsoleLivePreview">
                        <i class="fi fi-rr-eye"></i> <span>View Live Interaction</span>
                    </button>
                    <button type="button" class="btn-console-clear" id="btnConsoleReRun">
                        <i class="fi fi-rr-refresh"></i> Run Again
                    </button>
                    <button type="button" class="btn-console-close" id="btnCloseTestConsole">
                        <i class="fi fi-rr-cross"></i>
                    </button>
                </div>
            </div>
            <div class="test-console-body" id="testConsoleStepsList">
                <!-- Step results populated via JS -->
            </div>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- MODAL: CREATE WORKFLOW CHOOSER (SCRATCH VS TEMPLATES)          -->
    <!-- ============================================================== -->
    <div class="workflows-modal-overlay is-hidden" id="createChoiceModal">
        <div class="workflows-modal-card">
            <div class="modal-card-header">
                <div class="modal-card-titles">
                    <div class="modal-icon violet"><i class="fi fi-rr-apps-add"></i></div>
                    <div>
                        <h3>Create Automation Workflow</h3>
                        <p>Choose how you want to build your automation workflow today.</p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" id="btnCloseChoiceModal"><i class="fi fi-rr-cross"></i></button>
            </div>
            <div class="modal-card-body">
                <div class="creation-choice-grid">
                    <!-- Choice 1: Start from Scratch -->
                    <div class="creation-choice-card" id="choiceCardScratch">
                        <div class="choice-card-icon violet">
                            <i class="fi fi-rr-paint-brush"></i>
                        </div>
                        <div class="choice-card-body">
                            <h4>Start from Scratch (Blank Flowchart)</h4>
                            <p>Build your flow completely from an empty canvas. Add custom triggers, actions, and condition branches step-by-step.</p>
                            <span class="choice-action-badge"><i class="fi fi-rr-plus"></i> Open Blank Canvas</span>
                        </div>
                    </div>

                    <!-- Choice 2: Predefined Templates -->
                    <div class="creation-choice-card" id="choiceCardTemplates">
                        <div class="choice-card-icon emerald">
                            <i class="fi fi-rr-magic-wand"></i>
                        </div>
                        <div class="choice-card-body">
                            <h4>Predefined Starter Templates</h4>
                            <p>Choose from curated best-practice templates (Lead Round-Robin, WhatsApp Itinerary Concierge, Payment Vouchers).</p>
                            <span class="choice-action-badge secondary"><i class="fi fi-rr-arrow-small-right"></i> Browse Templates</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL: TEMPLATES LIBRARY MODAL                                 -->
    <!-- ============================================================== -->
    <div class="workflows-modal-overlay is-hidden" id="templatesModal">
        <div class="workflows-modal-card modal-lg">
            <div class="modal-card-header">
                <div class="modal-card-titles">
                    <div class="modal-icon violet"><i class="fi fi-rr-magic-wand"></i></div>
                    <div>
                        <h3>Pre-built Automation Templates</h3>
                        <p>Pick a ready-to-use template to install instantly into your workspace.</p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" id="btnCloseTemplatesModal"><i class="fi fi-rr-cross"></i></button>
            </div>
            <div class="modal-card-body">
                <div class="modal-templates-grid" id="modalTemplatesList">
                    <!-- Populated via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL: EXECUTION LOGS MODAL                                    -->
    <!-- ============================================================== -->
    <div class="workflows-modal-overlay is-hidden" id="executionLogsModal">
        <div class="workflows-modal-card modal-lg">
            <div class="modal-card-header">
                <div class="modal-card-titles">
                    <div class="modal-icon emerald"><i class="fi fi-rr-time-past"></i></div>
                    <div>
                        <h3>Execution Audit Logs</h3>
                        <p id="logsModalSubtitle">Historical simulation and trigger executions for this workflow.</p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" id="btnCloseLogsModal"><i class="fi fi-rr-cross"></i></button>
            </div>
            <div class="modal-card-body">
                <div class="logs-table-container">
                    <table class="workflows-logs-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Trigger Event</th>
                                <th>Status</th>
                                <th>Steps Executed</th>
                                <th>Duration</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="logsTableBody">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
    <!-- ============================================================== -->
    <!-- MODAL: LIVE SIMULATION INTERACTION & DUMMY DATA INSPECTOR      -->
    <!-- ============================================================== -->
    <div class="workflows-modal-overlay is-hidden" id="simulationInteractionModal">
        <div class="workflows-modal-card modal-xl simulation-modal-card">
            <div class="modal-card-header">
                <div class="modal-card-titles">
                    <div class="modal-icon emerald"><i class="fi fi-rr-play-alt"></i></div>
                    <div>
                        <h3>Workflow Live Simulation & Customer Interaction Preview</h3>
                        <p>Real-time visual experience showing how the customer and your CRM team encounter this automated flow.</p>
                    </div>
                </div>
                <div class="modal-header-actions">
                    <div class="simulation-preset-selector">
                        <label for="simulationPresetSelect"><i class="fi fi-rr-user"></i> Simulated Traveler:</label>
                        <select id="simulationPresetSelect" class="v3-select-compact">
                            <option value="couple">Aarav Sharma (Honeymoon Couple • Facebook Ads)</option>
                            <option value="family">Priya & Rajesh (Family Kashmir • Website)</option>
                            <option value="luxury">Vikram Singhania (Luxury Goa Villa • Google Ads)</option>
                        </select>
                    </div>
                    <button type="button" class="btn-v3-action" id="btnReRunFromModal" title="Re-run Simulation with this profile">
                        <i class="fi fi-rr-refresh"></i> <span>Re-run</span>
                    </button>
                    <button type="button" class="modal-close-btn" id="btnCloseSimulationModal"><i class="fi fi-rr-cross"></i></button>
                </div>
            </div>
            <div class="modal-card-body simulation-modal-body">
                <div class="simulation-tabs-bar">
                    <button type="button" class="simulation-tab is-active" data-tab="visual">
                        <i class="fi fi-rr-eye"></i> <span>Live Interactive Experience</span>
                    </button>
                    <button type="button" class="simulation-tab" data-tab="trace">
                        <i class="fi fi-rr-list-check"></i> <span>Step Trace & Payloads</span>
                    </button>
                    <button type="button" class="simulation-tab" data-tab="json">
                        <i class="fi fi-rr-brackets-curly"></i> <span>Raw Context JSON</span>
                    </button>
                </div>

                <!-- Tab 1: Visual Experience -->
                <div class="simulation-tab-content is-active" id="simTabVisual">
                    <div class="simulation-visual-grid" id="simVisualGrid">
                        <!-- Populated dynamically via JS: WhatsApp Phone Mockup, Inbound Lead Card, Assigned Agent Card, Tour Itinerary PDF Card -->
                    </div>
                </div>

                <!-- Tab 2: Step Trace & Payloads -->
                <div class="simulation-tab-content" id="simTabTrace">
                    <div class="simulation-trace-list" id="simTraceList">
                        <!-- Populated dynamically with step trace cards -->
                    </div>
                </div>

                <!-- Tab 3: Raw JSON -->
                <div class="simulation-tab-content" id="simTabJson">
                    <pre class="simulation-json-viewer" id="simJsonViewer"></pre>
                </div>
            </div>
            <div class="modal-card-footer">
                <div class="sim-footer-meta">
                    <span class="sim-badge success"><i class="fi fi-rr-check"></i> Flow Execution Validated</span>
                    <span class="sim-badge time" id="simExecutionTimeBadge"><i class="fi fi-rr-time-past"></i> 180ms</span>
                </div>
                <div class="sim-footer-actions">
                    <button type="button" class="btn-v3-action" id="btnCloseSimulationModalBtn">Close Preview</button>
                    <button type="button" class="btn-v3-primary" id="btnSaveWorkflowFromSim">
                        <i class="fi fi-rr-disk"></i> <span>Save & Activate Flow</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
