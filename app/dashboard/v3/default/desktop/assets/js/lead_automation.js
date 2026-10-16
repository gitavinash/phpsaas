/**
 * V3 Lead Flow & Distribution Automation Controller
 * Path: /app/dashboard/v3/default/desktop/assets/js/lead_automation.js
 */

(function ($) {
  'use strict';

  window.ISW_LeadAutomation = {
    apiUrl: '/api/v3/integration/lead_automation.php',
    workflows: [],
    agents: [],
    currentWorkflowId: 0,

    init: function () {
      if (!$('#leadAutomationApp').length) return;
      this.bindEvents();
      this.initSelect2Dropdowns('#leadAutomationApp');
      this.loadData();
    },

    initSelect2Dropdowns: function (parentSelector) {
      if (typeof $.fn.select2 !== 'function') return;
      const $context = parentSelector ? $(parentSelector) : $(document);
      $context.find('.selectjs2, .selectJS').each(function () {
        const $el = $(this);
        const isInsideModal = $el.closest('.lead-modal-card').length > 0;
        const options = {
          width: '100%'
        };
        if (isInsideModal) {
          options.dropdownParent = $el.closest('.lead-modal-card');
        }
        if (!$el.hasClass('select2-hidden-accessible')) {
          $el.select2(options);
        }
      });
    },

    bindEvents: function () {
      const self = this;

      // Tab switching
      $(document).on('click', '.lead-tab-btn', function () {
        const tab = $(this).data('tab');
        $('.lead-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.lead-tab-pane').hide();
        $('#leadTab_' + tab).show();

        if (tab === 'logs') {
          self.loadLogs();
        }
      });

      // Master Plugin Switch Toggle
      $(document).on('change', '#togglePluginActiveLead', function () {
        const isChecked = $(this).is(':checked');
        self.togglePlugin(isChecked);
      });

      // Refresh button
      $(document).on('click', '#btnRefreshLeadWorkflows', function () {
        self.loadData();
      });

      // Create Rule Button (Header & Empty state)
      $(document)
        .off('click', '#btnCreateLeadRule, #btnEmptyCreateRule, .btn-create-lead-rule')
        .on('click', '#btnCreateLeadRule, #btnEmptyCreateRule, .btn-create-lead-rule', function (e) {
          if (e) e.preventDefault();
          self.openRuleModal(0);
        });

      // Edit Rule
      $(document).on('click', '.btn-edit-lead-wf, .lead-action-btn.btn-edit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).attr('data-id') || $(this).data('id');
        self.openRuleModal(id);
      });

      // Toggle Single Rule
      $(document).on('change', '.lead-wf-status-toggle', function () {
        const id = $(this).attr('data-id') || $(this).data('id');
        const isChecked = $(this).is(':checked');
        self.toggleRule(id, isChecked);
      });

      // Delete Rule
      $(document).on('click', '.btn-delete-lead-wf, .lead-action-btn.btn-delete', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).attr('data-id') || $(this).data('id');
        self.deleteRule(id);
      });

      // Duplicate Rule
      $(document).on('click', '.btn-dup-lead-wf, .lead-action-btn.btn-dup', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).attr('data-id') || $(this).data('id');
        self.duplicateRule(id);
      });

      // Modal Close
      $(document).on('click', '#btnCloseLeadModal, #btnCancelLeadModal', function (e) {
        e.preventDefault();
        $('#leadWorkflowModal').removeClass('is-open').fadeOut(150);
      });

      // Click outside backdrop to close
      $(document).on('click', '#leadWorkflowModal', function (e) {
        if ($(e.target).is('#leadWorkflowModal')) {
          $('#leadWorkflowModal').removeClass('is-open').fadeOut(150);
        }
      });

      // Agent Pool Item Click
      $(document).on('click', '.lead-agent-pool-item', function (e) {
        if ($(e.target).closest('.v3-toggle-switch').length || $(e.target).is('input[type="checkbox"]')) {
          return;
        }
        const $chk = $(this).find('.lead-agent-chk');
        $chk.prop('checked', !$chk.is(':checked')).trigger('change');
      });

      $(document).on('change', '.lead-agent-chk', function () {
        const $item = $(this).closest('.lead-agent-pool-item');
        if ($(this).is(':checked')) {
          $item.addClass('selected');
        } else {
          $item.removeClass('selected');
        }
        self.updateSelectedAgentCount();
      });

      // Select All / Deselect All Toggle
      $(document).on('click', '#btnToggleAllLeadAgents', function (e) {
        e.preventDefault();
        const $visibleItems = $('.lead-agent-pool-item:visible');
        const $targetItems = $visibleItems.length ? $visibleItems : $('.lead-agent-pool-item');
        const $targetChks = $targetItems.find('.lead-agent-chk');
        const total = $targetChks.length;
        const checked = $targetChks.filter(':checked').length;
        const shouldCheck = checked < total;
        $targetChks.each(function () {
          $(this).prop('checked', shouldCheck);
          const $item = $(this).closest('.lead-agent-pool-item');
          if (shouldCheck) {
            $item.addClass('selected');
          } else {
            $item.removeClass('selected');
          }
        });
        self.updateSelectedAgentCount();
      });

      // Search / Filter Agents in Pool
      $(document).on('input', '#leadAgentSearchInput', function () {
        const q = ($(this).val() || '').toLowerCase().trim();
        $('.lead-agent-pool-item').each(function () {
          const name = ($(this).data('agent-name') || '').toString();
          if (!q || name.indexOf(q) !== -1) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });

      // Feature toggles collapse
      $(document).on('change', '#leadModalCreateTask', function () {
        if ($(this).is(':checked')) {
          $('#leadTaskSettingsCollapse').slideDown(150);
        } else {
          $('#leadTaskSettingsCollapse').slideUp(150);
        }
      });

      $(document).on('change', '#leadModalSendWa', function () {
        if ($(this).is(':checked')) {
          $('#leadWaSettingsCollapse').slideDown(150);
        } else {
          $('#leadWaSettingsCollapse').slideUp(150);
        }
      });

      // Insert Token in WhatsApp greeting
      $(document).on('click', '.lead-token-btn', function () {
        const token = $(this).data('token');
        const $textarea = $('#leadModalWaMsg');
        const pos = $textarea.prop('selectionStart') || 0;
        const text = $textarea.val();
        $textarea.val(text.substring(0, pos) + token + text.substring(pos));
        $textarea.focus();
      });

      // Save Form Submit
      $(document).on('submit', '#leadRuleForm', function (e) {
        e.preventDefault();
        self.saveRule();
      });

      // Simulator Run
      $(document).on('click', '#btnRunLeadSim', function () {
        self.runSimulation();
      });
    },

    loadData: function () {
      const self = this;
      $('#leadWorkflowsLoading').show();
      $('#leadWorkflowsEmpty').hide();

      $.ajax({
        url: self.apiUrl,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
          $('#leadWorkflowsLoading').hide();
          if (res && res.status === 'success') {
            self.workflows = res.workflows || [];
            self.agents = res.agents || [];

            // Master Plugin Status
            const isPluginActive = !!res.is_plugin_active;
            $('#togglePluginActiveLead').prop('checked', isPluginActive);
            if (isPluginActive) {
              $('#leadPluginStatusPill').removeClass('inactive').addClass('active').html('<span class="status-pulse-dot"></span> Active');
            } else {
              $('#leadPluginStatusPill').removeClass('active').addClass('inactive').html('<span class="status-pulse-dot"></span> Disabled');
            }

            // KPIs
            if (res.stats) {
              $('#statLeadTotalRules').text(res.stats.total_rules || 0);
              $('#statLeadActiveRules').text(res.stats.active_rules || 0);
              $('#statLeadDistributed').text(res.stats.total_leads_distributed || 0);
              $('#statLeadAgentsCount').text(res.stats.total_agents || 0);
            }

            self.renderRulesTable();
            self.renderAgentPoolList();
          }
        },
        error: function () {
          $('#leadWorkflowsLoading').hide();
        }
      });
    },

    renderRulesTable: function () {
      const self = this;
      const $tbody = $('#leadRulesTbody');
      $tbody.empty();

      if (!self.workflows.length) {
        $('#leadWorkflowsEmpty').show();
        $('#leadRulesTableWrapper').hide();
        return;
      }

      $('#leadWorkflowsEmpty').hide();
      $('#leadRulesTableWrapper').show();

      self.workflows.forEach(function (w) {
        const isActive = parseInt(w.workflow_status, 10) === 1;
        const priority = parseInt(w.workflow_priority, 10) || 10;
        const isRoundRobin = w.workflow_distribution_mode === 'round_robin';

        // Assigned agents preview
        let agentIds = [];
        if (Array.isArray(w.assigned_agents_list)) {
          agentIds = w.assigned_agents_list.map(x => parseInt(x, 10));
        } else if (typeof w.workflow_assigned_agents === 'string') {
          try {
            const parsed = JSON.parse(w.workflow_assigned_agents);
            agentIds = Array.isArray(parsed) ? parsed.map(x => parseInt(x, 10)) : [];
          } catch (e) {
            agentIds = [];
          }
        }

        const lastAgentId = parseInt(w.workflow_last_agent_id, 10) || 0;
        const maxDisplay = 3;
        const visibleAgentIds = agentIds.slice(0, maxDisplay);
        const remaining = agentIds.length - maxDisplay;

        const allNames = agentIds.map(function (id) {
          const a = self.agents.find(ag => parseInt(ag.id, 10) === id);
          return a ? a.name : 'Agent #' + id;
        });

        let agentsPreviewHtml = '';
        if (!agentIds.length) {
          agentsPreviewHtml = '<span class="lead-text-muted lead-text-small">No agents assigned</span>';
        } else if (agentIds.length === 1) {
          const singleAgent = self.agents.find(a => parseInt(a.id, 10) === agentIds[0]);
          const agName = singleAgent ? singleAgent.name : 'Agent #' + agentIds[0];
          const initials = (agName || 'A')
            .trim()
            .split(/\s+/)
            .map(n => n.charAt(0))
            .slice(0, 2)
            .join('')
            .toUpperCase() || 'AG';
          agentsPreviewHtml = `
            <div class="lead-agent-single" title="Assigned Agent: ${escapeHtml(agName)}">
              <div class="lead-stack-avatar is-single c-0">${escapeHtml(initials)}</div>
              <span class="lead-agent-single-name">${escapeHtml(agName)}</span>
            </div>
          `;
        } else {
          let avatarsHtml = '';
          visibleAgentIds.forEach(function (agId, idx) {
            const agentObj = self.agents.find(a => parseInt(a.id, 10) === agId);
            const agName = agentObj ? agentObj.name : 'Agent #' + agId;
            const isPointer = isRoundRobin && agId === lastAgentId;
            const initials = (agName || 'A')
              .trim()
              .split(/\s+/)
              .map(n => n.charAt(0))
              .slice(0, 2)
              .join('')
              .toUpperCase() || 'AG';
            avatarsHtml += `
              <div class="lead-stack-avatar c-${idx % 5} ${isPointer ? 'is-pointer' : ''}" title="${isPointer ? 'Next in rotation: ' + escapeHtml(agName) : escapeHtml(agName)}">
                ${escapeHtml(initials)}
              </div>
            `;
          });

          let badgeHtml = '';
          if (remaining > 0) {
            badgeHtml = `
              <span class="lead-stack-count-badge" title="All Assigned: ${escapeHtml(allNames.join(', '))}">
                +${remaining} more
              </span>
            `;
          } else {
            badgeHtml = `
              <span class="lead-stack-count-badge" title="All Assigned: ${escapeHtml(allNames.join(', '))}">
                ${agentIds.length} agents
              </span>
            `;
          }

          agentsPreviewHtml = `
            <div class="lead-agent-stack-wrap" title="Assigned Agent Pool (${agentIds.length}): ${escapeHtml(allNames.join(', '))}">
              <div class="lead-avatar-stack">${avatarsHtml}</div>
              ${badgeHtml}
            </div>
          `;
        }

        const tr = `
          <tr>
            <td>
              <span class="lead-priority-badge ${priority <= 3 ? 'p-high' : ''}">#${priority}</span>
            </td>
            <td>
              <div class="lead-rule-name-cell">${escapeHtml(w.workflow_name)}</div>
              <div class="lead-rule-desc-cell">${escapeHtml(w.workflow_description || 'Incoming lead distribution rule')}</div>
            </td>
            <td>
              <span class="lead-mode-pill ${isRoundRobin ? 'round_robin' : 'specific'}">
                <i class="fi ${isRoundRobin ? 'fi-rr-refresh' : 'fi-rr-user'}"></i> ${isRoundRobin ? 'Round-Robin' : 'Specific Agent'}
              </span>
            </td>
            <td>
              ${agentsPreviewHtml}
            </td>
            <td>
              <span class="lead-bold-title">${w.workflow_execution_count || 0}</span>
              <span class="lead-text-muted lead-text-xs"> leads</span>
            </td>
            <td>
              <label class="v3-toggle-switch">
                <input type="checkbox" class="lead-wf-status-toggle" data-id="${w.workflow_id}" ${isActive ? 'checked' : ''}>
                <span class="v3-toggle-slider"></span>
              </label>
            </td>
            <td>
              <div class="lead-action-btn-group">
                <button type="button" class="lead-action-btn btn-edit-lead-wf btn-edit" data-id="${w.workflow_id}" title="Edit Rule">
                  <i class="fi fi-rr-edit"></i>
                </button>
                <button type="button" class="lead-action-btn btn-dup-lead-wf btn-dup" data-id="${w.workflow_id}" title="Duplicate Rule">
                  <i class="fi fi-rr-copy"></i>
                </button>
                <button type="button" class="lead-action-btn btn-delete-lead-wf btn-delete is-danger" data-id="${w.workflow_id}" title="Delete Rule">
                  <i class="fi fi-rr-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
        $tbody.append(tr);
      });
    },

    updateSelectedAgentCount: function () {
      const total = $('.lead-agent-chk').length;
      const checked = $('.lead-agent-chk:checked').length;
      $('#leadSelectedAgentCount').text(checked + ' of ' + total + ' selected');
      if (total > 0 && checked === total) {
        $('#btnToggleAllLeadAgents').text('Deselect All');
      } else {
        $('#btnToggleAllLeadAgents').text('Select All');
      }
    },

    renderAgentPoolList: function (selectedAgentIds) {
      const self = this;
      const $container = $('#leadModalAgentPool');
      $container.empty();

      const numIds = (selectedAgentIds || []).map(x => parseInt(x, 10));

      if (!self.agents.length) {
        $container.html('<div class="lead-table-empty-cell">No active agents found in system.</div>');
        self.updateSelectedAgentCount();
        return;
      }

      const colorClasses = ['c-0', 'c-1', 'c-2', 'c-3', 'c-4'];

      self.agents.forEach(function (a, idx) {
        const agentId = parseInt(a.id, 10);
        const isChecked = numIds.includes(agentId);
        const initials = (a.name || 'Agent')
          .trim()
          .split(/\s+/)
          .map(n => n.charAt(0))
          .slice(0, 2)
          .join('')
          .toUpperCase() || 'AG';
        const colorCls = colorClasses[idx % colorClasses.length];

        const item = `
          <div class="lead-agent-pool-item ${isChecked ? 'selected' : ''}" data-agent-id="${agentId}" data-agent-name="${escapeHtml((a.name || '').toLowerCase())}">
            <div class="lead-agent-pool-item-main">
              <div class="lead-agent-pool-avatar ${colorCls}">${escapeHtml(initials)}</div>
              <div class="lead-agent-pool-details">
                <div class="lead-agent-pool-name" title="${escapeHtml(a.name)}">${escapeHtml(a.name)}</div>
                <div class="lead-agent-pool-meta" title="${escapeHtml(a.role || 'Agent')} &bull; ${escapeHtml(a.email || '')}">${escapeHtml(a.role || 'Agent')} &bull; ${escapeHtml(a.email || '')}</div>
              </div>
            </div>
            <div class="lead-agent-pool-item-right">
              <div class="lead-agent-pool-leads-pill">${a.active_leads_count || 0} active</div>
              <label class="v3-toggle-switch v3-toggle-sm" title="Toggle agent selection">
                <input type="checkbox" class="lead-agent-chk" value="${agentId}" ${isChecked ? 'checked' : ''}>
                <span class="v3-toggle-slider"></span>
              </label>
            </div>
          </div>
        `;
        $container.append(item);
      });

      self.updateSelectedAgentCount();
    },

    openRuleModal: function (id) {
      const self = this;
      const targetId = id ? parseInt(id, 10) : 0;
      self.currentWorkflowId = targetId;

      const form = document.getElementById('leadRuleForm');
      if (form && typeof form.reset === 'function') {
        form.reset();
      }
      $('#leadModalId').val(self.currentWorkflowId);
      $('#leadAgentSearchInput').val('');

      let selectedAgents = [];

      if (targetId > 0) {
        $('#leadModalTitle').text('Edit Lead Distribution Rule');
        const wf = self.workflows.find(w => parseInt(w.workflow_id, 10) === targetId);
        if (wf) {
          $('#leadModalName').val(wf.workflow_name || '');
          $('#leadModalDesc').val(wf.workflow_description || '');
          $('#leadModalSources').val(wf.workflow_sources || 'all').trigger('change');
          $('#leadModalDestination').val(wf.workflow_destination_filter || '');
          $('#leadModalMode').val(wf.workflow_distribution_mode || 'round_robin').trigger('change');
          $('#leadModalPriority').val(wf.workflow_priority || 10);

          const createTask = parseInt(wf.workflow_create_task, 10) === 1;
          $('#leadModalCreateTask').prop('checked', createTask);
          if (createTask) {
            $('#leadTaskSettingsCollapse').show();
          } else {
            $('#leadTaskSettingsCollapse').hide();
          }
          $('#leadModalTaskType').val(wf.workflow_task_type || 'Call').trigger('change');
          $('#leadModalTaskHours').val(wf.workflow_task_due_hours || 2);
          $('#leadModalTaskNote').val(wf.workflow_task_note || 'Follow up with newly distributed lead');

          const sendWa = parseInt(wf.workflow_send_whatsapp_greeting, 10) === 1;
          $('#leadModalSendWa').prop('checked', sendWa);
          if (sendWa) {
            $('#leadWaSettingsCollapse').show();
          } else {
            $('#leadWaSettingsCollapse').hide();
          }
          $('#leadModalWaMsg').val(wf.workflow_whatsapp_message || '');

          if (Array.isArray(wf.assigned_agents_list)) {
            selectedAgents = wf.assigned_agents_list.map(x => parseInt(x, 10));
          } else if (typeof wf.workflow_assigned_agents === 'string') {
            try {
              const parsed = JSON.parse(wf.workflow_assigned_agents);
              selectedAgents = Array.isArray(parsed) ? parsed.map(x => parseInt(x, 10)) : [];
            } catch (e) {
              selectedAgents = [];
            }
          }
        }
      } else {
        $('#leadModalTitle').text('Create Lead Distribution Rule');
        $('#leadModalName').val('');
        $('#leadModalDesc').val('');
        $('#leadModalSources').val('all').trigger('change');
        $('#leadModalDestination').val('');
        $('#leadModalMode').val('round_robin').trigger('change');
        $('#leadModalPriority').val(10);
        $('#leadModalCreateTask').prop('checked', true);
        $('#leadTaskSettingsCollapse').show();
        $('#leadModalTaskType').val('Call').trigger('change');
        $('#leadModalTaskHours').val(2);
        $('#leadModalTaskNote').val('Follow up with newly distributed lead');
        $('#leadModalSendWa').prop('checked', false);
        $('#leadWaSettingsCollapse').hide();
        $('#leadModalWaMsg').val('');
        // By default select all active agents
        selectedAgents = self.agents.map(a => parseInt(a.id, 10));
      }

      self.renderAgentPoolList(selectedAgents);

      // Open modal reliably with is-open flex class and init Select2
      $('#leadWorkflowModal').addClass('is-open').fadeIn(150, function () {
        self.initSelect2Dropdowns('#leadWorkflowModal');
      });
    },

    saveRule: function () {
      const self = this;
      const id = $('#leadModalId').val();
      const name = $('#leadModalName').val();
      const description = $('#leadModalDesc').val();
      const sources = $('#leadModalSources').val();
      const destination_filter = $('#leadModalDestination').val();
      const distribution_mode = $('#leadModalMode').val();
      const priority = $('#leadModalPriority').val();

      const create_task = $('#leadModalCreateTask').is(':checked') ? 1 : 0;
      const task_type = $('#leadModalTaskType').val();
      const task_due_hours = $('#leadModalTaskHours').val();
      const task_note = $('#leadModalTaskNote').val();

      const send_whatsapp_greeting = $('#leadModalSendWa').is(':checked') ? 1 : 0;
      const whatsapp_message = $('#leadModalWaMsg').val();

      const assigned_agents = [];
      $('.lead-agent-chk:checked').each(function () {
        assigned_agents.push(parseInt($(this).val(), 10));
      });

      if (!assigned_agents.length) {
        alert('Please select at least one agent in the distribution pool.');
        return;
      }

      const payload = {
        action: 'save',
        id: id,
        name: name,
        description: description,
        sources: sources,
        destination_filter: destination_filter,
        distribution_mode: distribution_mode,
        priority: priority,
        assigned_agents: assigned_agents,
        create_task: create_task,
        task_type: task_type,
        task_due_hours: task_due_hours,
        task_note: task_note,
        send_whatsapp_greeting: send_whatsapp_greeting,
        whatsapp_message: whatsapp_message
      };

      $('#btnSaveLeadRule').prop('disabled', true).text('Saving...');

      $.ajax({
        url: self.apiUrl,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        dataType: 'json',
        success: function (res) {
          $('#btnSaveLeadRule').prop('disabled', false).html('<i class="fi fi-rr-check"></i> Save Rule');
          if (res && res.status === 'success') {
            $('#leadWorkflowModal').fadeOut(150);
            if (typeof window.popUpMessage === 'function') {
              window.popUpMessage('center', res.msg, 'success');
            }
            self.loadData();
          } else {
            alert(res.msg || 'Failed to save rule.');
          }
        },
        error: function () {
          $('#btnSaveLeadRule').prop('disabled', false).html('<i class="fi fi-rr-check"></i> Save Rule');
          alert('Network or server error while saving rule.');
        }
      });
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
              $('#leadPluginStatusPill').removeClass('inactive').addClass('active').html('<span class="status-pulse-dot"></span> Active');
            } else {
              $('#leadPluginStatusPill').removeClass('active').addClass('inactive').html('<span class="status-pulse-dot"></span> Disabled');
            }
          }
        }
      });
    },

    toggleRule: function (id, status) {
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

    deleteRule: function (id) {
      const self = this;
      if (!confirm('Are you sure you want to delete this lead distribution rule?')) return;

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

    duplicateRule: function (id) {
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

    runSimulation: function () {
      const self = this;
      const name = $('#simLeadName').val().trim() || 'Aarav Patel';
      const source = $('#simLeadSource').val();
      const subject = $('#simLeadSubject').val().trim() || 'Kashmir Tour';

      $('#btnRunLeadSim').prop('disabled', true).text('Evaluating...');

      $.ajax({
        url: self.apiUrl,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
          action: 'test_simulate',
          name: name,
          source: source,
          subject: subject
        }),
        dataType: 'json',
        success: function (res) {
          $('#btnRunLeadSim').prop('disabled', false).html('<i class="fi fi-rr-play"></i> Run Distribution Test');
          if (res && res.status === 'success') {
            if (res.matched && res.assigned_agent) {
              $('#simResultBox').show();
              $('#simEmptyResult').hide();
              $('#simMatchedRule').text(res.workflow_name);
              $('#simAssignedAgent').text(res.assigned_agent.name);
              $('#simDistributionMode').text(res.distribution_mode === 'round_robin' ? 'Round-Robin Rotation' : 'Specific Agent Routing');

              let stepsHtml = '';
              (res.simulated_steps || []).forEach(function (s) {
                stepsHtml += `
                  <div class="lead-sim-step">
                    <span class="lead-sim-num"><i class="fi fi-rr-check"></i></span>
                    <div>
                      <div class="lead-sim-step-title">${escapeHtml(s.step)}</div>
                      <div class="lead-sim-step-desc">${escapeHtml(s.detail)}</div>
                    </div>
                  </div>
                `;
              });
              $('#simStepsContainer').html(stepsHtml);
            } else {
              $('#simResultBox').hide();
              $('#simEmptyResult').show().text(res.msg || 'No rule matched this test lead.');
            }
          }
        },
        error: function () {
          $('#btnRunLeadSim').prop('disabled', false).html('<i class="fi fi-rr-play"></i> Run Distribution Test');
        }
      });
    },

    loadLogs: function () {
      const self = this;
      const $tbody = $('#leadLogsTbody');
      $tbody.html('<tr><td colspan="6" class="lead-table-empty-cell"><i class="fi fi-rr-spinner animate-spin"></i> Loading distribution logs...</td></tr>');

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
                  <td class="lead-cell-meta">${escapeHtml(l.log_created_at)}</td>
                  <td class="lead-cell-leadname">${escapeHtml(l.log_lead_name || 'Lead #' + l.log_lead_id)}</td>
                  <td><span class="lead-source-badge">${escapeHtml(l.log_lead_source || 'direct')}</span></td>
                  <td>${escapeHtml(l.workflow_name || 'Rule #' + l.log_workflow_id)}</td>
                  <td class="lead-assigned-agent-cell"><i class="fi fi-rr-user"></i> ${escapeHtml(l.log_assigned_agent_name || 'Agent #' + l.log_assigned_agent_id)}</td>
                  <td><span class="lead-mode-pill round_robin">${escapeHtml(l.log_distribution_mode)}</span></td>
                </tr>
              `;
              $tbody.append(tr);
            });
          } else {
            $tbody.html(`
              <tr>
                <td colspan="6">
                  <div class="lead-table-empty-box">
                    <div class="lead-table-empty-icon"><i class="fi fi-rr-time-past"></i></div>
                    <div class="lead-table-empty-title">No distribution logs recorded yet</div>
                    <p class="lead-table-empty-desc">When incoming leads arrive from connected sources, their routing decisions and assigned agents will appear here in real-time.</p>
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

  $(document).ready(function () {
    window.ISW_LeadAutomation.init();
  });

})(jQuery);
