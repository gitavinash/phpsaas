<?php
/**
 * V3 Lead Flow & Distribution Automation Plugin View
 * Path: /app/dashboard/v3/default/desktop/integration/lead_automation/index.php
 */
?>
<div class="package-page-wrapper integration-plugin-wrapper" id="leadAutomationApp">

    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-lead-auto wf-kpi-icon indigo">
                <img src="/app/dashboard/v3/default/desktop/assets/images/integration/lead_automation.svg" alt="Lead Flow Automation">
            </div>
            <div class="package-header-titles">
                <h2>Lead Flow Automation</h2>
                <p>Automate how incoming leads are distributed among sales agents with visual Round-Robin rotation, source routing filters, and instant follow-ups.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/integration/" class="btn-v3-action" data-tooltip="Back to All Integrations">
                <i class="fi fi-rr-arrow-left"></i> <span>All Integrations</span>
            </a>
            <button type="button" class="btn-v3-action btn-v3-primary btn-create-lead-rule" id="btnCreateLeadRule">
                <i class="fi fi-rr-plus"></i> <span>Create Distribution Rule</span>
            </button>
        </div>
    </div>

    <!-- KPI Statistics Grid -->
    <div class="wf-kpi-grid">
        <div class="wf-kpi-card">
            <div class="wf-kpi-icon indigo">
                <i class="fi fi-rr-filter"></i>
            </div>
            <div class="wf-kpi-info">
                <span class="wf-kpi-value" id="statLeadTotalRules">0</span>
                <span class="wf-kpi-label">Total Rules</span>
            </div>
        </div>
        <div class="wf-kpi-card">
            <div class="wf-kpi-icon emerald">
                <i class="fi fi-rr-refresh"></i>
            </div>
            <div class="wf-kpi-info">
                <span class="wf-kpi-value" id="statLeadActiveRules">0</span>
                <span class="wf-kpi-label">Active Pipelines</span>
            </div>
        </div>
        <div class="wf-kpi-card">
            <div class="wf-kpi-icon amber">
                <i class="fi fi-rr-paper-plane"></i>
            </div>
            <div class="wf-kpi-info">
                <span class="wf-kpi-value" id="statLeadDistributed">0</span>
                <span class="wf-kpi-label">Leads Distributed</span>
            </div>
        </div>
        <div class="wf-kpi-card">
            <div class="wf-kpi-icon purple">
                <i class="fi fi-rr-users"></i>
            </div>
            <div class="wf-kpi-info">
                <span class="wf-kpi-value" id="statLeadAgentsCount">0</span>
                <span class="wf-kpi-label">Active Agents in Pool</span>
            </div>
        </div>
    </div>

    <!-- Navigation Category Tabs & Actions -->
    <div class="integration-toolbar-row">
        <div class="integration-category-tabs">
            <button type="button" class="integration-cat-tab lead-tab-btn active" data-tab="rules">
                <i class="fi fi-rr-list-check"></i> Distribution Rules
            </button>
            <button type="button" class="integration-cat-tab lead-tab-btn" data-tab="visual">
                <i class="fi fi-rr-workflow"></i> Visual Pipeline Flow
            </button>
            <button type="button" class="integration-cat-tab lead-tab-btn" data-tab="simulator">
                <i class="fi fi-rr-play-alt"></i> Distribution Simulator
            </button>
            <button type="button" class="integration-cat-tab lead-tab-btn" data-tab="logs">
                <i class="fi fi-rr-time-past"></i> Distribution Logs
            </button>
        </div>
        <div class="integration-toolbar-right">
            <button type="button" class="btn-v3-action" id="btnRefreshLeadWorkflows" title="Refresh Rules">
                <i class="fi fi-rr-refresh"></i> <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- Tab 1: Rules List -->
    <div class="lead-tab-pane" id="leadTab_rules">
        <div class="lead-tab-content-card">
            <div id="leadWorkflowsLoading" class="text-center py-4 text-muted">
                <i class="fi fi-rr-spinner animate-spin"></i> Loading distribution rules...
            </div>
            <div id="leadWorkflowsEmpty" class="text-center py-5">
                <div class="mb-3 text-muted lead-empty-icon"><i class="fi fi-rr-user-add"></i></div>
                <h4 class="lead-empty-title">No Distribution Rules Found</h4>
                <p class="lead-empty-desc">
                    Set up your first rule to automatically distribute incoming leads to your sales agents via Round-Robin rotation.
                </p>
                <button type="button" class="btn-v3-action btn-v3-primary btn-create-lead-rule" id="btnEmptyCreateRule">
                    <i class="fi fi-rr-plus"></i> Create Rule
                </button>
            </div>
            <div id="leadRulesTableWrapper" class="lead-rules-table-container">
                <table class="lead-rules-table">
                    <thead>
                        <tr>
                            <th>Priority</th>
                            <th>Rule Name</th>
                            <th>Distribution Mode</th>
                            <th>Assigned Agent Pool</th>
                            <th>Distributed</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="leadRulesTbody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 2: Visual Pipeline Flow -->
    <div class="lead-tab-pane" id="leadTab_visual">
        <div class="lead-tab-content-card">
            <div class="mb-4">
                <h3 class="lead-section-title">Visual Lead Distribution Architecture</h3>
                <p class="lead-section-subtitle">Visual representation of the lifecycle and decision path of incoming leads.</p>
            </div>

            <div class="lead-pipeline-canvas">
                <!-- Stage 1: Ingestion -->
                <div class="lead-pipeline-stage-card trigger">
                    <span class="lead-stage-badge trigger"><i class="fi fi-rr-inbox-in"></i> Stage 1: Lead Ingestion</span>
                    <h4 class="lead-arch-step-title">Incoming Lead Ingested</h4>
                    <p class="lead-arch-step-desc">Lead arrives via Facebook Lead Ads, Landing Page Web Forms, WhatsApp Webhooks, Google Ads, or Direct Entry.</p>
                </div>

                <div class="lead-connector"></div>

                <!-- Stage 2: Evaluation -->
                <div class="lead-pipeline-stage-card filter">
                    <span class="lead-stage-badge filter"><i class="fi fi-rr-filter"></i> Stage 2: Filter Evaluation</span>
                    <h4 class="lead-arch-step-title">Priority Rules Matching</h4>
                    <p class="lead-arch-step-desc">The engine inspects lead attributes (acquisition source, destination interest, travel dates) against your configured distribution rules in priority order (#1, #2, #3...).</p>
                </div>

                <div class="lead-connector"></div>

                <!-- Stage 3: Routing -->
                <div class="lead-pipeline-stage-card routing">
                    <span class="lead-stage-badge routing"><i class="fi fi-rr-refresh"></i> Stage 3: Agent Allocation</span>
                    <h4 class="lead-arch-step-title">Round-Robin Rotation or Specific Agent</h4>
                    <p class="lead-arch-step-desc">The lead is instantly assigned to the next eligible sales agent in the rotation pool, ensuring perfectly balanced workload distribution.</p>
                </div>

                <div class="lead-connector"></div>

                <!-- Stage 4: Automation -->
                <div class="lead-pipeline-stage-card actions">
                    <span class="lead-stage-badge actions"><i class="fi fi-rr-bolt"></i> Stage 4: Instant Follow-up</span>
                    <h4 class="lead-arch-step-title">Auto-Task &amp; WhatsApp Greeting</h4>
                    <p class="lead-arch-step-desc">Automatically creates an urgent Call Task for the assigned agent and sends an immediate personalized WhatsApp greeting to the traveler.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 3: Distribution Simulator -->
    <div class="lead-tab-pane" id="leadTab_simulator">
        <div class="lead-tab-content-card">
            <div class="mb-4">
                <h3 class="lead-section-title">Live Lead Routing Simulator</h3>
                <p class="lead-section-subtitle">Enter sample lead details to preview which distribution rule triggers and which agent is next in line to receive the lead.</p>
            </div>

            <div class="lead-sim-grid">
                <!-- Input Form -->
                <div class="lead-sim-form-card">
                    <h4 class="lead-sim-card-title">Test Lead Attributes</h4>
                    <div class="lead-sim-form-fields">
                        <div class="v3-form-group">
                            <label for="simLeadName" class="v3-form-label">Customer Name</label>
                            <input type="text" id="simLeadName" class="form-control" value="Aarav Patel">
                        </div>
                        <div class="v3-form-group">
                            <label for="simLeadSource" class="v3-form-label">Ingestion Source</label>
                            <select id="simLeadSource" class="form-select selectjs2 selectJS">
                                <option value="facebook">Facebook Lead Ads</option>
                                <option value="web_form">Landing Page Web Form</option>
                                <option value="whatsapp">WhatsApp Direct Ingestion</option>
                                <option value="google_ads">Google Ads</option>
                                <option value="direct">Direct / Website</option>
                            </select>
                        </div>
                        <div class="v3-form-group">
                            <label for="simLeadSubject" class="v3-form-label">Destination / Package Interest</label>
                            <input type="text" id="simLeadSubject" class="form-control" value="Kashmir 5D4N Tour">
                        </div>
                        <div class="lead-sim-btn-wrap">
                            <button type="button" class="btn-v3-action btn-v3-primary w-100 justify-content-center" id="btnRunLeadSim">
                                <i class="fi fi-rr-play"></i> Run Distribution Test
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Simulation Result Card -->
                <div class="lead-sim-result-card">
                    <h4 class="lead-sim-output-title">Simulation Output</h4>
                    <div id="simEmptyResult" class="lead-sim-empty-state">
                        <div class="lead-sim-empty-icon"><i class="fi fi-rr-route"></i></div>
                        <div class="lead-sim-empty-title">Ready for Simulation</div>
                        <p class="lead-sim-empty-desc">Click "Run Distribution Test" to evaluate against active routing rules and preview real-time assignment.</p>
                    </div>
                    <div id="simResultBox">
                        <div class="lead-sim-res-box">
                            <div class="lead-sim-res-sub">Assigned Agent:</div>
                            <div class="lead-sim-res-agent" id="simAssignedAgent">-</div>
                            <div class="lead-sim-res-info">
                                Matched Rule: <strong id="simMatchedRule">-</strong> (<span id="simDistributionMode">-</span>)
                            </div>
                        </div>
                        <div class="lead-sim-stepper" id="simStepsContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 4: Audit Logs -->
    <div class="lead-tab-pane" id="leadTab_logs">
        <div class="lead-tab-content-card">
            <div class="lead-section-header-row">
                <div>
                    <h3 class="lead-section-title">Lead Distribution Audit Trail</h3>
                    <p class="lead-section-subtitle">Complete history of automated lead assignments, applied rules, and assigned sales representatives.</p>
                </div>
                <button type="button" class="btn-v3-action" id="btnRefreshLeadLogs">
                    <i class="fi fi-rr-refresh"></i> Refresh Logs
                </button>
            </div>
            <div class="lead-rules-table-container">
                <table class="lead-rules-table">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Lead Name</th>
                            <th>Source</th>
                            <th>Distribution Rule</th>
                            <th>Assigned Agent</th>
                            <th>Mode</th>
                        </tr>
                    </thead>
                    <tbody id="leadLogsTbody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Distribution Rule Builder / Editor -->
    <div class="lead-modal-backdrop" id="leadWorkflowModal">
        <div class="lead-modal-card">
            <div class="v3-modal-header">
                <div class="v3-modal-header-left">
                    <div class="v3-modal-header-icon-box icon-edit">
                        <i class="fi fi-rr-settings-sliders"></i>
                    </div>
                    <div class="v3-modal-title-wrap">
                        <h3 id="leadModalTitle" class="v3-modal-title">Create Lead Distribution Rule</h3>
                        <p class="v3-modal-subtitle">Define routing conditions, agent pool allocation, and automated follow-ups.</p>
                    </div>
                </div>
                <button type="button" class="v3-modal-close-btn" id="btnCloseLeadModal" aria-label="Close">
                    <i class="fi fi-rr-cross"></i>
                </button>
            </div>
            <form id="leadRuleForm">
                <input type="hidden" id="leadModalId" value="0">
                <div class="lead-modal-body">
                    <div class="v3-form-grid">
                        <!-- Rule Identification (Rule Name & Priority) -->
                        <div class="lead-row-name-priority">
                            <div class="v3-form-group">
                                <label for="leadModalName" class="v3-form-label">
                                    Rule Name <span class="required">*</span>
                                </label>
                                <input type="text" id="leadModalName" class="form-control form-control-sm" placeholder="e.g. VIP Facebook Leads Rotation" required>
                            </div>
                            <div class="v3-form-group">
                                <label for="leadModalPriority" class="v3-form-label">
                                    Priority (1-100) <span class="lead-label-hint">Lower = First</span>
                                </label>
                                <input type="number" id="leadModalPriority" class="form-control form-control-sm" value="10" min="1" max="100" required>
                            </div>
                        </div>

                        <!-- Rule Description -->
                        <div class="v3-form-group span-2">
                            <label for="leadModalDesc" class="v3-form-label">
                                Rule Description <span class="lead-label-hint">Optional</span>
                            </label>
                            <input type="text" id="leadModalDesc" class="form-control form-control-sm" placeholder="e.g. Evenly distributes inquiries among premium destination specialists">
                        </div>

                        <!-- Filters & Routing -->
                        <div class="v3-form-group">
                            <label for="leadModalSources" class="v3-form-label">
                                Filter Ingestion Sources
                            </label>
                            <select id="leadModalSources" class="form-select form-select-sm selectjs2 selectJS">
                                <option value="all">All Sources (Any Ingestion)</option>
                                <option value="facebook">Facebook &amp; Instagram Ads Only</option>
                                <option value="web_form">Landing Page Web Forms Only</option>
                                <option value="whatsapp">WhatsApp Direct Inquiries Only</option>
                                <option value="google_ads">Google Ads Only</option>
                                <option value="direct">Direct / Website Only</option>
                            </select>
                        </div>
                        <div class="v3-form-group">
                            <label for="leadModalDestination" class="v3-form-label">
                                Destination Keyword Filter <span class="lead-label-hint">Optional</span>
                            </label>
                            <input type="text" id="leadModalDestination" class="form-control form-control-sm" placeholder="e.g. Kashmir, Maldives, Dubai">
                        </div>

                        <!-- Distribution Strategy -->
                        <div class="v3-form-group span-2">
                            <label for="leadModalMode" class="v3-form-label">
                                Distribution Strategy
                            </label>
                            <select id="leadModalMode" class="form-select form-select-sm selectjs2 selectJS">
                                <option value="round_robin">Round-Robin Rotation (Evenly distributes across pool)</option>
                                <option value="specific_agent">Specific Agent (Assigns exclusively to selected agent)</option>
                            </select>
                        </div>

                        <!-- Agent Allocation Pool -->
                        <div class="v3-form-group span-2">
                            <div class="lead-agent-pool-header">
                                <label class="v3-form-label mb-0">
                                    Select Agents for this Distribution Pool <span class="required">*</span>
                                    <span class="lead-agent-count-badge" id="leadSelectedAgentCount">0 selected</span>
                                </label>
                                <div class="lead-agent-header-actions">
                                    <div class="lead-agent-search-box">
                                        <i class="fi fi-rr-search"></i>
                                        <input type="text" id="leadAgentSearchInput" placeholder="Filter agents...">
                                    </div>
                                    <button type="button" class="lead-btn-link" id="btnToggleAllLeadAgents">Select All</button>
                                </div>
                            </div>
                            <div class="lead-agent-pool-box" id="leadModalAgentPool"></div>
                        </div>

                        <!-- Automation Feature 1: Follow-up Task Configuration -->
                        <div class="v3-form-group span-2">
                            <div class="lead-feature-card">
                                <div class="lead-feature-card-header">
                                    <div class="lead-feature-icon-badge"><i class="fi fi-rr-calendar-check"></i></div>
                                    <div class="lead-feature-info">
                                        <div class="lead-feature-title">Automated Follow-up Task</div>
                                        <div class="lead-feature-desc">Instantly schedules an action item for the assigned representative.</div>
                                    </div>
                                    <label class="v3-toggle-switch" title="Toggle Automated Follow-up Task">
                                        <input type="checkbox" id="leadModalCreateTask" checked>
                                        <span class="v3-toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="lead-feature-card-body" id="leadTaskSettingsCollapse">
                                    <div class="lead-task-grid">
                                        <div class="v3-form-group">
                                            <label for="leadModalTaskType" class="v3-form-label lead-sub-label">Task Type</label>
                                            <select id="leadModalTaskType" class="form-select form-select-sm selectjs2 selectJS">
                                                <option value="Call">Call</option>
                                                <option value="WhatsApp">WhatsApp</option>
                                                <option value="Meeting">Meeting</option>
                                                <option value="Email">Email</option>
                                            </select>
                                        </div>
                                        <div class="v3-form-group">
                                            <label for="leadModalTaskHours" class="v3-form-label lead-sub-label">Due In (Hours)</label>
                                            <input type="number" id="leadModalTaskHours" class="form-control form-control-sm" value="2" min="1">
                                        </div>
                                        <div class="v3-form-group">
                                            <label for="leadModalTaskNote" class="v3-form-label lead-sub-label">Task Note</label>
                                            <input type="text" id="leadModalTaskNote" class="form-control form-control-sm" value="Follow up with newly distributed lead">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Automation Feature 2: Instant WhatsApp Greeting -->
                        <div class="v3-form-group span-2">
                            <div class="lead-feature-card">
                                <div class="lead-feature-card-header">
                                    <div class="lead-feature-icon-badge wa"><i class="fi fi-rr-paper-plane"></i></div>
                                    <div class="lead-feature-info">
                                        <div class="lead-feature-title">Instant WhatsApp Greeting</div>
                                        <div class="lead-feature-desc">Dispatches an instant welcome message with the assigned agent's name.</div>
                                    </div>
                                    <label class="v3-toggle-switch" title="Toggle WhatsApp Greeting">
                                        <input type="checkbox" id="leadModalSendWa">
                                        <span class="v3-toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="lead-feature-card-body" id="leadWaSettingsCollapse">
                                    <div class="lead-wa-msg-header">
                                        <span class="lead-wa-msg-title">Message Template</span>
                                        <div class="lead-wa-tokens">
                                            <button type="button" class="btn-v3-action lead-token-btn lead-token-btn-sm" data-token="{name}">+ {name}</button>
                                            <button type="button" class="btn-v3-action lead-token-btn lead-token-btn-sm" data-token="{agent_name}">+ {agent_name}</button>
                                            <button type="button" class="btn-v3-action lead-token-btn lead-token-btn-sm" data-token="{destination}">+ {destination}</button>
                                        </div>
                                    </div>
                                    <textarea id="leadModalWaMsg" class="form-control form-control-sm lead-modal-wa-textarea" rows="2" placeholder="Hello {name}! Thanks for contacting us. Your dedicated trip specialist {agent_name} will connect with you shortly."></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="lead-modal-footer">
                    <div class="lead-modal-footer-hint">
                        <i class="fi fi-rr-info"></i>
                        <span>Active rules apply to all subsequent incoming leads.</span>
                    </div>
                    <div class="lead-modal-footer-actions">
                        <button type="button" class="btn-v3-action" id="btnCancelLeadModal">Cancel</button>
                        <button type="submit" class="btn-v3-action btn-v3-primary" id="btnSaveLeadRule">
                            <i class="fi fi-rr-check"></i> Save Rule
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
