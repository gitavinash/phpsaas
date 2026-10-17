/**
 * V3 WhatsApp Chat Automation Controller
 * Path: /app/dashboard/v3/default/desktop/assets/js/whatsapp_automation.js
 */

(function ($) {
  'use strict';

  window.ISW_WhatsAppAutomation = {
    apiUrl: '/api/v3/integration/whatsapp_automation/',
    workflows: [],
    templates: [],
    tourPackages: [],
    packageDestinations: [],
    packageDurations: [],
    currentWorkflowId: 0,
    currentWorkflow: null,
    currentNodes: [],
    selectedNodeIndex: 0,
    canvasZoom: 1.0,
    activeFilter: 'all',
    searchQuery: '',
    initialized: false,

    getDestinationEmoji: function (dest) {
      const d = (dest || '').toLowerCase();
      if (d.includes('manali') || d.includes('shimla') || d.includes('kashmir') || d.includes('himachal')) return '🏔️';
      if (d.includes('beach') || d.includes('goa') || d.includes('kerala') || d.includes('maldives') || d.includes('phuket') || d.includes('bali')) return '🏖️';
      if (d.includes('dubai') || d.includes('singapore') || d.includes('city')) return '🏙️';
      if (d.includes('ladakh') || d.includes('leh') || d.includes('camp') || d.includes('trek')) return '⛺';
      if (d.includes('kutch') || d.includes('gujarat') || d.includes('desert') || d.includes('rajasthan')) return '🏜️';
      if (d.includes('temple') || d.includes('dwarka') || d.includes('somnath') || d.includes('varanasi')) return '🛕';
      if (d.includes('sikkim') || d.includes('darjeeling') || d.includes('northeast')) return '🌿';
      return '📍';
    },

    renderConditionBranchCardHtml: function (node) {
      const targetDesc = node.condition_target === 'last_button' ? 'Selected Button' : (node.condition_target === 'destination' ? 'Lead Destination' : (node.condition_target === 'budget' ? 'Lead Budget' : 'Incoming Message'));
      const matchVal = escapeHtml(node.condition_value || 'Option');
      const op = escapeHtml(node.condition_operator || 'contains');

      // IF TRUE Branch details
      const ifType = node.if_action_type || 'send_itinerary';
      const ifLabel = escapeHtml(node.if_label || 'Execute IF True path');
      const ifMsg = node.if_message ? escapeHtml(node.if_message) : '';
      let ifTypeIcon = 'fi fi-rr-document-signed';
      let ifTypeName = 'Send Itinerary';
      if (ifType === 'reply_text') { ifTypeIcon = 'fi fi-rr-comment-alt'; ifTypeName = 'WhatsApp Reply'; }
      else if (ifType === 'handoff_agent') { ifTypeIcon = 'fi fi-rr-user-headset'; ifTypeName = 'Agent Handoff'; }
      else if (ifType === 'payment_link') { ifTypeIcon = 'fi fi-rr-credit-card'; ifTypeName = 'Payment Link'; }
      else if (ifType === 'next_step') { ifTypeIcon = 'fi fi-rr-arrow-right'; ifTypeName = 'Next Step'; }

      // ELSE FALSE Branch details
      const elseType = node.else_action_type || 'send_itinerary';
      const elseLabel = escapeHtml(node.else_label || 'Execute ELSE path');
      const elseMsg = node.else_message ? escapeHtml(node.else_message) : '';
      let elseTypeIcon = 'fi fi-rr-document-signed';
      let elseTypeName = 'Send Alternative Itinerary';
      if (elseType === 'reply_text') { elseTypeIcon = 'fi fi-rr-comment-alt'; elseTypeName = 'Fallback Reply'; }
      else if (elseType === 'handoff_agent') { elseTypeIcon = 'fi fi-rr-user-headset'; elseTypeName = 'Agent Handoff'; }
      else if (elseType === 'payment_link') { elseTypeIcon = 'fi fi-rr-credit-card'; elseTypeName = 'Payment Link'; }
      else if (elseType === 'next_step') { elseTypeIcon = 'fi fi-rr-arrow-right'; ifTypeName = 'Next Step'; }

      return `
        <div class="wa-condition-rule-box">
          <div class="wa-condition-rule-header">
            <span class="wa-pill active"><i class="fi fi-rr-code-branch"></i> If Rule</span>
            <span class="wa-condition-expr">${targetDesc} <strong>${op}</strong> "<span class="text-primary">${matchVal}</span>"</span>
          </div>
          <div class="wa-condition-branches">
            <div class="wa-branch-path is-if">
              <div class="wa-branch-badge is-true"><i class="fi fi-rr-check"></i> IF TRUE (MATCHES)</div>
              <div class="wa-branch-action-pill is-true"><i class="${ifTypeIcon}"></i> ${ifTypeName}</div>
              <div class="wa-branch-desc">${ifLabel}</div>
              ${ifMsg ? `<div class="wa-branch-msg-bubble">${ifMsg}</div>` : ''}
            </div>
            <div class="wa-branch-path is-else">
              <div class="wa-branch-badge is-false"><i class="fi fi-rr-cross-small"></i> ELSE FALSE (OTHERWISE)</div>
              <div class="wa-branch-action-pill is-false"><i class="${elseTypeIcon}"></i> ${elseTypeName}</div>
              <div class="wa-branch-desc">${elseLabel}</div>
              ${elseMsg ? `<div class="wa-branch-msg-bubble">${elseMsg}</div>` : ''}
            </div>
          </div>
        </div>
      `;
    },

    init: function () {
      if (!$('#whatsappAutomationApp').length) return;
      $('body').removeClass('wa-builder-mode wa-builder-sidebar-open');
      if (!this.initialized) {
        this.bindEvents();
        this.initialized = true;
      }
      this.initSelect2Dropdowns('#whatsappAutomationApp');
      this.loadData();
    },

    initSelect2Dropdowns: function (parentSelector) {
      if (typeof $.fn.select2 !== 'function') return;
      const $context = parentSelector ? $(parentSelector) : $('#whatsappAutomationApp');
      $context.find('select.selectjs2, select.selectJS').each(function () {
        const $el = $(this);
        if ($el.data('select2')) {
          try {
            $el.select2('destroy');
          } catch (e) {}
        }
        const $modal = $el.closest('.wa-modal-backdrop');
        const options = {
          width: '100%',
          dropdownAutoWidth: true
        };
        if ($modal.length) {
          options.dropdownParent = $modal.find('.wa-modal-card');
        }
        $el.select2(options);
      });
    },

    bindEvents: function () {
      const self = this;

      // Tab switching
      $(document)
        .off('click', '.wa-tab-btn')
        .on('click', '.wa-tab-btn', function (e) {
          if (e) e.preventDefault();
          const tab = $(this).data('tab');
          $('.wa-tab-btn').removeClass('active');
          $(this).addClass('active');
          $('.wa-tab-pane').removeClass('active').hide();
          $('#waTab_' + tab).addClass('active').show();

          if (tab === 'logs') {
            self.loadLogs();
          }
        });

      // Master Plugin Switch Toggle
      $(document)
        .off('change', '#togglePluginActiveWa')
        .on('change', '#togglePluginActiveWa', function () {
          const isChecked = $(this).is(':checked');
          self.togglePlugin(isChecked);
        });

      // Refresh Workflows button
      $(document)
        .off('click', '#btnRefreshWaWorkflows')
        .on('click', '#btnRefreshWaWorkflows', function (e) {
          if (e) e.preventDefault();
          self.loadData();
        });

      // Refresh Logs button
      $(document)
        .off('click', '#btnRefreshWaLogs')
        .on('click', '#btnRefreshWaLogs', function (e) {
          if (e) e.preventDefault();
          self.loadLogs();
        });

      // Search input in Workflows tab
      $(document)
        .off('input', '#waWorkflowSearchInput')
        .on('input', '#waWorkflowSearchInput', function () {
          const val = $(this).val();
          self.searchQuery = val;
          $('#waSearchClear').toggleClass('is-hidden', !val || val.trim().length === 0);
          self.renderWorkflowsTable();
        });

      // Clear search button
      $(document)
        .off('click', '#waSearchClear')
        .on('click', '#waSearchClear', function (e) {
          e.preventDefault();
          $('#waWorkflowSearchInput').val('');
          self.searchQuery = '';
          $(this).addClass('is-hidden');
          self.renderWorkflowsTable();
        });

      // Filter chips in Workflows tab
      $(document)
        .off('click', '.wa-filter-chip')
        .on('click', '.wa-filter-chip', function (e) {
          e.preventDefault();
          $('.wa-filter-chip').removeClass('active');
          $(this).addClass('active');
          self.activeFilter = $(this).attr('data-filter') || 'all';
          self.renderWorkflowsTable();
        });

      // Reset filters button
      $(document)
        .off('click', '#btnResetWfFilters')
        .on('click', '#btnResetWfFilters', function (e) {
          e.preventDefault();
          $('#waWorkflowSearchInput').val('');
          self.searchQuery = '';
          $('#waSearchClear').addClass('is-hidden');
          $('.wa-filter-chip').removeClass('active');
          $('.wa-filter-chip[data-filter="all"]').addClass('active');
          self.activeFilter = 'all';
          self.renderWorkflowsTable();
        });

      // Quick test in simulator from list or top toolbar
      $(document)
        .off('click', '#btnListOpenSim, .btn-sim-wa-wf')
        .on('click', '#btnListOpenSim, .btn-sim-wa-wf', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const kw = $(this).attr('data-keyword') || 'itinerary';
          $('.wa-tab-btn[data-tab="simulator"]').trigger('click');
          setTimeout(function () {
            const $input = $('#waSimInput');
            if ($input.length) {
              $input.val(kw).focus();
            }
          }, 150);
        });

      // New Workflow Button (Header button & Empty state button)
      $(document)
        .off('click', '#btnCreateWaWorkflow, #btnEmptyCreateWf, .btn-create-wa-wf')
        .on('click', '#btnCreateWaWorkflow, #btnEmptyCreateWf, .btn-create-wa-wf', function (e) {
          if (e) e.preventDefault();
          self.openVisualBuilder(0);
        });

      // Edit Workflow -> Opens Visual Builder
      $(document)
        .off('click', '.btn-edit-wa-wf')
        .on('click', '.btn-edit-wa-wf', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const id = $(this).attr('data-id') || $(this).data('id');
          self.openVisualBuilder(id);
        });

      // Use Template Button -> Opens Visual Builder with Template preloaded
      $(document)
        .off('click', '.btn-use-wa-template')
        .on('click', '.btn-use-wa-template', function (e) {
          e.preventDefault();
          const templateId = $(this).attr('data-id') || $(this).data('id');
          self.openVisualBuilder(0, templateId);
        });

      // Visual Builder Aside Navigation Toggle Button
      $(document)
        .off('click', '#btnToggleBuilderAside')
        .on('click', '#btnToggleBuilderAside', function (e) {
          e.preventDefault();
          $('body').toggleClass('wa-builder-sidebar-open');
        });

      // Visual Builder Aside Backdrop Click
      $(document)
        .off('click', '#waBuilderSidebarBackdrop')
        .on('click', '#waBuilderSidebarBackdrop', function (e) {
          e.preventDefault();
          $('body').removeClass('wa-builder-sidebar-open');
        });

      // Visual Builder Exit Button (Back to Dashboard)
      $(document)
        .off('click', '#btnExitVisualBuilder')
        .on('click', '#btnExitVisualBuilder', function (e) {
          e.preventDefault();
          self.closeVisualBuilder();
        });

      // Visual Builder Save Button
      $(document)
        .off('click', '#btnSaveVisualWorkflow')
        .on('click', '#btnSaveVisualWorkflow', function (e) {
          e.preventDefault();
          self.saveVisualWorkflow();
        });

      // Visual Builder Status Toggle Switch
      $(document)
        .off('change', '#builderWorkflowActiveToggle')
        .on('change', '#builderWorkflowActiveToggle', function () {
          const isChecked = $(this).is(':checked');
          const $badge = $('#builderStatusBadge');
          if (isChecked) {
            $badge.removeClass('is-inactive').addClass('is-active').text('Active');
          } else {
            $badge.removeClass('is-active').addClass('is-inactive').text('Inactive');
          }
        });

      // Visual Builder Workflow Title Live Sync
      $(document)
        .off('input', '#builderWfTitleInput')
        .on('input', '#builderWfTitleInput', function () {
          const val = $(this).val().trim();
          $('#builderTopWorkflowTitle').text(val || 'Untitled Bot Flow');
        });

      // Canvas Zoom Controls
      $(document)
        .off('click', '#btnCanvasZoomIn')
        .on('click', '#btnCanvasZoomIn', function (e) {
          e.preventDefault();
          self.setZoom(self.canvasZoom + 0.1);
        });

      $(document)
        .off('click', '#btnCanvasZoomOut')
        .on('click', '#btnCanvasZoomOut', function (e) {
          e.preventDefault();
          self.setZoom(self.canvasZoom - 0.1);
        });

      $(document)
        .off('click', '#btnCanvasResetZoom')
        .on('click', '#btnCanvasResetZoom', function (e) {
          e.preventDefault();
          self.setZoom(1.0);
        });

      // Simulator Quick Test Button in Builder Top Bar & Make.com Bottom Dock Run Once
      $(document)
        .off('click', '#btnBuilderTestSim, #btnMakeRunOnce')
        .on('click', '#btnBuilderTestSim, #btnMakeRunOnce', function (e) {
          e.preventDefault();
          self.closeVisualBuilder();
          $('.wa-tab-btn[data-tab="simulator"]').trigger('click');
          self.initSimulatorChat();
        });

      // Make.com Bottom Dock: Auto-Align
      $(document)
        .off('click', '#btnMakeAutoAlign')
        .on('click', '#btnMakeAutoAlign', function (e) {
          e.preventDefault();
          self.setZoom(1.0);
          $('#waFlowCanvasScroll').animate({ scrollLeft: 0, scrollTop: 0 }, 250);
        });

      // Make.com Bottom Dock: Save Workflow
      $(document)
        .off('click', '#btnMakeSaveWorkflow')
        .on('click', '#btnMakeSaveWorkflow', function (e) {
          e.preventDefault();
          $('#btnSaveVisualWorkflow').trigger('click');
        });

      // 1-Click Quick Automation Recipes Strip Click
      $(document)
        .off('click', '.wa-quick-recipe-btn')
        .on('click', '.wa-quick-recipe-btn', function (e) {
          e.preventDefault();
          const recipeKey = $(this).data('recipe');
          self.loadQuickRecipe(recipeKey);
        });

      // Canvas Quick Add / End Button / Connector Plus / Starting Point Plus
      $(document)
        .off('click', '#btnCanvasAddStepQuick, .wa-flow-end-btn, .wa-insert-step-btn')
        .on('click', '#btnCanvasAddStepQuick, .wa-flow-end-btn, .wa-insert-step-btn', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const rawIdx = $(this).data('insert-index');
          const insertIdx = (typeof rawIdx !== 'undefined' && rawIdx !== null) ? parseInt(rawIdx, 10) : self.currentNodes.length;
          self.openActionPicker(insertIdx);
        });

      // Close Action Picker Modal
      $(document)
        .off('click', '#btnCloseActionPicker')
        .on('click', '#btnCloseActionPicker', function (e) {
          e.preventDefault();
          self.closeActionPicker();
        });

      $(document)
        .off('click', '#waActionPickerModal')
        .on('click', '#waActionPickerModal', function (e) {
          if (e.target === this) {
            e.preventDefault();
            self.closeActionPicker();
          }
        });

      // Action Card Click to Insert
      $(document)
        .off('click', '.wa-action-card')
        .on('click', '.wa-action-card', function (e) {
          e.preventDefault();
          const type = $(this).data('action-type');
          const preset = $(this).data('action-preset');
          if (type) {
            self.addNewNode(type, self.pendingInsertIndex, preset);
            self.closeActionPicker();
          }
        });

      // Action Picker Category Filter Buttons
      $(document)
        .off('click', '.wa-picker-cat-btn')
        .on('click', '.wa-picker-cat-btn', function (e) {
          e.preventDefault();
          $('.wa-picker-cat-btn').removeClass('active');
          $(this).addClass('active');
          const cat = $(this).data('cat') || 'all';
          const q = $.trim($('#actionPickerSearchInput').val()).toLowerCase();
          self.filterActionCards(cat, q);
        });

      // Action Picker Search Input
      $(document)
        .off('input', '#actionPickerSearchInput')
        .on('input', '#actionPickerSearchInput', function () {
          const q = $.trim($(this).val()).toLowerCase();
          const cat = $('.wa-picker-cat-btn.active').data('cat') || 'all';
          self.filterActionCards(cat, q);
        });

      // Palette Item Click to Append Step
      $(document)
        .off('click', '.wa-palette-item')
        .on('click', '.wa-palette-item', function (e) {
          e.preventDefault();
          const type = $(this).data('type');
          self.addNewNode(type);
        });

      // Palette Drag and Drop onto Canvas
      $(document)
        .off('dragstart', '.wa-palette-item')
        .on('dragstart', '.wa-palette-item', function (e) {
          const type = $(this).data('type');
          if (e.originalEvent && e.originalEvent.dataTransfer) {
            e.originalEvent.dataTransfer.setData('text/palette-type', type);
          }
        });

      // Canvas Dropzone Handling
      $(document)
        .off('dragover', '.wa-flow-dropzone, #waFlowCanvasScroll')
        .on('dragover', '.wa-flow-dropzone, #waFlowCanvasScroll', function (e) {
          e.preventDefault();
          if ($(this).hasClass('wa-flow-dropzone')) {
            $(this).addClass('drag-over');
          }
        });

      $(document)
        .off('dragleave', '.wa-flow-dropzone')
        .on('dragleave', '.wa-flow-dropzone', function () {
          $(this).removeClass('drag-over');
        });

      $(document)
        .off('drop', '.wa-flow-dropzone, #waFlowCanvasScroll')
        .on('drop', '.wa-flow-dropzone, #waFlowCanvasScroll', function (e) {
          e.preventDefault();
          $('.wa-flow-dropzone').removeClass('drag-over');
          const dt = e.originalEvent ? e.originalEvent.dataTransfer : null;
          if (!dt) return;

          const paletteType = dt.getData('text/palette-type');
          const reorderIdxStr = dt.getData('text/node-index');

          if (paletteType) {
            let insertIdx = -1;
            if ($(this).hasClass('wa-flow-dropzone')) {
              insertIdx = parseInt($(this).data('insert-index'), 10);
            }
            self.addNewNode(paletteType, insertIdx >= 0 ? insertIdx : undefined);
          } else if (reorderIdxStr) {
            const fromIdx = parseInt(reorderIdxStr, 10);
            let toIdx = -1;
            if ($(this).hasClass('wa-flow-dropzone')) {
              toIdx = parseInt($(this).data('insert-index'), 10);
            }
            if (!isNaN(fromIdx) && toIdx >= 0 && fromIdx !== toIdx) {
              self.reorderNodes(fromIdx, toIdx);
            }
          }
        });

      // Canvas Node Selection Click
      $(document)
        .off('click', '.wa-flow-node-card, .wa-flow-step-card')
        .on('click', '.wa-flow-node-card, .wa-flow-step-card', function (e) {
          if ($(e.target).closest('.wa-node-actions, .wa-make-module-actions, .wa-step-header-actions, .wa-insert-step-btn, .wa-flow-add-step-btn').length) return;
          const idx = parseInt($(this).data('index'), 10);
          self.selectNode(idx);
        });

      // Canvas Node Action: Inspect Trigger / Module Settings
      $(document)
        .off('click', '.btn-node-inspect')
        .on('click', '.btn-node-inspect', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const idx = parseInt($(this).data('index'), 10);
          self.selectNode(idx);
        });

      // Canvas Node Action: Delete
      $(document)
        .off('click', '.btn-node-delete')
        .on('click', '.btn-node-delete', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const idx = parseInt($(this).data('index'), 10);
          self.deleteNode(idx);
        });

      // Canvas Node Action: Duplicate
      $(document)
        .off('click', '.btn-node-dup')
        .on('click', '.btn-node-dup', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const idx = parseInt($(this).data('index'), 10);
          self.duplicateNode(idx);
        });

      // Node Drag Reorder
      $(document)
        .off('dragstart', '.wa-flow-node-card')
        .on('dragstart', '.wa-flow-node-card', function (e) {
          const idx = parseInt($(this).data('index'), 10);
          if (idx === 0) {
            e.preventDefault();
            return;
          }
          $(this).addClass('is-dragging');
          if (e.originalEvent && e.originalEvent.dataTransfer) {
            e.originalEvent.dataTransfer.setData('text/node-index', String(idx));
          }
        });

      $(document)
        .off('dragend', '.wa-flow-node-card')
        .on('dragend', '.wa-flow-node-card', function () {
          $(this).removeClass('is-dragging');
          $('.wa-flow-dropzone').removeClass('drag-over');
        });

      // Inspector Live Inputs: Trigger Type
      $(document)
        .off('change', '#inspTriggerType')
        .on('change', '#inspTriggerType', function () {
          const val = $(this).val();
          const node = self.currentNodes[0];
          if (!node) return;
          node.trigger_type = val;
          if (val === 'default_fallback') {
            $('#inspKeywordsGroup').slideUp(120);
            $('#inspIsDefault').prop('checked', true);
            node.is_default = 1;
            node.title = 'Default Fallback Responder';
          } else {
            $('#inspKeywordsGroup').slideDown(120);
            $('#inspIsDefault').prop('checked', false);
            node.is_default = 0;
            node.title = 'Incoming Keyword Trigger';
          }
          self.updateNodeCardPreview(0);
        });

      // Inspector Live Inputs: Trigger Keywords
      $(document)
        .off('input', '#inspKeywords')
        .on('input', '#inspKeywords', function () {
          const val = $(this).val();
          const node = self.currentNodes[0];
          if (!node) return;
          node.keywords = val;
          self.updateNodeCardPreview(0);
        });

      // Inspector Live Inputs: Trigger Match Type
      $(document)
        .off('change', '#inspMatchType')
        .on('change', '#inspMatchType', function () {
          const val = $(this).val();
          const node = self.currentNodes[0];
          if (!node) return;
          node.match_type = val;
          self.updateNodeCardPreview(0);
        });

      // Inspector Live Inputs: Trigger Fallback Toggle
      $(document)
        .off('change', '#inspIsDefault')
        .on('change', '#inspIsDefault', function () {
          const isChecked = $(this).is(':checked');
          const node = self.currentNodes[0];
          if (!node) return;
          node.is_default = isChecked ? 1 : 0;
          if (isChecked) {
            $('#inspTriggerType').val('default_fallback').trigger('change');
          } else {
            $('#inspTriggerType').val('keyword').trigger('change');
          }
          self.updateNodeCardPreview(0);
        });

      // Inspector Live Inputs: Step Title
      $(document)
        .off('input', '#inspStepTitle')
        .on('input', '#inspStepTitle', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.title = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Live Inputs: Reply Text
      $(document)
        .off('input', '#inspReplyText')
        .on('input', '#inspReplyText', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.message_text = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Token Pill Click
      $(document)
        .off('click', '.wa-token-pill')
        .on('click', '.wa-token-pill', function (e) {
          e.preventDefault();
          const token = $(this).data('token');
          const $textarea = $('#inspReplyText');
          const elem = $textarea[0];
          if (!elem) return;
          const start = elem.selectionStart || 0;
          const end = elem.selectionEnd || 0;
          const val = $textarea.val();
          $textarea.val(val.substring(0, start) + token + val.substring(end));
          elem.selectionStart = elem.selectionEnd = start + token.length;
          $textarea.focus();
          $textarea.trigger('input');
        });

      // Inspector Live Inputs: Delay Seconds
      $(document)
        .off('input change', '#inspDelaySeconds')
        .on('input change', '#inspDelaySeconds', function () {
          const val = parseInt($(this).val(), 10) || 1;
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.delay_seconds = val;
          $('#inspDelayValLabel').text(val + 's');
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Quick Replies Add Option
      $(document)
        .off('click', '#btnAddQuickReplyChoice')
        .on('click', '#btnAddQuickReplyChoice', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          if (!Array.isArray(node.options)) node.options = [];
          node.options.push('Option ' + (node.options.length + 1));
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Quick Replies Option Input
      $(document)
        .off('input', '.insp-qr-input')
        .on('input', '.insp-qr-input', function () {
          const optIdx = parseInt($(this).data('option-index'), 10);
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node || !Array.isArray(node.options)) return;
          node.options[optIdx] = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Quick Replies Option Delete
      $(document)
        .off('click', '.insp-qr-del')
        .on('click', '.insp-qr-del', function (e) {
          e.preventDefault();
          const optIdx = parseInt($(this).data('option-index'), 10);
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node || !Array.isArray(node.options)) return;
          node.options.splice(optIdx, 1);
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Handoff Department
      $(document)
        .off('change', '#inspHandoffDept')
        .on('change', '#inspHandoffDept', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.department = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Handoff Note
      $(document)
        .off('input', '#inspHandoffNote')
        .on('input', '#inspHandoffNote', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.note = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Live Inputs: Collect Field
      $(document)
        .off('change', '#inspCollectField')
        .on('change', '#inspCollectField', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.field_key = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Live Inputs: Itinerary Title & URL
      $(document)
        .off('input', '#inspItinTitle')
        .on('input', '#inspItinTitle', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.itinerary_title = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('input', '#inspItinUrl')
        .on('input', '#inspItinUrl', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.itinerary_url = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Live Inputs: Payment Amount & Currency
      $(document)
        .off('input', '#inspPaymentAmount')
        .on('input', '#inspPaymentAmount', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.amount = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('change', '#inspPaymentCurrency')
        .on('change', '#inspPaymentCurrency', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.currency = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Live Inputs: Lead Stage & Tags
      $(document)
        .off('change', '#inspLeadStage')
        .on('change', '#inspLeadStage', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.lead_stage = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('input', '#inspLeadTags')
        .on('input', '#inspLeadTags', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.tags = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Collect Mode Radio Switch (Buttons vs Text)
      $(document)
        .off('change', 'input[name="inspCollectMode"]')
        .on('change', 'input[name="inspCollectMode"]', function () {
          const mode = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.input_mode = mode;
          if (mode === 'buttons') {
            $('#inspCollectButtonsWrap').removeClass('is-hidden');
            if (!Array.isArray(node.options) || !node.options.length) {
              const realDests = (self.packageDestinations && self.packageDestinations.length > 0)
                ? self.packageDestinations.slice(0, 4).map(d => `${self.getDestinationEmoji(d)} ${d}`)
                : ['🏔️ Manali & Himachal', '🏖️ Kerala & Munnar', '🏙️ Dubai Luxury', '⛺ Ladakh Adventure'];
              node.options = realDests;
            }
          } else {
            $('#inspCollectButtonsWrap').addClass('is-hidden');
          }
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Collect Options Add
      $(document)
        .off('click', '#btnAddCollectOptBtn')
        .on('click', '#btnAddCollectOptBtn', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          if (!Array.isArray(node.options)) node.options = [];
          node.options.push('Option ' + (node.options.length + 1));
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Real Tour Package Destination Chip Click -> Adds Option
      $(document)
        .off('click', '.wa-dest-chip')
        .on('click', '.wa-dest-chip', function (e) {
          e.preventDefault();
          const dest = $(this).data('dest');
          if (!dest) return;
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          if (!Array.isArray(node.options)) node.options = [];
          const optVal = `${self.getDestinationEmoji(dest)} ${dest}`;
          if (!node.options.includes(optVal) && !node.options.includes(dest)) {
            node.options.push(optVal);
          }
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Quick insert tour package as option
      $(document)
        .off('click', '#btnInsertPackageOption')
        .on('click', '#btnInsertPackageOption', function (e) {
          e.preventDefault();
          const pkgId = parseInt($('#inspCollectPackagePick').val(), 10);
          if (!pkgId) return;
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          const pkg = (self.tourPackages || []).find(p => p.id === pkgId);
          if (!pkg) return;
          if (!Array.isArray(node.options)) node.options = [];
          const optLabel = `${self.getDestinationEmoji(pkg.city || pkg.state)} ${pkg.title}`;
          node.options.push(optLabel);
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Collect Options Input
      $(document)
        .off('input', '.insp-collect-opt-input')
        .on('input', '.insp-collect-opt-input', function () {
          const optIdx = parseInt($(this).data('option-index'), 10);
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node || !Array.isArray(node.options)) return;
          node.options[optIdx] = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Collect Options Delete
      $(document)
        .off('click', '.insp-collect-opt-del')
        .on('click', '.insp-collect-opt-del', function (e) {
          e.preventDefault();
          const optIdx = parseInt($(this).data('option-index'), 10);
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node || !Array.isArray(node.options)) return;
          node.options.splice(optIdx, 1);
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Collect Presets: Destinations (Pulls real tour package destinations)
      $(document)
        .off('click', '.btn-pack-dest')
        .on('click', '.btn-pack-dest', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          const realDests = (self.packageDestinations && self.packageDestinations.length > 0)
            ? self.packageDestinations.slice(0, 4).map(d => `${self.getDestinationEmoji(d)} ${d}`)
            : ['🏔️ Manali & Himachal', '🏖️ Kerala & Munnar', '🏙️ Dubai Luxury', '⛺ Ladakh Adventure'];
          node.options = realDests;
          node.field_key = 'destination';
          $('#inspCollectField').val('destination');
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Collect Presets: Durations (Pulls real package durations)
      $(document)
        .off('click', '.btn-pack-dur')
        .on('click', '.btn-pack-dur', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          const realDurs = (self.packageDurations && self.packageDurations.length > 0)
            ? self.packageDurations.slice(0, 4).map(d => `🗓️ ${d}`)
            : ['🗓️ 3 Nights / 4 Days', '🗓️ 5 Nights / 6 Days', '🗓️ 7 Nights / 8 Days', '🗓️ 10 Nights / 11 Days'];
          node.options = realDurs;
          node.field_key = 'travel_dates';
          $('#inspCollectField').val('travel_dates');
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Collect Presets: Budgets
      $(document)
        .off('click', '.btn-pack-budg')
        .on('click', '.btn-pack-budg', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.options = ['💰 Economy (< ₹40k)', '💰 Premium (₹40k - ₹80k)', '💎 Luxury (₹80k - ₹1.5L)', '👑 Elite Ultra-Luxury'];
          node.field_key = 'budget';
          $('#inspCollectField').val('budget');
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Collect Presets: Pax
      $(document)
        .off('click', '.btn-pack-pax')
        .on('click', '.btn-pack-pax', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.options = ['💑 Couple / 2 Pax', '👨‍👩‍👧 Family (3-4 Pax)', '👥 Group Tour (5-10 Pax)', '🏢 Corporate (10+ Pax)'];
          node.field_key = 'pax';
          $('#inspCollectField').val('pax');
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Collect Presets: Travel Dates
      $(document)
        .off('click', '.btn-pack-dates')
        .on('click', '.btn-pack-dates', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.field_key = 'travel_dates';
          node.title = 'Select Travel Dates / Month';
          node.message_text = 'When are you planning to travel? Choose a preferred travel window:';
          node.options = ['🗓️ Next Month', '☀️ Summer Vacation', '❄️ Winter / Year-End', '✨ Flexible Dates'];
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Collect Presets: Hotel Preferences
      $(document)
        .off('click', '.btn-pack-hotel')
        .on('click', '.btn-pack-hotel', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.field_key = 'hotel_preference';
          node.title = 'Hotel Star Category';
          node.message_text = 'Which hotel accommodation category do you prefer for your stay?';
          node.options = ['⭐ 3-Star Comfortable', '⭐⭐ 4-Star Premium', '⭐⭐⭐ 5-Star Luxury Resort'];
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Collect Presets: Flights
      $(document)
        .off('click', '.btn-pack-flights')
        .on('click', '.btn-pack-flights', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.field_key = 'custom_requirement';
          node.title = 'Flights & Airport Transfers';
          node.message_text = 'Do you need flight tickets and airport transfers included in your holiday package?';
          node.options = ['✈️ Yes, Flights + Transfers Included', '🚗 Only Local Transfers', '🏨 Only Hotel & Sightseeing'];
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Reply Text: Quick Message Templates
      $(document)
        .off('click', '.btn-apply-tmpl')
        .on('click', '.btn-apply-tmpl', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          const tmpl = $(this).data('tmpl');
          if (tmpl === 'welcome') {
            node.message_text = 'Hello {name}! 👋 Thank you for contacting our travel desk. How can we help you plan your dream holiday today?';
          } else if (tmpl === 'after_hours') {
            node.message_text = 'Hello {name}! Our travel specialists are currently off-duty (Operating hours: 9:30 AM - 7:00 PM). We have received your query and will contact you first thing tomorrow morning! 🌅';
          } else if (tmpl === 'office_location') {
            node.message_text = '📍 Our head office is located at DLF Cyber City, Tower B, 5th Floor, Gurugram. Feel free to walk in between 10 AM and 6 PM on weekdays!';
          } else if (tmpl === 'callback') {
            node.message_text = 'Sure thing! 📞 A senior holiday advisor will call you shortly on {phone}. Please let us know if there is a specific time you prefer to talk.';
          } else if (tmpl === 'thank_you') {
            node.message_text = 'Thank you {name}! 🙏 Your requirement has been registered. We are preparing a customized holiday proposal and will share it shortly.';
          }
          $('#inspReplyText').val(node.message_text);
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Interactive Menu: Predefined Menu Sets
      $(document)
        .off('click', '.btn-menu-preset')
        .on('click', '.btn-menu-preset', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          const preset = $(this).data('preset');
          if (preset === 'main') {
            node.message_text = 'Welcome to our Travel Concierge! 👋 Please select an option:';
            node.options = ['1. 🗺️ View Tour Packages', '2. 💰 Get Instant Quote', '3. 👩‍💼 Talk to Travel Specialist'];
          } else if (preset === 'tour_type') {
            node.message_text = 'What type of vacation experience are you looking for?';
            node.options = ['💑 Honeymoon & Romance', '👨‍👩‍👧 Family Holiday', '⛺ Trekking & Adventure', '🧘 Wellness & Retreat'];
          } else if (preset === 'support') {
            node.message_text = 'How can our customer support team assist you today?';
            node.options = ['1. 📋 Existing Booking Status', '2. 🔄 Change Travel Dates', '3. 👩‍💼 Connect with Agent'];
          } else if (preset === 'yes_no') {
            node.message_text = 'Would you like our advisor to proceed with this custom proposal?';
            node.options = ['✅ Yes, Looks Great!', '✏️ Request Modifications', '❌ No, Looking for Something Else'];
          }
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Tag Lead: Predefined CRM Stage & Tag Bundles
      $(document)
        .off('click', '.btn-tag-preset')
        .on('click', '.btn-tag-preset', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.lead_stage = $(this).data('stage');
          node.tags = $(this).data('tags');
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Condition Branch: Predefined Rules
      $(document)
        .off('click', '.btn-cond-preset')
        .on('click', '.btn-cond-preset', function (e) {
          e.preventDefault();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.condition_target = $(this).data('target');
          node.condition_operator = 'contains';
          node.condition_value = $(this).data('val');
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Inspector Live Inputs: Condition Branch Rule
      $(document)
        .off('change', '#inspCondTarget')
        .on('change', '#inspCondTarget', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.condition_target = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('change', '#inspCondOp')
        .on('change', '#inspCondOp', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.condition_operator = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('input', '#inspCondVal')
        .on('input', '#inspCondVal', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.condition_value = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Condition Value quick suggestions chip
      $(document)
        .off('click', '.wa-cond-val-chip')
        .on('click', '.wa-cond-val-chip', function (e) {
          e.preventDefault();
          const val = $(this).data('val');
          if (!val) return;
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.condition_value = val;
          $('#inspCondVal').val(val);
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // DECISION: TRUE Condition Path
      $(document)
        .off('change', '#inspCondIfActionType')
        .on('change', '#inspCondIfActionType', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.if_action_type = val;
          if (val === 'send_itinerary' && !node.if_package_id && self.tourPackages && self.tourPackages.length > 0) {
            const p = self.tourPackages[0];
            node.if_package_id = p.id;
            node.if_label = 'Send ' + p.title + ' PDF';
            node.if_itinerary_title = p.title;
            node.if_message = 'Here is your tailored ' + p.title + ' itinerary PDF with verified hotel options and sightseeing included! 📄✨';
          } else if (val === 'reply_text' && !node.if_message) {
            node.if_label = 'Send Confirmation Message';
            node.if_message = 'Great choice! We have tailored travel options specifically matching your preferences.';
          } else if (val === 'handoff_agent') {
            node.if_label = 'Transfer to Destination Specialist';
            node.if_department = 'Destination Specialist';
          } else if (val === 'payment_link') {
            node.if_label = 'Send Advance Token Deposit Link';
            node.if_amount = 5000;
          }
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('change', '#inspCondIfPackageId')
        .on('change', '#inspCondIfPackageId', function () {
          const pkgId = parseInt($(this).val(), 10);
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.if_package_id = pkgId;
          const pkg = (self.tourPackages || []).find(p => p.id === pkgId);
          if (pkg) {
            node.if_itinerary_title = pkg.title;
            node.if_label = 'Send ' + pkg.title + ' PDF';
            node.if_message = 'Here is your tailored ' + pkg.title + ' itinerary PDF with verified hotel options, transfers, and sightseeing included! 📄✨';
            $('#inspCondIfLabel').val(node.if_label);
            $('#inspCondIfMsg').val(node.if_message);
          }
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('input', '#inspCondIfLabel')
        .on('input', '#inspCondIfLabel', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.if_label = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('input', '#inspCondIfMsg')
        .on('input', '#inspCondIfMsg', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.if_message = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('change', '#inspCondIfDept')
        .on('change', '#inspCondIfDept', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.if_department = val;
          node.if_label = 'Transfer to ' + val + ' Desk';
          $('#inspCondIfLabel').val(node.if_label);
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('input', '#inspCondIfAmount')
        .on('input', '#inspCondIfAmount', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.if_amount = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // DECISION: FALSE Condition Path (ELSE / OTHERWISE)
      $(document)
        .off('change', '#inspCondElseActionType')
        .on('change', '#inspCondElseActionType', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.else_action_type = val;
          if (val === 'send_itinerary' && !node.else_package_id && self.tourPackages && self.tourPackages.length > 1) {
            const p = self.tourPackages[1] || self.tourPackages[0];
            node.else_package_id = p.id;
            node.else_label = 'Send ' + p.title + ' PDF';
            node.else_itinerary_title = p.title;
            node.else_message = 'Here is our alternative curated ' + p.title + ' holiday package itinerary with complete details! 📄';
          } else if (val === 'reply_text' && !node.else_message) {
            node.else_label = 'Send Alternative Offer Message';
            node.else_message = 'Thank you for sharing your preference! Our travel specialist will curate an itinerary matching your exact needs.';
          } else if (val === 'handoff_agent') {
            node.else_label = 'Transfer to Support Desk';
            node.else_department = 'Support Desk';
          } else if (val === 'payment_link') {
            node.else_label = 'Send Booking Deposit Link';
            node.else_amount = 5000;
          }
          self.renderPropertyInspector();
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('change', '#inspCondElsePackageId')
        .on('change', '#inspCondElsePackageId', function () {
          const pkgId = parseInt($(this).val(), 10);
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.else_package_id = pkgId;
          const pkg = (self.tourPackages || []).find(p => p.id === pkgId);
          if (pkg) {
            node.else_itinerary_title = pkg.title;
            node.else_label = 'Send ' + pkg.title + ' PDF';
            node.else_message = 'Here is our alternative curated ' + pkg.title + ' holiday package itinerary with complete details! 📄';
            $('#inspCondElseLabel').val(node.else_label);
            $('#inspCondElseMsg').val(node.else_message);
          }
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('input', '#inspCondElseLabel')
        .on('input', '#inspCondElseLabel', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.else_label = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('input', '#inspCondElseMsg')
        .on('input', '#inspCondElseMsg', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.else_message = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('change', '#inspCondElseDept')
        .on('change', '#inspCondElseDept', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.else_department = val;
          node.else_label = 'Transfer to ' + val + ' Desk';
          $('#inspCondElseLabel').val(node.else_label);
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      $(document)
        .off('input', '#inspCondElseAmount')
        .on('input', '#inspCondElseAmount', function () {
          const val = $(this).val();
          const node = self.currentNodes[self.selectedNodeIndex];
          if (!node) return;
          node.else_amount = val;
          self.updateNodeCardPreview(self.selectedNodeIndex);
        });

      // Simulator Clickable Button Choice
      $(document)
        .off('click', '.wa-sim-click-choice')
        .on('click', '.wa-sim-click-choice', function (e) {
          e.preventDefault();
          const choiceText = $(this).attr('data-choice') || $(this).text().trim();
          $('#waSimInput').val(choiceText);
          self.sendSimulatedMessage();
        });

      // Single Workflow Toggle
      $(document)
        .off('change', '.wa-wf-status-toggle')
        .on('change', '.wa-wf-status-toggle', function () {
          const id = $(this).attr('data-id') || $(this).data('id');
          const isChecked = $(this).is(':checked');
          self.toggleWorkflow(id, isChecked);
        });

      // Delete Workflow
      $(document)
        .off('click', '.btn-delete-wa-wf')
        .on('click', '.btn-delete-wa-wf', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const id = $(this).attr('data-id') || $(this).data('id');
          self.deleteWorkflow(id);
        });

      // Duplicate Workflow
      $(document)
        .off('click', '.btn-dup-wa-wf')
        .on('click', '.btn-dup-wa-wf', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const id = $(this).attr('data-id') || $(this).data('id');
          self.duplicateWorkflow(id);
        });

      // Modal Fallback Handlers
      $(document)
        .off('click', '#btnCloseWaModal, #btnCancelWaModal')
        .on('click', '#btnCloseWaModal, #btnCancelWaModal', function (e) {
          if (e) e.preventDefault();
          self.closeModal();
        });

      $(document)
        .off('click', '#waWorkflowModal')
        .on('click', '#waWorkflowModal', function (e) {
          if ($(e.target).is('#waWorkflowModal')) {
            self.closeModal();
          }
        });

      $(document)
        .off('submit', '#waWorkflowForm')
        .on('submit', '#waWorkflowForm', function (e) {
          e.preventDefault();
          self.saveWorkflow();
        });

      // Simulator: Quick Keyword Click
      $(document)
        .off('click', '.wa-sim-chip')
        .on('click', '.wa-sim-chip', function (e) {
          e.preventDefault();
          const text = $(this).text().trim().replace(/['"]/g, '');
          $('#waSimInput').val(text);
          self.sendSimulatedMessage();
        });

      // Simulator: Send Message
      $(document)
        .off('click', '#waSimSendBtn')
        .on('click', '#waSimSendBtn', function (e) {
          e.preventDefault();
          self.sendSimulatedMessage();
        });

      $(document)
        .off('keypress', '#waSimInput')
        .on('keypress', '#waSimInput', function (e) {
          if (e.which === 13) {
            e.preventDefault();
            self.sendSimulatedMessage();
          }
        });
    },

    loadData: function () {
      const self = this;
      $('#waWorkflowsLoading').show();
      $('#waWorkflowsEmpty').hide();

      $.ajax({
        url: self.apiUrl,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
          $('#waWorkflowsLoading').hide();
          if (res && res.status === 'success') {
            self.workflows = res.workflows || [];
            self.templates = res.templates || [];
            self.tourPackages = res.tour_packages || [];
            self.packageDestinations = res.package_destinations || [];
            self.packageDurations = res.package_durations || [];

            // Update Master Plugin Switch & Status Pill
            const isPluginActive = !!res.is_plugin_active;
            $('#togglePluginActiveWa').prop('checked', isPluginActive);
            if (isPluginActive) {
              $('#waPluginStatusPill').removeClass('inactive').addClass('active').html('<span class="status-pulse-dot"></span> Active');
            } else {
              $('#waPluginStatusPill').removeClass('active').addClass('inactive').html('<span class="status-pulse-dot"></span> Setup Mode');
            }

            // Update KPIs
            if (res.stats) {
              $('#statWaTotal').text(res.stats.total_workflows || 0);
              $('#statWaActive').text(res.stats.active_workflows || 0);
              $('#statWaExecutions').text(res.stats.total_executions || 0);
            }

            // Update filter badges in list toolbar
            const totalCount = (self.workflows || []).length;
            const activeCount = (self.workflows || []).filter(w => parseInt(w.workflow_status, 10) === 1).length;
            const fallbackCount = (self.workflows || []).filter(w => parseInt(w.workflow_is_default, 10) === 1).length;
            const keywordCount = (self.workflows || []).filter(w => parseInt(w.workflow_is_default, 10) === 0).length;

            $('#waCountAll').text(totalCount);
            $('#waCountActive').text(activeCount);
            $('#waCountKeywords').text(keywordCount);
            $('#waCountFallback').text(fallbackCount);

            self.renderWorkflowsTable();
            self.renderTemplates();
          }
        },
        error: function () {
          $('#waWorkflowsLoading').hide();
        }
      });
    },

    renderWorkflowsTable: function () {
      const self = this;
      const $tbody = $('#waWorkflowsTbody');
      $tbody.empty();

      if (!self.workflows.length) {
        $('#waWorkflowsEmpty').show();
        $('#waWorkflowsNoMatch').addClass('is-hidden').hide();
        $('#waWorkflowsTableWrapper').hide();
        $('#waWorkflowsToolbar').hide();
        return;
      }

      $('#waWorkflowsEmpty').hide();
      $('#waWorkflowsToolbar').show();

      // Apply Filter & Search Query
      const q = (self.searchQuery || '').toLowerCase().trim();
      const filter = self.activeFilter || 'all';

      const filtered = self.workflows.filter(function (w) {
        const isActive = parseInt(w.workflow_status, 10) === 1;
        const isDefault = parseInt(w.workflow_is_default, 10) === 1;

        if (filter === 'active' && !isActive) return false;
        if (filter === 'fallback' && !isDefault) return false;
        if (filter === 'keyword' && isDefault) return false;

        if (q) {
          const nameMatch = (w.workflow_name || '').toLowerCase().indexOf(q) !== -1;
          const descMatch = (w.workflow_description || '').toLowerCase().indexOf(q) !== -1;
          const kwMatch = (w.workflow_keywords || '').toLowerCase().indexOf(q) !== -1;

          let nodeMatch = false;
          if (Array.isArray(w.workflow_nodes)) {
            for (let i = 0; i < w.workflow_nodes.length; i++) {
              const node = w.workflow_nodes[i];
              const text = (node.message_text || node.text || '').toLowerCase();
              if (text.indexOf(q) !== -1) {
                nodeMatch = true;
                break;
              }
              if (Array.isArray(node.buttons)) {
                for (let b = 0; b < node.buttons.length; b++) {
                  if ((node.buttons[b].label || '').toLowerCase().indexOf(q) !== -1) {
                    nodeMatch = true;
                    break;
                  }
                }
              }
            }
          }

          if (!nameMatch && !descMatch && !kwMatch && !nodeMatch) {
            return false;
          }
        }

        return true;
      });

      if (!filtered.length) {
        $('#waWorkflowsNoMatch').removeClass('is-hidden').show();
        $('#waWorkflowsTableWrapper').hide();
        return;
      }

      $('#waWorkflowsNoMatch').addClass('is-hidden').hide();
      $('#waWorkflowsTableWrapper').show();

      filtered.forEach(function (w) {
        const isActive = parseInt(w.workflow_status, 10) === 1;
        const isDefault = parseInt(w.workflow_is_default, 10) === 1;
        const nodes = Array.isArray(w.workflow_nodes) ? w.workflow_nodes : [];

        // Identify logic capabilities present in this flow
        const hasButtons = nodes.some(n => (n.buttons && n.buttons.length) || (n.options && n.options.length) || n.type === 'user_buttons' || n.type === 'interactive_buttons' || n.action_type === 'interactive_menu' || n.action_type === 'collect_input');
        const hasCondition = nodes.some(n => n.type === 'condition' || n.type === 'condition_branch' || n.action_type === 'condition_branch' || (n.rules && n.rules.length));
        const hasDoc = nodes.some(n => n.type === 'send_package' || n.action_type === 'send_itinerary');
        const hasPayment = nodes.some(n => n.action_type === 'payment_link');
        const hasHandoff = nodes.some(n => n.type === 'agent_handoff' || n.action_type === 'handoff_agent');
        const hasDelay = nodes.some(n => n.type === 'wait_delay' || n.action_type === 'delay');
        const hasTags = nodes.some(n => n.action_type === 'tag_lead');

        // Extract primary reply snippet
        let replyPreview = 'Standard Automated Reply';
        for (let i = 0; i < nodes.length; i++) {
          if (nodes[i].message_text || nodes[i].text) {
            replyPreview = nodes[i].message_text || nodes[i].text;
            break;
          }
        }
        if (replyPreview.length > 70) {
          replyPreview = replyPreview.substring(0, 70) + '...';
        }

        // Build informative sequence mini pills
        const sequencePills = [];
        const previewNodes = nodes.slice(0, 4);
        previewNodes.forEach(function (n, idx) {
          const actionType = n.action_type || n.subType || (n.type !== 'action' && n.type !== 'condition' && n.type !== 'trigger' ? n.type : '') || 'reply_text';
          let icon = 'fi fi-rr-comment-alt';
          let lbl = 'Message';
          let chipClass = 'msg';
          const fullTitle = n.title || `Step ${idx + 1}`;

          if (n.type === 'trigger') {
            icon = 'fi fi-rr-bolt';
            lbl = 'Trigger';
            chipClass = 'trigger';
          } else if (actionType === 'collect_input') {
            icon = 'fi fi-rr-edit';
            const f = (n.field_key || '').toLowerCase();
            if (f.includes('dest')) { lbl = 'Destination'; }
            else if (f.includes('pax')) { lbl = 'Travelers'; }
            else { lbl = 'Collect'; }
            chipClass = 'collect';
          } else if (actionType === 'send_itinerary' || actionType === 'send_package') {
            icon = 'fi fi-rr-document-signed';
            lbl = 'Itinerary';
            chipClass = 'itinerary';
          } else if (actionType === 'condition_branch') {
            icon = 'fi fi-rr-code-branch';
            lbl = 'If/Else';
            chipClass = 'logic';
          } else if (actionType === 'delay' || actionType === 'wait_delay') {
            icon = 'fi fi-rr-hourglass';
            lbl = `${parseInt(n.delay_seconds || 3, 10)}s Delay`;
            chipClass = 'delay';
          } else if (actionType === 'payment_link') {
            icon = 'fi fi-rr-credit-card';
            lbl = 'Advance Pay';
            chipClass = 'payment';
          } else if (actionType === 'interactive_menu' || actionType === 'user_buttons' || actionType === 'interactive_buttons') {
            icon = 'fi fi-rr-list-check';
            lbl = 'Choices';
            chipClass = 'menu';
          } else if (actionType === 'tag_lead' || actionType === 'lead_tags') {
            icon = 'fi fi-rr-tag';
            lbl = 'CRM Tag';
            chipClass = 'tag';
          } else if (actionType === 'handoff_agent' || actionType === 'agent_handoff') {
            icon = 'fi fi-rr-user-headset';
            lbl = 'Handoff';
            chipClass = 'handoff';
          } else {
            icon = 'fi fi-rr-comment-alt';
            lbl = idx === 1 ? 'Welcome' : 'Message';
            chipClass = 'msg';
          }

          sequencePills.push(`
            <span class="wa-seq-node-chip is-${chipClass}" title="${escapeHtml(fullTitle)}">
              <span class="wa-seq-num">${idx + 1}</span>
              <i class="${icon}"></i>
              <span>${escapeHtml(lbl)}</span>
            </span>
          `);
        });

        if (nodes.length > 4) {
          const remainingNodes = nodes.slice(4);
          const remainingSummary = remainingNodes.map((rn, rIdx) => `${rIdx + 5}. ${rn.title || rn.action_type || 'Step'}`).join(' \n');
          sequencePills.push(`<span class="wa-seq-more-chip" title="${escapeHtml(remainingSummary)}">+${nodes.length - 4} more</span>`);
        }

        // Trigger column HTML
        let triggerHtml = '';
        let simTriggerKeyword = '';
        if (isDefault) {
          triggerHtml = `
            <div class="wa-trigger-cell">
              <span class="wa-pill fallback"><i class="fi fi-rr-rotate-right"></i> Default Fallback</span>
              <div class="wa-trigger-subtitle">Answers any unrecognized chats</div>
            </div>
          `;
          simTriggerKeyword = 'Hello';
        } else {
          const kws = (w.workflow_keywords || '').split(',').map(k => k.trim()).filter(Boolean);
          simTriggerKeyword = kws[0] || 'itinerary';
          const maxDisplay = 3;
          const displayKws = kws.slice(0, maxDisplay);
          const remaining = kws.length - maxDisplay;

          const chips = displayKws.map(k => `<span class="wa-keyword-tag" title="Keyword trigger: #${escapeHtml(k)}">#${escapeHtml(k)}</span>`).join('');
          const moreChip = remaining > 0 ? `<span class="wa-keyword-more" title="${escapeHtml(kws.join(', '))}">+${remaining} more</span>` : '';

          triggerHtml = `
            <div class="wa-trigger-cell">
              <div class="wa-keyword-chips">${chips}${moreChip}</div>
              <div class="wa-trigger-count-hint">${kws.length} trigger phrase${kws.length === 1 ? '' : 's'}</div>
            </div>
          `;
        }

        const tr = `
          <tr data-id="${w.workflow_id}" class="wa-wf-row ${isActive ? 'is-active-row' : 'is-inactive-row'}">
            <td>
              <div class="wa-wf-col-wrap">
                <div class="wa-wf-avatar ${isDefault ? 'is-fallback' : 'is-keyword'}" title="${isDefault ? 'Default Fallback Bot' : 'Keyword Automated Flow'}">
                  <i class="${isDefault ? 'fi fi-rr-robot' : 'fi fi-rr-bolt'}"></i>
                </div>
                <div class="wa-wf-details">
                  <div class="wa-wf-title-row">
                    <span class="wa-workflow-name-cell">${escapeHtml(w.workflow_name)}</span>
                    ${isDefault ? '<span class="wa-badge-mini-fallback">Fallback</span>' : ''}
                  </div>
                  <div class="wa-workflow-desc-cell">${escapeHtml(w.workflow_description || 'WhatsApp conversation automation')}</div>
                  <div class="wa-logic-chips-row">
                    <span class="wa-logic-chip count"><i class="fi fi-rr-apps"></i> ${nodes.length} Steps</span>
                    ${hasButtons ? '<span class="wa-logic-chip buttons" title="Includes quick choice buttons"><i class="fi fi-rr-mouse"></i> Buttons</span>' : ''}
                    ${hasDoc ? '<span class="wa-logic-chip doc" title="Sends PDF itinerary brochure"><i class="fi fi-rr-document-signed"></i> Itinerary</span>' : ''}
                    ${hasCondition ? '<span class="wa-logic-chip logic" title="Smart If/Else branch routing"><i class="fi fi-rr-code-branch"></i> If/Else</span>' : ''}
                    ${hasPayment ? '<span class="wa-logic-chip payment" title="Includes booking advance payment link"><i class="fi fi-rr-credit-card"></i> Payment</span>' : ''}
                    ${hasTags ? '<span class="wa-logic-chip tag" title="Updates CRM lead stage and tags"><i class="fi fi-rr-tag"></i> CRM Stage</span>' : ''}
                    ${hasDelay ? '<span class="wa-logic-chip delay" title="Realistic agent typing delay"><i class="fi fi-rr-hourglass"></i> Delay</span>' : ''}
                    ${hasHandoff ? '<span class="wa-logic-chip agent" title="Handoff to human consultant"><i class="fi fi-rr-user-headset"></i> Handoff</span>' : ''}
                  </div>
                </div>
              </div>
            </td>
            <td>${triggerHtml}</td>
            <td>
              <div class="wa-sequence-preview-cell">
                <div class="wa-step-sequence-chain">
                  ${sequencePills.join('<i class="fi fi-rr-arrow-right wa-seq-arrow"></i>')}
                </div>
                <div class="wa-chat-bubble-preview" title="First automated message">
                  <div class="wa-bubble-preview-badge"><i class="fi fi-rr-comment-alt-middle"></i></div>
                  <div class="wa-bubble-preview-content">
                    <div class="wa-bubble-text">"${escapeHtml(replyPreview)}"</div>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <div class="wa-activity-cell">
                <div class="wa-bold-count">${w.workflow_execution_count || 0}</div>
                <div class="wa-text-xs-muted">triggers</div>
              </div>
            </td>
            <td>
              <div class="wa-status-toggle-cell">
                <label class="v3-toggle-switch" title="Toggle active status">
                  <input type="checkbox" class="wa-wf-status-toggle" data-id="${w.workflow_id}" ${isActive ? 'checked' : ''}>
                  <span class="v3-toggle-slider emerald"></span>
                </label>
                <span class="wa-status-text-pill ${isActive ? 'is-active' : 'is-inactive'}">${isActive ? 'Active' : 'Paused'}</span>
              </div>
            </td>
            <td>
              <div class="wa-action-btn-group">
                <button type="button" class="wa-action-btn btn-edit-wa-wf btn-edit" data-id="${w.workflow_id}" title="Edit Visual Flow">
                  <i class="fi fi-rr-edit"></i>
                </button>
                <button type="button" class="wa-action-btn btn-sim-wa-wf btn-sim" data-id="${w.workflow_id}" data-keyword="${escapeHtml(simTriggerKeyword)}" title="Test in Chat Simulator">
                  <i class="fi fi-rr-play"></i>
                </button>
                <button type="button" class="wa-action-btn btn-dup-wa-wf btn-dup" data-id="${w.workflow_id}" title="Duplicate Workflow">
                  <i class="fi fi-rr-copy"></i>
                </button>
                <button type="button" class="wa-action-btn btn-delete-wa-wf btn-delete is-danger" data-id="${w.workflow_id}" title="Delete Workflow">
                  <i class="fi fi-rr-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
        $tbody.append(tr);
      });
    },

    renderTemplates: function () {
      const self = this;
      const $container = $('#waTemplatesGrid');
      $container.empty();

      if (!self.templates.length) return;

      self.templates.forEach(function (tpl) {
        const card = `
          <div class="wa-template-card">
            <div>
              <div class="wa-template-card-header">
                <span class="wa-template-badge">Template</span>
                <span class="wa-template-setup-lbl">1-Click Setup</span>
              </div>
              <h4 class="wa-template-title">${escapeHtml(tpl.name)}</h4>
              <p class="wa-template-desc">${escapeHtml(tpl.description)}</p>
              <div class="wa-template-trigger-box">
                <strong>Trigger:</strong> ${tpl.is_default ? 'Any message (Fallback)' : escapeHtml(tpl.keywords)}
              </div>
            </div>
            <button type="button" class="btn-v3-action btn-v3-primary btn-use-wa-template" data-id="${tpl.id}">
              <i class="fi fi-rr-magic-wand"></i> Use This Template
            </button>
          </div>
        `;
        $container.append(card);
      });
    },

    openVisualBuilder: function (id, templateId) {
      const self = this;
      const targetId = id ? parseInt(id, 10) : 0;
      self.currentWorkflowId = targetId;
      self.canvasZoom = 1.0;

      // 1. Enter Visual Builder Mode & Hide Sidebar
      $('body').addClass('wa-builder-mode').removeClass('wa-builder-sidebar-open');
      $('#waDashboardView').addClass('is-hidden');
      $('#waVisualBuilderView').removeClass('is-hidden');

      // 2. Prepare Workflow Data & Nodes Sequence
      let wfName = 'Customer Greeting & Tour Inquiries';
      let wfDesc = 'Greets incoming inquiries and provides tour assistance options';
      let wfIsActive = true;
      let wfNodes = [];

      if (targetId > 0) {
        const wf = (self.workflows || []).find(w => parseInt(w.workflow_id, 10) === targetId);
        if (wf) {
          wfName = wf.workflow_name || 'Untitled Bot Flow';
          wfDesc = wf.workflow_description || '';
          wfIsActive = parseInt(wf.workflow_status, 10) === 1;

          const triggerType = wf.workflow_trigger_type || 'keyword';
          const isDefault = parseInt(wf.workflow_is_default, 10) === 1 || triggerType === 'default_fallback';
          const keywords = wf.workflow_keywords || '';
          const matchType = wf.workflow_match_type || 'contains';

          if (Array.isArray(wf.workflow_nodes) && wf.workflow_nodes.length) {
            wfNodes = JSON.parse(JSON.stringify(wf.workflow_nodes));
          } else {
            // Build default node sequence
            wfNodes = [
              {
                id: 'n_trigger',
                type: 'trigger',
                category: 'trigger',
                title: isDefault ? 'Default Fallback Responder' : 'Incoming Keyword Trigger',
                trigger_type: triggerType,
                keywords: keywords,
                match_type: matchType,
                is_default: isDefault ? 1 : 0
              },
              {
                id: 'n_action_1',
                type: 'action',
                category: 'action',
                action_type: 'reply_text',
                title: 'Send Automated WhatsApp Reply',
                message_text: 'Hello {name}! 👋 Thank you for messaging {company}. How can we assist you with your travel plans today?'
              }
            ];
          }

          // Ensure node[0] is always the trigger node
          if (!wfNodes.length || wfNodes[0].type !== 'trigger') {
            wfNodes.unshift({
              id: 'n_trigger',
              type: 'trigger',
              category: 'trigger',
              title: isDefault ? 'Default Fallback Responder' : 'Incoming Keyword Trigger',
              trigger_type: triggerType,
              keywords: keywords,
              match_type: matchType,
              is_default: isDefault ? 1 : 0
            });
          }
        }
      } else if (templateId) {
        const tpl = (self.templates || []).find(t => t.id === templateId);
        if (tpl) {
          wfName = tpl.name || 'New Template Flow';
          wfDesc = tpl.description || '';
          wfIsActive = true;

          const triggerType = tpl.trigger_type || 'keyword';
          const isDefault = !!tpl.is_default || triggerType === 'default_fallback';
          const keywords = tpl.keywords || '';
          const matchType = tpl.match_type || 'contains';

          wfNodes = [
            {
              id: 'n_trigger',
              type: 'trigger',
              category: 'trigger',
              title: isDefault ? 'Default Fallback Responder' : 'Incoming Keyword Trigger',
              trigger_type: triggerType,
              keywords: keywords,
              match_type: matchType,
              is_default: isDefault ? 1 : 0
            }
          ];

          if (Array.isArray(tpl.nodes) && tpl.nodes.length) {
            tpl.nodes.forEach(function (n, idx) {
              wfNodes.push({
                id: 'n_action_' + (idx + 1),
                type: 'action',
                category: 'action',
                action_type: 'reply_text',
                title: n.title || 'Send Automated WhatsApp Reply',
                message_text: n.message_text || 'Thank you for reaching out!'
              });
            });
          } else {
            wfNodes.push({
              id: 'n_action_1',
              type: 'action',
              category: 'action',
              action_type: 'reply_text',
              title: 'Send Automated WhatsApp Reply',
              message_text: 'Hello {name}! 👋 Thank you for messaging {company}. How can we assist you with your travel plans today?'
            });
          }
        }
      } else {
        // Brand new workflow
        wfNodes = [
          {
            id: 'n_trigger',
            type: 'trigger',
            category: 'trigger',
            title: 'Incoming Keyword Trigger',
            trigger_type: 'keyword',
            keywords: 'hi, hello, price, package, tour',
            match_type: 'contains',
            is_default: 0
          },
          {
            id: 'n_action_1',
            type: 'action',
            category: 'action',
            action_type: 'reply_text',
            title: 'Send Automated WhatsApp Reply',
            message_text: 'Hello {name}! 👋 Thank you for contacting {company}. We offer customized holiday packages and flight bookings. How may we help you today?'
          }
        ];
      }

      self.currentNodes = wfNodes;
      self.selectedNodeIndex = self.currentNodes.length > 1 ? 1 : 0;

      // 3. Populate Column 1 Workflow Details
      $('#builderWfTitleInput').val(wfName);
      $('#builderTopWorkflowTitle').text(wfName);
      $('#builderWfDescInput').val(wfDesc);
      $('#builderWorkflowActiveToggle').prop('checked', wfIsActive);
      if (wfIsActive) {
        $('#builderStatusBadge').removeClass('is-inactive').addClass('is-active').text('Active');
      } else {
        $('#builderStatusBadge').removeClass('is-active').addClass('is-inactive').text('Inactive');
      }

      // 4. Render Flow Canvas & Inspector
      self.renderFlowCanvas();
      self.renderPropertyInspector();
      self.setZoom(1.0);
    },

    closeVisualBuilder: function () {
      $('body').removeClass('wa-builder-mode wa-builder-sidebar-open');
      $('#waVisualBuilderView').addClass('is-hidden');
      $('#waDashboardView').removeClass('is-hidden');
    },

    setZoom: function (zoom) {
      const self = this;
      const clamped = Math.max(0.6, Math.min(1.4, Math.round(zoom * 10) / 10));
      self.canvasZoom = clamped;
      $('#canvasZoomLevel').text(Math.round(clamped * 100) + '%');
      $('#waFlowNodesContainer').css('transform', 'scale(' + clamped + ')');
    },

    openActionPicker: function (insertIndex) {
      const self = this;
      self.pendingInsertIndex = (typeof insertIndex === 'number' && !isNaN(insertIndex)) ? insertIndex : self.currentNodes.length;
      $('#actionPickerSearchInput').val('');
      $('.wa-picker-cat-btn').removeClass('active');
      $('.wa-picker-cat-btn[data-cat="all"]').addClass('active');
      $('.wa-action-card').show();
      $('#waActionPickerModal').addClass('is-open');
      setTimeout(function () {
        $('#actionPickerSearchInput').focus();
      }, 100);
    },

    closeActionPicker: function () {
      $('#waActionPickerModal').removeClass('is-open');
    },

    filterActionCards: function (cat, query) {
      $('.wa-action-card').each(function () {
        const $card = $(this);
        const cardCat = ($card.data('cat') || '').toString();
        const cardText = $card.text().toLowerCase();
        const matchesCat = (cat === 'all' || cardCat.indexOf(cat) !== -1);
        const matchesQuery = (!query || cardText.indexOf(query) !== -1);
        if (matchesCat && matchesQuery) {
          $card.show();
        } else {
          $card.hide();
        }
      });
    },

    selectNode: function (index) {
      const self = this;
      if (index < 0 || index >= self.currentNodes.length) return;
      self.selectedNodeIndex = index;
      $('.wa-flow-node-card').removeClass('is-selected');
      $('.wa-flow-node-card[data-index="' + index + '"]').addClass('is-selected');
      self.renderPropertyInspector();
    },

    addNewNode: function (type, atIndex, presetKey) {
      const self = this;
      const newId = 'n_' + Date.now();
      let newNode = null;

      const firstPkg = (self.tourPackages && self.tourPackages[0]) ? self.tourPackages[0] : null;
      const secondPkg = (self.tourPackages && self.tourPackages[1]) ? self.tourPackages[1] : null;
      const realDests = (self.packageDestinations && self.packageDestinations.length > 0)
        ? self.packageDestinations.slice(0, 4).map(d => `${self.getDestinationEmoji(d)} ${d}`)
        : ['🏔️ Manali & Himachal', '🏖️ Kerala & Munnar', '🏙️ Dubai Luxury', '⛺ Ladakh Adventure'];

      // Predefined Action Options
      if (presetKey === 'dest_picker') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'collect_input',
          title: 'Select Destination (Buttons)',
          field_key: 'destination',
          input_mode: 'buttons',
          message_text: 'Which dream holiday destination are you looking forward to exploring?',
          options: realDests,
          validation_type: 'choice'
        };
      } else if (presetKey === 'dates_picker') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'collect_input',
          title: 'Select Travel Dates / Month',
          field_key: 'travel_dates',
          input_mode: 'buttons',
          message_text: 'When are you planning this trip? Choose a preferred travel window:',
          options: ['🗓️ Next Month', '☀️ Summer Vacation', '❄️ Winter / Year-End', '✨ Flexible Dates'],
          validation_type: 'choice'
        };
      } else if (presetKey === 'pax_picker') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'collect_input',
          title: 'Number of Guests / Pax',
          field_key: 'pax',
          input_mode: 'buttons',
          message_text: 'How many travelers will be joining this holiday?',
          options: ['💑 Couple (2 Pax)', '👨‍👩‍👧 Family (3-4 Pax)', '👥 Group (5-10 Pax)', '🏢 Corporate Tour (10+ Pax)'],
          validation_type: 'choice'
        };
      } else if (presetKey === 'budget_picker') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'collect_input',
          title: 'Budget Preference Range',
          field_key: 'budget',
          input_mode: 'buttons',
          message_text: 'What is your preferred budget range per person?',
          options: ['💰 Economy (< ₹30k)', '💰 Standard (₹30k - ₹60k)', '💎 Luxury (₹60k - ₹1.2L)', '👑 Ultra VIP (₹1.2L+)'],
          validation_type: 'choice'
        };
      } else if (presetKey === 'welcome_menu') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'interactive_menu',
          title: 'Welcome & Main Menu',
          message_text: 'Welcome to our Travel Concierge! 👋 How can we make your trip wonderful today? Tap an option below:',
          options: ['1. 🗺️ View Tour Packages', '2. 💰 Get Instant Quote', '3. 👩‍💼 Talk to Travel Specialist']
        };
      } else if (presetKey === 'tour_itinerary') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'send_itinerary',
          title: firstPkg ? ('Send ' + firstPkg.title + ' Brochure') : 'Send Tour Itinerary PDF',
          itinerary_title: firstPkg ? firstPkg.title : 'Curated Tour Package Itinerary',
          itinerary_url: 'https://example.com/brochures/tour_package.pdf',
          message_text: firstPkg
            ? `Here is the verified itinerary PDF brochure for *${firstPkg.title}*! It includes complete day-wise sightseeing, hotel stays, verified vehicle transfers, and quotation. 📄✨`
            : 'Here is your personalized itinerary package brochure! Review daily plans, hotel accommodations, and price breakdown.'
        };
      } else if (presetKey === 'advance_token') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'payment_link',
          title: 'Advance Token Deposit (₹5,000)',
          amount: '5000',
          currency: 'INR',
          message_text: 'Lock in your holiday reservation and special seasonal discounts with an advance booking token of ₹5,000:'
        };
      } else if (presetKey === 'specialist_handoff') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'handoff_agent',
          title: 'Destination Specialist Desk',
          department: 'sales',
          note: 'High-intent travel lead routed to destination specialist for custom itinerary.'
        };
      } else if (presetKey === 'hot_qualifier') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'tag_lead',
          title: 'Hot Lead Stage & CRM Tags',
          lead_stage: 'Hot Lead - Requested Itinerary',
          tags: 'whatsapp_lead, high_intent, itinerary_requested'
        };
      } else if (type === 'delay') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'delay',
          title: 'Wait / Delay',
          delay_seconds: 3
        };
      } else if (type === 'interactive_menu') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'interactive_menu',
          title: 'Button / Quick Replies',
          message_text: 'Please choose an option to continue:',
          options: ['1. Popular Tour Packages', '2. Request Instant Quote', '3. Speak to Travel Specialist']
        };
      } else if (type === 'handoff_agent') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'handoff_agent',
          title: 'Human Agent Handoff',
          department: 'sales',
          note: 'Customer requested live human operator assistance.'
        };
      } else if (type === 'collect_input') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'collect_input',
          title: 'Choose Holiday Destination (Buttons)',
          field_key: 'destination',
          input_mode: 'buttons',
          message_text: 'Which holiday destination would you love to explore?',
          options: realDests,
          validation_type: 'choice'
        };
      } else if (type === 'condition_branch') {
        const firstDest = (self.packageDestinations && self.packageDestinations[0]) ? self.packageDestinations[0] : 'Manali';
        newNode = {
          id: newId,
          type: 'condition',
          category: 'logic',
          action_type: 'condition_branch',
          title: 'If / Else Branch by Selection',
          condition_target: 'last_button',
          condition_operator: 'contains',
          condition_value: firstDest,
          if_action_type: 'send_itinerary',
          if_package_id: firstPkg ? firstPkg.id : 0,
          if_itinerary_title: firstPkg ? firstPkg.title : 'Curated Tour Package Itinerary',
          if_label: firstPkg ? ('Send ' + firstPkg.title + ' PDF') : 'Send Matched Tour Package Itinerary',
          if_message: firstPkg ? ('Here is your tailored ' + firstPkg.title + ' itinerary PDF with verified hotel options and sightseeing included! 📄✨') : 'Here is your personalized holiday itinerary PDF!',
          else_action_type: 'send_itinerary',
          else_package_id: secondPkg ? secondPkg.id : 0,
          else_itinerary_title: secondPkg ? secondPkg.title : 'Alternative Holiday Itinerary',
          else_label: secondPkg ? ('Send ' + secondPkg.title + ' PDF') : 'Send Alternative Tour Package Itinerary',
          else_message: secondPkg ? ('Here is our curated ' + secondPkg.title + ' holiday package itinerary with complete details! 📄') : 'Here is our bestselling holiday package itinerary with complete details!'
        };
      } else if (type === 'send_itinerary') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'send_itinerary',
          title: 'Send Itinerary Package / PDF Brochure',
          itinerary_title: 'Custom Travel Itinerary & Quotation',
          itinerary_url: 'https://example.com/brochures/itinerary_brochure.pdf',
          message_text: 'Here is your personalized itinerary package tailored to your preferences! Click below to review daily plans, hotel selections, and pricing breakdown.'
        };
      } else if (type === 'payment_link') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'payment_link',
          title: 'Payment Link / Booking Token Advance',
          amount: '500',
          currency: 'USD',
          message_text: 'You can securely confirm your booking slot and lock in your reservation with an advance token payment of {amount}:'
        };
      } else if (type === 'tag_lead') {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'tag_lead',
          title: 'Update Lead Stage & Tags',
          lead_stage: 'Hot Lead - Requested Itinerary',
          tags: 'whatsapp_itinerary_sent, high_intent'
        };
      } else {
        newNode = {
          id: newId,
          type: 'action',
          category: 'action',
          action_type: 'reply_text',
          title: 'Send Automated WhatsApp Reply',
          message_text: 'Thank you for your response! We will get back to you shortly.'
        };
      }

      if (typeof atIndex === 'number' && atIndex >= 1 && atIndex <= self.currentNodes.length) {
        self.currentNodes.splice(atIndex, 0, newNode);
        self.selectedNodeIndex = atIndex;
      } else {
        self.currentNodes.push(newNode);
        self.selectedNodeIndex = self.currentNodes.length - 1;
      }

      self.renderFlowCanvas();
      self.renderPropertyInspector();
    },

    deleteNode: function (index) {
      const self = this;
      if (index === 0) {
        alert('The trigger node cannot be deleted. You can modify its keywords or fallback settings in the inspector.');
        return;
      }
      if (self.currentNodes.length <= 2) {
        alert('A workflow must contain at least one response step.');
        return;
      }

      self.currentNodes.splice(index, 1);
      self.selectedNodeIndex = Math.max(0, index - 1);
      self.renderFlowCanvas();
      self.renderPropertyInspector();
    },

    duplicateNode: function (index) {
      const self = this;
      if (index === 0) return;
      const source = self.currentNodes[index];
      if (!source) return;

      const clone = JSON.parse(JSON.stringify(source));
      clone.id = 'n_' + Date.now();
      clone.title = clone.title + ' (Copy)';

      self.currentNodes.splice(index + 1, 0, clone);
      self.selectedNodeIndex = index + 1;
      self.renderFlowCanvas();
      self.renderPropertyInspector();
    },

    reorderNodes: function (fromIdx, toIdx) {
      const self = this;
      if (fromIdx === 0 || toIdx === 0) return;
      if (fromIdx < 0 || fromIdx >= self.currentNodes.length) return;

      const targetIdx = Math.max(1, Math.min(self.currentNodes.length, toIdx));
      const movedItem = self.currentNodes.splice(fromIdx, 1)[0];
      const insertAt = fromIdx < targetIdx ? targetIdx - 1 : targetIdx;
      self.currentNodes.splice(insertAt, 0, movedItem);

      self.selectedNodeIndex = insertAt;
      self.renderFlowCanvas();
      self.renderPropertyInspector();
    },

    renderFlowCanvas: function () {
      const self = this;
      const $container = $('#waFlowNodesContainer');
      $container.empty();

      if (!self.currentNodes || !self.currentNodes.length) {
        $container.append(`
          <div class="wa-make-empty-canvas">
            <div class="wa-make-add-module-bubble wa-insert-step-btn" data-insert-index="0" title="Click to add Starting Point">
              <div class="wa-make-add-bubble-circle is-pulsing">
                <i class="fi fi-rr-plus"></i>
              </div>
              <div class="wa-make-add-bubble-label">
                <strong>Add Starting Point</strong>
                <div>Choose an inbound trigger or keyword match</div>
              </div>
            </div>
          </div>
        `);
        return;
      }

      self.currentNodes.forEach(function (node, i) {
        const isTrigger = i === 0 || node.type === 'trigger';
        const isSelected = i === self.selectedNodeIndex;

        let cardTypeClass = 'is-message';
        let iconClass = 'blue';
        let iconHtml = '<i class="fi fi-rr-comment-alt"></i>';
        const stepNumPadded = String(i + 1).padStart(2, '0');
        let stepCategory = `${stepNumPadded} &bull; MESSAGE`;

        if (isTrigger) {
          cardTypeClass = 'is-trigger';
          iconClass = 'emerald';
          iconHtml = '<i class="fi fi-rr-bolt"></i>';
          stepCategory = `STARTING POINT`;
        } else if (node.action_type === 'delay') {
          cardTypeClass = 'is-delay';
          iconClass = 'amber';
          iconHtml = '<i class="fi fi-rr-hourglass"></i>';
          stepCategory = `${stepNumPadded} &bull; DELAY`;
        } else if (node.action_type === 'interactive_menu') {
          cardTypeClass = 'is-menu';
          iconClass = 'indigo';
          iconHtml = '<i class="fi fi-rr-list-check"></i>';
          stepCategory = `${stepNumPadded} &bull; QUICK CHOICES`;
        } else if (node.action_type === 'handoff_agent') {
          cardTypeClass = 'is-handoff';
          iconClass = 'purple';
          iconHtml = '<i class="fi fi-rr-user-headset"></i>';
          stepCategory = `${stepNumPadded} &bull; AGENT HANDOFF`;
        } else if (node.action_type === 'collect_input') {
          cardTypeClass = 'is-collect';
          iconClass = 'teal';
          iconHtml = '<i class="fi fi-rr-edit"></i>';
          stepCategory = `${stepNumPadded} &bull; COLLECT`;
        } else if (node.action_type === 'condition_branch') {
          cardTypeClass = 'is-condition';
          iconClass = 'cyan';
          iconHtml = '<i class="fi fi-rr-code-branch"></i>';
          stepCategory = `${stepNumPadded} &bull; LOGIC`;
        } else if (node.action_type === 'send_itinerary') {
          cardTypeClass = 'is-itinerary';
          iconClass = 'rose';
          iconHtml = '<i class="fi fi-rr-document-signed"></i>';
          stepCategory = `${stepNumPadded} &bull; ITINERARY`;
        } else if (node.action_type === 'payment_link') {
          cardTypeClass = 'is-payment';
          iconClass = 'cyan';
          iconHtml = '<i class="fi fi-rr-credit-card"></i>';
          stepCategory = `${stepNumPadded} &bull; PAYMENT`;
        } else if (node.action_type === 'tag_lead') {
          cardTypeClass = 'is-tag';
          iconClass = 'orange';
          iconHtml = '<i class="fi fi-rr-tag"></i>';
          stepCategory = `${stepNumPadded} &bull; CRM TAG`;
        }

        // Node Preview Body HTML
        let bodyHtml = '';
        if (isTrigger) {
          const isDef = !!node.is_default || node.trigger_type === 'default_fallback';
          if (isDef) {
            bodyHtml = `
              <div class="wa-node-meta-row">
                <span class="wa-pill fallback"><i class="fi fi-rr-refresh"></i> Default Fallback</span>
              </div>
            `;
          } else {
            const kws = (node.keywords || 'hi, hello').split(',').map(k => k.trim()).filter(Boolean);
            const pills = kws.slice(0, 3).map(k => `<span class="wa-keyword-tag">${escapeHtml(k)}</span>`).join(' ');
            bodyHtml = `
              <div class="d-flex flex-wrap gap-1 justify-content-center">${pills || '<span class="text-muted fst-italic">No keywords</span>'}</div>
            `;
          }
        } else if (node.action_type === 'delay') {
          bodyHtml = `
            <div class="wa-node-meta-row justify-content-center">
              <span class="wa-pill active"><i class="fi fi-rr-hourglass"></i> ${parseInt(node.delay_seconds || 3, 10)}s Delay</span>
            </div>
          `;
        } else if (node.action_type === 'interactive_menu') {
          const options = Array.isArray(node.options) ? node.options : [];
          const optHtml = options.slice(0, 3).map(o => `<span class="wa-keyword-tag">${escapeHtml(o)}</span>`).join(' ');
          bodyHtml = `
            <div class="d-flex flex-wrap gap-1 justify-content-center">${optHtml || '<span class="text-muted small">No buttons</span>'}</div>
          `;
        } else if (node.action_type === 'handoff_agent') {
          bodyHtml = `
            <div class="wa-node-meta-row justify-content-center">
              <span class="wa-pill active"><i class="fi fi-rr-user-headset"></i> ${escapeHtml(node.department || 'Sales')}</span>
            </div>
          `;
        } else if (node.action_type === 'collect_input') {
          const fKey = escapeHtml(node.field_key || 'requirement');
          bodyHtml = `
            <div class="wa-node-meta-row justify-content-center">
              <span class="wa-pill active"><i class="fi fi-rr-disk"></i> Field: ${fKey}</span>
            </div>
          `;
        } else if (node.action_type === 'condition_branch') {
          bodyHtml = self.renderConditionBranchCardHtml(node);
        } else if (node.action_type === 'send_itinerary') {
          const itinTitle = escapeHtml(node.itinerary_title || 'Custom Tour Package');
          bodyHtml = `
            <div class="wa-node-meta-row justify-content-center">
              <span class="wa-pill active"><i class="fi fi-rr-file-pdf"></i> ${itinTitle}</span>
            </div>
          `;
        } else if (node.action_type === 'payment_link') {
          const amt = escapeHtml(node.amount || '500');
          const curr = escapeHtml(node.currency || 'USD');
          bodyHtml = `
            <div class="wa-node-meta-row justify-content-center">
              <span class="wa-pill active"><i class="fi fi-rr-dollar"></i> ${curr} ${amt}</span>
            </div>
          `;
        } else if (node.action_type === 'tag_lead') {
          const stage = escapeHtml(node.lead_stage || 'Active Prospect');
          bodyHtml = `
            <div class="wa-node-meta-row justify-content-center">
              <span class="wa-pill active"><i class="fi fi-rr-flag"></i> ${stage}</span>
            </div>
          `;
        } else {
          // Default: reply_text
          const rawText = node.message_text || '';
          let highlighted = escapeHtml(rawText)
            .replace(/\{name\}/g, '<span class="wa-node-var-tag">{name}</span>')
            .replace(/\{company\}/g, '<span class="wa-node-var-tag">{company}</span>')
            .replace(/\{phone\}/g, '<span class="wa-node-var-tag">{phone}</span>');
          bodyHtml = `<div class="wa-node-preview-bubble is-wa-bubble">${highlighted || '<span class="text-muted fst-italic">Empty reply text...</span>'}</div>`;
        }

        const actionsHtml = isTrigger ? `
          <button type="button" class="wa-node-action-btn btn-node-inspect" data-index="${i}" title="Inspect Trigger Settings">
            <i class="fi fi-rr-settings-sliders"></i>
          </button>
        ` : `
          <button type="button" class="wa-node-action-btn btn-node-dup" data-index="${i}" title="Duplicate this module">
            <i class="fi fi-rr-copy-alt"></i>
          </button>
          <button type="button" class="wa-node-action-btn is-danger btn-node-delete" data-index="${i}" title="Delete this module">
            <i class="fi fi-rr-trash"></i>
          </button>
        `;

        const nodeTitle = escapeHtml(node.title || (isTrigger ? 'Incoming Trigger' : 'Step ' + (i + 1)));

        const cardHtml = `
          <div class="wa-flow-node-card wa-make-module ${cardTypeClass} ${isSelected ? 'is-selected' : ''}" data-index="${i}" draggable="${isTrigger ? 'false' : 'true'}">
            <div class="wa-make-module-actions">
              ${actionsHtml}
            </div>
            <div class="wa-make-bubble-wrap">
              <div class="wa-make-bubble ${cardTypeClass}">
                <span class="wa-make-step-badge">${i + 1}</span>
                ${isTrigger ? '<span class="wa-make-trigger-badge" title="Starting Trigger"><i class="fi fi-rr-bolt"></i></span>' : ''}
                <div class="wa-make-bubble-icon ${iconClass}">
                  ${iconHtml}
                </div>
              </div>
            </div>
            <div class="wa-make-module-info">
              <div class="wa-make-module-title" id="nodeCardTitle_${i}">${nodeTitle}</div>
              <span class="wa-make-module-tag">${stepCategory}</span>
              <div class="wa-make-module-preview" id="nodeCardBody_${i}">
                ${bodyHtml}
              </div>
            </div>
          </div>
        `;

        $container.append(cardHtml);

        // Pipe Connector after module
        if (i < self.currentNodes.length - 1) {
          $container.append(`
            <div class="wa-make-pipe-connector">
              <div class="wa-make-pipe-line"></div>
              <button type="button" class="wa-make-plus-btn wa-insert-step-btn" data-insert-index="${i + 1}" title="Insert module here">
                <i class="fi fi-rr-plus"></i>
              </button>
              <div class="wa-make-pipe-line"></div>
            </div>
          `);
        } else {
          $container.append(`
            <div class="wa-make-pipe-connector is-terminal-pipe">
              <div class="wa-make-pipe-line"></div>
            </div>
          `);
        }
      });

      // Terminal Make.com "Add module" dashed circle
      $container.append(`
        <div class="wa-make-add-module-wrap">
          <div class="wa-make-add-module-bubble wa-insert-step-btn" data-insert-index="${self.currentNodes.length}" title="Add another module">
            <div class="wa-make-add-bubble-circle">
              <i class="fi fi-rr-plus"></i>
            </div>
            <span class="wa-make-add-bubble-label">Add module</span>
          </div>
        </div>
      `);
    },

    updateNodeCardPreview: function (index) {
      const self = this;
      const node = self.currentNodes[index];
      if (!node) return;

      const isTrigger = index === 0 || node.type === 'trigger';

      // Update title text
      $(`#nodeCardTitle_${index}`).text(node.title || (isTrigger ? 'Incoming Trigger' : 'Step ' + (index + 1)));

      // Update body content
      let bodyHtml = '';
      if (isTrigger) {
        const isDef = !!node.is_default || node.trigger_type === 'default_fallback';
        if (isDef) {
          bodyHtml = `
            <div class="wa-node-meta-row">
              <span class="wa-pill fallback"><i class="fi fi-rr-refresh"></i> Default Fallback Bot</span>
              <span class="text-muted">Triggers on any unmatched incoming message</span>
            </div>
          `;
        } else {
          const kws = (node.keywords || 'hi, hello').split(',').map(k => k.trim()).filter(Boolean);
          const pills = kws.map(k => `<span class="wa-keyword-tag">${escapeHtml(k)}</span>`).join(' ');
          bodyHtml = `
            <div class="mb-1 text-muted small"><strong>Triggers when message matches:</strong></div>
            <div class="d-flex flex-wrap gap-1">${pills || '<span class="text-muted fst-italic">No keywords defined</span>'}</div>
          `;
        }
      } else if (node.action_type === 'delay') {
        bodyHtml = `
          <div class="wa-node-meta-row">
            <span class="wa-pill active"><i class="fi fi-rr-hourglass"></i> ${parseInt(node.delay_seconds || 3, 10)}s Pause</span>
            <span>Displays WhatsApp typing indicator before sending next reply</span>
          </div>
        `;
      } else if (node.action_type === 'interactive_menu') {
        const options = Array.isArray(node.options) ? node.options : [];
        const optHtml = options.map(o => `<span class="wa-keyword-tag">${escapeHtml(o)}</span>`).join(' ');
        bodyHtml = `
          <div class="wa-node-preview-bubble mb-2">${escapeHtml(node.message_text || 'Please select an option:')}</div>
          <div class="d-flex flex-wrap gap-1">${optHtml}</div>
        `;
      } else if (node.action_type === 'handoff_agent') {
        bodyHtml = `
          <div class="wa-node-meta-row">
            <span class="wa-pill active"><i class="fi fi-rr-user-headset"></i> Routing to ${escapeHtml(node.department || 'Sales')} Team</span>
            <span>Internal alert sent to human operator inbox</span>
          </div>
        `;
      } else if (node.action_type === 'collect_input') {
        const fKey = escapeHtml(node.field_key || 'requirement');
        const hasButtons = Array.isArray(node.options) && node.options.length > 0;
        let buttonsHtml = '';
        if (hasButtons) {
          buttonsHtml = '<div class="d-flex flex-wrap gap-1 mt-2">' +
            node.options.map(opt => `<span class="wa-btn-option-pill"><i class="fi fi-rr-mouse"></i> ${escapeHtml(opt)}</span>`).join('') +
            '</div>';
        }
        bodyHtml = `
          <div class="wa-node-meta-row mb-1">
            <span class="wa-pill active"><i class="fi fi-rr-disk"></i> Saves to Field: ${fKey}</span>
            ${hasButtons ? '<span class="wa-pill active"><i class="fi fi-rr-cursor"></i> Button Choices</span>' : '<span class="text-muted small">Customer types text</span>'}
          </div>
          <div class="wa-node-preview-bubble is-wa-bubble">${escapeHtml(node.message_text || 'Prompting customer for details...')}</div>
          ${buttonsHtml}
        `;
      } else if (node.action_type === 'condition_branch') {
        bodyHtml = self.renderConditionBranchCardHtml(node);
      } else if (node.action_type === 'send_itinerary') {
        const itinTitle = escapeHtml(node.itinerary_title || 'Custom Tour Package Itinerary');
        const itinUrl = escapeHtml(node.itinerary_url || '#');
        bodyHtml = `
          <div class="wa-node-preview-bubble mb-2">${escapeHtml(node.message_text || 'Sending package details...')}</div>
          <div class="wa-itinerary-preview-card">
            <div class="wa-itinerary-preview-icon"><i class="fi fi-rr-file-pdf"></i></div>
            <div class="wa-itinerary-preview-info">
              <div class="wa-itinerary-preview-title">${itinTitle}</div>
              <div class="wa-itinerary-preview-meta text-muted small"><i class="fi fi-rr-link"></i> ${itinUrl}</div>
            </div>
          </div>
        `;
      } else if (node.action_type === 'payment_link') {
        const amt = escapeHtml(node.amount || '500');
        const curr = escapeHtml(node.currency || 'USD');
        bodyHtml = `
          <div class="wa-node-preview-bubble mb-2">${escapeHtml(node.message_text || 'Sending secure payment link...')}</div>
          <div class="wa-node-meta-row">
            <span class="wa-pill active"><i class="fi fi-rr-dollar"></i> Amount: ${curr} ${amt}</span>
            <span>Generates secure deposit link</span>
          </div>
        `;
      } else if (node.action_type === 'tag_lead') {
        const stage = escapeHtml(node.lead_stage || 'Active Prospect');
        const tags = (node.tags || '').split(',').map(t => t.trim()).filter(Boolean);
        const tagPills = tags.map(t => `<span class="wa-keyword-tag">${escapeHtml(t)}</span>`).join(' ');
        bodyHtml = `
          <div class="wa-node-meta-row mb-1">
            <span class="wa-pill active"><i class="fi fi-rr-flag"></i> Stage: ${stage}</span>
          </div>
          <div class="d-flex flex-wrap gap-1">${tagPills || '<span class="text-muted fst-italic">No tags</span>'}</div>
        `;
      } else {
        const rawText = node.message_text || '';
        let highlighted = escapeHtml(rawText)
          .replace(/\{name\}/g, '<span class="wa-node-var-tag">{name}</span>')
          .replace(/\{company\}/g, '<span class="wa-node-var-tag">{company}</span>')
          .replace(/\{phone\}/g, '<span class="wa-node-var-tag">{phone}</span>');
        bodyHtml = `<div class="wa-node-preview-bubble is-wa-bubble">${highlighted || '<span class="text-muted fst-italic">Empty reply text...</span>'}</div>`;
      }

      $(`#nodeCardBody_${index}`).html(bodyHtml);
    },

    renderPropertyInspector: function () {
      const self = this;
      const $content = $('#builderInspectorContent');
      $content.empty();

      const node = self.currentNodes[self.selectedNodeIndex];
      if (!node) {
        $('#builderSelectedStepChip').text('No Selection');
        $content.html(`
          <div class="wa-inspector-empty">
            <i class="fi fi-rr-cursor"></i>
            Click on any step card on the canvas to configure its properties.
          </div>
        `);
        return;
      }

      function finishInspector(chipTitle, html) {
        $('#builderSelectedStepChip').text(chipTitle);
        $content.html(html);
        self.initSelect2Dropdowns('#builderPropertyInspectorBody');
      }

      const isTrigger = self.selectedNodeIndex === 0 || node.type === 'trigger';

      if (isTrigger) {
        const isDefault = !!node.is_default || node.trigger_type === 'default_fallback';

        finishInspector('Trigger Step', `
          <div class="wa-inspector-card-badge emerald">
            <i class="fi fi-rr-bolt"></i> Step 1: Inbound Trigger Rules
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspTriggerType" class="v3-form-label">Trigger Condition</label>
            <select id="inspTriggerType" class="selectjs2 selectJS form-select form-select-sm">
              <option value="keyword" ${!isDefault ? 'selected' : ''}>Incoming Keyword Match</option>
              <option value="default_fallback" ${isDefault ? 'selected' : ''}>Default Fallback (Any Unmatched Message)</option>
            </select>
          </div>

          <div id="inspKeywordsGroup" class="${isDefault ? 'is-hidden' : ''}">
            <div class="v3-form-group mb-2">
              <label for="inspKeywords" class="v3-form-label">
                Keywords <span class="lead-label-hint">Comma Separated</span>
              </label>
              <input type="text" id="inspKeywords" class="form-control form-control-sm" value="${escapeHtml(node.keywords || 'hi, hello, price')}" placeholder="e.g. hi, hello, price, package">
              <div class="form-text wa-form-hint">Triggers whenever an incoming message matches any keyword.</div>
            </div>

            <div class="v3-form-group mb-2">
              <label for="inspMatchType" class="v3-form-label">Matching Logic</label>
              <select id="inspMatchType" class="selectjs2 selectJS form-select form-select-sm">
                <option value="contains" ${node.match_type === 'contains' ? 'selected' : ''}>Message Contains Any Keyword</option>
                <option value="exact" ${node.match_type === 'exact' ? 'selected' : ''}>Exact Message Match</option>
                <option value="starts_with" ${node.match_type === 'starts_with' ? 'selected' : ''}>Message Starts With</option>
              </select>
            </div>
          </div>

          <div class="v3-form-group mb-1">
            <div class="wa-fallback-check">
              <label class="v3-toggle-switch" title="Set as Fallback Bot">
                <input type="checkbox" id="inspIsDefault" ${isDefault ? 'checked' : ''}>
                <span class="v3-toggle-slider emerald"></span>
              </label>
              <label class="wa-checkbox-label mb-0" for="inspIsDefault">
                Set as Default Fallback Bot
              </label>
            </div>
            <div class="form-text wa-form-hint">Activates whenever customer queries do not match other specific keyword flows.</div>
          </div>
        `);
        return;
      }

      // Action Nodes Inspector
      if (node.action_type === 'delay') {
        finishInspector('Wait / Delay', `
          <div class="wa-inspector-card-badge amber">
            <i class="fi fi-rr-hourglass"></i> Step ${self.selectedNodeIndex + 1}: Wait / Typing Delay
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspStepTitle" class="v3-form-label">Step Label</label>
            <input type="text" id="inspStepTitle" class="form-control form-control-sm" value="${escapeHtml(node.title || 'Wait / Delay')}">
          </div>

          <div class="v3-form-group mb-2">
            <div class="d-flex justify-content-between align-items-center">
              <label for="inspDelaySeconds" class="v3-form-label mb-0">Delay Duration</label>
              <span class="badge bg-light text-dark border" id="inspDelayValLabel">${parseInt(node.delay_seconds || 3, 10)}s</span>
            </div>
            <input type="range" id="inspDelaySeconds" class="form-range mt-2" min="1" max="15" step="1" value="${parseInt(node.delay_seconds || 3, 10)}">
            <div class="d-flex justify-content-between text-muted wa-range-labels">
              <span>1 sec</span>
              <span>5 sec</span>
              <span>10 sec</span>
              <span>15 sec</span>
            </div>
            <div class="form-text wa-form-hint mt-2">
              Creates a natural human-like cadence by displaying the WhatsApp typing indicator before the next message sends.
            </div>
          </div>
        `);
        return;
      }

      if (node.action_type === 'interactive_menu') {
        const options = Array.isArray(node.options) ? node.options : ['Option 1', 'Option 2'];
        const optionsHtml = options.map((opt, oIdx) => `
          <div class="wa-quick-reply-row">
            <input type="text" class="form-control form-control-sm insp-qr-input" data-option-index="${oIdx}" value="${escapeHtml(opt)}">
            <button type="button" class="wa-quick-reply-del insp-qr-del" data-option-index="${oIdx}" title="Remove Choice">
              <i class="fi fi-rr-trash"></i>
            </button>
          </div>
        `).join('');

        finishInspector('Button / Quick Replies', `
          <div class="wa-inspector-card-badge indigo">
            <i class="fi fi-rr-list-check"></i> Step ${self.selectedNodeIndex + 1}: Quick Reply Menu
          </div>

          <!-- Quick Predefined Menu Presets -->
          <div class="wa-presets-box">
            <div class="wa-presets-box-title"><i class="fi fi-rr-magic-wand"></i> Quick Predefined Menus</div>
            <div class="wa-presets-pills-row">
              <button type="button" class="wa-preset-pill-btn btn-menu-preset" data-preset="main">🗺️ Agency Main</button>
              <button type="button" class="wa-preset-pill-btn btn-menu-preset" data-preset="tour_type">🏔️ Trip Types</button>
              <button type="button" class="wa-preset-pill-btn btn-menu-preset" data-preset="support">💬 Support Menu</button>
              <button type="button" class="wa-preset-pill-btn btn-menu-preset" data-preset="yes_no">✅ Yes / No</button>
            </div>
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspStepTitle" class="v3-form-label">Step Label</label>
            <input type="text" id="inspStepTitle" class="form-control form-control-sm" value="${escapeHtml(node.title || 'Button / Quick Replies')}">
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspReplyText" class="v3-form-label">Prompt Message</label>
            <textarea id="inspReplyText" class="form-control form-control-sm" rows="3">${escapeHtml(node.message_text || 'Please select an option below:')}</textarea>
          </div>

          <div class="v3-form-group mb-2">
            <label class="v3-form-label">Interactive Choices</label>
            <div class="wa-quick-replies-list" id="inspQuickRepliesList">
              ${optionsHtml}
            </div>
            <button type="button" class="wa-btn-add-choice" id="btnAddQuickReplyChoice">
              <i class="fi fi-rr-plus"></i> Add Choice
            </button>
          </div>
        `);
        return;
      }

      if (node.action_type === 'handoff_agent') {
        finishInspector('Human Agent Handoff', `
          <div class="wa-inspector-card-badge purple">
            <i class="fi fi-rr-user-headset"></i> Step ${self.selectedNodeIndex + 1}: Human Agent Handoff
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspStepTitle" class="v3-form-label">Step Label</label>
            <input type="text" id="inspStepTitle" class="form-control form-control-sm" value="${escapeHtml(node.title || 'Human Agent Handoff')}">
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspHandoffDept" class="v3-form-label">Assign Department</label>
            <select id="inspHandoffDept" class="selectjs2 selectJS form-select form-select-sm">
              <option value="sales" ${node.department === 'sales' ? 'selected' : ''}>Sales &amp; Tour Booking Desk</option>
              <option value="support" ${node.department === 'support' ? 'selected' : ''}>Customer Support</option>
              <option value="general" ${node.department === 'general' ? 'selected' : ''}>First Available Agent</option>
            </select>
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspHandoffNote" class="v3-form-label">Internal Operator Note</label>
            <textarea id="inspHandoffNote" class="form-control form-control-sm" rows="3" placeholder="Optional context note for the receiving agent...">${escapeHtml(node.note || '')}</textarea>
            <div class="form-text wa-form-hint">Transfers the WhatsApp thread to team inbox and notifies assigned team members.</div>
          </div>
        `);
        return;
      }

      if (node.action_type === 'collect_input') {
        const fKey = node.field_key || 'destination';
        const isButtons = node.input_mode !== 'text';
        let options = Array.isArray(node.options) ? [...node.options] : [];
        if (!options.length) {
          if (self.packageDestinations && self.packageDestinations.length > 0) {
            options = self.packageDestinations.slice(0, 4).map(d => `${self.getDestinationEmoji(d)} ${d}`);
          } else {
            options = ['🏔️ Manali & Himachal', '🏖️ Kerala & Munnar', '🏙️ Dubai Luxury', '⛺ Ladakh Adventure'];
          }
          node.options = options;
        }

        const optionsHtml = options.map((opt, oIdx) => `
          <div class="wa-quick-reply-row">
            <input type="text" class="form-control form-control-sm insp-collect-opt-input" data-option-index="${oIdx}" value="${escapeHtml(opt)}">
            <button type="button" class="wa-quick-reply-del insp-collect-opt-del" data-option-index="${oIdx}" title="Remove Choice">
              <i class="fi fi-rr-trash"></i>
            </button>
          </div>
        `).join('');

        // Real Tour Package Destination Chips
        const destSuggestions = (self.packageDestinations || []).map(d => `
          <button type="button" class="wa-dest-chip" data-dest="${escapeHtml(d)}" title="Click to add ${escapeHtml(d)} button choice">
            ${self.getDestinationEmoji(d)} ${escapeHtml(d)}
          </button>
        `).join('');

        // Tour packages dropdown options
        const packageOptions = (self.tourPackages || []).map(p => `
          <option value="${p.id}">${escapeHtml(p.title)} (${escapeHtml(p.city || p.state || 'Tour')})</option>
        `).join('');

        finishInspector('Collect Requirement / Choice', `
          <div class="wa-inspector-card-badge teal">
            <i class="fi fi-rr-edit"></i> Step ${self.selectedNodeIndex + 1}: Collect Requirement / Choice
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspStepTitle" class="v3-form-label">Step Label</label>
            <input type="text" id="inspStepTitle" class="form-control form-control-sm" value="${escapeHtml(node.title || 'Collect Requirement / Choice')}">
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspCollectField" class="v3-form-label">Save Answer To Lead Field</label>
            <select id="inspCollectField" class="selectjs2 selectJS form-select form-select-sm">
              <option value="destination" ${fKey === 'destination' ? 'selected' : ''}>Destination &amp; Duration</option>
              <option value="travel_dates" ${fKey === 'travel_dates' ? 'selected' : ''}>Travel Dates / Month</option>
              <option value="pax" ${fKey === 'pax' ? 'selected' : ''}>Number of Travelers / Guests</option>
              <option value="budget" ${fKey === 'budget' ? 'selected' : ''}>Budget Range</option>
              <option value="hotel_preference" ${fKey === 'hotel_preference' ? 'selected' : ''}>Hotel Preference (3★ / 4★ / 5★)</option>
              <option value="special_requests" ${fKey === 'special_requests' ? 'selected' : ''}>Special Requests / Dietary</option>
              <option value="custom_requirement" ${fKey === 'custom_requirement' ? 'selected' : ''}>Custom Requirement Note</option>
            </select>
          </div>

          <div class="v3-form-group mb-2">
            <label class="v3-form-label">Customer Response Mode</label>
            <div class="wa-segmented-radio-group">
              <label class="wa-radio-card ${isButtons ? 'is-active' : ''}" for="inspCollectModeButtons">
                <input type="radio" name="inspCollectMode" id="inspCollectModeButtons" value="buttons" ${isButtons ? 'checked' : ''}>
                <span class="wa-radio-custom-ring">
                  <span class="wa-radio-custom-dot"></span>
                </span>
                <span class="wa-radio-card-content">
                  <span class="wa-radio-card-title">🔘 Button Choices</span>
                  <span class="wa-radio-card-desc">Fast tap &amp; no typing</span>
                </span>
              </label>

              <label class="wa-radio-card ${!isButtons ? 'is-active' : ''}" for="inspCollectModeText">
                <input type="radio" name="inspCollectMode" id="inspCollectModeText" value="text" ${!isButtons ? 'checked' : ''}>
                <span class="wa-radio-custom-ring">
                  <span class="wa-radio-custom-dot"></span>
                </span>
                <span class="wa-radio-card-content">
                  <span class="wa-radio-card-title">✍️ Free Text</span>
                  <span class="wa-radio-card-desc">Customer types reply</span>
                </span>
              </label>
            </div>
          </div>

          <div class="v3-form-group mb-2">
            <div class="d-flex justify-content-between align-items-center">
              <label for="inspReplyText" class="v3-form-label mb-0">Prompt Question to User</label>
            </div>
            <div class="wa-token-row mb-1">
              <span class="text-muted small">Insert tag:</span>
              <button type="button" class="wa-token-pill" data-token="{name}">+ {name}</button>
              <button type="button" class="wa-token-pill" data-token="{destination}">+ {destination}</button>
            </div>
            <textarea id="inspReplyText" class="form-control form-control-sm" rows="3" placeholder="Ask customer for specific travel preferences...">${escapeHtml(node.message_text || '')}</textarea>
          </div>

          <div id="inspCollectButtonsWrap" class="${isButtons ? '' : 'is-hidden'}">
            <div class="v3-form-group mb-2">
              <div class="d-flex justify-content-between align-items-center">
                <label class="v3-form-label mb-0">Quick Reply Button Choices</label>
              </div>

              <!-- Real Database Tour Package Destination Suggestions -->
              <div class="wa-dest-suggestions-box mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="fw-semibold small text-dark"><i class="fi fi-rr-map-marker"></i> Suggest from Active Tour Packages</span>
                  <span class="badge bg-light text-secondary border">Real Destinations</span>
                </div>
                <div class="wa-dest-chips-wrap">
                  ${destSuggestions || '<span class="text-muted small">Loading active tour destinations...</span>'}
                </div>
                ${packageOptions ? `
                <div class="input-group input-group-sm mt-2">
                  <select id="inspCollectPackagePick" class="selectjs2 selectJS form-select form-select-sm">
                    <option value="">-- Or pick from active tour packages --</option>
                    ${packageOptions}
                  </select>
                  <button type="button" class="btn btn-outline-primary btn-sm" id="btnInsertPackageOption">
                    <i class="fi fi-rr-plus"></i> Add
                  </button>
                </div>
                ` : ''}
              </div>

              <div class="wa-preset-packs-row mb-2">
                <span class="text-muted small">Load Presets:</span>
                <button type="button" class="wa-preset-pack-btn btn-pack-dest" title="Load real destination buttons from tour packages">+ Destinations</button>
                <button type="button" class="wa-preset-pack-btn btn-pack-dur" title="Load real package duration buttons">+ Durations</button>
                <button type="button" class="wa-preset-pack-btn btn-pack-dates" title="Load travel dates / month buttons">+ Dates</button>
                <button type="button" class="wa-preset-pack-btn btn-pack-pax" title="Load group size buttons">+ Pax</button>
                <button type="button" class="wa-preset-pack-btn btn-pack-budg" title="Load budget range buttons">+ Budgets</button>
                <button type="button" class="wa-preset-pack-btn btn-pack-hotel" title="Load hotel preference buttons">+ Hotels</button>
                <button type="button" class="wa-preset-pack-btn btn-pack-flights" title="Load flight inclusion buttons">+ Flights</button>
              </div>

              <div class="wa-quick-replies-list" id="inspCollectButtonsList">
                ${optionsHtml}
              </div>
              <button type="button" class="wa-btn-add-choice mt-1" id="btnAddCollectOptBtn">
                <i class="fi fi-rr-plus"></i> Add Custom Button Choice
              </button>
              <div class="form-text wa-form-hint mt-2">
                Customer simply taps one of these buttons on WhatsApp to answer instantly without typing.
              </div>
            </div>
          </div>
        `);
        return;
      }

      if (node.action_type === 'condition_branch') {
        const cTarget = node.condition_target || 'last_button';
        const cOp = node.condition_operator || 'contains';
        const cVal = node.condition_value || (self.packageDestinations && self.packageDestinations[0] ? self.packageDestinations[0] : 'Manali');

        // TRUE Branch config
        const ifAction = node.if_action_type || 'send_itinerary';
        const ifPackageId = parseInt(node.if_package_id || 0, 10);
        const ifLabel = node.if_label || 'Dispatch Matched Tour Package Itinerary PDF';
        const ifMessage = node.if_message || '';
        const ifDept = node.if_department || 'Destination Specialist';
        const ifAmount = node.if_amount || 5000;

        // FALSE Branch config
        const elseAction = node.else_action_type || 'send_itinerary';
        const elsePackageId = parseInt(node.else_package_id || 0, 10);
        const elseLabel = node.else_label || 'Dispatch Alternative Holiday Package PDF';
        const elseMessage = node.else_message || '';
        const elseDept = node.else_department || 'Sales Team';
        const elseAmount = node.else_amount || 5000;

        // Active tour packages options for TRUE and FALSE branch selectors
        const ifPkgOptions = (self.tourPackages || []).map(p => `
          <option value="${p.id}" ${ifPackageId === p.id ? 'selected' : ''}>
            ${escapeHtml(p.title)} (${escapeHtml(p.city || p.state || 'Tour')})
          </option>
        `).join('');

        const elsePkgOptions = (self.tourPackages || []).map(p => `
          <option value="${p.id}" ${elsePackageId === p.id ? 'selected' : ''}>
            ${escapeHtml(p.title)} (${escapeHtml(p.city || p.state || 'Tour')})
          </option>
        `).join('');

        // Destination suggestion pills for Condition Value
        const condValChips = (self.packageDestinations || []).slice(0, 6).map(d => `
          <button type="button" class="wa-cond-val-chip" data-val="${escapeHtml(d)}">
            ${self.getDestinationEmoji(d)} ${escapeHtml(d)}
          </button>
        `).join('');

        finishInspector('If / Else Condition Branch', `
          <div class="wa-inspector-card-badge cyan">
            <i class="fi fi-rr-code-branch"></i> Step ${self.selectedNodeIndex + 1}: If / Else Condition Branch
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspStepTitle" class="v3-form-label">Step Label</label>
            <input type="text" id="inspStepTitle" class="form-control form-control-sm" value="${escapeHtml(node.title || 'If / Else Branch by Selection')}">
          </div>

          <!-- Quick Predefined Branch Rules -->
          <div class="wa-presets-box">
            <div class="wa-presets-box-title"><i class="fi fi-rr-magic-wand"></i> Quick Predefined Branch Rules</div>
            <div class="wa-presets-pills-row">
              <button type="button" class="wa-preset-pill-btn btn-cond-preset" data-target="last_button" data-val="Manali">🏔️ Match: Manali</button>
              <button type="button" class="wa-preset-pill-btn btn-cond-preset" data-target="last_button" data-val="Kerala">🏖️ Match: Kerala</button>
              <button type="button" class="wa-preset-pill-btn btn-cond-preset" data-target="last_button" data-val="Dubai">🏙️ Match: Dubai</button>
              <button type="button" class="wa-preset-pill-btn btn-cond-preset" data-target="budget" data-val="Luxury">💎 Match: Luxury</button>
            </div>
          </div>

          <!-- Evaluation Rule Box -->
          <div class="p-2 border rounded bg-light mb-3">
            <div class="fw-semibold small text-dark mb-2"><i class="fi fi-rr-filter"></i> 1. Condition Evaluation Rule</div>

            <div class="v3-form-group mb-2">
              <label for="inspCondTarget" class="v3-form-label">Evaluate On</label>
              <select id="inspCondTarget" class="selectjs2 selectJS form-select form-select-sm">
                <option value="last_button" ${cTarget === 'last_button' ? 'selected' : ''}>Selected Button / Quick Reply Choice</option>
                <option value="message_text" ${cTarget === 'message_text' ? 'selected' : ''}>Incoming Message Text / Keyword</option>
                <option value="destination" ${cTarget === 'destination' ? 'selected' : ''}>Lead Field: Destination</option>
                <option value="budget" ${cTarget === 'budget' ? 'selected' : ''}>Lead Field: Budget</option>
              </select>
            </div>

            <div class="row g-2 mb-2">
              <div class="col-5">
                <label for="inspCondOp" class="v3-form-label">Operator</label>
                <select id="inspCondOp" class="selectjs2 selectJS form-select form-select-sm">
                  <option value="contains" ${cOp === 'contains' ? 'selected' : ''}>Contains</option>
                  <option value="equals" ${cOp === 'equals' ? 'selected' : ''}>Equals Exact</option>
                </select>
              </div>
              <div class="col-7">
                <label for="inspCondVal" class="v3-form-label">Match Value</label>
                <input type="text" id="inspCondVal" class="form-control form-control-sm" value="${escapeHtml(cVal)}" placeholder="e.g. Manali, Kerala, Dubai">
              </div>
            </div>

            ${condValChips ? `
            <div class="d-flex flex-wrap align-items-center gap-1 mt-1">
              <span class="text-muted small">Quick Match:</span>
              ${condValChips}
            </div>
            ` : ''}
          </div>

          <!-- 2. TRUE PATH DECISION BOX -->
          <div class="wa-cond-config-box is-true">
            <div class="wa-cond-box-header is-true">
              <span><i class="fi fi-rr-check"></i> 2. WHAT TO DO ON TRUE (IF MATCHES)</span>
              <span class="badge bg-success-subtle text-success border border-success-subtle">Success Branch</span>
            </div>

            <div class="v3-form-group mb-2">
              <label for="inspCondIfActionType" class="v3-form-label">Decide Action on TRUE</label>
              <select id="inspCondIfActionType" class="selectjs2 selectJS form-select form-select-sm">
                <option value="send_itinerary" ${ifAction === 'send_itinerary' ? 'selected' : ''}>📄 Dispatch Tour Package Itinerary PDF</option>
                <option value="reply_text" ${ifAction === 'reply_text' ? 'selected' : ''}>💬 Send Tailored WhatsApp Message</option>
                <option value="handoff_agent" ${ifAction === 'handoff_agent' ? 'selected' : ''}>👩‍💼 Transfer to Destination Specialist</option>
                <option value="payment_link" ${ifAction === 'payment_link' ? 'selected' : ''}>💳 Send Advance Token Booking Link</option>
                <option value="next_step" ${ifAction === 'next_step' ? 'selected' : ''}>➡️ Continue to Next Automation Step</option>
              </select>
            </div>

            ${ifAction === 'send_itinerary' ? `
              <div class="v3-form-group mb-2">
                <label for="inspCondIfPackageId" class="v3-form-label">Select Tour Package Brochure</label>
                <select id="inspCondIfPackageId" class="selectjs2 selectJS form-select form-select-sm">
                  <option value="">-- Choose from Current Tour Packages --</option>
                  ${ifPkgOptions}
                </select>
              </div>
            ` : ''}

            ${ifAction === 'handoff_agent' ? `
              <div class="v3-form-group mb-2">
                <label for="inspCondIfDept" class="v3-form-label">Assign Desk</label>
                <select id="inspCondIfDept" class="selectjs2 selectJS form-select form-select-sm">
                  <option value="Destination Specialist" ${ifDept === 'Destination Specialist' ? 'selected' : ''}>Destination Specialist</option>
                  <option value="Sales Team" ${ifDept === 'Sales Team' ? 'selected' : ''}>Sales Team</option>
                  <option value="Custom VIP Desk" ${ifDept === 'Custom VIP Desk' ? 'selected' : ''}>Custom VIP Desk</option>
                </select>
              </div>
            ` : ''}

            ${ifAction === 'payment_link' ? `
              <div class="v3-form-group mb-2">
                <label for="inspCondIfAmount" class="v3-form-label">Deposit Amount (₹)</label>
                <input type="number" id="inspCondIfAmount" class="form-control form-control-sm" value="${escapeHtml(ifAmount)}">
              </div>
            ` : ''}

            <div class="v3-form-group mb-2">
              <label for="inspCondIfLabel" class="v3-form-label">Action Summary Title</label>
              <input type="text" id="inspCondIfLabel" class="form-control form-control-sm" value="${escapeHtml(ifLabel)}" placeholder="e.g. Dispatch Manali Package Brochure">
            </div>

            ${ifAction !== 'next_step' ? `
              <div class="v3-form-group mb-1">
                <label for="inspCondIfMsg" class="v3-form-label">WhatsApp Accompanying Message</label>
                <textarea id="inspCondIfMsg" class="form-control form-control-sm" rows="2" placeholder="Message dispatched on WhatsApp when condition matches...">${escapeHtml(ifMessage)}</textarea>
              </div>
            ` : ''}
          </div>

          <!-- 3. FALSE PATH DECISION BOX -->
          <div class="wa-cond-config-box is-false">
            <div class="wa-cond-box-header is-false">
              <span><i class="fi fi-rr-cross-small"></i> 3. WHAT TO DO ON FALSE (ELSE / OTHERWISE)</span>
              <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Fallback Branch</span>
            </div>

            <div class="v3-form-group mb-2">
              <label for="inspCondElseActionType" class="v3-form-label">Decide Action on FALSE</label>
              <select id="inspCondElseActionType" class="selectjs2 selectJS form-select form-select-sm">
                <option value="send_itinerary" ${elseAction === 'send_itinerary' ? 'selected' : ''}>📄 Dispatch Alternative Tour Package PDF</option>
                <option value="reply_text" ${elseAction === 'reply_text' ? 'selected' : ''}>💬 Send Fallback WhatsApp Message</option>
                <option value="handoff_agent" ${elseAction === 'handoff_agent' ? 'selected' : ''}>👩‍💼 Transfer to Support Desk</option>
                <option value="payment_link" ${elseAction === 'payment_link' ? 'selected' : ''}>💳 Send Advance Token Booking Link</option>
                <option value="next_step" ${elseAction === 'next_step' ? 'selected' : ''}>➡️ Continue to Next Automation Step</option>
              </select>
            </div>

            ${elseAction === 'send_itinerary' ? `
              <div class="v3-form-group mb-2">
                <label for="inspCondElsePackageId" class="v3-form-label">Select Alternative Tour Package</label>
                <select id="inspCondElsePackageId" class="selectjs2 selectJS form-select form-select-sm">
                  <option value="">-- Choose from Current Tour Packages --</option>
                  ${elsePkgOptions}
                </select>
              </div>
            ` : ''}

            ${elseAction === 'handoff_agent' ? `
              <div class="v3-form-group mb-2">
                <label for="inspCondElseDept" class="v3-form-label">Assign Desk</label>
                <select id="inspCondElseDept" class="selectjs2 selectJS form-select form-select-sm">
                  <option value="Sales Team" ${elseDept === 'Sales Team' ? 'selected' : ''}>Sales Team</option>
                  <option value="Support Desk" ${elseDept === 'Support Desk' ? 'selected' : ''}>Support Desk</option>
                </select>
              </div>
            ` : ''}

            ${elseAction === 'payment_link' ? `
              <div class="v3-form-group mb-2">
                <label for="inspCondElseAmount" class="v3-form-label">Deposit Amount (₹)</label>
                <input type="number" id="inspCondElseAmount" class="form-control form-control-sm" value="${escapeHtml(elseAmount)}">
              </div>
            ` : ''}

            <div class="v3-form-group mb-2">
              <label for="inspCondElseLabel" class="v3-form-label">Action Summary Title</label>
              <input type="text" id="inspCondElseLabel" class="form-control form-control-sm" value="${escapeHtml(elseLabel)}" placeholder="e.g. Dispatch Alternative Holiday Brochure">
            </div>

            ${elseAction !== 'next_step' ? `
              <div class="v3-form-group mb-1">
                <label for="inspCondElseMsg" class="v3-form-label">WhatsApp Fallback Message</label>
                <textarea id="inspCondElseMsg" class="form-control form-control-sm" rows="2" placeholder="Fallback message dispatched when condition does not match...">${escapeHtml(elseMessage)}</textarea>
              </div>
            ` : ''}
          </div>
          <div class="form-text wa-form-hint">
            The automation evaluates the customer's response and dynamically executes the corresponding TRUE or FALSE action path.
          </div>
        `);
        return;
      }

      if (node.action_type === 'send_itinerary') {
        finishInspector('Send Itinerary Package', `
          <div class="wa-inspector-card-badge rose">
            <i class="fi fi-rr-document-signed"></i> Step ${self.selectedNodeIndex + 1}: Send Itinerary Package / Brochure
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspStepTitle" class="v3-form-label">Step Label</label>
            <input type="text" id="inspStepTitle" class="form-control form-control-sm" value="${escapeHtml(node.title || 'Send Itinerary Package / PDF Brochure')}">
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspItinTitle" class="v3-form-label">Itinerary Title / Package Name</label>
            <input type="text" id="inspItinTitle" class="form-control form-control-sm" value="${escapeHtml(node.itinerary_title || '')}" placeholder="e.g. 7-Day Swiss Alps & Scenic Train Tour">
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspItinUrl" class="v3-form-label">Brochure PDF / Web Itinerary Link</label>
            <input type="text" id="inspItinUrl" class="form-control form-control-sm" value="${escapeHtml(node.itinerary_url || '')}" placeholder="https://example.com/brochures/package.pdf">
          </div>

          <div class="v3-form-group mb-2">
            <div class="d-flex justify-content-between align-items-center">
              <label for="inspReplyText" class="v3-form-label mb-0">Accompanying Message</label>
            </div>
            <div class="wa-token-row mb-1">
              <span class="text-muted small">Insert tag:</span>
              <button type="button" class="wa-token-pill" data-token="{name}">+ {name}</button>
              <button type="button" class="wa-token-pill" data-token="{company}">+ {company}</button>
            </div>
            <textarea id="inspReplyText" class="form-control form-control-sm" rows="4" placeholder="Personalized message sent with the itinerary attachment...">${escapeHtml(node.message_text || '')}</textarea>
            <div class="form-text wa-form-hint">Dispatches interactive rich PDF attachment card directly to the client's WhatsApp chat.</div>
          </div>
        `);
        return;
      }

      if (node.action_type === 'payment_link') {
        const curr = node.currency || 'USD';
        finishInspector('Payment Link / Advance Token', `
          <div class="wa-inspector-card-badge cyan">
            <i class="fi fi-rr-credit-card"></i> Step ${self.selectedNodeIndex + 1}: Payment Link / Booking Deposit
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspStepTitle" class="v3-form-label">Step Label</label>
            <input type="text" id="inspStepTitle" class="form-control form-control-sm" value="${escapeHtml(node.title || 'Payment Link / Booking Token Advance')}">
          </div>

          <div class="row g-2 mb-2">
            <div class="col-7">
              <label for="inspPaymentAmount" class="v3-form-label">Deposit Amount</label>
              <input type="text" id="inspPaymentAmount" class="form-control form-control-sm" value="${escapeHtml(node.amount || '500')}" placeholder="500">
            </div>
            <div class="col-5">
              <label for="inspPaymentCurrency" class="v3-form-label">Currency</label>
              <select id="inspPaymentCurrency" class="selectjs2 selectJS form-select form-select-sm">
                <option value="USD" ${curr === 'USD' ? 'selected' : ''}>USD ($)</option>
                <option value="EUR" ${curr === 'EUR' ? 'selected' : ''}>EUR (€)</option>
                <option value="GBP" ${curr === 'GBP' ? 'selected' : ''}>GBP (£)</option>
                <option value="INR" ${curr === 'INR' ? 'selected' : ''}>INR (₹)</option>
                <option value="AED" ${curr === 'AED' ? 'selected' : ''}>AED</option>
                <option value="AUD" ${curr === 'AUD' ? 'selected' : ''}>AUD</option>
              </select>
            </div>
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspReplyText" class="v3-form-label">Payment Message</label>
            <textarea id="inspReplyText" class="form-control form-control-sm" rows="3">${escapeHtml(node.message_text || 'Please complete your advance token payment to lock in your reservation:')}</textarea>
            <div class="form-text wa-form-hint">Generates an instant payment link via your connected payment gateway.</div>
          </div>
        `);
        return;
      }

      if (node.action_type === 'tag_lead') {
        const stage = node.lead_stage || 'Hot Lead - Requested Itinerary';
        finishInspector('Update Lead Stage & Tags', `
          <div class="wa-inspector-card-badge orange">
            <i class="fi fi-rr-tag"></i> Step ${self.selectedNodeIndex + 1}: Update Lead Stage &amp; Tags
          </div>

          <!-- Quick Predefined Stage & Tag Presets -->
          <div class="wa-presets-box">
            <div class="wa-presets-box-title"><i class="fi fi-rr-magic-wand"></i> Quick Predefined CRM Presets</div>
            <div class="wa-presets-pills-row">
              <button type="button" class="wa-preset-pill-btn btn-tag-preset" data-stage="Hot Lead - Requested Itinerary" data-tags="whatsapp_lead, high_intent, itinerary_sent">🔥 Hot Inquiry</button>
              <button type="button" class="wa-preset-pill-btn btn-tag-preset" data-stage="Proposal / Itinerary Sent" data-tags="quote_sent, follow_up_due">📄 Proposal Sent</button>
              <button type="button" class="wa-preset-pill-btn btn-tag-preset" data-stage="Follow-up Required" data-tags="token_pending, high_intent">💳 Token Pending</button>
              <button type="button" class="wa-preset-pill-btn btn-tag-preset" data-stage="Won / Confirmed Booking" data-tags="booking_confirmed, vip_client">🎉 Won / Confirmed</button>
            </div>
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspStepTitle" class="v3-form-label">Step Label</label>
            <input type="text" id="inspStepTitle" class="form-control form-control-sm" value="${escapeHtml(node.title || 'Update Lead Stage & Tags')}">
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspLeadStage" class="v3-form-label">Move Lead to Pipeline Stage</label>
            <select id="inspLeadStage" class="selectjs2 selectJS form-select form-select-sm">
              <option value="New Inquiry" ${stage === 'New Inquiry' ? 'selected' : ''}>New Inquiry</option>
              <option value="Qualified Prospect" ${stage === 'Qualified Prospect' ? 'selected' : ''}>Qualified Prospect</option>
              <option value="Hot Lead - Requested Itinerary" ${stage === 'Hot Lead - Requested Itinerary' ? 'selected' : ''}>Hot Lead - Requested Itinerary</option>
              <option value="Proposal / Itinerary Sent" ${stage === 'Proposal / Itinerary Sent' ? 'selected' : ''}>Proposal / Itinerary Sent</option>
              <option value="Follow-up Required" ${stage === 'Follow-up Required' ? 'selected' : ''}>Follow-up Required</option>
              <option value="Won / Confirmed Booking" ${stage === 'Won / Confirmed Booking' ? 'selected' : ''}>Won / Confirmed Booking</option>
            </select>
          </div>

          <div class="v3-form-group mb-2">
            <label for="inspLeadTags" class="v3-form-label">
              Apply Tags <span class="lead-label-hint">Comma Separated</span>
            </label>
            <input type="text" id="inspLeadTags" class="form-control form-control-sm" value="${escapeHtml(node.tags || 'whatsapp_itinerary_sent, high_intent')}" placeholder="e.g. whatsapp_lead, itinerary_sent">
            <div class="form-text wa-form-hint">Automatically attaches these tags to the contact in CRM upon reaching this flow step.</div>
          </div>
        `);
        return;
      }

      // Default: reply_text
      finishInspector('WhatsApp Auto-Reply', `
        <div class="wa-inspector-card-badge blue">
          <i class="fi fi-rr-comment-alt"></i> Step ${self.selectedNodeIndex + 1}: WhatsApp Auto-Reply
        </div>

        <!-- Quick Predefined Message Templates -->
        <div class="wa-presets-box">
          <div class="wa-presets-box-title"><i class="fi fi-rr-magic-wand"></i> Quick Predefined Templates</div>
          <div class="wa-presets-pills-row">
            <button type="button" class="wa-preset-pill-btn btn-apply-tmpl" data-tmpl="welcome">👋 Welcome</button>
            <button type="button" class="wa-preset-pill-btn btn-apply-tmpl" data-tmpl="after_hours">⏰ After-Hours</button>
            <button type="button" class="wa-preset-pill-btn btn-apply-tmpl" data-tmpl="office_location">📍 Location</button>
            <button type="button" class="wa-preset-pill-btn btn-apply-tmpl" data-tmpl="callback">📞 Call Request</button>
            <button type="button" class="wa-preset-pill-btn btn-apply-tmpl" data-tmpl="thank_you">🙏 Thank You</button>
          </div>
        </div>

        <div class="v3-form-group mb-2">
          <label for="inspStepTitle" class="v3-form-label">Step Label</label>
          <input type="text" id="inspStepTitle" class="form-control form-control-sm" value="${escapeHtml(node.title || 'Send Automated WhatsApp Reply')}">
        </div>

        <div class="v3-form-group mb-2">
          <div class="d-flex justify-content-between align-items-center">
            <label for="inspReplyText" class="v3-form-label mb-0">
              Message Content <span class="required">*</span>
            </label>
          </div>
          <div class="wa-token-row mb-1">
            <span class="text-muted small">Insert tag:</span>
            <button type="button" class="wa-token-pill" data-token="{name}">+ {name}</button>
            <button type="button" class="wa-token-pill" data-token="{company}">+ {company}</button>
            <button type="button" class="wa-token-pill" data-token="{phone}">+ {phone}</button>
          </div>
          <textarea id="inspReplyText" class="form-control form-control-sm" rows="5" placeholder="Type automated response message...">${escapeHtml(node.message_text || '')}</textarea>
          <div class="form-text wa-form-hint">Tags like {name} and {company} will be dynamically populated from the contact record.</div>
        </div>
      `);
    },

    saveVisualWorkflow: function () {
      const self = this;
      const name = $.trim($('#builderWfTitleInput').val());
      const description = $.trim($('#builderWfDescInput').val());
      const isActive = $('#builderWorkflowActiveToggle').is(':checked') ? 1 : 0;

      if (!name) {
        alert('Please enter a workflow title in the left panel.');
        $('#builderWfTitleInput').focus();
        return;
      }

      if (!self.currentNodes || self.currentNodes.length < 2) {
        alert('Please add at least one response step to this workflow.');
        return;
      }

      // Extract trigger properties from node 0
      const triggerNode = self.currentNodes[0] || {};
      const triggerType = triggerNode.trigger_type || 'keyword';
      const isDefault = (!!triggerNode.is_default || triggerType === 'default_fallback') ? 1 : 0;
      const keywords = $.trim(triggerNode.keywords || '');
      const matchType = triggerNode.match_type || 'contains';

      const payload = {
        action: 'save',
        id: self.currentWorkflowId,
        name: name,
        description: description,
        trigger_type: triggerType,
        keywords: keywords,
        match_type: matchType,
        is_default: isDefault,
        status: isActive,
        nodes: self.currentNodes
      };

      const $btn = $('#btnSaveVisualWorkflow');
      const originalText = $btn.html();
      $btn.prop('disabled', true).html('<i class="fi fi-rr-spinner animate-spin"></i> Saving...');

      $.ajax({
        url: self.apiUrl,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        dataType: 'json',
        success: function (res) {
          $btn.prop('disabled', false).html(originalText);
          if (res && res.status === 'success') {
            self.closeVisualBuilder();
            if (typeof window.popUpMessage === 'function') {
              window.popUpMessage('center', res.msg || 'Workflow saved successfully!', 'success');
            } else {
              alert(res.msg || 'Workflow saved successfully!');
            }
            self.loadData();
          } else {
            alert(res.msg || 'Failed to save workflow.');
          }
        },
        error: function () {
          $btn.prop('disabled', false).html(originalText);
          alert('Network or server error while saving workflow.');
        }
      });
    },

    // Backward compatible modal functions
    openWorkflowModal: function (id) {
      this.openVisualBuilder(id);
    },

    closeModal: function () {
      $('#waWorkflowModal').removeClass('is-open').fadeOut(150);
    },

    useTemplate: function (templateId) {
      this.openVisualBuilder(0, templateId);
    },

    saveWorkflow: function () {
      this.saveVisualWorkflow();
    },

    togglePlugin: function (isActive) {
      const self = this;
      $.ajax({
        url: self.apiUrl,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ action: 'toggle_plugin', is_active: isActive ? 1 : 0 }),
        dataType: 'json',
        success: function (res) {
          if (res && res.status === 'success') {
            if (typeof window.popUpMessage === 'function') {
              window.popUpMessage('center', res.msg, 'success');
            }
            if (isActive) {
              $('#waPluginStatusPill').removeClass('inactive').addClass('active').html('<span class="status-pulse-dot"></span> Active');
            } else {
              $('#waPluginStatusPill').removeClass('active').addClass('inactive').html('<span class="status-pulse-dot"></span> Setup Mode');
            }
          }
        }
      });
    },

    toggleWorkflow: function (id, status) {
      const self = this;
      $.ajax({
        url: self.apiUrl,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ action: 'toggle', id: id, status: status ? 1 : 0 }),
        dataType: 'json',
        success: function (res) {
          if (res && res.status === 'success') {
            self.loadData();
          }
        }
      });
    },

    deleteWorkflow: function (id) {
      const self = this;
      if (!confirm('Are you sure you want to delete this WhatsApp automation workflow?')) return;

      $.ajax({
        url: self.apiUrl,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ action: 'delete', id: id }),
        dataType: 'json',
        success: function (res) {
          if (res && res.status === 'success') {
            if (typeof window.popUpMessage === 'function') {
              window.popUpMessage('center', res.msg, 'success');
            }
            self.loadData();
          }
        }
      });
    },

    duplicateWorkflow: function (id) {
      const self = this;
      $.ajax({
        url: self.apiUrl,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ action: 'duplicate', id: id }),
        dataType: 'json',
        success: function (res) {
          if (res && res.status === 'success') {
            if (typeof window.popUpMessage === 'function') {
              window.popUpMessage('center', res.msg, 'success');
            }
            self.loadData();
          }
        }
      });
    },

    sendSimulatedMessage: function () {
      const self = this;
      const text = $('#waSimInput').val().trim();
      if (!text) return;

      // Append incoming bubble
      const incomingBubble = `<div class="wa-chat-bubble incoming">${escapeHtml(text)}</div>`;
      $('#waChatMessagesBody').append(incomingBubble);
      $('#waSimInput').val('');
      scrollChatToBottom();

      // Show typing indicator
      const typingIndicator = `<div class="wa-chat-bubble bot-reply wa-typing-indicator" id="waTypingDot">Typing...</div>`;
      $('#waChatMessagesBody').append(typingIndicator);
      scrollChatToBottom();

      $.ajax({
        url: self.apiUrl,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
          action: 'test_simulate',
          test_text: text,
          contact_name: 'Priya Sharma'
        }),
        dataType: 'json',
        success: function (res) {
          $('#waTypingDot').remove();
          if (res && res.status === 'success' && res.reply) {
            const replyBubble = `<div class="wa-chat-bubble bot-reply">${escapeHtml(res.reply)}</div>`;
            $('#waChatMessagesBody').append(replyBubble);

            if (res.buttons && res.buttons.length) {
              let btnsHtml = '<div class="wa-sim-buttons-wrap mt-2 d-flex flex-wrap gap-1">';
              res.buttons.forEach(function (b) {
                btnsHtml += `<button type="button" class="wa-btn-option-pill wa-sim-click-choice" data-choice="${escapeHtml(b)}"><i class="fi fi-rr-mouse"></i> ${escapeHtml(b)}</button>`;
              });
              btnsHtml += '</div>';
              $('#waChatMessagesBody').append(btnsHtml);
            }
          } else {
            const fallback = `<div class="wa-chat-bubble bot-reply wa-no-match-bubble">${escapeHtml(res.msg || 'No workflow matched this input.')}</div>`;
            $('#waChatMessagesBody').append(fallback);
          }

          // Update simulation trace panel
          if (res && res.simulated_steps && res.simulated_steps.length) {
            let traceHtml = '';
            res.simulated_steps.forEach(function (s, idx) {
              traceHtml += `
                <div class="wa-sim-step-item">
                  <span class="wa-step-num">${idx + 1}</span>
                  <div>
                    <div class="wa-sim-trace-title-text">${escapeHtml(s.step)}</div>
                    <div class="wa-sim-trace-desc-text">${escapeHtml(s.detail)}</div>
                  </div>
                </div>
              `;
            });
            $('#waSimTraceSteps').html(traceHtml);
          }
          scrollChatToBottom();
        },
        error: function () {
          $('#waTypingDot').remove();
        }
      });
    },

    loadLogs: function () {
      const self = this;
      const $tbody = $('#waLogsTbody');
      $tbody.html('<tr><td colspan="5" class="wa-table-empty-cell"><i class="fi fi-rr-spinner animate-spin"></i> Loading logs...</td></tr>');

      $.ajax({
        url: self.apiUrl + '?action=logs',
        type: 'GET',
        dataType: 'json',
        success: function (res) {
          $tbody.empty();
          if (res && res.status === 'success' && res.logs && res.logs.length) {
            res.logs.forEach(function (l) {
              const tr = `
                <tr>
                  <td class="wa-cell-meta">${escapeHtml(l.log_created_at)}</td>
                  <td class="wa-cell-phone">${escapeHtml(l.log_phone)}</td>
                  <td>${escapeHtml(l.workflow_name || 'Workflow #' + l.log_workflow_id)}</td>
                  <td><code>${escapeHtml(l.log_trigger_input || '')}</code></td>
                  <td><span class="wa-pill active">${escapeHtml(l.log_status)}</span></td>
                </tr>
              `;
              $tbody.append(tr);
            });
          } else {
            $tbody.html(`
              <tr>
                <td colspan="5">
                  <div class="wa-table-empty-box">
                    <div class="wa-table-empty-icon"><i class="fi fi-rr-time-past"></i></div>
                    <div class="wa-table-empty-title">No execution logs found yet</div>
                    <p class="wa-table-empty-desc">When incoming customer WhatsApp messages trigger automated bot responses, logs will appear here in real-time.</p>
                  </div>
                </td>
              </tr>
            `);
          }
        }
      });
    }
  };

  function escapeHtml(text) {
    if (!text) return '';
    return String(text)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function scrollChatToBottom() {
    const $box = $('#waChatMessagesBody');
    if ($box.length) {
      $box.scrollTop($box[0].scrollHeight);
    }
  }

  $(document).ready(function () {
    window.ISW_WhatsAppAutomation.init();
  });

})(jQuery);
