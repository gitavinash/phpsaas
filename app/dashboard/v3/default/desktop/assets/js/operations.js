/**
 * Operations Management Hub JavaScript Controller
 * File: app/dashboard/v3/default/desktop/assets/js/operations.js
 * SPA functionality for Confirmed Leads, Trip Planner, Booking Requests, Vouchers, Invoices, and Templates
 */

$(document).ready(function () {
  if (!$("#operationsApp").length) return;

  const OperationsHub = {
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
      this.bindEvents();
      this.handleHashRouting();
      this.loadMetrics();
      this.loadAgents();
      this.loadConfirmedLeads();
      this.handleDirectQueryActions();
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
      const self = this;

      // 1. Navigation Tab Switching
      $("#opsNavTabs").on("click", ".btn-date-preset", function () {
        const tab = $(this).data("tab");
        self.switchTab(tab);
      });

      // 2. Hash Change Handler
      $(window).on("hashchange", function () {
        self.handleHashRouting();
      });

      // 3. Refresh Button
      $("#btnRefreshOps").on("click", function () {
        self.loadMetrics();
        if (self.currentTab === "confirmed") self.loadConfirmedLeads();
        else if (self.currentTab === "trips") self.loadTripsBoard();
        else if (self.currentTab === "requests") self.loadBookingRequests();
        else if (self.currentTab === "unassigned") self.loadUnassignedLeads();
        else if (self.currentTab === "templates") self.loadTemplates();
      });

      // 4. Confirmed Leads Status Filter
      $("#confirmedStatusFilter").on("click", ".btn-ops-pill", function () {
        $("#confirmedStatusFilter .btn-ops-pill").removeClass("active");
        $(this).addClass("active");
        self.confirmedStatus = $(this).data("status");
        self.confirmedPage = 1;
        self.loadConfirmedLeads();
      });

      // 5. Confirmed Leads Search (Debounced)
      let searchTimer = null;
      $("#opsLeadSearchInput").on("input", function () {
        clearTimeout(searchTimer);
        const val = $(this).val().trim();
        searchTimer = setTimeout(function () {
          self.confirmedSearch = val;
          self.confirmedPage = 1;
          self.loadConfirmedLeads();
        }, 350);
      });

      // 6. Confirmed Pagination Click
      $("#opsPaginationControls").on("click", ".btn-ops-page", function () {
        const p = parseInt($(this).data("page"), 10);
        if (p > 0 && p !== self.confirmedPage) {
          self.confirmedPage = p;
          self.loadConfirmedLeads();
        }
      });

      // 7. Trip Planner Date Filters
      $("#opsTripDateInput").on("change", function () {
        self.tripDate = $(this).val();
        self.loadTripsBoard();
      });

      $("#btnTripDateToday").on("click", function () {
        const today = new Date().toISOString().split("T")[0];
        $("#opsTripDateInput").val(today);
        self.tripDate = today;
        $("#btnTripDateTomorrow").removeClass("active");
        $(this).addClass("active");
        self.loadTripsBoard();
      });

      $("#btnTripDateTomorrow").on("click", function () {
        const tomorrow = new Date(Date.now() + 86400000).toISOString().split("T")[0];
        $("#opsTripDateInput").val(tomorrow);
        self.tripDate = tomorrow;
        $("#btnTripDateToday").removeClass("active");
        $(this).addClass("active");
        self.loadTripsBoard();
      });

      // 8. Unassigned Checkbox Selection
      $("#chkSelectAllUnassigned").on("change", function () {
        const checked = $(this).is(":checked");
        $(".chk-unassigned-lead").prop("checked", checked);
        self.updateSelectedLeadsCount();
      });

      $(document).on("change", ".chk-unassigned-lead", function () {
        self.updateSelectedLeadsCount();
      });

      $("#opsUnassignedSearchInput").on("input", function () {
        const val = $(this).val().toLowerCase();
        $("#opsUnassignedTableBody tr").each(function () {
          const rowText = $(this).text().toLowerCase();
          $(this).toggle(rowText.indexOf(val) > -1);
        });
      });

      // 9. Template Channels & Vendor Filter
      $("#tplChannelFilter").on("click", ".btn-ops-pill", function () {
        $("#tplChannelFilter .btn-ops-pill").removeClass("active");
        $(this).addClass("active");
        self.filterTemplateCards();
      });

      $("#tplVendorFilter").on("click", ".btn-ops-pill", function () {
        $("#tplVendorFilter .btn-ops-pill").removeClass("active");
        $(this).addClass("active");
        self.filterTemplateCards();
      });

      // 10. Open Booking Request Modal
      $(document).on("click", ".btn-open-booking-modal", function () {
        const leadId = $(this).data("id");
        self.openBookingRequestModal(leadId);
      });

      // 11. Vendor Type Change inside Booking Request Modal
      $("#bkgVendorType").on("change", function () {
        self.populateVendorDropdown($(this).val());
        self.updateLivePreviewContent();
      });

      $("#bkgVendorSelect").on("change", function () {
        const vId = $(this).val();
        const vType = $("#bkgVendorType").val();
        self.fillSelectedVendorContact(vType, vId);
        self.updateLivePreviewContent();
      });

      // 12. Booking Preview Tab Switches
      $("#bkgPreviewTabs").on("click", ".btn-ops-preview-tab", function () {
        $("#bkgPreviewTabs .btn-ops-preview-tab").removeClass("active");
        $(this).addClass("active");
        const preview = $(this).data("preview");
        $(".ops-preview-box").removeClass("active");
        if (preview === "whatsapp") $("#previewBoxWhatsApp").addClass("active");
        else $("#previewBoxEmail").addClass("active");
      });

      // 13. Template Picker Change inside Booking Request Modal
      $("#bkgTemplatePicker").on("change", function () {
        const tId = $(this).val();
        self.applyTemplateToPreview(tId);
      });

      // 14. Send Booking Request Submit
      $("#btnConfirmSendBookingRequest").on("click", function () {
        self.submitBookingRequest();
      });

      // 15. Open Confirmation Voucher Modal
      $(document).on("click", ".btn-open-voucher-modal", function () {
        const leadId = $(this).data("id");
        self.openVoucherModal(leadId);
      });

      // 16. Save Confirmation Vouchers & Issue
      $("#btnConfirmIssueVouchers").on("click", function () {
        self.submitIssueVouchers();
      });

      // 17. Open Invoice / Proforma Modal
      $(document).on("click", ".btn-open-invoice-modal", function () {
        const leadId = $(this).data("id");
        self.openInvoiceModal(leadId);
      });

      // 18. Send Invoice Submit
      $("#btnConfirmSendInvoice").on("click", function () {
        self.submitSendInvoice();
      });

      // 19. Open Assign / Reassign Modal
      $(document).on("click", ".btn-open-assign-modal", function () {
        const leadId = $(this).data("id");
        const title = $(this).data("title") || "Assign Lead";
        self.openAssignModal([leadId], title);
      });

      $("#btnOpenBulkAssignModal").on("click", function () {
        if (!self.selectedLeadIds.length) return;
        self.openAssignModal(self.selectedLeadIds, "Bulk Assign Leads (" + self.selectedLeadIds.length + ")");
      });

      $("#btnConfirmAssignLead").on("click", function () {
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
        $("#dispatchLeadId").val(leadId);
        $("#dispatchDriverName").val($(this).data("driver") || "");
        $("#dispatchDriverPhone").val($(this).data("phone") || "");
        $("#dispatchVehicleNo").val($(this).data("veh") || "");
        $("#modalOpsDriverDispatch").addClass("active");
      });

      $("#btnConfirmDriverDispatch").on("click", function () {
        self.submitDriverDispatch();
      });

      // 21. Feedback Trigger Button
      $(document).on("click", ".btn-trigger-feedback", function () {
        const leadId = $(this).data("id");
        self.triggerFeedbackRequest(leadId);
      });

      // 22. Template Manager Modal Trigger & Save
      $("#btnOpenNewTemplateModal").on("click", function () {
        self.openTemplateEditorModal(null);
      });

      $(document).on("click", ".btn-edit-template", function () {
        const tId = $(this).data("id");
        const tpl = self.cachedTemplates.find(function (t) { return t.id === tId; });
        if (tpl) self.openTemplateEditorModal(tpl);
      });

      $(document).on("click", ".btn-delete-template", function () {
        const tId = $(this).data("id");
        if (confirm("Are you sure you want to delete this template?")) {
          self.deleteTemplate(tId);
        }
      });

      $(document).on("click", ".btn-set-default-template", function () {
        const tId = $(this).data("id");
        self.setDefaultTemplate(tId);
      });

      $("#btnConfirmSaveTemplate").on("click", function () {
        self.saveTemplate();
      });

      // 23. Inserter pills for Merge Tags in Template Editor
      $("#tplMergeTagsWrap").on("click", ".ops-tag-pill", function () {
        const tag = $(this).data("tag");
        const textarea = document.getElementById("tplEditorBody");
        if (!textarea) return;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + tag + text.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + tag.length;
      });

      // 24. Booking Requests Ledger Filter & Pagination
      $("#bkgLedgerVendorFilter").on("click", ".btn-ops-pill", function () {
        $("#bkgLedgerVendorFilter .btn-ops-pill").removeClass("active");
        $(this).addClass("active");
        self.requestsVendor = $(this).data("vendor");
        self.requestsPage = 1;
        self.loadBookingRequests();
      });

      $("#bkgLedgerStatusFilter").on("click", ".btn-ops-pill", function () {
        $("#bkgLedgerStatusFilter .btn-ops-pill").removeClass("active");
        $(this).addClass("active");
        self.requestsStatus = $(this).data("status");
        self.requestsPage = 1;
        self.loadBookingRequests();
      });

      let reqSearchTimer = null;
      $("#opsRequestsSearchInput").on("input", function () {
        clearTimeout(reqSearchTimer);
        const val = $(this).val().trim();
        reqSearchTimer = setTimeout(function () {
          self.requestsSearch = val;
          self.requestsPage = 1;
          self.loadBookingRequests();
        }, 350);
      });

      $("#opsRequestsPaginationControls").on("click", ".btn-ops-page", function () {
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

      $("#btnConfirmQuickBookingUpdate").on("click", function () {
        self.submitQuickBookingStatus();
      });

      $(document).on("click", ".btn-resend-booking-request", function () {
        const leadId = $(this).data("lead");
        self.openBookingRequestModal(leadId);
      });

      // 25. Modal Close Triggers
      $(document).on("click", ".v3-modal-close-trigger", function () {
        $(this).closest(".v3-modal-overlay").removeClass("active");
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
      $.getJSON("/api/v3/operations/leads.php?action=metrics", function (res) {
        if (res && res.status === "success" && res.metrics) {
          const m = res.metrics;
          $("#statConfirmedTotal").text(m.confirmed_total || 0);
          $("#statQuoteApproved").text(m.quotation_approved || 0);
          $("#statPaidBookings").text(m.payments_recorded || 0);
          $("#statVouchersIssued").text(m.vouchers_issued || 0);
          $("#statTripsInProgress").text(m.trips_in_progress || 0);

          $("#badgeConfirmedCount").text(m.confirmed_total || 0);
          $("#badgeTripsCount").text(m.trips_in_progress || 0);
          $("#badgeUnassignedCount").text(m.unassigned_count || 0);
        }
      });
    },

    loadAgents: function () {
      const self = this;
      $.getJSON("/api/v3/operations/assign.php", function (res) {
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

      let url = "/api/v3/operations/leads.php?type=confirmed&page=" + this.confirmedPage + "&limit=" + this.confirmedLimit;
      if (this.confirmedStatus !== "") url += "&status=" + this.confirmedStatus;
      if (this.confirmedSearch !== "") url += "&search=" + encodeURIComponent(this.confirmedSearch);

      $.getJSON(url, function (res) {
        if (res && res.status === "success") {
          self.renderConfirmedTable(res.leads || []);
          self.renderPagination(res.total || 0, res.page || 1, res.limit || 20);
        } else {
          $tbody.html('<tr><td colspan="8" class="text-center p-30 text-rose">Failed to load confirmed leads.</td></tr>');
        }
      }).fail(function () {
        $tbody.html('<tr><td colspan="8" class="text-center p-30 text-rose">Network error while fetching leads.</td></tr>');
      });
    },

    renderConfirmedTable: function (leads) {
      const $tbody = $("#opsConfirmedTableBody");
      if (!leads.length) {
        $tbody.html('<tr><td colspan="8" class="text-center p-40 text-muted"><i class="fi fi-rr-check font-32 text-slate-400 d-block mb-10"></i>No confirmed leads found in this filter view.</td></tr>');
        return;
      }

      let html = "";
      leads.forEach(function (ld) {
        const leadLink = "/dashboard/leads/view/?id=" + ld.encoded_id;
        const totalVal = "₹ " + Number(ld.quotation_price).toLocaleString();
        const paidVal = "₹ " + Number(ld.total_paid).toLocaleString();
        const balVal = "₹ " + Number(ld.balance_due).toLocaleString();

        html += '<tr>' +
          '<td>' +
            '<div class="ops-lead-meta">' +
              '<a href="' + leadLink + '" class="ops-lead-title">' + ld.customer_name + '</a>' +
              '<div class="ops-lead-sub">' +
                '<span><i class="fi fi-rr-phone-call"></i> ' + (ld.mobile || "N/A") + '</span>' +
                '<span>ID: #' + ld.id + '</span>' +
              '</div>' +
            '</div>' +
          '</td>' +
          '<td><strong>' + (ld.destination || "Holiday Tour") + '</strong></td>' +
          '<td>' +
            '<div>' + (ld.travel_date || "Dates Pending") + '</div>' +
          '</td>' +
          '<td>' + ld.adults + ' Adults' + (ld.children > 0 ? ', ' + ld.children + ' Kids' : '') + '</td>' +
          '<td>' +
            '<div class="ops-price-meta">' +
              '<span class="ops-price-total">' + totalVal + '</span>' +
              '<span class="ops-price-paid">Paid: ' + paidVal + '</span>' +
              '<span class="ops-price-bal">Bal: ' + balVal + '</span>' +
            '</div>' +
          '</td>' +
          '<td><span class="text-muted">' + ld.agent_name + '</span></td>' +
          '<td><span class="ops-status-badge ' + ld.status_badge + '">' + ld.status_label + '</span></td>' +
          '<td class="text-right">' +
            '<div class="ops-action-btn-group">' +
              '<button type="button" class="btn-ops-action primary btn-open-booking-modal" data-id="' + ld.encoded_id + '" title="Send Vendor Booking Request"><i class="fi fi-rr-paper-plane"></i> Request</button>' +
              '<button type="button" class="btn-ops-action emerald btn-open-voucher-modal" data-id="' + ld.encoded_id + '" title="Issue Confirmation Vouchers"><i class="fi fi-rr-ticket"></i> Voucher</button>' +
              '<button type="button" class="btn-ops-action amber btn-open-invoice-modal" data-id="' + ld.encoded_id + '" title="Generate Proforma / Invoice"><i class="fi fi-rr-document"></i> Invoice</button>' +
              '<a href="' + leadLink + '" class="btn-ops-action" title="View Full Lead Details"><i class="fi fi-rr-eye"></i></a>' +
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
      $("#cardsDepartingToday, #cardsActiveOnTour, #cardsReturningToday, #cardsCompletedTrips").html(
        '<div class="ops-empty-col"><i class="fi fi-rr-spinner fi-spin"></i> Loading...</div>'
      );

      $.getJSON("/api/v3/operations/trip-planner.php?date=" + this.tripDate, function (res) {
        if (res && res.status === "success") {
          self.renderTripColumn("#cardsDepartingToday", res.departing_today || [], "departing");
          self.renderTripColumn("#cardsActiveOnTour", res.active_on_tour || [], "on_tour");
          self.renderTripColumn("#cardsReturningToday", res.returning_today || [], "returning");
          self.renderTripColumn("#cardsCompletedTrips", res.completed_trips || [], "completed");

          $("#badgeDepartingToday").text((res.departing_today || []).length);
          $("#badgeActiveOnTour").text((res.active_on_tour || []).length);
          $("#badgeReturningToday").text((res.returning_today || []).length);
          $("#badgeCompletedTrips").text((res.completed_trips || []).length);

          const totalActive = (res.departing_today || []).length + (res.active_on_tour || []).length + (res.returning_today || []).length;
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

        if (type === "departing") {
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
      $.post("/api/v3/operations/trip-planner.php", { lead_id: leadId, milestone: milestone }, function (res) {
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

      $.post("/api/v3/operations/trip-planner.php", payload, function (res) {
        if (res && res.status === "success") {
          alert(res.message);
          $("#modalOpsDriverDispatch").removeClass("active");
          if (res.whatsapp_link) {
            window.open(res.whatsapp_link, "_blank");
          }
          self.loadTripsBoard();
        } else {
          alert(res.message || "Failed to dispatch driver.");
        }
      }, "json");
    },

    triggerFeedbackRequest: function (leadId) {
      $.post("/api/v3/operations/feedback.php", { lead_id: leadId }, function (res) {
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

      $.getJSON("/api/v3/operations/leads.php?type=unassigned", function (res) {
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
        url: "/api/v3/operations/assign.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ lead_ids: ids, agent_id: agentId, reason: reason }),
        success: function (res) {
          if (res && res.status === "success") {
            alert(res.message);
            $("#modalOpsAssign").removeClass("active");
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
    openBookingRequestModal: function (encodedLeadId) {
      const self = this;
      $("#bkgModalLeadId").val(encodedLeadId);

      $.getJSON("/api/v3/operations/booking-request.php?lead_id=" + encodedLeadId, function (res) {
        if (res && res.status === "success") {
          self.currentLeadBookingData = res;
          self.cachedProperties = res.properties || [];
          self.cachedTransporters = res.transporters || [];
          self.cachedTemplates = res.templates || [];

          // Prefill logistics
          $("#bkgCheckIn").val(res.lead.check_in || "");
          $("#bkgCheckOut").val(res.lead.check_out || "");

          self.populateVendorDropdown("hotel");
          self.populateTemplateDropdown("hotel");
          self.updateLivePreviewContent();

          $("#modalOpsBookingRequest").addClass("active");
        } else {
          alert(res.message || "Could not load booking details.");
        }
      });
    },

    populateVendorDropdown: function (vendorType) {
      let opts = '<option value="">-- Choose ' + (vendorType === "hotel" ? "Property" : "Transporter") + " --</option>";
      const list = vendorType === "hotel" ? this.cachedProperties : this.cachedTransporters;
      list.forEach(function (v) {
        opts += '<option value="' + v.id + '" data-phone="' + (v.phone || "") + '" data-email="' + (v.email || "") + '">' + v.name + (v.city ? " (" + v.city + ")" : "") + "</option>";
      });
      $("#bkgVendorSelect").html(opts);
      $("#bkgVendorPhone").val("");
      $("#bkgVendorEmail").val("");
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
    },

    updateLivePreviewContent: function () {
      if (!this.currentLeadBookingData) return;
      const data = this.currentLeadBookingData;
      const vendorType = $("#bkgVendorType").val();
      const vendorName = $("#bkgVendorSelect option:selected").text().replace(/-- Choose.*--/, "").trim() || "Vendor Partner";
      const vendorPhone = $("#bkgVendorPhone").val() || "";
      const checkIn = $("#bkgCheckIn").val() || data.lead.check_in;
      const checkOut = $("#bkgCheckOut").val() || data.lead.check_out;
      const roomCategory = $("#bkgRoomCategory").val() || "Standard";
      const mealPlan = $("#bkgMealPlan").val() || "CP";
      const specialNotes = $("#bkgSpecialNotes").val() || "None";

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
          .replace(/{nights}/g, data.lead.nights)
          .replace(/{room_category}/g, roomCategory)
          .replace(/{room_count}/g, "1")
          .replace(/{meal_plan}/g, mealPlan)
          .replace(/{vehicle_type}/g, "Sedan / SUV")
          .replace(/{pax_adults}/g, data.lead.pax_adults)
          .replace(/{pax_children}/g, data.lead.pax_children)
          .replace(/{special_instructions}/g, specialNotes)
          .replace(/{agency_name}/g, data.agency.name)
          .replace(/{ops_manager_name}/g, data.agency.ops_name)
          .replace(/{ops_manager_phone}/g, data.agency.ops_phone);
      };

      if (waTpl) {
        $("#bkgWhatsAppContent").val(replaceTags(waTpl.body));
      } else {
        $("#bkgWhatsAppContent").val(replaceTags("Dear {vendor_name},\n\nBooking request for {guest_name} ({pax_adults} Adults).\nCheck-in: {check_in} | Check-out: {check_out}.\nRoom: {room_category} | Plan: {meal_plan}.\n\nKindly confirm reference number.\n{agency_name} Operations"));
      }

      if (emTpl) {
        $("#bkgEmailSubject").val(replaceTags(emTpl.subject));
        $("#bkgEmailContent").val(replaceTags(emTpl.body));
      } else {
        $("#bkgEmailSubject").val(replaceTags("Booking Request: {guest_name} - {check_in} - {agency_name}"));
        $("#bkgEmailContent").val(replaceTags("<p>Dear {vendor_name},</p><p>Please reserve for {guest_name} from {check_in} to {check_out}.</p>"));
      }
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
      const checkIn = $("#bkgCheckIn").val() || data.lead.check_in;
      const checkOut = $("#bkgCheckOut").val() || data.lead.check_out;

      const replaceTags = function (text) {
        if (!text) return "";
        return text
          .replace(/{vendor_name}/g, vendorName)
          .replace(/{guest_name}/g, data.lead.guest_name)
          .replace(/{check_in}/g, checkIn)
          .replace(/{check_out}/g, checkOut)
          .replace(/{nights}/g, data.lead.nights)
          .replace(/{agency_name}/g, data.agency.name);
      };

      if (tpl.channel === "whatsapp") {
        $("#bkgWhatsAppContent").val(replaceTags(tpl.body));
        $("#bkgPreviewTabs button[data-preview='whatsapp']").click();
      } else {
        $("#bkgEmailSubject").val(replaceTags(tpl.subject));
        $("#bkgEmailContent").val(replaceTags(tpl.body));
        $("#bkgPreviewTabs button[data-preview='email']").click();
      }
    },

    submitBookingRequest: function () {
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
        email_content: $("#bkgEmailContent").val()
      };

      if (!payload.vendor_name || payload.vendor_name === "") {
        alert("Please select or specify a vendor name.");
        return;
      }

      $.ajax({
        url: "/api/v3/operations/booking-request.php",
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
          } else {
            alert(res.message || "Failed to dispatch booking request.");
          }
        }
      });
    },

    // -----------------------------------------------------------------------
    // Confirmation Vouchers & Ref IDs Form
    // -----------------------------------------------------------------------
    openVoucherModal: function (encodedLeadId) {
      const self = this;
      $("#voucherLeadId").val(encodedLeadId);

      $.getJSON("/api/v3/operations/voucher.php?lead_id=" + encodedLeadId, function (res) {
        if (res && res.status === "success") {
          $("#voucherGuestBanner").html(
            '<div><strong>' + res.guest_name + '</strong> | Destination: ' + res.destination + '</div>' +
            '<div>Total Paid: <strong>₹ ' + Number(res.total_paid).toLocaleString() + '</strong></div>'
          );

          const $notice = $("#voucherPaymentNotice");
          if (res.has_payment) {
            $notice.removeClass("warning").addClass("ok").html('<i class="fi fi-rr-check"></i> Advance payment verified. You may proceed to issue official confirmation vouchers.');
            $("#btnConfirmIssueVouchers").prop("disabled", false);
          } else {
            $notice.removeClass("ok").addClass("warning").html('<i class="fi fi-rr-exclamation"></i> Business Rule Guard: No payment verified for this lead. Status 11 (Voucher Issued) requires advance payment.');
            $("#btnConfirmIssueVouchers").prop("disabled", true);
          }

          let itemsHtml = "";
          if (res.bookings && res.bookings.length) {
            res.bookings.forEach(function (b) {
              itemsHtml += '<div class="ops-voucher-row" data-bkg-id="' + b.id + '">' +
                '<div class="ops-voucher-row-top">' +
                  '<span><i class="fi fi-rr-bed"></i> ' + b.vendor_name + ' (' + b.service.toUpperCase() + ')</span>' +
                  '<span class="ops-status-badge ' + (b.confirmation_ref ? 'badge-paid' : 'badge-payment-init') + '">' +
                    (b.confirmation_ref ? 'Ref #' + b.confirmation_ref : 'Pending Ref ID') +
                  '</span>' +
                '</div>' +
                '<div class="ops-form-row">' +
                  '<div class="v3-form-group">' +
                    '<label class="v3-form-label">Vendor Booking Ref ID *</label>' +
                    '<input type="text" class="v3-form-input v-ref-id" value="' + (b.confirmation_ref || "") + '" placeholder="e.g. HTL-99882">' +
                  '</div>' +
                  '<div class="v3-form-group">' +
                    '<label class="v3-form-label">Driver / Guide Contact</label>' +
                    '<input type="text" class="v3-form-input v-driver-phone" value="' + (b.driver_phone || "") + '" placeholder="e.g. 9876543210">' +
                  '</div>' +
                '</div>' +
              '</div>';
            });
          } else {
            itemsHtml = '<div class="text-muted p-20 text-center">No vendor booking requests dispatched yet for this lead. Send booking request first or enter direct confirmation numbers below.</div>';
          }
          $("#voucherItemsList").html(itemsHtml);

          $("#modalOpsVoucher").addClass("active");
        } else {
          alert(res.message || "Failed to load voucher data.");
        }
      });
    },

    submitIssueVouchers: function () {
      const self = this;
      const leadId = $("#voucherLeadId").val();
      const bookings = [];

      $(".ops-voucher-row").each(function () {
        const bId = $(this).data("bkg-id");
        const refId = $(this).find(".v-ref-id").val();
        const driverPhone = $(this).find(".v-driver-phone").val();
        bookings.push({ id: bId, confirmation_ref: refId, driver_phone: driverPhone });
      });

      $.ajax({
        url: "/api/v3/operations/voucher.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ lead_id: leadId, bookings: bookings }),
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
            self.loadConfirmedLeads();
            self.loadMetrics();
          } else {
            alert(res.message || "Failed to issue vouchers.");
          }
        }
      });
    },

    // -----------------------------------------------------------------------
    // Invoicing & Proforma Modal
    // -----------------------------------------------------------------------
    openInvoiceModal: function (encodedLeadId) {
      $("#invModalLeadId").val(encodedLeadId);

      $.getJSON("/api/v3/operations/invoice.php?lead_id=" + encodedLeadId, function (res) {
        if (res && res.status === "success") {
          $("#invNumber").val(res.invoice_number);
          $("#invDate").val(res.date);
          $("#invGuestName").val(res.guest_name);
          $("#invGuestPhone").val(res.guest_phone);

          $("#invPkgTotal").text("₹ " + Number(res.package_cost).toLocaleString());
          $("#invGstAmount").text("₹ " + Number(res.gst_amount).toLocaleString());
          $("#invAdvancePaid").text("- ₹ " + Number(res.total_paid).toLocaleString());
          $("#invBalanceDue").text("₹ " + Number(res.balance_due).toLocaleString());

          $("#modalOpsInvoice").addClass("active");
        } else {
          alert(res.message || "Failed to load invoice calculation.");
        }
      });
    },

    submitSendInvoice: function () {
      const payload = {
        lead_id: $("#invModalLeadId").val(),
        type: $("input[name='invDocType']:checked").val(),
        invoice_number: $("#invNumber").val(),
        guest_name: $("#invGuestName").val(),
        guest_phone: $("#invGuestPhone").val(),
        total_amount: parseFloat($("#invPkgTotal").text().replace(/[^0-9.]/g, "")) || 0,
        balance_due: parseFloat($("#invBalanceDue").text().replace(/[^0-9.]/g, "")) || 0
      };

      $.ajax({
        url: "/api/v3/operations/invoice.php",
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
          } else {
            alert(res.message || "Failed to dispatch invoice.");
          }
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

      $.getJSON("/api/v3/operations/templates.php", function (res) {
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
        html += '<div class="ops-template-card" data-channel="' + t.channel + '" data-vendor="' + t.vendor_type + '">' +
          '<div class="ops-tpl-card-header">' +
            '<div>' +
              '<div class="ops-tpl-title">' + t.title + '</div>' +
              '<div class="ops-lead-sub mt-4">' +
                '<span><i class="' + (t.channel === "whatsapp" ? "fi fi-brands-whatsapp text-emerald" : "fi fi-rr-envelope text-blue") + '"></i> ' + t.channel.toUpperCase() + '</span>' +
                '<span>Service: ' + t.vendor_type.toUpperCase() + '</span>' +
              '</div>' +
            '</div>' +
            (t.is_default ? '<span class="ops-status-badge badge-approved">Default</span>' : '<button type="button" class="btn-ops-action btn-set-default-template" data-id="' + t.id + '">Set Default</button>') +
          '</div>' +
          '<div class="ops-tpl-body-preview">' + (t.body || "") + '</div>' +
          '<div class="ops-tpl-footer">' +
            '<span class="text-muted font-11">Updated: ' + t.updated_at + '</span>' +
            '<div class="ops-action-btn-group">' +
              '<button type="button" class="btn-ops-action btn-edit-template" data-id="' + t.id + '"><i class="fi fi-rr-edit"></i> Edit</button>' +
              '<button type="button" class="btn-ops-action text-rose btn-delete-template" data-id="' + t.id + '"><i class="fi fi-rr-trash"></i></button>' +
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
      if (tpl) {
        $("#tplEditorModalTitle").text("Edit Template: " + tpl.title);
        $("#tplEditorId").val(tpl.id);
        $("#tplEditorChannel").val(tpl.channel);
        $("#tplEditorVendorType").val(tpl.vendor_type);
        $("#tplEditorTitle").val(tpl.title);
        $("#tplEditorSubject").val(tpl.subject || "");
        $("#tplEditorBody").val(tpl.body || "");
        $("#tplEditorIsDefault").prop("checked", tpl.is_default);
      } else {
        $("#tplEditorModalTitle").text("Create Request Template");
        $("#tplEditorId").val(0);
        $("#tplEditorTitle").val("");
        $("#tplEditorSubject").val("");
        $("#tplEditorBody").val("");
        $("#tplEditorIsDefault").prop("checked", false);
      }
      $("#modalOpsTemplateEditor").addClass("active");
    },

    saveTemplate: function () {
      const self = this;
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
        url: "/api/v3/operations/templates.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(payload),
        success: function (res) {
          if (res && res.status === "success") {
            alert(res.message);
            $("#modalOpsTemplateEditor").removeClass("active");
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
        url: "/api/v3/operations/templates.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ action: "delete", id: templateId }),
        success: function (res) {
          if (res && res.status === "success") {
            self.loadTemplates();
          }
        }
      });
    },

    setDefaultTemplate: function (templateId) {
      const self = this;
      $.ajax({
        url: "/api/v3/operations/templates.php",
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

    // -----------------------------------------------------------------------
    // Booking Requests Ledger
    // -----------------------------------------------------------------------
    loadBookingRequests: function () {
      const self = this;
      const $tbody = $("#opsRequestsTableBody");
      $tbody.html('<tr><td colspan="8" class="text-center p-30 text-muted"><i class="fi fi-rr-spinner fi-spin"></i> Fetching booking requests ledger...</td></tr>');

      let url = "/api/v3/operations/booking-request.php?action=ledger&page=" + this.requestsPage + "&limit=" + this.requestsLimit;
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
              '<button type="button" class="btn-ops-action emerald btn-quick-record-response" data-id="' + r.id + '" data-status="' + r.status + '" data-ref="' + (r.confirmation_ref || "") + '" title="Record Vendor Confirmation Ref ID"><i class="fi fi-rr-check"></i> Response</button>' +
              '<button type="button" class="btn-ops-action primary btn-resend-booking-request" data-lead="' + r.encoded_lead_id + '" title="Resend Booking Request"><i class="fi fi-rr-refresh"></i> Resend</button>' +
              '<a href="' + leadLink + '" class="btn-ops-action" title="View Lead Record"><i class="fi fi-rr-eye"></i></a>' +
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
        url: "/api/v3/operations/booking-request.php",
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

  OperationsHub.init();
});
