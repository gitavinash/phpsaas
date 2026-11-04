/**
 * V3 WhatsApp Templates & Action Engine Client
 * Path: /app/dashboard/v3/default/desktop/assets/js/whatsapp_templates.js
 * SPA behavior, pure event delegation, no inline JS.
 */

(function ($) {
  "use strict";

  const WhatsAppTemplatesApp = {
    templates: [],
    actionTriggers: [],
    activeActionMap: {},
    currentFilter: "all",
    currentScope: "all",
    searchQuery: "",
    selectedTemplateForSend: null,
    eventsBound: false,

    init: function () {
      if (!$("#whatsappTemplatesApp").length) return;

      if (!this.eventsBound) {
        this.bindEvents();
        this.eventsBound = true;
      }
      this.loadTemplates();
      this.checkUrlParams();
    },

    checkUrlParams: function () {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get("action") === "create") {
        this.openCreateModal();
      }
      if (urlParams.get("tab") === "actions") {
        setTimeout(() => {
          this.openActionDefaultsModal();
        }, 400);
      }
    },

    bindEvents: function () {
      const self = this;

      // 1. Header Buttons
      $(document).on("click", "#btnOpenCreateTemplateModal", function () {
        self.openCreateModal();
      });

      $(document).on("click", "#btnOpenActionDefaultsModal", function () {
        self.openActionDefaultsModal();
      });

      $(document).on("click", "#btnOpenQuickSendModal", function () {
        self.openSendModal();
      });

      $(document).on("click", "#btnRefreshTemplates", function () {
        self.loadTemplates(true);
      });

      // 2. Modals Close Buttons
      $(document).on("click", "#btnCloseTemplateModal, #btnCancelTemplateModal", function () {
        self.closeModal("#waTemplateModal");
      });

      $(document).on("click", "#btnCloseSendModal, #btnCancelSendModal", function () {
        self.closeModal("#waSendModal");
      });

      $(document).on("click", "#btnCloseActionDefaultsModal, #btnCloseActionDefaultsBtn", function () {
        self.closeModal("#waActionDefaultsModal");
      });

      // Close modal on escape
      $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
          $(".wa-modal-overlay:not(.is-hidden)").addClass("is-hidden");
        }
      });

      // 3. Search Bar
      let searchTimer = null;
      $(document).on("input", "#tplSearchInput", function () {
        const val = $(this).val().trim();
        self.searchQuery = val;
        if (val.length > 0) {
          $("#btnTplClearSearch").removeClass("is-hidden");
        } else {
          $("#btnTplClearSearch").addClass("is-hidden");
        }
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
          self.renderTemplates();
        }, 250);
      });

      $(document).on("click", "#btnTplClearSearch", function () {
        $("#tplSearchInput").val("").trigger("input").focus();
      });

      // 4. Action Category Filter Tabs
      $(document).on("click", ".wa-tpl-tab-btn", function () {
        const trigger = $(this).data("action-trigger");
        self.currentFilter = trigger;
        $(".wa-tpl-tab-btn").removeClass("active");
        $(this).addClass("active");
        self.renderTemplates();
      });

      // 5. Segmented Scope Filter (All, Defaults Only, Custom Only)
      $(document).on("click", ".wa-seg-btn", function () {
        const scope = $(this).data("scope");
        self.currentScope = scope;
        $(".wa-seg-btn").removeClass("active");
        $(this).addClass("active");
        self.renderTemplates();
      });

      // 6. Action Matrix Chip Click (filters to that action)
      $(document).on("click", ".wa-matrix-action-chip", function () {
        const act = $(this).data("action-trigger");
        if (act) {
          self.currentFilter = act;
          $(".wa-tpl-tab-btn").removeClass("active");
          $(`.wa-tpl-tab-btn[data-action-trigger="${act}"]`).addClass("active");
          self.renderTemplates();
        }
      });

      // 7. Form Variable Inserter Pill click
      $(document).on("click", ".wa-var-pill", function (e) {
        e.preventDefault();
        const tag = $(this).data("var");
        self.insertTextAtCursor(tag);
      });

      // 7b. Message Formatting Buttons (Bold, Italic, Strike, Code)
      $(document).on("click", ".wa-fmt-btn", function (e) {
        e.preventDefault();
        const fmt = $(this).data("fmt");
        self.applyFormatting(fmt);
      });

      // 7c. Quick Emoji Buttons
      $(document).on("click", ".wa-emoji-btn", function (e) {
        e.preventDefault();
        const emoji = $(this).data("emoji");
        self.insertTextAtCursor(emoji);
      });

      // 8. Textarea Input live preview update
      $(document).on("input", "#formTemplateContent", function () {
        self.updateLivePreview();
      });

      // 9. Action Trigger select change in modal
      $(document).on("change", "#formActionTrigger", function () {
        const val = $(this).val();
        self.updateActionHint(val);
      });

      // 10. Form Save Submit
      $(document).on("submit", "#waTemplateForm", function (e) {
        e.preventDefault();
        self.saveTemplate();
      });

      // 11. Card Action: Set as Action Default
      $(document).on("click", ".btn-set-default", function (e) {
        e.preventDefault();
        const tId = $(this).data("template-id");
        self.setAsDefault(tId);
      });

      // 12. Card Action: Edit Template
      $(document).on("click", ".btn-edit-tpl", function (e) {
        e.preventDefault();
        const tId = $(this).data("template-id");
        self.openEditModal(tId);
      });

      // 13. Card Action: Duplicate / Clone Template
      $(document).on("click", ".btn-duplicate-tpl", function (e) {
        e.preventDefault();
        const tId = $(this).data("template-id");
        self.duplicateTemplate(tId);
      });

      // 14. Card Action: Delete Template
      $(document).on("click", ".btn-delete-tpl", function (e) {
        e.preventDefault();
        const tId = $(this).data("template-id");
        self.deleteTemplate(tId);
      });

      // 15. Card Action: Test / Send Template
      $(document).on("click", ".btn-test-send", function (e) {
        e.preventDefault();
        const tId = $(this).data("template-id");
        self.openSendModal(tId);
      });

      // 16. Card Action: Copy template text
      $(document).on("click", ".btn-copy-tpl", function (e) {
        e.preventDefault();
        const tId = $(this).data("template-id");
        self.copyTemplateContent(tId, $(this));
      });

      // 17. Send Form: Template Selector change
      $(document).on("change", "#sendTemplateSelector", function () {
        const tId = parseInt($(this).val(), 10) || 0;
        self.updateSendPreview(tId);
      });

      // 18. Send Form: Search Lead for Auto-fill & Preview
      let leadSearchTimer = null;
      $(document).on("input", "#sendLeadSearchInput", function () {
        const q = $(this).val().trim();
        clearTimeout(leadSearchTimer);
        // If user edits text manually, clear previously locked lead selection
        if (self.selectedLeadId && !q.startsWith(`#${self.selectedLeadId}`)) {
          self.selectedLeadId = 0;
        }
        leadSearchTimer = setTimeout(function () {
          self.searchLeads(q);
        }, 250);
      });

      $(document).on("focus click", "#sendLeadSearchInput", function () {
        const q = $(this).val().trim();
        if ($("#waLeadSearchResults").hasClass("is-hidden")) {
          if (q.startsWith("#") && self.selectedLeadId) {
            self.searchLeads("");
          } else {
            self.searchLeads(q);
          }
        }
      });

      // 19. Send Form: Select Lead Item from dropdown
      $(document).on("click", ".wa-lead-search-item", function () {
        const phone = $(this).data("phone") || "";
        const name = $(this).data("name") || "";
        const leadId = $(this).data("lead-id") || "";

        self.selectedLeadId = leadId;
        $("#sendRecipientPhone").val(phone);
        const displayLabel = phone ? `#${leadId} - ${name} (${phone})` : `#${leadId} - ${name}`;
        $("#sendLeadSearchInput").val(displayLabel);
        $("#waLeadSearchResults").addClass("is-hidden").empty();

        const curTplId = parseInt($("#sendTemplateSelector").val(), 10) || 0;
        self.updateSendPreview(curTplId, leadId);
      });

      // Close lead search dropdown on click outside
      $(document).on("click", function (e) {
        if (!$(e.target).closest("#sendLeadSearchInput, #waLeadSearchResults").length) {
          $("#waLeadSearchResults").addClass("is-hidden");
        }
      });

      // 20. Send Form Submit
      $(document).on("submit", "#waSendForm", function (e) {
        e.preventDefault();
        self.dispatchSendTemplate();
      });

      // 21. Action Defaults Matrix Table: Change Default Selector
      $(document).on("change", ".wa-matrix-select-default", function () {
        const tId = parseInt($(this).val(), 10) || 0;
        if (tId > 0) {
          self.setAsDefault(tId, true);
        }
      });
    },

    loadTemplates: function (showToast) {
      const self = this;
      $("#waTemplatesGrid").html(`
        <div class="wa-tpl-loading-state">
            <i class="fi fi-rr-spinner wa-spin"></i>
            <span>Loading WhatsApp templates...</span>
        </div>
      `);

      $.ajax({
        url: "/api/v3/whatsapp/templates/?action=list",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res.status === "success") {
            self.templates = res.templates || [];
            self.actionTriggers = res.action_triggers || [];
            self.activeActionMap = res.active_action_map || {};

            self.renderKPIs(res.stats);
            self.renderActionMatrixStrip();
            self.renderTemplates();
            self.populateSendTemplateSelect();

            if (showToast && typeof swal === "function") {
              swal({
                title: "Updated",
                text: "WhatsApp templates refreshed successfully.",
                icon: "success",
                timer: 1500,
                buttons: false
              });
            }
          }
        },
        error: function () {
          $("#waTemplatesGrid").html(`
            <div class="wa-tpl-empty-state">
                <div class="wa-tpl-empty-icon"><i class="fi fi-rr-cross-circle"></i></div>
                <h3>Unable to Load Templates</h3>
                <p>Could not retrieve templates from the server. Please check your connection and try again.</p>
                <button type="button" class="btn-wa-primary" id="btnRetryLoad">Retry</button>
            </div>
          `);
          $(document).one("click", "#btnRetryLoad", function () {
            self.loadTemplates();
          });
        }
      });
    },

    renderKPIs: function (stats) {
      if (!stats) return;
      $("#kpiTotalTemplates").text(stats.total_templates || this.templates.length);
      $("#kpiActiveActions").text((stats.active_actions_count || Object.keys(this.activeActionMap).length) + " / " + this.actionTriggers.length);
      $("#kpiCustomTemplates").text(stats.custom_templates_count || 0);
      $("#kpiTotalDispatches").text(stats.total_usage_count || 0);
    },

    renderActionMatrixStrip: function () {
      const self = this;
      const $container = $("#waActionMatrixPills");
      $container.empty();

      this.actionTriggers.forEach(function (act) {
        const activeTplId = self.activeActionMap[act.key];
        const activeTpl = self.templates.find(t => parseInt(t.template_id, 10) === activeTplId);
        const tplTitle = activeTpl ? activeTpl.template_name : "Default";

        const $chip = $(`
          <div class="wa-matrix-action-chip" data-action-trigger="${act.key}" title="Active: ${tplTitle}">
              <span class="wa-matrix-chip-icon" style="background:${act.bg};color:${act.color};">
                  <i class="${act.icon}"></i>
              </span>
              <span class="wa-matrix-chip-name">${act.badge}:</span>
              <span class="wa-matrix-chip-template">${tplTitle}</span>
              <span class="wa-matrix-chip-badge">Active</span>
          </div>
        `);
        $container.append($chip);
      });
    },

    renderTemplates: function () {
      const self = this;
      const $grid = $("#waTemplatesGrid");
      $grid.empty();

      const filtered = this.templates.filter(function (t) {
        // Category filter
        if (self.currentFilter !== "all" && t.template_action_trigger !== self.currentFilter) {
          return false;
        }
        // Scope filter
        if (self.currentScope === "defaults_only" && parseInt(t.template_is_action_active, 10) !== 1) {
          return false;
        }
        if (self.currentScope === "custom_only" && parseInt(t.template_is_system_default, 10) === 1) {
          return false;
        }
        // Search query
        if (self.searchQuery) {
          const q = self.searchQuery.toLowerCase();
          const matchName = (t.template_name || "").toLowerCase().includes(q);
          const matchContent = (t.template_content || "").toLowerCase().includes(q);
          const matchCat = (t.template_category || "").toLowerCase().includes(q);
          if (!matchName && !matchContent && !matchCat) {
            return false;
          }
        }
        return true;
      });

      if (filtered.length === 0) {
        $grid.html(`
          <div class="wa-tpl-empty-state">
              <div class="wa-tpl-empty-icon"><i class="fi fi-rr-search-alt"></i></div>
              <h3>No Templates Found</h3>
              <p>No WhatsApp templates match your current filter or search criteria.</p>
              <button type="button" class="btn-wa-primary" id="btnEmptyCreate">
                  <i class="fi fi-rr-plus"></i> Create New Template
              </button>
          </div>
        `);
        $(document).one("click", "#btnEmptyCreate", function () {
          self.openCreateModal();
        });
        return;
      }

      filtered.forEach(function (t) {
        const isActionActive = parseInt(t.template_is_action_active, 10) === 1;
        const isSystemDefault = parseInt(t.template_is_system_default, 10) === 1;
        const actMeta = t.action_meta || {};

        // Highlight placeholder tokens with pretty badges
        const formattedBubbleHtml = self.formatTemplateBubbleHtml(t.template_content || "");

        const $card = $(`
          <div class="wa-template-card ${isActionActive ? "is-active-action-default" : ""}" data-template-id="${t.template_id}">
              <div class="wa-tpl-card-top">
                  <div class="wa-tpl-badges-row">
                      <span class="wa-action-badge" style="background:${actMeta.bg || "#eff6ff"};color:${actMeta.color || "#2563eb"};">
                          <i class="${actMeta.icon || "fi fi-rr-bolt"}"></i> ${actMeta.badge || t.template_action_trigger}
                      </span>
                      ${isActionActive ? `
                        <span class="wa-status-badge-default" title="This template is actively sent when this CRM action triggers">
                            <i class="fi fi-rr-check"></i> Active Default
                        </span>
                      ` : ""}
                      ${isSystemDefault ? `
                        <span class="wa-type-badge-system">System Default</span>
                      ` : `
                        <span class="wa-type-badge-custom">Custom</span>
                      `}
                  </div>
              </div>

              <h4 class="wa-tpl-card-title">${self.escapeHtml(t.template_name)}</h4>

              <!-- WhatsApp Chat Bubble Preview -->
              <div class="wa-tpl-bubble-preview">
                  <div class="wa-tpl-bubble-content">${formattedBubbleHtml}</div>
                  <div class="wa-tpl-bubble-meta">
                      <span>12:45 PM</span>
                      <i class="fi fi-rr-check-double"></i>
                  </div>
              </div>

              <!-- Footer Actions -->
              <div class="wa-tpl-card-footer">
                  <div class="wa-tpl-footer-left">
                      <button type="button" class="btn-card-action btn-test-send" data-template-id="${t.template_id}" title="Send this template to a recipient or lead">
                          <i class="fi fi-rr-paper-plane"></i> Test Send
                      </button>
                      ${!isActionActive ? `
                        <button type="button" class="btn-card-action btn-set-default" data-template-id="${t.template_id}" title="Make this template the active default for ${actMeta.name || "this action"}">
                            <i class="fi fi-rr-check-circle"></i> Set Default
                        </button>
                      ` : ""}
                  </div>
                  <div class="wa-tpl-footer-right">
                      <button type="button" class="btn-card-icon-only btn-copy-tpl" data-template-id="${t.template_id}" title="Copy template message">
                          <i class="fi fi-rr-copy"></i>
                      </button>
                      <button type="button" class="btn-card-icon-only btn-duplicate-tpl" data-template-id="${t.template_id}" title="Duplicate / Customize this template">
                          <i class="fi fi-rr-duplicate"></i>
                      </button>
                      <button type="button" class="btn-card-icon-only btn-edit-tpl" data-template-id="${t.template_id}" title="Edit Template">
                          <i class="fi fi-rr-pencil"></i>
                      </button>
                      ${!isSystemDefault ? `
                        <button type="button" class="btn-card-icon-only btn-delete-tpl" data-template-id="${t.template_id}" title="Delete Custom Template">
                            <i class="fi fi-rr-trash"></i>
                        </button>
                      ` : ""}
                  </div>
              </div>
          </div>
        `);

        $grid.append($card);
      });
    },

    formatTemplateBubbleHtml: function (content) {
      if (!content) return "";
      let safe = this.escapeHtml(content);

      // Replace {tokens} with token pills
      safe = safe.replace(/(\{[a-zA-Z0-9_-]+\})/g, '<span class="wa-tpl-token">$1</span>');

      // WhatsApp format bold *text*
      safe = safe.replace(/\*([^*\n]+)\*/g, "<strong>$1</strong>");

      // WhatsApp format italic _text_
      safe = safe.replace(/_([^_]+)_/g, "<em>$1</em>");

      return safe;
    },

    openCreateModal: function (presetAction) {
      $("#waTemplateModalHeading").text("Create WhatsApp Template");
      $("#formTemplateId").val("0");
      $("#formTemplateName").val("");
      $("#formTemplateContent").val("");
      $("#formIsActionActive").prop("checked", true);

      if (presetAction) {
        $("#formActionTrigger").val(presetAction);
      } else if (this.currentFilter !== "all") {
        $("#formActionTrigger").val(this.currentFilter);
      } else {
        $("#formActionTrigger").val("lead_welcome");
      }

      this.updateActionHint($("#formActionTrigger").val());
      this.updateLivePreview();
      this.openModal("#waTemplateModal");
      $("#formTemplateName").focus();
    },

    openEditModal: function (templateId) {
      const t = this.templates.find(item => parseInt(item.template_id, 10) === parseInt(templateId, 10));
      if (!t) return;

      $("#waTemplateModalHeading").text(parseInt(t.template_is_system_default, 10) === 1 ? "Customize Template: " + t.template_name : "Edit WhatsApp Template");
      $("#formTemplateId").val(t.template_id);
      $("#formTemplateName").val(t.template_name);
      $("#formActionTrigger").val(t.template_action_trigger);
      $("#formTemplateContent").val(t.template_content);
      $("#formIsActionActive").prop("checked", parseInt(t.template_is_action_active, 10) === 1);

      this.updateActionHint(t.template_action_trigger);
      this.updateLivePreview();
      this.openModal("#waTemplateModal");
    },

    duplicateTemplate: function (templateId) {
      const t = this.templates.find(item => parseInt(item.template_id, 10) === parseInt(templateId, 10));
      if (!t) return;

      $("#waTemplateModalHeading").text("Create Customized Copy of Template");
      $("#formTemplateId").val("0");
      $("#formTemplateName").val(t.template_name + " (Custom)");
      $("#formActionTrigger").val(t.template_action_trigger);
      $("#formTemplateContent").val(t.template_content);
      $("#formIsActionActive").prop("checked", true);

      this.updateActionHint(t.template_action_trigger);
      this.updateLivePreview();
      this.openModal("#waTemplateModal");
      $("#formTemplateName").focus();
    },

    insertTextAtCursor: function (textToInsert) {
      const textarea = document.getElementById("formTemplateContent");
      if (!textarea) return;
      const start = textarea.selectionStart;
      const end = textarea.selectionEnd;
      const val = textarea.value;

      textarea.value = val.substring(0, start) + textToInsert + val.substring(end);
      textarea.selectionStart = textarea.selectionEnd = start + textToInsert.length;
      textarea.focus();
      this.updateLivePreview();
    },

    applyFormatting: function (type) {
      const textarea = document.getElementById("formTemplateContent");
      if (!textarea) return;
      const start = textarea.selectionStart;
      const end = textarea.selectionEnd;
      const val = textarea.value;
      const selected = val.substring(start, end) || "text";

      let wrapped = "";
      if (type === "bold") wrapped = "*" + selected + "*";
      else if (type === "italic") wrapped = "_" + selected + "_";
      else if (type === "strike") wrapped = "~" + selected + "~";
      else if (type === "code") wrapped = "```" + selected + "```";

      textarea.value = val.substring(0, start) + wrapped + val.substring(end);
      textarea.selectionStart = start;
      textarea.selectionEnd = start + wrapped.length;
      textarea.focus();
      this.updateLivePreview();
    },

    updateActionHint: function (actionTrigger) {
      const act = this.actionTriggers.find(a => a.key === actionTrigger);
      if (act) {
        $("#formActionHint").text(act.desc || "");
        $("#waActionInfoTitle").text(act.name || act.badge || "CRM Action");
        $("#waActionInfoDesc").text(act.desc || "");
        if (act.icon) {
          $("#waActionInfoIcon").html('<i class="' + act.icon + '"></i>');
        }
        if (act.color && act.bg) {
          $("#waActionInfoIcon").css({ "color": act.color, "background": act.bg });
        }
      }
    },

    updateLivePreview: function () {
      const val = $("#formTemplateContent").val() || "";
      const charCount = val.length;
      const segments = Math.max(1, Math.ceil(charCount / 1000));
      $("#formCharCounter").text(charCount + " chars (" + segments + " segment" + (segments > 1 ? "s" : "") + ")");

      if (!val.trim()) {
        $("#waLivePreviewBubbleText").html('<span style="color:#94a3b8; font-style:italic;">Type template copy to preview live message...</span>');
        return;
      }

      // Contextual sample replacement values
      const sampleReplaced = val
        .replace(/\{customer_name\}|\{name\}/g, "Rahul Sharma")
        .replace(/\{company_name\}|\{company\}/g, "Musafir India Trip")
        .replace(/\{destination\}/g, "Kashmir Valley")
        .replace(/\{agent_name\}/g, "Pooja Verma")
        .replace(/\{agent_mobile\}/g, "+91 98765 43210")
        .replace(/\{lead_id\}/g, "1042")
        .replace(/\{itinerary_link\}/g, "https://musafir.in/itinerary/kashmir-7d")
        .replace(/\{payment_link\}/g, "https://musafir.in/pay/inv-1042")
        .replace(/\{review_link\}/g, "https://g.page/r/review")
        .replace(/\{dates\}/g, "15 - 22 Nov");

      let html = this.escapeHtml(sampleReplaced);

      // Monospace ```code```
      html = html.replace(/```([^`]+)```/g, "<code>$1</code>");
      // Bold *bold*
      html = html.replace(/\*([^*\n]+)\*/g, "<strong>$1</strong>");
      // Italic _italic_
      html = html.replace(/_([^_]+)_/g, "<em>$1</em>");
      // Strikethrough ~strike~
      html = html.replace(/~([^~\n]+)~/g, "<del>$1</del>");
      // Links
      html = html.replace(/(https?:\/\/[^\s<]+)/g, '<a href="$1" target="_blank" class="wa-chat-link">$1</a>');
      // Line breaks
      html = html.replace(/\n/g, "<br>");

      $("#waLivePreviewBubbleText").html(html);
    },

    saveTemplate: function () {
      const self = this;
      const tId = parseInt($("#formTemplateId").val(), 10) || 0;
      const name = $("#formTemplateName").val().trim();
      const actionTrigger = $("#formActionTrigger").val();
      const content = $("#formTemplateContent").val().trim();
      const isActionActive = $("#formIsActionActive").is(":checked") ? 1 : 0;

      if (!name) {
        if (typeof swal === "function") swal("Required", "Please enter a template name.", "warning");
        return;
      }
      if (!content) {
        if (typeof swal === "function") swal("Required", "Please enter WhatsApp message copy.", "warning");
        return;
      }

      const $btn = $("#btnSaveTemplateSubmit");
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner wa-spin"></i> Saving...');

      $.ajax({
        url: "/api/v3/whatsapp/templates/?action=save",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({
          template_id: tId,
          template_name: name,
          template_action_trigger: actionTrigger,
          template_content: content,
          template_is_action_active: isActionActive
        }),
        dataType: "json",
        success: function (res) {
          $btn.prop("disabled", false).html('<i class="fi fi-rr-disk"></i> <span>Save Template</span>');
          if (res.status === "success") {
            self.closeModal("#waTemplateModal");
            if (typeof swal === "function") {
              swal("Success", res.msg || "Template saved successfully!", "success");
            }
            self.loadTemplates();
          } else {
            if (typeof swal === "function") swal("Error", res.msg || "Failed to save template.", "error");
          }
        },
        error: function (xhr) {
          $btn.prop("disabled", false).html('<i class="fi fi-rr-disk"></i> <span>Save Template</span>');
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error saving template";
          if (typeof swal === "function") swal("Error", err, "error");
        }
      });
    },

    setAsDefault: function (templateId, fromMatrix) {
      const self = this;
      $.ajax({
        url: "/api/v3/whatsapp/templates/?action=set_default",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ template_id: templateId }),
        dataType: "json",
        success: function (res) {
          if (res.status === "success") {
            if (typeof swal === "function") {
              swal({
                title: "Active Action Default Changed!",
                text: res.msg,
                icon: "success",
                timer: 2000,
                buttons: false
              });
            }
            self.loadTemplates();
            if (fromMatrix) {
              setTimeout(() => {
                self.renderActionDefaultsMatrix();
              }, 400);
            }
          }
        },
        error: function () {
          if (typeof swal === "function") swal("Error", "Could not change action default.", "error");
        }
      });
    },

    deleteTemplate: function (templateId) {
      const self = this;
      const t = this.templates.find(item => parseInt(item.template_id, 10) === parseInt(templateId, 10));
      const tName = t ? t.template_name : "this template";

      const doDelete = function () {
        $.ajax({
          url: "/api/v3/whatsapp/templates/?action=delete",
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify({ template_id: templateId }),
          dataType: "json",
          success: function (res) {
            if (res.status === "success") {
              if (typeof swal === "function") swal("Deleted", res.msg, "success");
              self.loadTemplates();
            }
          }
        });
      };

      if (typeof swal === "function") {
        swal({
          title: "Delete Template?",
          text: `Are you sure you want to delete '${tName}'? If it is currently active, the system default will be restored.`,
          icon: "warning",
          buttons: ["Cancel", "Yes, Delete"],
          dangerMode: true
        }).then(function (willDelete) {
          if (willDelete) doDelete();
        });
      } else {
        if (confirm(`Delete '${tName}'?`)) doDelete();
      }
    },

    copyTemplateContent: function (templateId, $btn) {
      const t = this.templates.find(item => parseInt(item.template_id, 10) === parseInt(templateId, 10));
      if (!t || !t.template_content) return;

      navigator.clipboard.writeText(t.template_content).then(function () {
        const origHtml = $btn.html();
        $btn.html('<i class="fi fi-rr-check" style="color:#16a34a;"></i>');
        setTimeout(() => {
          $btn.html(origHtml);
        }, 1500);
      });
    },

    populateSendTemplateSelect: function () {
      const $sel = $("#sendTemplateSelector");
      $sel.empty();

      this.templates.forEach(function (t) {
        const isDef = parseInt(t.template_is_action_active, 10) === 1 ? " ★ (Default)" : "";
        $sel.append(`<option value="${t.template_id}">${t.template_name}${isDef}</option>`);
      });
    },

    openSendModal: function (preselectedTplId) {
      this.selectedLeadId = 0;
      $("#sendLeadSearchInput").val("");
      $("#sendRecipientPhone").val("");
      $("#waLeadSearchResults").addClass("is-hidden").empty();
      this.populateSendTemplateSelect();
      if (preselectedTplId) {
        $("#sendTemplateSelector").val(preselectedTplId);
      }
      const curTplId = parseInt($("#sendTemplateSelector").val(), 10) || 0;
      this.updateSendPreview(curTplId, 0);
      this.openModal("#waSendModal");
    },

    updateSendPreview: function (templateId, leadId) {
      if (!templateId) {
        $("#sendRenderedPreview").text("No template selected");
        return;
      }
      $.ajax({
        url: `/api/v3/whatsapp/templates/?action=preview&template_id=${templateId}&lead_id=${leadId || 0}`,
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res.status === "success") {
            $("#sendRenderedPreview").text(res.preview_text);
          }
        }
      });
    },

    searchLeads: function (q) {
      const self = this;
      $.ajax({
        url: `/api/v3/whatsapp/templates/?action=leads_search&q=${encodeURIComponent(q || "")}`,
        method: "GET",
        dataType: "json",
        success: function (res) {
          const $dropdown = $("#waLeadSearchResults");
          $dropdown.empty();
          if (res.status === "success" && res.leads && res.leads.length > 0) {
            res.leads.forEach(function (l) {
              const leadPhone = l.phone ? self.escapeHtml(l.phone) : "No Mobile";
              const leadName = self.escapeHtml(l.name || "Lead #" + l.id);
              const leadDest = self.escapeHtml(l.destination || "General Inquiry");
              const hasPhone = Boolean(l.phone && l.phone.trim().length > 0);

              $dropdown.append(`
                <div class="wa-lead-search-item" data-phone="${self.escapeHtml(l.phone || '')}" data-name="${leadName}" data-lead-id="${l.id}">
                    <div class="wa-lead-item-left">
                        <div class="wa-lead-item-header">
                            <span class="wa-lead-id-badge"><i class="fi fi-rr-hashtag"></i> ${l.id}</span>
                            <span class="wa-lead-item-name">${leadName}</span>
                        </div>
                        <div class="wa-lead-item-meta">
                            <i class="fi fi-rr-marker"></i>
                            <span>${leadDest}</span>
                        </div>
                    </div>
                    <div class="wa-lead-item-right">
                        <span class="wa-lead-item-phone ${hasPhone ? '' : 'wa-no-phone'}">
                            <i class="fi fi-rr-phone-call"></i> ${leadPhone}
                        </span>
                    </div>
                </div>
              `);
            });
            $dropdown.removeClass("is-hidden");
          } else {
            $dropdown.html(`
              <div class="wa-lead-search-empty">
                  <i class="fi fi-rr-info"></i>
                  <span>No matching CRM leads found</span>
              </div>
            `).removeClass("is-hidden");
          }
        }
      });
    },

    dispatchSendTemplate: function () {
      const self = this;
      const tId = parseInt($("#sendTemplateSelector").val(), 10) || 0;
      const phone = $("#sendRecipientPhone").val().trim();
      const $btn = $("#btnSendTemplateSubmit");

      if (!phone) {
        if (typeof swal === "function") swal("Required", "Please enter a recipient phone number.", "warning");
        return;
      }

      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner wa-spin"></i> Sending message...');

      $.ajax({
        url: "/api/v3/whatsapp/templates/?action=send",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({
          template_id: tId,
          phone: phone,
          lead_id: self.selectedLeadId || 0
        }),
        dataType: "json",
        success: function (res) {
          $btn.prop("disabled", false).html('<i class="fi fi-rr-paper-plane"></i> <span>Send via WhatsApp</span>');
          if (res.status === "success") {
            $("#waSendModal").addClass("is-hidden");
            if (typeof swal === "function") {
              swal("Dispatched!", res.msg || "WhatsApp message sent successfully!", "success");
            }
          } else {
            if (typeof swal === "function") swal("Error", res.msg || "Failed to send template message.", "error");
          }
        },
        error: function (xhr) {
          $btn.prop("disabled", false).html('<i class="fi fi-rr-paper-plane"></i> <span>Send via WhatsApp</span>');
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Failed to send WhatsApp message. Verify connected device.";
          if (typeof swal === "function") swal("Dispatch Failed", err, "error");
        }
      });
    },

    openActionDefaultsModal: function () {
      this.renderActionDefaultsMatrix();
      this.openModal("#waActionDefaultsModal");
    },

    renderActionDefaultsMatrix: function () {
      const self = this;
      const $tbody = $("#waMatrixTableBody");
      $tbody.empty();

      this.actionTriggers.forEach(function (act) {
        const activeTplId = self.activeActionMap[act.key] || 0;
        const matchingTpls = self.templates.filter(t => t.template_action_trigger === act.key);
        const activeTpl = matchingTpls.find(t => parseInt(t.template_id, 10) === activeTplId) || matchingTpls[0];
        const isCustom = activeTpl ? parseInt(activeTpl.template_is_system_default, 10) === 0 : false;

        let optionsHtml = "";
        matchingTpls.forEach(function (t) {
          const selected = parseInt(t.template_id, 10) === activeTplId ? "selected" : "";
          const defMark = parseInt(t.template_is_system_default, 10) === 1 ? " [System Default]" : " [Custom]";
          optionsHtml += `<option value="${t.template_id}" ${selected}>${t.template_name}${defMark}</option>`;
        });

        const $tr = $(`
          <tr>
              <td>
                  <span class="wa-action-badge" style="background:${act.bg};color:${act.color};">
                      <i class="${act.icon}"></i> ${act.badge}
                  </span>
              </td>
              <td>
                  <span style="font-size:12px;color:#64748b;">${act.desc}</span>
              </td>
              <td>
                  <strong>${activeTpl ? activeTpl.template_name : "None selected"}</strong>
              </td>
              <td>
                  ${isCustom ? `
                    <span class="wa-type-badge-custom">Custom Action Template</span>
                  ` : `
                    <span class="wa-type-badge-system">Built-in Default</span>
                  `}
              </td>
              <td class="text-right">
                  <select class="wa-matrix-select-default" data-action="${act.key}">
                      ${optionsHtml}
                  </select>
              </td>
          </tr>
        `);
        $tbody.append($tr);
      });
    },

    openModal: function (modalSelector) {
      $(modalSelector).removeClass("is-hidden").addClass("is-active is-open");
    },

    closeModal: function (modalSelector) {
      $(modalSelector).addClass("is-hidden").removeClass("is-active is-open");
    },

    escapeHtml: function (string) {
      const entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;"
      };
      return String(string).replace(/[&<>"']/g, function (s) {
        return entityMap[s];
      });
    }
  };
 
  window.WhatsAppTemplatesApp = WhatsAppTemplatesApp;

  $(document).ready(function () {
    WhatsAppTemplatesApp.init();
  });

  $(document).on("spa:loaded", function () {
    if ($("#whatsappTemplatesApp").length) {
      WhatsAppTemplatesApp.init();
    }
  });

  $(window).on("popstate", function () {
    setTimeout(function () {
      if ($("#whatsappTemplatesApp").length) {
        WhatsAppTemplatesApp.init();
      }
    }, 50);
  });

})(jQuery);
