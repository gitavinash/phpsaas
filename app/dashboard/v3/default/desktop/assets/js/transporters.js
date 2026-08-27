/**
 * V3 Transporters Directory Module
 * Path: app/dashboard/v3/default/desktop/assets/js/transporters.js
 * Handles fetching, filtering, pagination, quick contact actions, and deleting transporters via /api/v3/transporters/
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

  window.V3Transporters = {
    transportersData: [],
    metricsData: null,
    statesData: [],
    currentPage: 1,
    pageSize: 25,
    isLoading: false,
    isLoaded: false,
    activeXhr: null,
    searchDebounceTimer: null,
    eventsBound: false,

    init: function () {
      const $app = $("#transportersListApp");
      if (!$app.length) {
        this.isLoaded = false;
        this.isLoading = false;
        return;
      }

      if (this.isLoading) return;

      this.currentPage = 1;
      this.pageSize = parseInt($("#transporterPerPage").val(), 10) || 25;

      this.initCitySelect2();
      this.bindEvents();
      this.loadTransporters();
    },

    initCitySelect2: function () {
      const self = this;
      const $stateSelect = $("#filterTransporterState");

      if (!$stateSelect.length || !$.fn.select2) return;

      if ($stateSelect.hasClass("select2-hidden-accessible")) {
        $stateSelect.select2("destroy");
      }

      $stateSelect.select2({
        placeholder: "All Service States / Cities",
        allowClear: true,
        width: "220px",
        dropdownAutoWidth: true,
        ajax: {
          url: "/api/v3/cities/",
          dataType: "json",
          delay: 200,
          data: function (params) {
            return {
              search: params.term || "",
            };
          },
          processResults: function (data) {
            const results = [];
            if (data && data.results && data.results.length > 0) {
              data.results.forEach(function (item) {
                const label = item.city + (item.state ? ", " + item.state : "");
                results.push({
                  id: item.city || item.id,
                  text: label,
                });
              });
            }
            return { results: results };
          },
          cache: true,
        },
      });

      $stateSelect
        .off("change.transporterState")
        .on("change.transporterState", function () {
          if (!self.isLoaded) return;
          self.currentPage = 1;
          self.loadTransporters();
        });
    },

    bindEvents: function () {
      const self = this;
      const $app = $("#transportersListApp");

      if (this.eventsBound) return;
      this.eventsBound = true;

      // Live search with debounce
      $(document)
        .off("input", "#transporterSearchInput")
        .on("input", "#transporterSearchInput", function () {
          const val = $(this).val();
          if (val && val.trim().length > 0) {
            $("#clearTransporterSearchBtn").show();
          } else {
            $("#clearTransporterSearchBtn").hide();
          }

          clearTimeout(self.searchDebounceTimer);
          self.searchDebounceTimer = setTimeout(function () {
            self.currentPage = 1;
            self.loadTransporters();
          }, 250);
        });

      // Clear search button
      $(document)
        .off("click", "#clearTransporterSearchBtn")
        .on("click", "#clearTransporterSearchBtn", function () {
          $("#transporterSearchInput").val("");
          $(this).hide();
          self.currentPage = 1;
          self.loadTransporters();
        });

      // Status filter
      $(document)
        .off("change", "#filterTransporterStatus")
        .on("change", "#filterTransporterStatus", function () {
          self.currentPage = 1;
          self.loadTransporters();
        });

      // Per Page Select
      $(document)
        .off("change", "#transporterPerPage")
        .on("change", "#transporterPerPage", function () {
          self.pageSize = parseInt($(this).val(), 10) || 25;
          self.currentPage = 1;
          self.renderTableRows();
          self.renderPagination();
        });

      // Reset filters button
      $(document)
        .off("click", "#btnResetTransporterFilters")
        .on("click", "#btnResetTransporterFilters", function () {
          $("#transporterSearchInput").val("");
          $("#clearTransporterSearchBtn").hide();
          $("#filterTransporterStatus").val("");

          const $stateSelect = $("#filterTransporterState");
          if (
            $.fn.select2 &&
            $stateSelect.hasClass("select2-hidden-accessible")
          ) {
            $stateSelect.val(null).trigger("change");
          } else {
            $stateSelect.val("");
            self.currentPage = 1;
            self.loadTransporters();
          }
        });

      // Retry button click
      $(document)
        .off("click", ".retryTransportersBtn")
        .on("click", ".retryTransportersBtn", function (e) {
          e.preventDefault();
          self.loadTransporters();
        });

      // Pagination previous button
      $(document)
        .off("click", "#transporterPrevPage")
        .on("click", "#transporterPrevPage", function (e) {
          e.preventDefault();
          if (self.currentPage > 1) {
            self.currentPage--;
            self.renderTableRows();
            self.renderPagination();
            const $tbl = $("#transportersTable");
            if ($tbl.length) {
              $("html, body").animate(
                { scrollTop: $tbl.offset().top - 80 },
                200,
              );
            }
          }
        });

      // Pagination next button
      $(document)
        .off("click", "#transporterNextPage")
        .on("click", "#transporterNextPage", function (e) {
          e.preventDefault();
          const totalPages = Math.ceil(
            (self.transportersData || []).length / self.pageSize,
          );
          if (self.currentPage < totalPages) {
            self.currentPage++;
            self.renderTableRows();
            self.renderPagination();
            const $tbl = $("#transportersTable");
            if ($tbl.length) {
              $("html, body").animate(
                { scrollTop: $tbl.offset().top - 80 },
                200,
              );
            }
          }
        });

      // Pagination page numbers
      $(document)
        .off("click", ".transporter-page-btn")
        .on("click", ".transporter-page-btn", function (e) {
          e.preventDefault();
          const page = parseInt($(this).data("page"), 10);
          if (!isNaN(page) && page > 0 && page !== self.currentPage) {
            self.currentPage = page;
            self.renderTableRows();
            self.renderPagination();
            const $tbl = $("#transportersTable");
            if ($tbl.length) {
              $("html, body").animate(
                { scrollTop: $tbl.offset().top - 80 },
                200,
              );
            }
          }
        });

      // Delete Transporter
      $(document)
        .off("click", ".deleteTransporterBtn")
        .on("click", ".deleteTransporterBtn", function (e) {
          e.preventDefault();
          const id = $(this).data("id");
          const title = $(this).data("title") || "this transporter";

          if (
            confirm(
              `Are you sure you want to delete ${title}? This will also remove any package assignments.`,
            )
          ) {
            self.handleDeleteTransporter(id, $(this));
          }
        });
    },

    loadTransporters: function () {
      const self = this;

      // Abort previous ongoing request to prevent multiple hits
      if (this.activeXhr && typeof this.activeXhr.abort === "function") {
        this.activeXhr.abort();
        this.activeXhr = null;
      }

      this.isLoading = true;

      const search = $("#transporterSearchInput").val() || "";
      const status = $("#filterTransporterStatus").val() || "";
      const state = $("#filterTransporterState").val() || "";

      const $tbody = $("#transportersTableBody");
      $tbody.html(`
        <tr>
          <td colspan="6" class="table-loading-state" style="padding: 48px 20px; text-align: center; color: #64748b;">
            <div class="table-loading-spinner" style="margin: 0 auto 10px;"></div>
            <span style="font-size: 13.5px; font-weight: 500; display: block;">Loading transporters directory...</span>
          </td>
        </tr>
      `);

      const queryParams = {};
      if (search && search.trim().length > 0) {
        queryParams.search = search.trim();
      }
      if (status && status.trim().length > 0) {
        queryParams.status = status.trim();
      }
      if (state && state.trim().length > 0) {
        queryParams.state = state.trim();
      }

      this.activeXhr = $.ajax({
        url: "/api/v3/transporters/",
        type: "GET",
        data: queryParams,
        dataType: "json",
        success: function (res) {
          self.isLoading = false;
          self.isLoaded = true;
          self.activeXhr = null;

          if (res && res.status === "success") {
            self.transportersData = res.transporters || [];
            self.metricsData = res.metrics || {};
            self.statesData = res.states || [];

            self.renderKpis();
            self.renderTableRows();
            self.renderPagination();
          } else {
            const msg =
              res && res.msg ? res.msg : "Failed to fetch transporters list.";
            self.renderErrorState(msg);
          }
        },
        error: function (xhr, statusText) {
          if (statusText === "abort") return;
          self.isLoading = false;
          self.activeXhr = null;
          const msg =
            xhr.responseJSON && xhr.responseJSON.msg
              ? xhr.responseJSON.msg
              : "Server error fetching transporters.";
          self.renderErrorState(msg);
        },
      });
    },

    renderKpis: function () {
      const m = this.metricsData || {};
      $("#statTotalTransporters").text(m.total !== undefined ? m.total : "-");
      $("#statActiveTransporters").text(
        m.active !== undefined ? m.active : "-",
      );
      $("#statServiceStates").text(m.states !== undefined ? m.states : "-");
      $("#statAssignedTransporters").text(
        m.assigned !== undefined ? m.assigned : "-",
      );
    },

    renderTableRows: function () {
      const $tbody = $("#transportersTableBody");
      const allTransporters = this.transportersData || [];

      if (allTransporters.length === 0) {
        $tbody.html(`
          <tr>
            <td colspan="6" style="padding: 0; border: none;">
              <div class="transporters-empty-state">
                <div class="transporters-empty-icon-wrap">
                  <i class="fi fi-rr-truck-side"></i>
                </div>
                <h4 class="transporters-empty-title">No Transporters Found</h4>
                <p class="transporters-empty-desc">No transport vendors matched your current filter criteria.</p>
                <a href="/dashboard/vendors/transporter/add/" class="transporters-empty-action">
                  <i class="fi fi-rr-plus"></i> Add Transporter
                </a>
              </div>
            </td>
          </tr>
        `);
        $("#transporterPaginationBar").hide();
        return;
      }

      // Pagination slice
      const startIdx = (this.currentPage - 1) * this.pageSize;
      const endIdx = Math.min(startIdx + this.pageSize, allTransporters.length);
      const pageItems = allTransporters.slice(startIdx, endIdx);

      let html = "";
      pageItems.forEach(function (t) {
        // Initials avatar
        const nameParts = (t.title || "Transporter").trim().split(/\s+/);
        const initials =
          nameParts.length >= 2
            ? (nameParts[0][0] + nameParts[1][0]).toUpperCase()
            : nameParts[0].substring(0, 2).toUpperCase();

        const cleanPhone = (t.mobile || "").replace(/[^0-9+]/g, "");
        const waPhone = cleanPhone.replace(/^\+/, "");

        // Service state pills: show first 4 and +N more
        let statePillsHtml = "";
        const statesList = t.service_states_array || [];
        if (statesList.length > 0) {
          const maxVisible = 4;
          const visibleStates = statesList.slice(0, maxVisible);
          const remaining = statesList.length - maxVisible;

          visibleStates.forEach(function (st) {
            statePillsHtml += `<span class="badge-service-state" title="${st}">${st}</span>`;
          });

          if (remaining > 0) {
            const extraTooltip = statesList.slice(maxVisible).join(", ");
            statePillsHtml += `<span class="badge-service-more" title="${extraTooltip}">+${remaining} more</span>`;
          }
        } else {
          statePillsHtml =
            '<span style="color: #94a3b8; font-size: 12px;">All Regions</span>';
        }

        // Location formatting: area, city, state
        const cleanArea = (t.area || t.address || "").trim();
        const cleanCity = (t.city || "").trim();
        const cleanState = (t.state || "").trim();
        const cleanPincode = (t.pincode || "").trim();

        // Primary location line: Area, City
        let primaryLoc = "";
        if (
          cleanArea &&
          cleanCity &&
          cleanArea.toLowerCase() !== cleanCity.toLowerCase()
        ) {
          primaryLoc = `${cleanArea}, ${cleanCity}`;
        } else {
          primaryLoc = cleanArea || cleanCity || "-";
        }

        // Secondary location line: State, Pincode
        const subParts = [];
        if (
          cleanState &&
          cleanState.toLowerCase() !== cleanCity.toLowerCase()
        ) {
          subParts.push(cleanState);
        }
        if (cleanPincode) {
          subParts.push(cleanPincode);
        }
        const subLoc = subParts.join(" · ");

        html += `
          <tr class="transporter-row" data-id="${t.id}">
            <!-- Name & Info -->
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="transport-avatar-badge" style="width: 40px; height: 40px; font-size: 13.5px; flex-shrink: 0;">
                  ${initials}
                </div>
                <div style="min-width: 0;">
                  <div style="font-weight: 700; color: #0f172a; font-size: 14px; line-height: 1.3; margin-bottom: 3px;" title="${t.title}">
                    ${t.title}
                  </div>
                  ${
                    t.assigned_packages_count > 0
                      ? `<span class="badge-pkg-assigned"><i class="fi fi-rr-link-alt" style="font-size: 10px;"></i> ${t.assigned_packages_count} Packages</span>`
                      : '<span style="color: #94a3b8; font-size: 12px; font-weight: 500;">No Packages Assigned</span>'
                  }
                </div>
              </div>
            </td>

            <!-- Contact details -->
            <td>
              <div style="display: flex; flex-direction: column; gap: 4px;">
                ${
                  t.mobile
                    ? `
                  <div class="table-contact-link phone" title="${t.mobile}">
                    <i class="fi fi-rr-phone-call" style="color: #10b981; font-size: 11px;"></i> <span>${t.mobile}</span>
                  </div>
                `
                    : '<span style="color: #94a3b8; font-size: 12px;">-</span>'
                }
                ${
                  t.email
                    ? `
                  <a href="mailto:${t.email}" style="color: #6366f1; font-size: 12px; font-weight: 500; text-decoration: none; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px;" title="${t.email}">
                    <i class="fi fi-rr-envelope" style="font-size: 10.5px; color: #818cf8;"></i> <span>${t.email}</span>
                  </a>
                `
                    : ""
                }
              </div>
            </td>

            <!-- Location: area, city, state -->
            <td>
              <div class="table-location-block">
                <div class="table-location-icon">
                  <i class="fi fi-rr-marker"></i>
                </div>
                <div class="table-location-content">
                  <div class="table-location-main" title="${primaryLoc}">${primaryLoc}</div>
                  ${subLoc ? `<div class="table-location-sub" title="${subLoc}">${subLoc}</div>` : ""}
                </div>
              </div>
            </td>

            <!-- Service States -->
            <td>
              <div style="display: flex; flex-wrap: wrap; gap: 2px; max-width: 280px; align-items: center;">
                ${statePillsHtml}
              </div>
            </td>

            <!-- Status -->
            <td style="text-align: center;">
              ${
                t.status === 1
                  ? '<span class="v3-status-badge status-active"><span class="status-pulse-dot"></span> Active</span>'
                  : '<span class="v3-status-badge status-inactive"><span class="status-pulse-dot"></span> Inactive</span>'
              }
            </td>

            <!-- Actions -->
            <td style="text-align: right;">
              <div class="table-actions-inline" style="display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end;">
                <a href="/dashboard/vendors/transporter/update/?id=${t.encoded_id}" class="btn-action-icon edit" data-tooltip="Edit Transporter" title="Edit Transporter">
                  <i class="fi fi-rr-edit"></i>
                </a>
                <a href="/dashboard/vendors/transporter/fleet/?id=${t.encoded_id}" class="btn-action-icon transport" data-tooltip="Manage Fleet & Vehicle Models" title="Manage Fleet & Vehicle Models">
                  <i class="fi fi-rr-car-alt"></i>
                </a>
                <button type="button" class="btn-action-icon delete deleteTransporterBtn" data-id="${t.id}" data-title="${t.title}" data-tooltip="Delete Transporter" title="Delete Transporter">
                  <i class="fi fi-rr-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });

      $tbody.html(html);
    },

    renderPagination: function () {
      const allTransporters = this.transportersData || [];
      const totalItems = allTransporters.length;
      const totalPages = Math.ceil(totalItems / this.pageSize);

      if (totalItems <= this.pageSize) {
        $("#transporterPaginationBar").hide();
        return;
      }

      const startItem = (this.currentPage - 1) * this.pageSize + 1;
      const endItem = Math.min(this.currentPage * this.pageSize, totalItems);

      $("#transporterPaginationInfo").text(
        `Showing ${startItem} to ${endItem} of ${totalItems} transporters`,
      );

      // Prev / Next button states
      $("#transporterPrevPage").prop("disabled", this.currentPage <= 1);
      $("#transporterNextPage").prop(
        "disabled",
        this.currentPage >= totalPages,
      );

      let pagHtml = "";
      for (let p = 1; p <= totalPages; p++) {
        if (
          p === 1 ||
          p === totalPages ||
          (p >= this.currentPage - 2 && p <= this.currentPage + 2)
        ) {
          pagHtml += `<button type="button" class="pagination-page-btn transporter-page-btn ${p === this.currentPage ? "active" : ""}" data-page="${p}">${p}</button>`;
        } else if (p === this.currentPage - 3 || p === this.currentPage + 3) {
          pagHtml += '<span class="pagination-dots">...</span>';
        }
      }

      $("#transporterPaginationPages").html(pagHtml);
      $("#transporterPaginationBar").show();
    },

    handleDeleteTransporter: function (id, $btn) {
      const self = this;
      $btn.prop("disabled", true);

      $.ajax({
        url: "/api/v3/transporters/delete/",
        type: "POST",
        data: JSON.stringify({ transporter_id: id }),
        contentType: "application/json",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            if (typeof popUpMessage === "function") {
              popUpMessage(
                "center",
                res.msg || "Transporter deleted successfully.",
                "success",
              );
            }
            self.loadTransporters();
          } else {
            $btn.prop("disabled", false);
            const msg =
              res && res.msg ? res.msg : "Failed to delete transporter.";
            if (typeof popUpMessage === "function") {
              popUpMessage("center", msg, "error");
            } else {
              alert(msg);
            }
          }
        },
        error: function (xhr) {
          $btn.prop("disabled", false);
          const msg =
            xhr.responseJSON && xhr.responseJSON.msg
              ? xhr.responseJSON.msg
              : "Error deleting transporter.";
          if (typeof popUpMessage === "function") {
            popUpMessage("center", msg, "error");
          } else {
            alert(msg);
          }
        },
      });
    },

    renderErrorState: function (msg) {
      $("#transportersTableBody").html(`
        <tr>
          <td colspan="6" style="padding: 0; border: none;">
            <div class="transporters-empty-state">
              <div class="transporters-empty-icon-wrap" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-color: #fca5a5; color: #ef4444; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);">
                <i class="fi fi-rr-cross-circle"></i>
              </div>
              <h4 class="transporters-empty-title">Failed to load transporters</h4>
              <p class="transporters-empty-desc">${msg}</p>
              <button type="button" class="transporters-empty-action retryTransportersBtn" style="cursor: pointer;">
                <i class="fi fi-rr-refresh"></i> Retry Loading
              </button>
            </div>
          </td>
        </tr>
      `);
      $("#transporterPaginationBar").hide();
    },
  };
});
