/**
 * V3 Automation Workflows Plugin Application
 * Make.com-style Visual Flowchart Canvas, Node Drag-and-Drop, SVG Connections & Simulation Engine
 * Path: /app/dashboard/v3/default/desktop/assets/js/workflows.js
 */

(function (window, document) {
    'use strict';

    class WorkflowsPluginApp {
        constructor() {
            this.appEl = document.getElementById('workflowsPluginApp');
            if (!this.appEl) return;

            // API Endpoint
            this.apiUrl = '/api/v3/workflows/';

            // State
            this.currentView = 'list'; // 'list' or 'editor'
            this.currentWorkflowId = null;
            this.currentWorkflowData = null;
            this.workflowsList = [];
            this.leadSourcesList = [];
            this.webFormsList = [];
            this.fbFormsList = [];
            this.whatsappTemplatesList = [];
            this.agentsList = [];
            this.packagesList = [];
            this.pipelineStagesList = [];
            this.moduleCatalog = null;

            // Canvas & Editor State
            this.nodes = [];
            this.connections = [];
            this.selectedNodeId = null;
            this.selectedConnectionId = null;
            this.activePortDrag = null; // { sourceNodeId, portType, portEl, startX, startY }
            this.zoomLevel = 1.0;
            this.panX = 40;
            this.panY = 40;
            this.isPanning = false;
            this.isSpacePressed = false;
            this.isDirty = false;
            this.startPanMouseX = 0;
            this.startPanMouseY = 0;
            this.startPanX = 0;
            this.startPanY = 0;

            // History (Undo / Redo)
            this.history = [];
            this.historyIndex = -1;
            this.maxHistory = 35;

            // Unload / Refresh state flag
            this.allowUnload = false;

            // Bound handlers for clean removal on destroy
            this.boundWindowListeners = [];

            // Grab DOM references fresh
            this.refreshDomReferences();

            // Bind Event Listeners
            this.bindEvents();

            // Load Data & Initial URL Route
            this.initDataAndRoute();
        }

        refreshDomReferences() {
            this.listViewEl = document.getElementById('workflowsListView');
            this.editorViewEl = document.getElementById('workflowsEditorView');
            this.cardsContainerEl = document.getElementById('workflowsCardsContainer');
            this.searchInputEl = document.getElementById('workflowSearchInput');
            this.categoryTabsEl = document.getElementById('workflowCategoryTabs');
            this.starterTemplatesRowEl = document.getElementById('starterTemplatesRow');

            // Editor
            this.canvasContainerEl = document.getElementById('canvasContainer');
            this.canvasViewportEl = document.getElementById('canvasViewport');
            this.canvasSvgLayerEl = document.getElementById('canvasSvgLayer');
            this.svgConnectionsGroupEl = document.getElementById('svgConnectionsGroup');
            this.svgDraftConnectionPathEl = document.getElementById('svgDraftConnectionPath');
            this.canvasNodesLayerEl = document.getElementById('canvasNodesLayer');
            this.canvasEmptyHelperEl = document.getElementById('canvasEmptyHelper');
            this.canvasZoomLevelEl = document.getElementById('canvasZoomLevel');
            this.editorWorkflowNameEl = document.getElementById('editorWorkflowName');
            this.editorStatusToggleEl = document.getElementById('editorStatusToggle');
            this.editorStatusTextEl = document.getElementById('editorStatusText');
            this.editorSaveStateEl = document.getElementById('editorSaveState');

            // Topbar tools
            this.btnTopbarAddStepEl = document.getElementById('btnTopbarAddStep');
            this.btnCanvasUndoEl = document.getElementById('btnCanvasUndo');
            this.btnCanvasRedoEl = document.getElementById('btnCanvasRedo');

            // Drawer
            this.nodeConfigDrawerEl = document.getElementById('nodeConfigDrawer');
            this.drawerModuleTypeSelectEl = document.getElementById('drawerModuleTypeSelect');
            this.drawerInputTitleEl = document.getElementById('drawerInputTitle');
            this.drawerInputSubtitleEl = document.getElementById('drawerInputSubtitle');
            this.drawerDynamicFieldsEl = document.getElementById('drawerDynamicFields');
            this.drawerNodeTypeBadgeEl = document.getElementById('drawerNodeTypeBadge');
            this.btnDuplicateCurrentNodeEl = document.getElementById('btnDuplicateCurrentNode');

            // Quick Add Popover
            this.quickAddPopoverEl = document.getElementById('quickAddPopover');
            this.quickAddSearchInputEl = document.getElementById('quickAddSearchInput');
            this.quickAddCategoryTabsEl = document.getElementById('quickAddCategoryTabs');
            this.quickAddItemsListEl = document.getElementById('quickAddItemsList');
            this.btnCloseQuickAddPopoverEl = document.getElementById('btnCloseQuickAddPopover');
            this.activeQuickAddSourceNodeId = null;
            this.quickAddFilterCategory = 'all';

            // Simulation Console
            this.testRunConsoleEl = document.getElementById('testRunConsole');
            this.testConsoleStepsListEl = document.getElementById('testConsoleStepsList');

            // Modals
            this.createChoiceModalEl = document.getElementById('createChoiceModal');
            this.templatesModalEl = document.getElementById('templatesModal');
            this.executionLogsModalEl = document.getElementById('executionLogsModal');
            this.simulationModalEl = document.getElementById('simulationInteractionModal');
            this.simulationTimeouts = [];
            this.latestSimulationResult = null;
            this.isSimulating = false;
        }

        /* ==========================================================================
           Event Binding
           ========================================================================== */
        bindEvents() {
            // Choice Modal (Create from Scratch vs Predefined Templates)
            const btnOpenChoice = document.getElementById('btnOpenCreateChoiceModal');
            const btnCloseChoice = document.getElementById('btnCloseChoiceModal');
            const choiceScratch = document.getElementById('choiceCardScratch');
            const choiceTemplates = document.getElementById('choiceCardTemplates');

            if (btnOpenChoice) {
                btnOpenChoice.addEventListener('click', () => this.openChoiceModal());
            }
            if (btnCloseChoice) {
                btnCloseChoice.addEventListener('click', () => this.closeChoiceModal());
            }
            if (choiceScratch) {
                choiceScratch.addEventListener('click', () => {
                    this.closeChoiceModal();
                    this.openEditorForNew();
                });
            }
            if (choiceTemplates) {
                choiceTemplates.addEventListener('click', () => {
                    this.closeChoiceModal();
                    this.openTemplatesModal();
                });
            }

            // Direct Start from Scratch Button
            const btnCreateBlank = document.getElementById('btnCreateBlankWorkflow');
            if (btnCreateBlank) {
                btnCreateBlank.addEventListener('click', () => this.openEditorForNew());
            }

            // Templates Modal Buttons
            const btnOpenTemplates = document.getElementById('btnOpenTemplatesModal');
            const btnViewAllTpl = document.getElementById('btnViewAllTemplates');
            const btnCloseTemplates = document.getElementById('btnCloseTemplatesModal');

            if (btnOpenTemplates) btnOpenTemplates.addEventListener('click', () => this.openTemplatesModal());
            if (btnViewAllTpl) btnViewAllTpl.addEventListener('click', () => this.openTemplatesModal());
            if (btnCloseTemplates) btnCloseTemplates.addEventListener('click', () => this.closeTemplatesModal());

            const btnModalFooterScratch = document.getElementById('btnModalFooterScratch');
            if (btnModalFooterScratch) {
                btnModalFooterScratch.addEventListener('click', () => {
                    this.closeTemplatesModal();
                    this.openEditorForNew();
                });
            }

            // Search
            if (this.searchInputEl) {
                this.searchInputEl.addEventListener('input', (e) => this.filterWorkflows(e.target.value));
            }

            // Categories Filter
            if (this.categoryTabsEl) {
                this.categoryTabsEl.addEventListener('click', (e) => {
                    const btn = e.target.closest('.workflow-cat-tab');
                    if (!btn) return;
                    this.categoryTabsEl.querySelectorAll('.workflow-cat-tab').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    this.filterWorkflowsByCategory(btn.dataset.category);
                });
            }

            // Editor Topbar Navigation
            const btnBack = document.getElementById('btnBackToWorkflowList');
            if (btnBack) {
                btnBack.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.confirmExitWorkflow(() => this.returnToListView());
                });
            }

            const editorHeaderLogo = document.querySelector('.editor-header-logo');
            if (editorHeaderLogo) {
                editorHeaderLogo.addEventListener('click', (e) => {
                    if (this.currentView === 'editor') {
                        e.preventDefault();
                        this.confirmExitWorkflow(() => {
                            window.location.href = '/dashboard/';
                        });
                    }
                });
            }

            const btnFocusName = document.getElementById('btnFocusWorkflowName');
            if (btnFocusName && this.editorWorkflowNameEl) {
                btnFocusName.addEventListener('click', () => {
                    this.editorWorkflowNameEl.focus();
                    this.editorWorkflowNameEl.select();
                });
            }

            const btnSave = document.getElementById('btnSaveWorkflowFlowchart');
            if (btnSave) {
                btnSave.addEventListener('click', () => this.saveCurrentWorkflow());
            }

            const btnTestRun = document.getElementById('btnRunTestSimulation');
            if (btnTestRun) {
                btnTestRun.addEventListener('click', () => this.triggerSimulationRun());
            }

            const btnAutoAlign = document.getElementById('btnAutoAlignFlowchart');
            if (btnAutoAlign) {
                btnAutoAlign.addEventListener('click', () => this.autoAlignFlowchart());
            }

            // Undo & Redo Canvas Buttons
            if (this.btnCanvasUndoEl) {
                this.btnCanvasUndoEl.addEventListener('click', () => this.undo());
            }
            if (this.btnCanvasRedoEl) {
                this.btnCanvasRedoEl.addEventListener('click', () => this.redo());
            }

            // Zoom & Canvas View Controls
            const btnZoomIn = document.getElementById('btnZoomIn');
            const btnZoomOut = document.getElementById('btnZoomOut');
            const btnZoomReset = document.getElementById('btnZoomReset');
            if (btnZoomIn) btnZoomIn.addEventListener('click', () => this.setZoom(this.zoomLevel + 0.15));
            if (btnZoomOut) btnZoomOut.addEventListener('click', () => this.setZoom(this.zoomLevel - 0.15));
            if (btnZoomReset) btnZoomReset.addEventListener('click', () => { this.panX = 40; this.panY = 40; this.setZoom(1.0); });

            // Status Toggle in Editor
            if (this.editorStatusToggleEl) {
                this.editorStatusToggleEl.addEventListener('change', (e) => {
                    if (this.editorStatusTextEl) {
                        this.editorStatusTextEl.textContent = e.target.checked ? 'Active' : 'Paused';
                    }
                });
            }

            // Palette Toggle Button
            const btnTogglePalette = document.getElementById('btnTogglePalette');
            const paletteEl = document.getElementById('workflowPalette');
            if (btnTogglePalette && paletteEl) {
                btnTogglePalette.addEventListener('click', () => {
                    paletteEl.classList.toggle('is-collapsed');
                });
            }

            // Palette Search
            const paletteSearch = document.getElementById('paletteSearchInput');
            if (paletteSearch && paletteEl) {
                paletteSearch.addEventListener('input', (e) => {
                    const term = e.target.value.toLowerCase().trim();
                    paletteEl.querySelectorAll('.palette-item').forEach(item => {
                        const txt = (item.dataset.title || '') + ' ' + (item.dataset.subtitle || '');
                        item.style.display = txt.toLowerCase().includes(term) ? 'flex' : 'none';
                    });
                });
            }

            // Palette Drag & 1-Click Add
            this.bindPaletteDragAndAdd();

            // Canvas Pan & Zoom Interactions
            this.bindCanvasInteraction();

            // Topbar Add Step Button
            if (this.btnTopbarAddStepEl) {
                this.btnTopbarAddStepEl.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const rect = this.btnTopbarAddStepEl.getBoundingClientRect();
                    this.openQuickAddPopover(null, rect.left, rect.bottom + 8);
                });
            }

            // Quick Add Popover Controls
            if (this.btnCloseQuickAddPopoverEl) {
                this.btnCloseQuickAddPopoverEl.addEventListener('click', () => this.closeQuickAddPopover());
            }

            if (this.quickAddSearchInputEl) {
                this.quickAddSearchInputEl.addEventListener('input', () => this.renderQuickAddItems());
                this.quickAddSearchInputEl.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const firstItem = this.quickAddItemsListEl ? this.quickAddItemsListEl.querySelector('.quick-add-item') : null;
                        if (firstItem) firstItem.click();
                    }
                });
            }

            if (this.quickAddCategoryTabsEl) {
                this.quickAddCategoryTabsEl.addEventListener('click', (e) => {
                    const tab = e.target.closest('.popover-cat-btn, .quick-add-cat-btn');
                    if (!tab) return;
                    this.quickAddCategoryTabsEl.querySelectorAll('.popover-cat-btn, .quick-add-cat-btn').forEach(b => {
                        b.classList.remove('active');
                        b.classList.remove('is-active');
                    });
                    tab.classList.add('active');
                    tab.classList.add('is-active');
                    this.quickAddFilterCategory = tab.dataset.cat || 'all';
                    this.renderQuickAddItems();
                });
            }

            // Close popover when clicking outside
            const onDocClick = (e) => {
                if (this.quickAddPopoverEl && !this.quickAddPopoverEl.classList.contains('is-hidden')) {
                    if (!this.quickAddPopoverEl.contains(e.target) && !e.target.closest('#btnTopbarAddStep, .flow-node-quick-add, .flow-node-port')) {
                        this.closeQuickAddPopover();
                    }
                }
            };
            document.addEventListener('click', onDocClick);
            this.boundWindowListeners.push({ target: document, event: 'click', handler: onDocClick });

            // Drawer Controls
            const btnCloseDrawer = document.getElementById('btnCloseNodeDrawer');
            if (btnCloseDrawer) btnCloseDrawer.addEventListener('click', () => this.closeNodeDrawer());

            const btnApplyNode = document.getElementById('btnApplyNodeChanges');
            if (btnApplyNode) btnApplyNode.addEventListener('click', () => this.applyNodeDrawerChanges());

            const btnDeleteNode = document.getElementById('btnDeleteCurrentNode');
            if (btnDeleteNode) btnDeleteNode.addEventListener('click', () => this.deleteSelectedNode());

            // Drawer Duplicate Button
            if (this.btnDuplicateCurrentNodeEl) {
                this.btnDuplicateCurrentNodeEl.addEventListener('click', () => {
                    if (this.selectedNodeId) {
                        this.duplicateNode(this.selectedNodeId);
                    }
                });
            }

            // Drawer Module Type Switcher Dropdown
            if (this.drawerModuleTypeSelectEl) {
                this.drawerModuleTypeSelectEl.addEventListener('change', (e) => {
                    if (this.selectedNodeId && e.target.value) {
                        this.switchNodeModuleType(this.selectedNodeId, e.target.value);
                    }
                });
            }

            // Smart Token Pills Click Insertion & Focus Tracking
            if (this.nodeConfigDrawerEl) {
                this.nodeConfigDrawerEl.addEventListener('focusin', (e) => {
                    if (e.target && (e.target.tagName === 'TEXTAREA' || (e.target.tagName === 'INPUT' && e.target.type === 'text'))) {
                        this.lastFocusedInput = e.target;
                    }
                });

                this.nodeConfigDrawerEl.addEventListener('click', (e) => {
                    const pill = e.target.closest('.token-pill, .token-chip');
                    if (pill) {
                        e.preventDefault();
                        const token = pill.dataset.token || pill.textContent.trim().replace(/^\+\s*/, '');
                        this.insertTokenIntoActiveField(token);
                        return;
                    }

                    // Preset chips that set an exact value on a target input/select
                    const presetChip = e.target.closest('.drawer-chip[data-target][data-val]');
                    if (presetChip) {
                        e.preventDefault();
                        const targetId = presetChip.dataset.target;
                        const targetVal = presetChip.dataset.val;
                        const targetEl = document.getElementById(targetId);
                        if (targetEl) {
                            targetEl.value = targetVal;
                            const $ = window.jQuery || window.$;
                            if ($ && $(targetEl).hasClass('select2-hidden-accessible')) {
                                $(targetEl).val(targetVal).trigger('change');
                            } else {
                                targetEl.dispatchEvent(new Event('change', { bubbles: true }));
                                targetEl.dispatchEvent(new Event('input', { bubbles: true }));
                            }
                            const parentRow = presetChip.closest('.drawer-chip-row');
                            if (parentRow) {
                                parentRow.querySelectorAll('.drawer-chip').forEach(c => c.classList.remove('active'));
                                presetChip.classList.add('active');
                            }
                            this.applyNodeDrawerChanges(false);
                        }
                        return;
                    }

                    // Append chips (e.g. adding keyword or tag to an input)
                    const appendChip = e.target.closest('.drawer-chip.append-chip[data-target][data-append]');
                    if (appendChip) {
                        e.preventDefault();
                        const targetId = appendChip.dataset.target;
                        const appendVal = appendChip.dataset.append;
                        const targetEl = document.getElementById(targetId);
                        if (targetEl) {
                            let curVal = targetEl.value.trim();
                            const terms = curVal ? curVal.split(',').map(s => s.trim().toLowerCase()) : [];
                            if (!terms.includes(appendVal.toLowerCase())) {
                                targetEl.value = curVal ? (curVal + ', ' + appendVal) : appendVal;
                                targetEl.dispatchEvent(new Event('input', { bubbles: true }));
                                targetEl.dispatchEvent(new Event('change', { bubbles: true }));
                                this.applyNodeDrawerChanges(false);
                            }
                            appendChip.classList.add('active');
                            setTimeout(() => appendChip.classList.remove('active'), 500);
                        }
                        return;
                    }

                    // Template preset filler
                    const tplFillBtn = e.target.closest('[data-fill-template]');
                    if (tplFillBtn) {
                        e.preventDefault();
                        const tplText = tplFillBtn.dataset.fillTemplate;
                        const targetEl = document.getElementById('cfgWaMessage') || document.getElementById('cfgEmailBody');
                        if (targetEl) {
                            targetEl.value = tplText;
                            targetEl.dispatchEvent(new Event('input', { bubbles: true }));
                            targetEl.dispatchEvent(new Event('change', { bubbles: true }));
                            this.applyNodeDrawerChanges(false);
                        }
                        return;
                    }
                });

                // Live Auto-Sync on Drawer Inputs (No need to search for Apply button every time)
                let drawerDebounceTimer = null;
                this.nodeConfigDrawerEl.addEventListener('input', (e) => {
                    if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) {
                        clearTimeout(drawerDebounceTimer);
                        drawerDebounceTimer = setTimeout(() => {
                            this.applyNodeDrawerChanges(false);
                        }, 250);
                    }
                });

                this.nodeConfigDrawerEl.addEventListener('change', (e) => {
                    if (e.target && (e.target.tagName === 'SELECT' || e.target.type === 'checkbox' || e.target.type === 'radio')) {
                        this.applyNodeDrawerChanges(false);
                    }
                });
            }


            // Quick Add Trigger on Empty Canvas
            const btnQuickTrigger = document.getElementById('btnQuickAddTrigger');
            if (btnQuickTrigger) {
                btnQuickTrigger.addEventListener('click', () => {
                    this.addNodeToCanvas('trigger', 'lead_created', 'New Lead Ingested', 'Facebook Ads, Web Forms & Website', 'fi-rr-bullseye-arrow', '#8b5cf6', 100, 150);
                });
            }

            // Simulation Console Close & Rerun
            const btnCloseConsole = document.getElementById('btnCloseTestConsole');
            if (btnCloseConsole && this.testRunConsoleEl) {
                btnCloseConsole.onclick = () => this.testRunConsoleEl.classList.add('is-closed');
            }
            const btnReRun = document.getElementById('btnConsoleReRun');
            if (btnReRun) {
                btnReRun.onclick = () => this.triggerSimulationRun();
            }
            const btnLivePreview = document.getElementById('btnConsoleLivePreview');
            if (btnLivePreview) {
                btnLivePreview.onclick = () => this.openSimulationInteractionModal();
            }

            // Simulation Interaction Modal Controls
            const btnCloseSimModal = document.getElementById('btnCloseSimulationModal');
            const btnCloseSimModalBtn = document.getElementById('btnCloseSimulationModalBtn');
            if (btnCloseSimModal) btnCloseSimModal.onclick = () => this.closeSimulationInteractionModal();
            if (btnCloseSimModalBtn) btnCloseSimModalBtn.onclick = () => this.closeSimulationInteractionModal();

            const presetSelect = document.getElementById('simulationPresetSelect');
            if (presetSelect) {
                presetSelect.onchange = (e) => {
                    this.triggerSimulationRun(e.target.value);
                };
            }

            const btnReRunModal = document.getElementById('btnReRunFromModal');
            if (btnReRunModal) {
                btnReRunModal.onclick = () => {
                    const preset = document.getElementById('simulationPresetSelect')?.value || 'couple';
                    this.triggerSimulationRun(preset);
                };
            }

            const btnSaveFromSim = document.getElementById('btnSaveWorkflowFromSim');
            if (btnSaveFromSim) {
                btnSaveFromSim.onclick = () => {
                    this.saveCurrentWorkflow();
                    this.closeSimulationInteractionModal();
                };
            }

            // Tab switching in Simulation Modal
            const simModal = document.getElementById('simulationInteractionModal');
            if (simModal) {
                simModal.querySelectorAll('.simulation-tab').forEach(tabBtn => {
                    tabBtn.onclick = () => {
                        simModal.querySelectorAll('.simulation-tab').forEach(b => b.classList.remove('is-active'));
                        simModal.querySelectorAll('.simulation-tab-content').forEach(c => c.classList.remove('is-active'));
                        tabBtn.classList.add('is-active');
                        const targetTab = tabBtn.dataset.tab;
                        if (targetTab === 'visual') document.getElementById('simTabVisual')?.classList.add('is-active');
                        else if (targetTab === 'trace') document.getElementById('simTabTrace')?.classList.add('is-active');
                        else if (targetTab === 'json') document.getElementById('simTabJson')?.classList.add('is-active');
                    };
                });
            }

            // Logs Modal Close
            const btnCloseLogs = document.getElementById('btnCloseLogsModal');
            if (btnCloseLogs && this.executionLogsModalEl) {
                btnCloseLogs.onclick = () => this.executionLogsModalEl.classList.add('is-hidden');
            }

            // Global Keydown (Escape, Delete, Undo/Redo, Save, Duplicate, Spacebar pan)
            const onKeyDown = (e) => {
                if (e.key === 'Escape') {
                    if (this.selectedConnectionId) {
                        this.selectConnection(null);
                    }
                    if (this.quickAddPopoverEl && !this.quickAddPopoverEl.classList.contains('is-hidden')) {
                        this.closeQuickAddPopover();
                    }
                    if (this.nodeConfigDrawerEl && !this.nodeConfigDrawerEl.classList.contains('is-closed')) {
                        this.closeNodeDrawer();
                    }
                    if (this.activePortDrag) {
                        this.cancelDraftConnection();
                    }
                    if (this.createChoiceModalEl && !this.createChoiceModalEl.classList.contains('is-hidden')) {
                        this.closeChoiceModal();
                    }
                    if (this.templatesModalEl && !this.templatesModalEl.classList.contains('is-hidden')) {
                        this.closeTemplatesModal();
                    }
                    if (this.executionLogsModalEl && !this.executionLogsModalEl.classList.contains('is-hidden')) {
                        this.executionLogsModalEl.classList.add('is-hidden');
                    }
                    if (this.simulationModalEl && !this.simulationModalEl.classList.contains('is-hidden')) {
                        this.closeSimulationInteractionModal();
                    }
                }

                // Delete selected node or connection with Delete or Backspace
                if ((e.key === 'Delete' || e.key === 'Backspace') && this.currentView === 'editor') {
                    const tag = document.activeElement ? document.activeElement.tagName : '';
                    if (tag !== 'INPUT' && tag !== 'TEXTAREA' && tag !== 'SELECT') {
                        if (this.selectedConnectionId) {
                            e.preventDefault();
                            this.deleteSelectedConnection();
                        } else if (this.selectedNodeId) {
                            e.preventDefault();
                            this.deleteSelectedNode();
                        }
                    }
                }

                // Undo: Ctrl+Z / Cmd+Z
                if ((e.key === 'z' || e.key === 'Z') && (e.ctrlKey || e.metaKey) && !e.shiftKey && this.currentView === 'editor') {
                    const tag = document.activeElement ? document.activeElement.tagName : '';
                    if (tag !== 'INPUT' && tag !== 'TEXTAREA') {
                        e.preventDefault();
                        this.undo();
                    }
                }

                // Redo: Ctrl+Y / Cmd+Y or Ctrl+Shift+Z / Cmd+Shift+Z
                if ((((e.key === 'y' || e.key === 'Y') && (e.ctrlKey || e.metaKey)) || ((e.key === 'z' || e.key === 'Z') && (e.ctrlKey || e.metaKey) && e.shiftKey)) && this.currentView === 'editor') {
                    const tag = document.activeElement ? document.activeElement.tagName : '';
                    if (tag !== 'INPUT' && tag !== 'TEXTAREA') {
                        e.preventDefault();
                        this.redo();
                    }
                }

                // Save: Ctrl+S / Cmd+S
                if ((e.key === 's' || e.key === 'S') && (e.ctrlKey || e.metaKey) && this.currentView === 'editor') {
                    e.preventDefault();
                    this.saveCurrentWorkflow();
                }

                // Duplicate selected node with Ctrl+D / Cmd+D
                if ((e.key === 'd' || e.key === 'D') && (e.ctrlKey || e.metaKey) && this.selectedNodeId && this.currentView === 'editor') {
                    const tag = document.activeElement ? document.activeElement.tagName : '';
                    if (tag !== 'INPUT' && tag !== 'TEXTAREA') {
                        e.preventDefault();
                        this.duplicateNode(this.selectedNodeId);
                    }
                }

                // Spacebar panning toggle
                if (e.code === 'Space' && this.currentView === 'editor') {
                    const tag = document.activeElement ? document.activeElement.tagName : '';
                    if (tag !== 'INPUT' && tag !== 'TEXTAREA') {
                        e.preventDefault();
                        this.isSpacePressed = true;
                        if (this.canvasContainerEl && !this.isPanning) {
                            this.canvasContainerEl.style.cursor = 'grab';
                        }
                    }
                }

                // Intercept refresh keys (Cmd+R, Ctrl+R, F5) when editor is active
                if (this.currentView === 'editor') {
                    const isRefresh = (e.key === 'r' && (e.metaKey || e.ctrlKey)) || e.key === 'F5';
                    if (isRefresh) {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                            Swal.fire({
                                title: 'Reload Workflow Editor?',
                                text: 'Any unsaved changes will be discarded and the workflow will reload from the database.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#7c3aed',
                                cancelButtonColor: '#94a3b8',
                                confirmButtonText: 'Yes, Reload Page',
                                cancelButtonText: 'Stay in Editor',
                                reverseButtons: true,
                                focusCancel: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    this.allowUnload = true;
                                    window.location.reload();
                                }
                            });
                        } else if (confirm('Any unsaved changes will be discarded. Reload page?')) {
                            this.allowUnload = true;
                            window.location.reload();
                        }
                    }
                }
            };
            window.addEventListener('keydown', onKeyDown);
            this.boundWindowListeners.push({ target: window, event: 'keydown', handler: onKeyDown });

            const onKeyUp = (e) => {
                if (e.code === 'Space') {
                    this.isSpacePressed = false;
                    if (this.canvasContainerEl && !this.isPanning) {
                        this.canvasContainerEl.style.cursor = 'default';
                    }
                }
            };
            window.addEventListener('keyup', onKeyUp);
            this.boundWindowListeners.push({ target: window, event: 'keyup', handler: onKeyUp });

            // Browser Back / Forward Navigation (popstate)
            const onPopState = (e) => {
                if (this.currentView === 'editor') {
                    // Temporarily re-push current workflow state so URL stays in place until user confirms
                    const currentId = this.currentWorkflowId || 'new';
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('id', currentId);
                    window.history.pushState({ workflowId: currentId }, '', currentUrl.toString());

                    this.confirmExitWorkflow(() => {
                        this.returnToListView();
                    });
                } else {
                    const urlParams = new URLSearchParams(window.location.search);
                    const paramId = urlParams.get('id') || urlParams.get('workflow_id');
                    if (paramId) {
                        if (paramId === 'new') {
                            this.openEditorForNew(false);
                        } else {
                            this.loadWorkflowIntoEditor(paramId, false);
                        }
                    }
                }
            };
            window.addEventListener('popstate', onPopState);
            this.boundWindowListeners.push({ target: window, event: 'popstate', handler: onPopState });

            // Native Browser Leave / Tab Close / Refresh Prompt
            const onBeforeUnload = (e) => {
                if (this.currentView === 'editor' && !this.allowUnload) {
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            };
            window.addEventListener('beforeunload', onBeforeUnload);
            this.boundWindowListeners.push({ target: window, event: 'beforeunload', handler: onBeforeUnload });
        }

        /* ==========================================================================
           URL Routing & Navigation State
           ========================================================================== */
        async initDataAndRoute() {
            await this.loadWorkflowsList();
            this.loadMetaDependencies();
            this.checkInitialUrlRoute();
        }

        setWorkflowUrlParam(workflowId) {
            try {
                const url = new URL(window.location.href);
                if (workflowId !== null && workflowId !== undefined && workflowId !== '') {
                    url.searchParams.set('id', workflowId);
                } else {
                    url.searchParams.delete('id');
                    url.searchParams.delete('workflow_id');
                }
                window.history.pushState({ workflowId: workflowId }, '', url.toString());
            } catch (e) {
                console.warn('URL state update failed:', e);
            }
        }

        clearWorkflowUrlParam() {
            try {
                const url = new URL(window.location.href);
                url.searchParams.delete('id');
                url.searchParams.delete('workflow_id');
                window.history.pushState({ workflowId: null }, '', url.toString());
            } catch (e) {
                console.warn('URL state clear failed:', e);
            }
        }

        checkInitialUrlRoute() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const targetId = urlParams.get('id') || urlParams.get('workflow_id');
                if (targetId) {
                    if (targetId === 'new') {
                        this.openEditorForNew(false);
                    } else {
                        this.loadWorkflowIntoEditor(targetId, false);
                    }
                }
            } catch (e) {
                console.warn('Error reading initial URL route:', e);
            }
        }

        confirmExitWorkflow(onConfirm) {
            if (this.currentView !== 'editor') {
                if (typeof onConfirm === 'function') onConfirm();
                return;
            }

            const swalOpts = {
                title: 'Exit Workflow Editor?',
                text: 'Any unsaved changes on your visual canvas will be lost. Are you sure you want to return to the workflows overview?',
                icon: 'warning',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, Exit Workflow',
                cancelButtonText: 'Stay in Editor',
                reverseButtons: true,
                focusCancel: true,
                closeOnConfirm: true
            };

            let confirmed = false;
            const handleConfirm = () => {
                if (confirmed) return;
                confirmed = true;
                if (typeof Swal !== 'undefined' && typeof Swal.close === 'function') {
                    try { Swal.close(); } catch (e) {}
                } else if (typeof window.swal === 'function' && typeof window.swal.close === 'function') {
                    try { window.swal.close(); } catch (e) {}
                }
                if (typeof onConfirm === 'function') {
                    onConfirm();
                }
            };

            const isAffirmative = (res) => {
                if (res === true) return true;
                if (res && (res.value === true || res.value || res.isConfirmed === true)) return true;
                return false;
            };

            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                try {
                    const promise = Swal.fire(swalOpts, (isConfirm) => {
                        if (isAffirmative(isConfirm)) {
                            handleConfirm();
                        }
                    });
                    if (promise && typeof promise.then === 'function') {
                        promise.then((result) => {
                            if (isAffirmative(result)) {
                                handleConfirm();
                            }
                        }).catch(() => {});
                    }
                } catch (e) {
                    if (typeof window.swal === 'function') {
                        window.swal(swalOpts, (isConfirm) => {
                            if (isAffirmative(isConfirm)) handleConfirm();
                        });
                    } else if (confirm('Any unsaved changes will be lost. Are you sure you want to exit the workflow editor?')) {
                        handleConfirm();
                    }
                }
            } else if (typeof window.swal === 'function') {
                window.swal(swalOpts, (isConfirm) => {
                    if (isAffirmative(isConfirm)) handleConfirm();
                });
            } else {
                if (confirm('Any unsaved changes will be lost. Are you sure you want to exit the workflow editor?')) {
                    handleConfirm();
                }
            }
        }

        /* ==========================================================================
           Data Fetching & Loading
           ========================================================================== */
        async loadWorkflowsList() {
            try {
                const res = await fetch(`${this.apiUrl}?action=list`, { cache: 'no-store' });
                const json = await res.json();
                if (json.status === 'success' && json.data) {
                    this.workflowsList = json.data.workflows || [];
                    this.updateStats(json.data.stats);
                    this.renderWorkflowsCards(this.workflowsList);
                }
            } catch (err) {
                console.error('Error fetching workflows list:', err);
                if (this.cardsContainerEl) {
                    this.cardsContainerEl.innerHTML = `
                        <div class="workflows-empty-state">
                            <i class="fi fi-rr-exclamation workflows-empty-icon error"></i>
                            <h3>Unable to load workflows</h3>
                            <p>${this.escapeHtml(err.message || 'Please check your connection and try again.')}</p>
                        </div>
                    `;
                }
            }
        }

        async loadMetaDependencies() {
            try {
                const [metaRes, tplRes] = await Promise.all([
                    fetch(`${this.apiUrl}?action=meta`),
                    fetch(`${this.apiUrl}?action=templates`)
                ]);
                const metaJson = await metaRes.json();
                if (metaJson.status === 'success' && metaJson.data) {
                    const d = metaJson.data;
                    this.leadSourcesList = d.lead_sources || [];
                    this.webFormsList = d.web_forms || [];
                    this.fbFormsList = d.fb_forms || [];
                    this.whatsappTemplatesList = d.whatsapp_templates || [];
                    this.agentsList = d.agents || [];
                    this.packagesList = d.packages || [];
                    this.pipelineStagesList = d.pipeline_stages || [];
                    if (d.catalog) {
                        this.moduleCatalog = d.catalog;
                    }
                }
                const tplJson = await tplRes.json();
                if (tplJson.status === 'success') {
                    this.renderStarterTemplates(tplJson.data || []);
                    this.renderModalTemplates(tplJson.data || []);
                }
            } catch (err) {
                console.warn('Failed to load auxiliary meta dependencies:', err);
            }
        }

        getModuleCatalog() {
            if (this.moduleCatalog) return this.moduleCatalog;
            return this.getDefaultModuleCatalog();
        }

        getDefaultModuleCatalog() {
            return {
                triggers: [
                    { type: 'lead_created', category: 'trigger', title: 'New Lead Ingested', subtitle: 'Facebook Ads, Web Forms & Website', icon: 'fi-rr-bullseye-arrow', color: '#8b5cf6', badge: 'Trigger' },
                    { type: 'whatsapp_incoming', category: 'trigger', title: 'WhatsApp Chat Inbound', subtitle: 'Traveler incoming chat or keyword', icon: 'fi-rr-comment-dots', color: '#10b981', badge: 'Trigger' },
                    { type: 'payment_received', category: 'trigger', title: 'Payment Captured', subtitle: 'Token advance or full balance captured', icon: 'fi-rr-badge-percent', color: '#f59e0b', badge: 'Trigger' },
                    { type: 'lead_stage_changed', category: 'trigger', title: 'Pipeline Stage Update', subtitle: 'CRM Pipeline stage transition', icon: 'fi-rr-refresh', color: '#06b6d4', badge: 'Trigger' },
                    { type: 'itinerary_viewed', category: 'trigger', title: 'Itinerary Link Viewed', subtitle: 'Client opened live proposal link', icon: 'fi-rr-eye', color: '#ec4899', badge: 'Trigger' },
                    { type: 'booking_confirmed', category: 'trigger', title: 'Booking Confirmed', subtitle: 'Advance deposit paid & voucher issued', icon: 'fi-rr-check-circle', color: '#10b981', badge: 'Trigger' },
                    { type: 'lead_idle_timeout', category: 'trigger', title: 'Follow-up Overdue', subtitle: 'Lead inactive for 24h, 48h, or 3 days', icon: 'fi-rr-alarm-clock', color: '#f97316', badge: 'Trigger' },
                    { type: 'trip_completed', category: 'trigger', title: 'Trip Completed / Returned', subtitle: 'Traveler returned home from tour', icon: 'fi-rr-plane-arrival', color: '#3b82f6', badge: 'Trigger' },
                    { type: 'webhook_received', category: 'trigger', title: 'Inbound Webhook', subtitle: 'External CRM or Zapier ping', icon: 'fi-rr-network-cloud', color: '#6366f1', badge: 'Trigger' }
                ],
                actions: [
                    { type: 'send_whatsapp', category: 'action', title: 'Send WhatsApp Message', subtitle: 'Automated notification to lead', icon: 'fi-rr-comment-alt', color: '#10b981', badge: 'Action' },
                    { type: 'send_whatsapp_buttons', category: 'action', title: 'WhatsApp Quick Buttons', subtitle: 'Interactive 3-button response menu', icon: 'fi-rr-menu-dots', color: '#059669', badge: 'Action' },
                    { type: 'send_email', category: 'action', title: 'Send Email Proposal', subtitle: 'Branded email with attached PDF itinerary', icon: 'fi-rr-envelope', color: '#2563eb', badge: 'Action' },
                    { type: 'send_itinerary', category: 'action', title: 'Dispatch Tour Itinerary', subtitle: 'Verified PDF proposal with rates', icon: 'fi-rr-document-signed', color: '#3b82f6', badge: 'Action' },
                    { type: 'assign_agent', category: 'action', title: 'Assign Sales Specialist', subtitle: 'Round-Robin or destination expert', icon: 'fi-rr-users-alt', color: '#0ea5e9', badge: 'Action' },
                    { type: 'update_lead_stage', category: 'action', title: 'Update Pipeline Stage', subtitle: 'Progress stage in CRM pipeline', icon: 'fi-rr-refresh', color: '#8b5cf6', badge: 'Action' },
                    { type: 'add_tags', category: 'action', title: 'Apply CRM Tags', subtitle: 'Add or remove tags on traveler record', icon: 'fi-rr-tags', color: '#f59e0b', badge: 'Action' },
                    { type: 'create_task', category: 'action', title: 'Create Agent Task', subtitle: 'Urgent discovery call or follow-up', icon: 'fi-rr-calendar-clock', color: '#ec4899', badge: 'Action' },
                    { type: 'payment_link', category: 'action', title: 'Generate Payment Link', subtitle: 'Instant deposit payment link', icon: 'fi-rr-credit-card', color: '#14b8a6', badge: 'Action' },
                    { type: 'notify_internal_team', category: 'action', title: 'Internal Team Alert', subtitle: 'Bell notification or team alert', icon: 'fi-rr-bell-ring', color: '#ef4444', badge: 'Action' },
                    { type: 'request_feedback', category: 'action', title: 'Request Review & Feedback', subtitle: 'Post-trip Google / TripAdvisor review', icon: 'fi-rr-star', color: '#eab308', badge: 'Action' },
                    { type: 'internal_note', category: 'action', title: 'Add Internal Timeline Note', subtitle: 'Log audit trail entry in CRM', icon: 'fi-rr-note', color: '#64748b', badge: 'Action' },
                    { type: 'webhook_call', category: 'action', title: 'Outbound Webhook Call', subtitle: 'POST payload to Zapier/Make/Slack', icon: 'fi-rr-plug', color: '#6366f1', badge: 'Action' }
                ],
                logic: [
                    { type: 'condition_branch', category: 'logic', title: 'If / Else Condition Branch', subtitle: 'Route travelers by preference / budget', icon: 'fi-rr-code-fork', color: '#f59e0b', badge: 'Logic' },
                    { type: 'delay', category: 'logic', title: 'Wait / Delay Time', subtitle: 'Natural pause (mins, hours, days)', icon: 'fi-rr-hourglass-end', color: '#64748b', badge: 'Logic' },
                    { type: 'working_hours_check', category: 'logic', title: 'Working Hours Router', subtitle: 'Route day hours vs after-hours night bot', icon: 'fi-rr-clock', color: '#6366f1', badge: 'Logic' }
                ]
            };
        }

        getModuleCatalogItem(type) {
            const catalog = this.getModuleCatalog();
            const allItems = [
                ...(catalog.triggers || []),
                ...(catalog.actions || []),
                ...(catalog.logic || [])
            ];
            return allItems.find(item => (item.type === type || item.id === type)) || null;
        }

        updateStats(stats) {
            if (!stats) return;
            const elTotal = document.getElementById('statTotalWorkflows');
            const elActive = document.getElementById('statActiveWorkflows');
            const elExec = document.getElementById('statTotalExecutions');
            if (elTotal) elTotal.textContent = stats.total || 0;
            if (elActive) elActive.textContent = stats.active || 0;
            if (elExec) elExec.textContent = stats.executions || 0;
        }

        renderWorkflowsCards(list) {
            if (!this.cardsContainerEl) return;
            if (!list || list.length === 0) {
                this.cardsContainerEl.innerHTML = `
                    <div class="workflows-empty-state">
                        <i class="fi fi-rr-sparkles workflows-empty-icon"></i>
                        <h3>No Workflows Created Yet</h3>
                        <p>Create your custom visual automation flowchart from scratch or install a pre-configured template.</p>
                        <div class="workflows-empty-actions">
                            <button type="button" class="btn-v3-primary" id="btnEmptyStateScratch">
                                <i class="fi fi-rr-paint-brush"></i> <span>Start from Scratch</span>
                            </button>
                            <button type="button" class="btn-v3-action" id="btnEmptyStateTemplates">
                                <i class="fi fi-rr-magic-wand"></i> <span>Browse Templates</span>
                            </button>
                        </div>
                    </div>
                `;
                const btnScratch = document.getElementById('btnEmptyStateScratch');
                const btnTpl = document.getElementById('btnEmptyStateTemplates');
                if (btnScratch) btnScratch.addEventListener('click', () => this.openEditorForNew());
                if (btnTpl) btnTpl.addEventListener('click', () => this.openTemplatesModal());
                return;
            }

            let html = '';
            list.forEach(wf => {
                const isActive = parseInt(wf.status) === 1;
                const cat = (wf.category || 'crm').toLowerCase();
                const nodeCount = wf.node_count || 0;
                const execCount = wf.execution_count || 0;

                let lastRunDisplay = 'Never run';
                if (wf.last_run_at) {
                    try {
                        const d = new Date(wf.last_run_at);
                        lastRunDisplay = d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
                    } catch (e) {
                        lastRunDisplay = wf.last_run_at;
                    }
                }

                let catIcon = 'fi-rr-apps';
                let catLabel = cat.toUpperCase();
                if (cat === 'crm') {
                    catIcon = 'fi-rr-users-alt';
                    catLabel = 'CRM';
                } else if (cat === 'whatsapp') {
                    catIcon = 'fi-rr-comment-dots';
                    catLabel = 'WhatsApp';
                } else if (cat === 'payments') {
                    catIcon = 'fi-rr-credit-card';
                    catLabel = 'Payments';
                } else if (cat === 'leads') {
                    catIcon = 'fi-rr-bullseye-arrow';
                    catLabel = 'Leads';
                } else if (cat === 'booking' || cat === 'bookings') {
                    catIcon = 'fi-rr-plane-departure';
                    catLabel = 'Bookings';
                }

                html += `
                    <div class="workflow-item-card cat-${cat} ${isActive ? '' : 'is-paused'}" data-id="${wf.id}">
                        <div class="workflow-card-header">
                            <span class="workflow-card-tag ${cat}">
                                <i class="fi ${catIcon}"></i>
                                <span>${catLabel}</span>
                            </span>
                            <div class="workflow-header-status-wrap">
                                <span class="workflow-status-badge ${isActive ? 'is-active' : 'is-paused'}">
                                    <span class="status-pulse-dot"></span>
                                    <span class="workflow-status-text">${isActive ? 'Active' : 'Paused'}</span>
                                </span>
                                <label class="v3-toggle-switch" title="Toggle Active / Paused">
                                    <input type="checkbox" class="workflow-status-toggle" data-id="${wf.id}" ${isActive ? 'checked' : ''}>
                                    <span class="v3-toggle-slider violet"></span>
                                </label>
                            </div>
                        </div>
                        <div class="workflow-card-body">
                            <h3 class="workflow-card-title">${this.escapeHtml(wf.name)}</h3>
                            <p class="workflow-card-desc">${this.escapeHtml(wf.description || 'Custom multi-step automation workflow.')}</p>
                            <div class="workflow-card-badges-row">
                                <span class="flow-step-badge"><i class="fi fi-rr-network"></i> <strong>${nodeCount}</strong> Steps</span>
                                <span class="flow-step-badge runs"><i class="fi fi-rr-bolt"></i> <strong>${execCount}</strong> Runs</span>
                            </div>
                        </div>
                        <div class="workflow-card-footer">
                            <div class="workflow-card-meta" title="Last run: ${wf.last_run_at || 'Never'}">
                                <i class="fi fi-rr-time-past"></i>
                                <span class="workflow-card-meta-text">${lastRunDisplay}</span>
                            </div>
                            <div class="workflow-card-actions">
                                <button type="button" class="btn-card-action btn-card-logs" data-id="${wf.id}" title="Execution Audit Logs" aria-label="Execution Audit Logs">
                                    <i class="fi fi-rr-document"></i>
                                </button>
                                <button type="button" class="btn-card-action btn-card-duplicate" data-id="${wf.id}" title="Duplicate Workflow" aria-label="Duplicate Workflow">
                                    <i class="fi fi-rr-copy"></i>
                                </button>
                                <button type="button" class="btn-card-action btn-card-delete" data-id="${wf.id}" title="Delete Workflow" aria-label="Delete Workflow">
                                    <i class="fi fi-rr-trash"></i>
                                </button>
                                <button type="button" class="btn-card-edit-flow" data-id="${wf.id}">
                                    <span>Edit Flow</span> <i class="fi fi-rr-arrow-small-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            this.cardsContainerEl.innerHTML = html;
            this.bindCardActions();
        }

        bindCardActions() {
            // Edit Flow
            this.cardsContainerEl.querySelectorAll('.btn-card-edit-flow').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const id = btn.dataset.id;
                    this.loadWorkflowIntoEditor(id);
                });
            });

            // Card click also opens editor
            this.cardsContainerEl.querySelectorAll('.workflow-item-card').forEach(card => {
                card.addEventListener('click', (e) => {
                    if (e.target.closest('.btn-card-action, .v3-toggle-switch, .workflow-status-toggle, .btn-card-edit-flow')) return;
                    const id = card.dataset.id;
                    if (id) this.loadWorkflowIntoEditor(id);
                });
            });

            // Status Toggle
            this.cardsContainerEl.querySelectorAll('.workflow-status-toggle').forEach(t => {
                t.addEventListener('change', async (e) => {
                    const id = e.target.dataset.id;
                    const status = e.target.checked ? 1 : 0;
                    try {
                        const res = await fetch(this.apiUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action: 'toggle_status', id: id, status: status })
                        });
                        const resJson = await res.json();
                        if (resJson.status === 'success') {
                            const card = e.target.closest('.workflow-item-card');
                            if (card) {
                                const statusBadge = card.querySelector('.workflow-status-badge');
                                const statusText = card.querySelector('.workflow-status-text');
                                if (status === 1) {
                                    card.classList.remove('is-paused');
                                    if (statusBadge) {
                                        statusBadge.classList.remove('is-paused');
                                        statusBadge.classList.add('is-active');
                                    }
                                    if (statusText) statusText.textContent = 'Active';
                                } else {
                                    card.classList.add('is-paused');
                                    if (statusBadge) {
                                        statusBadge.classList.remove('is-active');
                                        statusBadge.classList.add('is-paused');
                                    }
                                    if (statusText) statusText.textContent = 'Paused';
                                }
                            }
                        }
                    } catch (err) {
                        console.error('Status toggle failed:', err);
                    }
                });
            });

            // Duplicate
            this.cardsContainerEl.querySelectorAll('.btn-card-duplicate').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const id = btn.dataset.id;
                    try {
                        const res = await fetch(this.apiUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action: 'duplicate', id: id })
                        });
                        const resJson = await res.json();
                        if (resJson.status === 'success') {
                            this.showToast('Workflow duplicated successfully!', 'success');
                            this.loadWorkflowsList();
                        }
                    } catch (err) {
                        console.error('Duplicate error:', err);
                    }
                });
            });

            // Delete
            this.cardsContainerEl.querySelectorAll('.btn-card-delete').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const id = btn.dataset.id;
                    
                    const doDelete = async () => {
                        try {
                            const res = await fetch(this.apiUrl, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ action: 'delete', id: id })
                            });
                            const resJson = await res.json();
                            if (resJson.status === 'success') {
                                this.showToast('Workflow deleted.', 'success');
                                this.loadWorkflowsList();
                            }
                        } catch (err) {
                            console.error('Delete error:', err);
                        }
                    };

                    if (typeof window.swal === 'function') {
                        window.swal({
                            title: 'Delete Workflow?',
                            text: 'Are you sure you want to delete this automation workflow? All execution history will be removed.',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'Yes, delete',
                            closeOnConfirm: true
                        }, function (isConfirm) {
                            if (isConfirm) doDelete();
                        });
                    } else if (confirm('Are you sure you want to delete this automation workflow? All execution history will be removed.')) {
                        doDelete();
                    }
                });
            });

            // View Logs
            this.cardsContainerEl.querySelectorAll('.btn-card-logs').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const id = btn.dataset.id;
                    this.openExecutionLogsModal(id);
                });
            });
        }

        formatTemplateCardHtml(tpl, btnLabel = 'Install into Workspace', btnIcon = 'fi-rr-check') {
            const cat = (tpl.category || 'crm').toLowerCase();
            let catIcon = 'fi-rr-apps';
            let catLabel = cat.toUpperCase();
            if (cat === 'crm') {
                catIcon = 'fi-rr-users-alt';
                catLabel = 'CRM';
            } else if (cat === 'whatsapp') {
                catIcon = 'fi-rr-comment-dots';
                catLabel = 'WhatsApp';
            } else if (cat === 'payments') {
                catIcon = 'fi-rr-credit-card';
                catLabel = 'Payments';
            } else if (cat === 'leads') {
                catIcon = 'fi-rr-bullseye-arrow';
                catLabel = 'Leads';
            } else if (cat === 'booking' || cat === 'bookings') {
                catIcon = 'fi-rr-plane-departure';
                catLabel = 'Bookings';
            }

            let triggerLabel = 'Automated Trigger';
            if (tpl.trigger_type === 'lead_created') {
                triggerLabel = 'New Lead Ingested';
            } else if (tpl.trigger_type === 'whatsapp_message_received') {
                triggerLabel = 'WhatsApp Message';
            } else if (tpl.trigger_type === 'payment_captured') {
                triggerLabel = 'Payment Captured';
            } else if (tpl.trigger_type === 'deal_status_changed') {
                triggerLabel = 'Pipeline Status Change';
            }

            const stepCount = (tpl.nodes && Array.isArray(tpl.nodes)) ? tpl.nodes.length : 4;

            return `
                <div class="template-card cat-${cat}" data-id="${tpl.id}">
                    <div class="template-card-top">
                        <div class="template-card-header">
                            <div class="template-icon ${cat}">
                                <i class="fi ${catIcon}"></i>
                            </div>
                            <span class="template-category-pill ${cat}">
                                <i class="fi ${catIcon}"></i> <span>${catLabel}</span>
                            </span>
                        </div>
                        <h4 class="template-title">${this.escapeHtml(tpl.name)}</h4>
                        <p class="template-desc">${this.escapeHtml(tpl.description)}</p>
                        <div class="template-meta-row">
                            <span class="template-meta-pill steps">
                                <i class="fi fi-rr-network"></i> <strong>${stepCount}</strong> Steps
                            </span>
                            <span class="template-meta-pill trigger">
                                <i class="fi fi-rr-bolt"></i> <span>${triggerLabel}</span>
                            </span>
                        </div>
                    </div>
                    <div class="template-card-bottom">
                        <button type="button" class="btn-use-template" data-template-id="${tpl.id}">
                            <i class="fi ${btnIcon}"></i> <span>${btnLabel}</span>
                        </button>
                    </div>
                </div>
            `;
        }

        renderStarterTemplates(templates) {
            if (!this.starterTemplatesRowEl) return;
            let html = `
                <div class="template-card template-scratch-card" id="starterCardScratch">
                    <div class="template-card-top">
                        <div class="template-card-header">
                            <div class="template-icon scratch-icon"><i class="fi fi-rr-paint-brush"></i></div>
                            <span class="template-category-pill crm"><i class="fi fi-rr-plus"></i> <span>CUSTOM FLOW</span></span>
                        </div>
                        <h4 class="template-title">Start from Scratch</h4>
                        <p class="template-desc">Begin with a clean canvas to architect your unique multi-step triggers, actions, and logic branches.</p>
                        <div class="template-meta-row">
                            <span class="template-meta-pill steps"><i class="fi fi-rr-grid"></i> Empty Canvas</span>
                            <span class="template-meta-pill trigger"><i class="fi fi-rr-settings-sliders"></i> Full Flexibility</span>
                        </div>
                    </div>
                    <div class="template-card-bottom">
                        <button type="button" class="btn-use-template btn-use-template-primary" id="btnStarterUseScratch">
                            <i class="fi fi-rr-plus"></i> <span>Create Blank Flow</span>
                        </button>
                    </div>
                </div>
            `;

            templates.forEach(tpl => {
                html += this.formatTemplateCardHtml(tpl, 'Use Template', 'fi-rr-plus');
            });
            this.starterTemplatesRowEl.innerHTML = html;

            const btnScratch = this.starterTemplatesRowEl.querySelector('#btnStarterUseScratch');
            if (btnScratch) {
                btnScratch.addEventListener('click', () => this.openEditorForNew());
            }

            this.starterTemplatesRowEl.querySelectorAll('.btn-use-template[data-template-id]').forEach(b => {
                b.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.installTemplate(b.dataset.templateId);
                });
            });
        }

        renderModalTemplates(templates) {
            const listEl = document.getElementById('modalTemplatesList');
            if (!listEl) return;

            if (!templates || templates.length === 0) {
                listEl.innerHTML = `
                    <div class="workflows-empty-state">
                        <i class="fi fi-rr-magic-wand"></i>
                        <h4>No Predefined Templates Found</h4>
                        <p>No automation templates are currently available. You can design one from scratch!</p>
                    </div>
                `;
                return;
            }

            let html = '';
            templates.forEach(tpl => {
                html += this.formatTemplateCardHtml(tpl, 'Install into Workspace', 'fi-rr-check');
            });
            listEl.innerHTML = html;

            listEl.querySelectorAll('.template-card').forEach(card => {
                card.addEventListener('click', (e) => {
                    if (e.target.closest('.btn-use-template')) return;
                    const tplId = card.dataset.id;
                    if (tplId) {
                        this.installTemplate(tplId);
                        this.closeTemplatesModal();
                    }
                });
            });

            listEl.querySelectorAll('.btn-use-template[data-template-id]').forEach(b => {
                b.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.installTemplate(b.dataset.templateId);
                    this.closeTemplatesModal();
                });
            });
        }

        async installTemplate(templateId) {
            try {
                const res = await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'install_template', template_id: templateId })
                });
                const resJson = await res.json();
                if (resJson.status === 'success' && resJson.data) {
                    this.showToast('Template installed successfully!', 'success');
                    this.loadWorkflowIntoEditor(resJson.data.id);
                } else {
                    this.showToast(resJson.msg || 'Failed to install template.', 'error');
                }
            } catch (err) {
                console.error('Install template error:', err);
            }
        }

        filterWorkflows(term) {
            term = term.toLowerCase().trim();
            if (!this.cardsContainerEl) return;
            const cards = this.cardsContainerEl.querySelectorAll('.workflow-item-card');
            cards.forEach(c => {
                const title = c.querySelector('.workflow-card-title')?.textContent.toLowerCase() || '';
                const desc = c.querySelector('.workflow-card-desc')?.textContent.toLowerCase() || '';
                if (title.includes(term) || desc.includes(term)) {
                    c.style.display = 'flex';
                } else {
                    c.style.display = 'none';
                }
            });
        }

        filterWorkflowsByCategory(cat) {
            cat = cat.toLowerCase();
            if (!this.cardsContainerEl) return;
            const cards = this.cardsContainerEl.querySelectorAll('.workflow-item-card');
            cards.forEach(c => {
                if (cat === 'all') {
                    c.style.display = 'flex';
                } else {
                    const tag = c.querySelector('.workflow-card-tag')?.textContent.toLowerCase() || '';
                    c.style.display = tag === cat ? 'flex' : 'none';
                }
            });
        }

        /* ==========================================================================
           Visual Editor - Canvas & Flowchart Mechanics
           ========================================================================== */
        openEditorForNew(updateUrl = true) {
            this.currentWorkflowId = 'new';
            this.currentWorkflowData = null;
            if (updateUrl) {
                this.setWorkflowUrlParam('new');
            }
            this.nodes = [
                {
                    id: 'node_trigger',
                    type: 'trigger',
                    category: 'trigger',
                    trigger_event: 'lead_created',
                    title: 'New Lead Ingested',
                    subtitle: 'Facebook Ads, Web Forms & Website',
                    icon: 'fi-rr-bullseye-arrow',
                    color: '#8b5cf6',
                    x: 100,
                    y: 160,
                    config: { sources: ['all'], filter_mode: 'any' }
                }
            ];
            this.connections = [];
            this.zoomLevel = 1.0;
            this.panX = 40;
            this.panY = 40;
            this.selectedNodeId = null;
            this.selectedConnectionId = null;
            this.history = [];
            this.historyIndex = -1;
            this.pushHistoryState();
            this.markDirty(false);

            if (this.editorWorkflowNameEl) this.editorWorkflowNameEl.value = 'New Custom Automation Flow';
            if (this.editorStatusToggleEl) this.editorStatusToggleEl.checked = true;
            if (this.editorStatusTextEl) this.editorStatusTextEl.textContent = 'Active';

            const catBadge = document.getElementById('editorCategoryBadge');
            if (catBadge) catBadge.textContent = 'CUSTOM FLOW';

            this.switchView('editor');
            this.renderCanvas();
            setTimeout(() => this.renderCanvas(), 60);
        }

        async loadWorkflowIntoEditor(workflowId, updateUrl = true) {
            try {
                const res = await fetch(`${this.apiUrl}?action=get&id=${workflowId}`, { cache: 'no-store' });
                const json = await res.json();
                if (json.status === 'success' && json.data) {
                    const wf = json.data;
                    this.currentWorkflowId = wf.id;
                    this.currentWorkflowData = wf;
                    if (updateUrl) {
                        this.setWorkflowUrlParam(wf.id);
                    }
                    this.nodes = Array.isArray(wf.nodes) ? wf.nodes : [];
                    this.connections = Array.isArray(wf.connections) ? wf.connections : [];
                    this.selectedNodeId = null;
                    this.selectedConnectionId = null;
                    this.history = [];
                    this.historyIndex = -1;
                    this.pushHistoryState();
                    this.markDirty(false);

                    if (this.editorWorkflowNameEl) this.editorWorkflowNameEl.value = wf.name || 'Untitled Workflow';
                    if (this.editorStatusToggleEl) this.editorStatusToggleEl.checked = parseInt(wf.status) === 1;
                    if (this.editorStatusTextEl) this.editorStatusTextEl.textContent = parseInt(wf.status) === 1 ? 'Active' : 'Paused';

                    const catBadge = document.getElementById('editorCategoryBadge');
                    if (catBadge) catBadge.textContent = (wf.category || 'crm').toUpperCase() + ' FLOW';

                    // Restore canvas zoom / pan
                    if (wf.canvas && typeof wf.canvas === 'object') {
                        this.zoomLevel = wf.canvas.zoom || 1.0;
                        this.panX = wf.canvas.panX || 40;
                        this.panY = wf.canvas.panY || 40;
                    }

                    this.switchView('editor');
                    this.renderCanvas();
                    setTimeout(() => this.renderCanvas(), 60);
                }
            } catch (err) {
                console.error('Failed to load workflow into editor:', err);
                this.showToast('Could not open workflow editor: ' + err.message, 'error');
            }
        }

        /* ==========================================================================
           Undo / Redo & History Management
           ========================================================================== */
        pushHistoryState() {
            try {
                const snapshot = JSON.stringify({
                    nodes: this.nodes,
                    connections: this.connections
                });

                if (this.historyIndex >= 0 && this.history[this.historyIndex] === snapshot) {
                    return;
                }

                if (this.historyIndex < this.history.length - 1) {
                    this.history = this.history.slice(0, this.historyIndex + 1);
                }

                this.history.push(snapshot);
                if (this.history.length > this.maxHistory) {
                    this.history.shift();
                } else {
                    this.historyIndex++;
                }

                this.updateHistoryButtons();
            } catch (err) {
                console.warn('History snapshot error:', err);
            }
        }

        undo() {
            if (this.historyIndex > 0) {
                this.historyIndex--;
                const state = JSON.parse(this.history[this.historyIndex]);
                this.nodes = state.nodes || [];
                this.connections = state.connections || [];
                this.selectedConnectionId = null;
                this.renderCanvas();
                this.updateHistoryButtons();
                this.markDirty(true);
                this.showToast('Undo', 'info');
                if (this.selectedNodeId && !this.nodes.some(n => n.id === this.selectedNodeId)) {
                    this.closeNodeDrawer();
                }
            }
        }

        redo() {
            if (this.historyIndex < this.history.length - 1) {
                this.historyIndex++;
                const state = JSON.parse(this.history[this.historyIndex]);
                this.nodes = state.nodes || [];
                this.connections = state.connections || [];
                this.selectedConnectionId = null;
                this.renderCanvas();
                this.updateHistoryButtons();
                this.markDirty(true);
                this.showToast('Redo', 'info');
                if (this.selectedNodeId && !this.nodes.some(n => n.id === this.selectedNodeId)) {
                    this.closeNodeDrawer();
                }
            }
        }

        updateHistoryButtons() {
            if (this.btnCanvasUndoEl) {
                this.btnCanvasUndoEl.disabled = (this.historyIndex <= 0);
            }
            if (this.btnCanvasRedoEl) {
                this.btnCanvasRedoEl.disabled = (this.historyIndex >= this.history.length - 1);
            }
        }

        markDirty(isDirty = true) {
            this.isDirty = isDirty;
            if (this.editorSaveStateEl) {
                if (isDirty) {
                    this.editorSaveStateEl.innerHTML = `<i class="fi fi-rr-circle" style="color: #f59e0b; font-size: 8px;"></i> Unsaved changes`;
                } else {
                    this.editorSaveStateEl.innerHTML = `<i class="fi fi-rr-check"></i> Saved at ${new Date().toLocaleTimeString()}`;
                }
            }
        }

        switchView(viewName) {
            this.currentView = viewName;
            this.refreshDomReferences();

            if (viewName === 'editor') {
                if (this.listViewEl) {
                    this.listViewEl.classList.remove('is-visible');
                    this.listViewEl.classList.add('is-hidden');
                }
                if (this.editorViewEl) {
                    this.editorViewEl.classList.remove('is-hidden');
                    this.editorViewEl.classList.add('is-visible');
                }
                document.body.classList.add('v3-workflow-editor-active');
            } else {
                if (this.editorViewEl) {
                    this.editorViewEl.classList.remove('is-visible');
                    this.editorViewEl.classList.add('is-hidden');
                }
                if (this.listViewEl) {
                    this.listViewEl.classList.remove('is-hidden');
                    this.listViewEl.classList.add('is-visible');
                }
                document.body.classList.remove('v3-workflow-editor-active');
            }
        }

        returnToListView() {
            this.clearWorkflowUrlParam();
            this.switchView('list');
            this.loadWorkflowsList();
        }

        setZoom(newZoom) {
            this.zoomLevel = Math.max(0.4, Math.min(2.0, parseFloat(newZoom.toFixed(2))));
            if (this.canvasZoomLevelEl) {
                this.canvasZoomLevelEl.textContent = Math.round(this.zoomLevel * 100) + '%';
            }
            this.applyCanvasTransform();
        }

        applyCanvasTransform() {
            if (!this.canvasViewportEl) return;
            this.canvasViewportEl.style.transform = `translate(${this.panX}px, ${this.panY}px) scale(${this.zoomLevel})`;
        }

        /* ==========================================================================
           Canvas Mouse & Pan Interactions (Safe Pointer Handling)
           ========================================================================== */
        bindCanvasInteraction() {
            const container = this.canvasContainerEl;
            if (!container) return;

            const onStartPan = (e) => {
                if (e.button !== 0 && e.button !== 1) return;
                // If clicked inside a node card, port, button, drawer, or dialog: NEVER PAN!
                if (e.target.closest('.flow-node-card, .flow-node-port, .flow-node-quick-add, .canvas-floating-tips, .canvas-empty-helper, .btn-v3-primary, .btn-v3-action, button, input')) {
                    return;
                }

                // If user clicks on empty canvas while draft connection is active: cancel it
                if (this.activePortDrag) {
                    this.cancelDraftConnection();
                    return;
                }

                // Clicked on empty canvas: deselect connection and deselect node
                if (this.selectedConnectionId) {
                    this.selectConnection(null);
                }
                if (this.selectedNodeId) {
                    this.selectNode(null);
                    this.closeNodeDrawer();
                }

                if (this.quickAddPopoverEl && !this.quickAddPopoverEl.classList.contains('is-hidden')) {
                    this.closeQuickAddPopover();
                }

                this.isPanning = true;
                this.startPanMouseX = e.clientX;
                this.startPanMouseY = e.clientY;
                this.startPanX = this.panX;
                this.startPanY = this.panY;
                container.style.cursor = 'grabbing';

                try {
                    container.setPointerCapture(e.pointerId);
                } catch (err) {}
            };

            const onMovePan = (e) => {
                if (this.isPanning) {
                    const dx = e.clientX - this.startPanMouseX;
                    const dy = e.clientY - this.startPanMouseY;
                    this.panX = this.startPanX + dx;
                    this.panY = this.startPanY + dy;
                    this.applyCanvasTransform();
                }
                if (this.activePortDrag) {
                    this.updateDraftConnection(e.clientX, e.clientY);
                }
            };

            const onEndPan = (e) => {
                if (this.isPanning) {
                    this.isPanning = false;
                    container.style.cursor = 'default';
                    try {
                        container.releasePointerCapture(e.pointerId);
                    } catch (err) {}
                }
            };

            container.addEventListener('pointerdown', onStartPan);
            container.addEventListener('pointermove', onMovePan);
            container.addEventListener('pointerup', onEndPan);
            container.addEventListener('pointercancel', onEndPan);

            // Mouse Wheel Zoom
            container.addEventListener('wheel', (e) => {
                e.preventDefault();
                const delta = e.deltaY < 0 ? 0.08 : -0.08;
                this.setZoom(this.zoomLevel + delta);
            }, { passive: false });
        }

        /* ==========================================================================
           Palette Drag-and-Drop & 1-Click Module Placement
           ========================================================================== */
        bindPaletteDragAndAdd() {
            const paletteEl = document.getElementById('workflowPalette');
            if (!paletteEl) return;

            // 1. Draggable and 1-Click Add on Palette Items
            paletteEl.querySelectorAll('.palette-item').forEach(item => {
                item.addEventListener('dragstart', (e) => {
                    const moduleData = {
                        type: item.dataset.type,
                        actionType: item.dataset.action || item.dataset.event,
                        title: item.dataset.title,
                        subtitle: item.dataset.subtitle,
                        icon: item.dataset.icon,
                        color: item.dataset.color
                    };
                    e.dataTransfer.setData('text/plain', JSON.stringify(moduleData));
                    e.dataTransfer.effectAllowed = 'copy';
                });

                // Click ANYWHERE on palette item to append directly to canvas
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.appendNodeFromPalette(item);
                });
            });

            // 2. Canvas Container Drop Handling
            const container = this.canvasContainerEl;
            if (container) {
                container.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'copy';
                });

                container.addEventListener('drop', (e) => {
                    e.preventDefault();
                    try {
                        const raw = e.dataTransfer.getData('text/plain');
                        if (!raw) return;
                        const data = JSON.parse(raw);
                        const rect = container.getBoundingClientRect();

                        // Convert screen cursor to canvas world coordinates
                        const canvasX = Math.round((e.clientX - rect.left - this.panX) / this.zoomLevel);
                        const canvasY = Math.round((e.clientY - rect.top - this.panY) / this.zoomLevel);

                        this.addNodeToCanvas(data.type, data.actionType, data.title, data.subtitle, data.icon, data.color, canvasX, canvasY);
                    } catch (err) {
                        console.error('Drop error:', err);
                    }
                });
            }
        }

        appendNodeFromPalette(paletteItem) {
            const type = paletteItem.dataset.type;
            const actionType = paletteItem.dataset.action || paletteItem.dataset.event;
            const title = paletteItem.dataset.title;
            const subtitle = paletteItem.dataset.subtitle;
            const icon = paletteItem.dataset.icon;
            const color = paletteItem.dataset.color;

            let nextX = 100;
            let nextY = 160;
            let prevNode = null;

            if (this.nodes.length > 0) {
                // If a node is selected, connect from selected; otherwise from rightmost node
                if (this.selectedNodeId) {
                    prevNode = this.nodes.find(n => n.id === this.selectedNodeId);
                }
                if (!prevNode) {
                    prevNode = this.nodes.reduce((prev, curr) => (curr.x > prev.x) ? curr : prev, this.nodes[0]);
                }
                nextX = prevNode.x + 320;
                nextY = prevNode.y;
            }

            const newNode = this.addNodeToCanvas(type, actionType, title, subtitle, icon, color, nextX, nextY);

            if (prevNode && newNode) {
                const connId = `conn_${prevNode.id}_${newNode.id}`;
                if (!this.connections.some(c => c.from === prevNode.id && c.to === newNode.id)) {
                    this.connections.push({
                        id: connId,
                        from: prevNode.id,
                        to: newNode.id,
                        label: 'Next Step'
                    });
                    this.renderConnections();
                }
            }
        }

        /* ==========================================================================
           Node Card Creation & Rendering
           ========================================================================== */
        addNodeToCanvas(type, actionType, title, subtitle, icon, color, x, y) {
            const nodeId = 'node_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const newNode = {
                id: nodeId,
                type: type,
                category: type,
                action_type: actionType,
                trigger_event: (type === 'trigger') ? actionType : undefined,
                title: title,
                subtitle: subtitle,
                icon: icon,
                color: color || '#7c3aed',
                x: Math.max(20, x),
                y: Math.max(20, y),
                config: this.getDefaultConfigForModule(actionType)
            };

            this.nodes.push(newNode);
            this.renderCanvas();
            this.selectNode(nodeId);
            this.openNodeDrawer(nodeId);
            return newNode;
        }

        getDefaultConfigForModule(actionType) {
            switch (actionType) {
                case 'lead_created':
                    return {
                        source: 'all',
                        min_budget: '0',
                        min_pax: '1'
                    };
                case 'whatsapp_incoming':
                    return {
                        trigger_rule: 'keywords',
                        keywords: 'itinerary, package, tour, trip, quote, cost, booking',
                        match_type: 'contains'
                    };
                case 'payment_received':
                    return {
                        payment_type: 'all',
                        min_amount: '1'
                    };
                case 'lead_stage_changed':
                    return {
                        from_stage: 'any',
                        to_stage: 'any'
                    };
                case 'send_whatsapp':
                    return {
                        template_id: '0',
                        message_template: "Namaste {name}! 🌍 Welcome to {company}.\n\nThank you for reaching out for your upcoming travel inquiry. Our holiday specialist is curating custom hotel and cab options for you!",
                        attach_brochure: '1'
                    };
                case 'send_itinerary':
                    return {
                        package_id: '0',
                        itinerary_title: 'Custom Destination Tour Itinerary',
                        message_text: "Here is your tailored holiday itinerary brochure PDF with private sightseeing transfers and 4★ mountain resort inclusions: 📄 [Download PDF]"
                    };
                case 'assign_agent':
                    return {
                        mode: 'round_robin',
                        assigned_agent_id: '0',
                        fallback_mode: 'round_robin'
                    };
                case 'update_lead_stage':
                    return {
                        stage_code: 'itinerary_sent',
                        status_note: 'Automated stage progression'
                    };
                case 'add_tags':
                    return {
                        action: 'add',
                        tags: 'new_lead, high_intent'
                    };
                case 'create_task':
                    return {
                        task_type: 'Call',
                        due_hours: 2,
                        assigned_agent_id: '0',
                        task_note: 'Discovery Call: Confirm travelers count, hotel category preference, and dates.'
                    };
                case 'condition_branch':
                    return {
                        field: 'destination',
                        operator: 'contains',
                        value: '',
                        branch_yes_label: 'Condition Met',
                        branch_no_label: 'Otherwise'
                    };
                case 'delay':
                    return {
                        delay_seconds: 3
                    };
                case 'payment_link':
                    return {
                        amount: '5000',
                        expiry_hours: '48',
                        message_text: "To lock in early-bird hotel rates and confirm driver allotment, you can secure reservation with ₹5,000 token advance:\n\n💳 Instant Secure Payment: {payment_link}"
                    };
                case 'webhook_call':
                    return {
                        url: 'https://hooks.zapier.com/hooks/catch/custom/',
                        method: 'POST'
                    };
                case 'internal_note':
                    return {
                        note_text: 'Automation executed: customer engaged via workflow.',
                        is_pinned: '0'
                    };
                case 'itinerary_viewed':
                    return {
                        package_id: '0',
                        min_view_seconds: '15'
                    };
                case 'webhook_received':
                    return {
                        event_name: 'external_lead',
                        secret_token: ''
                    };
                case 'booking_confirmed':
                    return {
                        min_amount: '0',
                        package_type: 'all',
                        send_voucher: '1'
                    };
                case 'lead_idle_timeout':
                    return {
                        idle_hours: '24',
                        stage: 'any',
                        alert_manager: '1'
                    };
                case 'trip_completed':
                    return {
                        trigger_timing: 'same_day',
                        request_review: '1'
                    };
                case 'send_whatsapp_buttons':
                    return {
                        header_text: 'Trip Proposal Update',
                        body_text: "Namaste {name}! 🌴 We have prepared your tailored quotation for {destination}. Would you like to proceed or review details?",
                        btn1: 'Confirm Booking',
                        btn2: 'Talk to Expert',
                        btn3: 'Modify Dates'
                    };
                case 'send_email':
                    return {
                        subject: 'Your Vacation Proposal & Itinerary - {destination}',
                        sender_dept: 'sales',
                        attach_pdf: '1',
                        email_body: "Dear {name},\n\nThank you for choosing {company}. Attached is your detailed day-by-day travel plan and price breakdown for {destination}.\n\nPlease let us know if you need any adjustments!"
                    };
                case 'notify_internal_team':
                    return {
                        channel: 'all',
                        priority: 'high',
                        alert_text: "🔥 Hot Lead Alert: {name} engaged with {destination} proposal. Call immediately!"
                    };
                case 'request_feedback':
                    return {
                        platform: 'google_review',
                        review_url: 'https://g.page/r/aleya-tours/review',
                        incentive: '10% discount coupon for next vacation'
                    };
                case 'working_hours_check':
                    return {
                        start_time: '09:30',
                        end_time: '19:30',
                        work_days: 'mon_sat'
                    };
                default:
                    return {};
            }
        }

        renderCanvas() {
            this.applyCanvasTransform();
            this.renderNodes();
            this.renderConnections();

            if (this.canvasEmptyHelperEl) {
                if (this.nodes.length === 0) {
                    this.canvasEmptyHelperEl.classList.remove('is-hidden');
                } else {
                    this.canvasEmptyHelperEl.classList.add('is-hidden');
                }
            }
        }

        renderNodes() {
            if (!this.canvasNodesLayerEl) return;
            this.canvasNodesLayerEl.innerHTML = '';

            this.nodes.forEach(node => {
                const isSelected = (node.id === this.selectedNodeId);
                const isTrigger = (node.category === 'trigger');
                const isLogic = (node.category === 'logic');

                const cardEl = document.createElement('div');
                cardEl.className = `flow-node-card ${isSelected ? 'is-selected' : ''}`;
                cardEl.id = `flowNode_${node.id}`;
                cardEl.dataset.nodeId = node.id;
                cardEl.style.left = `${node.x}px`;
                cardEl.style.top = `${node.y}px`;

                let outputPortsHtml = '';
                if (isLogic) {
                    outputPortsHtml = `
                        <div class="flow-node-port port-output port-branch-yes" data-node-id="${node.id}" data-port="yes" title="If Condition True / Yes"></div>
                        <span class="branch-port-label yes">YES</span>
                        <div class="flow-node-port port-output port-branch-no" data-node-id="${node.id}" data-port="no" title="If Condition False / No"></div>
                        <span class="branch-port-label no">NO</span>
                    `;
                } else {
                    outputPortsHtml = `
                        <div class="flow-node-port port-output" data-node-id="${node.id}" data-port="out" title="Connect next step"></div>
                        <div class="flow-node-quick-add" data-node-id="${node.id}" title="Quick Add Next Step">
                            <i class="fi fi-rr-plus"></i>
                        </div>
                    `;
                }

                cardEl.innerHTML = `
                    ${!isTrigger ? `<div class="flow-node-port port-input" data-node-id="${node.id}" data-port="in" title="Incoming Flow"></div>` : ''}
                    ${outputPortsHtml}
                    
                    <div class="flow-node-header" data-handle="drag">
                        <span class="flow-node-category-tag ${node.category}">${node.category.toUpperCase()}</span>
                        <div class="flow-node-header-actions">
                            <button type="button" class="btn-node-tool btn-node-duplicate" data-node-id="${node.id}" title="Duplicate Step (Cmd/Ctrl+D)">
                                <i class="fi fi-rr-copy"></i>
                            </button>
                            <button type="button" class="btn-node-tool btn-node-edit btn-node-config" data-node-id="${node.id}" title="Edit Step Configuration">
                                <i class="fi fi-rr-edit"></i>
                            </button>
                            <button type="button" class="btn-node-tool btn-node-delete" data-node-id="${node.id}" title="Delete Step (Del/Backspace)">
                                <i class="fi fi-rr-cross-small"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flow-node-body">
                        <div class="flow-node-icon">
                            <i class="${node.icon || 'fi fi-rr-apps'}"></i>
                        </div>
                        <div class="flow-node-content">
                            <h4 class="flow-node-title">${this.escapeHtml(node.title)}</h4>
                            <p class="flow-node-subtitle">${this.escapeHtml(node.subtitle || 'Step configured')}</p>
                            ${this.renderNodeConfigPillsHtml(node)}
                        </div>
                    </div>
                `;

                // Set node icon background safely
                const iconEl = cardEl.querySelector('.flow-node-icon');
                if (iconEl && node.color) {
                    iconEl.style.backgroundColor = node.color;
                }

                this.bindNodeCardEvents(cardEl, node);
                this.canvasNodesLayerEl.appendChild(cardEl);
            });
        }

        bindNodeCardEvents(cardEl, node) {
            // Smooth, responsive node dragging from anywhere on card (except buttons or ports)
            let startClientX = 0;
            let startClientY = 0;
            let nodeStartX = 0;
            let nodeStartY = 0;
            let isDragging = false;

            const onCardPointerDown = (e) => {
                if (e.button !== 0) return;
                // If clicked an action button, quick-add, or port, ignore dragging and stop bubbling
                if (e.target.closest('button, .flow-node-port, .flow-node-quick-add')) {
                    e.stopPropagation();
                    return;
                }

                e.stopPropagation(); // Stop canvas panning

                startClientX = e.clientX;
                startClientY = e.clientY;
                nodeStartX = node.x;
                nodeStartY = node.y;
                isDragging = false;

                const onPointerMove = (moveEvt) => {
                    const dx = (moveEvt.clientX - startClientX) / this.zoomLevel;
                    const dy = (moveEvt.clientY - startClientY) / this.zoomLevel;

                    if (!isDragging && (Math.abs(dx) > 3 || Math.abs(dy) > 3)) {
                        isDragging = true;
                        cardEl.classList.add('is-dragging');
                    }

                    if (isDragging) {
                        node.x = Math.max(20, Math.round(nodeStartX + dx));
                        node.y = Math.max(20, Math.round(nodeStartY + dy));
                        cardEl.style.left = `${node.x}px`;
                        cardEl.style.top = `${node.y}px`;
                        this.renderConnections();
                    }
                };

                const onPointerUp = (upEvt) => {
                    window.removeEventListener('pointermove', onPointerMove);
                    window.removeEventListener('pointerup', onPointerUp);
                    window.removeEventListener('pointercancel', onPointerUp);

                    cardEl.classList.remove('is-dragging');

                    if (isDragging) {
                        this.pushHistoryState();
                        this.markDirty(true);
                    } else {
                        // Clean click: select node and open config drawer
                        this.selectNode(node.id);
                        this.openNodeDrawer(node.id);
                    }
                };

                window.addEventListener('pointermove', onPointerMove);
                window.addEventListener('pointerup', onPointerUp);
                window.addEventListener('pointercancel', onPointerUp);
            };

            cardEl.addEventListener('pointerdown', onCardPointerDown);

            // Double click opens drawer and focuses title for fast rename
            cardEl.addEventListener('dblclick', (e) => {
                e.stopPropagation();
                this.selectNode(node.id);
                this.openNodeDrawer(node.id);
                setTimeout(() => {
                    if (this.drawerInputTitleEl) {
                        this.drawerInputTitleEl.focus();
                        this.drawerInputTitleEl.select();
                    }
                }, 100);
            });

            // Button actions inside card
            const btnDuplicate = cardEl.querySelector('.btn-node-duplicate');
            if (btnDuplicate) {
                btnDuplicate.addEventListener('pointerdown', (e) => e.stopPropagation());
                btnDuplicate.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.duplicateNode(node.id);
                });
            }

            const btnEdit = cardEl.querySelector('.btn-node-edit, .btn-node-config');
            if (btnEdit) {
                btnEdit.addEventListener('pointerdown', (e) => e.stopPropagation());
                btnEdit.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.selectNode(node.id);
                    this.openNodeDrawer(node.id);
                });
            }

            const btnDelete = cardEl.querySelector('.btn-node-delete');
            if (btnDelete) {
                btnDelete.addEventListener('pointerdown', (e) => e.stopPropagation());
                btnDelete.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.deleteNode(node.id);
                });
            }

            const btnQuickAdd = cardEl.querySelector('.flow-node-quick-add');
            if (btnQuickAdd) {
                btnQuickAdd.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const rect = btnQuickAdd.getBoundingClientRect();
                    this.openQuickAddPopover(node.id, rect.right + 10, rect.top - 20);
                });
            }

            // Output Ports: Both Click-to-Connect and Drag-to-Connect
            cardEl.querySelectorAll('.flow-node-port.port-output').forEach(port => {
                const handleOutputPortStart = (e) => {
                    e.stopPropagation();
                    if (e.button !== 0) return;

                    // If already connecting from this same port, toggle cancel
                    if (this.activePortDrag && this.activePortDrag.sourceNodeId === node.id && this.activePortDrag.portType === (port.dataset.port || 'out')) {
                        this.cancelDraftConnection();
                        return;
                    }

                    this.startDraftConnection(node.id, port.dataset.port || 'out', port, e.clientX, e.clientY);
                };

                port.addEventListener('pointerdown', handleOutputPortStart);
            });

            // Input Port: Click/Drop connection target
            const inPort = cardEl.querySelector('.flow-node-port.port-input');
            if (inPort) {
                const handleInputPortFinish = (e) => {
                    if (this.activePortDrag) {
                        e.stopPropagation();
                        this.finishDraftConnection(node.id);
                    }
                };
                inPort.addEventListener('pointerup', handleInputPortFinish);
                inPort.addEventListener('click', handleInputPortFinish);
            }
        }

        /* ==========================================================================
           SVG Dynamic Bezier Curves Connecting Nodes
           ========================================================================== */
        selectConnection(connId) {
            this.selectedConnectionId = connId;
            if (this.canvasNodesLayerEl) {
                this.canvasNodesLayerEl.querySelectorAll('.flow-node-card.is-selected').forEach(c => c.classList.remove('is-selected'));
            }
            if (this.svgConnectionsGroupEl) {
                this.svgConnectionsGroupEl.querySelectorAll('.flow-connection-group').forEach(g => {
                    const isSel = (g.dataset.connId === connId);
                    g.classList.toggle('is-selected', isSel);
                    const path = g.querySelector('.flow-connector-path');
                    if (path) {
                        path.classList.toggle('is-selected', isSel);
                        path.setAttribute('marker-end', isSel ? 'url(#flowArrowActive)' : 'url(#flowArrow)');
                    }
                });
            }
        }

        deleteConnection(connId) {
            const initialCount = this.connections.length;
            this.connections = this.connections.filter(c => c.id !== connId);
            if (this.selectedConnectionId === connId) {
                this.selectedConnectionId = null;
            }
            if (this.connections.length !== initialCount) {
                this.pushHistoryState();
                this.markDirty(true);
                this.renderConnections();
                this.showToast('Connection removed', 'info');
            }
        }

        deleteSelectedConnection() {
            if (this.selectedConnectionId) {
                this.deleteConnection(this.selectedConnectionId);
            }
        }

        renderConnections() {
            if (!this.svgConnectionsGroupEl) return;
            this.svgConnectionsGroupEl.innerHTML = '';

            const validConnections = [];

            this.connections.forEach((conn, index) => {
                const fromNode = this.nodes.find(n => n.id === conn.from);
                const toNode = this.nodes.find(n => n.id === conn.to);

                if (!fromNode || !toNode) return;
                validConnections.push(conn);

                const fromCardEl = document.getElementById(`flowNode_${fromNode.id}`);
                const toCardEl = document.getElementById(`flowNode_${toNode.id}`);
                const fromWidth = fromCardEl ? fromCardEl.offsetWidth : 260;
                const fromHeight = fromCardEl ? fromCardEl.offsetHeight : 110;
                const toHeight = toCardEl ? toCardEl.offsetHeight : 110;

                const startX = fromNode.x + fromWidth;
                let startY = fromNode.y + (fromHeight / 2);

                if (conn.port === 'yes') {
                    startY = fromNode.y + (fromHeight * 0.3);
                } else if (conn.port === 'no') {
                    startY = fromNode.y + (fromHeight * 0.7);
                }

                const endX = toNode.x;
                const endY = toNode.y + (toHeight / 2);

                const dx = Math.max(60, Math.abs(endX - startX) * 0.5);
                const cp1x = startX + dx;
                const cp1y = startY;
                const cp2x = endX - dx;
                const cp2y = endY;

                const pathData = `M ${startX} ${startY} C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${endX} ${endY}`;
                const isSelected = (this.selectedConnectionId === conn.id);

                const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                g.setAttribute('class', `flow-connection-group ${isSelected ? 'is-selected' : ''}`);
                g.setAttribute('data-conn-id', conn.id || `conn_${index}`);

                // Hitbox for easy clicking/hovering
                const hitPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                hitPath.setAttribute('d', pathData);
                hitPath.setAttribute('class', 'flow-connector-hitbox');

                // Visible Bezier Curve
                const pathEl = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                pathEl.setAttribute('d', pathData);
                pathEl.setAttribute('class', `flow-connector-path ${isSelected ? 'is-selected' : ''}`);
                pathEl.setAttribute('id', `connPath_${conn.id || index}`);
                pathEl.setAttribute('marker-end', isSelected ? 'url(#flowArrowActive)' : 'url(#flowArrow)');

                hitPath.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.selectConnection(conn.id);
                });

                pathEl.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.selectConnection(conn.id);
                });

                g.appendChild(hitPath);
                g.appendChild(pathEl);

                // Midpoint calculation for label / delete button
                const midX = (startX + endX) / 2;
                const midY = (startY + endY) / 2;

                const badgeG = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                badgeG.setAttribute('class', 'flow-connector-badge-group');
                badgeG.setAttribute('transform', `translate(${midX}, ${midY})`);

                let badgeText = conn.label || '';
                if (!badgeText && conn.port === 'yes') badgeText = 'If Yes';
                if (!badgeText && conn.port === 'no') badgeText = 'If No';

                if (badgeText && (conn.port === 'yes' || conn.port === 'no')) {
                    const badgeWidth = Math.max(54, badgeText.length * 7 + 18);
                    badgeG.innerHTML = `
                        <rect class="connector-label-bg ${conn.port}" x="${-badgeWidth / 2}" y="-11" width="${badgeWidth}" height="22" rx="11" ry="11"></rect>
                        <text class="connector-label-text" x="0" y="1" text-anchor="middle">${this.escapeHtml(badgeText)}</text>
                        <circle class="connector-delete-btn" cx="${badgeWidth / 2 - 4}" cy="-8" r="8"></circle>
                        <text class="connector-delete-icon" x="${badgeWidth / 2 - 4}" y="-7" text-anchor="middle">×</text>
                    `;
                } else {
                    badgeG.innerHTML = `
                        <circle class="connector-delete-circle" cx="0" cy="0" r="10"></circle>
                        <text class="connector-delete-cross" x="0" y="1" text-anchor="middle">×</text>
                    `;
                }

                badgeG.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.deleteConnection(conn.id);
                });

                g.appendChild(badgeG);
                this.svgConnectionsGroupEl.appendChild(g);
            });

            this.connections = validConnections;
        }

        startDraftConnection(sourceNodeId, portType, portEl, clientX, clientY) {
            this.cancelDraftConnection(); // Clean previous if any

            this.activePortDrag = {
                sourceNodeId: sourceNodeId,
                portType: portType,
                portEl: portEl,
                startX: clientX,
                startY: clientY
            };

            // Highlight source port
            portEl.classList.add('is-connecting-source');

            // Highlight candidate input ports on other nodes
            if (this.canvasNodesLayerEl) {
                this.canvasNodesLayerEl.querySelectorAll('.flow-node-card').forEach(card => {
                    if (card.dataset.nodeId !== sourceNodeId) {
                        const inPort = card.querySelector('.flow-node-port.port-input');
                        if (inPort) inPort.classList.add('is-candidate');
                    }
                });
            }

            this.updateDraftConnection(clientX, clientY);

            // Listen for window pointerup to complete drag-to-connect via hit-testing
            const onWindowRelease = (upEvt) => {
                window.removeEventListener('pointerup', onWindowRelease);
                if (!this.activePortDrag) return;

                const startX = this.activePortDrag.startX || upEvt.clientX;
                const startY = this.activePortDrag.startY || upEvt.clientY;
                const dragDistance = Math.hypot(upEvt.clientX - startX, upEvt.clientY - startY);

                // Hit test element under release point
                const hitElem = document.elementFromPoint(upEvt.clientX, upEvt.clientY);
                if (hitElem) {
                    const targetInPort = hitElem.closest('.flow-node-port.port-input');
                    const targetCard = hitElem.closest('.flow-node-card');
                    const targetId = targetInPort ? targetInPort.dataset.nodeId : (targetCard ? targetCard.dataset.nodeId : null);

                    if (targetId && targetId !== this.activePortDrag.sourceNodeId) {
                        this.finishDraftConnection(targetId);
                        return;
                    }
                }

                // If user dragged out to empty canvas space and released
                if (dragDistance > 22) {
                    const srcId = this.activePortDrag.sourceNodeId;
                    const pType = this.activePortDrag.portType;
                    this.cancelDraftConnection();

                    let dropCanvasX = 120;
                    let dropCanvasY = 160;
                    if (this.canvasContainerEl) {
                        const rect = this.canvasContainerEl.getBoundingClientRect();
                        dropCanvasX = Math.round((upEvt.clientX - rect.left - this.panX) / this.zoomLevel);
                        dropCanvasY = Math.round((upEvt.clientY - rect.top - this.panY) / this.zoomLevel);
                    }

                    this.openQuickAddPopover(srcId, upEvt.clientX + 10, upEvt.clientY - 20, pType, dropCanvasX, dropCanvasY);
                    return;
                }
            };

            window.addEventListener('pointerup', onWindowRelease);
        }

        updateDraftConnection(clientX, clientY) {
            if (!this.activePortDrag || !this.svgDraftConnectionPathEl) return;
            const sourceNode = this.nodes.find(n => n.id === this.activePortDrag.sourceNodeId);
            if (!sourceNode) return;

            const cardEl = document.getElementById(`flowNode_${sourceNode.id}`);
            const nodeWidth = cardEl ? cardEl.offsetWidth : 260;
            const nodeHeight = cardEl ? cardEl.offsetHeight : 110;
            const startX = sourceNode.x + nodeWidth;
            let startY = sourceNode.y + (nodeHeight / 2);
            if (this.activePortDrag.portType === 'yes') startY = sourceNode.y + (nodeHeight * 0.3);
            if (this.activePortDrag.portType === 'no') startY = sourceNode.y + (nodeHeight * 0.7);

            const containerRect = this.canvasContainerEl.getBoundingClientRect();
            const mouseCanvasX = (clientX - containerRect.left - this.panX) / this.zoomLevel;
            const mouseCanvasY = (clientY - containerRect.top - this.panY) / this.zoomLevel;

            const dx = Math.max(40, Math.abs(mouseCanvasX - startX) * 0.5);
            const pathData = `M ${startX} ${startY} C ${startX + dx} ${startY}, ${mouseCanvasX - dx} ${mouseCanvasY}, ${mouseCanvasX} ${mouseCanvasY}`;
            this.svgDraftConnectionPathEl.setAttribute('d', pathData);
        }

        finishDraftConnection(targetNodeId) {
            if (!this.activePortDrag) return;
            const sourceId = this.activePortDrag.sourceNodeId;
            const portType = this.activePortDrag.portType || 'out';

            if (sourceId !== targetNodeId) {
                const exists = this.connections.some(c => c.from === sourceId && c.to === targetNodeId && (c.port || 'out') === portType);
                if (!exists) {
                    let label = 'Next Step';
                    if (portType === 'yes') label = 'If Yes';
                    if (portType === 'no') label = 'If No';

                    this.connections.push({
                        id: `conn_${sourceId}_${targetNodeId}_${portType}_${Date.now()}`,
                        from: sourceId,
                        to: targetNodeId,
                        port: portType,
                        label: label
                    });
                    this.pushHistoryState();
                    this.markDirty(true);
                    this.showToast('Steps connected!', 'success');
                }
            }

            this.cancelDraftConnection();
            this.renderConnections();
        }

        cancelDraftConnection() {
            if (this.activePortDrag && this.activePortDrag.portEl) {
                this.activePortDrag.portEl.classList.remove('is-connecting-source');
            }
            this.activePortDrag = null;

            if (this.canvasNodesLayerEl) {
                this.canvasNodesLayerEl.querySelectorAll('.flow-node-port.is-candidate').forEach(p => {
                    p.classList.remove('is-candidate');
                });
            }

            if (this.svgDraftConnectionPathEl) {
                this.svgDraftConnectionPathEl.setAttribute('d', '');
            }
        }

        renderNodeConfigPillsHtml(node) {
            const cfg = node.config || {};
            const type = node.action_type || node.trigger_event || '';
            const pills = [];

            if (type === 'lead_created') {
                if (cfg.source && cfg.source !== 'all') {
                    pills.push({ icon: 'fi-rr-bullseye', text: cfg.source.replace(/^webform_|^fbform_/, '') });
                }
                if (cfg.min_budget && cfg.min_budget !== '0') {
                    pills.push({ icon: 'fi-rr-badge-percent', text: `₹${parseInt(cfg.min_budget).toLocaleString('en-IN')}+` });
                }
            } else if (type === 'whatsapp_incoming') {
                if (cfg.keywords) {
                    const firstKw = cfg.keywords.split(',')[0].trim();
                    pills.push({ icon: 'fi-rr-search', text: `"${firstKw}"` });
                }
            } else if (type === 'payment_received') {
                if (cfg.min_amount) {
                    pills.push({ icon: 'fi-rr-bank', text: `Min ₹${parseInt(cfg.min_amount).toLocaleString('en-IN')}` });
                }
            } else if (type === 'lead_stage_changed') {
                if (cfg.to_stage && cfg.to_stage !== 'any') {
                    pills.push({ icon: 'fi-rr-exchange', text: `Stage: ${cfg.to_stage}` });
                }
            } else if (type === 'send_whatsapp') {
                if (cfg.template_id && cfg.template_id !== '0') {
                    const tpl = this.whatsappTemplatesList.find(t => String(t.id) === String(cfg.template_id));
                    pills.push({ icon: 'fi-rr-document-signed', text: tpl ? tpl.name : 'Template' });
                }
                if (cfg.attach_brochure !== '0') {
                    pills.push({ icon: 'fi-rr-file-pdf', text: 'PDF Brochure' });
                }
            } else if (type === 'send_itinerary') {
                if (cfg.package_id && cfg.package_id !== '0') {
                    const pkg = this.packagesList.find(p => String(p.id) === String(cfg.package_id));
                    pills.push({ icon: 'fi-rr-plane', text: pkg ? pkg.name.substring(0, 16) + '...' : 'Package' });
                } else {
                    pills.push({ icon: 'fi-rr-magic-wand', text: 'Auto Package' });
                }
            } else if (type === 'assign_agent') {
                if (cfg.mode === 'round_robin' || !cfg.assigned_agent_id || cfg.assigned_agent_id === '0') {
                    pills.push({ icon: 'fi-rr-refresh', text: 'Round-Robin' });
                } else {
                    const agent = this.agentsList.find(a => String(a.id) === String(cfg.assigned_agent_id));
                    pills.push({ icon: 'fi-rr-user', text: agent ? agent.name : 'Specialist' });
                }
            } else if (type === 'update_lead_stage') {
                if (cfg.stage_code) {
                    pills.push({ icon: 'fi-rr-checkbox', text: cfg.stage_code });
                }
            } else if (type === 'add_tags') {
                if (cfg.tags) {
                    const firstTag = cfg.tags.split(',')[0].trim();
                    pills.push({ icon: 'fi-rr-tag', text: `#${firstTag}` });
                }
            } else if (type === 'create_task') {
                pills.push({ icon: 'fi-rr-alarm-clock', text: `${cfg.task_type || 'Call'} in ${cfg.due_hours || 2}h` });
            } else if (type === 'condition_branch') {
                pills.push({ icon: 'fi-rr-code-branch', text: `${cfg.field || 'budget'} ${cfg.operator || '='} ${cfg.value || ''}` });
            } else if (type === 'delay') {
                const sec = parseInt(cfg.delay_seconds || 3);
                const label = sec >= 3600 ? `${Math.round(sec / 3600)}h` : (sec >= 60 ? `${Math.round(sec / 60)}m` : `${sec}s`);
                pills.push({ icon: 'fi-rr-hourglass', text: `Wait ${label}` });
            } else if (type === 'payment_link') {
                pills.push({ icon: 'fi-rr-credit-card', text: `₹${parseInt(cfg.amount || 5000).toLocaleString('en-IN')}` });
            } else if (type === 'internal_note') {
                pills.push({ icon: 'fi-rr-document', text: 'Internal Note' });
            } else if (type === 'itinerary_viewed') {
                pills.push({ icon: 'fi-rr-eye', text: `${cfg.min_view_seconds || 15}s view` });
            } else if (type === 'webhook_call') {
                pills.push({ icon: 'fi-rr-cloud-share', text: cfg.method || 'POST' });
            } else if (type === 'webhook_received') {
                pills.push({ icon: 'fi-rr-network', text: cfg.event_name || 'Webhook' });
            }

            if (pills.length === 0) return '';
            return `<div class="flow-node-config-pills">${pills.map(p => `<span class="flow-config-pill"><i class="fi ${p.icon}"></i> ${this.escapeHtml(p.text)}</span>`).join('')}</div>`;
        }

        duplicateNode(nodeId) {
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return null;

            const newId = 'node_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const newNode = {
                ...JSON.parse(JSON.stringify(node)),
                id: newId,
                title: `${node.title} (Copy)`,
                x: node.x + 40,
                y: node.y + 40
            };

            this.nodes.push(newNode);
            this.pushHistoryState();
            this.markDirty(true);
            this.renderCanvas();
            this.selectNode(newId);
            this.openNodeDrawer(newId);
            this.showToast(`Duplicated "${node.title}"`, 'success');
            return newNode;
        }

        populateDrawerModuleSelect(node) {
            if (!this.drawerModuleTypeSelectEl) return;
            const $ = window.jQuery || window.$;
            if ($ && $(this.drawerModuleTypeSelectEl).data('select2')) {
                try {
                    $(this.drawerModuleTypeSelectEl).select2('destroy');
                } catch (e) {}
            }
            const catalog = this.getModuleCatalog();
            const currentType = node.action_type || node.trigger_event || node.type || '';

            let html = '';
            const categories = [
                { key: 'triggers', label: 'Triggers (Flow Starters)' },
                { key: 'actions', label: 'Actions (Automated Steps)' },
                { key: 'logic', label: 'Logic & Control Flow' }
            ];

            categories.forEach(cat => {
                const items = catalog[cat.key] || [];
                if (items.length > 0) {
                    html += `<optgroup label="${cat.label}">`;
                    items.forEach(item => {
                        const modType = item.type || item.id;
                        const isSelected = (modType === currentType) ? 'selected' : '';
                        html += `<option value="${modType}" ${isSelected}>${this.escapeHtml(item.title)}</option>`;
                    });
                    html += `</optgroup>`;
                }
            });

            this.drawerModuleTypeSelectEl.innerHTML = html;
            this.drawerModuleTypeSelectEl.value = currentType;
        }

        switchNodeModuleType(nodeId, newType) {
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return;
            const module = this.getModuleCatalogItem(newType);
            if (!module) return;

            const oldCategory = node.category;
            node.type = module.category;
            node.category = module.category;
            node.action_type = module.type;
            node.trigger_event = (module.category === 'trigger') ? module.type : undefined;
            node.title = module.title;
            node.subtitle = module.subtitle;
            node.icon = module.icon;
            node.color = module.color;
            node.config = this.getDefaultConfigForModule(module.type);

            // If switching from logic to action, preserve connections by migrating yes/no ports to out!
            if (oldCategory === 'logic' && module.category !== 'logic') {
                this.connections.forEach(c => {
                    if (c.from === node.id) {
                        c.port = 'out';
                        c.label = 'Next Step';
                    }
                });
            }

            if (this.drawerInputTitleEl) this.drawerInputTitleEl.value = node.title;
            if (this.drawerInputSubtitleEl) this.drawerInputSubtitleEl.value = node.subtitle;
            if (this.drawerNodeTypeBadgeEl) {
                this.drawerNodeTypeBadgeEl.textContent = (node.category || 'action').toUpperCase() + ' CONFIG';
            }
            const iconEl = document.getElementById('drawerNodeIcon');
            if (iconEl) {
                iconEl.innerHTML = `<i class="${node.icon || 'fi fi-rr-settings'}"></i>`;
                iconEl.style.color = node.color || '#7c3aed';
            }

            this.renderDynamicFieldsForNode(node);
            this.pushHistoryState();
            this.markDirty(true);
            this.renderCanvas();
            this.showToast(`Switched step to "${module.title}"`, 'success');
        }

        quickAddNextStep(sourceNodeId, clientX = null, clientY = null) {
            const sourceNode = this.nodes.find(n => n.id === sourceNodeId);
            if (!sourceNode) return;
            if (clientX === null || clientY === null) {
                const cardEl = document.getElementById(`flowNode_${sourceNodeId}`);
                if (cardEl) {
                    const rect = cardEl.getBoundingClientRect();
                    clientX = rect.right + 12;
                    clientY = rect.top + 20;
                }
            }
            this.openQuickAddPopover(sourceNodeId, clientX, clientY, 'out');
        }

        openQuickAddPopover(sourceNodeId = null, clientX = null, clientY = null, sourcePort = 'out', targetCanvasX = null, targetCanvasY = null) {
            if (!this.quickAddPopoverEl) return;
            this.activeQuickAddSourceNodeId = sourceNodeId;
            this.activeQuickAddSourcePort = sourcePort || 'out';
            this.activeQuickAddTargetCanvasX = targetCanvasX;
            this.activeQuickAddTargetCanvasY = targetCanvasY;
            this.quickAddFilterCategory = 'all';

            if (this.quickAddSearchInputEl) {
                this.quickAddSearchInputEl.value = '';
            }
            if (this.quickAddCategoryTabsEl) {
                this.quickAddCategoryTabsEl.querySelectorAll('.popover-cat-btn, .quick-add-cat-btn').forEach((b, idx) => {
                    b.classList.toggle('active', idx === 0);
                    b.classList.toggle('is-active', idx === 0);
                });
            }

            this.renderQuickAddItems();
            this.quickAddPopoverEl.classList.remove('is-hidden');

            const popoverW = 340;
            const popoverH = 430;
            const winW = window.innerWidth;
            const winH = window.innerHeight;

            let posX = clientX !== null ? clientX : (winW - popoverW) / 2;
            let posY = clientY !== null ? clientY : (winH - popoverH) / 2;

            if (posX + popoverW > winW - 20) {
                posX = winW - popoverW - 20;
            }
            if (posX < 20) posX = 20;

            if (posY + popoverH > winH - 20) {
                posY = winH - popoverH - 20;
            }
            if (posY < 20) posY = 20;

            this.quickAddPopoverEl.style.left = `${posX}px`;
            this.quickAddPopoverEl.style.top = `${posY}px`;

            setTimeout(() => {
                if (this.quickAddSearchInputEl) this.quickAddSearchInputEl.focus();
            }, 60);
        }

        closeQuickAddPopover() {
            if (this.quickAddPopoverEl) {
                this.quickAddPopoverEl.classList.add('is-hidden');
            }
            this.activeQuickAddSourceNodeId = null;
            this.activeQuickAddSourcePort = 'out';
            this.activeQuickAddTargetCanvasX = null;
            this.activeQuickAddTargetCanvasY = null;
        }

        renderQuickAddItems() {
            if (!this.quickAddItemsListEl) return;
            const catalog = this.getModuleCatalog();
            const searchTerm = this.quickAddSearchInputEl ? this.quickAddSearchInputEl.value.trim().toLowerCase() : '';
            const activeCat = this.quickAddFilterCategory || 'all';

            let items = [];
            if (activeCat === 'all') {
                items = [
                    ...(catalog.triggers || []),
                    ...(catalog.actions || []),
                    ...(catalog.logic || [])
                ];
            } else if (activeCat === 'triggers' || activeCat === 'trigger') {
                items = catalog.triggers || [];
            } else if (activeCat === 'actions' || activeCat === 'action') {
                items = catalog.actions || [];
            } else if (activeCat === 'logic') {
                items = catalog.logic || [];
            }

            if (searchTerm) {
                items = items.filter(item => {
                    const text = `${item.title} ${item.subtitle} ${item.type} ${item.category}`.toLowerCase();
                    return text.includes(searchTerm);
                });
            }

            if (items.length === 0) {
                this.quickAddItemsListEl.innerHTML = `
                    <div style="padding: 24px 12px; text-align: center; color: #94a3b8; font-size: 13px;">
                        <i class="fi fi-rr-search" style="font-size: 20px; display: block; margin-bottom: 6px; opacity: 0.6;"></i>
                        No matching steps found
                    </div>
                `;
                return;
            }

            this.quickAddItemsListEl.innerHTML = items.map(item => {
                const modType = item.type || item.id;
                const cat = item.category || 'action';
                const badgeText = item.badge || cat.toUpperCase();
                return `
                    <div class="popover-item quick-add-item" data-module-type="${modType}">
                        <div class="popover-item-icon quick-add-item-icon" style="background: ${item.color || '#7c3aed'};">
                            <i class="${item.icon || 'fi fi-rr-apps'}"></i>
                        </div>
                        <div class="popover-item-info quick-add-item-content">
                            <div class="popover-item-header quick-add-item-header">
                                <span class="popover-item-title quick-add-item-title">${this.escapeHtml(item.title)}</span>
                                <span class="popover-item-badge quick-add-item-badge ${cat}">${this.escapeHtml(badgeText)}</span>
                            </div>
                            <p class="popover-item-desc quick-add-item-sub">${this.escapeHtml(item.subtitle || item.description || '')}</p>
                        </div>
                    </div>
                `;
            }).join('');

            this.quickAddItemsListEl.querySelectorAll('.popover-item, .quick-add-item').forEach(itemEl => {
                itemEl.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const moduleType = itemEl.dataset.moduleType;
                    this.addStepFromQuickAdd(
                        moduleType,
                        this.activeQuickAddSourceNodeId,
                        this.activeQuickAddSourcePort || 'out',
                        this.activeQuickAddTargetCanvasX,
                        this.activeQuickAddTargetCanvasY
                    );
                    this.closeQuickAddPopover();
                });
            });
        }

        addStepFromQuickAdd(moduleType, sourceNodeId = null, sourcePort = 'out', targetX = null, targetY = null) {
            const module = this.getModuleCatalogItem(moduleType);
            if (!module) return;

            let posX = 120;
            let posY = 160;
            let sourceNode = null;

            if (sourceNodeId) {
                sourceNode = this.nodes.find(n => n.id === sourceNodeId);
            }

            if (targetX !== null && targetY !== null) {
                posX = targetX;
                posY = targetY;
            } else if (sourceNode) {
                posX = sourceNode.x + 320;
                posY = sourceNode.y + (sourcePort === 'no' ? 140 : (sourcePort === 'yes' ? -60 : 0));
            } else if (this.canvasContainerEl) {
                const rect = this.canvasContainerEl.getBoundingClientRect();
                posX = Math.round((-this.panX + (rect.width / 2) - 130) / this.zoomLevel);
                posY = Math.round((-this.panY + (rect.height / 2) - 60) / this.zoomLevel);
            }

            const newNode = this.addNodeToCanvas(
                module.category,
                module.type,
                module.title,
                module.subtitle,
                module.icon,
                module.color,
                posX,
                posY
            );

            if (sourceNode && newNode) {
                let label = 'Next Step';
                if (sourcePort === 'yes') label = 'If Yes';
                if (sourcePort === 'no') label = 'If No';

                const connId = `conn_${sourceNode.id}_${newNode.id}_${sourcePort}_${Date.now()}`;
                if (!this.connections.some(c => c.from === sourceNode.id && c.to === newNode.id && (c.port || 'out') === sourcePort)) {
                    this.connections.push({
                        id: connId,
                        from: sourceNode.id,
                        to: newNode.id,
                        port: sourcePort,
                        label: label
                    });
                    this.renderConnections();
                }
            }

            this.pushHistoryState();
            this.markDirty(true);
            this.showToast(`Added ${module.title}`, 'success');
        }

        /* ==========================================================================
           Auto-Align Flowchart (Clean Hierarchy Generator)
           ========================================================================== */
        autoAlignFlowchart() {
            if (this.nodes.length === 0) return;

            const incomingCounts = {};
            this.nodes.forEach(n => incomingCounts[n.id] = 0);
            this.connections.forEach(c => {
                if (incomingCounts[c.to] !== undefined) incomingCounts[c.to]++;
            });

            const roots = this.nodes.filter(n => incomingCounts[n.id] === 0);
            if (roots.length === 0) roots.push(this.nodes[0]);

            const levels = {};
            const visited = new Set();
            const queue = [];

            roots.forEach(r => {
                queue.push({ node: r, depth: 0 });
                visited.add(r.id);
            });

            while (queue.length > 0) {
                const { node, depth } = queue.shift();
                if (!levels[depth]) levels[depth] = [];
                levels[depth].push(node);

                const outConns = this.connections.filter(c => c.from === node.id);
                outConns.forEach(c => {
                    const child = this.nodes.find(n => n.id === c.to);
                    if (child && !visited.has(child.id)) {
                        visited.add(child.id);
                        queue.push({ node: child, depth: depth + 1 });
                    }
                });
            }

            this.nodes.forEach(n => {
                if (!visited.has(n.id)) {
                    if (!levels[0]) levels[0] = [];
                    levels[0].push(n);
                }
            });

            const startX = 80;
            const startY = 120;
            const xSpacing = 320;
            const ySpacing = 160;

            Object.keys(levels).forEach(d => {
                const depth = parseInt(d);
                const nodesAtDepth = levels[depth];
                nodesAtDepth.forEach((n, idx) => {
                    n.x = startX + (depth * xSpacing);
                    n.y = startY + (idx * ySpacing);
                });
            });

            this.panX = 40;
            this.panY = 40;
            this.renderCanvas();
            this.showToast('Flowchart layout auto-aligned neatly!', 'success');
        }

        /* ==========================================================================
           Node Configuration Inspector Drawer
           ========================================================================== */
        selectNode(nodeId) {
            this.selectedNodeId = nodeId;
            if (this.canvasNodesLayerEl) {
                this.canvasNodesLayerEl.querySelectorAll('.flow-node-card').forEach(c => {
                    c.classList.toggle('is-selected', c.dataset.nodeId === nodeId);
                });
            }
        }

        openNodeDrawer(nodeId) {
            const node = typeof nodeId === 'object' && nodeId ? nodeId : this.nodes.find(n => n.id === nodeId);
            if (!node || !this.nodeConfigDrawerEl) return;

            this.selectNode(node.id);

            // Open drawer and bring into view immediately
            this.nodeConfigDrawerEl.classList.remove('is-closed');

            if (this.drawerInputTitleEl) this.drawerInputTitleEl.value = node.title || '';
            if (this.drawerInputSubtitleEl) this.drawerInputSubtitleEl.value = node.subtitle || '';
            if (this.drawerNodeTypeBadgeEl) this.drawerNodeTypeBadgeEl.textContent = (node.category || 'action').toUpperCase() + ' CONFIG';

            const iconEl = document.getElementById('drawerNodeIcon');
            if (iconEl) {
                iconEl.innerHTML = `<i class="${node.icon || 'fi fi-rr-edit'}"></i>`;
                iconEl.style.color = node.color || '#7c3aed';
            }

            try {
                this.populateDrawerModuleSelect(node);
            } catch (err) {
                console.warn('Error populating drawer module select:', err);
            }

            try {
                this.renderDynamicFieldsForNode(node);
            } catch (err) {
                console.warn('Error rendering dynamic fields:', err);
            }
            
            // Scroll drawer to top smoothly and bring options into view
            const drawerBody = this.nodeConfigDrawerEl.querySelector('.drawer-body');
            if (drawerBody) {
                drawerBody.scrollTop = 0;
            }

            setTimeout(() => {
                try {
                    this.initDrawerSelects();
                } catch (err) {
                    console.warn('Error initializing drawer selects:', err);
                }
            }, 260);
        }

        closeNodeDrawer() {
            this.destroyDrawerSelects(false);
            if (this.nodeConfigDrawerEl) {
                this.nodeConfigDrawerEl.classList.add('is-closed');
            }
        }

        renderDynamicFieldsForNode(node) {
            if (!this.drawerDynamicFieldsEl) return;
            this.destroyDrawerSelects(true);
            const cfg = node.config || {};
            const actionType = node.action_type || node.trigger_event || '';
            let html = '';

            if (actionType === 'lead_created') {
                const currentSource = cfg.source || (Array.isArray(cfg.sources) && cfg.sources.length ? cfg.sources[0] : 'all');
                let sourceOptions = `<option value="all" ${currentSource === 'all' ? 'selected' : ''}>All Ingestion Channels (Universal)</option>`;

                if (this.leadSourcesList && this.leadSourcesList.length > 0) {
                    sourceOptions += `<optgroup label="CRM Enquiry Lead Sources">`;
                    this.leadSourcesList.forEach(s => {
                        const isSel = (currentSource === s.id || currentSource === s.name) ? 'selected' : '';
                        sourceOptions += `<option value="${this.escapeHtml(s.id)}" ${isSel}>${this.escapeHtml(s.name)}</option>`;
                    });
                    sourceOptions += `</optgroup>`;
                }

                if (this.webFormsList && this.webFormsList.length > 0) {
                    sourceOptions += `<optgroup label="Website Custom Web Forms">`;
                    this.webFormsList.forEach(f => {
                        const val = 'webform_' + f.id;
                        const isSel = (currentSource === val) ? 'selected' : '';
                        sourceOptions += `<option value="${val}" ${isSel}>Web Form: ${this.escapeHtml(f.title)}</option>`;
                    });
                    sourceOptions += `</optgroup>`;
                }

                if (this.fbFormsList && this.fbFormsList.length > 0) {
                    sourceOptions += `<optgroup label="Facebook Instant Lead Forms">`;
                    this.fbFormsList.forEach(f => {
                        const val = 'fbform_' + f.id;
                        const isSel = (currentSource === val) ? 'selected' : '';
                        sourceOptions += `<option value="${val}" ${isSel}>FB Form: ${this.escapeHtml(f.name)}</option>`;
                    });
                    sourceOptions += `</optgroup>`;
                }

                const minBudget = String(cfg.min_budget || '0');
                const minPax = String(cfg.min_pax || '1');

                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-bullseye-arrow"></i>
                        <div><strong>Lead Ingestion Trigger:</strong> Executes immediately whenever a new traveler lead lands from any connected campaign, web form, or chat.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgTriggerSources">
                            <span>Ingestion Lead Source / Channel</span>
                            <span class="drawer-label-badge">Site Data</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgTriggerSources" data-placeholder="Select Ingestion Channel">
                            ${sourceOptions}
                        </select>
                        <span class="drawer-help-text">Filters which lead origin fires this workflow. Loaded from site's live CRM sources.</span>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Preset:</span>
                            <button type="button" class="drawer-chip ${currentSource === 'all' ? 'active' : ''}" data-target="cfgTriggerSources" data-val="all">All Channels</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgTriggerMinBudget">
                            <span>Minimum Inquired Budget (₹ INR)</span>
                            <span class="drawer-label-badge">Optional Filter</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgTriggerMinBudget" data-placeholder="Filter by Budget">
                            <option value="0" ${minBudget === '0' ? 'selected' : ''}>Any Inquired Budget (No Minimum)</option>
                            <option value="25000" ${minBudget === '25000' ? 'selected' : ''}>₹25,000+ Budget</option>
                            <option value="50000" ${minBudget === '50000' ? 'selected' : ''}>₹50,000+ Budget (Premium)</option>
                            <option value="100000" ${minBudget === '100000' ? 'selected' : ''}>₹1,00,000+ Budget (Luxury / Honeymoon)</option>
                            <option value="200000" ${minBudget === '200000' ? 'selected' : ''}>₹2,00,000+ Budget (HNW / Group Package)</option>
                        </select>
                        <span class="drawer-help-text">Flow will only execute if inquiry budget meets or exceeds threshold.</span>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Quick:</span>
                            <button type="button" class="drawer-chip ${minBudget === '0' ? 'active' : ''}" data-target="cfgTriggerMinBudget" data-val="0">Any Budget</button>
                            <button type="button" class="drawer-chip ${minBudget === '25000' ? 'active' : ''}" data-target="cfgTriggerMinBudget" data-val="25000">₹25K+</button>
                            <button type="button" class="drawer-chip ${minBudget === '50000' ? 'active' : ''}" data-target="cfgTriggerMinBudget" data-val="50000">₹50K+ (Premium)</button>
                            <button type="button" class="drawer-chip ${minBudget === '100000' ? 'active' : ''}" data-target="cfgTriggerMinBudget" data-val="100000">₹1L+ (Luxury)</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgTriggerMinPax">
                            <span>Minimum Group Size (Pax)</span>
                            <span class="drawer-label-badge">Optional Filter</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgTriggerMinPax" data-placeholder="Filter by Group Size">
                            <option value="1" ${minPax === '1' ? 'selected' : ''}>Any Group Size (1+ Pax)</option>
                            <option value="2" ${minPax === '2' ? 'selected' : ''}>Couples / 2+ Pax</option>
                            <option value="4" ${minPax === '4' ? 'selected' : ''}>Family / Small Group (4+ Pax)</option>
                            <option value="10" ${minPax === '10' ? 'selected' : ''}>Corporate / Large Group (10+ Pax)</option>
                        </select>
                        <span class="drawer-help-text">Target couples, family vacations, or corporate group departures.</span>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Quick:</span>
                            <button type="button" class="drawer-chip ${minPax === '1' ? 'active' : ''}" data-target="cfgTriggerMinPax" data-val="1">Any (1+)</button>
                            <button type="button" class="drawer-chip ${minPax === '2' ? 'active' : ''}" data-target="cfgTriggerMinPax" data-val="2">Couples (2)</button>
                            <button type="button" class="drawer-chip ${minPax === '4' ? 'active' : ''}" data-target="cfgTriggerMinPax" data-val="4">Family (4+)</button>
                            <button type="button" class="drawer-chip ${minPax === '10' ? 'active' : ''}" data-target="cfgTriggerMinPax" data-val="10">Group (10+)</button>
                        </div>
                    </div>
                `;
            } else if (actionType === 'whatsapp_incoming') {
                const rule = cfg.trigger_rule || 'keywords';
                const matchType = cfg.match_type || 'contains';
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-comment-dots"></i>
                        <div><strong>Inbound WhatsApp Trigger:</strong> Fires when a traveler messages your WhatsApp business channel. Route by message intent or auto-reply at night.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWaTriggerRule">
                            <span>Incoming Trigger Scenario</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgWaTriggerRule" data-placeholder="Select Trigger Scenario">
                            <option value="keywords" ${rule === 'keywords' ? 'selected' : ''}>Keyword / Phrase Match</option>
                            <option value="any" ${rule === 'any' ? 'selected' : ''}>Any Inbound WhatsApp Message</option>
                            <option value="out_of_hours" ${rule === 'out_of_hours' ? 'selected' : ''}>After Business Hours / Night Bot</option>
                        </select>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Scenario:</span>
                            <button type="button" class="drawer-chip ${rule === 'keywords' ? 'active' : ''}" data-target="cfgWaTriggerRule" data-val="keywords">Keyword Match</button>
                            <button type="button" class="drawer-chip ${rule === 'any' ? 'active' : ''}" data-target="cfgWaTriggerRule" data-val="any">Any Message</button>
                            <button type="button" class="drawer-chip ${rule === 'out_of_hours' ? 'active' : ''}" data-target="cfgWaTriggerRule" data-val="out_of_hours">Night Bot (24/7)</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgKeywords">
                            <span>Trigger Keyword(s)</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgKeywords" value="${this.escapeHtml(cfg.keywords || 'itinerary, package, tour, trip, quote, cost, booking')}" placeholder="e.g. itinerary, package, quote">
                        <span class="drawer-help-text">Comma-separated terms. Click below to append common travel search intents:</span>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip append-chip" data-target="cfgKeywords" data-append="quote">+ quote</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgKeywords" data-append="price">+ price</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgKeywords" data-append="itinerary">+ itinerary</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgKeywords" data-append="package">+ package</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgKeywords" data-append="resort">+ resort</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgKeywords" data-append="booking">+ booking</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgMatchType">
                            <span>Keyword Match Precision</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgMatchType" data-placeholder="Select Match Type">
                            <option value="contains" ${matchType === 'contains' ? 'selected' : ''}>Contains Keyword Anywhere in Message</option>
                            <option value="exact" ${matchType === 'exact' ? 'selected' : ''}>Exact Message Match Only</option>
                        </select>
                    </div>
                `;
            } else if (actionType === 'payment_received') {
                const pType = cfg.payment_type || 'all';
                const pAmount = String(cfg.min_amount || '1');
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-badge-percent"></i>
                        <div><strong>Payment Captured Trigger:</strong> Executes when Razorpay, UPI, PhonePe, or bank payment is confirmed. Trigger vouchers or dispatch receipt messages.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgPaymentType">
                            <span>Payment Milestone</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgPaymentType" data-placeholder="Select Payment Milestone">
                            <option value="all" ${pType === 'all' ? 'selected' : ''}>Any Captured Payment</option>
                            <option value="token" ${pType === 'token' ? 'selected' : ''}>Token / Advance Deposit Only</option>
                            <option value="full" ${pType === 'full' ? 'selected' : ''}>Full Balance Settlement</option>
                        </select>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Quick:</span>
                            <button type="button" class="drawer-chip ${pType === 'all' ? 'active' : ''}" data-target="cfgPaymentType" data-val="all">Any Payment</button>
                            <button type="button" class="drawer-chip ${pType === 'token' ? 'active' : ''}" data-target="cfgPaymentType" data-val="token">Token Deposit</button>
                            <button type="button" class="drawer-chip ${pType === 'full' ? 'active' : ''}" data-target="cfgPaymentType" data-val="full">Full Settlement</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgPaymentMinAmount">
                            <span>Minimum Transaction Amount (₹ INR)</span>
                        </label>
                        <input type="number" class="drawer-input" id="cfgPaymentMinAmount" value="${this.escapeHtml(cfg.min_amount || '1')}" placeholder="e.g. 5000">
                        <span class="drawer-help-text">Only trigger if payment value meets or exceeds this threshold.</span>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Preset:</span>
                            <button type="button" class="drawer-chip ${pAmount === '1' ? 'active' : ''}" data-target="cfgPaymentMinAmount" data-val="1">Any (₹1+)</button>
                            <button type="button" class="drawer-chip ${pAmount === '2500' ? 'active' : ''}" data-target="cfgPaymentMinAmount" data-val="2500">₹2,500</button>
                            <button type="button" class="drawer-chip ${pAmount === '5000' ? 'active' : ''}" data-target="cfgPaymentMinAmount" data-val="5000">₹5,000</button>
                            <button type="button" class="drawer-chip ${pAmount === '10000' ? 'active' : ''}" data-target="cfgPaymentMinAmount" data-val="10000">₹10,000</button>
                            <button type="button" class="drawer-chip ${pAmount === '25000' ? 'active' : ''}" data-target="cfgPaymentMinAmount" data-val="25000">₹25,000</button>
                        </div>
                    </div>
                `;
            } else if (actionType === 'lead_stage_changed') {
                let fromStageOpts = `<option value="any" ${(!cfg.from_stage || cfg.from_stage === 'any') ? 'selected' : ''}>Any Starting Stage</option>`;
                let toStageOpts = `<option value="any" ${(!cfg.to_stage || cfg.to_stage === 'any') ? 'selected' : ''}>Any New Stage</option>`;

                if (this.pipelineStagesList && this.pipelineStagesList.length > 0) {
                    this.pipelineStagesList.forEach(st => {
                        const code = st.code || st.id;
                        const fromSel = (cfg.from_stage === code || cfg.from_stage == st.id) ? 'selected' : '';
                        const toSel = (cfg.to_stage === code || cfg.to_stage == st.id) ? 'selected' : '';
                        const optHtml = `<option value="${this.escapeHtml(code)}"`;
                        fromStageOpts += `${optHtml} ${fromSel}>${this.escapeHtml(st.label)}</option>`;
                        toStageOpts += `${optHtml} ${toSel}>${this.escapeHtml(st.label)}</option>`;
                    });
                }

                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-refresh"></i>
                        <div><strong>Pipeline Stage Movement:</strong> Triggers when lead transitions across your 20-stage travel CRM pipeline. Great for automated follow-ups or operations handoffs.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgFromStage">
                            <span>Previous Pipeline Stage</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgFromStage" data-placeholder="Select Starting Stage">
                            ${fromStageOpts}
                        </select>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgToStage">
                            <span>New Pipeline Stage</span>
                            <span class="drawer-label-badge">Site Stages</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgToStage" data-placeholder="Select New Stage">
                            ${toStageOpts}
                        </select>
                        <span class="drawer-help-text">Populated from site's real 20 CRM travel pipeline stages.</span>
                    </div>
                `;
            } else if (actionType === 'send_whatsapp') {
                let waTplOpts = `<option value="0" ${(!cfg.template_id || cfg.template_id === '0') ? 'selected' : ''}>Custom Message (Write below)</option>`;
                if (this.whatsappTemplatesList && this.whatsappTemplatesList.length > 0) {
                    waTplOpts += `<optgroup label="Site WhatsApp Templates">`;
                    this.whatsappTemplatesList.forEach(t => {
                        const isSel = (cfg.template_id == t.id) ? 'selected' : '';
                        waTplOpts += `<option value="${t.id}" ${isSel}>${this.escapeHtml(t.name)}</option>`;
                    });
                    waTplOpts += `</optgroup>`;
                }
                const attachBrochure = cfg.attach_brochure !== '0';

                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-comment-alt"></i>
                        <div><strong>Send WhatsApp Message:</strong> Dispatches personalized WhatsApp greetings, quotations, or reminders. Click dynamic tokens to personalize with traveler info.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWaTemplateSelect">
                            <span>Choose WhatsApp Template</span>
                            <span class="drawer-label-badge">Site Data</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgWaTemplateSelect" data-placeholder="Select WhatsApp Template">
                            ${waTplOpts}
                        </select>
                        <span class="drawer-help-text">Selecting a pre-approved template auto-fills the message content below.</span>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWaMessage">
                            <span>WhatsApp Message Body</span>
                        </label>
                        <textarea class="drawer-textarea" id="cfgWaMessage" rows="5">${this.escapeHtml(cfg.message_template || '')}</textarea>
                        <div class="tokens-title">Click to Insert Dynamic Tokens:</div>
                        <div class="token-pills-list">
                            <button type="button" class="token-pill" data-token="{name}">+ {name}</button>
                            <button type="button" class="token-pill" data-token="{phone}">+ {phone}</button>
                            <button type="button" class="token-pill" data-token="{destination}">+ {destination}</button>
                            <button type="button" class="token-pill" data-token="{budget}">+ {budget}</button>
                            <button type="button" class="token-pill" data-token="{pax}">+ {pax}</button>
                            <button type="button" class="token-pill" data-token="{agent_name}">+ {agent_name}</button>
                            <button type="button" class="token-pill" data-token="{company}">+ {company}</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgAttachBrochure">
                            <span>Attach Verified Itinerary Brochure?</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgAttachBrochure" data-placeholder="Attach Brochure?">
                            <option value="1" ${attachBrochure ? 'selected' : ''}>Yes, auto-attach destination PDF brochure</option>
                            <option value="0" ${!attachBrochure ? 'selected' : ''}>No, send text only</option>
                        </select>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip ${attachBrochure ? 'active' : ''}" data-target="cfgAttachBrochure" data-val="1">Yes, Attach PDF Brochure</button>
                            <button type="button" class="drawer-chip ${!attachBrochure ? 'active' : ''}" data-target="cfgAttachBrochure" data-val="0">Text Only</button>
                        </div>
                    </div>
                `;
            } else if (actionType === 'send_itinerary') {
                const pkgId = String(cfg.package_id || '0');
                let pkgOptions = `<option value="0" ${pkgId === '0' ? 'selected' : ''}>Auto-Match verified package based on inquiry destination</option>`;
                if (this.packagesList && this.packagesList.length > 0) {
                    pkgOptions += `<optgroup label="Site Tour Packages">`;
                    this.packagesList.forEach(p => {
                        const dur = p.duration ? ` (${this.escapeHtml(p.duration)})` : '';
                        pkgOptions += `<option value="${p.id}" ${pkgId == String(p.id) ? 'selected' : ''}>${this.escapeHtml(p.name)}${dur}</option>`;
                    });
                    pkgOptions += `</optgroup>`;
                }

                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-document-signed"></i>
                        <div><strong>Dispatch Tour Itinerary:</strong> Automatically matches and shares a verified tour package PDF proposal with day-wise itinerary and hotel inclusions.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgPackageId">
                            <span>Select Tour Itinerary Package</span>
                            <span class="drawer-label-badge">Site Data</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgPackageId" data-placeholder="Select Tour Package">${pkgOptions}</select>
                        <span class="drawer-help-text">Pulls verified daily schedule, hotels, inclusions, and PDF brochure.</span>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip ${pkgId === '0' ? 'active' : ''}" data-target="cfgPackageId" data-val="0">Auto-Match by Destination</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgItineraryTitle">
                            <span>Itinerary Proposal Title</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgItineraryTitle" value="${this.escapeHtml(cfg.itinerary_title || 'Tailored Tour Package')}" placeholder="e.g. Manali Romantic Escape">
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgItineraryMsg">
                            <span>Accompanying Message</span>
                        </label>
                        <textarea class="drawer-textarea" id="cfgItineraryMsg" rows="3">${this.escapeHtml(cfg.message_text || '')}</textarea>
                        <div class="tokens-title">Dynamic Tokens:</div>
                        <div class="token-pills-list">
                            <button type="button" class="token-pill" data-token="{name}">+ {name}</button>
                            <button type="button" class="token-pill" data-token="{destination}">+ {destination}</button>
                            <button type="button" class="token-pill" data-token="{itinerary_link}">+ {itinerary_link}</button>
                        </div>
                    </div>
                `;
            } else if (actionType === 'assign_agent') {
                const currentMode = cfg.mode || 'round_robin';
                let agentOptions = `<option value="round_robin" ${currentMode === 'round_robin' ? 'selected' : ''}>Round-Robin: Distribute equally across active team</option>`;
                if (this.agentsList && this.agentsList.length > 0) {
                    agentOptions += `<optgroup label="Site Sales Team Specialists">`;
                    this.agentsList.forEach(a => {
                        const isSel = (currentMode === 'specific_agent' && (cfg.assigned_agent_id == a.id)) ? 'selected' : '';
                        agentOptions += `<option value="${a.id}" ${isSel}>${this.escapeHtml(a.name)} (${this.escapeHtml(a.role || 'Agent')})</option>`;
                    });
                    agentOptions += `</optgroup>`;
                }
                const fallbackMode = cfg.fallback_mode || 'round_robin';

                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-users-alt"></i>
                        <div><strong>Agent Lead Routing:</strong> Automatically allots lead ownership in the CRM and triggers desktop/sound notifications to the consultant.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgAgentMode">
                            <span>Lead Distribution Mode</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgAgentMode" data-placeholder="Select Distribution Mode">${agentOptions}</select>
                        <span class="drawer-help-text">Distribute equally among consultants or route to a dedicated destination expert.</span>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Strategy:</span>
                            <button type="button" class="drawer-chip ${currentMode === 'round_robin' ? 'active' : ''}" data-target="cfgAgentMode" data-val="round_robin">Round-Robin (Equal Split)</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgAgentFallback">
                            <span>Fallback if Specialist is Away / Inactive</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgAgentFallback" data-placeholder="Select Fallback Mode">
                            <option value="round_robin" ${fallbackMode === 'round_robin' ? 'selected' : ''}>Re-route to next active team member</option>
                            <option value="wait" ${fallbackMode === 'wait' ? 'selected' : ''}>Keep assigned to specialist (Do not re-route)</option>
                        </select>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip ${fallbackMode === 'round_robin' ? 'active' : ''}" data-target="cfgAgentFallback" data-val="round_robin">Re-route to Next Active</button>
                            <button type="button" class="drawer-chip ${fallbackMode === 'wait' ? 'active' : ''}" data-target="cfgAgentFallback" data-val="wait">Wait for Specialist</button>
                        </div>
                    </div>
                `;
            } else if (actionType === 'update_lead_stage') {
                let stageOptions = '';
                const currentStageCode = cfg.stage_code || cfg.stage_id || '';
                if (this.pipelineStagesList && this.pipelineStagesList.length > 0) {
                    this.pipelineStagesList.forEach(st => {
                        const code = st.code || st.id;
                        const isSel = (currentStageCode === code || currentStageCode == st.id) ? 'selected' : '';
                        stageOptions += `<option value="${this.escapeHtml(code)}" ${isSel}>${this.escapeHtml(st.label)} [${this.escapeHtml(st.stage || 'CRM')}]</option>`;
                    });
                }

                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-refresh"></i>
                        <div><strong>Update Sales Pipeline Stage:</strong> Moves the traveler to a new milestone stage in CRM and records an automatic audit status note.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgTargetStage">
                            <span>Target CRM Pipeline Stage</span>
                            <span class="drawer-label-badge">Site Stages</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgTargetStage" data-placeholder="Select CRM Stage">
                            ${stageOptions}
                        </select>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Quick:</span>
                            <button type="button" class="drawer-chip ${currentStageCode === 'contacted' ? 'active' : ''}" data-target="cfgTargetStage" data-val="contacted">Contacted</button>
                            <button type="button" class="drawer-chip ${currentStageCode === 'itinerary_sent' ? 'active' : ''}" data-target="cfgTargetStage" data-val="itinerary_sent">Itinerary Sent</button>
                            <button type="button" class="drawer-chip ${currentStageCode === 'converted_to_booking' ? 'active' : ''}" data-target="cfgTargetStage" data-val="converted_to_booking">Converted to Booking</button>
                            <button type="button" class="drawer-chip ${currentStageCode === 'followup' ? 'active' : ''}" data-target="cfgTargetStage" data-val="followup">Follow Up</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgStageNote">
                            <span>Audit / Timeline Status Note</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgStageNote" value="${this.escapeHtml(cfg.status_note || 'Moved via automation workflow')}" placeholder="e.g. Itinerary sent via WhatsApp">
                    </div>
                `;
            } else if (actionType === 'add_tags') {
                const tagAction = cfg.action || 'add';
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-tags"></i>
                        <div><strong>CRM Lead Tags:</strong> Organizes, segments, and labels inquiries for fast filtering, retargeting, and sales priority.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgTagsAction">
                            <span>Tag Operation</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgTagsAction" data-placeholder="Select Operation">
                            <option value="add" ${tagAction === 'add' ? 'selected' : ''}>Add Tags to Traveler Record</option>
                            <option value="remove" ${tagAction === 'remove' ? 'selected' : ''}>Remove Tags from Traveler Record</option>
                        </select>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip ${tagAction === 'add' ? 'active' : ''}" data-target="cfgTagsAction" data-val="add">Add Tags</button>
                            <button type="button" class="drawer-chip ${tagAction === 'remove' ? 'active' : ''}" data-target="cfgTagsAction" data-val="remove">Remove Tags</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgTags">
                            <span>CRM Tags (Comma Separated)</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgTags" value="${this.escapeHtml(cfg.tags || 'new_lead, high_intent')}" placeholder="e.g. vip, honeymoontrip, immediate_call">
                        <span class="drawer-help-text">Click travel tags below to quickly add them:</span>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip append-chip" data-target="cfgTags" data-append="vip">+ VIP Client</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgTags" data-append="honeymoon">+ Honeymoon</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgTags" data-append="high_budget">+ High-Budget</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgTags" data-append="family_holiday">+ Family</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgTags" data-append="corporate">+ Corporate</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgTags" data-append="urgent">+ Urgent</button>
                        </div>
                    </div>
                `;
            } else if (actionType === 'create_task') {
                const taskType = cfg.task_type || 'Call';
                const dueHours = String(cfg.due_hours || '2');
                let taskAgentOptions = `<option value="0" ${(!cfg.assigned_agent_id || cfg.assigned_agent_id === '0') ? 'selected' : ''}>Current Lead Specialist (Owner)</option>`;
                if (this.agentsList && this.agentsList.length > 0) {
                    taskAgentOptions += `<optgroup label="Specific Team Member">`;
                    this.agentsList.forEach(a => {
                        taskAgentOptions += `<option value="${a.id}" ${cfg.assigned_agent_id == a.id ? 'selected' : ''}>${this.escapeHtml(a.name)} (${this.escapeHtml(a.role || 'Agent')})</option>`;
                    });
                    taskAgentOptions += `</optgroup>`;
                }

                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-calendar-clock"></i>
                        <div><strong>Schedule CRM Task:</strong> Creates a follow-up call, quotation due, or reminder on the consultant's dashboard.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgTaskType">
                            <span>Task Type</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgTaskType" data-placeholder="Select Task Type">
                            <option value="Call" ${taskType === 'Call' ? 'selected' : ''}>Discovery Phone Call</option>
                            <option value="WhatsApp" ${taskType === 'WhatsApp' ? 'selected' : ''}>WhatsApp Follow-up</option>
                            <option value="Quote" ${taskType === 'Quote' ? 'selected' : ''}>Prepare Quotation &amp; Rates</option>
                            <option value="Meeting" ${taskType === 'Meeting' ? 'selected' : ''}>Video / In-person Consultation</option>
                        </select>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Type:</span>
                            <button type="button" class="drawer-chip ${taskType === 'Call' ? 'active' : ''}" data-target="cfgTaskType" data-val="Call">Discovery Call</button>
                            <button type="button" class="drawer-chip ${taskType === 'WhatsApp' ? 'active' : ''}" data-target="cfgTaskType" data-val="WhatsApp">WhatsApp</button>
                            <button type="button" class="drawer-chip ${taskType === 'Quote' ? 'active' : ''}" data-target="cfgTaskType" data-val="Quote">Send Quote</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgDueHours">
                            <span>Due Time Frame</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgDueHours" data-placeholder="Select Due Time Frame">
                            <option value="1" ${dueHours === '1' ? 'selected' : ''}>Due in 1 Hour (Urgent Hot Lead)</option>
                            <option value="2" ${dueHours === '2' ? 'selected' : ''}>Due in 2 Hours</option>
                            <option value="4" ${dueHours === '4' ? 'selected' : ''}>Due in 4 Hours</option>
                            <option value="24" ${dueHours === '24' ? 'selected' : ''}>Due in 24 Hours</option>
                            <option value="48" ${dueHours === '48' ? 'selected' : ''}>Due in 48 Hours</option>
                        </select>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Due In:</span>
                            <button type="button" class="drawer-chip ${dueHours === '1' ? 'active' : ''}" data-target="cfgDueHours" data-val="1">1 Hour</button>
                            <button type="button" class="drawer-chip ${dueHours === '2' ? 'active' : ''}" data-target="cfgDueHours" data-val="2">2 Hours</button>
                            <button type="button" class="drawer-chip ${dueHours === '4' ? 'active' : ''}" data-target="cfgDueHours" data-val="4">4 Hours</button>
                            <button type="button" class="drawer-chip ${dueHours === '24' ? 'active' : ''}" data-target="cfgDueHours" data-val="24">24 Hours</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgTaskAgent">
                            <span>Assign Task To</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgTaskAgent" data-placeholder="Select Assignee">
                            ${taskAgentOptions}
                        </select>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgTaskNote">
                            <span>Task Instructions / Note</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgTaskNote" value="${this.escapeHtml(cfg.task_note || 'Call lead to qualify travel dates, hotel preferences, and group size')}">
                    </div>
                `;
            } else if (actionType === 'condition_branch') {
                const condField = cfg.field || 'destination';
                const condOp = cfg.operator || 'contains';
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-code-fork"></i>
                        <div><strong>Decision Branching:</strong> Routes leads along separate flowchart paths based on inquiry conditions (e.g. high budget, destination, or source).</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgCondField">
                            <span>Data Field to Evaluate</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgCondField" data-placeholder="Select Data Field">
                            <option value="destination" ${condField === 'destination' ? 'selected' : ''}>Holiday Destination</option>
                            <option value="source" ${condField === 'source' ? 'selected' : ''}>Lead Source</option>
                            <option value="budget" ${condField === 'budget' ? 'selected' : ''}>Package Budget (₹ INR)</option>
                            <option value="pax" ${condField === 'pax' ? 'selected' : ''}>Traveler Pax Count</option>
                            <option value="message_content" ${condField === 'message_content' ? 'selected' : ''}>Incoming Message Text</option>
                        </select>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Field:</span>
                            <button type="button" class="drawer-chip ${condField === 'destination' ? 'active' : ''}" data-target="cfgCondField" data-val="destination">Destination</button>
                            <button type="button" class="drawer-chip ${condField === 'budget' ? 'active' : ''}" data-target="cfgCondField" data-val="budget">Budget</button>
                            <button type="button" class="drawer-chip ${condField === 'pax' ? 'active' : ''}" data-target="cfgCondField" data-val="pax">Pax</button>
                            <button type="button" class="drawer-chip ${condField === 'source' ? 'active' : ''}" data-target="cfgCondField" data-val="source">Source</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgCondOp">
                            <span>Operator</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgCondOp" data-placeholder="Select Operator">
                            <option value="contains" ${condOp === 'contains' ? 'selected' : ''}>Contains keywords / phrase</option>
                            <option value="equals" ${condOp === 'equals' ? 'selected' : ''}>Is exactly equal to</option>
                            <option value="gt" ${condOp === 'gt' ? 'selected' : ''}>Is greater than (&gt;)</option>
                            <option value="lt" ${condOp === 'lt' ? 'selected' : ''}>Is less than (&lt;)</option>
                        </select>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgCondValue">
                            <span>Value to Match</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgCondValue" value="${this.escapeHtml(cfg.value || '')}" placeholder="e.g. manali, honeymoon, 50000">
                        <span class="drawer-help-text">Click destination shortcuts to match popular traveler inquiries:</span>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip append-chip" data-target="cfgCondValue" data-append="Dubai">+ Dubai</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgCondValue" data-append="Bali">+ Bali</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgCondValue" data-append="Maldives">+ Maldives</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgCondValue" data-append="Thailand">+ Thailand</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgCondValue" data-append="Kashmir">+ Kashmir</button>
                            <button type="button" class="drawer-chip append-chip" data-target="cfgCondValue" data-append="Goa">+ Goa</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgBranchYesLabel">
                            <span>Branch Yes Label</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgBranchYesLabel" value="${this.escapeHtml(cfg.branch_yes_label || 'Condition Met')}">
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgBranchNoLabel">
                            <span>Branch No Label</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgBranchNoLabel" value="${this.escapeHtml(cfg.branch_no_label || 'Otherwise')}">
                    </div>
                `;
            } else if (actionType === 'delay') {
                const delaySec = String(cfg.delay_seconds || '300');
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-hourglass-end"></i>
                        <div><strong>Cadence Delay:</strong> Pauses execution for a specified duration to simulate natural conversation pace or wait before sending follow-ups.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgDelaySeconds">
                            <span>Delay Duration</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgDelaySeconds" data-placeholder="Select Delay Duration">
                            <option value="3" ${delaySec === '3' ? 'selected' : ''}>3 Seconds (Simulate typing pace)</option>
                            <option value="30" ${delaySec === '30' ? 'selected' : ''}>30 Seconds</option>
                            <option value="60" ${delaySec === '60' ? 'selected' : ''}>1 Minute</option>
                            <option value="300" ${delaySec === '300' ? 'selected' : ''}>5 Minutes</option>
                            <option value="900" ${delaySec === '900' ? 'selected' : ''}>15 Minutes</option>
                            <option value="3600" ${delaySec === '3600' ? 'selected' : ''}>1 Hour</option>
                            <option value="7200" ${delaySec === '7200' ? 'selected' : ''}>2 Hours</option>
                            <option value="86400" ${delaySec === '86400' ? 'selected' : ''}>24 Hours (Next Day Follow-up)</option>
                            <option value="259200" ${delaySec === '259200' ? 'selected' : ''}>3 Days (Retention Check)</option>
                        </select>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Quick:</span>
                            <button type="button" class="drawer-chip ${delaySec === '3' ? 'active' : ''}" data-target="cfgDelaySeconds" data-val="3">3 Secs</button>
                            <button type="button" class="drawer-chip ${delaySec === '300' ? 'active' : ''}" data-target="cfgDelaySeconds" data-val="300">5 Mins</button>
                            <button type="button" class="drawer-chip ${delaySec === '3600' ? 'active' : ''}" data-target="cfgDelaySeconds" data-val="3600">1 Hour</button>
                            <button type="button" class="drawer-chip ${delaySec === '86400' ? 'active' : ''}" data-target="cfgDelaySeconds" data-val="86400">24 Hours</button>
                            <button type="button" class="drawer-chip ${delaySec === '259200' ? 'active' : ''}" data-target="cfgDelaySeconds" data-val="259200">3 Days</button>
                        </div>
                    </div>
                `;
            } else if (actionType === 'payment_link') {
                const payAmount = String(cfg.amount || '5000');
                const expiryHours = String(cfg.expiry_hours || '48');
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-credit-card"></i>
                        <div><strong>Generate Payment Checkout Link:</strong> Creates instant Razorpay / UPI booking advance deposit link for the traveler.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgPayAmount">
                            <span>Token Advance Amount (₹ INR)</span>
                        </label>
                        <input type="number" class="drawer-input" id="cfgPayAmount" value="${this.escapeHtml(cfg.amount || '5000')}" placeholder="5000">
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Preset:</span>
                            <button type="button" class="drawer-chip ${payAmount === '2500' ? 'active' : ''}" data-target="cfgPayAmount" data-val="2500">₹2,500 (Token)</button>
                            <button type="button" class="drawer-chip ${payAmount === '5000' ? 'active' : ''}" data-target="cfgPayAmount" data-val="5000">₹5,000 (Advance)</button>
                            <button type="button" class="drawer-chip ${payAmount === '10000' ? 'active' : ''}" data-target="cfgPayAmount" data-val="10000">₹10,000</button>
                            <button type="button" class="drawer-chip ${payAmount === '25000' ? 'active' : ''}" data-target="cfgPayAmount" data-val="25000">₹25,000</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgPayExpiry">
                            <span>Link Validity Window</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgPayExpiry" data-placeholder="Select Link Expiry">
                            <option value="24" ${expiryHours === '24' ? 'selected' : ''}>Valid for 24 Hours</option>
                            <option value="48" ${expiryHours === '48' ? 'selected' : ''}>Valid for 48 Hours</option>
                            <option value="168" ${expiryHours === '168' ? 'selected' : ''}>Valid for 7 Days</option>
                        </select>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip ${expiryHours === '24' ? 'active' : ''}" data-target="cfgPayExpiry" data-val="24">24 Hours</button>
                            <button type="button" class="drawer-chip ${expiryHours === '48' ? 'active' : ''}" data-target="cfgPayExpiry" data-val="48">48 Hours</button>
                            <button type="button" class="drawer-chip ${expiryHours === '168' ? 'active' : ''}" data-target="cfgPayExpiry" data-val="168">7 Days</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgPayMsg">
                            <span>Payment Request Message</span>
                        </label>
                        <textarea class="drawer-textarea" id="cfgPayMsg" rows="3">${this.escapeHtml(cfg.message_text || '')}</textarea>
                        <div class="tokens-title">Dynamic Tokens:</div>
                        <div class="token-pills-list">
                            <button type="button" class="token-pill" data-token="{payment_link}">+ {payment_link}</button>
                            <button type="button" class="token-pill" data-token="{amount}">+ {amount}</button>
                            <button type="button" class="token-pill" data-token="{name}">+ {name}</button>
                        </div>
                    </div>
                `;
            } else if (actionType === 'webhook_call') {
                const reqMethod = cfg.method || 'POST';
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-network-cloud"></i>
                        <div><strong>Outbound Webhook / API:</strong> Sends traveler payload via HTTP JSON POST to Zapier, Make, n8n, or external backends.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWebhookUrl">
                            <span>Outbound Destination URL</span>
                        </label>
                        <input type="url" class="drawer-input" id="cfgWebhookUrl" value="${this.escapeHtml(cfg.url || '')}" placeholder="https://hooks.zapier.com/...">
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWebhookMethod">
                            <span>HTTP Method</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgWebhookMethod" data-placeholder="Select HTTP Method">
                            <option value="POST" ${reqMethod === 'POST' ? 'selected' : ''}>POST (JSON Payload)</option>
                            <option value="GET" ${reqMethod === 'GET' ? 'selected' : ''}>GET</option>
                        </select>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip ${reqMethod === 'POST' ? 'active' : ''}" data-target="cfgWebhookMethod" data-val="POST">POST (JSON)</button>
                            <button type="button" class="drawer-chip ${reqMethod === 'GET' ? 'active' : ''}" data-target="cfgWebhookMethod" data-val="GET">GET</button>
                        </div>
                    </div>
                `;
            } else if (actionType === 'internal_note') {
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-note"></i>
                        <div><strong>Internal CRM Timeline Note:</strong> Logs an internal audit entry in the lead's history timeline visible exclusively to your sales staff.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgNoteText">
                            <span>Internal CRM Timeline Note</span>
                        </label>
                        <textarea class="drawer-textarea" id="cfgNoteText" rows="4" placeholder="Enter note logged to lead timeline...">${this.escapeHtml(cfg.note_text || 'Workflow milestone executed: customer engaged via workflow.')}</textarea>
                        <div class="tokens-title">Dynamic Tokens:</div>
                        <div class="token-pills-list">
                            <button type="button" class="token-pill" data-token="{name}">+ {name}</button>
                            <button type="button" class="token-pill" data-token="{destination}">+ {destination}</button>
                            <button type="button" class="token-pill" data-token="{budget}">+ {budget}</button>
                            <button type="button" class="token-pill" data-token="{agent_name}">+ {agent_name}</button>
                        </div>
                        <span class="drawer-help-text">Logs directly into the traveler's CRM audit timeline, visible to all agents.</span>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgNotePinned">
                            <span>Pin Note to Top of Lead Record?</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgNotePinned" data-placeholder="Pin to Top">
                            <option value="0" ${cfg.is_pinned !== '1' ? 'selected' : ''}>No, normal chronological entry</option>
                            <option value="1" ${cfg.is_pinned === '1' ? 'selected' : ''}>Yes, pin note to top of CRM profile</option>
                        </select>
                    </div>
                `;
            } else if (actionType === 'itinerary_viewed') {
                let pkgOptions = `<option value="0" ${(!cfg.package_id || cfg.package_id === '0') ? 'selected' : ''}>Any Itinerary Proposal Link</option>`;
                if (this.packagesList && this.packagesList.length > 0) {
                    pkgOptions += `<optgroup label="Specific Proposal Package">`;
                    this.packagesList.forEach(p => {
                        pkgOptions += `<option value="${p.id}" ${cfg.package_id == p.id ? 'selected' : ''}>${this.escapeHtml(p.name)}</option>`;
                    });
                    pkgOptions += `</optgroup>`;
                }
                const minSec = String(cfg.min_view_seconds || '15');
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-eye"></i>
                        <div><strong>Itinerary Viewed Online:</strong> Fires the instant a traveler opens their proposal link to review hotels and itinerary inclusions.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgItinViewPackage">
                            <span>Target Proposal / Itinerary Package</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgItinViewPackage" data-placeholder="Select Package">${pkgOptions}</select>
                        <span class="drawer-help-text">Fires when client opens or reviews their dynamic itinerary link.</span>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgItinMinSeconds">
                            <span>Minimum Dwell / View Duration</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgItinMinSeconds" data-placeholder="Select View Duration">
                            <option value="5" ${minSec === '5' ? 'selected' : ''}>5 Seconds (Initial Glance)</option>
                            <option value="15" ${minSec === '15' ? 'selected' : ''}>15 Seconds (Reading Details)</option>
                            <option value="30" ${minSec === '30' ? 'selected' : ''}>30 Seconds (Engaged Prospect)</option>
                            <option value="60" ${minSec === '60' ? 'selected' : ''}>60 Seconds (High-Intent Hot Lead)</option>
                        </select>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">Quick:</span>
                            <button type="button" class="drawer-chip ${minSec === '5' ? 'active' : ''}" data-target="cfgItinMinSeconds" data-val="5">5s (Glance)</button>
                            <button type="button" class="drawer-chip ${minSec === '15' ? 'active' : ''}" data-target="cfgItinMinSeconds" data-val="15">15s (Reading)</button>
                            <button type="button" class="drawer-chip ${minSec === '30' ? 'active' : ''}" data-target="cfgItinMinSeconds" data-val="30">30s (Engaged)</button>
                            <button type="button" class="drawer-chip ${minSec === '60' ? 'active' : ''}" data-target="cfgItinMinSeconds" data-val="60">60s (Hot Lead)</button>
                        </div>
                    </div>
                `;
            } else if (actionType === 'webhook_received') {
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-network-cloud"></i>
                        <div><strong>Inbound Webhook Trigger:</strong> Listens for incoming HTTP POST JSON payloads from external landing pages, Zapier, Make, or ads.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWebhookEvent">
                            <span>Webhook Trigger Event Name</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgWebhookEvent" value="${this.escapeHtml(cfg.event_name || 'external_lead')}" placeholder="e.g. form_submit, call_completed">
                        <span class="drawer-help-text">Custom identifier for inbound webhooks received from Zapier, Make, or custom API.</span>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWebhookSecret">
                            <span>Secret Security Token (Optional)</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgWebhookSecret" value="${this.escapeHtml(cfg.secret_token || '')}" placeholder="Optional Bearer token or secret">
                    </div>
                `;
            } else if (actionType === 'booking_confirmed') {
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-check-circle"></i>
                        <div><strong>Booking Confirmed Trigger:</strong> Executes when customer pays advance deposit and booking confirmation voucher is generated.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgBookingMinAmount">
                            <span>Minimum Booking Amount (₹)</span>
                        </label>
                        <input type="number" class="drawer-input" id="cfgBookingMinAmount" value="${this.escapeHtml(cfg.min_amount || '0')}" placeholder="0 for any booking">
                        <span class="drawer-help-text">Triggers when a package booking is confirmed and voucher is issued.</span>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgBookingPkgType">
                            <span>Package Category</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgBookingPkgType">
                            <option value="all" ${(!cfg.package_type || cfg.package_type === 'all') ? 'selected' : ''}>All Tour Types</option>
                            <option value="custom" ${cfg.package_type === 'custom' ? 'selected' : ''}>Tailor-Made Custom Tour</option>
                            <option value="group" ${cfg.package_type === 'group' ? 'selected' : ''}>Fixed Group Departure</option>
                            <option value="honeymoon" ${cfg.package_type === 'honeymoon' ? 'selected' : ''}>Honeymoon Special</option>
                            <option value="luxury" ${cfg.package_type === 'luxury' ? 'selected' : ''}>5★ Luxury Vacation</option>
                        </select>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-checkbox-label">
                            <input type="checkbox" id="cfgBookingSendVoucher" ${cfg.send_voucher !== '0' ? 'checked' : ''}>
                            <span>Auto-generate and attach confirmation voucher PDF</span>
                        </label>
                    </div>
                `;
            } else if (actionType === 'lead_idle_timeout') {
                let stageOptions = '<option value="any">Any Pipeline Stage</option>';
                (this.pipelineStagesList || []).forEach(st => {
                    const sel = (cfg.stage === st.code || cfg.stage === String(st.id)) ? 'selected' : '';
                    stageOptions += `<option value="${st.code || st.id}" ${sel}>${this.escapeHtml(st.label || st.name)}</option>`;
                });
                const idleHours = String(cfg.idle_hours || '24');
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-alarm-clock"></i>
                        <div><strong>Follow-up Overdue Trigger:</strong> Fires automatically when a lead remains untouched without calls, notes, or stage updates within SLA.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgIdleHours">
                            <span>Inactivity Duration (Hours)</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgIdleHours">
                            <option value="12" ${idleHours === '12' ? 'selected' : ''}>12 Hours (Fast Follow-up)</option>
                            <option value="24" ${idleHours === '24' ? 'selected' : ''}>24 Hours (Standard 1 Day)</option>
                            <option value="48" ${idleHours === '48' ? 'selected' : ''}>48 Hours (2 Days Overdue)</option>
                            <option value="72" ${idleHours === '72' ? 'selected' : ''}>72 Hours (3 Days Stale)</option>
                            <option value="168" ${idleHours === '168' ? 'selected' : ''}>7 Days (Re-engagement Warning)</option>
                        </select>
                        <span class="drawer-help-text">Triggers when no note, call, or stage update occurs within this window.</span>
                        <div class="drawer-chip-row">
                            <span class="drawer-chip-lead">SLA:</span>
                            <button type="button" class="drawer-chip ${idleHours === '12' ? 'active' : ''}" data-target="cfgIdleHours" data-val="12">12 Hours</button>
                            <button type="button" class="drawer-chip ${idleHours === '24' ? 'active' : ''}" data-target="cfgIdleHours" data-val="24">24 Hours</button>
                            <button type="button" class="drawer-chip ${idleHours === '48' ? 'active' : ''}" data-target="cfgIdleHours" data-val="48">48 Hours</button>
                            <button type="button" class="drawer-chip ${idleHours === '72' ? 'active' : ''}" data-target="cfgIdleHours" data-val="72">3 Days</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgIdleStage">
                            <span>Monitor Pipeline Stage</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgIdleStage">${stageOptions}</select>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-checkbox-label">
                            <input type="checkbox" id="cfgIdleAlertManager" ${cfg.alert_manager !== '0' ? 'checked' : ''}>
                            <span>Escalate alert to Sales Manager if unresolved</span>
                        </label>
                    </div>
                `;
            } else if (actionType === 'trip_completed') {
                const timing = cfg.trigger_timing || 'same_day';
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-plane-arrival"></i>
                        <div><strong>Trip Completed / Returned:</strong> Fires when the traveler concludes their holiday to send welcome home greetings and collect Google reviews.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgTripTiming">
                            <span>Trigger Timing</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgTripTiming">
                            <option value="same_day" ${timing === 'same_day' ? 'selected' : ''}>Same Day of Return (Evening Welcome)</option>
                            <option value="24h" ${timing === '24h' ? 'selected' : ''}>24 Hours After Return</option>
                            <option value="3_days" ${timing === '3_days' ? 'selected' : ''}>3 Days After Return (Follow-up Feedback)</option>
                        </select>
                        <span class="drawer-help-text">Fires when the traveler's tour end date arrives in the CRM.</span>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip ${timing === 'same_day' ? 'active' : ''}" data-target="cfgTripTiming" data-val="same_day">Same Day</button>
                            <button type="button" class="drawer-chip ${timing === '24h' ? 'active' : ''}" data-target="cfgTripTiming" data-val="24h">24h After</button>
                            <button type="button" class="drawer-chip ${timing === '3_days' ? 'active' : ''}" data-target="cfgTripTiming" data-val="3_days">3 Days After</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-checkbox-label">
                            <input type="checkbox" id="cfgTripRequestReview" ${cfg.request_review !== '0' ? 'checked' : ''}>
                            <span>Automatically prompt for Google / TripAdvisor review</span>
                        </label>
                    </div>
                `;
            } else if (actionType === 'send_whatsapp_buttons') {
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-apps"></i>
                        <div><strong>Interactive WhatsApp Buttons:</strong> Sends quick reply buttons (e.g. Confirm Booking, Chat with Agent) with 3x higher traveler engagement.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWaBtnHeader">
                            <span>Header Title (Optional)</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgWaBtnHeader" value="${this.escapeHtml(cfg.header_text || 'Trip Proposal Update')}" placeholder="e.g. Exclusive Offer, Tour Proposal">
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWaBtnBody">
                            <span>WhatsApp Body Text</span>
                        </label>
                        <textarea class="drawer-textarea" id="cfgWaBtnBody" rows="4" placeholder="Message text with {name}, {destination}...">${this.escapeHtml(cfg.body_text || "Namaste {name}! 🌴 We have prepared your tailored quotation for {destination}. Would you like to proceed or review details?")}</textarea>
                        <div class="tokens-title">Dynamic Tokens:</div>
                        <div class="token-pills-list">
                            <button type="button" class="token-pill" data-token="{name}">+ {name}</button>
                            <button type="button" class="token-pill" data-token="{destination}">+ {destination}</button>
                            <button type="button" class="token-pill" data-token="{budget}">+ {budget}</button>
                            <button type="button" class="token-pill" data-token="{company}">+ {company}</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWaBtn1">
                            <span>Quick Action Button 1</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgWaBtn1" value="${this.escapeHtml(cfg.btn1 || 'Confirm Booking')}" placeholder="Button 1 label">
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWaBtn2">
                            <span>Quick Action Button 2</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgWaBtn2" value="${this.escapeHtml(cfg.btn2 || 'Talk to Expert')}" placeholder="Button 2 label">
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgWaBtn3">
                            <span>Quick Action Button 3 (Optional)</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgWaBtn3" value="${this.escapeHtml(cfg.btn3 || 'Modify Dates')}" placeholder="Button 3 label">
                        <span class="drawer-help-text">Interactive WhatsApp buttons boost response rates by 300%.</span>
                    </div>
                `;
            } else if (actionType === 'send_email') {
                const dept = cfg.sender_dept || 'sales';
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-envelope"></i>
                        <div><strong>Send Email Proposal:</strong> Dispatches rich branded email quotation with destination highlights and attached PDF.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgEmailSubject">
                            <span>Email Subject</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgEmailSubject" value="${this.escapeHtml(cfg.subject || 'Your Vacation Proposal & Itinerary - {destination}')}" placeholder="Subject with tokens">
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgEmailSenderDept">
                            <span>Sender Department</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgEmailSenderDept">
                            <option value="sales" ${dept === 'sales' ? 'selected' : ''}>Holidays &amp; Sales Desk (travel@)</option>
                            <option value="concierge" ${dept === 'concierge' ? 'selected' : ''}>VIP Concierge Desk (concierge@)</option>
                            <option value="support" ${dept === 'support' ? 'selected' : ''}>Customer Support (care@)</option>
                        </select>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-checkbox-label">
                            <input type="checkbox" id="cfgEmailAttachPdf" ${cfg.attach_pdf !== '0' ? 'checked' : ''}>
                            <span>Attach Branded PDF Itinerary &amp; Rate Card</span>
                        </label>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgEmailBody">
                            <span>Email Body Content</span>
                        </label>
                        <textarea class="drawer-textarea" id="cfgEmailBody" rows="5">${this.escapeHtml(cfg.email_body || "Dear {name},\n\nThank you for choosing {company}. Attached is your detailed day-by-day travel plan and price breakdown for {destination}.\n\nPlease let us know if you need any adjustments!")}</textarea>
                        <div class="tokens-title">Dynamic Tokens:</div>
                        <div class="token-pills-list">
                            <button type="button" class="token-pill" data-token="{name}">+ {name}</button>
                            <button type="button" class="token-pill" data-token="{destination}">+ {destination}</button>
                            <button type="button" class="token-pill" data-token="{company}">+ {company}</button>
                        </div>
                    </div>
                `;
            } else if (actionType === 'notify_internal_team') {
                const chan = cfg.channel || 'all';
                const prio = cfg.priority || 'high';
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-bell"></i>
                        <div><strong>Internal Team Alert:</strong> Immediately pings sales consultants or managers when an inquiry needs prompt attention.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgNotifyChannel">
                            <span>Notification Channel</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgNotifyChannel">
                            <option value="all" ${chan === 'all' ? 'selected' : ''}>All (In-App Bell + Team WhatsApp)</option>
                            <option value="bell" ${chan === 'bell' ? 'selected' : ''}>In-App CRM Bell Notification Only</option>
                            <option value="whatsapp" ${chan === 'whatsapp' ? 'selected' : ''}>Operations Team WhatsApp Group</option>
                            <option value="email" ${chan === 'email' ? 'selected' : ''}>Manager Email Alert</option>
                        </select>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip ${chan === 'all' ? 'active' : ''}" data-target="cfgNotifyChannel" data-val="all">All Channels</button>
                            <button type="button" class="drawer-chip ${chan === 'whatsapp' ? 'active' : ''}" data-target="cfgNotifyChannel" data-val="whatsapp">WhatsApp Group</button>
                            <button type="button" class="drawer-chip ${chan === 'bell' ? 'active' : ''}" data-target="cfgNotifyChannel" data-val="bell">In-App Bell</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgNotifyPriority">
                            <span>Priority Alert Level</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgNotifyPriority">
                            <option value="high" ${prio === 'high' ? 'selected' : ''}>🔥 Urgent (Hot High-Intent Lead)</option>
                            <option value="medium" ${prio === 'medium' ? 'selected' : ''}>Standard Alert</option>
                            <option value="low" ${prio === 'low' ? 'selected' : ''}>Informational</option>
                        </select>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgNotifyText">
                            <span>Alert Message Template</span>
                        </label>
                        <textarea class="drawer-textarea" id="cfgNotifyText" rows="3">${this.escapeHtml(cfg.alert_text || "🔥 Hot Lead Alert: {name} engaged with {destination} proposal. Call immediately!")}</textarea>
                    </div>
                `;
            } else if (actionType === 'request_feedback') {
                const plat = cfg.platform || 'google_review';
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-star"></i>
                        <div><strong>Traveler Feedback Request:</strong> Prompts happy travelers for Google 5★ reviews or TripAdvisor ratings with incentive vouchers.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgFeedbackPlatform">
                            <span>Review Platform</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgFeedbackPlatform">
                            <option value="google_review" ${plat === 'google_review' ? 'selected' : ''}>Google Maps 5★ Review</option>
                            <option value="tripadvisor" ${plat === 'tripadvisor' ? 'selected' : ''}>TripAdvisor Listing</option>
                            <option value="crm_survey" ${plat === 'crm_survey' ? 'selected' : ''}>In-House Customer Satisfaction Rating</option>
                        </select>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip ${plat === 'google_review' ? 'active' : ''}" data-target="cfgFeedbackPlatform" data-val="google_review">Google Review</button>
                            <button type="button" class="drawer-chip ${plat === 'tripadvisor' ? 'active' : ''}" data-target="cfgFeedbackPlatform" data-val="tripadvisor">TripAdvisor</button>
                        </div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgFeedbackUrl">
                            <span>Review / Survey URL</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgFeedbackUrl" value="${this.escapeHtml(cfg.review_url || 'https://g.page/r/aleya-tours/review')}" placeholder="https://g.page/r/...">
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgFeedbackIncentive">
                            <span>Traveler Incentive / Reward</span>
                        </label>
                        <input type="text" class="drawer-input" id="cfgFeedbackIncentive" value="${this.escapeHtml(cfg.incentive || '10% discount coupon for next vacation')}" placeholder="e.g. ₹1,000 credit or discount voucher">
                    </div>
                `;
            } else if (actionType === 'working_hours_check') {
                const workDays = cfg.work_days || 'mon_sat';
                html = `
                    <div class="drawer-callout-info">
                        <i class="fi fi-rr-clock"></i>
                        <div><strong>Office Working Hours Check:</strong> Distinguishes inquiries during active business hours vs. night/weekend arrivals. Route daytime leads to sales consultants and nighttime leads to the 24/7 automated bot.</div>
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgHoursStart">
                            <span>Business Hours Start</span>
                        </label>
                        <input type="time" class="drawer-input" id="cfgHoursStart" value="${this.escapeHtml(cfg.start_time || '09:30')}">
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgHoursEnd">
                            <span>Business Hours End</span>
                        </label>
                        <input type="time" class="drawer-input" id="cfgHoursEnd" value="${this.escapeHtml(cfg.end_time || '19:30')}">
                    </div>
                    <div class="drawer-form-group">
                        <label class="drawer-label" for="cfgHoursDays">
                            <span>Operating Days</span>
                        </label>
                        <select class="drawer-select selectjs2 selectJS2" id="cfgHoursDays">
                            <option value="mon_sat" ${workDays === 'mon_sat' ? 'selected' : ''}>Monday - Saturday (Standard Agency)</option>
                            <option value="mon_fri" ${workDays === 'mon_fri' ? 'selected' : ''}>Monday - Friday (Corporate Desk)</option>
                            <option value="all_days" ${workDays === 'all_days' ? 'selected' : ''}>All 7 Days (24/7 Agency)</option>
                        </select>
                        <div class="drawer-chip-row">
                            <button type="button" class="drawer-chip ${workDays === 'mon_sat' ? 'active' : ''}" data-target="cfgHoursDays" data-val="mon_sat">Mon - Sat</button>
                            <button type="button" class="drawer-chip ${workDays === 'mon_fri' ? 'active' : ''}" data-target="cfgHoursDays" data-val="mon_fri">Mon - Fri</button>
                            <button type="button" class="drawer-chip ${workDays === 'all_days' ? 'active' : ''}" data-target="cfgHoursDays" data-val="all_days">All 7 Days</button>
                        </div>
                        <span class="drawer-help-text">Outputs 2 branches: YES = During office hours (route to agent). NO = After hours (trigger night bot greeting).</span>
                    </div>
                `;
            }

            this.drawerDynamicFieldsEl.innerHTML = html;
            try {
                this.initDrawerSelects();
            } catch (err) {}

            // WhatsApp template select change listener -> auto-fill textarea
            const waTplSelect = document.getElementById('cfgWaTemplateSelect');
            if (waTplSelect) {
                const handleWaTplChange = () => {
                    const selId = waTplSelect.value;
                    if (selId && selId !== '0') {
                        const found = this.whatsappTemplatesList.find(t => String(t.id) === String(selId));
                        if (found && found.content) {
                            const msgBox = document.getElementById('cfgWaMessage');
                            if (msgBox) {
                                msgBox.value = found.content;
                            }
                        }
                    }
                };
                waTplSelect.addEventListener('change', handleWaTplChange);
                const $ = window.jQuery || window.$;
                if ($) {
                    $(waTplSelect).on('change', handleWaTplChange);
                }
            }
        }

        applyNodeDrawerChanges(showNotification = true) {
            if (!this.selectedNodeId) return;
            const node = this.nodes.find(n => n.id === this.selectedNodeId);
            if (!node) return;

            if (this.drawerInputTitleEl) node.title = this.drawerInputTitleEl.value.trim() || node.title;
            const customSubtitle = this.drawerInputSubtitleEl ? this.drawerInputSubtitleEl.value.trim() : '';

            const actionType = node.action_type || node.trigger_event || '';
            const cfg = node.config || {};
            let generatedSubtitle = '';

            if (actionType === 'lead_created') {
                const sEl = document.getElementById('cfgTriggerSources');
                const bEl = document.getElementById('cfgTriggerMinBudget');
                const pEl = document.getElementById('cfgTriggerMinPax');
                if (sEl) {
                    cfg.source = sEl.value;
                    cfg.sources = [sEl.value];
                    const optText = sEl.options[sEl.selectedIndex]?.text || sEl.value;
                    generatedSubtitle = `Source: ${optText}`;
                }
                if (bEl) cfg.min_budget = bEl.value;
                if (pEl) cfg.min_pax = pEl.value;
            } else if (actionType === 'whatsapp_incoming') {
                const rEl = document.getElementById('cfgWaTriggerRule');
                const kEl = document.getElementById('cfgKeywords');
                const mEl = document.getElementById('cfgMatchType');
                if (rEl) cfg.trigger_rule = rEl.value;
                if (kEl) {
                    cfg.keywords = kEl.value.trim();
                    generatedSubtitle = `Keywords: ${cfg.keywords}`;
                }
                if (mEl) cfg.match_type = mEl.value;
            } else if (actionType === 'payment_received') {
                const ptEl = document.getElementById('cfgPaymentType');
                const paEl = document.getElementById('cfgPaymentMinAmount');
                if (ptEl) cfg.payment_type = ptEl.value;
                if (paEl) {
                    cfg.min_amount = paEl.value;
                    generatedSubtitle = `Min Amount: ₹${cfg.min_amount}`;
                }
            } else if (actionType === 'lead_stage_changed') {
                const fsEl = document.getElementById('cfgFromStage');
                const tsEl = document.getElementById('cfgToStage');
                if (fsEl) cfg.from_stage = fsEl.value;
                if (tsEl) {
                    cfg.to_stage = tsEl.value;
                    const optText = tsEl.options[tsEl.selectedIndex]?.text || tsEl.value;
                    generatedSubtitle = `To Stage: ${optText}`;
                }
            } else if (actionType === 'send_whatsapp') {
                const tEl = document.getElementById('cfgWaTemplateSelect');
                const wEl = document.getElementById('cfgWaMessage');
                const aEl = document.getElementById('cfgAttachBrochure');
                if (tEl) {
                    cfg.template_id = tEl.value;
                    if (tEl.value !== '0') {
                        const optText = tEl.options[tEl.selectedIndex]?.text || '';
                        generatedSubtitle = `Template: ${optText}`;
                    } else {
                        generatedSubtitle = 'Custom WhatsApp Message';
                    }
                }
                if (wEl) cfg.message_template = wEl.value;
                if (aEl) cfg.attach_brochure = aEl.value;
            } else if (actionType === 'send_itinerary') {
                const pEl = document.getElementById('cfgPackageId');
                const tEl = document.getElementById('cfgItineraryTitle');
                const mEl = document.getElementById('cfgItineraryMsg');
                if (pEl) {
                    cfg.package_id = pEl.value;
                    if (pEl.value !== '0') {
                        const optText = pEl.options[pEl.selectedIndex]?.text || '';
                        generatedSubtitle = `Package: ${optText}`;
                    } else {
                        generatedSubtitle = 'Dynamic destination match';
                    }
                }
                if (tEl) cfg.itinerary_title = tEl.value;
                if (mEl) cfg.message_text = mEl.value;
            } else if (actionType === 'assign_agent') {
                const aEl = document.getElementById('cfgAgentMode');
                const fbEl = document.getElementById('cfgAgentFallback');
                if (aEl) {
                    if (aEl.value === 'round_robin' || aEl.value === '0') {
                        cfg.mode = 'round_robin';
                        cfg.assigned_agent_id = '0';
                        generatedSubtitle = 'Round-Robin Lead Allocation';
                    } else {
                        cfg.mode = 'specific_agent';
                        cfg.assigned_agent_id = aEl.value;
                        const optText = aEl.options[aEl.selectedIndex]?.text || '';
                        generatedSubtitle = `Assigned to: ${optText}`;
                    }
                }
                if (fbEl) cfg.fallback_mode = fbEl.value;
            } else if (actionType === 'update_lead_stage') {
                const stEl = document.getElementById('cfgTargetStage');
                const snEl = document.getElementById('cfgStageNote');
                if (stEl) {
                    cfg.stage_code = stEl.value;
                    const optText = stEl.options[stEl.selectedIndex]?.text || stEl.value;
                    generatedSubtitle = `Move to: ${optText}`;
                }
                if (snEl) cfg.status_note = snEl.value;
            } else if (actionType === 'add_tags') {
                const aEl = document.getElementById('cfgTagsAction');
                const tEl = document.getElementById('cfgTags');
                if (aEl) cfg.action = aEl.value;
                if (tEl) {
                    cfg.tags = tEl.value.trim();
                    generatedSubtitle = `Tags: ${cfg.tags}`;
                }
            } else if (actionType === 'create_task') {
                const tEl = document.getElementById('cfgTaskType');
                const dEl = document.getElementById('cfgDueHours');
                const aEl = document.getElementById('cfgTaskAgent');
                const nEl = document.getElementById('cfgTaskNote');
                if (tEl) cfg.task_type = tEl.value;
                if (dEl) cfg.due_hours = dEl.value;
                if (aEl) cfg.assigned_agent_id = aEl.value;
                if (nEl) cfg.task_note = nEl.value;
                generatedSubtitle = `${cfg.task_type || 'Task'} due in ${cfg.due_hours || 2}h`;
            } else if (actionType === 'condition_branch') {
                const fEl = document.getElementById('cfgCondField');
                const oEl = document.getElementById('cfgCondOp');
                const vEl = document.getElementById('cfgCondValue');
                const yEl = document.getElementById('cfgBranchYesLabel');
                const nEl = document.getElementById('cfgBranchNoLabel');
                if (fEl) cfg.field = fEl.value;
                if (oEl) cfg.operator = oEl.value;
                if (vEl) cfg.value = vEl.value;
                if (yEl) cfg.branch_yes_label = yEl.value;
                if (nEl) cfg.branch_no_label = nEl.value;
                generatedSubtitle = `Check ${cfg.field} ${cfg.operator} "${cfg.value}"`;
            } else if (actionType === 'delay') {
                const dEl = document.getElementById('cfgDelaySeconds');
                if (dEl) {
                    cfg.delay_seconds = dEl.value;
                    generatedSubtitle = `Wait ${cfg.delay_seconds} seconds`;
                }
            } else if (actionType === 'payment_link') {
                const pEl = document.getElementById('cfgPayAmount');
                const expEl = document.getElementById('cfgPayExpiry');
                const mEl = document.getElementById('cfgPayMsg');
                if (pEl) {
                    cfg.amount = pEl.value;
                    generatedSubtitle = `Deposit Link: ₹${cfg.amount}`;
                }
                if (expEl) cfg.expiry_hours = expEl.value;
                if (mEl) cfg.message_text = mEl.value;
            } else if (actionType === 'webhook_call') {
                const uEl = document.getElementById('cfgWebhookUrl');
                const mEl = document.getElementById('cfgWebhookMethod');
                if (uEl) {
                    cfg.url = uEl.value;
                    generatedSubtitle = `POST ${cfg.url}`;
                }
                if (mEl) cfg.method = mEl.value;
            } else if (actionType === 'internal_note') {
                const ntEl = document.getElementById('cfgNoteText');
                const npEl = document.getElementById('cfgNotePinned');
                if (ntEl) {
                    cfg.note_text = ntEl.value.trim();
                    generatedSubtitle = `Note: ${cfg.note_text.substring(0, 32)}...`;
                }
                if (npEl) cfg.is_pinned = npEl.value;
            } else if (actionType === 'itinerary_viewed') {
                const ivpEl = document.getElementById('cfgItinViewPackage');
                const ivsEl = document.getElementById('cfgItinMinSeconds');
                if (ivpEl) cfg.package_id = ivpEl.value;
                if (ivsEl) cfg.min_view_seconds = ivsEl.value;
                generatedSubtitle = `Client views proposal for ${cfg.min_view_seconds || 15}s`;
            } else if (actionType === 'webhook_received') {
                const weEl = document.getElementById('cfgWebhookEvent');
                const wsEl = document.getElementById('cfgWebhookSecret');
                if (weEl) {
                    cfg.event_name = weEl.value.trim();
                    generatedSubtitle = `Event: ${cfg.event_name}`;
                }
                if (wsEl) cfg.secret_token = wsEl.value.trim();
            } else if (actionType === 'booking_confirmed') {
                const bAmt = document.getElementById('cfgBookingMinAmount');
                const bType = document.getElementById('cfgBookingPkgType');
                const bVoucher = document.getElementById('cfgBookingSendVoucher');
                if (bAmt) cfg.min_amount = bAmt.value.trim();
                if (bType) cfg.package_type = bType.value;
                if (bVoucher) cfg.send_voucher = bVoucher.checked ? '1' : '0';
                generatedSubtitle = `Booking confirmed (${cfg.package_type || 'all'})`;
            } else if (actionType === 'lead_idle_timeout') {
                const idH = document.getElementById('cfgIdleHours');
                const idSt = document.getElementById('cfgIdleStage');
                const idAl = document.getElementById('cfgIdleAlertManager');
                if (idH) cfg.idle_hours = idH.value;
                if (idSt) cfg.stage = idSt.value;
                if (idAl) cfg.alert_manager = idAl.checked ? '1' : '0';
                generatedSubtitle = `Inactive for ${cfg.idle_hours || 24}h without update`;
            } else if (actionType === 'trip_completed') {
                const ttEl = document.getElementById('cfgTripTiming');
                const trEl = document.getElementById('cfgTripRequestReview');
                if (ttEl) cfg.trigger_timing = ttEl.value;
                if (trEl) cfg.request_review = trEl.checked ? '1' : '0';
                generatedSubtitle = `Trip return (${cfg.trigger_timing || 'same_day'})`;
            } else if (actionType === 'send_whatsapp_buttons') {
                const hEl = document.getElementById('cfgWaBtnHeader');
                const bEl = document.getElementById('cfgWaBtnBody');
                const b1El = document.getElementById('cfgWaBtn1');
                const b2El = document.getElementById('cfgWaBtn2');
                const b3El = document.getElementById('cfgWaBtn3');
                if (hEl) cfg.header_text = hEl.value.trim();
                if (bEl) cfg.body_text = bEl.value.trim();
                if (b1El) cfg.btn1 = b1El.value.trim();
                if (b2El) cfg.btn2 = b2El.value.trim();
                if (b3El) cfg.btn3 = b3El.value.trim();
                generatedSubtitle = `Buttons: ${cfg.btn1 || 'Option 1'}, ${cfg.btn2 || 'Option 2'}`;
            } else if (actionType === 'send_email') {
                const sEl = document.getElementById('cfgEmailSubject');
                const dEl = document.getElementById('cfgEmailSenderDept');
                const aEl = document.getElementById('cfgEmailAttachPdf');
                const bEl = document.getElementById('cfgEmailBody');
                if (sEl) cfg.subject = sEl.value.trim();
                if (dEl) cfg.sender_dept = dEl.value;
                if (aEl) cfg.attach_pdf = aEl.checked ? '1' : '0';
                if (bEl) cfg.email_body = bEl.value.trim();
                generatedSubtitle = `Email: ${cfg.subject || 'Proposal'}`;
            } else if (actionType === 'notify_internal_team') {
                const chEl = document.getElementById('cfgNotifyChannel');
                const prEl = document.getElementById('cfgNotifyPriority');
                const txEl = document.getElementById('cfgNotifyText');
                if (chEl) cfg.channel = chEl.value;
                if (prEl) cfg.priority = prEl.value;
                if (txEl) cfg.alert_text = txEl.value.trim();
                generatedSubtitle = `Alert: ${cfg.priority || 'high'} via ${cfg.channel || 'all'}`;
            } else if (actionType === 'request_feedback') {
                const pfEl = document.getElementById('cfgFeedbackPlatform');
                const urEl = document.getElementById('cfgFeedbackUrl');
                const inEl = document.getElementById('cfgFeedbackIncentive');
                if (pfEl) cfg.platform = pfEl.value;
                if (urEl) cfg.review_url = urEl.value.trim();
                if (inEl) cfg.incentive = inEl.value.trim();
                generatedSubtitle = `Review: ${cfg.platform || 'google_review'}`;
            } else if (actionType === 'working_hours_check') {
                const stEl = document.getElementById('cfgHoursStart');
                const enEl = document.getElementById('cfgHoursEnd');
                const dyEl = document.getElementById('cfgHoursDays');
                if (stEl) cfg.start_time = stEl.value;
                if (enEl) cfg.end_time = enEl.value;
                if (dyEl) cfg.work_days = dyEl.value;
                generatedSubtitle = `Hours: ${cfg.start_time || '09:30'} - ${cfg.end_time || '19:30'}`;
            }

            node.subtitle = customSubtitle || generatedSubtitle || node.subtitle;
            if (this.drawerInputSubtitleEl && !customSubtitle && generatedSubtitle) {
                this.drawerInputSubtitleEl.placeholder = generatedSubtitle;
            }

            node.config = cfg;
            this.pushHistoryState();
            this.markDirty(true);
            this.renderNodes();
            this.renderConnections();

            if (showNotification) {
                const btnApply = document.getElementById('btnApplyNodeChanges');
                if (btnApply) {
                    const origHtml = btnApply.innerHTML;
                    btnApply.innerHTML = `<i class="fi fi-rr-check"></i> <span>Applied!</span>`;
                    setTimeout(() => { btnApply.innerHTML = origHtml; }, 1200);
                }
                this.showToast('Step updated!', 'success');
            }
        }

        insertTokenIntoActiveField(token) {
            if (!this.nodeConfigDrawerEl) return;
            const activeEl = this.lastFocusedInput ||
                             this.nodeConfigDrawerEl.querySelector('textarea:focus, input[type="text"]:focus') ||
                             this.nodeConfigDrawerEl.querySelector('#cfgWaMessage') ||
                             this.nodeConfigDrawerEl.querySelector('#cfgItineraryMsg') ||
                             this.nodeConfigDrawerEl.querySelector('#cfgPayMsg') ||
                             this.nodeConfigDrawerEl.querySelector('textarea, input[type="text"]');
            if (activeEl) {
                const start = activeEl.selectionStart !== undefined ? activeEl.selectionStart : activeEl.value.length;
                const end = activeEl.selectionEnd !== undefined ? activeEl.selectionEnd : activeEl.value.length;
                const val = activeEl.value;
                activeEl.value = val.substring(0, start) + token + val.substring(end);
                activeEl.focus();
                activeEl.selectionStart = activeEl.selectionEnd = start + token.length;
            }
        }

        deleteNode(nodeId, skipConfirm = false) {
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return;
            const nodeTitle = node.title || 'this step';

            const executeDelete = () => {
                this.nodes = this.nodes.filter(n => n.id !== nodeId);
                this.connections = this.connections.filter(c => c.from !== nodeId && c.to !== nodeId);
                if (this.selectedNodeId === nodeId) {
                    this.selectedNodeId = null;
                    this.closeNodeDrawer();
                }
                this.pushHistoryState();
                this.markDirty(true);
                this.renderCanvas();
                this.showToast(`Removed "${nodeTitle}"`, 'info');
            };

            if (skipConfirm) {
                executeDelete();
                return;
            }

            // SweetAlert confirmation dialog
            if (typeof window.Swal === 'function') {
                window.Swal.fire({
                    title: 'Remove Step?',
                    text: `Are you sure you want to remove "${nodeTitle}" from this workflow? Connected routes will also be detached.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Remove Step',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result && (result.isConfirmed || result.value)) {
                        executeDelete();
                    }
                });
            } else if (typeof window.swal === 'function') {
                window.swal({
                    title: 'Remove Step?',
                    text: `Are you sure you want to remove "${nodeTitle}" from this workflow? Connected routes will also be detached.`,
                    icon: 'warning',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, Remove Step',
                    cancelButtonText: 'Cancel',
                    buttons: ['Cancel', 'Yes, Remove Step'],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete === true || (willDelete && willDelete.isConfirmed)) {
                        executeDelete();
                    }
                });
            } else {
                if (window.confirm(`Are you sure you want to remove "${nodeTitle}" from this workflow? Connected routes will also be detached.`)) {
                    executeDelete();
                }
            }
        }

        deleteSelectedNode() {
            if (this.selectedNodeId) {
                this.deleteNode(this.selectedNodeId);
            }
        }

        /* ==========================================================================
           Save Workflow via API
           ========================================================================== */
        async saveCurrentWorkflow() {
            const name = this.editorWorkflowNameEl ? this.editorWorkflowNameEl.value.trim() : 'New Workflow';
            if (!name) {
                this.showToast('Please enter a name for this workflow.', 'error');
                return;
            }

            const triggerNode = this.nodes.find(n => n.category === 'trigger');
            if (!triggerNode && this.nodes.length > 0) {
                this.showToast('Flowchart should start with a Trigger step (e.g. New Lead Ingested, WhatsApp Chat Inbound).', 'info');
            }

            const status = this.editorStatusToggleEl && this.editorStatusToggleEl.checked ? 1 : 0;
            const category = 'crm';
            const triggerType = triggerNode ? (triggerNode.trigger_event || triggerNode.action_type || 'lead_created') : 'lead_created';

            const payload = {
                action: 'save',
                id: this.currentWorkflowId || 0,
                name: name,
                description: `Visual flowchart pipeline with ${this.nodes.length} automation steps.`,
                category: category,
                trigger_type: triggerType,
                status: status,
                nodes: this.nodes,
                connections: this.connections,
                canvas: { zoom: this.zoomLevel, panX: this.panX, panY: this.panY }
            };

            const btnSave = document.getElementById('btnSaveWorkflowFlowchart');
            if (btnSave) {
                btnSave.disabled = true;
                btnSave.innerHTML = `<i class="fi fi-rr-spinner btn-loading-spinner"></i> <span>Saving...</span>`;
            }

            try {
                const res = await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const resJson = await res.json();
                if (resJson.status === 'success' && resJson.data) {
                    this.currentWorkflowId = resJson.data.id;
                    this.setWorkflowUrlParam(resJson.data.id);
                    this.markDirty(false);
                    this.showToast('Workflow saved successfully!', 'success');
                } else {
                    this.showToast(resJson.msg || 'Save failed.', 'error');
                }
            } catch (err) {
                console.error('Save workflow error:', err);
                this.showToast('Save failed: ' + err.message, 'error');
            } finally {
                if (btnSave) {
                    btnSave.disabled = false;
                    btnSave.innerHTML = `<i class="fi fi-rr-disk"></i> <span>Save Workflow</span>`;
                }
            }
        }

        /* ==========================================================================
           Simulation / "Run Once" Test Runner
           ========================================================================== */
        async triggerSimulationRun(preset = null) {
            if (this.nodes.length === 0) {
                this.showToast('Add at least one trigger or action module to run simulation.', 'info');
                return;
            }

            if (this.isSimulating) return;
            this.isSimulating = true;

            // Clear any lingering step timers from prior run
            if (this.simulationTimeouts && this.simulationTimeouts.length > 0) {
                this.simulationTimeouts.forEach(t => clearTimeout(t));
            }
            this.simulationTimeouts = [];

            if (!this.testRunConsoleEl) return;
            this.testRunConsoleEl.classList.remove('is-closed');

            const statusDot = document.getElementById('consoleStatusDot');
            const titleEl = document.getElementById('consoleHeaderTitle');
            if (titleEl) titleEl.textContent = 'Executing Flowchart Simulation...';
            if (statusDot) {
                statusDot.className = 'console-status-dot is-running';
            }
            if (this.testConsoleStepsListEl) {
                this.testConsoleStepsListEl.innerHTML = '<div class="console-msg-pending"><i class="fi fi-rr-spinner btn-loading-spinner"></i> Transmitting trigger event payload across flowchart nodes...</div>';
            }

            this.svgConnectionsGroupEl.querySelectorAll('.flow-connector-path').forEach(p => p.classList.add('is-running'));

            const selectedPreset = preset || document.getElementById('simulationPresetSelect')?.value || 'couple';

            try {
                const res = await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'test_run',
                        id: this.currentWorkflowId || 0,
                        preset: selectedPreset,
                        nodes: this.nodes,
                        connections: this.connections
                    })
                });
                const resJson = await res.json();

                if (resJson.status === 'success' && resJson.data) {
                    this.latestSimulationResult = resJson.data;
                    const steps = resJson.data.steps || [];
                    const timeMs = resJson.data.execution_time_ms || 180;
                    this.animateSimulationSteps(steps, timeMs);

                    // If simulation modal is open, re-render its content
                    if (this.simulationModalEl && !this.simulationModalEl.classList.contains('is-hidden')) {
                        this.renderSimulationModalContent(this.latestSimulationResult);
                    }
                } else {
                    if (this.testConsoleStepsListEl) {
                        this.testConsoleStepsListEl.innerHTML = `<div class="console-msg-error">Simulation error: ${this.escapeHtml(resJson.msg || 'Unknown error')}</div>`;
                    }
                    this.isSimulating = false;
                }
            } catch (err) {
                console.error('Simulation error:', err);
                if (this.testConsoleStepsListEl) {
                    this.testConsoleStepsListEl.innerHTML = `<div class="console-msg-error">Simulation request failed: ${this.escapeHtml(err.message)}</div>`;
                }
                this.isSimulating = false;
            }
        }

        animateSimulationSteps(steps, totalTimeMs) {
            const timerBadge = document.getElementById('consoleTimerBadge');
            const titleEl = document.getElementById('consoleHeaderTitle');
            if (timerBadge) timerBadge.textContent = `${totalTimeMs}ms`;

            if (this.testConsoleStepsListEl) this.testConsoleStepsListEl.innerHTML = '';

            steps.forEach((step, idx) => {
                const tId = setTimeout(() => {
                    const nodeEl = document.getElementById(`flowNode_${step.node_id}`);
                    if (nodeEl) {
                        nodeEl.classList.add('is-running');
                        setTimeout(() => nodeEl.classList.remove('is-running'), 2000);

                        let badge = nodeEl.querySelector('.flow-node-result-badge');
                        if (!badge) {
                            badge = document.createElement('div');
                            badge.className = 'flow-node-result-badge success';
                            nodeEl.appendChild(badge);
                        }
                        badge.innerHTML = `<i class="fi fi-rr-check"></i> Executed OK (${step.timestamp})`;
                        badge.style.cursor = 'pointer';
                        badge.title = 'Click to view live customer interaction';
                        badge.onclick = (e) => {
                            e.stopPropagation();
                            this.openSimulationInteractionModal();
                        };
                    }

                    if (this.testConsoleStepsListEl) {
                        const row = document.createElement('div');
                        row.className = 'console-step-row';
                        row.style.cursor = 'pointer';
                        row.title = 'Click to inspect step payload and customer preview';
                        row.innerHTML = `
                            <div class="console-step-left">
                                <span class="console-step-icon"><i class="fi fi-rr-check-circle"></i></span>
                                <div>
                                    <span class="console-step-title">Step ${idx + 1}: ${this.escapeHtml(step.title)}</span>
                                    <span class="console-step-msg"> - ${this.escapeHtml(step.message)}</span>
                                </div>
                            </div>
                            <span class="console-step-time">${step.timestamp}</span>
                        `;
                        row.onclick = () => this.openSimulationInteractionModal();
                        this.testConsoleStepsListEl.appendChild(row);
                    }

                    if (idx === steps.length - 1) {
                        this.isSimulating = false;
                        if (titleEl) titleEl.textContent = `Simulation Finished - All ${steps.length} Steps Executed Successfully!`;
                        const statusDot = document.getElementById('consoleStatusDot');
                        if (statusDot) statusDot.className = 'console-status-dot';
                        this.svgConnectionsGroupEl.querySelectorAll('.flow-connector-path').forEach(p => p.classList.remove('is-running'));
                    }
                }, (idx + 1) * 350);

                this.simulationTimeouts.push(tId);
            });
        }

        /* ==========================================================================
           Interactive Simulation & Customer Experience Modal
           ========================================================================== */
        openSimulationInteractionModal() {
            if (!this.simulationModalEl) return;
            this.simulationModalEl.classList.remove('is-hidden');
            if (this.latestSimulationResult) {
                this.renderSimulationModalContent(this.latestSimulationResult);
            } else {
                this.triggerSimulationRun();
            }
        }

        closeSimulationInteractionModal() {
            if (this.simulationModalEl) {
                this.simulationModalEl.classList.add('is-hidden');
            }
        }

        renderSimulationModalContent(data) {
            if (!data) return;
            const steps = data.steps || [];
            const payload = data.test_payload || {};
            const timeMs = data.execution_time_ms || 180;

            const timeBadge = document.getElementById('simExecutionTimeBadge');
            if (timeBadge) timeBadge.innerHTML = `<i class="fi fi-rr-time-past"></i> ${timeMs}ms`;

            // Preset selector sync
            const presetSelect = document.getElementById('simulationPresetSelect');
            if (presetSelect && data.selected_preset) {
                presetSelect.value = data.selected_preset;
            }

            // Render Visual Tab
            this.renderSimulationVisualTab(data);

            // Render Trace Tab
            this.renderSimulationTraceTab(steps);

            // Render JSON Tab
            const jsonViewer = document.getElementById('simJsonViewer');
            if (jsonViewer) {
                jsonViewer.textContent = JSON.stringify({
                    simulated_at: data.completed_at || new Date().toLocaleTimeString(),
                    execution_time_ms: timeMs,
                    traveler_context: payload,
                    steps: steps
                }, null, 2);
            }
        }

        renderSimulationVisualTab(data) {
            const container = document.getElementById('simVisualGrid');
            if (!container) return;

            const payload = data.test_payload || {};
            const steps = data.steps || [];

            // Find WhatsApp step if any
            const waStep = steps.find(s => s.details && s.details.message_body);
            // Find Itinerary step if any
            const itinStep = steps.find(s => s.details && s.details.itinerary);
            // Find Agent step if any
            const agentStep = steps.find(s => s.details && s.details.assigned_agent);
            // Find Pipeline step if any
            const stageStep = steps.find(s => s.details && s.details.new_stage);
            // Find Payment step if any
            const paymentStep = steps.find(s => s.details && s.details.link);

            const travelerName = payload.name || 'Aarav Sharma';
            const travelerInitials = travelerName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
            const destination = payload.destination || 'Himachal Vacation';
            const budget = payload.budget || '₹45,000';
            const phone = payload.phone || '+91 98765 43210';
            const source = payload.source || 'Facebook Lead Ads';
            const inquiry = payload.message || 'Looking for personalized tour itinerary';

            // WhatsApp content
            const waMessage = waStep ? waStep.details.message_body : `Namaste ${travelerName}! 🌍✈️ Welcome to Aleya.\n\nThank you for reaching out for your upcoming vacation to ${destination}. Our destination specialist is preparing custom resort recommendations and sightseeing highlights for you right now!`;
            const waHasBrochure = waStep ? waStep.details.attach_brochure : !!itinStep;
            const waItineraryTitle = itinStep ? itinStep.details.itinerary : `${destination} Verified Package`;

            // Agent content
            const agentName = agentStep ? agentStep.details.assigned_agent : 'Rohit Verma';
            const agentRole = agentStep?.details?.designation || 'Senior Destination Consultant';
            const assignMethod = agentStep?.details?.method === 'round_robin' ? 'Round-Robin Engine (Equal Allocation)' : 'Direct Specialist Rule';

            let html = `
                <!-- Column 1: Customer Profile & Lead Routing -->
                <div class="sim-column">
                    <!-- Traveler Inbound Profile Card -->
                    <div class="sim-preview-card">
                        <div class="sim-preview-header">
                            <span class="sim-preview-badge lead"><i class="fi fi-rr-user"></i> Simulated Traveler Inbound</span>
                            <span class="sim-preview-badge crm"><i class="fi fi-rr-bullseye-arrow"></i> ${this.escapeHtml(source)}</span>
                        </div>
                        <div class="lead-profile-head">
                            <div class="lead-avatar">${travelerInitials}</div>
                            <div class="lead-name-wrap">
                                <h4>${this.escapeHtml(travelerName)}</h4>
                                <span>${this.escapeHtml(phone)} • ${this.escapeHtml(payload.email || 'traveler@gmail.com')}</span>
                            </div>
                        </div>
                        <div class="sim-data-grid">
                            <div class="sim-data-item">
                                <strong>Destination</strong>
                                <span>${this.escapeHtml(destination)}</span>
                            </div>
                            <div class="sim-data-item">
                                <strong>Budget</strong>
                                <span>${this.escapeHtml(budget)}</span>
                            </div>
                        </div>
                        <div class="sim-inquiry-box">
                            <strong><i class="fi fi-rr-comment"></i> Customer Query:</strong>
                            <p style="margin:4px 0 0 0;">"${this.escapeHtml(inquiry)}"</p>
                        </div>
                    </div>

                    <!-- CRM Consultant Desk & Task Card -->
                    <div class="sim-preview-card">
                        <div class="sim-preview-header">
                            <span class="sim-preview-badge agent"><i class="fi fi-rr-users-alt"></i> CRM Lead Desk</span>
                            <span class="sim-preview-badge" style="background:#ecfdf5;color:#059669;"><i class="fi fi-rr-check"></i> Allotted</span>
                        </div>
                        <div class="lead-profile-head">
                            <div class="lead-avatar" style="background:linear-gradient(135deg, #0284c7, #38bdf8);">
                                <i class="fi fi-rr-headset" style="font-size:18px;"></i>
                            </div>
                            <div class="lead-name-wrap">
                                <h4>${this.escapeHtml(agentName)}</h4>
                                <span>${this.escapeHtml(agentRole)} • ${this.escapeHtml(assignMethod)}</span>
                            </div>
                        </div>
                        <div class="sim-data-grid">
                            <div class="sim-data-item">
                                <strong>Pipeline Status</strong>
                                <span style="color:#059669;"><i class="fi fi-rr-flag"></i> ${this.escapeHtml(stageStep?.details?.new_stage || 'Assigned to Specialist')}</span>
                            </div>
                            <div class="sim-data-item">
                                <strong>Next Action</strong>
                                <span><i class="fi fi-rr-calendar"></i> Discovery Call (Due in 2h)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: WhatsApp Live Customer Phone Mockup -->
                <div class="sim-column">
                    <div class="whatsapp-phone-mockup">
                        <div class="phone-status-bar">
                            <span>9:41</span>
                            <span><i class="fi fi-rr-wifi"></i> <i class="fi fi-rr-signal-alt-2"></i> 100%</span>
                        </div>
                        <div class="whatsapp-chat-header">
                            <div class="wa-header-avatar">A</div>
                            <div class="wa-header-info">
                                <h5>Aleya Concierge Desk</h5>
                                <span>Online</span>
                            </div>
                            <div style="display:flex;gap:12px;font-size:15px;">
                                <i class="fi fi-rr-video-camera-alt"></i>
                                <i class="fi fi-rr-phone-call"></i>
                            </div>
                        </div>
                        <div class="whatsapp-chat-canvas">
                            <span class="wa-date-pill">TODAY</span>
                            
                            <div class="wa-bubble-outgoing">
                                <div>${this.escapeHtml(waMessage)}</div>
                                
                                ${waHasBrochure ? `
                                    <div class="wa-attachment-box">
                                        <div class="wa-attachment-icon"><i class="fi fi-rr-document"></i></div>
                                        <div class="wa-attachment-text">
                                            <h6>${this.escapeHtml(waItineraryTitle)}.pdf</h6>
                                            <span>6 Pages • 2.4 MB • Verified Itinerary</span>
                                        </div>
                                    </div>
                                ` : ''}

                                ${paymentStep ? `
                                    <div class="wa-attachment-box" style="border-left: 3px solid #10b981;">
                                        <div class="wa-attachment-icon" style="background:#ecfdf5;color:#059669;"><i class="fi fi-rr-credit-card"></i></div>
                                        <div class="wa-attachment-text">
                                            <h6>Token Deposit Link: ₹${paymentStep.details.amount || '5,000'}</h6>
                                            <span>Instant Razorpay & UPI Checkout</span>
                                        </div>
                                    </div>
                                ` : ''}

                                <div class="wa-bubble-footer">
                                    <span>${data.completed_at || 'Just now'}</span>
                                    <i class="fi fi-rr-check-double"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            container.innerHTML = html;
        }

        renderSimulationTraceTab(steps) {
            const list = document.getElementById('simTraceList');
            if (!list) return;

            if (steps.length === 0) {
                list.innerHTML = '<div style="text-align:center;padding:30px;color:#94a3b8;">No steps traced in this run.</div>';
                return;
            }

            let html = '';
            steps.forEach((step, idx) => {
                const icon = step.friendly_icon || (step.status === 'success' ? 'fi-rr-check-circle' : 'fi-rr-info');
                const color = step.friendly_color || (step.status === 'success' ? '#10b981' : '#f59e0b');
                const headline = step.friendly_headline || step.title;
                const summary = step.friendly_summary || step.message;
                const pill = step.friendly_pill ? `<span class="sim-preview-badge" style="background:#f1f5f9;color:#475569;font-size:11px;padding:3px 8px;border-radius:6px;"><i class="fi ${icon}"></i> ${this.escapeHtml(step.friendly_pill)}</span>` : '';

                html += `
                    <div class="sim-trace-card" style="border-left: 3px solid ${color};">
                        <div class="sim-trace-step-number" style="background:${color};color:#ffffff;">${idx + 1}</div>
                        <div class="sim-trace-content">
                            <div class="sim-trace-head">
                                <span class="sim-trace-title">${this.escapeHtml(headline)}</span>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    ${pill}
                                    <span class="sim-trace-time">${this.escapeHtml(step.timestamp)}</span>
                                </div>
                            </div>
                            <div class="sim-trace-msg">${this.escapeHtml(summary)}</div>
                        </div>
                    </div>
                `;
            });
            list.innerHTML = html;
        }

        /* ==========================================================================
           Execution Logs Modal
           ========================================================================== */
        async openExecutionLogsModal(workflowId) {
            if (!this.executionLogsModalEl) return;
            this.executionLogsModalEl.classList.remove('is-hidden');
            const tbody = document.getElementById('logsTableBody');
            if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="logs-table-empty-cell"><i class="fi fi-rr-spinner btn-loading-spinner"></i> Loading execution logs...</td></tr>`;

            try {
                const res = await fetch(`${this.apiUrl}?action=logs&id=${workflowId}`, { cache: 'no-store' });
                const json = await res.json();
                if (json.status === 'success') {
                    const logs = json.data || [];
                    if (logs.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="6" class="logs-table-empty-cell">No execution logs found yet. Click 'Run Once' in the flowchart editor to test-run.</td></tr>`;
                        return;
                    }

                    let rowsHtml = '';
                    logs.forEach(l => {
                        rowsHtml += `
                            <tr>
                                <td>${new Date(l.created_at).toLocaleString()}</td>
                                <td><code>${this.escapeHtml(l.trigger_event || 'event')}</code></td>
                                <td><span class="log-status-pill success">${this.escapeHtml(l.status)}</span></td>
                                <td>${l.steps_executed} steps</td>
                                <td>${l.execution_time_ms} ms</td>
                                <td><span class="logs-details-meta">${l.details?.length || 0} step outputs logged</span></td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = rowsHtml;
                }
            } catch (err) {
                console.error('Error fetching logs:', err);
            }
        }

        /* ==========================================================================
           Modals & Utilities
           ========================================================================== */
        openChoiceModal() {
            if (this.createChoiceModalEl) this.createChoiceModalEl.classList.remove('is-hidden');
        }

        closeChoiceModal() {
            if (this.createChoiceModalEl) this.createChoiceModalEl.classList.add('is-hidden');
        }

        openTemplatesModal() {
            if (this.templatesModalEl) this.templatesModalEl.classList.remove('is-hidden');
        }

        closeTemplatesModal() {
            if (this.templatesModalEl) this.templatesModalEl.classList.add('is-hidden');
        }

        showToast(msg, type = 'info') {
            if (typeof window.swal === 'function') {
                window.swal({
                    title: type === 'success' ? 'Success' : (type === 'error' ? 'Notice' : 'Information'),
                    text: msg,
                    type: type,
                    timer: 2200,
                    showConfirmButton: false
                });
            } else {
                console.log(`[Toast ${type}]: ${msg}`);
            }
        }

        escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        initDrawerSelects() {
            const $ = window.jQuery || window.$;
            if (!$ || typeof $.fn.select2 !== 'function') return;

            const $drawer = $('#nodeConfigDrawer');
            if (!$drawer.length) return;

            const $selects = $drawer.find('select.selectjs2, select.selectJS2, select.drawer-select');
            if (!$selects.length) return;

            $selects.each(function () {
                const $sel = $(this);
                if ($sel.data('select2') || $sel.hasClass('select2-hidden-accessible')) {
                    return;
                }
                const ph = $sel.attr('data-placeholder') || $sel.attr('placeholder') || 'Select option';
                try {
                    $sel.select2({
                        width: '100%',
                        placeholder: ph,
                        dropdownAutoWidth: true,
                        minimumResultsForSearch: 6
                    });
                } catch (e) {
                    try {
                        $sel.select2({ width: '100%' });
                    } catch (err) {}
                }
            });
        }

        destroyDrawerSelects(destroyAll = true) {
            const $ = window.jQuery || window.$;
            if (!$ || typeof $.fn.select2 !== 'function') return;
            const $selects = $('#nodeConfigDrawer').find('select.selectjs2, select.selectJS2, select.drawer-select');
            $selects.each(function () {
                const $sel = $(this);
                if ($sel.data('select2') || $sel.hasClass('select2-hidden-accessible')) {
                    try {
                        if (destroyAll) {
                            $sel.select2('destroy');
                        } else {
                            $sel.select2('close');
                        }
                    } catch (e) {}
                }
            });
        }

        destroy() {
            this.destroyDrawerSelects(true);
            // Clean up window listeners
            this.boundWindowListeners.forEach(item => {
                try {
                    item.target.removeEventListener(item.event, item.handler);
                } catch (e) {}
            });
            this.boundWindowListeners = [];
            this.cancelDraftConnection();
            if (this.simulationTimeouts) {
                this.simulationTimeouts.forEach(t => clearTimeout(t));
                this.simulationTimeouts = [];
            }
        }
    }

    // SPA-Aware Global Initializer
    function initWorkflowsApp() {
        const appEl = document.getElementById('workflowsPluginApp');
        if (!appEl) return;

        // If previously instantiated, destroy old instance safely
        if (window.workflowsPluginAppInstance && typeof window.workflowsPluginAppInstance.destroy === 'function') {
            window.workflowsPluginAppInstance.destroy();
        }

        // Initialize fresh instance for the newly mounted DOM container
        window.workflowsPluginAppInstance = new WorkflowsPluginApp();
    }

    // Export globally for SPA Router in app.js
    window.initWorkflowsApp = initWorkflowsApp;
    window.V3Workflows = { init: initWorkflowsApp };

    // 1. Initial page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWorkflowsApp);
    } else {
        initWorkflowsApp();
    }

    // 2. Dashboard SPA Navigation Event (app.js triggers 'spa:loaded' on document)
    if (window.jQuery) {
        window.jQuery(document).off('spa:loaded.v3Workflows').on('spa:loaded.v3Workflows', function () {
            if (document.getElementById('workflowsPluginApp')) {
                initWorkflowsApp();
            }
        });
    } else {
        document.addEventListener('spa:loaded', function () {
            if (document.getElementById('workflowsPluginApp')) {
                initWorkflowsApp();
            }
        });
    }

    // 3. Browser History popstate
    window.addEventListener('popstate', function () {
        if (document.getElementById('workflowsPluginApp')) {
            setTimeout(initWorkflowsApp, 80);
        }
    });

})(window, document);
