/**
 * Operations Management Hub JavaScript Controller
 * File: app/dashboard/v3/default/desktop/assets/js/operations.js
 * SPA functionality for Confirmed Leads, Trip Planner, Booking Requests, Vouchers, Invoices, and Templates
 */

(function (window, document, $) {
  "use strict";

  const OperationsHub = {
    eventsBound: false,
    currentTab: "confirmed",
    confirmedPage: 1,
    confirmedLimit: 20,
    confirmedStatus: "",
    confirmedSearch: "",
    requestsPage: 1,
    requestsLimit: 25,
    requestsVendor: "all",
    requestsStatus: "",
    requestsSearch: "",
    tripDate: new Date().toISOString().split("T")[0],
    unassignedSearch: "",
    selectedLeadIds: [],
    cachedAgents: [],
    cachedProperties: [],
    cachedTransporters: [],
    cachedTemplates: [],
    currentLeadBookingData: null,

    init: function () {
      if (!$("#operationsApp").length) return;

      this.syncAsideNav();
      this.bindEvents();

      // Reset filters to clean default state on navigation
      this.confirmedPage = 1;
      this.requestsPage = 1;
      this.confirmedSearch = "";
      this.requestsSearch = "";
      this.confirmedStatus = "";
      this.requestsStatus = "";
      this.selectedLeadIds = [];

      const page = $("#operationsApp").data("page") || "all";
      const leadId = $("#operationsApp").data("lead-id") || new URLSearchParams(window.location.search).get("lead_id");
      const templateId = $("#operationsApp").data("template-id") || new URLSearchParams(window.location.search).get("id");

      if (page === "list" || page === "confirmed") {
        this.loadMetrics();
        this.loadAgents();
        this.loadConfirmedLeads();
      } else if (page === "trips") {
        this.loadMetrics();
        this.loadTripsBoard();
      } else if (page === "requests") {
        this.loadMetrics();
        this.loadBookingRequests();
      } else if (page === "unassigned") {
        this.loadMetrics();
        this.loadAgents();
        this.loadUnassignedLeads();
      } else if (page === "templates") {
        this.loadTemplates();
      } else if (page === "booking-request") {
        this.loadAgents();
        if (leadId) {
          this.openBookingRequestModal(leadId);
        } else {
          this.loadDefaultBookingDependencies();
        }
      } else if (page === "voucher") {
        if (leadId) {
          this.openVoucherModal(leadId);
        }
      } else if (page === "invoice") {
        if (leadId) {
          this.openInvoiceModal(leadId);
        }
      } else if (page === "dispatch") {
        if (leadId) {
          $("#dispatchLeadId").val(leadId);
          $("#dispatchLeadIdManualInput").val(leadId);
        }
      } else if (page === "assign") {
        this.loadAgents();
      } else if (page === "template-add") {
        if (templateId && parseInt(templateId, 10) > 0) {
          this.loadSingleTemplate(parseInt(templateId, 10));
        } else {
          this.tplEditorViewMode = "rich";
          this.loadBodyIntoRichEditor($("#tplEditorBody").val(), $("#tplEditorChannel").val() || "whatsapp");
          this.syncTemplateEditorUI();
          this.updateTemplateLivePreview();
        }
      } else if (page === "hub") {
        this.loadMetrics();
      } else if (page === "dashboard") {
        this.loadMetrics();
        this.loadAgents();
        this.loadTripsBoard();
        this.loadConfirmedLeads();
        this.loadBookingRequests();
      } else {
        this.handleHashRouting();
        this.loadMetrics();
        this.loadAgents();
        this.loadConfirmedLeads();
        this.handleDirectQueryActions();
      }
    },

    syncAsideNav: function () {
      try {
        const path = window.location.pathname;
        const $opsItem = $("nav.side-nav li.ops-aside-item, nav.side-nav li[data-aside-nav='operations']");
        if (!$opsItem.length) return;

        // If not on an operations page (e.g. on /dashboard/), ensure Operations Hub is NOT active!
        if (!path.includes("/operations")) {
          $opsItem.removeClass("active");
          $opsItem.find(".side-menu li, .bottom-menu li").removeClass("active");
          $opsItem.find(".side-menu a, .bottom-menu a").removeClass("active");
          return;
        }

        $opsItem.addClass("active");
        $opsItem.find(".side-menu li, .bottom-menu li").removeClass("active");
        $opsItem.find(".side-menu a, .bottom-menu a").removeClass("active");

        let matched = false;
        $opsItem.find(".side-menu a, .bottom-menu a").each(function () {
          const href = $(this).attr("href");
          if (href && (path === href || path.indexOf(href) === 0 || (href.length > 15 && path.includes(href)))) {
            $(this).addClass("active");
            $(this).closest("li").addClass("active");
            matched = true;
          }
        });

        // If no specific child matched and on list or main ops
        if (!matched && (path.includes("/operations/list") || path.endsWith("/operations/") || path.endsWith("/operations"))) {
          const $firstLink = $opsItem.find(".bottom-menu li:first-child a, .side-menu li:first-child a");
          $firstLink.addClass("active").closest("li").addClass("active");
        }
      } catch (e) {}
    },

    closeAllModals: function () {
      $(".v3-modal-overlay").removeClass("active is-active");
    },

    setBtnLoading: function ($btn) {
      if (!$btn || !$btn.length) return;
      if (!$btn.data("orig-html")) {
        $btn.data("orig-html", $btn.html());
      }
      $btn.addClass("btn-loading").prop("disabled", true);
      $btn.html('<i class="fi fi-rr-spinner fi-spin"></i> <span class="btn-action-text">Loading...</span>');
    },

    resetBtnLoading: function ($btn) {
      if (!$btn || !$btn.length) return;
      const orig = $btn.data("orig-html");
      if (orig) {
        $btn.html(orig);
        $btn.removeData("orig-html");
      }
      $btn.removeClass("btn-loading").prop("disabled", false);
    },

    formatEmailDoc: function (cmd, val) {
      document.execCommand(cmd, false, val || null);
      $("#bkgEmailContent").val($("#bkgEmailVisualEditor").html());
      const editor = document.getElementById("bkgEmailVisualEditor");
      if (editor) editor.focus();
    },

    handleDirectQueryActions: function () {
      const self = this;
      const urlParams = new URLSearchParams(window.location.search);
      const action = urlParams.get("action");
      const leadId = urlParams.get("lead_id");
      const tab = urlParams.get("tab");
      if (tab && ["confirmed", "trips", "requests", "unassigned", "templates"].includes(tab)) {
        this.switchTab(tab);
      }
      if (leadId && action) {
        setTimeout(function () {
          if (action === "booking") self.openBookingRequestModal(leadId);
          else if (action === "voucher") self.openVoucherModal(leadId);
          else if (action === "invoice") self.openInvoiceModal(leadId);
          else if (action === "feedback") self.triggerFeedbackRequest(leadId);
        }, 500);
      }
    },

    bindEvents: function () {
      if (this.eventsBound) return;
      this.eventsBound = true;

      const self = this;

      // 1. Navigation Tab Switching
      $(document).on("click", "#opsNavTabs .btn-date-preset", function () {
        const tab = $(this).data("tab");
        self.switchTab(tab);
      });

      // 2. Hash Change Handler
      $(window).on("hashchange", function () {
        self.handleHashRouting();
      });

      // 3. Refresh Button
      $(document).on("click", "#btnRefreshOps", function () {
        const page = $("#operationsApp").data("page") || "all";
        self.loadMetrics();
        if (page === "dashboard") {
          self.loadTripsBoard();
          self.loadConfirmedLeads();
          self.loadBookingRequests();
        } else if (self.currentTab === "confirmed" || page === "list") self.loadConfirmedLeads();
        else if (self.currentTab === "trips" || page === "trips") self.loadTripsBoard();
        else if (self.currentTab === "requests" || page === "requests") self.loadBookingRequests();
        else if (self.currentTab === "unassigned" || page === "unassigned") self.loadUnassignedLeads();
        else if (self.currentTab === "templates" || page === "templates") self.loadTemplates();
      });

      // 4. Confirmed Leads Status Filter
      $(document).on("click", "#confirmedStatusFilter .btn-ops-pill", function () {
        $("#confirmedStatusFilter .btn-ops-pill").removeClass("active");
        $(this).addClass("active");
        const status = $(this).data("status");
        const filterVal = status === "" ? "all" : String(status);
        $(".ops-kpi-row .pkg-mini-kpi").removeClass("active");
        $(".ops-kpi-row .pkg-mini-kpi[data-filter-status='" + filterVal + "']").addClass("active");
        self.confirmedStatus = status;
        self.confirmedPage = 1;
        self.loadConfirmedLeads();
      });

      // 4b. KPI Card Click Filtering
      $(document).on("click", ".ops-kpi-row .pkg-mini-kpi[data-filter-status]", function () {
        const status = $(this).data("filter-status");
        const targetVal = status === "all" ? "" : String(status);
        $(".ops-kpi-row .pkg-mini-kpi").removeClass("active");
        $(this).addClass("active");
        $("#confirmedStatusFilter .btn-ops-pill").removeClass("active");
        const $pill = $("#confirmedStatusFilter .btn-ops-pill[data-status='" + targetVal + "']");
        if ($pill.length) {
          $pill.addClass("active");
        }
        self.confirmedStatus = targetVal;
        self.confirmedPage = 1;
        self.loadConfirmedLeads();
      });

      $(document).on("click", ".ops-kpi-row .pkg-mini-kpi[data-href]", function () {
        const href = $(this).data("href");
        if (href) window.location.href = href;
      });

      // 5. Confirmed Leads Search (Debounced)
      let searchTimer = null;
      $(document).on("input", "#opsLeadSearchInput", function () {
        clearTimeout(searchTimer);
        const val = $(this).val().trim();
        if (val.length > 0) {
          $("#opsClearLeadSearchBtn").show();
        } else {
          $("#opsClearLeadSearchBtn").hide();
        }
        searchTimer = setTimeout(function () {
          self.confirmedSearch = val;
          self.confirmedPage = 1;
          self.loadConfirmedLeads();
        }, 350);
      });

      $(document).on("click", "#opsClearLeadSearchBtn", function () {
        $("#opsLeadSearchInput").val("").trigger("input");
        $(this).hide();
      });

      // 6. Confirmed Pagination Click
      $(document).on("click", "#opsPaginationControls .btn-ops-page", function () {
        const p = parseInt($(this).data("page"), 10);
        if (p > 0 && p !== self.confirmedPage) {
          self.confirmedPage = p;
          self.loadConfirmedLeads();
        }
      });

      // 7. Trip Planner Date Filters
      $(document).on("change", "#opsTripDateInput", function () {
        self.tripDate = $(this).val();
        self.loadTripsBoard();
      });

      $(document).on("click", "#btnTripDateToday", function () {
        const today = new Date().toISOString().split("T")[0];
        $("#opsTripDateInput").val(today);
        self.tripDate = today;
        $("#btnTripDateTomorrow").removeClass("active");
        $(this).addClass("active");
        self.loadTripsBoard();
      });

      $(document).on("click", "#btnTripDateTomorrow", function () {
        const tomorrow = new Date(Date.now() + 86400000).toISOString().split("T")[0];
        $("#opsTripDateInput").val(tomorrow);
        self.tripDate = tomorrow;
        $("#btnTripDateToday").removeClass("active");
        $(this).addClass("active");
        self.loadTripsBoard();
      });

      // 8. Unassigned Checkbox Selection
      $(document).on("change", "#chkSelectAllUnassigned", function () {
        const checked = $(this).is(":checked");
        $(".chk-unassigned-lead").prop("checked", checked);
        self.updateSelectedLeadsCount();
      });

      $(document).on("change", ".chk-unassigned-lead", function () {
        self.updateSelectedLeadsCount();
      });

      $(document).on("input", "#opsUnassignedSearchInput", function () {
        const val = $(this).val().toLowerCase();
        $("#opsUnassignedTableBody tr").each(function () {
          const rowText = $(this).text().toLowerCase();
          $(this).toggle(rowText.indexOf(val) > -1);
        });
      });

      // 9. Template Channels & Vendor Filter
      $(document).on("click", "#tplChannelFilter .btn-ops-pill", function () {
        $("#tplChannelFilter .btn-ops-pill").removeClass("active");
        $(this).addClass("active");
        self.filterTemplateCards();
      });

      $(document).on("click", "#tplVendorFilter .btn-ops-pill", function () {
        $("#tplVendorFilter .btn-ops-pill").removeClass("active");
        $(this).addClass("active");
        self.filterTemplateCards();
      });

      // 10. Open Booking Request Modal with Button Loading State
      $(document).on("click", ".btn-open-booking-modal", function () {
        const $btn = $(this);
        const leadId = $btn.data("id");
        self.setBtnLoading($btn);
        self.openBookingRequestModal(leadId, function () {
          self.resetBtnLoading($btn);
        });
      });

      // 11. Vendor Type Change inside Booking Request Modal
      $(document).on("change select2:select", "#bkgVendorType", function () {
        const vType = $(this).val();
        self.populateVendorDropdown(vType);
        self.populateTemplateDropdown(vType);
        self.updateLivePreviewContent();
      });

      $(document).on("change select2:select", "#bkgVendorSelect", function () {
        const vId = $(this).val();
        const vType = $("#bkgVendorType").val();
        self.fillSelectedVendorContact(vType, vId);
        self.updateLivePreviewContent();
      });

      // 12. Booking Preview Tab Switches
      $(document).on("click", "#bkgPreviewTabs .btn-ops-preview-tab", function () {
        $("#bkgPreviewTabs .btn-ops-preview-tab").removeClass("active");
        $(this).addClass("active");
        const preview = $(this).data("preview");
        $(".ops-preview-box").removeClass("active");
        if (preview === "whatsapp") {
          $("#previewBoxWhatsApp").addClass("active");
        } else {
          $("#previewBoxEmail").addClass("active");
        }
      });

      // 13. Template Picker Change inside Booking Request Modal
      $(document).on("change select2:select", "#bkgTemplatePicker", function () {
        const tId = $(this).val();
        self.applyTemplateToPreview(tId);
      });

      // Live updates to preview when inputs change
      $(document).on("input change select2:select", "#bkgMealPlan, #bkgCheckIn, #bkgCheckOut, #bkgRoomCategory, #bkgSpecialNotes, #bkgVendorPhone, #bkgVendorEmail, #bkgOptAttachGuest", function () {
        self.updateLivePreviewContent();
      });

      // Dispatch Channel Toggle Change
      $(document).on("change", "input[name='bkgChannel']", function () {
        const ch = $(this).val();
        if (ch === "whatsapp") {
          $("#bkgPreviewTabs button[data-preview='whatsapp']").click();
        } else if (ch === "email") {
          $("#bkgPreviewTabs button[data-preview='email']").click();
        }
      });

      // Live WhatsApp character counter
      $(document).on("input keyup", "#bkgWhatsAppContent", function () {
        const len = ($(this).val() || "").length;
        $("#bkgWaCharCount").text(len + " characters");
      });

      // WhatsApp Copy button
      $(document).on("click", "#btnCopyWaPreview", function () {
        const text = $("#bkgWhatsAppContent").val();
        if (!text) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(function () {
            const $btn = $("#btnCopyWaPreview");
            const orig = $btn.html();
            $btn.html('<i class="fi fi-rr-check text-emerald"></i> Copied!');
            setTimeout(function () { $btn.html(orig); }, 1800);
          });
        }
      });

      // WhatsApp & Email Reset buttons
      $(document).on("click", "#btnResetWaPreview, #btnResetEmailPreview", function () {
        self.updateLivePreviewContent();
      });

      // Email Rich Text Formatting Tools
      $(document).on("click", ".btn-editor-tool", function (e) {
        e.preventDefault();
        const cmd = $(this).data("cmd");
        if (cmd) {
          self.formatEmailDoc(cmd);
        }
      });

      // Email Editor Mode Toggle (Visual vs Code View)
      $(document).on("click", "#btnEmailModeVisual", function (e) {
        e.preventDefault();
        $(".btn-mode-tab").removeClass("active");
        $(this).addClass("active");
        const code = $("#bkgEmailContent").val();
        $("#bkgEmailVisualEditor").html(code).show();
        $("#bkgEmailCodeWrap").addClass("hidden").hide();
      });

      $(document).on("click", "#btnEmailModeCode", function (e) {
        e.preventDefault();
        $(".btn-mode-tab").removeClass("active");
        $(this).addClass("active");
        const html = $("#bkgEmailVisualEditor").html();
        $("#bkgEmailContent").val(html);
        $("#bkgEmailVisualEditor").hide();
        $("#bkgEmailCodeWrap").removeClass("hidden").show();
      });

      // Two-way synchronization between Email Visual Viewport and Hidden Textarea
      $(document).on("input blur keyup", "#bkgEmailVisualEditor", function () {
        $("#bkgEmailContent").val($(this).html());
      });

      $(document).on("input keyup", "#bkgEmailContent", function () {
        $("#bkgEmailVisualEditor").html($(this).val());
      });

      // 14. Send Booking Request Submit
      $(document).on("click", "#btnConfirmSendBookingRequest", function () {
        self.submitBookingRequest();
      });

      // 15. Open Confirmation Voucher Modal with Button Loading State
      $(document).on("click", ".btn-open-voucher-modal", function () {
        const $btn = $(this);
        const leadId = $btn.data("id");
        self.setBtnLoading($btn);
        self.openVoucherModal(leadId, function () {
          self.resetBtnLoading($btn);
        });
      });

      // Voucher Manager Override Toggle
      $(document).on("change", "#voucherOptManagerOverride", function () {
        const checked = $(this).is(":checked");
        const hasPayment = $("#voucherPaymentNotice").hasClass("ok");
        if (hasPayment || checked) {
          $("#btnConfirmIssueVouchers").prop("disabled", false);
        } else {
          $("#btnConfirmIssueVouchers").prop("disabled", true);
        }
      });

      // 16. Save Confirmation Vouchers & Issue
      $(document).on("click", "#btnConfirmIssueVouchers", function () {
        self.submitIssueVouchers();
      });

      // 17. Open Invoice / Proforma Modal with Button Loading State
      $(document).on("click", ".btn-open-invoice-modal", function () {
        const $btn = $(this);
        const leadId = $btn.data("id");
        self.setBtnLoading($btn);
        self.openInvoiceModal(leadId, function () {
          self.resetBtnLoading($btn);
        });
      });

      // Invoice Document Type Toggle (Proforma vs Tax Invoice)
      $(document).on("change", "input[name='invDocType']", function () {
        const val = $(this).val();
        const rawNum = $("#invNumber").val();
        if (val === "tax_invoice") {
          $("#invNumber").val(rawNum.replace(/^PI-/, "INV-"));
        } else {
          $("#invNumber").val(rawNum.replace(/^INV-/, "PI-"));
        }
      });

      // Invoice Editable Advance Payment Live Recalculation
      $(document).on("input keyup change", "#invAdvanceInput", function () {
        const rawPkg = parseFloat($("#invRawPackageTotal").val()) || 0;
        const advance = Math.max(0, parseFloat($(this).val()) || 0);
        const balance = Math.max(0, rawPkg - advance);
        $("#invBalanceDue").text("₹ " + Number(balance).toLocaleString("en-IN"));
        $("#invAdvancePaid").text("- ₹ " + Number(advance).toLocaleString("en-IN"));
      });

      // 18. Send Invoice Submit
      $(document).on("click", "#btnConfirmSendInvoice", function () {
        self.submitSendInvoice();
      });

      // 19. Open Assign / Reassign Modal
      $(document).on("click", ".btn-open-assign-modal", function () {
        const leadId = $(this).data("id");
        const title = $(this).data("title") || "Assign Lead";
        self.openAssignModal([leadId], title);
      });

      $(document).on("click", "#btnOpenBulkAssignModal", function () {
        if (!self.selectedLeadIds.length) return;
        self.openAssignModal(self.selectedLeadIds, "Bulk Assign Leads (" + self.selectedLeadIds.length + ")");
      });

      $(document).on("click", "#btnConfirmAssignLead", function () {
        self.submitAssignLeads();
      });

      // 20. Trip Milestone Buttons
      $(document).on("click", ".btn-trip-milestone", function () {
        const leadId = $(this).data("id");
        const milestone = $(this).data("milestone");
        self.updateTripMilestone(leadId, milestone);
      });

      // 20.5 Driver Dispatch Modal
      $(document).on("click", ".btn-open-driver-dispatch", function () {
        const leadId = $(this).data("id");
        self.closeAllModals();
        $("#dispatchLeadId").val(leadId);
        $("#dispatchDriverName").val($(this).data("driver") || "");
        $("#dispatchDriverPhone").val($(this).data("phone") || "");
        $("#dispatchVehicleNo").val($(this).data("veh") || "");
        $("#modalOpsDriverDispatch").addClass("active");
      });

      $(document).on("click", "#btnConfirmDriverDispatch", function () {
        self.submitDriverDispatch();
      });

      // 21. Feedback Trigger Button
      $(document).on("click", ".btn-trigger-feedback", function () {
        const leadId = $(this).data("id");
        self.triggerFeedbackRequest(leadId);
      });

      // 22. Template Manager Modal Trigger & Save
      $(document).on("click", "#btnOpenNewTemplateModal, #btnOpenNewTemplateModalAlt", function () {
        self.openTemplateEditorModal(null);
      });

      $(document).on("click", ".btn-edit-template", function () {
        const $btn = $(this);
        const tId = $btn.data("id") || $btn.attr("data-id");
        let tpl = self.cachedTemplates.find(function (t) { return Number(t.id) === Number(tId); });
        if (tpl) {
          self.openTemplateEditorModal(tpl);
        } else {
          self.setBtnLoading($btn);
          $.getJSON("/api/v3/operations/templates/", function (res) {
            if (res && res.status === "success" && res.templates) {
              self.cachedTemplates = res.templates;
              tpl = res.templates.find(function (t) { return Number(t.id) === Number(tId); });
              if (tpl) {
                self.openTemplateEditorModal(tpl);
              } else {
                alert("Template could not be found.");
              }
            }
          }).always(function () {
            self.resetBtnLoading($btn);
          });
        }
      });

      $(document).on("click", ".btn-delete-template", function () {
        const tId = $(this).data("id") || $(this).attr("data-id");
        const swalObj = typeof window.Swal !== "undefined" ? window.Swal : (typeof Swal !== "undefined" ? Swal : null);
        if (swalObj && typeof swalObj.fire === "function") {
          swalObj.fire({
            title: "Delete Template?",
            text: "Are you sure you want to delete this template? This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#e11d48",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel"
          }).then(function (result) {
            if (result.isConfirmed) {
              self.deleteTemplate(tId);
            }
          });
        } else if (typeof window.swal === "function" || typeof swal === "function") {
          const swalFn = typeof window.swal === "function" ? window.swal : swal;
          swalFn({
            title: "Delete Template?",
            text: "Are you sure you want to delete this template? This action cannot be undone.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#e11d48",
            confirmButtonText: "Yes, delete it",
            closeOnConfirm: true
          }, function (isConfirm) {
            if (isConfirm) {
              self.deleteTemplate(tId);
            }
          });
        } else {
          if (confirm("Are you sure you want to delete this template?")) {
            self.deleteTemplate(tId);
          }
        }
      });

      $(document).on("click", ".btn-set-default-template", function () {
        const tId = $(this).data("id");
        self.setDefaultTemplate(tId);
      });

      $(document).on("click", "#btnConfirmSaveTemplate", function () {
        self.saveTemplate();
      });

      // 23. Inserter pills for Merge Tags in Template Editor
      $(document).on("click", "#tplMergeTagsWrap .ops-tag-pill", function () {
        const tag = $(this).data("tag");
        self.insertIntoEditor(tag);
      });

      // Template variable live search filter
      $(document).on("input", "#tplSearchMergeTags", function () {
        const q = ($(this).val() || "").toLowerCase().trim();
        if (!q) {
          $("#tplMergeTagsWrap .ops-tag-pill").show();
          $("#tplMergeTagsWrap .ops-var-cat-section").show();
          return;
        }
        $("#tplMergeTagsWrap .ops-tag-pill").each(function () {
          const tag = ($(this).data("tag") || "").toLowerCase();
          const name = ($(this).data("name") || "").toLowerCase();
          const match = tag.indexOf(q) !== -1 || name.indexOf(q) !== -1;
          $(this).toggle(match);
        });
        $("#tplMergeTagsWrap .ops-var-cat-section").each(function () {
          const count = $(this).find(".ops-tag-pill:visible").length;
          $(this).toggle(count > 0);
        });
      });

      // Visual Channel Selector
      $(document).on("click", "#tplChannelSegmented .ops-seg-btn", function () {
        const channel = $(this).data("channel");
        $("#tplChannelSegmented .ops-seg-btn").removeClass("active");
        $(this).addClass("active");
        $("#tplEditorChannel").val(channel);
        self.syncTemplateEditorUI();
        self.loadBodyIntoRichEditor($("#tplEditorBody").val(), channel);
        self.updateTemplateLivePreview();
      });

      // Visual Vendor Type Selector
      $(document).on("click", "#tplVendorSegmented .ops-seg-btn", function () {
        const vendor = $(this).data("vendor");
        $("#tplVendorSegmented .ops-seg-btn").removeClass("active");
        $(this).addClass("active");
        $("#tplEditorVendorType").val(vendor);
        self.syncTemplateEditorUI();
        self.updateTemplateLivePreview();
      });

      // Live update on typing in Rich Editor (Active by Default)
      $(document).on("input", "#tplEditorRichBody", function () {
        const channel = $("#tplEditorChannel").val() || "whatsapp";
        if (channel === "email") {
          $("#tplEditorBody").val($(this).html());
        } else {
          $("#tplEditorBody").val(self.richHtmlToWaText(this));
        }
        self.updateTemplateLivePreview();
      });

      // Live update on typing in Raw Source Textarea
      $(document).on("input", "#tplEditorBody", function () {
        self.updateTemplateLivePreview();
      });

      $(document).on("input", "#tplEditorSubject", function () {
        self.updateTemplateLivePreview();
      });

      // View Switcher: Visual Rich Text vs Source Code
      $(document).on("click", "#btnTplViewRich", function () {
        self.switchTemplateEditorView("rich");
      });

      $(document).on("click", "#btnTplViewSource", function () {
        self.switchTemplateEditorView("source");
      });

      // Preview Mode switcher: Sample Data vs Raw Tags
      $(document).on("click", ".ops-prev-mode-btn", function () {
        $(".ops-prev-mode-btn").removeClass("active");
        $(this).addClass("active");
        self.templatePreviewMode = $(this).data("mode") || "sample";
        self.updateTemplateLivePreview();
      });

      // Copy Preview Text button
      $(document).on("click", "#btnCopyTplPreview", function () {
        const channel = $("#tplEditorChannel").val();
        let textToCopy = "";
        if (channel === "email") {
          textToCopy = $("#tplEmailBodyContent").text() || $("#tplEditorBody").val();
        } else {
          textToCopy = $("#tplWaBubbleContent").text() || $("#tplEditorBody").val();
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(textToCopy).then(function () {
            const $btn = $("#btnCopyTplPreview");
            const orig = $btn.html();
            $btn.html('<i class="fi fi-rr-check text-emerald"></i> Copied!');
            setTimeout(function () { $btn.html(orig); }, 2000);
          });
        }
      });

      // Formatting Toolbar Buttons (supports both Visual Rich Editor & Source mode)
      $(document).on("click", "#btnTplFormatBold", function () {
        self.formatEditorSelection("bold", "*", "*", "bold text");
      });

      $(document).on("click", "#btnTplFormatItalic", function () {
        self.formatEditorSelection("italic", "_", "_", "italic text");
      });

      $(document).on("click", "#btnTplFormatStrike", function () {
        self.formatEditorSelection("strike", "~", "~", "strike text");
      });

      $(document).on("click", "#btnTplFormatCode", function () {
        self.formatEditorSelection("code", "```", "```", "code");
      });

      $(document).on("click", "#btnTplFormatBullet", function () {
        self.formatEditorSelection("bullet", "\n• ", "", "");
      });

      $(document).on("click", ".ops-emoji-btn", function () {
        const emoji = $(this).data("emoji");
        self.insertIntoEditor(emoji);
      });

      // Reset to Standard Preset button
      $(document).on("click", "#btnTplRestorePreset", function () {
        const vendor = $("#tplEditorVendorType").val() || "hotel";
        const channel = $("#tplEditorChannel").val() || "whatsapp";
        if (confirm("Restore the standard recommended preset for " + vendor.toUpperCase() + " (" + channel.toUpperCase() + ")? Any custom edits in the body will be replaced.")) {
          self.restoreTemplatePreset(channel, vendor);
        }
      });

      // 24. Booking Requests Ledger Filter & Pagination
      $(document).on("click", "#bkgLedgerVendorFilter .btn-ops-pill", function () {
        $("#bkgLedgerVendorFilter .btn-ops-pill").removeClass("active");
        $(this).addClass("active");
        self.requestsVendor = $(this).data("vendor");
        self.requestsPage = 1;
        self.loadBookingRequests();
      });

      $(document).on("click", "#bkgLedgerStatusFilter .btn-ops-pill", function () {
        $("#bkgLedgerStatusFilter .btn-ops-pill").removeClass("active");
        $(this).addClass("active");
        self.requestsStatus = $(this).data("status");
        self.requestsPage = 1;
        self.loadBookingRequests();
      });

      let reqSearchTimer = null;
      $(document).on("input", "#opsRequestsSearchInput", function () {
        clearTimeout(reqSearchTimer);
        const val = $(this).val().trim();
        reqSearchTimer = setTimeout(function () {
          self.requestsSearch = val;
          self.requestsPage = 1;
          self.loadBookingRequests();
        }, 350);
      });

      $(document).on("click", "#opsRequestsPaginationControls .btn-ops-page", function () {
        const p = parseInt($(this).data("page"), 10);
        if (p > 0 && p !== self.requestsPage) {
          self.requestsPage = p;
          self.loadBookingRequests();
        }
      });

      // Quick Record Response & Resend
      $(document).on("click", ".btn-quick-record-response", function () {
        const bId = $(this).data("id");
        const curStatus = $(this).data("status");
        const curRef = $(this).data("ref");
        self.openQuickBookingStatusModal(bId, curStatus, curRef);
      });

      $(document).on("click", "#btnConfirmQuickBookingUpdate", function () {
        self.submitQuickBookingStatus();
      });

      $(document).on("click", ".btn-resend-booking-request", function () {
        const $btn = $(this);
        const leadId = $btn.data("lead");
        self.setBtnLoading($btn);
        self.openBookingRequestModal(leadId, function () {
          self.resetBtnLoading($btn);
        });
      });

      // 25. Modal Close Triggers
      $(document).on("click", ".v3-modal-close-trigger", function () {
        $(this).closest(".v3-modal-overlay").removeClass("active is-active");
      });

      $(document).on("click", ".v3-modal-overlay", function (e) {
        if (e.target === this) {
          $(this).removeClass("active is-active");
        }
      });

      // 26. Dedicated CTA Page Event Handlers
      $(document).on("click", "#btnLoadBookingLead", function () {
        const $btn = $(this);
        const id = $("#bkgLeadIdManualInput").val().trim();
        if (id) {
          self.setBtnLoading($btn);
          self.openBookingRequestModal(id, function () {
            self.resetBtnLoading($btn);
          });
        }
      });

      $(document).on("click", "#btnLoadVoucherLead", function () {
        const $btn = $(this);
        const id = $("#voucherLeadIdManualInput").val().trim();
        if (id) {
          self.setBtnLoading($btn);
          self.openVoucherModal(id, function () {
            self.resetBtnLoading($btn);
          });
        }
      });

      $(document).on("click", "#btnLoadInvoiceLead", function () {
        const $btn = $(this);
        const id = $("#invLeadIdManualInput").val().trim();
        if (id) {
          self.setBtnLoading($btn);
          self.openInvoiceModal(id, function () {
            self.resetBtnLoading($btn);
          });
        }
      });

      $(document).on("click", "#btnLoadDispatchLead", function () {
        const id = $("#dispatchLeadIdManualInput").val().trim();
        if (id) {
          $("#dispatchLeadId").val(id);
        }
      });

      $(document).on("input", "#assignTargetLeadManual", function () {
        $("#assignTargetLeadIds").val($(this).val().trim());
      });
    },

    handleHashRouting: function () {
      const hash = window.location.hash.replace("#", "");
      if (["confirmed", "trips", "requests", "unassigned", "templates"].includes(hash)) {
        this.switchTab(hash);
      }
    },

    switchTab: function (tab) {
      this.currentTab = tab;
      window.location.hash = tab;

      $("#opsNavTabs .btn-date-preset").removeClass("active");
      $("#opsNavTabs .btn-date-preset[data-tab='" + tab + "']").addClass("active");

      $(".ops-tab-section").removeClass("active");

      if (tab === "confirmed") {
        $("#tabSectionConfirmed").addClass("active");
        this.loadConfirmedLeads();
      } else if (tab === "trips") {
        $("#tabSectionTrips").addClass("active");
        this.loadTripsBoard();
      } else if (tab === "requests") {
        $("#tabSectionRequests").addClass("active");
        this.loadBookingRequests();
      } else if (tab === "unassigned") {
        $("#tabSectionUnassigned").addClass("active");
        this.loadUnassignedLeads();
      } else if (tab === "templates") {
        $("#tabSectionTemplates").addClass("active");
        this.loadTemplates();
      }
    },

    // -----------------------------------------------------------------------
    // Metrics & Agents Loader
    // -----------------------------------------------------------------------
    loadMetrics: function () {
      $.getJSON("/api/v3/operations/leads/?action=metrics", function (res) {
        if (res && res.status === "success" && res.metrics) {
          const m = res.metrics;
          $("#statConfirmedTotal").text(m.confirmed_total || 0);
          $("#statQuoteApproved").text(m.quotation_approved || 0);
          $("#statPaidBookings").text(m.confirmed_queue !== undefined ? m.confirmed_queue : (m.payments_recorded || 0));
          $("#statVouchersIssued").text(m.vouchers_issued || 0);
          $("#statTripsInProgress").text(m.trips_in_progress || 0);

          $("#badgeConfirmedCount").text(m.confirmed_total || 0);
          $("#badgeTripsCount").text(m.trips_in_progress || 0);
          $("#badgeUnassignedCount").text(m.unassigned_count || 0);

          // Update filter pill counts dynamically
          $(".pill-count-all, #pillCountAll").text(m.confirmed_total || 0);
          $(".pill-count-approved, #pillCountApproved").text(m.quotation_approved || 0);
          $(".pill-count-confirmed, #pillCountConfirmed").text(m.confirmed_queue !== undefined ? m.confirmed_queue : (m.payments_recorded || 0));
          $(".pill-count-voucher, #pillCountVoucher").text(m.vouchers_issued || 0);
          $(".pill-count-progress, #pillCountProgress").text(m.trips_in_progress || 0);
          $(".pill-count-completed, #pillCountCompleted").text(m.trips_completed || 0);
        }
      });
    },

    loadAgents: function () {
      const self = this;
      $.getJSON("/api/v3/operations/assign/", function (res) {
        if (res && res.status === "success" && res.agents) {
          self.cachedAgents = res.agents;
          let opts = '<option value="">-- Choose Sales Agent --</option>';
          res.agents.forEach(function (ag) {
            opts += '<option value="' + ag.id + '">' + ag.name + " (" + ag.role_name + ")</option>";
          });
          $("#assignAgentSelect").html(opts);
        }
      });
    },

    // -----------------------------------------------------------------------
    // Confirmed Leads Table
    // -----------------------------------------------------------------------
    loadConfirmedLeads: function () {
      const self = this;
      const $tbody = $("#opsConfirmedTableBody");
      $tbody.html('<tr><td colspan="8" class="text-center p-30 text-muted"><i class="fi fi-rr-spinner fi-spin"></i> Fetching confirmed operational leads...</td></tr>');

      let url = "/api/v3/operations/leads/?type=confirmed&page=" + this.confirmedPage + "&limit=" + this.confirmedLimit;
      if (this.confirmedStatus !== "") url += "&status=" + this.confirmedStatus;
      if (this.confirmedSearch !== "") url += "&search=" + encodeURIComponent(this.confirmedSearch);

      $.getJSON(url, function (res) {
        if (res && res.status === "success") {
          self.renderConfirmedTable(res.leads || []);
          self.renderPagination(res.total || 0, res.page || 1, res.limit || 20);
        } else {
          $tbody.html('<tr><td colspan="5" class="text-center p-30 text-rose">Failed to load confirmed leads.</td></tr>');
        }
      }).fail(function () {
        $tbody.html('<tr><td colspan="5" class="text-center p-30 text-rose">Network error while fetching leads.</td></tr>');
      });
    },

    renderConfirmedTable: function (leads) {
      const $tbody = $("#opsConfirmedTableBody");
      if (!leads.length) {
        $tbody.html('<tr><td colspan="5" class="text-center p-40 text-muted"><i class="fi fi-rr-check font-32 text-slate-400 d-block mb-10"></i>No confirmed leads found in this filter view.</td></tr>');
        return;
      }

      let html = "";
      leads.forEach(function (ld) {
        const leadLink = "/dashboard/leads/view/?id=" + ld.encoded_id;
        const qPrice = Number(ld.quotation_price) || 0;
        const totalPaid = Number(ld.total_paid) || 0;
        const balDue = Number(ld.balance_due) || 0;
        const hasBalance = balDue > 0;
        const cleanPhone = (ld.mobile || "").replace(/[^0-9]/g, "");
        const waLink = cleanPhone ? "https://wa.me/" + cleanPhone : "";

        let paymentHtml = "";
        if (qPrice > 0) {
          paymentHtml = '<span class="ops-price-total">₹ ' + qPrice.toLocaleString() + '</span>' +
            (hasBalance 
              ? '<span class="ops-pay-pill due">Due: ₹ ' + balDue.toLocaleString() + '</span>' 
              : '<span class="ops-pay-pill settled"><i class="fi fi-rr-check font-9"></i> Settled</span>');
        } else {
          paymentHtml = '<span class="ops-price-total text-muted font-12">Price Pending</span>' +
            '<span class="ops-pay-pill pending">Awaiting Quote</span>';
        }

        // Pure operational fulfillment status (never show payment-related terms in Status column)
        let opStatusLabel = ld.status_label || "Active";
        let opStatusBadge = ld.status_badge || "badge-default";

        if (/paid|payment|upay|success/i.test(opStatusLabel)) {
          if (ld.bookings_count > 0) {
            opStatusLabel = "Vendor Requested";
            opStatusBadge = "badge-in-progress";
          } else {
            opStatusLabel = "Confirmed";
            opStatusBadge = "badge-approved";
          }
        }

        html += '<tr>' +
          '<td class="td-lead">' +
            '<div class="ops-lead-meta">' +
              '<div class="ops-lead-header-row">' +
                '<a href="' + leadLink + '" class="ops-lead-title" title="View Lead Details">' + (ld.customer_name || "Traveler") + '</a>' +
                '<span class="ops-lead-id-pill">#' + ld.id + '</span>' +
              '</div>' +
              '<div class="ops-lead-sub">' +
                '<span class="ops-lead-phone"><i class="fi fi-rr-phone-call font-11"></i> ' + (ld.mobile || "N/A") + '</span>' +
                (waLink ? '<a href="' + waLink + '" target="_blank" class="ops-lead-wa-pill" title="Chat on WhatsApp"><i class="fi fi-brands-whatsapp"></i></a>' : '') +
              '</div>' +
              '<div class="ops-lead-agent">' +
                '<i class="fi fi-rr-user font-10"></i> <span>' + (ld.agent_name || "Direct") + '</span>' +
              '</div>' +
            '</div>' +
          '</td>' +
          '<td class="td-tour">' +
            '<div class="ops-tour-cell">' +
              '<div class="ops-tour-title" title="' + (ld.destination || "Holiday Tour") + '">' +
                '<i class="fi fi-rr-marker text-indigo font-12"></i> <strong>' + (ld.destination || "Holiday Tour") + '</strong>' +
              '</div>' +
              '<div class="ops-tour-meta">' +
                '<span class="ops-tour-dates"><i class="fi fi-rr-calendar text-muted font-11"></i> ' + (ld.travel_date || "Dates Pending") + '</span>' +
                '<span class="ops-pax-badge"><i class="fi fi-rr-users text-muted font-10"></i> ' + ld.adults + 'A' + (ld.children > 0 ? ', ' + ld.children + 'C' : '') + '</span>' +
              '</div>' +
            '</div>' +
          '</td>' +
          '<td class="td-financials">' +
            '<div class="ops-price-meta">' +
              paymentHtml +
            '</div>' +
          '</td>' +
          '<td class="td-status">' +
            '<span class="ops-status-badge ' + opStatusBadge + '"><span class="badge-dot"></span> ' + opStatusLabel + '</span>' +
          '</td>' +
          '<td class="td-actions text-right">' +
            '<div class="ops-action-grid">' +
              '<button type="button" class="btn-ops-action primary btn-open-booking-modal" data-id="' + ld.encoded_id + '" title="Send Vendor Booking Request"><i class="fi fi-rr-paper-plane"></i> <span>Request</span></button>' +
              '<button type="button" class="btn-ops-action emerald btn-open-voucher-modal" data-id="' + ld.encoded_id + '" title="Issue Confirmation Vouchers"><i class="fi fi-rr-ticket"></i> <span>Voucher</span></button>' +
              '<button type="button" class="btn-ops-action amber btn-open-invoice-modal" data-id="' + ld.encoded_id + '" title="Generate Proforma / Invoice"><i class="fi fi-rr-document"></i> <span>Invoice</span></button>' +
              '<a href="' + leadLink + '" class="btn-ops-action neutral" title="View Full Lead Details"><i class="fi fi-rr-eye"></i> <span>View</span></a>' +
            '</div>' +
          '</td>' +
        '</tr>';
      });

      $tbody.html(html);
    },

    renderPagination: function (total, page, limit) {
      const totalPages = Math.ceil(total / limit);
      const start = total === 0 ? 0 : (page - 1) * limit + 1;
      const end = Math.min(page * limit, total);

      $("#opsPaginationInfo").text("Showing " + start + " - " + end + " of " + total + " leads");

      let btns = "";
      if (totalPages > 1) {
        for (let i = 1; i <= totalPages; i++) {
          btns += '<button type="button" class="btn-ops-page ' + (i === page ? 'active' : '') + '" data-page="' + i + '">' + i + '</button>';
        }
      }
      $("#opsPaginationControls").html(btns);
    },

    // -----------------------------------------------------------------------
    // Daily Trip Planner Board
    // -----------------------------------------------------------------------
    loadTripsBoard: function () {
      const self = this;
      $("#cardsTomorrowTrips, #cardsDepartingToday, #cardsActiveOnTour, #cardsReturningToday, #cardsCompletedTrips").html(
        '<div class="ops-empty-col"><i class="fi fi-rr-spinner fi-spin"></i> Loading...</div>'
      );

      $.getJSON("/api/v3/operations/trip-planner/?date=" + this.tripDate, function (res) {
        if (res && res.status === "success") {
          self.renderTripColumn("#cardsTomorrowTrips", res.tomorrow_trips || [], "tomorrow");
          self.renderTripColumn("#cardsDepartingToday", res.departing_today || [], "departing");
          self.renderTripColumn("#cardsActiveOnTour", res.active_on_tour || [], "on_tour");
          self.renderTripColumn("#cardsReturningToday", res.returning_today || [], "returning");
          self.renderTripColumn("#cardsCompletedTrips", res.completed_trips || [], "completed");

          $("#badgeTomorrowTrips").text((res.tomorrow_trips || []).length);
          $("#badgeDepartingToday").text((res.departing_today || []).length);
          $("#badgeActiveOnTour").text((res.active_on_tour || []).length);
          $("#badgeReturningToday").text((res.returning_today || []).length);
          $("#badgeCompletedTrips").text((res.completed_trips || []).length);

          const totalActive = (res.tomorrow_trips || []).length + (res.departing_today || []).length + (res.active_on_tour || []).length + (res.returning_today || []).length;
          $("#opsTotalActiveTours").text(totalActive);
        }
      });
    },

    renderTripColumn: function (containerSelector, items, type) {
      const $col = $(containerSelector);
      if (!items.length) {
        $col.html('<div class="ops-empty-col">No scheduled tours.</div>');
        return;
      }

      let html = "";
      items.forEach(function (item) {
        const leadLink = "/dashboard/leads/view/?id=" + item.encoded_id;
        const waUrl = item.wa_mobile ? "https://wa.me/" + item.wa_mobile : "#";

        html += '<div class="ops-trip-card">' +
          '<div class="ops-trip-card-top">' +
            '<a href="' + leadLink + '" class="ops-trip-guest-name">' + item.guest_name + '</a>' +
            '<span class="ops-status-badge ' + (item.has_voucher ? 'badge-voucher' : 'badge-payment-init') + '">' +
              (item.has_voucher ? 'Vouchers Ready' : 'Voucher Pending') +
            '</span>' +
          '</div>' +
          '<div class="ops-trip-dest"><i class="fi fi-rr-marker"></i> ' + item.destination + '</div>' +
          '<div class="ops-trip-dates-row">' +
            '<span><i class="fi fi-rr-calendar"></i> ' + item.start_date + ' to ' + item.end_date + '</span>' +
            '<span>' + item.pax_adults + 'A' + (item.pax_children > 0 ? ', ' + item.pax_children + 'C' : '') + '</span>' +
          '</div>' +
          (item.driver_name ? '<div class="text-emerald font-11 mb-8"><strong><i class="fi fi-rr-car"></i> Driver:</strong> ' + item.driver_name + ' &bull; ' + item.vehicle_no + '</div>' : '<div class="text-amber font-11 mb-8"><i class="fi fi-rr-exclamation"></i> Driver unassigned</div>') +
          '<div class="ops-trip-card-actions">';

        if (type === "tomorrow") {
          html += '<button type="button" class="btn-ops-action btn-open-driver-dispatch" data-id="' + item.encoded_id + '" data-driver="' + (item.driver_name || "") + '" data-phone="' + (item.driver_phone || "") + '" data-veh="' + (item.vehicle_no || "") + '" title="Dispatch Driver 24hr Pre-trip Info"><i class="fi fi-rr-car"></i> Driver</button>' +
                  '<button type="button" class="btn-ops-action primary btn-trip-milestone" data-id="' + item.encoded_id + '" data-milestone="start_trip" title="Start Early"><i class="fi fi-rr-play"></i> Start</button>';
        } else if (type === "departing") {
          html += '<button type="button" class="btn-ops-action primary btn-trip-milestone" data-id="' + item.encoded_id + '" data-milestone="start_trip"><i class="fi fi-rr-play"></i> Start Tour</button>' +
                  '<button type="button" class="btn-ops-action btn-open-driver-dispatch" data-id="' + item.encoded_id + '" data-driver="' + (item.driver_name || "") + '" data-phone="' + (item.driver_phone || "") + '" data-veh="' + (item.vehicle_no || "") + '" title="Dispatch Driver Info"><i class="fi fi-rr-car"></i> Driver</button>';
        } else if (type === "returning" || type === "on_tour") {
          html += '<button type="button" class="btn-ops-action emerald btn-trip-milestone" data-id="' + item.encoded_id + '" data-milestone="complete_trip"><i class="fi fi-rr-check"></i> Complete Tour</button>' +
                  '<button type="button" class="btn-ops-action btn-open-driver-dispatch" data-id="' + item.encoded_id + '" data-driver="' + (item.driver_name || "") + '" data-phone="' + (item.driver_phone || "") + '" data-veh="' + (item.vehicle_no || "") + '" title="Dispatch Driver Info"><i class="fi fi-rr-car"></i> Driver</button>';
        } else if (type === "completed") {
          html += '<button type="button" class="btn-ops-action amber btn-trigger-feedback" data-id="' + item.encoded_id + '"><i class="fi fi-brands-whatsapp"></i> Send Feedback</button>';
        }

        if (waUrl !== "#") {
          html += '<a href="' + waUrl + '" target="_blank" class="btn-ops-action" title="WhatsApp Guest"><i class="fi fi-brands-whatsapp text-emerald"></i></a>';
        }
        html += '<a href="' + leadLink + '" class="btn-ops-action" title="View Lead"><i class="fi fi-rr-eye"></i></a>' +
          '</div></div>';
      });

      $col.html(html);
    },

    updateTripMilestone: function (leadId, milestone) {
      const self = this;
      $.post("/api/v3/operations/trip-planner/", { lead_id: leadId, milestone: milestone }, function (res) {
        if (res && res.status === "success") {
          alert(res.message);
          self.loadTripsBoard();
          self.loadMetrics();
        } else {
          alert(res.message || "Failed to update milestone.");
        }
      }, "json");
    },

    submitDriverDispatch: function () {
      const self = this;
      const leadId = $("#dispatchLeadId").val();
      const payload = {
        lead_id: leadId,
        milestone: "dispatch_driver",
        driver_name: $("#dispatchDriverName").val(),
        driver_phone: $("#dispatchDriverPhone").val(),
        vehicle_type: $("#dispatchVehicleType").val(),
        vehicle_no: $("#dispatchVehicleNo").val(),
        pickup_time: $("#dispatchPickupTime").val(),
        pickup_location: $("#dispatchPickupLoc").val()
      };

      if (!payload.driver_name || !payload.driver_phone) {
        alert("Please enter driver name and phone.");
        return;
      }

      $.post("/api/v3/operations/trip-planner/", payload, function (res) {
        if (res && res.status === "success") {
          alert(res.message);
          $("#modalOpsDriverDispatch").removeClass("active");
          if (res.whatsapp_link) {
            window.open(res.whatsapp_link, "_blank");
          }
          if ($("#operationsApp").data("page") === "dispatch") {
            window.location.href = "/dashboard/operations/trips/";
          }
          self.loadTripsBoard();
        } else {
          alert(res.message || "Failed to dispatch driver.");
        }
      }, "json");
    },

    triggerFeedbackRequest: function (leadId) {
      $.post("/api/v3/operations/feedback/", { lead_id: leadId }, function (res) {
        if (res && res.status === "success") {
          if (res.whatsapp_link) {
            window.open(res.whatsapp_link, "_blank");
          }
          alert("Feedback review link generated and logged!");
        } else {
          alert(res.message || "Could not generate feedback request.");
        }
      }, "json");
    },

    // -----------------------------------------------------------------------
    // Unassigned Leads Desk
    // -----------------------------------------------------------------------
    loadUnassignedLeads: function () {
      const self = this;
      const $tbody = $("#opsUnassignedTableBody");
      $tbody.html('<tr><td colspan="7" class="text-center p-30 text-muted"><i class="fi fi-rr-spinner fi-spin"></i> Loading unassigned leads...</td></tr>');

      $.getJSON("/api/v3/operations/leads/?type=unassigned", function (res) {
        if (res && res.status === "success") {
          self.renderUnassignedTable(res.leads || []);
        } else {
          $tbody.html('<tr><td colspan="7" class="text-center p-30 text-rose">Failed to load unassigned leads.</td></tr>');
        }
      });
    },

    renderUnassignedTable: function (leads) {
      const $tbody = $("#opsUnassignedTableBody");
      if (!leads.length) {
        $tbody.html('<tr><td colspan="7" class="text-center p-40 text-muted"><i class="fi fi-rr-badge-check font-32 text-emerald d-block mb-10"></i>All leads have been allocated to sales executives.</td></tr>');
        return;
      }

      let html = "";
      leads.forEach(function (ld) {
        html += '<tr>' +
          '<td><input type="checkbox" class="chk-unassigned-lead" value="' + ld.id + '"></td>' +
          '<td><strong>' + ld.customer_name + '</strong><br><small class="text-muted">Lead #' + ld.id + '</small></td>' +
          '<td>' + (ld.destination || "General Travel Inquiry") + '</td>' +
          '<td><div>' + (ld.mobile || "N/A") + '</div><small class="text-muted">' + (ld.email || "") + '</small></td>' +
          '<td><span class="ops-status-badge badge-payment-init">Direct Enquiry</span></td>' +
          '<td><small class="text-muted">' + ld.created_at + '</small></td>' +
          '<td class="text-right">' +
            '<button type="button" class="btn-ops-action primary btn-open-assign-modal" data-id="' + ld.id + '" data-title="Assign Lead #' + ld.id + '"><i class="fi fi-rr-user-add"></i> Assign</button>' +
          '</td>' +
        '</tr>';
      });

      $tbody.html(html);
      this.updateSelectedLeadsCount();
    },

    updateSelectedLeadsCount: function () {
      const selected = [];
      $(".chk-unassigned-lead:checked").each(function () {
        selected.push(parseInt($(this).val(), 10));
      });
      this.selectedLeadIds = selected;
      $("#selectedLeadsCount").text(selected.length + " leads selected");
      $("#btnOpenBulkAssignModal").prop("disabled", selected.length === 0);
    },

    openAssignModal: function (leadIds, title) {
      this.closeAllModals();
      $("#assignTargetLeadIds").val(leadIds.join(","));
      $("#assignModalTitle").text(title);
      $("#assignReasonText").val("");
      $("#modalOpsAssign").addClass("active");
    },

    submitAssignLeads: function () {
      const self = this;
      const leadIdsStr = $("#assignTargetLeadIds").val();
      const agentId = $("#assignAgentSelect").val();
      const reason = $("#assignReasonText").val();

      if (!leadIdsStr || !agentId) {
        alert("Please select a sales executive.");
        return;
      }

      const ids = leadIdsStr.split(",").map(function (x) { return parseInt(x, 10); });

      $.ajax({
        url: "/api/v3/operations/assign/",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ lead_ids: ids, agent_id: agentId, reason: reason }),
        success: function (res) {
          if (res && res.status === "success") {
            alert(res.message);
            $("#modalOpsAssign").removeClass("active");
            if ($("#operationsApp").data("page") === "assign") {
              window.location.href = "/dashboard/operations/unassigned/";
            }
            self.loadUnassignedLeads();
            self.loadMetrics();
          } else {
            alert(res.message || "Failed to assign lead(s).");
          }
        }
      });
    },

    // -----------------------------------------------------------------------
    // Booking Request & Live Interactive Preview
    // -----------------------------------------------------------------------
    openBookingRequestModal: function (encodedLeadId, cb) {
      const self = this;
      this.closeAllModals();
      $("#bkgModalLeadId").val(encodedLeadId);

      $.getJSON("/api/v3/operations/booking-request/?lead_id=" + encodedLeadId, function (res) {
        if (res && res.status === "success") {
          self.currentLeadBookingData = res;
          self.cachedProperties = res.properties || [];
          self.cachedTransporters = res.transporters || [];
          self.cachedTemplates = res.templates || [];

          // Populate traveler info ribbon
          if (res.lead) {
            $("#bkgLeadGuestName").text(res.lead.guest_name || "Guest");
            const leadNum = res.lead.lead_id || res.lead.id || "";
            let displayId = leadNum ? leadNum : "";
            if (!displayId && encodedLeadId) {
              try { displayId = atob(encodedLeadId); } catch (e) { displayId = encodedLeadId; }
            }
            $("#bkgLeadIdBadge").text("#" + displayId);
            $("#bkgLeadDestBadge").html('<i class="fi fi-rr-map-marker"></i> ' + (res.lead.destination || "Tour Package"));
            const paxText = (res.lead.pax_adults || 1) + " Adults" + (res.lead.pax_children ? ", " + res.lead.pax_children + " Child" : "");
            $("#bkgLeadPaxBadge").html('<i class="fi fi-rr-users"></i> ' + paxText);
          }

          // Prefill logistics
          $("#bkgCheckIn").val(res.lead.check_in || "");
          $("#bkgCheckOut").val(res.lead.check_out || "");
          $("#bkgRoomCategory").val(res.lead.room_category || "Standard Room");

          // Reset vendor type
          $("#bkgVendorType").val("hotel");

          self.populateVendorDropdown("hotel");
          self.populateTemplateDropdown("hotel");

          // Reset channel radio to both
          $("input[name='bkgChannel'][value='both']").prop("checked", true);

          // Reset preview tab to whatsapp
          $("#bkgPreviewTabs button[data-preview='whatsapp']").click();

          // Reset email editor mode to visual preview
          $(".btn-mode-tab").removeClass("active");
          $("#btnEmailModeVisual").addClass("active");
          $("#bkgEmailVisualEditor").show();
          $("#bkgEmailCodeWrap").addClass("hidden").hide();

          self.initBookingModalSelect2();
          self.updateLivePreviewContent();

          $("#modalOpsBookingRequest").addClass("active");
        } else {
          alert(res.message || "Could not load booking details.");
        }
      }).fail(function () {
        alert("Failed to connect to operations backend. Please try again.");
      }).always(function () {
        if (typeof cb === "function") cb();
      });
    },

    initBookingModalSelect2: function () {
      if (typeof $.fn.select2 !== "function") return;
      const $modal = $("#modalOpsBookingRequest");
      $("#bkgVendorType, #bkgVendorSelect, #bkgMealPlan, #bkgTemplatePicker").each(function () {
        const $el = $(this);
        try {
          if ($el.hasClass("select2-hidden-accessible")) {
            $el.select2("destroy");
          }
          $el.select2({
            dropdownParent: $modal,
            width: "100%",
            placeholder: $el.data("placeholder") || "Select an option"
          });
        } catch (e) {
          console.warn("Select2 init warning:", e);
        }
      });
    },

    populateVendorDropdown: function (vendorType) {
      let opts = '<option value="">-- Choose ' + (vendorType === "hotel" ? "Property" : (vendorType === "transport" ? "Transporter" : "Vendor")) + " --</option>";
      const list = vendorType === "hotel" ? this.cachedProperties : (vendorType === "transport" ? this.cachedTransporters : []);
      list.forEach(function (v) {
        opts += '<option value="' + v.id + '" data-phone="' + (v.phone || "") + '" data-email="' + (v.email || "") + '">' + v.name + (v.city ? " (" + v.city + ")" : "") + "</option>";
      });
      $("#bkgVendorSelect").html(opts);
      $("#bkgVendorPhone").val("");
      $("#bkgVendorEmail").val("");

      // Update avatar icon on WhatsApp preview
      if (vendorType === "hotel") {
        $("#bkgWaVendorAvatarIcon").attr("class", "fi fi-rr-building");
      } else if (vendorType === "transport") {
        $("#bkgWaVendorAvatarIcon").attr("class", "fi fi-rr-car");
      } else {
        $("#bkgWaVendorAvatarIcon").attr("class", "fi fi-rr-user");
      }

      if (typeof $.fn.select2 === "function" && $("#bkgVendorSelect").hasClass("select2-hidden-accessible")) {
        $("#bkgVendorSelect").trigger("change.select2");
      }
    },

    fillSelectedVendorContact: function (vendorType, vendorId) {
      const $opt = $("#bkgVendorSelect option:selected");
      $("#bkgVendorPhone").val($opt.data("phone") || "");
      $("#bkgVendorEmail").val($opt.data("email") || "");
    },

    populateTemplateDropdown: function (vendorType) {
      let opts = '<option value="">Default Template</option>';
      this.cachedTemplates.forEach(function (tpl) {
        if (tpl.vendor_type === vendorType) {
          opts += '<option value="' + tpl.id + '">' + tpl.title + " (" + tpl.channel.toUpperCase() + ")</option>";
        }
      });
      $("#bkgTemplatePicker").html(opts);

      if (typeof $.fn.select2 === "function" && $("#bkgTemplatePicker").hasClass("select2-hidden-accessible")) {
        $("#bkgTemplatePicker").trigger("change.select2");
      }
    },

    updateLivePreviewContent: function () {
      if (!this.currentLeadBookingData) return;
      const data = this.currentLeadBookingData;
      const vendorType = $("#bkgVendorType").val();
      const vendorName = $("#bkgVendorSelect option:selected").text().replace(/-- Choose.*--/, "").trim() || "Vendor Partner";
      const vendorPhone = $("#bkgVendorPhone").val() || "";
      const vendorEmail = $("#bkgVendorEmail").val() || "";
      const checkIn = $("#bkgCheckIn").val() || (data.lead && data.lead.check_in) || "";
      const checkOut = $("#bkgCheckOut").val() || (data.lead && data.lead.check_out) || "";
      const roomCategory = $("#bkgRoomCategory").val() || "Standard";
      const mealPlan = $("#bkgMealPlan").val() || "CP";
      const specialNotes = $("#bkgSpecialNotes").val() || "None";
      const nightsCount = (data.lead && data.lead.nights) ? data.lead.nights : 1;

      // Live updates to header / recipient
      $("#bkgWaLiveVendorName").text(vendorName);
      $("#bkgEmailRecipientPreview").text(vendorEmail ? (vendorName + " <" + vendorEmail + ">") : (vendorName + " <booking@hotel.com>"));

      // Find default templates for whatsapp & email
      const waTpl = this.cachedTemplates.find(function (t) { return t.vendor_type === vendorType && t.channel === "whatsapp" && t.is_default; });
      const emTpl = this.cachedTemplates.find(function (t) { return t.vendor_type === vendorType && t.channel === "email" && t.is_default; });

      const replaceTags = function (text) {
        if (!text) return "";
        return text
          .replace(/{vendor_name}/g, vendorName)
          .replace(/{vendor_contact}/g, vendorPhone)
          .replace(/{guest_name}/g, data.lead.guest_name)
          .replace(/{guest_phone}/g, data.lead.guest_phone || "")
          .replace(/{check_in}/g, checkIn)
          .replace(/{check_out}/g, checkOut)
          .replace(/{nights}/g, nightsCount)
          .replace(/{room_category}/g, roomCategory)
          .replace(/{room_count}/g, "1")
          .replace(/{meal_plan}/g, mealPlan)
          .replace(/{vehicle_type}/g, "Sedan / SUV")
          .replace(/{pax_adults}/g, data.lead.pax_adults || 1)
          .replace(/{pax_children}/g, data.lead.pax_children || 0)
          .replace(/{special_instructions}/g, specialNotes)
          .replace(/{agency_name}/g, (data.agency && data.agency.name) ? data.agency.name : "Musafir India Trip")
          .replace(/{ops_manager_name}/g, (data.agency && data.agency.ops_name) ? data.agency.ops_name : "Operations Team")
          .replace(/{ops_manager_phone}/g, (data.agency && data.agency.ops_phone) ? data.agency.ops_phone : "");
      };

      if (waTpl) {
        $("#bkgWhatsAppContent").val(replaceTags(waTpl.body));
      } else {
        $("#bkgWhatsAppContent").val(replaceTags("Dear {vendor_name},\n\nGreetings from {agency_name}!\n\nKindly confirm availability and reserve room(s) as per following booking request:\n\n🏨 Property: {vendor_name}\n👤 Guest Name: {guest_name} ({pax_adults} Adults, {pax_children} Children)\n📅 Check-In: {check_in}\n📅 Check-Out: {check_out} ({nights} Nights)\n🛏️ Room Type: {room_category} (1 Rooms)\n🍽️ Meal Plan: {meal_plan}\n📝 Special Remarks: {special_instructions}\n\nPlease acknowledge receipt with your reservation confirmation number.\n\nWarm regards,\n{ops_manager_name} | {agency_name}\n📞 {ops_manager_phone}"));
      }

      let emailHtmlBody = "";
      if (emTpl) {
        $("#bkgEmailSubject").val(replaceTags(emTpl.subject));
        emailHtmlBody = replaceTags(emTpl.body);
      } else {
        $("#bkgEmailSubject").val(replaceTags("Booking Request: {guest_name} - {nights}N - Check-In: {check_in} - {agency_name}"));
        emailHtmlBody = '<p>Dear Reservations Team (<strong>' + vendorName + '</strong>),</p>' +
          '<p>Warm greetings from <strong>' + ((data.agency && data.agency.name) ? data.agency.name : "Musafir India Trip Pvt. Ltd.") + '</strong>.</p>' +
          '<p>Please find our booking request below and confirm with your reservation reference number:</p>' +
          '<table style="width:100%; border-collapse:collapse; margin:16px 0; font-size:13px;">' +
            '<tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc; width:35%;">Property / Service</td><td style="padding:8px; border:1px solid #e2e8f0;">' + vendorName + '</td></tr>' +
            '<tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;">Guest Name</td><td style="padding:8px; border:1px solid #e2e8f0;">' + data.lead.guest_name + ' (' + (data.lead.pax_adults || 1) + ' Adults' + (data.lead.pax_children ? ', ' + data.lead.pax_children + ' Children' : '') + ')</td></tr>' +
            '<tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;">Check-In / Pickup</td><td style="padding:8px; border:1px solid #e2e8f0;">' + checkIn + '</td></tr>' +
            '<tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;">Check-Out / End</td><td style="padding:8px; border:1px solid #e2e8f0;">' + checkOut + ' (' + nightsCount + ' Nights)</td></tr>' +
            '<tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;">Room / Vehicle Category</td><td style="padding:8px; border:1px solid #e2e8f0;">' + roomCategory + '</td></tr>' +
            '<tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;">Meal Plan</td><td style="padding:8px; border:1px solid #e2e8f0;">' + mealPlan + '</td></tr>' +
            '<tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;">Special Remarks</td><td style="padding:8px; border:1px solid #e2e8f0;">' + specialNotes + '</td></tr>' +
          '</table>' +
          '<p>Please acknowledge and reply with your booking confirmation reference ID at the earliest.</p>' +
          '<p>Best regards,<br><strong>' + ((data.agency && data.agency.ops_name) ? data.agency.ops_name : "Operations Team") + '</strong><br>' + ((data.agency && data.agency.name) ? data.agency.name : "Musafir India Trip") + '<br>Contact: ' + ((data.agency && data.agency.ops_phone) ? data.agency.ops_phone : "") + '</p>';
      }

      $("#bkgEmailContent").val(emailHtmlBody);
      $("#bkgEmailVisualEditor").html(emailHtmlBody);

      // Update character counter
      const waLen = ($("#bkgWhatsAppContent").val() || "").length;
      $("#bkgWaCharCount").text(waLen + " characters");
    },

    applyTemplateToPreview: function (templateId) {
      if (!templateId) {
        this.updateLivePreviewContent();
        return;
      }
      const tpl = this.cachedTemplates.find(function (t) { return t.id === parseInt(templateId, 10); });
      if (!tpl || !this.currentLeadBookingData) return;

      const data = this.currentLeadBookingData;
      const vendorName = $("#bkgVendorSelect option:selected").text().replace(/-- Choose.*--/, "").trim() || "Vendor Partner";
      const checkIn = $("#bkgCheckIn").val() || (data.lead && data.lead.check_in) || "";
      const checkOut = $("#bkgCheckOut").val() || (data.lead && data.lead.check_out) || "";
      const nightsCount = (data.lead && data.lead.nights) ? data.lead.nights : 1;

      const replaceTags = function (text) {
        if (!text) return "";
        return text
          .replace(/{vendor_name}/g, vendorName)
          .replace(/{guest_name}/g, data.lead.guest_name)
          .replace(/{check_in}/g, checkIn)
          .replace(/{check_out}/g, checkOut)
          .replace(/{nights}/g, nightsCount)
          .replace(/{agency_name}/g, (data.agency && data.agency.name) ? data.agency.name : "Musafir India Trip");
      };

      if (tpl.channel === "whatsapp") {
        $("#bkgWhatsAppContent").val(replaceTags(tpl.body));
        $("#bkgPreviewTabs button[data-preview='whatsapp']").click();
      } else {
        $("#bkgEmailSubject").val(replaceTags(tpl.subject));
        const rendered = replaceTags(tpl.body);
        $("#bkgEmailContent").val(rendered);
        $("#bkgEmailVisualEditor").html(rendered);
        $("#bkgPreviewTabs button[data-preview='email']").click();
      }

      const waLen = ($("#bkgWhatsAppContent").val() || "").length;
      $("#bkgWaCharCount").text(waLen + " characters");
    },

    submitBookingRequest: function () {
      const emailFinalBody = $("#bkgEmailContent").val() || $("#bkgEmailVisualEditor").html();
      const payload = {
        lead_id: $("#bkgModalLeadId").val(),
        vendor_type: $("#bkgVendorType").val(),
        vendor_id: $("#bkgVendorSelect").val(),
        vendor_name: $("#bkgVendorSelect option:selected").text().replace(/-- Choose.*--/, "").trim(),
        vendor_phone: $("#bkgVendorPhone").val(),
        vendor_email: $("#bkgVendorEmail").val(),
        check_in: $("#bkgCheckIn").val(),
        check_out: $("#bkgCheckOut").val(),
        room_category: $("#bkgRoomCategory").val(),
        meal_plan: $("#bkgMealPlan").val(),
        special_notes: $("#bkgSpecialNotes").val(),
        channel: $("input[name='bkgChannel']:checked").val(),
        whatsapp_content: $("#bkgWhatsAppContent").val(),
        email_subject: $("#bkgEmailSubject").val(),
        email_content: emailFinalBody,
        attach_itinerary: $("#bkgOptAttachGuest").is(":checked"),
        notify_ops: $("#bkgOptNotifyOps").is(":checked")
      };

      if (!payload.vendor_name || payload.vendor_name === "") {
        alert("Please select or specify a vendor name.");
        return;
      }

      const $btn = $("#btnConfirmSendBookingRequest");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner fi-spin"></i> Sending Request...');

      $.ajax({
        url: "/api/v3/operations/booking-request/",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(payload),
        success: function (res) {
          if (res && res.status === "success") {
            alert(res.message);
            $("#modalOpsBookingRequest").removeClass("active");
            if (res.whatsapp_link) {
              window.open(res.whatsapp_link, "_blank");
            }
            if ($("#operationsApp").data("page") === "booking-request") {
              window.location.href = "/dashboard/operations/requests/";
            }
          } else {
            alert(res.message || "Failed to dispatch booking request.");
          }
        },
        error: function () {
          alert("Network error while dispatching booking request.");
        },
        complete: function () {
          $btn.prop("disabled", false).html(origHtml);
        }
      });
    },

    // -----------------------------------------------------------------------
    // Confirmation Vouchers & Ref IDs Form
    // -----------------------------------------------------------------------
    openVoucherModal: function (encodedLeadId, cb) {
      const self = this;
      this.closeAllModals();
      $("#voucherLeadId").val(encodedLeadId);

      $.getJSON("/api/v3/operations/voucher/?lead_id=" + encodedLeadId, function (res) {
        if (res && res.status === "success") {
          const guestInitial = (res.guest_name || "G").charAt(0).toUpperCase();
          const paidAmount = Number(res.total_paid || 0);
          const formattedPaid = paidAmount > 0 ? "₹ " + paidAmount.toLocaleString("en-IN") : "₹ 0";
          const isPaid = res.has_payment;

          $("#voucherGuestBanner").html(
            '<div class="ops-voucher-guest-info">' +
              '<div class="ops-guest-avatar">' + guestInitial + '</div>' +
              '<div class="ops-guest-details">' +
                '<h4 class="ops-guest-name">' + (res.guest_name || "Guest") + '</h4>' +
                '<span class="ops-guest-dest"><i class="fi fi-rr-marker text-indigo"></i> ' + (res.destination || "Tour Package") + '</span>' +
              '</div>' +
            '</div>' +
            '<div class="ops-voucher-guest-payment">' +
              '<span class="payment-label">Total Verified Paid</span>' +
              '<span class="payment-value ' + (isPaid ? 'text-emerald' : 'text-slate-600') + '">' + formattedPaid + '</span>' +
              '<span class="ops-voucher-paid-pill ' + (isPaid ? 'paid' : 'pending') + '">' +
                (isPaid ? '<i class="fi fi-rr-badge-check"></i> Advance Verified' : '<i class="fi fi-rr-clock"></i> Payment Pending') +
              '</span>' +
            '</div>'
          );

          const $notice = $("#voucherPaymentNotice");
          if (res.has_payment) {
            $notice.removeClass("warning").addClass("ok").html('<i class="fi fi-rr-check"></i> Advance payment verified. You may proceed to issue official confirmation vouchers.');
            $("#btnConfirmIssueVouchers").prop("disabled", false);
            $("#voucherOptManagerOverride").prop("checked", false);
          } else {
            $notice.removeClass("ok").addClass("warning").html('<i class="fi fi-rr-exclamation"></i> Business Rule Guard: No advance payment verified for this lead. Status 11 (Voucher Issued) normally requires advance payment. Enable Manager Override below if payment was verified offline.');
            const overrideChecked = $("#voucherOptManagerOverride").is(":checked");
            $("#btnConfirmIssueVouchers").prop("disabled", !overrideChecked);
          }

          let itemsHtml = "";
          if (res.bookings && res.bookings.length) {
            res.bookings.forEach(function (b) {
              const isHotel = (b.service || "").toLowerCase() === "hotel";
              itemsHtml += '<div class="ops-voucher-item-card" data-bkg-id="' + b.id + '">' +
                '<div class="ops-voucher-card-top">' +
                  '<div class="ops-vendor-info">' +
                    '<i class="fi fi-rr-' + (isHotel ? "building" : "car") + ' text-indigo"></i> ' +
                    '<strong>' + b.vendor_name + '</strong> ' +
                    '<span class="ops-service-tag">' + (b.service || "SERVICE").toUpperCase() + '</span>' +
                  '</div>' +
                  '<span class="ops-status-badge ' + (b.confirmation_ref ? 'badge-paid' : 'badge-payment-init') + '">' +
                    (b.confirmation_ref ? '<i class="fi fi-rr-check"></i> Ref #' + b.confirmation_ref : 'Pending Ref') +
                  '</span>' +
                '</div>' +
                '<div class="ops-form-row">' +
                  '<div class="v3-form-group">' +
                    '<label class="v3-form-label">Vendor Booking Ref ID *</label>' +
                    '<input type="text" class="v3-form-input v-ref-id" value="' + (b.confirmation_ref || "") + '" placeholder="e.g. HTL-99882">' +
                  '</div>' +
                  '<div class="v3-form-group">' +
                    '<label class="v3-form-label">Driver / Contact Phone</label>' +
                    '<input type="text" class="v3-form-input v-driver-phone" value="' + (b.driver_phone || "") + '" placeholder="e.g. 9876543210">' +
                  '</div>' +
                '</div>' +
              '</div>';
            });
          } else {
            itemsHtml = '<div class="ops-voucher-empty-prompt">' +
              '<i class="fi fi-rr-info text-slate-400 font-20 d-block mb-5"></i>' +
              'No vendor booking requests dispatched yet for this lead.<br>' +
              'You can enter hotel confirmation numbers or driver details directly in the section below to issue vouchers.' +
            '</div>';
          }
          $("#voucherItemsList").html(itemsHtml);

          $("#voucherDirectHotelRef").val("");
          $("#voucherDirectDriverRef").val("");
          $("#voucherOptSendWa").prop("checked", true);

          $("#modalOpsVoucher").addClass("active");
        } else {
          alert(res.message || "Failed to load voucher data.");
        }
      }).fail(function () {
        alert("Failed to connect to operations backend. Please try again.");
      }).always(function () {
        if (typeof cb === "function") cb();
      });
    },

    submitIssueVouchers: function () {
      const self = this;
      const leadId = $("#voucherLeadId").val();
      const bookings = [];

      $(".ops-voucher-item-card, .ops-voucher-row").each(function () {
        const bId = $(this).data("bkg-id");
        const refId = $(this).find(".v-ref-id").val();
        const driverPhone = $(this).find(".v-driver-phone").val();
        if (bId) {
          bookings.push({ id: bId, confirmation_ref: refId, driver_phone: driverPhone });
        }
      });

      const payload = {
        lead_id: leadId,
        bookings: bookings,
        direct_hotel_ref: $("#voucherDirectHotelRef").val().trim(),
        direct_driver_ref: $("#voucherDirectDriverRef").val().trim(),
        send_whatsapp: $("#voucherOptSendWa").is(":checked"),
        force_override: $("#voucherOptManagerOverride").is(":checked")
      };

      const $btn = $("#btnConfirmIssueVouchers");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner fi-spin"></i> Issuing Vouchers...');

      $.ajax({
        url: "/api/v3/operations/voucher/",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(payload),
        success: function (res) {
          if (res && res.status === "success") {
            alert(res.message);
            $("#modalOpsVoucher").removeClass("active");
            if (res.whatsapp_link) {
              if (confirm("Would you like to share the generated voucher to traveler via WhatsApp now?")) {
                window.open(res.whatsapp_link, "_blank");
              }
            }
            if (res.view_url) {
              window.open(res.view_url, "_blank");
            }
            if ($("#operationsApp").data("page") === "voucher") {
              window.location.href = "/dashboard/operations/list/";
            }
            self.loadConfirmedLeads();
            self.loadMetrics();
          } else {
            alert(res.message || "Failed to issue vouchers.");
          }
        },
        error: function () {
          alert("Network error while issuing vouchers. Please try again.");
        },
        complete: function () {
          $btn.prop("disabled", false).html(origHtml);
        }
      });
    },

    // -----------------------------------------------------------------------
    // Invoicing & Proforma Modal
    // -----------------------------------------------------------------------
    openInvoiceModal: function (encodedLeadId, cb) {
      this.closeAllModals();
      $("#invModalLeadId").val(encodedLeadId);

      $.getJSON("/api/v3/operations/invoice/?lead_id=" + encodedLeadId, function (res) {
        if (res && res.status === "success") {
          $("#invNumber").val(res.invoice_number);
          $("#invDate").val(res.date);
          $("#invGuestName").val(res.guest_name);
          $("#invGuestPhone").val(res.guest_phone);

          const pkgCost = parseFloat(res.package_cost) || 0;
          const paidAmt = parseFloat(res.total_paid) || 0;
          const gstAmt = parseFloat(res.gst_amount) || Math.round(pkgCost * 0.05 / 1.05);
          const balDue = Math.max(0, pkgCost - paidAmt);

          $("#invRawPackageTotal").val(pkgCost);
          $("#invPkgTotal").text("₹ " + Number(pkgCost).toLocaleString("en-IN"));
          $("#invGstAmount").text("₹ " + Number(gstAmt).toLocaleString("en-IN"));
          $("#invAdvanceInput").val(paidAmt);
          $("#invAdvancePaid").text("- ₹ " + Number(paidAmt).toLocaleString("en-IN"));
          $("#invBalanceDue").text("₹ " + Number(balDue).toLocaleString("en-IN"));

          // Default selection to Proforma Invoice
          $("input[name='invDocType'][value='proforma']").prop("checked", true);

          $("#modalOpsInvoice").addClass("active");
        } else {
          alert(res.message || "Failed to load invoice calculation.");
        }
      }).fail(function () {
        alert("Failed to connect to operations backend. Please try again.");
      }).always(function () {
        if (typeof cb === "function") cb();
      });
    },

    submitSendInvoice: function () {
      const payload = {
        lead_id: $("#invModalLeadId").val(),
        type: $("input[name='invDocType']:checked").val(),
        invoice_number: $("#invNumber").val(),
        guest_name: $("#invGuestName").val(),
        guest_phone: $("#invGuestPhone").val(),
        total_amount: parseFloat($("#invRawPackageTotal").val()) || 0,
        advance_amount: parseFloat($("#invAdvanceInput").val()) || 0,
        balance_due: parseFloat($("#invBalanceDue").text().replace(/[^0-9.]/g, "")) || 0
      };

      const $btn = $("#btnConfirmSendInvoice");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner fi-spin"></i> Generating Invoice...');

      $.ajax({
        url: "/api/v3/operations/invoice/",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(payload),
        success: function (res) {
          if (res && res.status === "success") {
            alert(res.message);
            $("#modalOpsInvoice").removeClass("active");
            if (res.whatsapp_link) {
              window.open(res.whatsapp_link, "_blank");
            }
            if ($("#operationsApp").data("page") === "invoice") {
              window.location.href = "/dashboard/operations/list/";
            }
          } else {
            alert(res.message || "Failed to dispatch invoice.");
          }
        },
        error: function () {
          alert("Network error while generating invoice.");
        },
        complete: function () {
          $btn.prop("disabled", false).html(origHtml);
        }
      });
    },

    // -----------------------------------------------------------------------
    // Booking Request Templates Manager
    // -----------------------------------------------------------------------
    loadTemplates: function () {
      const self = this;
      const $grid = $("#opsTemplatesGrid");
      $grid.html('<div class="p-30 text-center text-muted"><i class="fi fi-rr-spinner fi-spin"></i> Loading templates...</div>');

      $.getJSON("/api/v3/operations/templates/", function (res) {
        if (res && res.status === "success") {
          self.cachedTemplates = res.templates || [];
          self.renderTemplatesGrid(res.templates || []);
        } else {
          $grid.html('<div class="p-30 text-center text-rose">Failed to load templates.</div>');
        }
      });
    },

    renderTemplatesGrid: function (templates) {
      const $grid = $("#opsTemplatesGrid");
      if (!templates.length) {
        $grid.html('<div class="p-40 text-center text-muted">No templates found. Click "Create Template" to add one.</div>');
        return;
      }

      let html = "";
      templates.forEach(function (t) {
        let vendorIcon = "fi-rr-bed";
        if (t.vendor_type === "transport") vendorIcon = "fi-rr-car";
        if (t.vendor_type === "activity") vendorIcon = "fi-rr-flag";

        const tagMatches = (t.body || "").match(/\{[a-zA-Z0-9_]+\}/g) || [];
        const tagCount = Array.from(new Set(tagMatches)).length;

        // Clean, readable preview excerpt without raw broken table tags
        let previewExcerpt = t.body || "";
        if (t.channel === "email") {
          previewExcerpt = previewExcerpt
            .replace(/<\/(p|div|tr|h[1-6])>/gi, "\n")
            .replace(/<td[^>]*>/gi, " • ")
            .replace(/<[^>]+>/g, "")
            .replace(/\n\s*\n/g, "\n")
            .trim();
        }
        let highlightedPreview = $("<div>").text(previewExcerpt).html();
        highlightedPreview = highlightedPreview.replace(/\{([a-zA-Z0-9_]+)\}/g, '<span class="ops-card-var-tag">{$1}</span>');

        html += '<div class="ops-template-card" data-channel="' + t.channel + '" data-vendor="' + t.vendor_type + '">' +
          '<div class="ops-tpl-card-header">' +
            '<div>' +
              '<div class="ops-tpl-title">' + (t.title || "Untitled Template") + '</div>' +
              '<div class="ops-card-badges">' +
                '<span class="ops-card-badge ' + (t.channel === "whatsapp" ? "badge-wa" : "badge-email") + '">' +
                  '<i class="' + (t.channel === "whatsapp" ? "fi fi-brands-whatsapp" : "fi fi-rr-envelope") + '"></i> ' + t.channel.toUpperCase() +
                '</span>' +
                '<span class="ops-card-badge badge-vendor">' +
                  '<i class="fi ' + vendorIcon + '"></i> ' + t.vendor_type.toUpperCase() +
                '</span>' +
                '<span class="ops-card-badge badge-vars">' +
                  '<i class="fi fi-rr-magic-wand"></i> ' + tagCount + ' vars' +
                '</span>' +
              '</div>' +
            '</div>' +
            (t.is_default
              ? '<span class="ops-card-badge badge-default"><i class="fi fi-rr-star"></i> Default</span>'
              : '<button type="button" class="btn-ops-action btn-set-default-template" data-id="' + t.id + '" title="Set as default template for this channel and vendor"><i class="fi fi-rr-star"></i> Set Default</button>') +
          '</div>' +
          '<div class="ops-tpl-body-preview">' + (highlightedPreview || '<em class="text-muted">No content</em>') + '</div>' +
          '<div class="ops-tpl-footer">' +
            '<span class="ops-tpl-footer-time"><i class="fi fi-rr-clock"></i> ' + (t.updated_at || "Recent") + '</span>' +
            '<div class="ops-action-btn-group">' +
              '<button type="button" class="btn-ops-action btn-edit-template" data-id="' + t.id + '"><i class="fi fi-rr-edit"></i> Edit</button>' +
              '<button type="button" class="btn-ops-action btn-delete-template" data-id="' + t.id + '" title="Delete this template"><i class="fi fi-rr-trash"></i></button>' +
            '</div>' +
          '</div>' +
        '</div>';
      });

      $grid.html(html);
    },

    filterTemplateCards: function () {
      const channel = $("#tplChannelFilter .btn-ops-pill.active").data("channel");
      const vendor = $("#tplVendorFilter .btn-ops-pill.active").data("vendor");

      $(".ops-template-card").each(function () {
        const cMatch = channel === "all" || $(this).data("channel") === channel;
        const vMatch = vendor === "all" || $(this).data("vendor") === vendor;
        $(this).toggle(cMatch && vMatch);
      });
    },

    openTemplateEditorModal: function (tpl) {
      this.closeAllModals();
      this.templatePreviewMode = "sample";
      this.tplEditorViewMode = "rich";
      $("#btnTplModeSample").addClass("active");
      $("#btnTplModeRaw").removeClass("active");

      const channel = tpl ? (tpl.channel || "whatsapp") : "whatsapp";
      const vendor = tpl ? (tpl.vendor_type || "hotel") : "hotel";
      const body = tpl ? (tpl.body || "") : "";

      if (tpl) {
        $("#tplEditorModalTitle").text("Edit Template: " + tpl.title);
        $("#tplEditorId").val(tpl.id);
        $("#tplEditorChannel").val(channel);
        $("#tplEditorVendorType").val(vendor);
        $("#tplEditorTitle").val(tpl.title || "");
        $("#tplEditorSubject").val(tpl.subject || "");
        $("#tplEditorBody").val(body);
        $("#tplEditorIsDefault").prop("checked", tpl.is_default == 1 || tpl.is_default === true);
      } else {
        $("#tplEditorModalTitle").text("Create Request Template");
        $("#tplEditorId").val(0);
        $("#tplEditorChannel").val("whatsapp");
        $("#tplEditorVendorType").val("hotel");
        $("#tplEditorTitle").val("");
        $("#tplEditorSubject").val("");
        $("#tplEditorBody").val("");
        $("#tplEditorIsDefault").prop("checked", false);
      }

      this.loadBodyIntoRichEditor(body, channel);
      $("#tplEditorRichBody").removeClass("d-none");
      $("#tplEditorBody").addClass("d-none");
      $("#btnTplViewRich").addClass("active");
      $("#btnTplViewSource").removeClass("active");

      // Reset merge variable search and ensure all categories and pills are visible
      $("#tplSearchMergeTags").val("");
      $("#tplMergeTagsWrap .ops-tag-pill").show();
      $("#tplMergeTagsWrap .ops-var-cat-section").show();

      this.syncTemplateEditorUI();
      this.updateTemplateLivePreview();

      $("#modalOpsTemplateEditor").addClass("active is-active");
    },

    syncTemplateEditorUI: function () {
      const channel = $("#tplEditorChannel").val() || "whatsapp";
      const vendor = $("#tplEditorVendorType").val() || "hotel";

      // 1. Sync segmented buttons
      $("#tplChannelSegmented .ops-seg-btn").removeClass("active");
      $("#tplChannelSegmented .ops-seg-btn[data-channel='" + channel + "']").addClass("active");

      $("#tplVendorSegmented .ops-seg-btn").removeClass("active");
      $("#tplVendorSegmented .ops-seg-btn[data-vendor='" + vendor + "']").addClass("active");

      // 2. Channel Badges & Subject Field
      if (channel === "email") {
        $("#tplEditorHeaderIconBox").removeClass("icon-emerald").addClass("icon-blue");
        $("#tplEditorHeaderIcon").attr("class", "fi fi-rr-envelope");
        $("#tplEditorChannelBadge").attr("class", "ops-tpl-channel-badge email-badge").html('<i class="fi fi-rr-envelope"></i> <span>Email Mode</span>');
        $("#tplEditorSubjectWrap").removeClass("d-none");
        $("#tplWaPreviewFrame").addClass("d-none");
        $("#tplEmailPreviewFrame").removeClass("d-none");
        $("#tplFormattingTip").html('<i class="fi fi-rr-info"></i> HTML and table tags supported for email templates.');
      } else {
        $("#tplEditorHeaderIconBox").removeClass("icon-blue").addClass("icon-emerald");
        $("#tplEditorHeaderIcon").attr("class", "fi fi-brands-whatsapp");
        $("#tplEditorChannelBadge").attr("class", "ops-tpl-channel-badge wa-badge").html('<i class="fi fi-brands-whatsapp"></i> <span>WhatsApp Mode</span>');
        $("#tplEditorSubjectWrap").addClass("d-none");
        $("#tplWaPreviewFrame").removeClass("d-none");
        $("#tplEmailPreviewFrame").addClass("d-none");
        $("#tplFormattingTip").html('<i class="fi fi-rr-info"></i> WhatsApp formatting: <strong>*bold*</strong>, <em>_italic_</em>, <s>~strike~</s>');
      }

      // 3. Vendor Subtitle & Contact Avatar
      let vendorLabel = "Hotel / Property Reservation";
      let avatarIcon = "🏨";
      let sampleContact = "The Grand Palace Srinagar";
      let sampleStatus = "online • Hotel Reservation Desk";

      if (vendor === "transport") {
        vendorLabel = "Cab / Transporter Logistics";
        avatarIcon = "🚗";
        sampleContact = "Kashmir Wheels Taxi Logistics";
        sampleStatus = "online • Transport Fleet Manager";
      } else if (vendor === "activity") {
        vendorLabel = "Activity & Excursion Booking";
        avatarIcon = "⛵";
        sampleContact = "Gulmarg Adventure Excursions";
        sampleStatus = "online • Excursion Desk";
      }

      $("#tplEditorModalSubtitle").text(vendorLabel);
      $("#tplWaAvatar").text(avatarIcon);
      $("#tplWaContactName").text(sampleContact);
      $("#tplWaContactStatus").text(sampleStatus);
      $("#tplEmailRecipientPreview").text(sampleContact + " <reservations@" + vendor + ".com>");
    },

    getSampleDataMap: function (vendorType) {
      if (vendorType === "transport") {
        return {
          "{vendor_name}": "Kashmir Wheels Taxi Logistics",
          "{vendor_contact}": "+91 94190 12345",
          "{guest_name}": "Rajesh & Priya Sharma",
          "{guest_phone}": "+91 98765 43210",
          "{pax_adults}": "2",
          "{pax_children}": "1",
          "{vehicle_type}": "Innova Crysta (7-Seater AC)",
          "{pickup_location}": "Srinagar International Airport (SXR)",
          "{drop_location}": "The Grand Palace Srinagar",
          "{pickup_date_time}": "24 Dec 2026, 11:30 AM",
          "{check_in}": "24 Dec 2026",
          "{check_out}": "27 Dec 2026",
          "{nights}": "3",
          "{room_category}": "Airport Transfer & Sightseeing",
          "{room_count}": "1",
          "{meal_plan}": "N/A",
          "{special_instructions}": "Luggage: 3 large trolley bags. Hindi/English speaking driver preferred.",
          "{agency_name}": "Aleya Travels",
          "{ops_manager_name}": "Amit Verma",
          "{ops_manager_phone}": "+91 98111 22334"
        };
      }
      if (vendorType === "activity") {
        return {
          "{vendor_name}": "Gulmarg Adventure Excursions",
          "{vendor_contact}": "+91 94191 56789",
          "{guest_name}": "Rajesh & Priya Sharma",
          "{guest_phone}": "+91 98765 43210",
          "{pax_adults}": "2",
          "{pax_children}": "1",
          "{vehicle_type}": "Gondola Phase 1 & 2 + Snowmobile",
          "{pickup_location}": "Gulmarg Base Station",
          "{drop_location}": "Kongdoori Mountain Point",
          "{pickup_date_time}": "25 Dec 2026, 10:00 AM",
          "{check_in}": "25 Dec 2026",
          "{check_out}": "25 Dec 2026",
          "{nights}": "1 Day",
          "{room_category}": "VIP Gondola Phase 1 & 2 Passes",
          "{room_count}": "3 Passes",
          "{meal_plan}": "Hot Kashmiri Kahwa & Snacks Included",
          "{special_instructions}": "Please assign experienced certified guide for snowmobile ride.",
          "{agency_name}": "Aleya Travels",
          "{ops_manager_name}": "Amit Verma",
          "{ops_manager_phone}": "+91 98111 22334"
        };
      }
      // Default: Hotel
      return {
        "{vendor_name}": "The Grand Palace Srinagar",
        "{vendor_contact}": "+91 194 250 1234",
        "{guest_name}": "Rajesh & Priya Sharma",
        "{guest_phone}": "+91 98765 43210",
        "{pax_adults}": "2",
        "{pax_children}": "1",
        "{check_in}": "24 Dec 2026",
        "{check_out}": "27 Dec 2026",
        "{nights}": "3",
        "{room_category}": "Deluxe Valley View Room",
        "{room_count}": "2",
        "{meal_plan}": "MAP (Breakfast + Dinner)",
        "{vehicle_type}": "Innova Crysta",
        "{pickup_location}": "Srinagar Airport (SXR)",
        "{drop_location}": "The Grand Palace Srinagar",
        "{pickup_date_time}": "24 Dec 2026, 11:30 AM",
        "{special_instructions}": "Quiet room on higher floor with valley view. Honeymoon couple.",
        "{agency_name}": "Aleya Travels",
        "{ops_manager_name}": "Amit Verma",
        "{ops_manager_phone}": "+91 98111 22334"
      };
    },

    formatWhatsAppPreview: function (rawText, isSampleMode, sampleMap) {
      if (!rawText) return '<em class="text-muted">Empty message body. Type or click variables on the left to compose.</em>';

      let text = rawText;

      const recognizedTags = [
        "{vendor_name}", "{vendor_contact}", "{guest_name}", "{guest_phone}",
        "{pax_adults}", "{pax_children}", "{check_in}", "{check_out}",
        "{nights}", "{room_category}", "{room_count}", "{meal_plan}",
        "{vehicle_type}", "{pickup_location}", "{drop_location}",
        "{pickup_date_time}", "{special_instructions}", "{agency_name}",
        "{ops_manager_name}", "{ops_manager_phone}"
      ];

      // Escape HTML
      text = text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");

      if (isSampleMode) {
        Object.keys(sampleMap).forEach(function (tag) {
          const regex = new RegExp(tag.replace(/([{}])/g, "\\$1"), "g");
          text = text.replace(regex, '<span class="ops-val-highlight">' + sampleMap[tag] + '</span>');
        });
      } else {
        recognizedTags.forEach(function (tag) {
          const regex = new RegExp(tag.replace(/([{}])/g, "\\$1"), "g");
          text = text.replace(regex, '<span class="ops-tag-raw-highlight">' + tag + '</span>');
        });
      }

      // Highlight any unrecognized tags
      text = text.replace(/\{([a-zA-Z0-9_]+)\}/g, function (match) {
        if (match.indexOf('class="') !== -1) return match;
        return '<span class="text-rose font-weight-bold" title="Unrecognized tag">' + match + '</span>';
      });

      // Parse WhatsApp formatting:
      text = text.replace(/```([^`]+)```/g, '<code>$1</code>');
      text = text.replace(/\*([^\*\n]+)\*/g, '<strong>$1</strong>');
      text = text.replace(/(^|\s)_([^_]+)_(?=\s|$|[.,!?])/g, '$1<em>$2</em>');
      text = text.replace(/~([^~\n]+)~/g, '<del>$1</del>');

      text = text.replace(/\n/g, '<br>');

      return text;
    },

    formatEmailPreview: function (rawBody, rawSubject, isSampleMode, sampleMap) {
      let bodyText = rawBody || "";
      let subjText = rawSubject || "";

      if (isSampleMode) {
        Object.keys(sampleMap).forEach(function (tag) {
          const regex = new RegExp(tag.replace(/([{}])/g, "\\$1"), "g");
          bodyText = bodyText.replace(regex, sampleMap[tag]);
          subjText = subjText.replace(regex, sampleMap[tag]);
        });
      }

      $("#tplEmailSubjectPreview").text(subjText || "No Subject Specified");

      if (!bodyText) {
        return '<p class="text-muted"><em>Template body is empty.</em></p>';
      }

      if (/<[a-z][\s\S]*>/i.test(bodyText)) {
        return bodyText;
      }
      return bodyText.split("\n\n").map(function (para) {
        return '<p>' + para.replace(/\n/g, "<br>") + '</p>';
      }).join("");
    },

    updateTemplateStatsAndDiagnostics: function (rawBody) {
      const chars = (rawBody || "").length;
      const words = (rawBody || "").trim() ? (rawBody || "").trim().split(/\s+/).length : 0;
      
      const tagMatches = (rawBody || "").match(/\{[a-zA-Z0-9_]+\}/g) || [];
      const uniqueVars = Array.from(new Set(tagMatches));

      $("#tplStatChars").text(chars);
      $("#tplStatWords").text(words);
      $("#tplStatVars").text(uniqueVars.length);

      const recognizedTags = [
        "{vendor_name}", "{vendor_contact}", "{guest_name}", "{guest_phone}",
        "{pax_adults}", "{pax_children}", "{check_in}", "{check_out}",
        "{nights}", "{room_category}", "{room_count}", "{meal_plan}",
        "{vehicle_type}", "{pickup_location}", "{drop_location}",
        "{pickup_date_time}", "{special_instructions}", "{agency_name}",
        "{ops_manager_name}", "{ops_manager_phone}"
      ];

      const invalidTags = uniqueVars.filter(function (t) {
        return recognizedTags.indexOf(t) === -1;
      });

      if (invalidTags.length > 0) {
        $("#tplDiagStatus").removeClass("valid").addClass("warning");
        $("#tplDiagStatus i").attr("class", "fi fi-rr-exclamation");
        $("#tplDiagText").html('<strong>Notice:</strong> Unknown variable ' + invalidTags.join(", "));
        $("#tplDiagCount").text(uniqueVars.length + ' tags detected (' + invalidTags.length + ' unrecognized)');
      } else {
        $("#tplDiagStatus").removeClass("warning").addClass("valid");
        $("#tplDiagStatus i").attr("class", "fi fi-rr-shield-check");
        $("#tplDiagText").text("All " + uniqueVars.length + " dynamic variables verified & ready for dispatch");
        $("#tplDiagCount").text(uniqueVars.length > 0 ? "✓ Ready" : "No variables used");
      }
    },

    updateTemplateLivePreview: function () {
      const channel = $("#tplEditorChannel").val() || "whatsapp";
      const vendor = $("#tplEditorVendorType").val() || "hotel";
      const body = $("#tplEditorBody").val() || "";
      const subject = $("#tplEditorSubject").val() || "";
      const isSample = (this.templatePreviewMode || "sample") === "sample";

      const sampleMap = this.getSampleDataMap(vendor);

      if (channel === "email") {
        const renderedEmail = this.formatEmailPreview(body, subject, isSample, sampleMap);
        $("#tplEmailBodyContent").html(renderedEmail);
      } else {
        const renderedWa = this.formatWhatsAppPreview(body, isSample, sampleMap);
        $("#tplWaBubbleContent").html(renderedWa);
      }

      this.updateTemplateStatsAndDiagnostics(body);
    },

    restoreTemplatePreset: function (channel, vendor) {
      if (channel === "whatsapp" && vendor === "hotel") {
        $("#tplEditorTitle").val("Hotel Booking Request (Standard)");
        $("#tplEditorBody").val("Dear *{vendor_name}*,\n\nGreetings from *{agency_name}*!\n\nKindly confirm availability and reserve room(s) as per following booking request:\n\n🏨 *Property:* {vendor_name}\n👤 *Guest Name:* {guest_name} ({pax_adults} Adults, {pax_children} Children)\n📅 *Check-In:* {check_in}\n📅 *Check-Out:* {check_out} ({nights} Nights)\n🛏️ *Room Type:* {room_category} ({room_count} Rooms)\n🍽️ *Meal Plan:* {meal_plan}\n\n📌 *Special Remarks:* {special_instructions}\n\nPlease share the Confirmation / Booking Reference ID at your earliest.\n\nThank you,\n*{ops_manager_name}* | Operations Desk\n📱 {ops_manager_phone}");
      } else if (channel === "email" && vendor === "hotel") {
        $("#tplEditorTitle").val("Formal Hotel Reservation Email");
        $("#tplEditorSubject").val("Booking Request: {guest_name} - {nights}N - Check-in {check_in} - {agency_name}");
        $("#tplEditorBody").val('<p>Dear Reservations Team (<strong>{vendor_name}</strong>),</p><p>Warm greetings from <strong>{agency_name}</strong>.</p><p>Please find our booking request below and confirm with your reservation reference number:</p><table style="width:100%; border-collapse:collapse; margin:16px 0; font-size:13px;"><tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;">Primary Guest</td><td style="padding:8px; border:1px solid #e2e8f0;">{guest_name} ({pax_adults} Adults, {pax_children} Kids)</td></tr><tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;">Check-In / Out</td><td style="padding:8px; border:1px solid #e2e8f0;">{check_in} to {check_out} ({nights} Nights)</td></tr><tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;">Rooms & Category</td><td style="padding:8px; border:1px solid #e2e8f0;">{room_count} x {room_category}</td></tr><tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;">Meal Plan</td><td style="padding:8px; border:1px solid #e2e8f0;">{meal_plan}</td></tr><tr><td style="padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;">Special Notes</td><td style="padding:8px; border:1px solid #e2e8f0;">{special_instructions}</td></tr></table><p>Kindly acknowledge receipt and confirm by replying to this email.</p><p>Best regards,<br><strong>{ops_manager_name}</strong><br>Operations Desk | {agency_name}<br>Contact: {ops_manager_phone}</p>');
      } else if (channel === "whatsapp" && vendor === "transport") {
        $("#tplEditorTitle").val("Cab / Vehicle Booking Request");
        $("#tplEditorBody").val("Hello *{vendor_name}*,\n\nNew cab booking request from *{agency_name}*:\n\n🚗 *Vehicle Required:* {vehicle_type}\n👤 *Guest Name:* {guest_name}\n📱 *Guest Contact:* {guest_phone}\n👥 *Total Pax:* {pax_adults} Adults, {pax_children} Kids\n📍 *Pickup Location:* {pickup_location}\n📍 *Drop Location:* {drop_location}\n⏰ *Pickup Date & Time:* {pickup_date_time}\n\n📌 *Notes:* {special_instructions}\n\nPlease allocate driver and share vehicle number & driver contact.\n\nRegards,\n*{ops_manager_name}* | {ops_manager_phone}");
      } else if (channel === "whatsapp" && vendor === "activity") {
        $("#tplEditorTitle").val("Activity & Guide Slot Booking");
        $("#tplEditorBody").val("Dear *{vendor_name}*,\n\nPlease block the following excursion slot for our guests:\n\n🎯 *Activity/Tour:* {room_category}\n👤 *Guest:* {guest_name} ({pax_adults} Adults, {pax_children} Kids)\n📅 *Date:* {check_in}\n⏰ *Slot Time:* {pickup_date_time}\n\nKindly confirm slot availability.\n\n*{agency_name}* Operations Desk");
      }
      this.tplEditorViewMode = "rich";
      this.loadBodyIntoRichEditor($("#tplEditorBody").val(), channel);
      $("#tplEditorRichBody").removeClass("d-none");
      $("#tplEditorBody").addClass("d-none");
      $("#btnTplViewRich").addClass("active");
      $("#btnTplViewSource").removeClass("active");

      this.syncTemplateEditorUI();
      this.updateTemplateLivePreview();
    },

    switchTemplateEditorView: function (mode) {
      this.tplEditorViewMode = mode;
      const channel = $("#tplEditorChannel").val() || "whatsapp";
      const $rich = $("#tplEditorRichBody");
      const $source = $("#tplEditorBody");

      if (mode === "rich") {
        $("#btnTplViewRich").addClass("active");
        $("#btnTplViewSource").removeClass("active");
        this.loadBodyIntoRichEditor($source.val(), channel);
        $rich.removeClass("d-none");
        $source.addClass("d-none");
      } else {
        $("#btnTplViewSource").addClass("active");
        $("#btnTplViewRich").removeClass("active");
        if (channel === "email") {
          $source.val($rich.html());
        } else {
          $source.val(this.richHtmlToWaText($rich[0]));
        }
        $source.removeClass("d-none");
        $rich.addClass("d-none");
      }
    },

    loadBodyIntoRichEditor: function (body, channel) {
      const $rich = $("#tplEditorRichBody");
      if (channel === "email") {
        $rich.removeClass("wa-mode");
        if (/<[a-z][\s\S]*>/i.test(body || "")) {
          $rich.html(body || "");
        } else if (body) {
          $rich.html((body || "").split("\n\n").map(function (p) {
            return "<p>" + p.replace(/\n/g, "<br>") + "</p>";
          }).join(""));
        } else {
          $rich.html("");
        }
      } else {
        $rich.addClass("wa-mode");
        $rich.html(this.waTextToRichHtml(body || ""));
      }
    },

    waTextToRichHtml: function (text) {
      if (!text) return "";
      let escaped = $("<div>").text(text).html();
      // *bold* -> <strong>$1</strong>
      escaped = escaped.replace(/\*([^*\n\r]+)\*/g, "<strong>$1</strong>");
      // _italic_ -> <em>$1</em>
      escaped = escaped.replace(/_([^_\n\r]+)_/g, "<em>$1</em>");
      // ~strike~ -> <del>$1</del>
      escaped = escaped.replace(/~([^~\n\r]+)~/g, "<del>$1</del>");
      // Newlines -> <br>
      escaped = escaped.replace(/\r?\n/g, "<br>");
      return escaped;
    },

    richHtmlToWaText: function (element) {
      if (!element) return "";
      const clone = element.cloneNode(true);
      const $clone = $(clone);

      $clone.find("br").replaceWith("\n");
      $clone.find("p, div, li, tr").each(function () {
        $(this).append("\n");
      });

      $clone.find("strong, b").each(function () {
        const t = $(this).text();
        $(this).replaceWith("*" + t + "*");
      });
      $clone.find("em, i").each(function () {
        const t = $(this).text();
        $(this).replaceWith("_" + t + "_");
      });
      $clone.find("del, s").each(function () {
        const t = $(this).text();
        $(this).replaceWith("~" + t + "~");
      });

      let raw = clone.textContent || "";
      raw = raw.replace(/\r\n/g, "\n");
      return raw;
    },

    insertIntoEditor: function (text, htmlIfRich) {
      if (this.tplEditorViewMode === "rich") {
        const editor = document.getElementById("tplEditorRichBody");
        if (!editor) return;
        editor.focus();
        const sel = window.getSelection();
        const content = htmlIfRich !== undefined ? htmlIfRich : text;
        if (sel && sel.rangeCount > 0) {
          const range = sel.getRangeAt(0);
          if (editor.contains(range.commonAncestorContainer)) {
            range.deleteContents();
            const el = document.createElement("div");
            el.innerHTML = content;
            const frag = document.createDocumentFragment();
            let node, lastNode;
            while ((node = el.firstChild)) {
              lastNode = frag.appendChild(node);
            }
            range.insertNode(frag);
            if (lastNode) {
              range.setStartAfter(lastNode);
              range.collapse(true);
              sel.removeAllRanges();
              sel.addRange(range);
            }
            $("#tplEditorRichBody").trigger("input");
            return;
          }
        }
        editor.innerHTML += content;
        $("#tplEditorRichBody").trigger("input");
      } else {
        const textarea = document.getElementById("tplEditorBody");
        if (!textarea) return;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const val = textarea.value;
        textarea.value = val.substring(0, start) + text + val.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + text.length;
        $("#tplEditorBody").trigger("input");
      }
    },

    formatEditorSelection: function (cmd, prefix, suffix, placeholder) {
      if (this.tplEditorViewMode === "rich") {
        const editor = document.getElementById("tplEditorRichBody");
        if (!editor) return;
        editor.focus();
        if (cmd === "bold") {
          document.execCommand("bold", false, null);
        } else if (cmd === "italic") {
          document.execCommand("italic", false, null);
        } else if (cmd === "strike") {
          document.execCommand("strikeThrough", false, null);
        } else if (cmd === "code") {
          document.execCommand("fontName", false, "monospace");
        } else if (cmd === "bullet") {
          const channel = $("#tplEditorChannel").val() || "whatsapp";
          if (channel === "email") {
            document.execCommand("insertUnorderedList", false, null);
          } else {
            this.insertIntoEditor("\n• ", "<br>• ");
          }
        }
        $("#tplEditorRichBody").trigger("input");
      } else {
        const textarea = document.getElementById("tplEditorBody");
        if (!textarea) return;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selText = textarea.value.substring(start, end) || placeholder;
        const replacement = prefix + selText + suffix;
        textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
        textarea.focus();
        textarea.selectionStart = start + prefix.length;
        textarea.selectionEnd = start + prefix.length + selText.length;
        $("#tplEditorBody").trigger("input");
      }
    },

    saveTemplate: function () {
      const self = this;
      if (this.tplEditorViewMode === "rich") {
        const channel = $("#tplEditorChannel").val() || "whatsapp";
        const $rich = $("#tplEditorRichBody");
        if (channel === "email") {
          $("#tplEditorBody").val($rich.html());
        } else {
          $("#tplEditorBody").val(this.richHtmlToWaText($rich[0]));
        }
      }
      const payload = {
        action: "save",
        id: $("#tplEditorId").val(),
        channel: $("#tplEditorChannel").val(),
        vendor_type: $("#tplEditorVendorType").val(),
        title: $("#tplEditorTitle").val(),
        subject: $("#tplEditorSubject").val(),
        body: $("#tplEditorBody").val(),
        is_default: $("#tplEditorIsDefault").is(":checked") ? 1 : 0
      };

      if (!payload.title || !payload.body) {
        alert("Please enter template title and body.");
        return;
      }

      $.ajax({
        url: "/api/v3/operations/templates/",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(payload),
        success: function (res) {
          if (res && res.status === "success") {
            alert(res.message);
            $("#modalOpsTemplateEditor").removeClass("active is-active");
            if ($("#operationsApp").data("page") === "template-add") {
              window.location.href = "/dashboard/operations/templates/";
            }
            self.loadTemplates();
          } else {
            alert(res.message || "Failed to save template.");
          }
        }
      });
    },

    deleteTemplate: function (templateId) {
      const self = this;
      $.ajax({
        url: "/api/v3/operations/templates/",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ action: "delete", id: Number(templateId) }),
        success: function (res) {
          if (res && res.status === "success") {
            const swalObj = typeof window.Swal !== "undefined" ? window.Swal : (typeof Swal !== "undefined" ? Swal : null);
            if (swalObj && typeof swalObj.fire === "function") {
              swalObj.fire({
                title: "Deleted!",
                text: res.message || "Template deleted successfully.",
                icon: "success",
                timer: 1600,
                showConfirmButton: false
              });
            } else if (typeof window.swal === "function" || typeof swal === "function") {
              const swalFn = typeof window.swal === "function" ? window.swal : swal;
              swalFn("Deleted!", res.message || "Template deleted successfully.", "success");
            } else {
              alert(res.message || "Template removed successfully.");
            }
            self.loadTemplates();
          } else {
            const msg = res && res.message ? res.message : "Failed to delete template.";
            const swalObj = typeof window.Swal !== "undefined" ? window.Swal : (typeof Swal !== "undefined" ? Swal : null);
            if (swalObj && typeof swalObj.fire === "function") {
              swalObj.fire("Error", msg, "error");
            } else if (typeof window.swal === "function" || typeof swal === "function") {
              const swalFn = typeof window.swal === "function" ? window.swal : swal;
              swalFn("Error", msg, "error");
            } else {
              alert(msg);
            }
          }
        },
        error: function () {
          const swalObj = typeof window.Swal !== "undefined" ? window.Swal : (typeof Swal !== "undefined" ? Swal : null);
          if (swalObj && typeof swalObj.fire === "function") {
            swalObj.fire("Error", "Error communicating with server while deleting template.", "error");
          } else {
            alert("Error communicating with server while deleting template.");
          }
        }
      });
    },

    setDefaultTemplate: function (templateId) {
      const self = this;
      $.ajax({
        url: "/api/v3/operations/templates/",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ action: "set_default", id: templateId }),
        success: function (res) {
          if (res && res.status === "success") {
            self.loadTemplates();
          }
        }
      });
    },

    loadDefaultBookingDependencies: function () {
      const self = this;
      $.getJSON("/api/v3/operations/booking-request/", function (res) {
        if (res && res.status === "success") {
          self.cachedProperties = res.properties || [];
          self.cachedTransporters = res.transporters || [];
          self.cachedTemplates = res.templates || [];
          self.populateVendorDropdown("hotel");
          self.populateTemplateDropdown("hotel");
        }
      });
    },

    loadSingleTemplate: function (templateId) {
      const self = this;
      $.getJSON("/api/v3/operations/templates/", function (res) {
        if (res && res.status === "success" && res.templates) {
          self.cachedTemplates = res.templates;
          const tpl = res.templates.find(function (t) { return Number(t.id) === Number(templateId); });
          if (tpl) {
            self.openTemplateEditorModal(tpl);
          }
        }
      });
    },

    // -----------------------------------------------------------------------
    // Booking Requests Ledger
    // -----------------------------------------------------------------------
    loadBookingRequests: function () {
      const self = this;
      const $tbody = $("#opsRequestsTableBody");
      $tbody.html('<tr><td colspan="8" class="text-center p-30 text-muted"><i class="fi fi-rr-spinner fi-spin"></i> Fetching booking requests ledger...</td></tr>');

      let url = "/api/v3/operations/booking-request/?action=ledger&page=" + this.requestsPage + "&limit=" + this.requestsLimit;
      if (this.requestsVendor !== "all") url += "&vendor_type=" + this.requestsVendor;
      if (this.requestsStatus !== "") url += "&status=" + this.requestsStatus;
      if (this.requestsSearch !== "") url += "&search=" + encodeURIComponent(this.requestsSearch);

      $.getJSON(url, function (res) {
        if (res && res.status === "success") {
          self.renderBookingRequestsTable(res.requests || []);
          self.renderRequestsPagination(res.total || 0, res.page || 1, res.limit || 25);
          $("#badgeRequestsCount").text(res.total || 0);
        } else {
          $tbody.html('<tr><td colspan="8" class="text-center p-30 text-rose">Failed to load booking requests.</td></tr>');
        }
      });
    },

    renderBookingRequestsTable: function (requests) {
      const $tbody = $("#opsRequestsTableBody");
      if (!requests.length) {
        $tbody.html('<tr><td colspan="8" class="text-center p-40 text-muted"><i class="fi fi-rr-paper-plane font-32 text-slate-400 d-block mb-10"></i>No dispatched vendor booking requests found.</td></tr>');
        return;
      }

      let html = "";
      requests.forEach(function (r) {
        const leadLink = "/dashboard/leads/view/?id=" + r.encoded_lead_id;

        html += '<tr>' +
          '<td>' +
            '<div class="ops-lead-meta">' +
              '<span class="ops-lead-title">' + r.vendor_name + '</span>' +
              '<div class="ops-lead-sub">' +
                '<span><i class="fi fi-rr-phone-call"></i> ' + (r.vendor_phone || "N/A") + '</span>' +
                '<span>Booking #' + r.id + '</span>' +
              '</div>' +
            '</div>' +
          '</td>' +
          '<td><span class="ops-status-badge badge-payment-init">' + r.service.toUpperCase() + '</span></td>' +
          '<td>' +
            '<a href="' + leadLink + '"><strong>' + r.guest_name + '</strong></a>' +
            '<div class="text-muted font-11">Lead #' + r.lead_id + ' &bull; ' + (r.destination || "") + '</div>' +
          '</td>' +
          '<td>' +
            '<div>' + (r.check_in || "N/A") + ' to ' + (r.check_out || "N/A") + '</div>' +
          '</td>' +
          '<td>' + (r.room_category || "Standard") + (r.meal_plan ? ' (' + r.meal_plan + ')' : '') + '</td>' +
          '<td>' +
            (r.confirmation_ref ? '<strong class="text-emerald">' + r.confirmation_ref + '</strong>' : '<span class="text-muted font-11">Awaiting Ref ID</span>') +
          '</td>' +
          '<td><span class="ops-status-badge ' + r.status_badge + '">' + r.status_label + '</span></td>' +
          '<td class="text-right">' +
            '<div class="ops-action-btn-group">' +
              '<button type="button" class="btn-ops-action emerald btn-quick-record-response" data-id="' + r.id + '" data-status="' + r.status + '" data-ref="' + (r.confirmation_ref || "") + '" title="Record Vendor Confirmation Ref ID"><i class="fi fi-rr-check"></i> <span class="btn-action-text">Response</span></button>' +
              '<button type="button" class="btn-ops-action primary btn-resend-booking-request" data-lead="' + r.encoded_lead_id + '" title="Resend Booking Request"><i class="fi fi-rr-refresh"></i> <span class="btn-action-text">Resend</span></button>' +
              '<a href="' + leadLink + '" class="btn-ops-action neutral btn-icon-only" title="View Lead Record"><i class="fi fi-rr-eye"></i></a>' +
            '</div>' +
          '</td>' +
        '</tr>';
      });

      $tbody.html(html);
    },

    renderRequestsPagination: function (total, page, limit) {
      const totalPages = Math.ceil(total / limit);
      const start = total === 0 ? 0 : (page - 1) * limit + 1;
      const end = Math.min(page * limit, total);

      $("#opsRequestsPaginationInfo").text("Showing " + start + " - " + end + " of " + total + " requests");

      let btns = "";
      if (totalPages > 1) {
        for (let i = 1; i <= totalPages; i++) {
          btns += '<button type="button" class="btn-ops-page ' + (i === page ? 'active' : '') + '" data-page="' + i + '">' + i + '</button>';
        }
      }
      $("#opsRequestsPaginationControls").html(btns);
    },

    openQuickBookingStatusModal: function (bookingId, currentStatus, currentRef) {
      this.closeAllModals();
      $("#quickBookingId").val(bookingId);
      $("#quickBookingStatus").val(currentStatus >= 0 ? currentStatus : 1);
      $("#quickBookingRefId").val(currentRef || "");
      $("#modalOpsUpdateBookingStatus").addClass("active");
    },

    submitQuickBookingStatus: function () {
      const self = this;
      const bookingId = $("#quickBookingId").val();
      const status = $("#quickBookingStatus").val();
      const refId = $("#quickBookingRefId").val();

      $.ajax({
        url: "/api/v3/operations/booking-request/",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ action: "update_status", booking_id: bookingId, status: status, confirmation_ref: refId }),
        success: function (res) {
          if (res && res.status === "success") {
            alert(res.message);
            $("#modalOpsUpdateBookingStatus").removeClass("active");
            self.loadBookingRequests();
          } else {
            alert(res.message || "Failed to update booking status.");
          }
        }
      });
    }
  };

  window.V3OperationsHub = OperationsHub;

  // 1. Initial Page Load
  $(document).ready(function () {
    if ($("#operationsApp").length) {
      OperationsHub.init();
    }
  });

  // 2. SPA Navigation Event (app.js triggers 'spa:loaded' on document)
  $(document).off("spa:loaded.v3Operations").on("spa:loaded.v3Operations", function () {
    if ($("#operationsApp").length) {
      OperationsHub.init();
    }
  });

  document.addEventListener("spa:loaded", function () {
    if ($("#operationsApp").length) {
      OperationsHub.init();
    }
  });

  // 3. Browser History popstate
  window.addEventListener("popstate", function () {
    if ($("#operationsApp").length) {
      setTimeout(function () {
        OperationsHub.init();
      }, 80);
    }
  });
})(window, document, window.jQuery || window.$);
