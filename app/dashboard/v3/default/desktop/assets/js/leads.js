/**
 * V3 Leads Management SPA Module
 * Handles dynamic API fetching, multi-filter pipeline, bulk actions, and agent assignments.
 */
(function (factory) {
  if (typeof window.jQuery !== "undefined") {
    factory(window.jQuery);
  } else {
    document.addEventListener("DOMContentLoaded", function () {
      if (typeof window.jQuery !== "undefined") {
        factory(window.jQuery);
      }
    });
  }
})(function ($) {
  "use strict";

  window.V3Leads = {
    currentPage: 1,
    limit: 50,
    activeStatus: "all",
    activeSearch: "",
    activeAgent: "all",
    activeDateRange: "",
    activeSource: "all",
    agentsList: [],
    selectedLeads: new Set(),
    isLoading: false,
    eventsBound: false,

    init: function () {
      const $app = $("#leadsListApp");
      if (!$app.length) return;

      this.isLoading = false;
      this.selectedLeads.clear();
      $("#leadsBulkToolbar").hide();
      this.initSelect2();
      this.initDateRangePicker();
      if (!this.eventsBound) {
        this.bindEvents();
        this.eventsBound = true;
      }
      this.loadLeads();
    },

    initSelect2: function () {
      if (typeof $.fn.select2 !== "undefined") {
        $(".leads-filter-select").each(function () {
          if ($(this).data("select2")) {
            $(this).select2("destroy");
          }
        });

        $("#filterLeadStatus").select2({
          dropdownAutoWidth: true,
          width: "135px",
        });

        $("#filterLeadAgent").select2({
          dropdownAutoWidth: true,
          width: "155px",
        });

        $("#filterLeadSource").select2({
          dropdownAutoWidth: true,
          width: "130px",
        });

        if ($("#bulkActionSelect").data("select2")) {
          $("#bulkActionSelect").select2("destroy");
        }
        $("#bulkActionSelect").select2({
          minimumResultsForSearch: Infinity,
          dropdownAutoWidth: true,
          width: "100%",
        });
      }
    },

    initDateRangePicker: function () {
      const self = this;
      const $dateInput = $("#filterLeadDateRange");
      if (!$dateInput.length || typeof $.fn.daterangepicker === "undefined") return;

      $dateInput.daterangepicker({
        autoUpdateInput: false,
        opens: "left",
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
        },
        locale: {
          format: "YYYY-MM-DD",
          cancelLabel: "Clear",
        },
      });

      $dateInput.on("apply.daterangepicker", function (ev, picker) {
        const formatted = picker.startDate.format("YYYY-MM-DD") + " - " + picker.endDate.format("YYYY-MM-DD");
        $(this).val(formatted);
        $("#clearLeadDateRangeBtn").show();
        self.activeDateRange = formatted;
        self.currentPage = 1;
        self.loadLeads();
      });

      $dateInput.on("cancel.daterangepicker", function () {
        $(this).val("");
        $("#clearLeadDateRangeBtn").hide();
        self.activeDateRange = "";
        self.currentPage = 1;
        self.loadLeads();
      });

      $("#clearLeadDateRangeBtn").on("click", function (e) {
        e.stopPropagation();
        $dateInput.val("");
        $(this).hide();
        self.activeDateRange = "";
        self.currentPage = 1;
        self.loadLeads();
      });
    },

    bindEvents: function () {
      const self = this;

      // 1. Debounced Search Input (Name, Mobile, Email, Subject)
      let searchTimeout;
      $("#leadTableSearch").on("input", function () {
        const val = $(this).val().trim();
        clearTimeout(searchTimeout);
        $("#clearLeadSearchBtn").toggle(val.length > 0);
        searchTimeout = setTimeout(function () {
          self.activeSearch = val;
          self.currentPage = 1;
          self.loadLeads();
        }, 300);
      });

      $("#clearLeadSearchBtn").on("click", function () {
        $("#leadTableSearch").val("");
        $(this).hide();
        self.activeSearch = "";
        self.currentPage = 1;
        self.loadLeads();
      });

      // 2. Filter Dropdowns Change
      $("#filterLeadStatus").on("change", function () {
        self.activeStatus = $(this).val();
        self.currentPage = 1;
        self.loadLeads();
      });

      $("#filterLeadAgent").on("change", function () {
        self.activeAgent = $(this).val();
        self.currentPage = 1;
        self.loadLeads();
      });

      $("#filterLeadSource").on("change", function () {
        self.activeSource = $(this).val();
        self.currentPage = 1;
        self.loadLeads();
      });

      // 3. Reset All Filters Button
      $("#btnResetLeadFilters").on("click", function () {
        $("#leadTableSearch").val("");
        $("#clearLeadSearchBtn").hide();
        $("#filterLeadDateRange").val("");
        $("#clearLeadDateRangeBtn").hide();

        $("#filterLeadStatus").val("all");
        $("#filterLeadAgent").val("all");
        $("#filterLeadSource").val("all");

        if (typeof $.fn.select2 !== "undefined") {
          $(".leads-filter-select").trigger("change.select2");
        }

        self.activeSearch = "";
        self.activeDateRange = "";
        self.activeStatus = "all";
        self.activeAgent = "all";
        self.activeSource = "all";
        self.limit = 50;
        self.currentPage = 1;
        self.loadLeads();
      });

      // 3b. Empty State Reset Button Click
      $(document).on("click", "#btnEmptyStateReset", function () {
        $("#btnResetLeadFilters").trigger("click");
      });

      // 4. Quick KPI Cards Filter Click
      $(document).on("click", ".lead-kpi-filter-btn", function () {
        const status = $(this).data("status");
        if (status !== undefined) {
          $("#filterLeadStatus").val(status);
          if (typeof $.fn.select2 !== "undefined") {
            $("#filterLeadStatus").trigger("change.select2");
          }
          self.activeStatus = status;
          self.currentPage = 1;
          self.loadLeads();
        }
      });

      // 5. Select All Checkbox
      $("#selectAllLeads").on("change", function () {
        const isChecked = $(this).is(":checked");
        $(".lead-row-checkbox").prop("checked", isChecked).trigger("change");
      });

      // 6. Individual Row Checkbox
      $(document).on("change", ".lead-row-checkbox", function () {
        const id = $(this).val();
        const $row = $(this).closest("tr");
        if ($(this).is(":checked")) {
          self.selectedLeads.add(id);
          $row.addClass("row-selected");
        } else {
          self.selectedLeads.delete(id);
          $row.removeClass("row-selected");
        }
        self.updateBulkActionToolbar();
      });

      // 7. Apply Bulk Actions
      $("#btnApplyBulkAction").on("click", function () {
        const action = $("#bulkActionSelect").val();
        if (!action) {
          if (typeof popUpMessage === "function") {
            popUpMessage("center", "Please select a bulk action from the dropdown.", "warning");
          } else {
            alert("Please select a bulk action from the dropdown.");
          }
          return;
        }
        const ids = Array.from(self.selectedLeads);
        if (ids.length === 0) {
          if (typeof popUpMessage === "function") {
            popUpMessage("center", "Please select at least one lead.", "warning");
          } else {
            alert("Please select at least one lead.");
          }
          return;
        }
        self.handleBulkAction(action, ids);
      });

      // 7. Inline Agent Assignment Change with SweetAlert Reassignment Confirmation
      $(document).on("change", ".inline-agent-select", function () {
        const $select = $(this);
        const leadId = $select.data("lead-id");
        const newAgentId = $select.val();
        const newAgentName = $select.find("option:selected").text();
        const prevAgentId = String($select.data("current-agent") !== undefined ? $select.data("current-agent") : "0");
        const prevAgentName = $select.data("current-agent-name") || "assigned agent";

        const doAssign = function () {
          $select.prop("disabled", true);

          $.ajax({
            url: "/api/v3/leads/assign/",
            type: "POST",
            data: {
              lead_id: leadId,
              leadId: leadId,
              agent_id: newAgentId,
              agentId: newAgentId,
            },
            dataType: "json",
            success: function (res) {
              $select.prop("disabled", false);
              if (res.status === 200 || res.status_code === 200 || res.success === true) {
                // Update tracking data attributes
                $select.data("current-agent", newAgentId);
                $select.data("current-agent-name", newAgentName);
                $select.attr("data-current-agent", newAgentId);
                $select.attr("data-current-agent-name", newAgentName);

                if (newAgentId === "0" || newAgentId === 0) {
                  self.showBottomToast("Lead marked as <strong>Unassigned</strong>", "success");
                } else {
                  self.showBottomToast(`Lead assigned to <strong>${newAgentName}</strong>`, "success");
                }
              } else {
                $select.val(prevAgentId);
                self.showBottomToast(res.msg || res.message || "Failed to assign lead.", "error");
              }
            },
            error: function (xhr) {
              $select.prop("disabled", false);
              $select.val(prevAgentId);
              let errMsg = "Failed to assign lead. Please try again.";
              try {
                const res = JSON.parse(xhr.responseText);
                if (res.msg) errMsg = res.msg;
              } catch (e) {}
              self.showBottomToast(errMsg, "error");
            },
          });
        };

        // If lead is already assigned to someone and changing to another agent or unassigned
        if (prevAgentId !== "0" && prevAgentId !== newAgentId) {
          const isUnassign = newAgentId === "0";
          const title = isUnassign ? "Unassign Lead?" : "Reassign Lead?";
          const text = isUnassign
            ? `This lead is currently assigned to ${prevAgentName}. Are you sure you want to unassign it?`
            : `This lead is currently assigned to ${prevAgentName}. Are you sure you want to reassign it to ${newAgentName}?`;
          const confirmBtnText = isUnassign ? "Yes, Unassign" : "Yes, Reassign";

          if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
            Swal.fire({
              title: title,
              text: text,
              type: "warning",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#0284c7",
              cancelButtonColor: "#64748b",
              confirmButtonText: confirmBtnText,
              cancelButtonText: "Cancel",
            }).then(function (result) {
              if (result && (result.value || result.isConfirmed)) {
                doAssign();
              } else {
                $select.val(prevAgentId);
              }
            });
          } else if (confirm(text)) {
            doAssign();
          } else {
            $select.val(prevAgentId);
          }
        } else {
          // Fresh assignment from unassigned
          doAssign();
        }
      });

      // 8. Inline Quick Status Update
      $(document).on("change", ".inline-status-select", function () {
        const $select = $(this);
        const leadId = $select.data("lead-id");
        const statusVal = $select.val();
        if (!leadId) return;

        $select.prop("disabled", true);
        $.ajax({
          url: "/api/v3/leads/update-status/",
          type: "POST",
          data: {
            lead_id: leadId,
            leadId: leadId,
            id: leadId,
            enquiry_id: leadId,
            status: statusVal,
          },
          dataType: "json",
          success: function (res) {
            $select.prop("disabled", false);
            if (res.status === 200 || res.status === "success") {
              if (typeof popUpMessage === "function") {
                popUpMessage("center", "Lead status updated!", "success");
              }
              self.loadLeads(false);
            } else {
              if (typeof popUpMessage === "function") {
                popUpMessage("center", res.message || "Failed to update status", "error");
              }
            }
          },
          error: function () {
            $select.prop("disabled", false);
          },
        });
      });

      // 9. Pagination Navigation
      $(document).on("click", ".lead-page-btn", function (e) {
        e.preventDefault();
        const page = parseInt($(this).data("page"));
        if (page > 0 && page !== self.currentPage) {
          self.currentPage = page;
          self.loadLeads();
          $("html, body").animate({ scrollTop: $("#leadsListApp").offset().top - 20 }, 200);
        }
      });
    },

    updateBulkActionToolbar: function () {
      const count = this.selectedLeads.size;
      $("#selectedLeadsCount").text(count);
      const $toolbar = $("#leadsBulkToolbar");
      if (count > 0) {
        $toolbar.css("display", "flex").fadeIn(150);
      } else {
        $toolbar.fadeOut(150);
        $("#selectAllLeads").prop("checked", false);
      }
    },

    handleBulkAction: function (action, ids) {
      const self = this;
      if (action === "delete") {
        if (!confirm(`Are you sure you want to delete ${ids.length} selected lead(s)?`)) return;
        let completed = 0;
        ids.forEach(function (id) {
          $.post("/api/v3/leads/update-status/", { leadId: id, lead_id: id, id: id, enquiry_id: id, status: 4 }, function () {
            completed++;
            if (completed >= ids.length) {
              self.selectedLeads.clear();
              self.updateBulkActionToolbar();
              self.loadLeads();
            }
          });
        });
      } else if (action === "mark_duplicate") {
        let completed = 0;
        ids.forEach(function (id) {
          $.post("/api/v3/leads/update-status/", { leadId: id, lead_id: id, id: id, enquiry_id: id, status: 5 }, function () {
            completed++;
            if (completed >= ids.length) {
              self.selectedLeads.clear();
              self.updateBulkActionToolbar();
              self.loadLeads();
            }
          });
        });
      } else if (action === "convert_to_sales") {
        let completed = 0;
        ids.forEach(function (id) {
          $.post("/api/v3/leads/update-status/", { leadId: id, lead_id: id, id: id, enquiry_id: id, status: 1 }, function () {
            completed++;
            if (completed >= ids.length) {
              self.selectedLeads.clear();
              self.updateBulkActionToolbar();
              self.loadLeads();
            }
          });
        });
      }
    },

    loadLeads: function (showLoader = true) {
      const self = this;
      if (self.isLoading) return;
      self.isLoading = true;

      const $tbody = $("#leadsTableBody");
      if (showLoader) {
        $tbody.html(`
          <tr>
            <td colspan="6">
              <div class="v3-leads-table-loader">
                <div class="v3-table-spinner">
                  <div class="v3-spinner-ring"></div>
                  <div class="v3-spinner-inner-dot"></div>
                </div>
                <h4 class="v3-loader-title">Loading Leads Pipeline...</h4>
                <p class="v3-loader-subtitle">Fetching inquiries, customer records, and live status.</p>
              </div>
            </td>
          </tr>
        `);
      }

      $.ajax({
        url: "/api/v3/leads/list.php",
        type: "POST",
        data: {
          page: self.currentPage,
          limit: self.limit,
          search: self.activeSearch,
          status: self.activeStatus,
          agent_id: self.activeAgent,
          source: self.activeSource,
          daterange: self.activeDateRange || (typeof dateRangeParams !== "undefined" ? dateRangeParams : ""),
        },
        dataType: "json",
        success: function (res) {
          self.isLoading = false;
          if (res.status === 200 || res.status === "success") {
            self.agentsList = res.agents || [];
            self.populateAgentDropdowns(res.agents || []);
            self.renderKPIs(res.stats || {});
            self.renderTableRows(res.data || []);
            self.renderPagination(res.pagination || {});
          } else {
            $tbody.html(`
              <tr>
                <td colspan="6">
                  <div class="v3-leads-empty-state">
                    <div class="v3-empty-icon-wrap">
                      <i class="fi fi-rr-cross-circle"></i>
                    </div>
                    <h4 class="v3-empty-title">Failed to Load Leads</h4>
                    <p class="v3-empty-subtitle">${res.message || "An error occurred while fetching leads."}</p>
                    <button type="button" class="btn-empty-reset-filters" id="btnEmptyStateReset">
                      <i class="fi fi-rr-refresh"></i> Retry
                    </button>
                  </div>
                </td>
              </tr>
            `);
          }
        },
        error: function (err) {
          self.isLoading = false;
          $tbody.html(`
            <tr>
              <td colspan="6">
                <div class="v3-leads-empty-state">
                  <div class="v3-empty-icon-wrap">
                    <i class="fi fi-rr-exclamation"></i>
                  </div>
                  <h4 class="v3-empty-title">Network Connection Error</h4>
                  <p class="v3-empty-subtitle">Unable to communicate with the server. Please check your connection.</p>
                  <button type="button" class="btn-empty-reset-filters" id="btnEmptyStateReset">
                    <i class="fi fi-rr-refresh"></i> Retry
                  </button>
                </div>
              </td>
            </tr>
          `);
        },
      });
    },

    populateAgentDropdowns: function (agents) {
      const $filter = $("#filterLeadAgent");
      if ($filter.length && $filter.children("option").length <= 2) {
        agents.forEach(function (ag) {
          $filter.append(`<option value="${ag.id}">${ag.name} (${ag.role})</option>`);
        });
        if (typeof $.fn.select2 !== "undefined") {
          $filter.trigger("change.select2");
        }
      }
    },

    renderKPIs: function (stats) {
      $("#statTotalLeads").text(stats.total || 0);
      $("#statFreshLeads").text(stats.fresh || 0);
      $("#statPendingLeads").text((stats.pending || 0) + (stats.followup || 0));
      $("#statConfirmedLeads").text(stats.confirmed || 0);
    },

    renderTableRows: function (leads) {
      const self = this;
      const $tbody = $("#leadsTableBody");
      if (!leads || leads.length === 0) {
        $tbody.html(`
          <tr>
            <td colspan="6">
              <div class="v3-leads-empty-state">
                <div class="v3-empty-icon-wrap">
                  <i class="fi fi-rr-inbox"></i>
                </div>
                <h4 class="v3-empty-title">No Leads Found</h4>
                <p class="v3-empty-subtitle">No customer enquiries match your current filters or search term.</p>
                <button type="button" class="btn-empty-reset-filters" id="btnEmptyStateReset">
                  <i class="fi fi-rr-refresh"></i> Clear All Filters
                </button>
              </div>
            </td>
          </tr>
        `);
        return;
      }

      let html = "";
      leads.forEach(function (lead) {
        const isChecked = self.selectedLeads.has(String(lead.id));
        const initials = (lead.name || "L")
          .split(" ")
          .map((n) => n[0])
          .slice(0, 2)
          .join("")
          .toUpperCase();

        let agentOptions = `<option value="0">Unassigned</option>`;
        self.agentsList.forEach(function (ag) {
          const sel = lead.agent_id === ag.id ? "selected" : "";
          agentOptions += `<option value="${ag.id}" ${sel}>${ag.name}</option>`;
        });

        const statusBadge = `
          <span class="v3-lead-badge ${lead.status_class}">
            <span class="lead-dot"></span> ${lead.status_label}
          </span>
        `;

        const phoneLink = lead.mobile
          ? `<a href="tel:${lead.mobile}" class="lead-contact-link" data-tooltip="Call ${lead.mobile}"><i class="fi fi-rr-phone-call"></i> ${lead.mobile}</a>`
          : `<span class="text-muted">-</span>`;

        const waSvg = `
          <svg class="wa-svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
          </svg>
        `;

        const waIconBtn = lead.wa_mobile
          ? `<a href="https://wa.me/${lead.wa_mobile}?text=Hello%20${encodeURIComponent(lead.first_name || lead.name)}" target="_blank" class="lead-wa-btn-vibrant" data-tooltip="WhatsApp ${lead.name}">${waSvg}</a>`
          : "";

        const emailLink = lead.email
          ? `<a href="mailto:${lead.email}" class="lead-email-link" data-tooltip="Email ${lead.email}"><i class="fi fi-rr-envelope"></i> ${lead.email}</a>`
          : `<span class="meta-txt muted">No email</span>`;

        const noteSnippet = lead.recent_note
          ? `<div class="lead-note-badge"><i class="fi fi-rr-comment-dots"></i> <span>${lead.recent_note}</span></div>`
          : `<span class="lead-no-note"><i class="fi fi-rr-comment"></i> No notes recorded</span>`;

        html += `
          <tr class="${isChecked ? "row-selected" : ""}" data-lead-id="${lead.id}">
            <td class="col-checkbox">
              <input type="checkbox" class="lead-row-checkbox" value="${lead.id}" ${isChecked ? "checked" : ""}>
            </td>

            <td class="col-customer">
              <div class="lead-cell-wrap">
                <div class="lead-avatar">${initials}</div>
                <div class="lead-cell-meta">
                  <div class="lead-name-row">
                    <a href="/dashboard/leads/view/?id=${lead.encoded_id}" class="lead-primary-text" data-tooltip="View ${lead.name}">
                      ${lead.name}
                    </a>
                    ${statusBadge}
                  </div>
                  <div class="lead-sub-meta">
                    <span class="meta-txt"><i class="fi fi-rr-clock"></i> ${lead.time_relative}</span>
                    <span class="meta-dot">·</span>
                    <span class="meta-txt"><i class="fi fi-rr-globe"></i> ${lead.source}</span>
                  </div>
                </div>
              </div>
            </td>

            <td class="col-contact">
              <div class="lead-cell-meta">
                <div class="lead-phone-line">
                  ${phoneLink}
                  ${waIconBtn}
                </div>
                <div class="lead-sub-meta">
                  ${emailLink}
                </div>
              </div>
            </td>

            <td class="col-subject">
              <div class="lead-cell-meta">
                <div class="lead-primary-text subject">
                  <i class="fi fi-rr-compass"></i> <span>${lead.subject}</span>
                </div>
                <div class="lead-sub-meta">
                  ${noteSnippet}
                </div>
              </div>
            </td>

            <td class="col-agent">
              <div class="lead-assign-wrap">
                <i class="fi fi-rr-user lead-assign-icon"></i>
                <select class="lead-agent-select inline-agent-select" data-lead-id="${lead.id}" data-current-agent="${lead.agent_id || 0}" data-current-agent-name="${lead.agent_name || 'Unassigned'}">
                  ${agentOptions}
                </select>
              </div>
            </td>

            <td class="col-actions">
              <div class="lead-actions-group">
                <a href="/dashboard/leads/view/?id=${lead.encoded_id}" class="btn-lead-view-cta" data-tooltip="View Details">
                  <i class="fi fi-rr-eye"></i> <span>View</span>
                </a>
                <a href="#" class="btn-lead-icon-action duplicate duplicateLead" id="${lead.encoded_id}" data-tooltip="Mark as Duplicate">
                  <i class="fi fi-rr-copy"></i>
                </a>
                <a href="#" class="btn-lead-icon-action delete deleteButton" data-type="leads" data-id="${lead.id}" data-title="Delete lead" data-redirect="false" data-tooltip="Delete Lead">
                  <i class="fi fi-rr-trash"></i>
                </a>
              </div>
            </td>
          </tr>
        `;
      });

      $tbody.html(html);
    },

    renderPagination: function (p) {
      const $wrap = $("#leadsPaginationWrap");
      if (!p || p.total_pages <= 1) {
        $wrap.empty();
        return;
      }

      const cur = p.current_page;
      const total = p.total_pages;
      const totalRecords = p.total_records;
      const from = p.offset + 1;
      const to = Math.min(p.offset + p.limit, totalRecords);

      let pagesHtml = "";
      const startPage = Math.max(1, cur - 2);
      const endPage = Math.min(total, cur + 2);

      if (cur > 1) {
        pagesHtml += `<button type="button" class="pagination-btn lead-page-btn" data-page="${cur - 1}"><i class="fi fi-rr-angle-left"></i> Prev</button>`;
      }

      for (let i = startPage; i <= endPage; i++) {
        pagesHtml += `<button type="button" class="pagination-btn lead-page-btn ${i === cur ? "active" : ""}" data-page="${i}">${i}</button>`;
      }

      if (cur < total) {
        pagesHtml += `<button type="button" class="pagination-btn lead-page-btn" data-page="${cur + 1}">Next <i class="fi fi-rr-angle-right"></i></button>`;
      }

      $wrap.html(`
        <div class="v3-pagination-bar">
          <div class="pagination-meta">
            Showing <b>${from}–${to}</b> of <b>${totalRecords}</b> leads
          </div>
          <div class="pagination-buttons">
            ${pagesHtml}
          </div>
        </div>
      `);
    },

    showBottomToast: function (message, type = "success") {
      let $container = $("#v3BottomToastContainer");
      if (!$container.length) {
        $("body").append('<div id="v3BottomToastContainer" class="v3-bottom-toast-container"></div>');
        $container = $("#v3BottomToastContainer");
      }

      const iconClass = type === "error" ? "fi fi-rr-cross-circle" : "fi fi-rr-check-circle";
      const isError = type === "error" ? "toast-error" : "";

      const $toast = $(`
        <div class="v3-bottom-toast-label ${isError}">
          <span class="toast-icon"><i class="${iconClass}"></i></span>
          <span class="toast-content">${message}</span>
          <button type="button" class="toast-close-btn" title="Dismiss">✕</button>
        </div>
      `);

      $container.append($toast);

      $toast.find(".toast-close-btn").on("click", function () {
        $toast.css({ opacity: 0, transform: "translateY(10px)" });
        setTimeout(function () {
          $toast.remove();
        }, 200);
      });

      setTimeout(function () {
        $toast.css({ opacity: 0, transform: "translateY(10px)" });
        setTimeout(function () {
          $toast.remove();
        }, 200);
      }, 3500);
    },
  };

  $(document).ready(function () {
    window.V3Leads.init();
  });

  $(document).on("spa:loaded", function () {
    window.V3Leads.init();
  });
});
