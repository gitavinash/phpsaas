/**
 * V3 WhatsApp Chat Automation Controller
 * Path: /app/dashboard/v3/default/desktop/assets/js/whatsapp_automation.js
 */

(function ($) {
  'use strict';

  window.ISW_WhatsAppAutomation = {
    apiUrl: '/api/v3/integration/whatsapp_automation.php',
    workflows: [],
    templates: [],
    currentWorkflowId: 0,

    init: function () {
      if (!$('#whatsappAutomationApp').length) return;
      this.bindEvents();
      this.initSelect2Dropdowns('#whatsappAutomationApp');
      this.loadData();
    },

    initSelect2Dropdowns: function (parentSelector) {
      if (typeof $.fn.select2 !== 'function') return;
      const $context = parentSelector ? $(parentSelector) : $(document);
      $context.find('.selectjs2, .selectJS').each(function () {
        const $el = $(this);
        const isInsideModal = $el.closest('.wa-modal-backdrop').length > 0;
        const options = {
          width: '100%'
        };
        if (isInsideModal) {
          options.dropdownParent = $('#waWorkflowModal');
        }
        if (!$el.hasClass('select2-hidden-accessible')) {
          $el.select2(options);
        }
      });
    },

    bindEvents: function () {
      const self = this;

      // Tab switching
      $(document).on('click', '.wa-tab-btn', function () {
        const tab = $(this).data('tab');
        $('.wa-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.wa-tab-pane').hide();
        $('#waTab_' + tab).show();

        if (tab === 'logs') {
          self.loadLogs();
        }
      });

      // Master Plugin Switch Toggle
      $(document).on('change', '#togglePluginActiveWa', function () {
        const isChecked = $(this).is(':checked');
        self.togglePlugin(isChecked);
      });

      // Refresh button
      $(document).on('click', '#btnRefreshWaWorkflows', function () {
        self.loadData();
      });

      // New Workflow Button (Header button & Empty state button)
      $(document)
        .off('click', '#btnCreateWaWorkflow, #btnEmptyCreateWf, .btn-create-wa-wf')
        .on('click', '#btnCreateWaWorkflow, #btnEmptyCreateWf, .btn-create-wa-wf', function (e) {
          if (e) e.preventDefault();
          self.openWorkflowModal(0);
        });

      // Trigger Type Change
      $(document).off('change', '#waModalTriggerType').on('change', '#waModalTriggerType', function () {
        const val = $(this).val();
        if (val === 'default_fallback') {
          $('#waKeywordsGroup').slideUp(150);
        } else {
          $('#waKeywordsGroup').slideDown(150);
        }
      });

      // Edit Workflow
      $(document).off('click', '.btn-edit-wa-wf').on('click', '.btn-edit-wa-wf', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).attr('data-id') || $(this).data('id');
        self.openWorkflowModal(id);
      });

      // Toggle Single Workflow
      $(document).off('change', '.wa-wf-status-toggle').on('change', '.wa-wf-status-toggle', function () {
        const id = $(this).attr('data-id') || $(this).data('id');
        const isChecked = $(this).is(':checked');
        self.toggleWorkflow(id, isChecked);
      });

      // Delete Workflow
      $(document).off('click', '.btn-delete-wa-wf').on('click', '.btn-delete-wa-wf', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).attr('data-id') || $(this).data('id');
        self.deleteWorkflow(id);
      });

      // Duplicate Workflow
      $(document).off('click', '.btn-dup-wa-wf').on('click', '.btn-dup-wa-wf', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).attr('data-id') || $(this).data('id');
        self.duplicateWorkflow(id);
      });

      // Use Template Button
      $(document).off('click', '.btn-use-wa-template').on('click', '.btn-use-wa-template', function (e) {
        e.preventDefault();
        const templateId = $(this).attr('data-id') || $(this).data('id');
        self.useTemplate(templateId);
      });

      // Modal Close
      $(document)
        .off('click', '#btnCloseWaModal, #btnCancelWaModal')
        .on('click', '#btnCloseWaModal, #btnCancelWaModal', function (e) {
          if (e) e.preventDefault();
          $('#waWorkflowModal').removeClass('is-open').fadeOut(150);
        });

      // Click outside backdrop to close
      $(document).off('click', '#waWorkflowModal').on('click', '#waWorkflowModal', function (e) {
        if ($(e.target).is('#waWorkflowModal')) {
          $('#waWorkflowModal').removeClass('is-open').fadeOut(150);
        }
      });

      // Insert Token in message editor
      $(document).on('click', '.wa-token-btn', function () {
        const token = $(this).data('token');
        const $textarea = $('#waModalReplyText');
        const pos = $textarea.prop('selectionStart') || 0;
        const text = $textarea.val();
        $textarea.val(text.substring(0, pos) + token + text.substring(pos));
        $textarea.focus();
      });

      // Workflow Form Submit
      $(document).on('submit', '#waWorkflowForm', function (e) {
        e.preventDefault();
        self.saveWorkflow();
      });

      // Simulator: Quick Keyword Click
      $(document).on('click', '.wa-sim-chip', function () {
        const text = $(this).text().trim().replace(/['"]/g, '');
        $('#waSimInput').val(text);
        $('#waSimSendBtn').trigger('click');
      });

      // Simulator: Send Message
      $(document).on('click', '#waSimSendBtn', function () {
        self.sendSimulatedMessage();
      });

      $(document).on('keypress', '#waSimInput', function (e) {
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
        $('#waWorkflowsTableWrapper').hide();
        return;
      }

      $('#waWorkflowsEmpty').hide();
      $('#waWorkflowsTableWrapper').show();

      self.workflows.forEach(function (w) {
        const isActive = parseInt(w.workflow_status, 10) === 1;
        const isDefault = parseInt(w.workflow_is_default, 10) === 1;

        let triggerHtml = '';
        if (isDefault) {
          triggerHtml = '<span class="wa-pill fallback"><i class="fi fi-rr-refresh"></i> Default Fallback</span>';
        } else {
          const kws = (w.workflow_keywords || '').split(',').map(k => k.trim()).filter(Boolean);
          const chips = kws.map(k => `<span class="wa-keyword-tag">${escapeHtml(k)}</span>`).join('');
          triggerHtml = `<div class="wa-keyword-chips">${chips}</div>`;
        }

        const nodes = Array.isArray(w.workflow_nodes) ? w.workflow_nodes : [];
        let replyPreview = 'Standard Automated Reply';
        for (let i = 0; i < nodes.length; i++) {
          if (nodes[i].message_text || nodes[i].text) {
            replyPreview = nodes[i].message_text || nodes[i].text;
            break;
          }
        }
        if (replyPreview.length > 80) {
          replyPreview = replyPreview.substring(0, 80) + '...';
        }

        const tr = `
          <tr>
            <td>
              <div class="wa-workflow-name-cell">${escapeHtml(w.workflow_name)}</div>
              <div class="wa-workflow-desc-cell">${escapeHtml(w.workflow_description || 'WhatsApp conversation automation')}</div>
            </td>
            <td>${triggerHtml}</td>
            <td>
              <div class="wa-reply-preview-cell">"${escapeHtml(replyPreview)}"</div>
            </td>
            <td>
              <span class="wa-bold-count">${w.workflow_execution_count || 0}</span>
              <span class="wa-text-xs-muted"> msgs</span>
            </td>
            <td>
              <label class="v3-toggle-switch">
                <input type="checkbox" class="wa-wf-status-toggle" data-id="${w.workflow_id}" ${isActive ? 'checked' : ''}>
                <span class="v3-toggle-slider"></span>
              </label>
            </td>
            <td>
              <div class="wa-action-btn-group">
                <button type="button" class="wa-action-btn btn-edit-wa-wf btn-edit" data-id="${w.workflow_id}" title="Edit Workflow">
                  <i class="fi fi-rr-edit"></i>
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

    openWorkflowModal: function (id) {
      const self = this;
      const targetId = id ? parseInt(id, 10) : 0;
      self.currentWorkflowId = targetId;

      // 1. Instantly display modal so user gets immediate visual feedback
      const $modal = $('#waWorkflowModal');
      $modal.addClass('is-open').stop(true, true).fadeIn(150, function () {
        self.initSelect2Dropdowns('#waWorkflowModal');
      });

      // 2. Safe form reset and population
      try {
        const form = document.getElementById('waWorkflowForm');
        if (form && typeof form.reset === 'function') {
          form.reset();
        }
        $('#waModalId').val(self.currentWorkflowId);

        if (targetId > 0) {
          $('#waModalTitle').text('Edit WhatsApp Chat Workflow');
          const wf = (self.workflows || []).find(w => parseInt(w.workflow_id, 10) === targetId);
          if (wf) {
            $('#waModalName').val(wf.workflow_name || '');
            $('#waModalDesc').val(wf.workflow_description || '');
            $('#waModalTriggerType').val(wf.workflow_trigger_type || 'keyword').trigger('change');
            $('#waModalKeywords').val(wf.workflow_keywords || '');
            $('#waModalMatchType').val(wf.workflow_match_type || 'contains').trigger('change');
            $('#waModalIsDefault').prop('checked', parseInt(wf.workflow_is_default, 10) === 1);

            const nodes = Array.isArray(wf.workflow_nodes) ? wf.workflow_nodes : [];
            for (let i = 0; i < nodes.length; i++) {
              if (nodes[i].message_text || nodes[i].text) {
                $('#waModalReplyText').val(nodes[i].message_text || nodes[i].text);
                break;
              }
            }
          }
        } else {
          $('#waModalTitle').text('Create WhatsApp Chat Workflow');
          $('#waModalName').val('');
          $('#waModalDesc').val('');
          $('#waModalTriggerType').val('keyword').trigger('change');
          $('#waModalKeywords').val('');
          $('#waModalMatchType').val('contains').trigger('change');
          $('#waModalIsDefault').prop('checked', false);
          $('#waModalReplyText').val("Hello {name}! 👋 Thank you for messaging {company}. How can we assist you with your travel plans today?");
        }
      } catch (err) {
        // Prevent field reset errors from halting modal
      }
    },

    useTemplate: function (templateId) {
      const self = this;
      const tpl = self.templates.find(t => t.id === templateId);
      if (!tpl) return;

      self.openWorkflowModal(0);
      $('#waModalName').val(tpl.name);
      $('#waModalDesc').val(tpl.description);
      $('#waModalTriggerType').val(tpl.trigger_type).trigger('change');
      $('#waModalKeywords').val(tpl.keywords);
      $('#waModalMatchType').val(tpl.match_type).trigger('change');
      $('#waModalIsDefault').prop('checked', !!tpl.is_default);

      if (tpl.nodes && tpl.nodes.length) {
        for (let i = 0; i < tpl.nodes.length; i++) {
          if (tpl.nodes[i].message_text) {
            $('#waModalReplyText').val(tpl.nodes[i].message_text);
            break;
          }
        }
      }
    },

    saveWorkflow: function () {
      const self = this;
      const id = $('#waModalId').val();
      const name = $('#waModalName').val();
      const description = $('#waModalDesc').val();
      const trigger_type = $('#waModalTriggerType').val();
      const keywords = $('#waModalKeywords').val();
      const match_type = $('#waModalMatchType').val();
      const is_default = $('#waModalIsDefault').is(':checked') ? 1 : 0;
      const reply_text = $('#waModalReplyText').val();

      const nodes = [
        {
          id: 'n_trigger',
          type: 'trigger',
          title: is_default ? 'Default Auto-Responder' : 'Keyword Match: ' + keywords,
          category: 'trigger'
        },
        {
          id: 'n_action',
          type: 'action',
          action_type: 'reply_text',
          title: 'Send Automated WhatsApp Reply',
          message_text: reply_text
        }
      ];

      const payload = {
        action: 'save',
        id: id,
        name: name,
        description: description,
        trigger_type: trigger_type,
        keywords: keywords,
        match_type: match_type,
        is_default: is_default,
        nodes: nodes
      };

      $('#btnSaveWaWorkflow').prop('disabled', true).text('Saving...');

      $.ajax({
        url: self.apiUrl,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        dataType: 'json',
        success: function (res) {
          $('#btnSaveWaWorkflow').prop('disabled', false).html('<i class="fi fi-rr-check"></i> Save Workflow');
          if (res && res.status === 'success') {
            $('#waWorkflowModal').fadeOut(150);
            if (typeof window.popUpMessage === 'function') {
              window.popUpMessage('center', res.msg, 'success');
            }
            self.loadData();
          } else {
            alert(res.msg || 'Failed to save workflow.');
          }
        },
        error: function () {
          $('#btnSaveWaWorkflow').prop('disabled', false).html('<i class="fi fi-rr-check"></i> Save Workflow');
          alert('Network or server error while saving workflow.');
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

            // Update simulation trace panel
            if (res.simulated_steps && res.simulated_steps.length) {
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
          } else {
            const fallback = `<div class="wa-chat-bubble bot-reply wa-no-match-bubble">No workflow matched this input.</div>`;
            $('#waChatMessagesBody').append(fallback);
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
