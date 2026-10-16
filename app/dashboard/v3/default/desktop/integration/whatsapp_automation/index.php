<?php
/**
 * V3 WhatsApp Chat Automation Plugin View
 * Path: /app/dashboard/v3/default/desktop/integration/whatsapp_automation/index.php
 */
?>
<div class="package-page-wrapper integration-plugin-wrapper" id="whatsappAutomationApp">

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
            <div id="waWorkflowsTableWrapper" class="wa-workflows-table-container">
                <table class="wa-workflows-table">
                    <thead>
                        <tr>
                            <th>Workflow Name</th>
                            <th>Trigger / Keywords</th>
                            <th>Auto-Response Message</th>
                            <th>Executions</th>
                            <th>Status</th>
                            <th>Actions</th>
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

    <!-- Modal: Workflow Builder / Editor -->
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
                            <div class="form-check wa-fallback-check">
                                <input class="form-check-input" type="checkbox" id="waModalIsDefault">
                                <label class="form-check-label wa-checkbox-label" for="waModalIsDefault">
                                    Set as Default Fallback Bot (triggers when no specific keywords match)
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

</div>
